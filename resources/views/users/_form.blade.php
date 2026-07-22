<div class="space-y-6">

    {{-- Input nama pengguna. --}}
    <div>
        <label
            for="name"
            class="mb-2 block text-sm font-semibold text-slate-700"
        >
            Nama Pengguna
            <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old(
                'name',
                isset($user) ? $user->name : ''
            ) }}"
            placeholder="Masukkan nama lengkap pengguna"
            autocomplete="name"
            class="w-full rounded-lg border px-4 py-2.5
                text-sm outline-none transition
                @error('name')
                    border-red-500 focus:border-red-500
                    focus:ring-2 focus:ring-red-200
                @else
                    border-slate-300 focus:border-blue-500
                    focus:ring-2 focus:ring-blue-200
                @enderror"
        >

        @error('name')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Input email pengguna. --}}
    <div>
        <label
            for="email"
            class="mb-2 block text-sm font-semibold text-slate-700"
        >
            Email
            <span class="text-red-500">*</span>
        </label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old(
                'email',
                isset($user) ? $user->email : ''
            ) }}"
            placeholder="nama@perusahaan.com"
            autocomplete="email"
            class="w-full rounded-lg border px-4 py-2.5
                text-sm outline-none transition
                @error('email')
                    border-red-500 focus:border-red-500
                    focus:ring-2 focus:ring-red-200
                @else
                    border-slate-300 focus:border-blue-500
                    focus:ring-2 focus:ring-blue-200
                @enderror"
        >

        @error('email')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Input password dan konfirmasi password. --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        {{-- Input password pengguna. --}}
        <div>
            <label
                for="password"
                class="mb-2 block text-sm font-semibold
                    text-slate-700"
            >
                {{ isset($user)
                    ? 'Password Baru'
                    : 'Password' }}

                @unless (isset($user))
                    <span class="text-red-500">*</span>
                @endunless
            </label>

            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    placeholder="{{ isset($user)
                        ? 'Kosongkan apabila tidak diubah'
                        : 'Minimal 8 karakter' }}"
                    class="w-full rounded-lg border px-4 py-2.5
                        pr-12 text-sm outline-none transition
                        @error('password')
                            border-red-500 focus:border-red-500
                            focus:ring-2 focus:ring-red-200
                        @else
                            border-slate-300 focus:border-blue-500
                            focus:ring-2 focus:ring-blue-200
                        @enderror"
                >

                {{-- Tombol menampilkan atau menyembunyikan password. --}}
                <button
                    type="button"
                    class="toggle-password absolute inset-y-0
                        right-0 flex w-12 items-center
                        justify-center text-slate-500 transition
                        hover:text-slate-700 focus:outline-none"
                    data-target="password"
                    aria-label="Tampilkan password"
                    aria-pressed="false"
                >
                    <i class="bi bi-eye-slash text-lg"></i>
                </button>
            </div>

            @error('password')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror

            @if (isset($user))
                <p class="mt-2 text-xs text-slate-500">
                    Kosongkan password apabila tidak ingin
                    mengubahnya.
                </p>
            @else
                <p class="mt-2 text-xs text-slate-500">
                    Password minimal terdiri dari 8 karakter.
                </p>
            @endif
        </div>

        {{-- Input konfirmasi password. --}}
        <div>
            <label
                for="password_confirmation"
                class="mb-2 block text-sm font-semibold
                    text-slate-700"
            >
                Konfirmasi Password

                @unless (isset($user))
                    <span class="text-red-500">*</span>
                @endunless
            </label>

            <div class="relative">
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    placeholder="Masukkan kembali password"
                    class="w-full rounded-lg border px-4 py-2.5
                        pr-12 text-sm outline-none transition
                        @error('password_confirmation')
                            border-red-500 focus:border-red-500
                            focus:ring-2 focus:ring-red-200
                        @else
                            border-slate-300 focus:border-blue-500
                            focus:ring-2 focus:ring-blue-200
                        @enderror"
                >

                {{-- Tombol menampilkan atau menyembunyikan konfirmasi password. --}}
                <button
                    type="button"
                    class="toggle-password absolute inset-y-0
                        right-0 flex w-12 items-center
                        justify-center text-slate-500 transition
                        hover:text-slate-700 focus:outline-none"
                    data-target="password_confirmation"
                    aria-label="Tampilkan konfirmasi password"
                    aria-pressed="false"
                >
                    <i class="bi bi-eye-slash text-lg"></i>
                </button>
            </div>

            @error('password_confirmation')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Tombol aksi form pengguna. --}}
    <div
        class="flex flex-col-reverse gap-3 border-t
            border-slate-200 pt-6 sm:flex-row
            sm:justify-end"
    >
        <a
            href="{{ route('users.index') }}"
            class="rounded-lg border border-slate-300
                px-5 py-2.5 text-center text-sm font-semibold
                text-slate-600 transition hover:bg-slate-50"
        >
            Batal
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center
                gap-2 rounded-lg bg-blue-600 px-5 py-2.5
                text-sm font-semibold text-white transition
                hover:bg-blue-700"
        >
            <i class="bi bi-save"></i>

            {{ $buttonText }}
        </button>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleButtons = document.querySelectorAll(
                '.toggle-password'
            );

            /**
             * Menampilkan atau menyembunyikan input password.
             */
            function togglePasswordVisibility(event) {
                const button = event.currentTarget;
                const targetId = button.dataset.target;
                const passwordInput =
                    document.getElementById(targetId);
                const icon = button.querySelector('i');

                if (! passwordInput || ! icon) {
                    return;
                }

                const shouldShowPassword =
                    passwordInput.type === 'password';

                passwordInput.type = shouldShowPassword
                    ? 'text'
                    : 'password';

                icon.classList.toggle(
                    'bi-eye-slash',
                    ! shouldShowPassword
                );

                icon.classList.toggle(
                    'bi-eye',
                    shouldShowPassword
                );

                button.setAttribute(
                    'aria-pressed',
                    shouldShowPassword ? 'true' : 'false'
                );

                const isConfirmationInput =
                    targetId === 'password_confirmation';

                if (shouldShowPassword) {
                    button.setAttribute(
                        'aria-label',
                        isConfirmationInput
                            ? 'Sembunyikan konfirmasi password'
                            : 'Sembunyikan password'
                    );

                    return;
                }

                button.setAttribute(
                    'aria-label',
                    isConfirmationInput
                        ? 'Tampilkan konfirmasi password'
                        : 'Tampilkan password'
                );
            }

            /**
             * Memasang event pada seluruh tombol password.
             */
            toggleButtons.forEach((button) => {
                button.addEventListener(
                    'click',
                    togglePasswordVisibility
                );
            });
        });
    </script>
@endpush
