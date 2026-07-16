<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::insert([

            [
                'category_id' => 1,
                'code' => 'BRG001',
                'name' => 'Barang 1',
                'unit' => 'Unit',
                'purchase_price' => 850000,
                'stock' => 15,
                'minimum_stock' => 5,
            ],

            [
                'category_id' => 1,
                'code' => 'BRG002',
                'name' => 'Barang 2',
                'unit' => 'Unit',
                'purchase_price' => 150000,
                'stock' => 40,
                'minimum_stock' => 10,
            ],

            [
                'category_id' => 2,
                'code' => 'BRG003',
                'name' => 'Barang 3',
                'unit' => 'Unit',
                'purchase_price' => 45000,
                'stock' => 30,
                'minimum_stock' => 10,
            ],
        ]);
    }
}
