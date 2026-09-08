<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function gudang(Request $request): View
    {
        return $this->tampilkan($request, Barang::STATUS_DI_GUDANG);
    }

    public function baru(Request $request): View
    {
        return $this->tampilkan($request, Barang::STATUS_BARU);
    }

    public function lama(Request $request): View
    {
        return $this->tampilkan($request, Barang::STATUS_LAMA);
    }

    public function terjual(Request $request): View
    {
        return $this->tampilkan($request, Barang::STATUS_TERJUAL);
    }

    private function tampilkan(Request $request, string $status): View
    {
        $query = Barang::query();

        match ($status) {
            Barang::STATUS_TERJUAL => $query->terjual(),
            Barang::STATUS_DI_GUDANG => $query->stokGudang(),
            Barang::STATUS_BARU => $query->baru(),
            Barang::STATUS_LAMA => $query->lama(),
        };

        if ($cari = $request->input('cari')) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode_produk', 'like', "%{$cari}%")
                    ->orWhere('merk_produk', 'like', "%{$cari}%")
                    ->orWhere('jenis_barang', 'like', "%{$cari}%");
            });
        }

        if ($jenis = $request->input('jenis_barang')) {
            $query->where('jenis_barang', $jenis);
        }

        if ($merk = $request->input('merk_produk')) {
            $query->where('merk_produk', $merk);
        }

        $barangs = $query->latest('id')->paginate(10)->withQueryString();

        return view('laporan.index', [
            'status' => $status,
            'barangs' => $barangs,
            'daftarJenis' => Barang::distinct()->orderBy('jenis_barang')->pluck('jenis_barang'),
            'daftarMerk' => Barang::distinct()->orderBy('merk_produk')->pluck('merk_produk'),
            'ringkasan' => [
                Barang::STATUS_DI_GUDANG => Barang::stokGudang()->count(),
                Barang::STATUS_BARU => Barang::baru()->count(),
                Barang::STATUS_LAMA => Barang::lama()->count(),
                Barang::STATUS_TERJUAL => Barang::terjual()->count(),
            ],
            'filter' => $request->only(['cari', 'jenis_barang', 'merk_produk']),
        ]);
    }
}
