<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KonfigurasiGudang;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSku = Barang::count();
        $totalJenis = Barang::distinct('jenis_barang')->count('jenis_barang');
        $totalQty = (int) Barang::sum('qty');
        $totalSisa = (int) Barang::sum('sisa_stok');
        $totalTerjual = (int) Barang::sum('terjual');
        $totalNilaiStok = (int) Barang::selectRaw('SUM(sisa_stok * harga_gudang) as nilai')->value('nilai');
        $potensiOmzet = (int) Barang::selectRaw('SUM(sisa_stok * harga_jual) as nilai')->value('nilai');

        $kapasitas = KonfigurasiGudang::kapasitasAktif();
        $overload = $kapasitas > 0 && $totalSisa > $kapasitas;

        $stokPerJenis = Barang::selectRaw('jenis_barang, SUM(sisa_stok) as sisa, SUM(terjual) as terjual')
            ->groupBy('jenis_barang')
            ->orderByDesc('sisa')
            ->get()
            ->map(fn ($row) => [
                'jenis' => $row->jenis_barang,
                'sisa' => (int) $row->sisa,
                'terjual' => (int) $row->terjual,
            ]);

        $stokPerMerk = Barang::selectRaw('merk_produk, SUM(sisa_stok) as total')
            ->groupBy('merk_produk')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'merk' => ucfirst($row->merk_produk),
                'total' => (int) $row->total,
            ]);

        $barangTerbaru = Barang::latest('id')->take(5)->get();

        return view('dashboard.index', compact(
            'totalSku',
            'totalJenis',
            'totalQty',
            'totalSisa',
            'totalTerjual',
            'totalNilaiStok',
            'potensiOmzet',
            'kapasitas',
            'overload',
            'stokPerJenis',
            'stokPerMerk',
            'barangTerbaru',
        ));
    }
}
