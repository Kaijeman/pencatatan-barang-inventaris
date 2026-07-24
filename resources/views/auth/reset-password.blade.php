@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
    <div
        class="relative flex min-h-screen items-center justify-center
            overflow-hidden bg-slate-100 px-4 py-10 sm:px-6"
    >
        {{-- Dekorasi latar belakang. --}}
        <div
            class="pointer-events-none absolute -left-32 -top-32
                h-80 w-80 rounded-full bg-blue-300/30 blur-3xl"
        ></div>

        <div
            class="pointer-events-none absolute -bottom-32 -right-32
                h-80 w-80 rounded-full bg-indigo-300/30 blur-3xl"
        ></div>

        <div class="relative z-10 w-full max-w-md">

            {{-- Identitas aplikasi. --}}
            <div class="mb-7 text-center">
                <div
                    class="mx-auto flex h-20 w-20 items-center
                        justify-center overflow-hidden rounded-2xl
                        bg-white p-3 shadow-lg shadow-slate-300/50"
                >
                    <img
                        src="{{ asset('images/logo.webp') }}"
                        alt="Logo Sistem Inventory Gudang"
                        class="h-full w-full object-contain"
                    >
                </div>
            </div>

            {{-- Kartu reset password. --}}
            <div
                class="rounded-3xl border border-slate-200
                    bg-white p-6 shadow-xl shadow-slate-200/70
                    sm:p-8"
            >
                <div class="text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center
                            justify-center rounded-full bg-blue-100
                            text-xl text-blue-600"
                    >
                        <i class="bi bi-shield-lock"></i>
                    </div>

                    <h2
                        class="mt-4 text-2xl font-bold
                            text-slate-800"
                    >
                        Buat Password Baru
                    </h2>

                    <p
                        class="mt-2 text-sm leading-6
                            text-slate-500"
                    >
                        Masukkan password baru untuk akun Anda.
                    </p>
                </div>

                {{-- Form reset password. --}}
                <form
                    method="POST"
                    action="{{ route('password.store') }}"
                    class="mt-7 space-y-5"
                >
                    @csrf

                    {{-- Token reset password. --}}
                    <input
                        type="hidden"
                        name="token"
                        value="{{ request()->route('token') }}"
                    >

                    {{-- Input email. --}}
                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Email
                        </label>

                        <div class="relative">
                            <div
                                class="pointer-events-none absolute
                                    inset-y-0 left-0 flex w-12
                                    items-center justify-center
                                    text-slate-400"
                            >
                                <i class="bi bi-envelope"></i>
                            </div>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old(
                                    'email',
                                    request()->query('email')
                                ) }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="w-full rounded-xl border py-3
                                    pl-12 pr-4 text-sm text-slate-700
                                    outline-none transition
                                    @error('email')
                                        border-red-400 bg-red-50/40
                                        focus:border-red-500
                                        focus:ring-4 focus:ring-red-100
                                    @else
                                        border-slate-300 bg-white
                                        focus:border-blue-500
                                        focus:ring-4 focus:ring-blue-100
                                    @enderror"
                            >
                        </div>

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Input password baru. --}}
                    <div>
                        <label
                            for="password"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Password Baru
                        </label>

                        <div class="relative">
                            <div
                                class="pointer-events-none absolute
                                    inset-y-0 left-0 flex w-12
                                    items-center justify-center
                                    text-slate-400"
                            >
                                <i class="bi bi-lock"></i>
                            </div>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                placeholder="Minimal 8 karakter"
                                class="reset-password-input w-full
                                    rounded-xl border py-3 pl-12
                                    pr-12 text-sm text-slate-700
                                    outline-none transition
                                    @error('password')
                                        border-red-400 bg-red-50/40
                                        focus:border-red-500
                                        focus:ring-4 focus:ring-red-100
                                    @else
                                        border-slate-300 bg-white
                                        focus:border-blue-500
                                        focus:ring-4 focus:ring-blue-100
                                    @enderror"
                            >

                            <button
                                type="button"
                                class="toggle-reset-password absolute
                                    inset-y-0 right-0 flex w-12
                                    items-center justify-center
                                    text-slate-400 transition
                                    hover:text-slate-700"
                                data-target="password"
                                aria-label="Tampilkan password baru"
                            >
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi password baru. --}}
                    <div>
                        <label
                            for="password_confirmation"
                            class="mb-2 block text-sm font-semibold
                                text-slate-700"
                        >
                            Konfirmasi Password Baru
                        </label>

                        <div class="relative">
                            <div
                                class="pointer-events-none absolute
                                    inset-y-0 left-0 flex w-12
                                    items-center justify-center
                                    text-slate-400"
                            >
                                <i class="bi bi-lock-fill"></i>
                            </div>

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Masukkan kembali password"
                                class="reset-password-input w-full
                                    rounded-xl border border-slate-300
                                    bg-white py-3 pl-12 pr-12
                                    text-sm text-slate-700 outline-none
                                    transition focus:border-blue-500
                                    focus:ring-4 focus:ring-blue-100"
                            >

                            <button
                                type="button"
                                class="toggle-reset-password absolute
                                    inset-y-0 right-0 flex w-12
                                    items-center justify-center
                                    text-slate-400 transition
                                    hover:text-slate-700"
                                data-target="password_confirmation"
                                aria-label="Tampilkan konfirmasi password"
                            >
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol reset password. --}}
                    <button
                        type="submit"
                        class="inline-flex w-full items-center
                            justify-center gap-2 rounded-xl
                            bg-blue-600 px-5 py-3 text-sm
                            font-bold text-white shadow-lg
                            shadow-blue-600/20 transition
                            hover:-translate-y-0.5
                            hover:bg-blue-700 hover:shadow-xl"
                    >
                        <i class="bi bi-check-circle"></i>

                        <span>
                            Simpan Password Baru
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButtons = document.querySelectorAll(
                '.toggle-reset-password'
            );

            /**
             * Menampilkan atau menyembunyikan input password.
             */
            toggleButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const targetId = button.dataset.target;
                    const input = document.getElementById(targetId);
                    const icon = button.querySelector('i');

                    if (! input || ! icon) {
                        return;
                    }

                    const shouldShow =
                        input.type === 'password';

                    input.type = shouldShow
                        ? 'text'
                        : 'password';

                    icon.classList.toggle(
                        'bi-eye-slash',
                        ! shouldShow
                    );

                    icon.classList.toggle(
                        'bi-eye',
                        shouldShow
                    );
                });
            });
        });
    </script>
@endpush
