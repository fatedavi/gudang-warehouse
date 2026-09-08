@props(['status', 'label' => null])

@php
    $gaya = match ($status) {
        'baru' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        'lama' => 'bg-amber-50 text-amber-700 ring-amber-600/25',
        'di_gudang' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'terjual' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        default => 'bg-slate-50 text-slate-600 ring-slate-400/20',
    };
    $teks = $label ?? \App\Models\Barang::statusList()[$status] ?? ucfirst($status);
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset {{ $gaya }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $teks }}
</span>