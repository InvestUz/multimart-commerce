<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            // Other seeders can be called here
        ]);
        // Create a super admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Create a vendor user
        $vendor = User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@example.com',
            'password' => bcrypt('password'),
            'role' => 'vendor',
            'store_name' => 'Tech Store',
            'store_description' => 'Best electronics store',
            'is_active' => true,
        ]);

        // Create a customer user
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        // Create Car Category
        $carCategory = Category::create([
            'name' => 'Car',
            'slug' => 'car',
            'icon' => 'fa-car',
            'color' => '#FF6B6B',
            'is_active' => true,
            'order' => 1,
        ]);

        // Create Sub-Categories for Car
        $hybridSubCategory = SubCategory::create([
            'category_id' => $carCategory->id,
            'name' => 'Hybrid',
            'slug' => 'hybrid',
            'icon' => 'fa-leaf',
            'color' => '#51CF66',
            'is_active' => true,
            'order' => 1,
        ]);

        $electricSubCategory = SubCategory::create([
            'category_id' => $carCategory->id,
            'name' => 'Electric',
            'slug' => 'electric',
            'icon' => 'fa-bolt',
            'color' => '#FFD43B',
            'is_active' => true,
            'order' => 2,
        ]);

        // Create first product - Hybrid Car
        $hybridProduct = Product::create([
            'user_id' => $vendor->id,
            'category_id' => $carCategory->id,
            'sub_category_id' => $hybridSubCategory->id,
            'name' => 'Toyota Prius Hybrid 2024',
            'slug' => 'toyota-prius-hybrid-2024',
            'description' => 'The Toyota Prius is a reliable hybrid vehicle with excellent fuel efficiency and eco-friendly performance.',
            'price' => 28500.00,
            'old_price' => 32000.00,
            'discount_percentage' => 11,
            'stock' => 5,
            'sku' => 'SKU-HYBRID-001',
            'brand' => 'Toyota',
            'model' => 'Prius',
            'condition' => 'new',
            'weight' => 1450.50,
            'dimensions' => json_encode(['length' => 4.63, 'width' => 1.78, 'height' => 1.52]),
            'is_active' => true,
            'is_featured' => true,
            'average_rating' => 4.5,
            'total_reviews' => 12,
            'views' => 150,
            'total_sales' => 8,
        ]);

        // Create second product - Electric Car
        $electricProduct = Product::create([
            'user_id' => $vendor->id,
            'category_id' => $carCategory->id,
            'sub_category_id' => $electricSubCategory->id,
            'name' => 'Tesla Model 3 2024',
            'slug' => 'tesla-model-3-2024',
            'description' => 'Tesla Model 3 is a premium electric vehicle with advanced autopilot technology and impressive range.',
            'price' => 45000.00,
            'old_price' => 50000.00,
            'discount_percentage' => 10,
            'stock' => 3,
            'sku' => 'SKU-ELECTRIC-001',
            'brand' => 'Tesla',
            'model' => 'Model 3',
            'condition' => 'new',
            'weight' => 1611.00,
            'dimensions' => json_encode(['length' => 4.69, 'width' => 1.84, 'height' => 1.44]),
            'is_active' => true,
            'is_featured' => true,
            'average_rating' => 4.8,
            'total_reviews' => 25,
            'views' => 300,
            'total_sales' => 15,
        ]);

        echo "✓ Category 'Car' created\n";
        echo "✓ Sub-categories 'Hybrid' and 'Electric' created\n";
        echo "✓ 2 Products created successfully\n";
    }
}
