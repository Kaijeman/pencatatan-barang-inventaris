@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman. --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Edit Akun Saya
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui nama, email, atau password akun Anda.
            </p>
        </div>

        {{-- Pesan berhasil. --}}
        @if (session('success'))
            <div
                class="rounded-lg border border-green-200
                    bg-green-50 px-4 py-3 text-sm text-green-700"
            >
                {{ session('success') }}
            </div>
        @endif

        {{-- Form edit akun. --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form
                method="POST"
                action="{{ route('users.update', $user) }}"
            >
                @csrf
                @method('PUT')

                @include('users._form', [
                    'buttonText' => 'Simpan Perubahan',
                ])
            </form>
        </div>

        {{-- Bagian hapus akun. --}}
        <div
            class="rounded-xl border border-red-200
                bg-white p-6 shadow-sm"
        >
            <div>
                <h2 class="text-lg font-bold text-red-700">
                    Hapus Akun
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Akun tidak dapat digunakan kembali setelah dihapus.
                    Riwayat transaksi tetap tersimpan.
                </p>
            </div>

            <form
                method="POST"
                action="{{ route('users.destroy', $user) }}"
                class="mt-5"
                onsubmit="return confirm(
                    'Apakah Anda yakin ingin menghapus akun ini?'
                )"
            >
                @csrf
                @method('DELETE')

                {{-- Konfirmasi password pengguna. --}}
                <div>
                    <label
                        for="current_password"
                        class="mb-2 block text-sm font-semibold
                            text-slate-700"
                    >
                        Password Saat Ini
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        placeholder="Masukkan password saat ini"
                        class="w-full rounded-lg border px-4 py-2.5
                            text-sm outline-none transition
                            @error('current_password')
                                border-red-500 focus:border-red-500
                                focus:ring-2 focus:ring-red-200
                            @else
                                border-slate-300 focus:border-red-500
                                focus:ring-2 focus:ring-red-200
                            @enderror"
                    >

                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-5 flex justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center
                            gap-2 rounded-lg bg-red-600 px-5 py-2.5
                            text-sm font-semibold text-white transition
                            hover:bg-red-700"
                    >
                        <i class="bi bi-trash"></i>

                        Hapus Akun Saya
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
