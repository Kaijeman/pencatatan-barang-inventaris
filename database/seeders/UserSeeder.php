<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Kepala Gudang',
            'email' => 'kepala@inventory.test',
            'password' => bcrypt('password'),
            'role' => 'kepala_gudang',
        ]);

        User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@inventory.test',
            'password' => bcrypt('password'),
            'role' => 'staff_gudang',
        ]);
    }
}
