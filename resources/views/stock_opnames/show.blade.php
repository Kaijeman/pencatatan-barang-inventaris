@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Judul halaman --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Detail Stock Opname
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Pemeriksaan stok {{ $stockOpname->item->name }}.
                </p>
            </div>

            <a href="{{ route('stock-opnames.index') }}"
               class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                Kembali
            </a>
        </div>

        {{-- Pesan berhasil --}}
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- Informasi opname --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Tanggal Opname
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $stockOpname->opname_date->format('d/m/Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Barang
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $stockOpname->item->name }}
                    </p>

                    <p class="text-xs text-slate-500">
                        {{ $stockOpname->item->code }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Kategori
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $stockOpname->item->category->name }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase text-slate-400">
                        Dicatat Oleh
                    </p>

                    <p class="mt-2 font-semibold text-slate-800">
                        {{ $stockOpname->user->name }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Perbandingan stok --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
            <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Stok Sistem
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-800">
                    {{ $stockOpname->system_stock }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $stockOpname->item->unit }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Stok Fisik
                </p>

                <p class="mt-3 text-3xl font-bold text-slate-800">
                    {{ $stockOpname->physical_stock }}
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    {{ $stockOpname->item->unit }}
                </p>
            </div>

            <div class="rounded-xl bg-white p-6 text-center shadow-sm">
                <p class="text-sm font-medium text-slate-500">
                    Selisih
                </p>

                @if ($stockOpname->difference > 0)
                    <p class="mt-3 text-3xl font-bold text-blue-600">
                        +{{ $stockOpname->difference }}
                    </p>
                @elseif ($stockOpname->difference < 0)
                    <p class="mt-3 text-3xl font-bold text-red-600">
                        {{ $stockOpname->difference }}
                    </p>
                @else
                    <p class="mt-3 text-3xl font-bold text-green-600">
                        0
                    </p>
                @endif

                <p class="mt-1 text-sm text-slate-500">
                    {{ $stockOpname->item->unit }}
                </p>
            </div>
        </div>

        {{-- Catatan opname --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-slate-800">
                Catatan
            </h2>

            <p class="mt-3 whitespace-pre-line text-sm text-slate-600">
                {{ $stockOpname->note ?: 'Tidak ada catatan.' }}
            </p>
        </div>
    </div>
@endsection
