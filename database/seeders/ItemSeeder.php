<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Menambahkan data awal barang.
     */
    public function run(): void
    {
        /**
         * Mengambil kategori berdasarkan nama agar tidak
         * bergantung pada urutan ID di database.
         */
        $electronicsCategory = Category::query()
            ->where('name', 'Elektronik')
            ->firstOrFail();

        $stationeryCategory = Category::query()
            ->where('name', 'ATK')
            ->firstOrFail();

        $items = [
            [
                'category_id' => $electronicsCategory->id,
                'name' => 'Barang 1',
                'unit' => 'Unit',
                'purchase_price' => 850000,
                'stock' => 15,
                'minimum_stock' => 5,
                'description' => null,
            ],
            [
                'category_id' => $electronicsCategory->id,
                'name' => 'Barang 2',
                'unit' => 'Unit',
                'purchase_price' => 150000,
                'stock' => 40,
                'minimum_stock' => 10,
                'description' => null,
            ],
            [
                'category_id' => $stationeryCategory->id,
                'name' => 'Barang 3',
                'unit' => 'Unit',
                'purchase_price' => 45000,
                'stock' => 30,
                'minimum_stock' => 10,
                'description' => null,
            ],
        ];

        /**
         * Memperbarui barang berdasarkan nama dan kategori
         * atau membuat barang baru jika belum tersedia.
         */
        foreach ($items as $item) {
            Item::updateOrCreate(
                [
                    'category_id' => $item['category_id'],
                    'name' => $item['name'],
                ],
                [
                    'unit' => $item['unit'],
                    'purchase_price' => $item['purchase_price'],
                    'stock' => $item['stock'],
                    'minimum_stock' => $item['minimum_stock'],
                    'description' => $item['description'],
                ]
            );
        }
    }
}
