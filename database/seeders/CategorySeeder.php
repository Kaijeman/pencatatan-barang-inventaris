<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Menambahkan data awal kategori barang.
     */
    public function run(): void
    {
        $categories = [
            'Elektronik',
            'ATK',
            'Furniture',
            'Sparepart',
            'Peralatan',
        ];

        /**
         * Menggunakan firstOrCreate agar kategori
         * tidak terduplikasi saat seeder dijalankan ulang.
         */
        foreach ($categories as $categoryName) {
            Category::firstOrCreate([
                'name' => $categoryName,
            ]);
        }
    }
}
