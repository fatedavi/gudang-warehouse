@extends('layouts.app')

@php
    $isAdmin = auth()->user()?->isAdmin();
    $judul = 'Detail Produk';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Barang', 'url' => route('barang.index')],
        ['label' => $barang->kodeTampil()],
    ];
    $fmt = fn ($nilai) => 'Rp ' . number_format($nilai, 0, ',', '.');
@endphp

@section('konten')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="card xl:col-span-2">
            <div class="card-head flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-brand-950">{{ $barang->merk_produk }} <span class="text-slate-400">uk. {{ $barang->ukuran_produk }}</span></h2>
                    <p class="mt-0.5 font-mono text-sm text-slate-500">{{ $barang->kodeTampil() }}</p>
                </div>
                <x-status-badge :status="$barang->status" />
            </div>

            <dl class="grid grid-cols-1 gap-x-6 gap-y-5 p-6 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Jenis Barang</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $barang->jenis_barang }}</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Merk / Ukuran / Warna</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">
                        {{ $barang->merk_produk }} · {{ $barang->ukuran_produk }} · {{ $barang->warna_produk }}
                    </dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kondisi Barang</dt>
                    <dd class="mt-1 text-sm font-semibold capitalize text-slate-800">{{ $barang->kondisi_barang }}</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Unit</dt>
                    <dd data-hitung class="mt-1 font-mono text-lg font-bold tabular-nums text-brand-950">
                        {{ number_format($barang->qty, 0, ',', '.') }} unit
                    </dd>
                </div>
                <div class="rounded-xl border border-brand-100 bg-brand-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-brand-600">Sisa Stok</dt>
                    <dd data-hitung class="mt-1 font-mono text-2xl font-bold tabular-nums {{ $barang->sisa_stok > 0 ? 'text-brand-950' : 'text-slate-400' }}">
                        {{ number_format($barang->sisa_stok, 0, ',', '.') }} unit
                    </dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga Gudang</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $fmt($barang->harga_gudang) }}</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga Jual</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $fmt($barang->harga_jual) }}</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Margin Kotor</dt>
                    <dd class="mt-1 text-sm font-semibold text-emerald-700">{{ $fmt($barang->margin_kotor) }}</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Margin</dt>
                    <dd class="mt-1 text-sm font-semibold text-emerald-700">{{ number_format($barang->margin_persen, 1, ',', '.') }}%</dd>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terakhir Diubah</dt>
                    <dd class="mt-1 text-sm font-semibold text-slate-800">{{ $barang->updated_at->format('d M Y H:i:s') }}</dd>
                </div>
            </dl>

            <div class="border-t border-slate-100 px-6 py-5">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-bold uppercase tracking-wide text-brand-950">Komposisi Unit</h3>
                    <span class="text-xs text-slate-400">Total {{ number_format($barang->qty, 0, ',', '.') }} unit</span>
                </div>
                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Terjual</p>
                        <p class="mt-1 font-mono text-xl font-bold tabular-nums text-slate-700">{{ number_format($barang->unitTerjual(), 0, ',', '.') }}</p>
                        <p class="text-[11px] text-slate-400">unit terjual</p>
                    </div>
                    <div class="rounded-xl border border-rose-100 bg-rose-50/60 p-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-rose-500">Dipegang Penjual</p>
                        <p class="mt-1 font-mono text-xl font-bold tabular-nums text-rose-600">{{ number_format($barang->unitDiLuar(), 0, ',', '.') }}</p>
                        <p class="text-[11px] text-rose-400">masih di luar</p>
                    </div>
                    <div class="rounded-xl border border-amber-100 bg-amber-50/60 p-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-amber-600">Sisa Baru</p>
                        <p class="mt-1 font-mono text-xl font-bold tabular-nums text-amber-700">{{ number_format($barang->stokBelumKeluar(), 0, ',', '.') }}</p>
                        <p class="text-[11px] text-amber-500">di gudang</p>
                    </div>
                    <div class="rounded-xl border border-sky-100 bg-sky-50/60 p-3">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-sky-600">Sisa Lama</p>
                        <p class="mt-1 font-mono text-xl font-bold tabular-nums text-sky-700">{{ number_format($barang->stokSudahKeluar(), 0, ',', '.') }}</p>
                        <p class="text-[11px] text-sky-500">pernah keluar</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4">
                @if ($isAdmin)
                <a href="{{ route('barang.edit', $barang) }}" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    Edit Barang
                </a>
            @endif
                <a href="{{ route('barang.index') }}" class="btn-secondary">Kembali</a>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <h2 class="text-base font-bold text-brand-950">Riwayat Produk</h2>
                <p class="mt-0.5 text-sm text-slate-500">Aktivitas tercatat otomatis.</p>
            </div>
            <ul class="max-h-[420px] divide-y divide-slate-100 overflow-y-auto px-5 py-2">
                @forelse ($barang->historis()->latest()->take(15)->get() as $h)
                    <li class="py-3">
                        <div class="flex items-center gap-2">
                            @php
                                $warna = match ($h->aksi) {
                                    'masuk' => 'bg-emerald-100 text-emerald-700',
                                    'keluar' => 'bg-amber-100 text-amber-700',
                                    'kembali' => 'bg-sky-100 text-sky-700',
                                    'terjual' => 'bg-violet-100 text-violet-700',
                                    'ubah' => 'bg-slate-100 text-slate-600',
                                    'hapus' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-slate-100 text-slate-600',
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
                            <span class="rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $warna }}">{{ $lbl }}</span>
                            <span class="ml-auto font-mono text-[11px] tabular-nums text-slate-400">{{ $h->created_at->format('d M Y H:i:s') }}</span>
                        </div>
                        <p class="mt-1.5 text-xs leading-relaxed text-slate-600">{{ $h->detail }}</p>
                    </li>
                @empty
                    <li class="py-8 text-center text-sm text-slate-400">Belum ada riwayat.</li>
                @endforelse
            </ul>
        </div>
    </div>
@endsection