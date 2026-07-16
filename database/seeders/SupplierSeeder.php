<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::insert([
            [
                'name' => 'PT Sumber Jaya',
                'phone' => '081234567890',
                'email' => 'supplier1@mail.com',
                'address' => 'Balikpapan',
            ],
            [
                'name' => 'CV Maju Bersama',
                'phone' => '081234567891',
                'email' => 'supplier2@mail.com',
                'address' => 'Samarinda',
            ],
            [
                'name' => 'PT Nusantara',
                'phone' => '081234567892',
                'email' => 'supplier3@mail.com',
                'address' => 'Banjarmasin',
            ],
        ]);
    }
}
