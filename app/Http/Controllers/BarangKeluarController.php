<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangUnit;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BarangKeluarController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = BarangKeluar::with(['barang', 'user'])
            ->where('kembali', false)
            ->whereNull('kembali_dari')
            ->where('terjual', false)
            ->whereNull('terjual_dari');

        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        if ($cari = $request->input('cari')) {
            $query->whereHas('barang', function ($q) use ($cari) {
                $q->where('kode_produk', 'like', "%{$cari}%")
                    ->orWhere('merk_produk', 'like', "%{$cari}%")
                    ->orWhere('jenis_barang', 'like', "%{$cari}%");
            });
        }

        if ($userTerpilih = $request->input('user_id')) {
            if ($user->isAdmin()) {
                $query->where('user_id', $userTerpilih);
            }
        }

        if ($dari = $request->input('dari')) {
            $query->whereDate('created_at', '>=', $dari);
        }

        if ($sampai = $request->input('sampai')) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        $barangTersedia = Barang::where('sisa_stok', '>', 0)
            ->orderBy('jenis_barang')
            ->orderBy('merk_produk')
            ->get();

        return view('barang-keluar.index', [
            'keluars' => $query->latest()->paginate(10)->withQueryString(),
            'daftarBarang' => $barangTersedia,
            'daftarUser' => $user->isAdmin()
                ? User::where('role', User::ROLE_USER)->orderBy('name')->get()
                : collect(),
            'filter' => $request->only(['cari', 'user_id', 'dari', 'sampai']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'user_id' => $user->isAdmin()
                ? ['required', 'exists:users,id']
                : ['nullable'],
        ]);

        $barang = Barang::findOrFail($data['barang_id']);

        if ($data['jumlah'] > $barang->sisa_stok) {
            throw ValidationException::withMessages([
                'jumlah' => "Jumlah keluar ({$data['jumlah']}) melebihi sisa stok ({$barang->sisa_stok}).",
            ]);
        }

        $userId = $user->isAdmin() ? $data['user_id'] : $user->id;
        $penjual = User::find($userId);

        DB::transaction(function () use ($data, $barang, $userId, $penjual) {
            BarangKeluar::create([
                'barang_id' => $barang->id,
                'user_id' => $userId,
                'jumlah' => $data['jumlah'],
                'kembali' => false,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $barang->decrementQuietly('sisa_stok', $data['jumlah']);
            $barang->sinkronkanKeluar();

            $unitIds = $barang->units()
                ->where('status', BarangUnit::STATUS_DI_GUDANG)
                ->orderBy('nomor_urut')
                ->limit($data['jumlah'])
                ->pluck('id');

            $barang->units()->whereIn('id', $unitIds)->update([
                'pernah_keluar' => true,
                'status' => BarangUnit::STATUS_KELUAR,
            ]);

            $barang->historis()->create([
                'aksi' => 'keluar',
                'detail' => "Barang keluar {$data['jumlah']} unit {$barang->kode_produk} oleh ".($penjual?->name ?? Auth::user()?->name)." (sisa stok menjadi {$barang->fresh()->sisa_stok}).",
            ]);
        });

        return redirect()->route('keluar.index')
            ->with('sukses', 'Barang keluar berhasil dicatat.');
    }

    public function kembali(Request $request, BarangKeluar $barangKeluar): RedirectResponse
    {
        $this->pastikanBerhakMengubah($barangKeluar);

        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $sisa = $barangKeluar->sisa_belum_kembali;

        if ($data['jumlah'] > $sisa) {
            throw ValidationException::withMessages([
                'jumlah' => "Jumlah kembali ({$data['jumlah']}) melebihi sisa belum kembali ({$sisa}).",
            ]);
        }

        $barang = $barangKeluar->barang;

        DB::transaction(function () use ($data, $barangKeluar, $barang) {
            BarangKeluar::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'jumlah' => $data['jumlah'],
                'kembali' => true,
                'kembali_dari' => $barangKeluar->id,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $barang->incrementQuietly('sisa_stok', $data['jumlah']);
            $barang->sinkronkanKeluar();
            $barang->perbaruiSuffixKode();

            $unitIds = $barang->units()
                ->where('status', BarangUnit::STATUS_KELUAR)
                ->orderBy('nomor_urut')
                ->limit($data['jumlah'])
                ->pluck('id');

            $barang->units()->whereIn('id', $unitIds)
                ->update(['status' => BarangUnit::STATUS_DI_GUDANG]);

            $barang->historis()->create([
                'aksi' => 'kembali',
                'detail' => "Barang kembali {$data['jumlah']} unit {$barang->kode_produk} dari pengeluaran #{$barangKeluar->id} oleh ".Auth::user()?->name.' (sisa stok menjadi '.$barang->fresh()->sisa_stok.', barang menjadi barang lama).',
            ]);
        });

        return redirect()->route('keluar.index')
            ->with('sukses', 'Barang kembali berhasil dicatat.');
    }

    public function jual(Request $request, BarangKeluar $barangKeluar): RedirectResponse
    {
        $this->pastikanBerhakMengubah($barangKeluar);

        $data = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $sisa = $barangKeluar->sisa_belum_kembali;

        if ($data['jumlah'] > $sisa) {
            throw ValidationException::withMessages([
                'jumlah' => "Jumlah terjual ({$data['jumlah']}) melebihi sisa belum terjual ({$sisa}).",
            ]);
        }

        $barang = $barangKeluar->barang;

        DB::transaction(function () use ($data, $barangKeluar, $barang) {
            BarangKeluar::create([
                'barang_id' => $barang->id,
                'user_id' => Auth::id(),
                'jumlah' => $data['jumlah'],
                'terjual' => true,
                'terjual_dari' => $barangKeluar->id,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $barang->incrementQuietly('terjual', $data['jumlah']);
            $barang->sinkronkanKeluar();

            $unitIds = $barang->units()
                ->where('status', BarangUnit::STATUS_KELUAR)
                ->orderBy('nomor_urut')
                ->limit($data['jumlah'])
                ->pluck('id');

            $barang->units()->whereIn('id', $unitIds)
                ->update(['status' => BarangUnit::STATUS_TERJUAL]);

            $barang->historis()->create([
                'aksi' => 'terjual',
                'detail' => "Barang terjual {$data['jumlah']} unit {$barang->kode_produk} dari pengeluaran #{$barangKeluar->id} oleh ".Auth::user()?->name.' (terjual menjadi '.$barang->fresh()->terjual.', keluar menjadi '.$barang->fresh()->keluar.').',
            ]);
        });

        return redirect()->route('keluar.index')
            ->with('sukses', 'Barang terjual berhasil dicatat.');
    }

    private function pastikanBerhakMengubah(BarangKeluar $barangKeluar): void
    {
        $user = Auth::user();

        if (! $user->isAdmin() && $barangKeluar->user_id !== $user->id) {
            abort(403);
        }

        if ($barangKeluar->kembali || $barangKeluar->terjual || $barangKeluar->kembali_dari || $barangKeluar->terjual_dari) {
            abort(404);
        }
    }
}
