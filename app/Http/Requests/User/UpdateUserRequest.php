<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Menentukan apakah pengguna dapat memperbarui akun.
     */
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->role === 'kepala_gudang';
    }

    /**
     * Membersihkan data sebelum proses validasi.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => strtolower(
                trim((string) $this->input('email'))
            ),
        ]);
    }

    /**
     * Mendefinisikan aturan validasi perubahan pengguna.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($this->route('user')),
            ],

            'role' => [
                'required',
                Rule::in([
                    'kepala_gudang',
                    'staff_gudang',
                ]),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    /**
     * Mendefinisikan pesan kesalahan validasi.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 255 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan.',
            'email.max' => 'Email maksimal 255 karakter.',

            'role.required' => 'Role pengguna wajib dipilih.',
            'role.in' => 'Role pengguna tidak valid.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',
            'password.min' =>
                'Password minimal terdiri dari 8 karakter.',
        ];
    }
}
