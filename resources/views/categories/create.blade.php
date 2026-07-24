@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Kategori
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Masukkan informasi kategori barang baru.
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('categories.store') }}">

                @csrf

                @include('categories._form', [
                    'buttonText' => 'Simpan Kategori',
                ])
            </form>
        </div>

    </div>
@endsection
