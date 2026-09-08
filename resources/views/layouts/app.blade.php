@php
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $judul ?? 'Dashboard' }} · Dashboard Gudang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-brand-950 text-slate-300">
            <div class="flex items-center gap-3 border-b border-white/10 px-5 py-5">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-700 text-white shadow-inner">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12l8-4.5M12 12v9M12 12L4 7.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold tracking-wide text-white">Gudang<span class="text-sky-400">Flow</span></p>
                    <p class="text-[11px] text-slate-400">Manajemen Inventori</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
                <p class="px-3 pb-2 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('barang.index') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('barang.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-8.25-4.5-8.25 4.5v9l8.25 4.5 8.25-4.5v-9zM12 3v18M17.25 13.5L6.75 7.5M17.25 6.75L6.75 12.75" />
                    </svg>
                    Data Barang
                </a>

                <a href="{{ route('keluar.index') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('keluar.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15M12 9l3-3m0 0l-3-3m3 3H9m9 3v4.5a2.25 2.25 0 01-2.25 2.25H9" />
                    </svg>
                    Barang Keluar
                </a>

                @if ($user?->isAdmin())
                <p class="px-3 pb-2 pt-4 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Laporan</p>

                <a href="{{ route('laporan.gudang') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('laporan.gudang') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z" />
                    </svg>
                    Stok di Gudang
                </a>

                <a href="{{ route('laporan.baru') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('laporan.baru') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15l3-3m0 0l3 3m-3-3v6m-6.75 0A2.25 2.25 0 014.5 15.75v-7.5A2.25 2.25 0 016.75 6h3c.222 0 .44.05.64.143L12 6.75l2.61-.607a2.25 2.25 0 01.64-.143h3a2.25 2.25 0 012.25 2.25v7.5A2.25 2.25 0 0118.25 18h-7.5A2.25 2.25 0 018.5 15.75v-7.5A2.25 2.25 0 0110.75 6h3" />
                    </svg>
                    Barang Baru
                </a>

                <a href="{{ route('laporan.lama') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('laporan.lama') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                    Barang Lama
                </a>

                <a href="{{ route('laporan.terjual') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('laporan.terjual') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    Barang Terjual
                </a>

                <p class="px-3 pb-2 pt-4 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Pengaturan</p>

                <a href="{{ route('konfigurasi.index') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('konfigurasi.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    Kapasitas Gudang
                </a>

                <a href="{{ route('histori.index') }}"
                   class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('histori.*') ? 'bg-white/10 text-white shadow-sm' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Aktivitas
                </a>
                @endif
            </nav>

            <div class="border-t border-white/10 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-700 text-xs font-bold text-white">
                        {{ strtoupper(\Illuminate\Support\Str::substr($user?->name ?? 'U', 0, 2)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-white">{{ $user?->name }}</p>
                        <p class="text-[11px] text-slate-400">{{ \App\Models\User::daftarRole()[$user?->role] ?? '' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="rounded-lg p-2 text-slate-400 transition hover:bg-white/10 hover:text-rose-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5A2.25 2.25 0 003.75 5.25v13.5A2.25 2.25 0 005.25 21h7.5a2.25 2.25 0 002.25-2.25V15M12 9l3-3m0 0l-3-3m3 3H9m9 3v4.5a2.25 2.25 0 01-2.25 2.25H9" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- KONTEN UTAMA --}}
        <div class="flex min-h-screen w-full flex-1 flex-col pl-64">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/80 backdrop-blur">
                <div class="flex flex-col gap-4 px-8 py-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
<p class="text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                            @foreach ($breadcrumbs ?? [['label' => 'Dashboard']] as $b)
                                @if (($b['url'] ?? null))
                                    <a href="{{ $b['url'] }}" class="hover:text-brand-600">{{ $b['label'] }}</a>
                                @else
                                    {{ $b['label'] }}
                                @endif
                                @if (! $loop->last)
                                    <span class="mx-1 text-slate-300">/</span>
                                @endif
                            @endforeach
                        </p>
                        <h1 class="mt-0.5 text-xl font-bold tracking-tight text-brand-950">{{ $judul ?? 'Dashboard' }}</h1>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2 shadow-card">
                            <svg class="h-5 w-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="leading-tight">
                                <p id="jam-waktu" class="font-mono text-base font-bold tabular-nums text-brand-950">--:--:--</p>
                                <p id="jam-tanggal" class="text-[11px] text-slate-500">—</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-8 py-6">
                @if (session('sukses'))
                    <div class="mb-5 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                        <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ session('sukses') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <p class="mb-1 font-semibold">Terdapat kesalahan pada form:</p>
                        <ul class="ml-4 list-disc space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('konten')
            </main>

            <footer class="border-t border-slate-200 bg-white px-8 py-4 text-xs text-slate-400">
                &copy; {{ date('Y') }} GudangFlow · Sistem manajemen inventori gudang. Seluruh transaksi tercatat otomatis.
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>