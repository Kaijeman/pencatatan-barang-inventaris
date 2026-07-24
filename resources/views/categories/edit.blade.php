@extends('layouts.app')

@section('title', 'Tambah Edi Keluar')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Kategori
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi kategori
                <span class="font-semibold text-slate-700">
                    {{ $category->name }}
                </span>.
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('categories.update', $category) }}">

                @csrf
                @method('PUT')

                @include('categories._form', [
                    'buttonText' => 'Simpan Perubahan',
                ])
            </form>
        </div>

    </div>
@endsection
