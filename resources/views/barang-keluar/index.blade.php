@extends('layouts.app')

@php
    $isAdmin = auth()->user()?->isAdmin();
    $judul = $isAdmin ? 'Barang Keluar' : 'Barang yang Saya Bawa';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => $judul],
    ];
@endphp

@section('konten')
    @if ($isAdmin)
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- FORM CATAT KELUAR --}}
            <div class="card h-fit xl:col-span-1">
                <div class="card-head">
                    <h2 class="text-base font-bold text-brand-950">Catat Barang Keluar</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Admin mencatat pengambilan barang atas nama penjual. Stok gudang berkurang otomatis.</p>
                </div>

                <form action="{{ route('keluar.store') }}" method="POST" class="space-y-4 p-6">
                    @csrf
                    <div>
                        <label for="user_id" class="label">User Penjual <span class="text-rose-500">*</span></label>
                        <select name="user_id" id="user_id" required class="input">
                            <option value="">-- Pilih User Penjual --</option>
                            @foreach ($daftarUser as $u)
                                <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="barang_id" class="label">Pilih Barang <span class="text-rose-500">*</span></label>
                        <select name="barang_id" id="barang_id" required class="input">
                            <option value="">-- Pilih Barang (sisa stok > 0) --</option>
                            @foreach ($daftarBarang as $b)
                                <option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>
                                    {{ $b->kodeTampil() }} — {{ $b->jenis_barang }} {{ $b->merk_produk }} uk. {{ $b->ukuran_produk }} (sisa {{ $b->sisa_stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="jumlah" class="label">Jumlah Keluar <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" required min="1" step="1" class="input font-mono"
                               value="{{ old('jumlah') }}" placeholder="contoh: 2">
                        <p id="hint-jumlah" class="mt-1 text-xs text-slate-500">Maksimal sesuai sisa stok barang.</p>
                        @error('jumlah')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="catatan" class="label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="2" class="input resize-none" placeholder="opsional">{{ old('catatan') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15" />
                        </svg>
                        Simpan Barang Keluar
                    </button>
                </form>
            </div>

            {{-- DAFTAR TRANSAKSI --}}
            <div class="card xl:col-span-2">
                <div class="card-head">
                    <h2 class="text-base font-bold text-brand-950">Daftar Barang Keluar</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Seluruh catatan barang yang diambil dari gudang oleh penjual.</p>
                </div>

                <div class="border-b border-slate-100 px-5 py-4">
                    <form method="GET" action="{{ route('keluar.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
                        <div class="flex-1">
                            <label for="cari" class="label">Cari Barang</label>
                            <input type="text" name="cari" id="cari" value="{{ $filter['cari'] ?? '' }}"
                                   placeholder="Kode, merek, atau jenis…" class="input">
                        </div>
                        <div class="w-full sm:w-48">
                            <label for="user_id" class="label">User Penjual</label>
                            <select name="user_id" id="user_id" class="input">
                                <option value="">Semua User</option>
                                @foreach ($daftarUser as $u)
                                    <option value="{{ $u->id }}" @selected(($filter['user_id'] ?? '') == $u->id)>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">Terapkan</button>
                            @if (collect($filter)->filter()->isNotEmpty())
                                <a href="{{ route('keluar.index') }}" class="btn-secondary">Reset</a>
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
                                <th class="px-5 py-3 font-semibold">Diambil Oleh</th>
                                <th class="px-5 py-3 font-semibold text-right">Jumlah</th>
                                <th class="px-5 py-3 font-semibold text-right">Kembali</th>
                                <th class="px-5 py-3 font-semibold text-right">Terjual</th>
                                <th class="px-5 py-3 font-semibold text-right">Sisa</th>
                                <th class="px-5 py-3 text-right font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($keluars as $k)
                                <tr class="align-top transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-3.5 font-mono text-xs tabular-nums text-slate-500">
                                        {{ $k->created_at->format('d M Y') }}
                                        <span class="block text-slate-400">{{ $k->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <p class="font-medium text-slate-800">{{ $k->barang?->merk_produk }} <span class="text-slate-400">uk. {{ $k->barang?->ukuran_produk }}</span></p>
                                        <p class="mt-0.5 text-xs text-slate-400">
                                            <span class="font-mono">{{ $k->barang?->kodeTampil() }}</span> · {{ $k->barang?->jenis_barang }}
                                        </p>
                                        @if ($k->catatan)
                                            <p class="mt-1 text-xs italic text-slate-400">"{{ $k->catatan }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $k->user?->name ?? '—' }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono font-semibold tabular-nums text-brand-950">{{ number_format($k->jumlah, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono tabular-nums text-emerald-700">{{ number_format($k->jumlah_sudah_kembali, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono tabular-nums text-sky-700">{{ number_format($k->jumlah_sudah_terjual, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3.5 text-right font-mono font-semibold tabular-nums {{ $k->sisa_belum_kembali > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                        {{ number_format($k->sisa_belum_kembali, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                            @if ($k->sisa_belum_kembali > 0)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    Berjalan
                                                </span>
                                            @else
                                                <div class="flex flex-col items-end gap-1">
                                                    @if ($k->jumlah_sudah_terjual > 0)
                                                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-medium text-sky-700 ring-1 ring-inset ring-sky-600/20">Laku</span>
                                                    @endif
                                                    @if ($k->jumlah_sudah_kembali > 0)
                                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Kembali</span>
                                                    @endif
                                                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Lengkap</span>
                                                </div>
                                            @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-16 text-center">
                                        <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15M12 9l3-3m0 0l-3-3m3 3H9m9 3v4.5a2.25 2.25 0 01-2.25 2.25H9" />
                                        </svg>
                                        <p class="text-sm font-medium text-slate-500">Belum ada barang keluar tercatat</p>
                                        <p class="mt-1 text-sm text-slate-400">Gunakan form di samping/kiri untuk mencatat barang keluar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $keluars->links() }}
                </div>
            </div>
        </div>
    @else
        {{-- ===== TAMPILAN PENJUAL ===== --}}
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            {{-- FORM AMBIL BARANG --}}
            <div class="card h-fit xl:col-span-1">
                <div class="card-head">
                    <h2 class="text-base font-bold text-brand-950">Ambil Barang dari Gudang</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Pilih barang untuk dibawa dijual. Stok gudang berkurang otomatis.</p>
                </div>

                <form action="{{ route('keluar.store') }}" method="POST" class="space-y-4 p-6">
                    @csrf
                    <div>
                        <label for="barang_id" class="label">Pilih Barang <span class="text-rose-500">*</span></label>
                        <select name="barang_id" id="barang_id" required class="input">
                            <option value="">-- Pilih Barang (sisa stok > 0) --</option>
                            @foreach ($daftarBarang as $b)
                                <option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>
                                    {{ $b->kodeTampil() }} — {{ $b->jenis_barang }} {{ $b->merk_produk }} uk. {{ $b->ukuran_produk }} (sisa {{ $b->sisa_stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="jumlah" class="label">Jumlah Diambil <span class="text-rose-500">*</span></label>
                        <input type="number" name="jumlah" id="jumlah" required min="1" step="1" class="input font-mono"
                               value="{{ old('jumlah') }}" placeholder="contoh: 2">
                        <p id="hint-jumlah" class="mt-1 text-xs text-slate-500">Maksimal sesuai sisa stok barang.</p>
                        @error('jumlah')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="catatan" class="label">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="2" class="input resize-none" placeholder="opsional">{{ old('catatan') }}</textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15" />
                        </svg>
                        Ambil Barang
                    </button>
                </form>
            </div>

            <div class="xl:col-span-2">
            {{-- RINGKASAN --}}
            @php
                $totalBawa = $keluars->sum('jumlah');
                $totalLaku = $keluars->sum('jumlah_sudah_terjual');
                $totalKembali = $keluars->sum('jumlah_sudah_kembali');
                $totalSisa = $keluars->sum('sisa_belum_kembali');
            @endphp
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Dibawa</p>
                    <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-brand-950">{{ number_format($totalBawa, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sudah Laku</p>
                    <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-sky-700">{{ number_format($totalLaku, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Dikembalikan</p>
                    <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-amber-700">{{ number_format($totalKembali, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-card">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Masih Dipegang</p>
                    <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-rose-600">{{ number_format($totalSisa, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- DAFTAR KARTU --}}
            <div class="card mt-6">
                <div class="card-head flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-brand-950">Barang yang Saya Bawa</h2>
                        <p class="mt-0.5 text-sm text-slate-500">Kelola barang yang Anda bawa untuk dijual.</p>
                    </div>
                    <form method="GET" action="{{ route('keluar.index') }}" class="flex items-center gap-2">
                        <input type="text" name="cari" id="cari" value="{{ $filter['cari'] ?? '' }}"
                               placeholder="Cari kode / merek…" class="input !w-64 !py-2">
                        <button type="submit" class="btn-primary !py-2">Cari</button>
                        @if (collect($filter)->filter()->isNotEmpty())
                            <a href="{{ route('keluar.index') }}" class="btn-secondary !py-2">Reset</a>
                        @endif
                    </form>
                </div>

                @forelse ($keluars as $k)
                    @php
                        $selesai = $k->sisa_belum_kembali <= 0;
                        $sisaItem = $k->sisa_belum_kembali;
                    @endphp
                    <div class="grid grid-cols-1 gap-4 border-t border-slate-100 p-5 lg:grid-cols-3 lg:items-center">
                        {{-- INFO BARANG --}}
                        <div class="lg:col-span-1">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-slate-800">{{ $k->barang?->merk_produk }} <span class="font-normal text-slate-400">uk. {{ $k->barang?->ukuran_produk }}</span></p>
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        <span class="font-mono">{{ $k->barang?->kodeTampil() }}</span> · {{ $k->barang?->jenis_barang }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-400">Diambil {{ $k->created_at->format('d M Y, H:i') }}</p>
                                    @if ($k->catatan)
                                        <p class="mt-1.5 text-xs italic text-slate-500">"{{ $k->catatan }}"</p>
                                    @endif
                                </div>
                                @if ($selesai)
                                    <span class="shrink-0 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Selesai</span>
                                @else
                                    <span class="shrink-0 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Berjalan</span>
                                @endif
                            </div>
                        </div>

                        {{-- PROGRESS JUMLAH --}}
                        <div class="lg:col-span-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-medium text-slate-500">Terjual</span>
                                <span class="font-mono text-sm font-semibold tabular-nums text-sky-700">{{ $k->jumlah_sudah_terjual }}</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <span class="text-xs font-medium text-slate-500">Dikembalikan</span>
                                <span class="font-mono text-sm font-semibold tabular-nums text-amber-700">{{ $k->jumlah_sudah_kembali }}</span>
                            </div>
                            <div class="mt-1 flex items-center justify-between gap-2">
                                <span class="text-xs font-medium text-slate-500">Sisa dipegang</span>
                                <span class="font-mono text-sm font-semibold tabular-nums {{ $sisaItem > 0 ? 'text-rose-600' : 'text-slate-400' }}">{{ $sisaItem }}</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <span class="font-mono text-xs tabular-nums text-slate-400">dari {{ $k->jumlah }}</span>
                                <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-sky-500" style="width: {{ $k->jumlah > 0 ? round($k->jumlah_sudah_terjual / $k->jumlah * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- AKSI --}}
                        <div class="lg:col-span-1">
                            @if ($sisaItem > 0)
                                <div class="flex flex-wrap gap-2">
                                <details class="group">
                                    <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 rounded-lg bg-sky-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-sky-700">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                        Jual
                                        <svg class="h-3 w-3 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </summary>
                                    <div class="pt-2">
                                    <form action="{{ route('keluar.jual', $k) }}" method="POST" class="rounded-xl border border-sky-100 bg-sky-50/50 p-3">
                                        @csrf
                                        <div class="mb-2 flex items-center justify-between">
                                            <label class="text-xs font-semibold text-slate-600">Jumlah terjual</label>
                                            <span class="font-mono text-[11px] text-slate-400">maks {{ $sisaItem }}</span>
                                        </div>
                                        <input type="number" name="jumlah" required min="1" max="{{ $sisaItem }}" step="1"
                                               class="input py-2 font-mono" placeholder="Jumlah">
                                        <input type="text" name="catatan" class="input mt-2 py-2" placeholder="Catatan (opsional)">
                                        <button type="submit" class="btn-primary mt-3 !w-full !py-2 !text-xs">Simpan Terjual</button>
                                    </form>
                                    </div>
                                </details>
                                <details class="group">
                                    <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                        </svg>
                                        Kembalikan
                                        <svg class="h-3 w-3 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </summary>
                                    <div class="pt-2">
                                    <form action="{{ route('keluar.kembali', $k) }}" method="POST" class="rounded-xl border border-amber-200 bg-amber-50/60 p-3">
                                        @csrf
                                        <div class="mb-2 flex items-center justify-between">
                                            <label class="text-xs font-semibold text-slate-600">Jumlah kembali</label>
                                            <span class="font-mono text-[11px] text-slate-400">maks {{ $sisaItem }}</span>
                                        </div>
                                        <input type="number" name="jumlah" required min="1" max="{{ $sisaItem }}" step="1"
                                               class="input py-2 font-mono" placeholder="Jumlah">
                                        <input type="text" name="catatan" class="input mt-2 py-2" placeholder="Catatan (opsional)">
                                        <button type="submit" class="btn-primary mt-3 !w-full !py-2 !text-xs">Simpan Kembali</button>
                                    </form>
                                    </div>
                                </details>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-xs font-medium text-slate-500">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Lengkap
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-16 text-center">
                        <svg class="mx-auto mb-3 h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15" />
                        </svg>
                        <p class="text-sm font-medium text-slate-500">Belum ada catatan barang untuk Anda</p>
                        <p class="mt-1 text-sm text-slate-400">Gunakan form "Ambil Barang dari Gudang" di samping.</p>
                    </div>
                @endforelse

                @if ($keluars->hasPages())
                    <div class="border-t border-slate-200 px-5 py-4">
                        {{ $keluars->links() }}
                    </div>
                @endif
            </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const pilihBarang = document.getElementById('barang_id');
            const inputJumlah = document.getElementById('jumlah');
            const hintJumlah = document.getElementById('hint-jumlah');

            if (!pilihBarang || !inputJumlah) {
                return;
            }

            function maxKeluar() {
                const option = pilihBarang.selectedOptions[0];
                const sisa = option ? parseInt(option.text.match(/sisa\s+(\d+)/)?.[1] || '0', 10) : 0;
                inputJumlah.max = sisa;
                hintJumlah.textContent = 'Maksimal ' + sisa + ' unit (sisa stok barang terpilih).';
                if (inputJumlah.value && parseInt(inputJumlah.value, 10) > sisa) {
                    inputJumlah.value = '';
                }
            }

            pilihBarang.addEventListener('change', maxKeluar);
            maxKeluar();
        })();
    </script>
@endpush