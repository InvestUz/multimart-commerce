<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Apple Products (Vendor ID: 2)
        $appleProducts = [
            [
                'user_id' => 2,
                'category_id' => 6, // Electronics
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system. Features include 6.7-inch Super Retina XDR display, ProMotion technology, and up to 1TB storage.',
                'price' => 1199.99,
                'old_price' => 1299.99,
                'discount_percentage' => 8,
                'stock' => 50,
                'brand' => 'Apple',
                'model' => 'iPhone 15 Pro Max',
                'condition' => 'new',
                'sizes' => json_encode(['128GB', '256GB', '512GB', '1TB']),
                'colors' => json_encode(['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium']),
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.8,
                'total_reviews' => 156,
            ],
            [
                'user_id' => 2,
                'category_id' => 6, // Electronics
                'name' => 'MacBook Pro 14"',
                'description' => 'Powerful laptop with M3 chip, stunning Liquid Retina XDR display, and all-day battery life. Perfect for professionals and creators.',
                'price' => 1999.99,
                'stock' => 30,
                'brand' => 'Apple',
                'model' => 'MacBook Pro 14" M3',
                'condition' => 'new',
                'colors' => json_encode(['Space Gray', 'Silver']),
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.9,
                'total_reviews' => 89,
            ],
            [
                'user_id' => 2,
                'category_id' => 6, // Electronics
                'name' => 'iPad Pro 12.9"',
                'description' => 'Ultimate iPad with M2 chip and Liquid Retina XDR display. The most powerful and versatile iPad ever.',
                'price' => 1099.99,
                'old_price' => 1199.99,
                'discount_percentage' => 8,
                'stock' => 40,
                'brand' => 'Apple',
                'model' => 'iPad Pro 12.9"',
                'condition' => 'new',
                'sizes' => json_encode(['128GB', '256GB', '512GB', '1TB', '2TB']),
                'colors' => json_encode(['Space Gray', 'Silver']),
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.7,
                'total_reviews' => 67,
            ],
            [
                'user_id' => 2,
                'category_id' => 6, // Electronics
                'name' => 'Apple Watch Series 9',
                'description' => 'Advanced health and fitness features with always-on Retina display, ECG app, blood oxygen sensor, and more.',
                'price' => 399.99,
                'stock' => 60,
                'brand' => 'Apple',
                'model' => 'Apple Watch Series 9',
                'condition' => 'new',
                'sizes' => json_encode(['41mm', '45mm']),
                'colors' => json_encode(['Midnight', 'Starlight', 'Silver', 'Pink', 'Red']),
                'is_active' => true,
                'average_rating' => 4.6,
                'total_reviews' => 234,
            ],
        ];

        foreach ($appleProducts as $product) {
            $createdProduct = Product::create($product);

            // Use placeholder images from placeholder services
            // These are real, working image URLs
            $imageUrls = [
                'https://placehold.co/800x800/e0e0e0/666666?text=Product+Image+1',
                'https://placehold.co/800x800/d0d0d0/555555?text=Product+Image+2',
                'https://placehold.co/800x800/c0c0c0/444444?text=Product+Image+3',
                'https://placehold.co/800x800/b0b0b0/333333?text=Product+Image+4',
            ];

            // Alternative: Use Picsum for random photos
            // $imageUrls = [
            //     'https://picsum.photos/seed/' . $createdProduct->id . '1/800/800',
            //     'https://picsum.photos/seed/' . $createdProduct->id . '2/800/800',
            //     'https://picsum.photos/seed/' . $createdProduct->id . '3/800/800',
            //     'https://picsum.photos/seed/' . $createdProduct->id . '4/800/800',
            // ];

            // Alternative: Use DummyImage
            // $imageUrls = [
            //     'https://dummyimage.com/800x800/4a90e2/ffffff&text=' . urlencode($createdProduct->name . ' 1'),
            //     'https://dummyimage.com/800x800/50c878/ffffff&text=' . urlencode($createdProduct->name . ' 2'),
            //     'https://dummyimage.com/800x800/ff6b6b/ffffff&text=' . urlencode($createdProduct->name . ' 3'),
            //     'https://dummyimage.com/800x800/ffd93d/ffffff&text=' . urlencode($createdProduct->name . ' 4'),
            // ];

            for ($i = 0; $i < 4; $i++) {
                ProductImage::create([
                    'product_id' => $createdProduct->id,
                    'image_path' => $imageUrls[$i],
                    'order' => $i + 1,
                    'is_primary' => $i === 0,
                ]);
            }
        }
        // Alibaba Products (Vendor ID: 3)
        $alibabaProducts = [
            [
                'user_id' => 3,
                'category_id' => 3, // Women
                'name' => 'Women Summer Dress',
                'description' => 'Elegant floral summer dress, perfect for casual and formal occasions. Made with breathable fabric and comfortable fit.',
                'price' => 29.99,
                'old_price' => 49.99,
                'discount_percentage' => 40,
                'stock' => 100,
                'brand' => 'Alibaba Fashion',
                'condition' => 'new',
                'sizes' => json_encode(['S', 'M', 'L', 'XL', 'XXL']),
                'colors' => json_encode(['Red', 'Blue', 'Green', 'Yellow', 'Black']),
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.3,
                'total_reviews' => 45,
            ],
            [
                'user_id' => 3,
                'category_id' => 4, // Men
                'name' => 'Men Business Shirt',
                'description' => 'Premium cotton shirt for business and formal wear. Wrinkle-free and comfortable for all-day wear.',
                'price' => 24.99,
                'old_price' => 39.99,
                'discount_percentage' => 38,
                'stock' => 150,
                'brand' => 'Alibaba Fashion',
                'condition' => 'new',
                'sizes' => json_encode(['S', 'M', 'L', 'XL', 'XXL', 'XXXL']),
                'colors' => json_encode(['White', 'Blue', 'Black', 'Gray']),
                'is_active' => true,
                'average_rating' => 4.4,
                'total_reviews' => 78,
            ],
            [
                'user_id' => 3,
                'category_id' => 9, // Auto Parts
                'name' => 'Car LED Headlight Kit',
                'description' => 'High-performance LED headlight bulbs with 6000K white light. Easy installation and long lifespan.',
                'price' => 49.99,
                'old_price' => 79.99,
                'discount_percentage' => 38,
                'stock' => 80,
                'brand' => 'Alibaba Auto',
                'condition' => 'new',
                'specifications' => json_encode(['Power' => '60W', 'Lumens' => '12000LM', 'Lifespan' => '50000hrs', 'Color Temperature' => '6000K']),
                'is_active' => true,
                'average_rating' => 4.5,
                'total_reviews' => 92,
            ],
            [
                'user_id' => 3,
                'category_id' => 7, // Home
                'name' => 'Kitchen Cookware Set',
                'description' => '12-piece non-stick cookware set with glass lids. Includes pots, pans, and utensils. Dishwasher safe.',
                'price' => 89.99,
                'old_price' => 149.99,
                'discount_percentage' => 40,
                'stock' => 45,
                'brand' => 'Alibaba Home',
                'condition' => 'new',
                'colors' => json_encode(['Red', 'Black', 'Gray']),
                'is_active' => true,
                'average_rating' => 4.6,
                'total_reviews' => 134,
            ],
        ];

        foreach ($alibabaProducts as $product) {
            $createdProduct = Product::create($product);

            for ($i = 1; $i <= 4; $i++) {
                ProductImage::create([
                    'product_id' => $createdProduct->id,
                    'image_path' => 'products/alibaba-' . $createdProduct->id . '-' . $i . '.jpg',
                    'order' => $i,
                    'is_primary' => $i === 1,
                ]);
            }
        }

        // Baby World Products (Vendor ID: 4)
        $babyProducts = [
            [
                'user_id' => 4,
                'category_id' => 2, // Baby
                'name' => 'Organic Cotton Baby Bodysuit',
                'description' => 'Soft organic cotton bodysuit with snap closures, gentle on baby skin. Hypoallergenic and certified organic.',
                'price' => 14.99,
                'old_price' => 19.99,
                'discount_percentage' => 25,
                'stock' => 200,
                'brand' => 'Baby World',
                'condition' => 'new',
                'sizes' => json_encode(['0-3M', '3-6M', '6-9M', '9-12M', '12-18M']),
                'colors' => json_encode(['White', 'Pink', 'Blue', 'Beige', 'Green']),
                'is_featured' => true,
                'is_active' => true,
                'average_rating' => 4.8,
                'total_reviews' => 267,
            ],
            [
                'user_id' => 4,
                'category_id' => 1, // Kids
                'name' => 'Kids Winter Jacket',
                'description' => 'Warm and comfortable winter jacket with hood. Water-resistant and insulated for cold weather.',
                'price' => 39.99,
                'old_price' => 59.99,
                'discount_percentage' => 33,
                'stock' => 75,
                'brand' => 'Baby World',
                'condition' => 'new',
                'sizes' => json_encode(['2T', '3T', '4T', '5T', '6T']),
                'colors' => json_encode(['Navy', 'Red', 'Pink', 'Black']),
                'is_active' => true,
                'average_rating' => 4.5,
                'total_reviews' => 145,
            ],
            [
                'user_id' => 4,
                'category_id' => 5, // Accessories
                'name' => 'Baby Blanket Set',
                'description' => 'Ultra-soft muslin blankets, perfect for swaddling and cuddling. Set of 3 breathable blankets.',
                'price' => 24.99,
                'stock' => 120,
                'brand' => 'Baby World',
                'condition' => 'new',
                'colors' => json_encode(['White', 'Pink', 'Blue', 'Gray']),
                'is_active' => true,
                'average_rating' => 4.7,
                'total_reviews' => 189,
            ],
            [
                'user_id' => 4,
                'category_id' => 1, // Kids
                'name' => 'Kids Sneakers',
                'description' => 'Comfortable and durable sneakers for active kids. Non-slip sole and breathable material.',
                'price' => 34.99,
                'old_price' => 49.99,
                'discount_percentage' => 30,
                'stock' => 90,
                'brand' => 'Baby World',
                'condition' => 'new',
                'sizes' => json_encode(['Size 6', 'Size 7', 'Size 8', 'Size 9', 'Size 10', 'Size 11', 'Size 12']),
                'colors' => json_encode(['Black', 'White', 'Pink', 'Blue', 'Red']),
                'is_active' => true,
                'average_rating' => 4.4,
                'total_reviews' => 98,
            ],
        ];

        foreach ($babyProducts as $product) {
            $createdProduct = Product::create($product);

            for ($i = 1; $i <= 4; $i++) {
                ProductImage::create([
                    'product_id' => $createdProduct->id,
                    'image_path' => 'products/baby-' . $createdProduct->id . '-' . $i . '.jpg',
                    'order' => $i,
                    'is_primary' => $i === 1,
                ]);
            }
        }
    }
}
