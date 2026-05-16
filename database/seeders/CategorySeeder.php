<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => [
                    'en' => 'Electronics',
                    'ar' => 'إلكترونيات'
                ],
            ],
            [
                'name' => [
                    'en' => 'Clothing',
                    'ar' => 'ملابس'
                ],
            ],
            [
                'name' => [
                    'en' => 'Books',
                    'ar' => 'كتب'
                ],
            ],
            [
                'name' => [
                    'en' => 'Home & Garden',
                    'ar' => 'المنزل والحديقة'
                ],
            ],
            [
                'name' => [
                    'en' => 'Sports & Outdoors',
                    'ar' => 'الرياضة والهواء الطلق'
                ],
            ],
            [
                'name' => [
                    'en' => 'Toys & Games',
                    'ar' => 'ألعاب وألعاب'
                ],
            ],
            [
                'name' => [
                    'en' => 'Health & Beauty',
                    'ar' => 'الصحة والجمال'
                ],
            ],
            [
                'name' => [
                    'en' => 'Automotive',
                    'ar' => 'السيارات'
                ],
            ],
            [
                'name' => [
                    'en' => 'Food & Beverages',
                    'ar' => 'الطعام والمشروبات'
                ],
            ],
            [
                'name' => [
                    'en' => 'Furniture',
                    'ar' => 'الأثاث'
                ],
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name']
            ]);
        }

        Category::factory()->count(10)->create();
    }
}
