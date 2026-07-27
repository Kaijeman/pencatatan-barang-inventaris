@extends('layouts.guest')

@section('title', 'Login')

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

            {{-- Kartu form login. --}}
            <div
                class="rounded-3xl border border-slate-200
                    bg-white p-6 shadow-xl shadow-slate-200/70
                    sm:p-8"
            >
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-slate-800">
                        Gudang
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Masukkan email dan password akun Anda.
                    </p>
                </div>

                {{-- Status dari sistem. --}}
                @if (session('status'))
                    <div
                        class="mt-6 rounded-xl border border-green-200
                            bg-green-50 px-4 py-3 text-sm
                            text-green-700"
                    >
                        <div class="flex items-start gap-3">
                            <i
                                class="bi bi-check-circle-fill mt-0.5"
                            ></i>

                            <p>
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                @endif

                {{-- Kesalahan login umum. --}}
                @if ($errors->any())
                    <div
                        class="mt-6 rounded-xl border border-red-200
                            bg-red-50 px-4 py-3 text-sm text-red-700"
                    >
                        <div class="flex items-start gap-3">
                            <i
                                class="bi bi-exclamation-circle-fill
                                    mt-0.5"
                            ></i>

                            <div>
                                <p class="font-semibold">
                                    Login gagal
                                </p>

                                <p class="mt-1">
                                    Periksa kembali email dan password
                                    yang dimasukkan.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Form login. --}}
                <form
                    method="POST"
                    action="{{ route('login') }}"
                    class="mt-7 space-y-5"
                >
                    @csrf

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
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan email"
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

                    {{-- Input password. --}}
                    <div>
                        <div
                            class="mb-2 flex items-center
                                justify-between gap-3"
                        >
                            <label
                                for="password"
                                class="block text-sm font-semibold
                                    text-slate-700"
                            >
                                Password
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route(
                                        'password.request'
                                    ) }}"
                                    class="text-xs font-semibold
                                        text-blue-600 transition
                                        hover:text-blue-700
                                        hover:underline"
                                >
                                    Lupa password?
                                </a>
                            @endif
                        </div>

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
                                autocomplete="current-password"
                                placeholder="Masukkan password"
                                class="w-full rounded-xl border py-3
                                    pl-12 pr-12 text-sm text-slate-700
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

                            {{-- Tombol menampilkan password. --}}
                            <button
                                type="button"
                                id="toggle-password"
                                title="Tampilkan password"
                                aria-label="Tampilkan password"
                                aria-pressed="false"
                                class="absolute inset-y-0 right-0
                                    flex w-12 items-center
                                    justify-center text-slate-400
                                    transition hover:text-slate-700
                                    focus:outline-none"
                            >
                                <i
                                    id="password-icon"
                                    class="bi bi-eye-slash"
                                ></i>
                            </button>
                        </div>

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Pilihan ingat akun. --}}
                    <label
                        for="remember"
                        class="inline-flex cursor-pointer
                            items-center gap-3"
                    >
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            class="h-4 w-4 rounded border-slate-300
                                text-blue-600 focus:ring-blue-500"
                            @checked(old('remember'))
                        >

                        <span class="text-sm text-slate-600">
                            Ingat saya
                        </span>
                    </label>

                    {{-- Tombol login. --}}
                    <button
                        type="submit"
                        class="inline-flex w-full items-center
                            justify-center gap-2 rounded-xl
                            bg-blue-600 px-5 py-3 text-sm
                            font-bold text-white shadow-lg
                            shadow-blue-600/20 transition
                            hover:-translate-y-0.5
                            hover:bg-blue-700 hover:shadow-xl
                            focus:outline-none focus:ring-4
                            focus:ring-blue-200"
                    >
                        <span>
                            Masuk
                        </span>

                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>

            {{-- Informasi keamanan. --}}
            <div
                class="mt-6 flex items-center justify-center
                    gap-2 text-center text-xs text-slate-500"
            >
                <i class="bi bi-shield-check text-green-600"></i>

                <span>
                    Akses hanya tersedia untuk pengguna terdaftar.
                </span>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButton = document.getElementById(
                'toggle-password'
            );

            const passwordInput = document.getElementById(
                'password'
            );

            const passwordIcon = document.getElementById(
                'password-icon'
            );

            if (
                ! toggleButton
                || ! passwordInput
                || ! passwordIcon
            ) {
                return;
            }

            /**
             * Menampilkan atau menyembunyikan password login.
             */
            toggleButton.addEventListener('click', () => {
                const shouldShow =
                    passwordInput.type === 'password';

                passwordInput.type = shouldShow
                    ? 'text'
                    : 'password';

                passwordIcon.classList.toggle(
                    'bi-eye-slash',
                    ! shouldShow
                );

                passwordIcon.classList.toggle(
                    'bi-eye',
                    shouldShow
                );

                toggleButton.setAttribute(
                    'aria-pressed',
                    shouldShow ? 'true' : 'false'
                );

                toggleButton.setAttribute(
                    'aria-label',
                    shouldShow
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                );

                toggleButton.setAttribute(
                    'title',
                    shouldShow
                        ? 'Sembunyikan password'
                        : 'Tampilkan password'
                );
            });
        });
    </script>
@endpush
