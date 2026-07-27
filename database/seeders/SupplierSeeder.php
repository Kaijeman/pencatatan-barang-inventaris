<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Menambahkan data awal supplier.
     */
    public function run(): void
    {
        $suppliers = [
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
        ];

        /**
         * Memperbarui supplier berdasarkan email
         * atau membuat data baru jika belum tersedia.
         */
        foreach ($suppliers as $supplier) {
            Supplier::updateOrCreate(
                [
                    'email' => $supplier['email'],
                ],
                [
                    'name' => $supplier['name'],
                    'phone' => $supplier['phone'],
                    'address' => $supplier['address'],
                ]
            );
        }
    }
}
