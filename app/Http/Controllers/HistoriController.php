<?php

namespace App\Http\Controllers;

use App\Models\HistoriBarang;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoriController extends Controller
{
    public function index(Request $request): View
    {
        $query = HistoriBarang::with('barang');

        if ($cari = $request->input('cari')) {
            $query->whereHas('barang', function ($q) use ($cari) {
                $q->where('kode_produk', 'like', "%{$cari}%")
                    ->orWhere('merk_produk', 'like', "%{$cari}%")
                    ->orWhere('jenis_barang', 'like', "%{$cari}%");
            });
        }

        if ($aksi = $request->input('aksi')) {
            $query->where('aksi', $aksi);
        }

        if ($dari = $request->input('dari')) {
            $query->whereDate('created_at', '>=', $dari);
        }

        if ($sampai = $request->input('sampai')) {
            $query->whereDate('created_at', '<=', $sampai);
        }

        return view('histori.index', [
            'historis' => $query->latest()->paginate(15)->withQueryString(),
            'filter' => $request->only(['cari', 'aksi', 'dari', 'sampai']),
        ]);
    }
}
