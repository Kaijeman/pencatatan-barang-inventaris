<div class="space-y-6">

    {{-- Kategori barang --}}
    <div>
        <label for="category_id"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Kategori
            <span class="text-red-500">*</span>
        </label>

        <select id="category_id"
                name="category_id"
                class="w-full rounded-lg border px-4 py-2.5 text-sm text-slate-700 outline-none transition
                    @error('category_id')
                        border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                    @else
                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                    @enderror">

            <option value="">Pilih kategori</option>

            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(
                        old(
                            'category_id',
                            isset($item) ? $item->category_id : ''
                        ) == $category->id
                    )>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                <i class="bi bi-exclamation-circle"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Kode dan nama barang --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="code"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Kode Barang
                <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   id="code"
                   name="code"
                   value="{{ old('code', isset($item) ? $item->code : '') }}"
                   placeholder="Contoh: BRG001"
                   class="w-full rounded-lg border px-4 py-2.5 text-sm uppercase text-slate-700 outline-none transition
                        @error('code')
                            border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-200
                        @else
                            border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                        @enderror">

            @error('code')
                <p class="mt-2 flex items-center gap-1 text-sm text-red-600">
                    <i class="bi bi-exclamation-circle"></i>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="name"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Nama Barang
                <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   id="name"
                   name="name"
                   value="{{ old('name', isset($item) ? $item->name : '') }}"
                   placeholder="Contoh: Laptop Lenovo"
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
    </div>

    {{-- Satuan, harga, dan stok minimum --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div>
            <label for="unit"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Satuan
                <span class="text-red-500">*</span>
            </label>

            <input type="text"
                   id="unit"
                   name="unit"
                   value="{{ old('unit', isset($item) ? $item->unit : '') }}"
                   placeholder="Unit, Box, Pcs"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

            @error('unit')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="purchase_price"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Harga Beli
                <span class="text-red-500">*</span>
            </label>

            <input type="number"
                   id="purchase_price"
                   name="purchase_price"
                   value="{{ old('purchase_price', isset($item) ? $item->purchase_price : 0) }}"
                   min="0"
                   step="0.01"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

            @error('purchase_price')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label for="minimum_stock"
                   class="mb-2 block text-sm font-semibold text-slate-700">
                Stok Minimum
                <span class="text-red-500">*</span>
            </label>

            <input type="number"
                   id="minimum_stock"
                   name="minimum_stock"
                   value="{{ old('minimum_stock', isset($item) ? $item->minimum_stock : 0) }}"
                   min="0"
                   step="1"
                   class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">

            @error('minimum_stock')
                <p class="mt-2 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Deskripsi barang --}}
    <div>
        <label for="description"
               class="mb-2 block text-sm font-semibold text-slate-700">
            Deskripsi
        </label>

        <textarea id="description"
                  name="description"
                  rows="4"
                  placeholder="Masukkan keterangan barang..."
                  class="w-full resize-y rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200">{{ old('description', isset($item) ? $item->description : '') }}</textarea>

        @error('description')
            <p class="mt-2 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Tombol form --}}
    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ route('items.index') }}"
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
