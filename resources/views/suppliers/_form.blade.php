<div class="space-y-6">

    <div>
        <label for="name"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Nama Supplier
            <span class="text-red-500">*</span>
        </label>

        <input type="text"
               id="name"
               name="name"
               value="{{ old('name', isset($supplier) ? $supplier->name : '') }}"
               placeholder="Contoh: PT Sumber Jaya"
               autofocus
               class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                    @error('name')
                        border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                    @else
                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                    @enderror">

        @error('name')
            <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        <div>
            <label for="phone"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Nomor Telepon
            </label>

            <input type="text"
                   id="phone"
                   name="phone"
                   value="{{ old('phone', isset($supplier) ? $supplier->phone : '') }}"
                   placeholder="Contoh: 081234567890"
                   class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                        @error('phone')
                            border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                        @else
                            border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                        @enderror">

            @error('phone')
                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="email"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Email
            </label>

            <input type="email"
                   id="email"
                   name="email"
                   value="{{ old('email', isset($supplier) ? $supplier->email : '') }}"
                   placeholder="Contoh: supplier@email.com"
                   class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                        @error('email')
                            border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                        @else
                            border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                        @enderror">

            @error('email')
                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

    </div>

    <div>
        <label for="address"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Alamat
        </label>

        <textarea id="address"
                  name="address"
                  rows="5"
                  placeholder="Masukkan alamat lengkap supplier..."
                  class="w-full resize-y rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                    @error('address')
                        border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                    @else
                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                    @enderror">{{ old('address', isset($supplier) ? $supplier->address : '') }}</textarea>

        @error('address')
            <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">

        <a href="{{ route('suppliers.index') }}"
           class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            Batal
        </a>

        <button type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

            <i class="bi bi-save"></i>

            {{ $buttonText }}
        </button>

    </div>
</div>
