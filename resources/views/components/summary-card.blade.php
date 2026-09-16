@props(['icon', 'label', 'nilai', 'sub' => null, 'peringatan' => false])

<div class="card relative overflow-hidden p-5">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $label }}</p>
            <p data-hitung class="mt-2 text-2xl font-bold tabular-nums tracking-tight {{ $peringatan ? 'text-rose-600' : 'text-brand-950' }}" {{ $attributes }}>
                {{ $nilai }}
            </p>
            @if ($sub)
                <p class="mt-1 text-xs text-slate-500">{{ $sub }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $peringatan ? 'bg-rose-50 text-rose-600' : 'bg-brand-50 text-brand-700' }}">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                {!! $icon !!}
            </svg>
        </div>
    </div>
    @if ($peringatan)
        <div class="absolute inset-x-0 top-0 h-1 bg-rose-500"></div>
    @endif
</div>