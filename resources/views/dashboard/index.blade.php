@extends('layouts.app')

@php
    $judul = 'Dashboard Inventori';
    $breadcrumbs = [['label' => 'Dashboard']];
    $pemanfaatan = $kapasitas > 0 ? round(($totalSisa / $kapasitas) * 100) : 0;
    $fmt = fn ($nilai) => 'Rp ' . number_format($nilai, 0, ',', '.');
@endphp

@section('konten')
    {{-- BANNER OVERLOAD --}}
    @if ($overload)
        <div class="mb-6 flex items-center justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-rose-800">Peringatan: Gudang melebihi kapasitas maksimum!</p>
                    <p class="text-sm text-rose-700">
                        Total sisa stok {{ number_format($totalSisa, 0, ',', '.') }} unit melebihi kapasitas {{ number_format($kapasitas, 0, ',', '.') }} unit. Segera evaluasi penempatan barang.
                    </p>
                </div>
            </div>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('konfigurasi.index') }}" class="btn-secondary shrink-0 !text-slate-700">Atur Kapasitas</a>
            @endif
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        <x-summary-card
            label="Total SKU Produk"
            :nilai="number_format($totalSku, 0, ',', '.')"
            sub="{{ $totalJenis }} jenis sepatu terdaftar"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M20.25 7.5l-8.25-4.5-8.25 4.5v9l8.25 4.5 8.25-4.5v-9zM12 3v18M17.25 13.5L6.75 7.5M17.25 6.75L6.75 12.75' />"
        />
        <x-summary-card
            label="Sisa Stok Gudang"
            :nilai="number_format($totalSisa, 0, ',', '.') . ' unit'"
            sub="Terisi {{ $pemanfaatan }}% dari kapasitas {{ number_format($kapasitas, 0, ',', '.') }} unit"
            :peringatan="$overload"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M6.429 9.75L12 12.75l5.571-3M12 12.75v6.75M6.429 14.25L12 17.25l5.571-3M6.429 6.75L12 3l5.571 3.75L12 10.5 6.429 6.75z' />"
        />
        <x-summary-card
            label="Total Qty"
            :nilai="number_format($totalQty, 0, ',', '.') . ' unit'"
            sub="Total barang tercatat (masuk gudang)"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9A2.25 2.25 0 004.5 18.75z' />"
        />
        <x-summary-card
            label="Total Terjual"
            :nilai="number_format($totalTerjual, 0, ',', '.') . ' unit'"
            sub="Unit yang sudah terjual"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M14.25 7.756a4.5 4.5 0 100 8.488M7.5 10.5h5.25m-5.25 3h5.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z' />"
        />
        <x-summary-card
            label="Nilai Stok Gudang"
            :nilai="$fmt($totalNilaiStok)"
            sub="Modal tersimpan (sisa x harga gudang)"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6h18a.75.75 0 01.75.75v1.5a.75.75 0 01-.75.75H2.25a.75.75 0 01-.75-.75V6a.75.75 0 01.75-.75z' />"
        />
        <x-summary-card
            label="Potensi Omzet Sisa"
            :nilai="$fmt($potensiOmzet)"
            sub="Estimasi penjualan (sisa x harga jual)"
            icon="<path stroke-linecap='round' stroke-linejoin='round' d='M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z' />"
        />
    </div>

    {{-- GRAFIK + RINGKASAN --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="card-head flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-brand-950">Analisis Stok per Jenis Sepatu</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Perbandingan sisa stok dan unit terjual terhadap kapasitas maksimum.</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                    <span class="h-1.5 w-1.5 rounded-full {{ $overload ? 'bg-rose-500' : 'bg-brand-500' }}"></span>
                    Kapasitas: {{ number_format($kapasitas, 0, ',', '.') }} unit
                </span>
            </div>
            <div class="p-5">
                <div class="relative h-80">
                    <canvas id="grafik-jenis"></canvas>
                </div>
                <script type="application/json" id="grafik-jenis-data">
                    {
                        "kapasitas": {{ $kapasitas }},
                        "totalSisa": {{ $totalSisa }},
                        "stokPerJenis": @json($stokPerJenis)
                    }
                </script>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <h2 class="text-base font-bold text-brand-950">Pemanfaatan Kapasitas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Ketersediaan ruang gudang satuan saat ini.</p>
            </div>
            <div class="p-5">
                <div class="flex items-end justify-between">
                    <p class="font-mono text-3xl font-bold tabular-nums {{ $overload ? 'text-rose-600' : 'text-brand-950' }}">{{ $pemanfaatan }}%</p>
                    <p class="text-xs text-slate-500">
                        <span class="font-semibold text-slate-700">{{ number_format($totalSisa, 0, ',', '.') }}</span> / {{ number_format($kapasitas, 0, ',', '.') }} unit
                    </p>
                </div>
                <div class="mt-3 h-3 w-full overflow-hidden rounded-full bg-slate-200">
                    <div class="h-full rounded-full {{ $overload ? 'bg-rose-500' : 'bg-brand-700' }}" style="width: {{ min($pemanfaatan, 100) }}%"></div>
                </div>
            </div>

            <div class="border-t border-slate-100">
                <div class="card-head">
                    <h2 class="text-sm font-bold text-brand-950">Top Merek per Sisa Stok</h2>
                    <p class="mt-0.5 text-xs text-slate-500">Merek dengan sisa terbanyak di gudang.</p>
                </div>
                <ul class="divide-y divide-slate-100 px-5 py-2">
                    @forelse ($stokPerMerk->take(5) as $merk)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand-50 text-xs font-bold text-brand-700">
                                    {{ strtoupper(Str::substr($merk['merk'], 0, 2)) }}
                                </span>
                                <span class="text-sm font-medium text-slate-700">{{ $merk['merk'] }}</span>
                            </div>
                            <span class="font-mono text-sm font-semibold tabular-nums text-brand-950">
                                {{ number_format($merk['total'], 0, ',', '.') }}
                            </span>
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-slate-400">Belum ada data merek.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- GRAFIK MEREK --}}
    <div class="card mt-6">
        <div class="card-head">
            <h2 class="text-base font-bold text-brand-950">Sisa Stok per Merek</h2>
            <p class="mt-0.5 text-sm text-slate-500">Distribusi sisa stok seluruh merek di gudang.</p>
        </div>
        <div class="p-5">
            <div class="relative h-72">
                <canvas id="grafik-merk"></canvas>
            </div>
            <script type="application/json" id="grafik-merk-data">
                @json($stokPerMerk)
            </script>
        </div>
    </div>

    {{-- BARANG TERBARU --}}
    <div class="card mt-6">
        <div class="card-head flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-brand-950">Produk Terbaru</h2>
                <p class="mt-0.5 text-sm text-slate-500">Produk yang terakhir tercatat di sistem.</p>
            </div>
            @if (auth()->user()?->isAdmin())
                <a href="{{ route('barang.index') }}" class="btn-secondary">Lihat Semua</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                        <th class="px-5 py-3 font-semibold">Produk</th>
                        <th class="px-5 py-3 font-semibold">Kode</th>
                        <th class="px-5 py-3 font-semibold text-right">Terjual</th>
                        <th class="px-5 py-3 font-semibold text-right">Sisa Stok</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($barangTerbaru as $barang)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <p class="font-medium text-slate-800">{{ $barang->jenis_barang }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $barang->merk_produk }} · ukuran {{ $barang->ukuran_produk }} · {{ $barang->warna_produk }}</p>
                            </td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500">{{ $barang->kodeTampil() }}</td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-slate-600">{{ number_format($barang->terjual, 0, ',', '.') }}</td>
                            <td class="px-5 py-3 text-right font-mono font-semibold tabular-nums {{ $barang->sisa_stok > 0 ? 'text-brand-950' : 'text-slate-400' }}">
                                {{ number_format($barang->sisa_stok, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3"><x-status-badge :status="$barang->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada data produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection