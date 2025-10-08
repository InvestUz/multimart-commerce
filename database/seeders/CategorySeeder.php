<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kids',
                'slug' => 'kids',
                'icon' => 'fa-child',
                'color' => '#F5F5DC',
                'is_active' => true,
                'order' => 1
            ],
            [
                'name' => 'Baby',
                'slug' => 'baby',
                'icon' => 'fa-baby',
                'color' => '#FCE4EC',
                'is_active' => true,
                'order' => 2
            ],
            [
                'name' => 'Women',
                'slug' => 'women',
                'icon' => 'fa-female',
                'color' => '#E1F5FE',
                'is_active' => true,
                'order' => 3
            ],
            [
                'name' => 'Men',
                'slug' => 'men',
                'icon' => 'fa-male',
                'color' => '#F5F5DC',
                'is_active' => true,
                'order' => 4
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'icon' => 'fa-mitten',
                'color' => '#FCE4EC',
                'is_active' => true,
                'order' => 5
            ],
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'icon' => 'fa-mobile-alt',
                'color' => '#F5F5DC',
                'is_active' => true,
                'order' => 6
            ],
            [
                'name' => 'Home',
                'slug' => 'home',
                'icon' => 'fa-home',
                'color' => '#FCE4EC',
                'is_active' => true,
                'order' => 7
            ],
            [
                'name' => 'Beauty',
                'slug' => 'beauty',
                'icon' => 'fa-spa',
                'color' => '#E1F5FE',
                'is_active' => true,
                'order' => 8
            ],
            [
                'name' => 'Auto Parts',
                'slug' => 'auto-parts',
                'icon' => 'fa-car',
                'color' => '#E8F5E8',
                'is_active' => true,
                'order' => 9
            ],
            [
                'name' => 'Sports',
                'slug' => 'sports',
                'icon' => 'fa-running',
                'color' => '#F5F5DC',
                'is_active' => true,
                'order' => 10
            ],
            [
                'name' => 'Sale',
                'slug' => 'sale',
                'icon' => 'fa-tag',
                'color' => '#FFEBEE',
                'is_active' => true,
                'order' => 11
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
