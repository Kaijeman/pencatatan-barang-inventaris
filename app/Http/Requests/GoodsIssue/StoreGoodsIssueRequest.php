<?php

namespace App\Http\Requests\GoodsIssue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGoodsIssueRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna dapat membuat barang keluar.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Membersihkan data sebelum proses validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'destination' => trim(
                (string) $this->input('destination')
            ),
        ]);
    }

    /**
     * Mendefinisikan aturan validasi transaksi barang keluar.
     */
    public function rules(): array
    {
        return [
            'destination' => [
                'required',
                'string',
                'max:255',
            ],

            'issued_at' => [
                'required',
                'date',
                'before_or_equal:today',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.item_id' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('items', 'id'),
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }

    /**
     * Mendefinisikan pesan kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'destination.required' =>
                'Tujuan pengeluaran barang wajib diisi.',
            'destination.max' =>
                'Tujuan pengeluaran maksimal 255 karakter.',

            'issued_at.required' =>
                'Tanggal pengeluaran wajib diisi.',
            'issued_at.date' =>
                'Tanggal pengeluaran tidak valid.',
            'issued_at.before_or_equal' =>
                'Tanggal pengeluaran tidak boleh melewati hari ini.',

            'note.max' =>
                'Catatan maksimal 1000 karakter.',

            'items.required' =>
                'Minimal satu barang harus ditambahkan.',
            'items.array' =>
                'Format data barang tidak valid.',
            'items.min' =>
                'Minimal satu barang harus ditambahkan.',

            'items.*.item_id.required' =>
                'Barang wajib dipilih.',
            'items.*.item_id.distinct' =>
                'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
            'items.*.item_id.exists' =>
                'Barang yang dipilih tidak ditemukan.',

            'items.*.quantity.required' =>
                'Jumlah barang wajib diisi.',
            'items.*.quantity.integer' =>
                'Jumlah barang harus berupa bilangan bulat.',
            'items.*.quantity.min' =>
                'Jumlah barang minimal satu.',
        ];
    }
}
