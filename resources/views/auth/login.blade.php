<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · Dashboard Gudang</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-brand-950 px-4">

    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 text-sky-400 shadow-inner">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12l8-4.5M12 12v9M12 12L4 7.5" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Gudang<span class="text-sky-400">Flow</span></h1>
            <p class="mt-1 text-sm text-slate-400">Masuk untuk mengelola inventori gudang.</p>
        </div>

        <div class="rounded-2xl border border-white/10 bg-white p-6 shadow-2xl">
            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.proses') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="input" placeholder="admin@example.com">
                </div>
                <div>
                    <label for="password" class="label">Password</label>
                    <input type="password" name="password" id="password" required class="input" placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                    Ingat saya
                </label>
                <button type="submit" class="btn-primary w-full !justify-center py-2.5 !text-base">
                    Masuk
                </button>
            </form>

            <p class="mt-5 border-t border-slate-100 pt-4 text-center text-xs text-slate-400">
                Akun demo: <span class="font-mono font-medium text-slate-600">admin@example.com</span> / <span class="font-mono font-medium text-slate-600">password</span><br>
                <span class="font-mono font-medium text-slate-600">penjual@example.com</span> / <span class="font-mono font-medium text-slate-600">password</span>
            </p>
        </div>
    </div>

</body>
</html>