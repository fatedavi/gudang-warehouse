@extends('layouts.app')

@php
    $judul = 'Kapasitas Gudang';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Kapasitas Gudang'],
    ];
@endphp

@section('konten')
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- FORM TAMBAH --}}
        <div class="card self-start">
            <div class="card-head">
                <h2 class="text-base font-bold text-brand-950">Tambah Konfigurasi Kapasitas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Kapasitas dengan status aktif dipakai grafik dashboard.</p>
            </div>
            <form action="{{ route('konfigurasi.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="nama" class="label">Nama Konfigurasi <span class="text-rose-500">*</span></label>
                        <input type="text" name="nama" id="nama" required class="input"
                               value="{{ old('nama') }}" placeholder="contoh: Kapasitas Gudang Utama">
                    </div>
                    <div>
                        <label for="kapasitas_maks" class="label">Kapasitas Maksimum (unit) <span class="text-rose-500">*</span></label>
                        <input type="number" name="kapasitas_maks" id="kapasitas_maks" required min="1" step="1"
                               class="input font-mono" value="{{ old('kapasitas_maks') }}" placeholder="contoh: 5000">
                    </div>
                    <label class="flex cursor-pointer items-center justify-between rounded-xl border border-slate-200 px-4 py-3 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <span class="text-sm font-medium text-slate-700">Jadikan kapasitas aktif</span>
                        <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded accent-brand-800" @checked(old('aktif', true))>
                    </label>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>

        {{-- DAFTAR KONFIGURASI --}}
        <div class="card xl:col-span-2">
            <div class="card-head">
                <h2 class="text-base font-bold text-brand-950">Daftar Kapasitas</h2>
                <p class="mt-0.5 text-sm text-slate-500">Semua konfigurasi kapasitas gudang yang pernah dicatat.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-y border-slate-200 bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                            <th class="px-5 py-3 font-semibold">Nama Konfigurasi</th>
                            <th class="px-5 py-3 font-semibold text-right">Kapasitas Maks</th>
                            <th class="px-5 py-3 font-semibold text-center">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($konfigurasis as $k)
                            <tr class="transition hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-800">
                                    {{ $k->nama }}
                                    <p class="mt-0.5 text-xs text-slate-400">Diperbarui {{ $k->updated_at->format('d M Y H:i') }}</p>
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-semibold tabular-nums text-brand-950">
                                    {{ number_format($k->kapasitas_maks, 0, ',', '.') }} unit
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($k->aktif)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500 ring-1 ring-inset ring-slate-400/20">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="#edit-{{ $k->id }}" title="Edit" class="rounded-lg p-2 text-slate-400 transition hover:bg-brand-50 hover:text-brand-700">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('konfigurasi.destroy', $k) }}" method="POST"
                                              data-confirm="Hapus konfigurasi '{{ $k->nama }}'?">
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

                            {{-- MODAL EDIT --}}
                            <tr id="edit-{{ $k->id }}" class="hidden">
                                <td colspan="4" class="bg-brand-50/50 px-5 py-4">
                                    <form action="{{ route('konfigurasi.update', $k) }}" method="POST" class="grid grid-cols-1 gap-3 md:grid-cols-2 md:items-end">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="label">Nama Konfigurasi</label>
                                            <input type="text" name="nama" value="{{ $k->nama }}" required class="input">
                                        </div>
                                        <div>
                                            <label class="label">Kapasitas Maks</label>
                                            <input type="number" name="kapasitas_maks" value="{{ $k->kapasitas_maks }}" required min="1" class="input font-mono">
                                        </div>
                                        <label class="flex items-center gap-2 text-sm text-slate-600">
                                            <input type="checkbox" name="aktif" value="1" class="h-4 w-4 rounded accent-brand-800" @checked($k->aktif)>
                                            Kapasitas aktif
                                        </label>
                                        <div class="flex justify-end gap-2">
                                            <a href="#" class="btn-secondary">Batal</a>
                                            <button type="submit" class="btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-16 text-center text-sm text-slate-400">
                                    Belum ada konfigurasi kapasitas. Tambahkan melalui form di samping.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('a[href^="#edit-"]').forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const baris = document.getElementById(link.getAttribute('href').slice(1));
                if (baris) {
                    const tersembunyi = baris.classList.contains('hidden');
                    document.querySelectorAll('tr[id^="edit-"]').forEach((tr) => tr.classList.add('hidden'));
                    if (tersembunyi) baris.classList.remove('hidden');
                }
            });
        });
    </script>
@endpush