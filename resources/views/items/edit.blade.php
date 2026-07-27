@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Barang
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi
                <span class="font-semibold text-slate-700">
                    {{ $item->name }}
                </span>.
            </p>
        </div>

        {{-- Form barang --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('items.update', $item) }}">

                @csrf
                @method('PUT')

                @include('items._form', [
                    'buttonText' => 'Simpan Perubahan',
                ])
            </form>
        </div>

    </div>
@endsection
