@extends('layouts.app')

@section('content')
    @php
        /*
         * Mencari kembali barang terpilih ketika validasi gagal.
         */
        $selectedItem = $items->firstWhere(
            'id',
            old('item_id')
        );
    @endphp

    <div class="mx-auto max-w-4xl space-y-6">

        {{-- Judul halaman --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Tambah Stock Opname
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Bandingkan stok sistem dengan jumlah fisik di gudang.
            </p>
        </div>

        @if ($items->isEmpty())
            {{-- Peringatan ketika barang belum tersedia --}}
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                Belum ada data barang yang dapat diperiksa.
            </div>
        @endif

        {{-- Kesalahan validasi umum --}}
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                Terdapat data yang belum benar. Periksa kembali formulir.
            </div>
        @endif

        <form method="POST"
              action="{{ route('stock-opnames.store') }}"
              class="space-y-6">

            @csrf

            <div class="rounded-xl bg-white p-6 shadow-sm">
                <div class="space-y-6">

                    {{-- Pilihan barang --}}
                    <div>
                        <label for="item_id"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Barang
                            <span class="text-red-500">*</span>
                        </label>

                        <select id="item_id"
                                name="item_id"
                                class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition
                                    @error('item_id')
                                        border-red-500
                                    @else
                                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                                    @enderror">

                            <option value="">Pilih barang</option>

                            @foreach ($items as $item)
                                <option value="{{ $item->id }}"
                                        data-stock="{{ $item->stock }}"
                                        data-unit="{{ $item->unit }}"
                                    @selected(old('item_id') == $item->id)>

                                    {{ $item->code }} -
                                    {{ $item->name }}
                                    ({{ $item->category->name }})
                                </option>
                            @endforeach
                        </select>

                        @error('item_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Informasi stok --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Stok Sistem
                            </label>

                            <div id="system-stock-display"
                                 class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700">

                                @if ($selectedItem)
                                    {{ $selectedItem->stock }}
                                    {{ $selectedItem->unit }}
                                @else
                                    Pilih barang terlebih dahulu
                                @endif
                            </div>
                        </div>

                        <div>
                            <label for="physical_stock"
                                   class="mb-2 block text-sm font-semibold text-slate-700">
                                Stok Fisik
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="number"
                                   id="physical_stock"
                                   name="physical_stock"
                                   value="{{ old('physical_stock') }}"
                                   min="0"
                                   step="1"
                                   placeholder="0"
                                   class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition
                                        @error('physical_stock')
                                            border-red-500
                                        @else
                                            border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                                        @enderror">

                            @error('physical_stock')
                                <p class="mt-2 text-sm text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Selisih
                            </label>

                            <div id="difference-display"
                                 class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700">

                                -
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal opname --}}
                    <div>
                        <label for="opname_date"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Tanggal Opname
                            <span class="text-red-500">*</span>
                        </label>

                        <input type="date"
                               id="opname_date"
                               name="opname_date"
                               value="{{ old('opname_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition
                                    @error('opname_date')
                                        border-red-500
                                    @else
                                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                                    @enderror">

                        @error('opname_date')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label for="note"
                               class="mb-2 block text-sm font-semibold text-slate-700">
                            Catatan
                        </label>

                        <textarea id="note"
                                  name="note"
                                  rows="4"
                                  placeholder="Jelaskan penyebab selisih, misalnya barang rusak, hilang, atau kesalahan pencatatan..."
                                  class="w-full rounded-lg border px-4 py-2.5 text-sm outline-none transition
                                    @error('note')
                                        border-red-500
                                    @else
                                        border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200
                                    @enderror">{{ old('note') }}</textarea>

                        @error('note')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                        <p class="mt-2 text-xs text-slate-500">
                            Catatan wajib diisi apabila terdapat selisih stok.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tombol form --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('stock-opnames.index') }}"
                   class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>

                <button type="submit"
                        @disabled($items->isEmpty())
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">

                    <i class="bi bi-save"></i>

                    Simpan Stock Opname
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemSelect = document.getElementById('item_id');
            const physicalStockInput =
                document.getElementById('physical_stock');
            const systemStockDisplay =
                document.getElementById('system-stock-display');
            const differenceDisplay =
                document.getElementById('difference-display');

            let systemStock = null;
            let unit = '';

            /**
             * Memperbarui informasi stok barang terpilih.
             */
            function updateSelectedItem() {
                const selectedOption =
                    itemSelect.options[itemSelect.selectedIndex];

                if (
                    selectedOption &&
                    selectedOption.dataset.stock !== undefined
                ) {
                    systemStock = Number(
                        selectedOption.dataset.stock
                    );

                    unit = selectedOption.dataset.unit ?? '';

                    systemStockDisplay.textContent =
                        `${systemStock} ${unit}`;

                    updateDifference();
                    return;
                }

                systemStock = null;
                unit = '';

                systemStockDisplay.textContent =
                    'Pilih barang terlebih dahulu';

                differenceDisplay.textContent = '-';
            }

            /**
             * Menghitung dan menampilkan selisih stok.
             */
            function updateDifference() {
                if (
                    systemStock === null ||
                    physicalStockInput.value === ''
                ) {
                    differenceDisplay.textContent = '-';
                    differenceDisplay.className =
                        'rounded-lg border border-slate-200 ' +
                        'bg-slate-50 px-4 py-2.5 text-sm ' +
                        'font-semibold text-slate-700';

                    return;
                }

                const physicalStock = Number(
                    physicalStockInput.value
                );

                const difference = physicalStock - systemStock;

                const formattedDifference =
                    difference > 0
                        ? `+${difference} ${unit}`
                        : `${difference} ${unit}`;

                differenceDisplay.textContent =
                    difference === 0
                        ? 'Sesuai'
                        : formattedDifference;

                if (difference > 0) {
                    differenceDisplay.className =
                        'rounded-lg border border-blue-200 ' +
                        'bg-blue-50 px-4 py-2.5 text-sm ' +
                        'font-semibold text-blue-700';
                } else if (difference < 0) {
                    differenceDisplay.className =
                        'rounded-lg border border-red-200 ' +
                        'bg-red-50 px-4 py-2.5 text-sm ' +
                        'font-semibold text-red-700';
                } else {
                    differenceDisplay.className =
                        'rounded-lg border border-green-200 ' +
                        'bg-green-50 px-4 py-2.5 text-sm ' +
                        'font-semibold text-green-700';
                }
            }

            itemSelect.addEventListener(
                'change',
                updateSelectedItem
            );

            physicalStockInput.addEventListener(
                'input',
                updateDifference
            );

            /*
             * Menampilkan kembali perhitungan setelah validasi gagal.
             */
            updateSelectedItem();
        });
    </script>
@endpush
