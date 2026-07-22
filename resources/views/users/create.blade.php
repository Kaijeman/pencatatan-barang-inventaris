@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman. --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Pengguna
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Tambahkan akun pengguna baru ke dalam sistem.
            </p>
        </div>

        {{-- Form tambah pengguna. --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form
                method="POST"
                action="{{ route('users.store') }}"
            >
                @csrf

                @include('users._form', [
                    'buttonText' => 'Simpan Pengguna',
                ])
            </form>
        </div>
    </div>
@endsection
