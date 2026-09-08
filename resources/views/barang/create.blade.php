@extends('layouts.app')

@php
    $judul = 'Tambah Barang';
    $breadcrumbs = [
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Data Barang', 'url' => route('barang.index')],
        ['label' => 'Tambah Barang'],
    ];
@endphp

@section('konten')
    <div class="mx-auto max-w-3xl">
        <div class="card">
            <div class="card-head">
                <h2 class="text-base font-bold text-brand-950">Form Tambah Barang</h2>
                <p class="mt-0.5 text-sm text-slate-500">Lengkapi informasi barang untuk dicatat di gudang. Sistem mencatat timestamp otomatis.</p>
            </div>
            <form action="{{ route('barang.store') }}" method="POST" class="p-6">
                @csrf
                @include('barang.form')
                <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('barang.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection