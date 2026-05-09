<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Product::create([
            'name' => ['en' => 'Test Product', 'ar' => 'منتج تجريبي'],
            'description' => ['en' => 'Test product description', 'ar' => 'وصف المنتج التجريبي'],
            'brand' => 'Test Brand',
            'price' => 99.99,
            'stock' => 10
        ]);
    }
}
