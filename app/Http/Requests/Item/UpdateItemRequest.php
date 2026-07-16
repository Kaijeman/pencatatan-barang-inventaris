<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna diperbolehkan memperbarui barang.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Menyiapkan dan membersihkan data sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->input('code'))),
            'name' => trim((string) $this->input('name')),
            'unit' => trim((string) $this->input('unit')),
        ]);
    }

    /**
     * Mendefinisikan aturan validasi perubahan barang.
     */
    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items', 'code')
                    ->ignore($this->route('item')),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'unit' => [
                'required',
                'string',
                'max:50',
            ],

            'purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'minimum_stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    /**
     * Mendefinisikan pesan kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',

            'code.required' => 'Kode barang wajib diisi.',
            'code.unique' => 'Kode barang tersebut sudah digunakan.',
            'code.max' => 'Kode barang maksimal 50 karakter.',

            'name.required' => 'Nama barang wajib diisi.',
            'name.max' => 'Nama barang maksimal 150 karakter.',

            'unit.required' => 'Satuan barang wajib diisi.',
            'unit.max' => 'Satuan barang maksimal 50 karakter.',

            'purchase_price.required' => 'Harga beli wajib diisi.',
            'purchase_price.numeric' => 'Harga beli harus berupa angka.',
            'purchase_price.min' => 'Harga beli tidak boleh bernilai negatif.',

            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa bilangan bulat.',
            'minimum_stock.min' => 'Stok minimum tidak boleh bernilai negatif.',

            'description.max' => 'Deskripsi maksimal 1000 karakter.',
        ];
    }
}
