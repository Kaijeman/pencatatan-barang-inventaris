@extends('layouts.guest')

@section('title', 'Lupa Password')

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

            {{-- Kartu lupa password. --}}
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
                        <i class="bi bi-key"></i>
                    </div>

                    <h2
                        class="mt-4 text-2xl font-bold
                            text-slate-800"
                    >
                        Lupa Password
                    </h2>

                    <p
                        class="mt-2 text-sm leading-6
                            text-slate-500"
                    >
                        Masukkan email akun Anda. Sistem akan
                        mengirimkan tautan untuk membuat password baru.
                    </p>
                </div>

                {{-- Status pengiriman tautan reset. --}}
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

                {{-- Form permintaan reset password. --}}
                <form
                    method="POST"
                    action="{{ route('password.email') }}"
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
                                autocomplete="email"
                                placeholder="Masukkan email terdaftar"
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

                    {{-- Tombol kirim tautan reset. --}}
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
                        <i class="bi bi-send"></i>

                        <span>
                            Kirim Tautan Reset
                        </span>
                    </button>
                </form>

                {{-- Tautan kembali ke login. --}}
                <div
                    class="mt-6 border-t border-slate-200
                        pt-5 text-center"
                >
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-2
                            text-sm font-semibold text-blue-600
                            transition hover:text-blue-700
                            hover:underline"
                    >
                        <i class="bi bi-arrow-left"></i>

                        Kembali ke halaman login
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
