<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Memastikan pengguna hanya mengubah akunnya sendiri.
     */
    public function authorize(): bool
    {
        $editedUser = $this->route('user');

        return $editedUser instanceof User
            && $this->user()?->is($editedUser);
    }

    /**
     * Mendapatkan aturan validasi pembaruan pengguna.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $editedUser */
        $editedUser = $this->route('user');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')
                    ->ignore($editedUser->id),
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    /**
     * Mendapatkan pesan kesalahan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'name.max' => 'Nama pengguna maksimal 100 karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',

            'password.confirmed' =>
                'Konfirmasi password tidak sesuai.',

            'password.min' =>
                'Password minimal terdiri dari 8 karakter.',
        ];
    }
}
