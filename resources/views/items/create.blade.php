@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Barang
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan informasi barang baru. Stok awal barang akan bernilai nol.
            </p>
        </div>

        {{-- Form barang --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('items.store') }}">

                @csrf

                @include('items._form', [
                    'buttonText' => 'Simpan Barang',
                ])
            </form>
        </div>

    </div>
@endsection
