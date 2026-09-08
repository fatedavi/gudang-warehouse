@extends('layouts.app')

@php
    $judul = 'Data Barang';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Barang'],
    ];
@endphp

@section('konten')
    <div class="card">
        {{-- TOOLBAR: SEARCH & FILTER --}}
        <div class="card-head">
            <form method="GET" action="{{ route('barang.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                <div class="flex-1">
                    <label for="cari" class="label">Cari Produk</label>
                    <input type="text" name="cari" id="cari" value="{{ $filter['cari'] ?? '' }}"
                           placeholder="Cari kode, merek, atau jenis…" class="input">
                </div>
                <div class="w-full sm:w-56">
                    <label for="jenis_barang" class="label">Jenis Barang</label>
                    <select name="jenis_barang" id="jenis_barang" class="input">
                        <option value="">Semua Jenis</option>
                        @foreach ($daftarJenis as $jenis)
                            <option value="{{ $jenis }}" @selected(($filter['jenis_barang'] ?? '') === $jenis)>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-44">
                    <label for="merk_produk" class="label">Merek</label>
                    <select name="merk_produk" id="merk_produk" class="input">
                        <option value="">Semua Merek</option>
                        @foreach ($daftarMerk as $merk)
                            <option value="{{ $merk }}" @selected(($filter['merk_produk'] ?? '') === $merk)>{{ $merk }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-40">
                    <label for="status" class="label">Status</label>
                    <select name="status" id="status" class="input">
                        <option value="">Semua Status</option>
                        @foreach ($daftarStatus as $kunci => $teks)
                            <option value="{{ $kunci }}" @selected(($filter['status'] ?? '') === $kunci)>{{ $teks }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        Terapkan
                    </button>
                    @if (collect($filter)->filter()->isNotEmpty())
                        <a href="{{ route('barang.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-semibold text-slate-700">{{ $barangs->total() }}</span> data produk
                @if (collect($filter)->filter()->isNotEmpty())
                    <span class="text-slate-400">(hasil filter)</span>
                @endif
            </p>
            <a href="{{ route('barang.create') }}" class="btn-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Barang
            </a>
        </div>

        {{-- TABEL --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-y border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3 font-semibold">Produk</th>
                        <th class="px-5 py-3 font-semibold">Kode Produk</th>
                        <th class="px-5 py-3 font-semibold">Jenis Barang</th>
                        <th class="px-5 py-3 font-semibold text-right">Qty</th>
                        <th class="px-5 py-3 font-semibold text-right">Terjual</th>
                        <th class="px-5 py-3 font-semibold text-right">Keluar</th>
                        <th class="px-5 py-3 font-semibold text-right">Sisa Stok</th>
                        <th class="px-5 py-3 font-semibold text-right">Harga Jual</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($barangs as $barang)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800">
                                    {{ $barang->merk_produk }} <span class="text-slate-400">uk. {{ $barang->ukuran_produk }}</span>
                                </p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $barang->warna_produk }} · {{ $barang->kondisi_barang }}</p>
                            </td>
                            <td class="px-5 py-3">
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 font-mono text-xs text-slate-600">{{ $barang->kodeTampil() }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-600">{{ $barang->jenis_barang }}</td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-slate-600">{{ number_format($barang->qty, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-slate-600">{{ number_format($barang->terjual, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-amber-600">{{ number_format($barang->keluar, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-mono font-semibold tabular-nums {{ $barang->sisa_stok > 0 ? 'text-brand-950' : 'text-slate-400' }}">
                                {{ number_format($barang->sisa_stok, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-slate-700">
                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3"><x-status-badge :status="$barang->status" /></td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('barang.show', $barang) }}" title="Lihat" class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('barang.edit', $barang) }}" title="Edit" class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('barang.destroy', $barang) }}" method="POST" data-confirm="Hapus produk '{{ $barang->kodeTampil() }}'? Tindakan ini tidak dapat dibatalkan.">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus" class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
                                <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-8.25-4.5-8.25 4.5v9l8.25 4.5 8.25-4.5v-9zM12 3v18M17.25 13.5L6.75 7.5M17.25 6.75L6.75 12.75" />
                                </svg>
                                <p class="text-sm font-medium text-slate-500">Tidak ada data ditemukan</p>
                                <p class="mt-1 text-sm text-slate-400">Coba ubah kata kunci pencarian atau filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 px-5 py-4">
            {{ $barangs->links() }}
        </div>
    </div>
@endsection