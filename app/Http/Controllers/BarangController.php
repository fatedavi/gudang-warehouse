<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $query = Barang::query();

        if ($cari = $request->input('cari')) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode_produk', 'like', "%{$cari}%")
                    ->orWhere('merk_produk', 'like', "%{$cari}%")
                    ->orWhere('jenis_barang', 'like', "%{$cari}%");
            });
        }

        if ($status = $request->input('status')) {
            match ($status) {
                Barang::STATUS_TERJUAL => $query->terjual(),
                Barang::STATUS_DI_GUDANG => $query->stokGudang(),
                Barang::STATUS_BARU => $query->baru(),
                Barang::STATUS_LAMA => $query->lama(),
                Barang::STATUS_CAMPURAN => $query->campuran(),
                default => null,
            };
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($merk = $request->input('merk_produk')) {
            $query->where('merk_produk', $merk);
        }

        $barangs = $query->denganUnitStok()->latest('id')->paginate(10)->withQueryString();

        return view('barang.index', [
            'barangs' => $barangs,
            'daftarJenis' => Barang::distinct()->orderBy('jenis_barang')->pluck('jenis_barang'),
            'daftarMerk' => Barang::distinct()->orderBy('merk_produk')->pluck('merk_produk'),
            'daftarStatus' => Barang::statusList(),
            'filter' => $request->only(['cari', 'status', 'jenis_barang', 'merk_produk']),
        ]);
    }

    public function create(): View
    {
        return view('barang.create');
    }

    public function kodeOtomatis(Request $request)
    {
        $jenis = trim((string) $request->query('jenis', ''));

        if ($jenis === '' || Barang::kodeSegmen($jenis) === null) {
            return response()->json(['tersedia' => false]);
        }

        return response()->json([
            'tersedia' => true,
            'kode' => Barang::buatKodeOtomatis($jenis),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['kode_produk'] = $this->kodeAkhir($request, $data, null);
        $data['terjual'] = 0;
        $data['sisa_stok'] = max(0, $data['qty']);
        $data = $this->hitungMargin($data);

        Barang::create($data);

        return redirect()->route('barang.index')
            ->with('sukses', 'Data produk berhasil ditambahkan.');
    }

    public function show(Barang $barang): View
    {
        $barang->loadCount([
            'units as unit_stok_belum_keluar' => fn ($q) => $q->stokBaru(),
            'units as unit_stok_sudah_keluar' => fn ($q) => $q->stokLama(),
            'units as unit_di_luar' => fn ($q) => $q->where('status', BarangUnit::STATUS_KELUAR),
            'units as unit_terjual' => fn ($q) => $q->where('status', BarangUnit::STATUS_TERJUAL),
        ]);

        return view('barang.show', [
            'barang' => $barang,
        ]);
    }

    public function edit(Barang $barang): View
    {
        return view('barang.edit', [
            'barang' => $barang,
        ]);
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['kode_produk'] = $this->kodeAkhir($request, $data, $barang);
        $data['terjual'] = $barang->terjual;
        $data['sisa_stok'] = max(0, $data['qty'] - $data['terjual'] - $barang->keluar);
        $data = $this->hitungMargin($data);

        $barang->update([...$data, 'keluar' => $barang->keluar]);

        $this->sinkronkanUnitQty($barang, (int) $data['qty']);

        return redirect()->route('barang.index')
            ->with('sukses', 'Data produk berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        $barang->delete();

        return redirect()->route('barang.index')
            ->with('sukses', 'Data produk berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'kode_produk' => ['nullable', 'string', 'max:50', 'alpha_dash', 'unique:barangs,kode_produk,'.$request->route('barang')?->id],
            'jenis_barang' => ['required', 'string', 'max:100'],
            'merk_produk' => ['required', 'string', 'max:100'],
            'ukuran_produk' => ['required', 'string', 'max:20'],
            'warna_produk' => ['required', 'string', 'max:50'],
            'kondisi_barang' => ['required', 'string', 'max:20'],
            'qty' => ['required', 'integer', 'min:0'],
            'terjual' => ['nullable', 'integer', 'min:0'],
            'sisa_stok' => ['nullable', 'integer', 'min:0'],
            'harga_gudang' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0'],
            'margin_kotor' => ['nullable', 'integer', 'min:0'],
            'margin_persen' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function kodeAkhir(Request $request, array $data, ?Barang $barang): string
    {
        $kode = trim((string) ($data['kode_produk'] ?? ''));
        $jenisBaru = $data['jenis_barang'];
        $jenisLama = $barang?->jenis_barang;

        if ($barang !== null && $jenisLama !== $jenisBaru) {
            if (Barang::kodeSegmen($jenisBaru) !== null) {
                return Barang::buatKodeOtomatis($jenisBaru);
            }

            if ($kode === '') {
                throw ValidationException::withMessages([
                    'kode_produk' => 'Kode produk wajib diisi manual karena jenis barang ini tidak memiliki kode otomatis.',
                ]);
            }

            return $kode;
        }

        if ($kode !== '') {
            return $kode;
        }

        if ($barang !== null) {
            return $barang->kode_produk;
        }

        if (Barang::kodeSegmen($jenisBaru) !== null) {
            return Barang::buatKodeOtomatis($jenisBaru);
        }

        throw ValidationException::withMessages([
            'kode_produk' => 'Kode produk wajib diisi manual karena jenis barang ini tidak memiliki kode otomatis.',
        ]);
    }

    private function hitungMargin(array $data): array
    {
        $hargaGudang = (int) ($data['harga_gudang'] ?? 0);
        $hargaJual = (int) ($data['harga_jual'] ?? 0);
        $data['margin_kotor'] = max(0, $hargaJual - $hargaGudang);
        $data['margin_persen'] = $hargaGudang > 0 ? round(($data['margin_kotor'] / $hargaGudang) * 100, 2) : 0.0;

        return $data;
    }

    private function sinkronkanUnitQty(Barang $barang, int $qty): void
    {
        $jumlahAda = (int) $barang->units()->count();

        if ($qty > $jumlahAda) {
            $this->buatUnitBarangFrom($barang, $jumlahAda + 1, $qty);
        } elseif ($qty < $jumlahAda) {
            $barang->units()->where('nomor_urut', '>', $qty)->delete();
        }
    }

    private function buatUnitBarangFrom(Barang $barang, int $mulai, int $sampai): void
    {
        $sekarang = now();
        $rows = [];

        for ($i = $mulai; $i <= $sampai; $i++) {
            $rows[] = [
                'barang_id' => $barang->id,
                'nomor_urut' => $i,
                'pernah_keluar' => false,
                'status' => BarangUnit::STATUS_DI_GUDANG,
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ];
        }

        DB::table('barang_units')->insert($rows);
    }
}
