<div class="space-y-6">

    {{-- Nama kategori --}}
    <div>
        <label for="name"
               class="mb-2 block text-sm font-semibold text-slate-700">

            Nama Kategori
            <span class="text-red-500">*</span>
        </label>

        <input type="text"
               id="name"
               name="name"
               value="{{ old('name', isset($category) ? $category->name : '') }}"
               placeholder="Contoh: Elektronik"
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

    {{-- Deskripsi --}}
    <div>
        <label for="description"
               class="mb-2 block text-sm font-semibold text-slate-700">

            Deskripsi
        </label>

        <textarea id="description"
                  name="description"
                  rows="5"
                  placeholder="Masukkan keterangan mengenai kategori..."
                  class="w-full resize-y rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                    @error('description')
                        border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                    @else
                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                    @enderror">{{ old('description', isset($category) ? $category->description : '') }}</textarea>

        @error('description')
            <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </p>
        @enderror

        <p class="mt-2 text-xs text-slate-400">
            Deskripsi bersifat opsional.
        </p>
    </div>

    {{-- Tombol --}}
    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">

        <a href="{{ route('categories.index') }}"
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
