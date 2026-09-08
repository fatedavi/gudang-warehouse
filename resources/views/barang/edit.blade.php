@extends('layouts.app')

@php
    $judul = 'Edit Barang';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Barang', 'url' => route('barang.index')],
        ['label' => 'Edit ' . $barang->kodeTampil()],
    ];
@endphp

@section('konten')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-head flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-brand-950">Form Edit Barang</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Perubahan pada barang akan tercatat di riwayat aktivitas.</p>
                </div>
                <span class="rounded-full bg-slate-100 px-3 py-1 font-mono text-xs font-semibold text-slate-600">{{ $barang->kodeTampil() }}</span>
            </div>
            <form action="{{ route('barang.update', $barang) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                @include('barang.form')
                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('barang.show', $barang) }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection