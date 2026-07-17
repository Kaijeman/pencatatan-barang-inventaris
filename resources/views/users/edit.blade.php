@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Pengguna
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui informasi akun
                <span class="font-semibold text-slate-700">
                    {{ $user->name }}
                </span>.
            </p>
        </div>

        {{-- Pesan gagal --}}
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form pengguna --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST"
                  action="{{ route('users.update', $user) }}">

                @csrf
                @method('PUT')

                @include('users._form', [
                    'buttonText' => 'Simpan Perubahan',
                ])
            </form>
        </div>
    </div>
@endsection
