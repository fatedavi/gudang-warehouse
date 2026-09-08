@extends('layouts.app')

@php
    $judul = 'Riwayat Aktivitas';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Riwayat Aktivitas'],
    ];
@endphp

@section('konten')
    <div class="card">
        <div class="card-head">
            <form method="GET" action="{{ route('histori.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label for="cari" class="label">Cari Barang</label>
                    <input type="text" name="cari" id="cari" value="{{ $filter['cari'] ?? '' }}"
                           placeholder="Cari kode, merek, atau jenis…" class="input">
                </div>
                <div class="w-full sm:w-44">
                    <label for="aksi" class="label">Jenis Aksi</label>
                    <select name="aksi" id="aksi" class="input">
                        <option value="">Semua Aksi</option>
                        <option value="masuk" @selected(($filter['aksi'] ?? '') === 'masuk')>Masuk</option>
                        <option value="keluar" @selected(($filter['aksi'] ?? '') === 'keluar')>Keluar</option>
                        <option value="kembali" @selected(($filter['aksi'] ?? '') === 'kembali')>Kembali</option>
                        <option value="terjual" @selected(($filter['aksi'] ?? '') === 'terjual')>Terjual</option>
                        <option value="ubah" @selected(($filter['aksi'] ?? '') === 'ubah')>Diubah</option>
                        <option value="hapus" @selected(($filter['aksi'] ?? '') === 'hapus')>Dihapus</option>
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="dari" class="label">Tanggal Dari</label>
                    <input type="date" name="dari" id="dari" value="{{ $filter['dari'] ?? '' }}" class="input">
                </div>
                <div class="w-full sm:w-40">
                    <label for="sampai" class="label">Sampai</label>
                    <input type="date" name="sampai" id="sampai" value="{{ $filter['sampai'] ?? '' }}" class="input">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary">Terapkan</button>
                    @if (collect($filter)->filter()->isNotEmpty())
                        <a href="{{ route('histori.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-y border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3 font-semibold">Waktu</th>
                        <th class="px-5 py-3 font-semibold">Barang</th>
                        <th class="px-5 py-3 font-semibold">Aksi</th>
                        <th class="px-5 py-3 font-semibold">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($historis as $h)
                        <tr class="align-top transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-3.5 font-mono text-xs tabular-nums text-slate-500">
                                {{ $h->created_at->format('d M Y') }}
                                <span class="block text-slate-400">{{ $h->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-800">{{ $h->barang?->merk_produk ?? 'Produk terhapus' }}</p>
                                <p class="font-mono text-xs text-slate-400">{{ $h->barang?->kodeTampil() ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                @php
                                    $warna = match ($h->aksi) {
                                        'masuk' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'keluar' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                        'kembali' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
                                        'terjual' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                        'ubah' => 'bg-slate-100 text-slate-600 ring-slate-400/20',
                                        'hapus' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
                                        default => 'bg-slate-100 text-slate-600 ring-slate-400/20',
                                    };
                                    $lbl = match ($h->aksi) {
                                        'masuk' => 'Masuk',
                                        'keluar' => 'Keluar',
                                        'kembali' => 'Kembali',
                                        'terjual' => 'Terjual',
                                        'ubah' => 'Diubah',
                                        'hapus' => 'Dihapus',
                                        default => ucfirst($h->aksi),
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $warna }}">{{ $lbl }}</span>
                            </td>
                            <td class="max-w-md px-5 py-3.5 text-xs leading-relaxed text-slate-600">{{ $h->detail }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center text-sm text-slate-400">Belum ada aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $historis->links() }}
        </div>
    </div>
@endsection