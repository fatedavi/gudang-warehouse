@extends('layouts.app')

@php
    $meta = match ($status) {
        \App\Models\Barang::STATUS_DI_GUDANG => ['Barang di Gudang', 'Semua barang dengan sisa stok di gudang (baru + lama).'],
        \App\Models\Barang::STATUS_BARU => ['Barang Baru', 'Unit yang belum pernah keluar dari gudang.'],
        \App\Models\Barang::STATUS_LAMA => ['Barang Lama', 'Unit yang pernah keluar dan masih tersisa di gudang.'],
        \App\Models\Barang::STATUS_CAMPURAN => ['Baru + Lama', 'Barang dengan campuran unit baru dan lama.'],
        \App\Models\Barang::STATUS_TERJUAL => ['Barang Terjual', 'Barang yang sudah terjual, sebagian atau seluruhnya.'],
    };
    $judul = 'Laporan ' . $meta[0];
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Laporan'],
        ['label' => $meta[0]],
    ];
@endphp

@section('konten')
    {{-- RINGKASAN PER STATUS --}}
    <div class="mb-5 grid grid-cols-2 gap-3 xl:grid-cols-5">
        <a href="{{ route('laporan.gudang') }}" class="rounded-xl border p-4 shadow-card transition {{ $status === \App\Models\Barang::STATUS_DI_GUDANG ? 'border-emerald-300 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Di Gudang</p>
            <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-emerald-700">{{ number_format($ringkasan[\App\Models\Barang::STATUS_DI_GUDANG], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] leading-snug text-slate-400">Produk yang masih punya sisa stok di gudang.</p>
        </a>
        <a href="{{ route('laporan.baru') }}" class="rounded-xl border p-4 shadow-card transition {{ $status === \App\Models\Barang::STATUS_BARU ? 'border-sky-300 bg-sky-50' : 'border-slate-200 bg-white hover:border-sky-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Barang Baru</p>
            <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-sky-700">{{ number_format($ringkasan[\App\Models\Barang::STATUS_BARU], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] leading-snug text-slate-400">Unit di gudang yang belum pernah keluar.</p>
        </a>
        <a href="{{ route('laporan.lama') }}" class="rounded-xl border p-4 shadow-card transition {{ $status === \App\Models\Barang::STATUS_LAMA ? 'border-amber-300 bg-amber-50' : 'border-slate-200 bg-white hover:border-amber-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Barang Lama</p>
            <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-amber-700">{{ number_format($ringkasan[\App\Models\Barang::STATUS_LAMA], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] leading-snug text-slate-400">Unit di gudang yang pernah keluar lalu kembali.</p>
        </a>
        <a href="{{ route('keluar.index') }}" class="rounded-xl border p-4 shadow-card transition border-rose-200 bg-white hover:border-rose-300">
            <p class="text-xs font-semibold uppercase tracking-wide text-rose-500">Barang di Luar</p>
            <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-rose-600">{{ number_format($ringkasan[\App\Models\BarangUnit::STATUS_KELUAR], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] leading-snug text-slate-400">Unit masih dipegang penjual / di luar belum kembali.</p>
        </a>
        <a href="{{ route('laporan.terjual') }}" class="rounded-xl border p-4 shadow-card transition {{ $status === \App\Models\Barang::STATUS_TERJUAL ? 'border-slate-300 bg-slate-50' : 'border-slate-200 bg-white hover:border-slate-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Terjual</p>
            <p class="mt-1 font-mono text-2xl font-bold tabular-nums text-slate-700">{{ number_format($ringkasan[\App\Models\Barang::STATUS_TERJUAL], 0, ',', '.') }}</p>
            <p class="mt-1 text-[11px] leading-snug text-slate-400">Total unit yang sudah terjual.</p>
        </a>
    </div>

    <div class="card">
        {{-- TOOLBAR: SEARCH & FILTER --}}
        <div class="card-head">
            <p class="text-sm text-slate-500">{{ $meta[1] }}</p>
            <div class="mt-3 border-t border-slate-100 pt-3">
                <form method="GET" action="{{ request()->url() }}" class="flex flex-col gap-3 lg:flex-row lg:items-end">
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
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary">Terapkan</button>
                        @if (collect($filter)->filter()->isNotEmpty())
                            <a href="{{ request()->url() }}" class="btn-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <div class="flex items-center justify-between px-5 py-4">
            <p class="text-sm text-slate-500">
                Menampilkan <span class="font-semibold text-slate-700">{{ $barangs->total() }}</span> data
                @if (collect($filter)->filter()->isNotEmpty())
                    <span class="text-slate-400">(hasil filter)</span>
                @endif
            </p>
            @php
                $pdfRoute = match ($status) {
                    \App\Models\Barang::STATUS_DI_GUDANG => 'laporan.gudang.pdf',
                    \App\Models\Barang::STATUS_BARU => 'laporan.baru.pdf',
                    \App\Models\Barang::STATUS_LAMA => 'laporan.lama.pdf',
                    \App\Models\Barang::STATUS_TERJUAL => 'laporan.terjual.pdf',
                };
            @endphp
            <a href="{{ route($pdfRoute) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Download PDF
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
                        <th class="px-5 py-3 font-semibold">Unit</th>
                        <th class="px-5 py-3 font-semibold text-right">Harga Jual</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
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
                            <td class="whitespace-nowrap px-5 py-3 text-xs">
                                @php
                                    $bagian = [
                                        $barang->stokBelumKeluar() > 0 ? ['Baru', $barang->stokBelumKeluar(), 'text-sky-600'] : null,
                                        $barang->stokSudahKeluar() > 0 ? ['Lama', $barang->stokSudahKeluar(), 'text-amber-600'] : null,
                                        $barang->unitDiLuar() > 0 ? ['Diluar', $barang->unitDiLuar(), 'text-rose-600'] : null,
                                        $barang->unitTerjual() > 0 ? ['Terjual', $barang->unitTerjual(), 'text-slate-500'] : null,
                                    ];
                                @endphp
                                @forelse (array_filter($bagian) as $i => [$label, $nilai, $warna])
                                    @if ($i > 0)
                                        <span class="mx-0.5 text-slate-300">/</span>
                                    @endif
                                    <span class="{{ $warna }}">{{ $label }} {{ $nilai }}</span>
                                @empty
                                    <span class="text-slate-400">—</span>
                                @endforelse
                            </td>
                            <td class="px-5 py-3 text-right font-mono tabular-nums text-slate-700">
                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3"><x-status-badge :status="$barang->status" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-5 py-16 text-center">
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