<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Menambahkan data awal pengguna.
     */
    public function run(): void
    {
        /**
         * Semua pengguna memiliki hak akses yang sama.
         */
        User::updateOrCreate(
            [
                'email' => 'pengguna1@inventory.test',
            ],
            [
                'name' => 'Pengguna Gudang 1',
                'password' => Hash::make('password'),
            ]
        );

        User::updateOrCreate(
            [
                'email' => 'pengguna2@inventory.test',
            ],
            [
                'name' => 'Pengguna Gudang 2',
                'password' => Hash::make('password'),
            ]
        );
    }
}
