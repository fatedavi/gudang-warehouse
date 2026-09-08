<?php

namespace App\Http\Controllers;

use App\Models\KonfigurasiGudang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KonfigurasiController extends Controller
{
    public function index(): View
    {
        return view('konfigurasi.index', [
            'konfigurasis' => KonfigurasiGudang::latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request, null);

        KonfigurasiGudang::create($data);

        return redirect()->route('konfigurasi.index')
            ->with('sukses', 'Konfigurasi kapasitas berhasil ditambahkan.');
    }

    public function update(Request $request, KonfigurasiGudang $konfigurasi): RedirectResponse
    {
        $data = $this->validateData($request, $konfigurasi->id);

        $konfigurasi->update($data);

        return redirect()->route('konfigurasi.index')
            ->with('sukses', 'Konfigurasi kapasitas berhasil diperbarui.');
    }

    public function destroy(KonfigurasiGudang $konfigurasi): RedirectResponse
    {
        $konfigurasi->delete();

        return redirect()->route('konfigurasi.index')
            ->with('sukses', 'Konfigurasi kapasitas berhasil dihapus.');
    }

    private function validateData(Request $request, ?int $id): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kapasitas_maks' => ['required', 'integer', 'min:1'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => $request->boolean('aktif')];
    }
}
