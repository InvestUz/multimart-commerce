<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\Cart;
use App\Models\Wishlist;
use App\Models\FlashSale;
use App\Models\UserAddress;
use Carbon\Carbon;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates sample data for testing and demonstration.
     */
    public function run(): void
    {
        echo "Creating sample data...\n";

        // Get existing data
        $vendors = User::where('role', 'vendor')->get();
        $customers = User::where('role', 'customer')->get();
        $categories = Category::all();
        $brands = Brand::all();

        // =====================================================
        // 1. CREATE SAMPLE PRODUCTS
        // =====================================================

        echo "Creating sample products...\n";

        $sampleProducts = [
            // Electronics
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Electronics',
                'subcategory' => 'Smartphones',
                'brand' => 'Apple',
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system. Features a 6.7-inch Super Retina XDR display.',
                'price' => 1199.99,
                'old_price' => 1299.99,
                'discount_percentage' => 8,
                'stock' => 50,
                'is_featured' => true,
                'sizes' => ['128GB', '256GB', '512GB', '1TB'],
                'colors' => ['Natural Titanium', 'Blue Titanium', 'White Titanium', 'Black Titanium'],
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Electronics',
                'subcategory' => 'Laptops',
                'brand' => 'Dell',
                'name' => 'Dell XPS 15',
                'description' => 'Powerful laptop with Intel i7 processor, 16GB RAM, and 512GB SSD. Perfect for professionals and creators.',
                'price' => 1499.99,
                'old_price' => 1699.99,
                'discount_percentage' => 12,
                'stock' => 30,
                'is_featured' => true,
                'sizes' => ['i5/8GB/256GB', 'i7/16GB/512GB', 'i9/32GB/1TB'],
                'colors' => ['Silver', 'Black'],
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Electronics',
                'subcategory' => 'Cameras',
                'brand' => 'Canon',
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Professional mirrorless camera with 24.2MP full-frame sensor and 4K video recording.',
                'price' => 2499.99,
                'old_price' => null,
                'discount_percentage' => 0,
                'stock' => 15,
                'is_featured' => true,
                'sizes' => ['Body Only', 'With 24-105mm Lens'],
                'colors' => ['Black'],
            ],
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Electronics',
                'subcategory' => 'Audio & Headphones',
                'brand' => 'Sony',
                'name' => 'Sony WH-1000XM5 Headphones',
                'description' => 'Industry-leading noise canceling wireless headphones with exceptional sound quality and 30-hour battery life.',
                'price' => 399.99,
                'old_price' => 449.99,
                'discount_percentage' => 11,
                'stock' => 100,
                'is_featured' => false,
                'sizes' => null,
                'colors' => ['Black', 'Silver'],
            ],

            // Fashion
            [
                'vendor_id' => $vendors[1]->id,
                'category' => 'Fashion',
                'subcategory' => "Men's Clothing",
                'brand' => 'Nike',
                'name' => 'Nike Dri-FIT T-Shirt',
                'description' => 'Comfortable athletic t-shirt with moisture-wicking technology. Perfect for workouts and everyday wear.',
                'price' => 29.99,
                'old_price' => 39.99,
                'discount_percentage' => 25,
                'stock' => 200,
                'is_featured' => false,
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'colors' => ['Black', 'White', 'Navy', 'Red', 'Grey'],
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'category' => 'Fashion',
                'subcategory' => "Women's Clothing",
                'brand' => 'Zara',
                'name' => 'Zara Summer Dress',
                'description' => 'Elegant floral summer dress perfect for any occasion. Made from lightweight, breathable fabric.',
                'price' => 59.99,
                'old_price' => 79.99,
                'discount_percentage' => 25,
                'stock' => 80,
                'is_featured' => true,
                'sizes' => ['XS', 'S', 'M', 'L', 'XL'],
                'colors' => ['Floral Blue', 'Floral Pink', 'Solid Black', 'Solid White'],
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'category' => 'Fashion',
                'subcategory' => 'Shoes',
                'brand' => 'Adidas',
                'name' => 'Adidas Ultraboost Running Shoes',
                'description' => 'Premium running shoes with responsive Boost cushioning and Primeknit upper for ultimate comfort.',
                'price' => 180.00,
                'old_price' => 200.00,
                'discount_percentage' => 10,
                'stock' => 60,
                'is_featured' => true,
                'sizes' => ['7', '8', '9', '10', '11', '12'],
                'colors' => ['Black', 'White', 'Grey', 'Blue'],
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'category' => 'Fashion',
                'subcategory' => 'Bags & Accessories',
                'brand' => null,
                'name' => 'Leather Crossbody Bag',
                'description' => 'Stylish genuine leather crossbody bag with multiple compartments. Perfect for daily use.',
                'price' => 89.99,
                'old_price' => null,
                'discount_percentage' => 0,
                'stock' => 45,
                'is_featured' => false,
                'sizes' => ['Small', 'Medium', 'Large'],
                'colors' => ['Brown', 'Black', 'Tan'],
            ],

            // Home & Living
            [
                'vendor_id' => $vendors[2]->id,
                'category' => 'Home & Living',
                'subcategory' => 'Furniture',
                'brand' => 'IKEA',
                'name' => 'Modern Sofa Set',
                'description' => 'Comfortable 3-seater sofa with cushioned seating and modern design. Includes 2 throw pillows.',
                'price' => 799.99,
                'old_price' => 999.99,
                'discount_percentage' => 20,
                'stock' => 20,
                'is_featured' => true,
                'sizes' => ['3-Seater', '4-Seater', 'L-Shape'],
                'colors' => ['Grey', 'Beige', 'Navy Blue'],
            ],
            [
                'vendor_id' => $vendors[2]->id,
                'category' => 'Home & Living',
                'subcategory' => 'Kitchen & Dining',
                'brand' => null,
                'name' => 'Stainless Steel Cookware Set',
                'description' => '12-piece professional-grade cookware set. Includes pots, pans, and lids with non-stick coating.',
                'price' => 149.99,
                'old_price' => 199.99,
                'discount_percentage' => 25,
                'stock' => 40,
                'is_featured' => false,
                'sizes' => null,
                'colors' => ['Silver', 'Black'],
            ],
            [
                'vendor_id' => $vendors[2]->id,
                'category' => 'Home & Living',
                'subcategory' => 'Bedding',
                'brand' => null,
                'name' => 'Egyptian Cotton Bed Sheet Set',
                'description' => 'Luxury 100% Egyptian cotton bed sheet set. Includes fitted sheet, flat sheet, and 2 pillowcases.',
                'price' => 79.99,
                'old_price' => null,
                'discount_percentage' => 0,
                'stock' => 100,
                'is_featured' => false,
                'sizes' => ['Twin', 'Full', 'Queen', 'King'],
                'colors' => ['White', 'Ivory', 'Navy', 'Grey'],
            ],

            // Sports
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Sports & Outdoors',
                'subcategory' => 'Exercise & Fitness',
                'brand' => null,
                'name' => 'Adjustable Dumbbell Set',
                'description' => 'Space-saving adjustable dumbbells from 5 to 50 lbs. Perfect for home gym.',
                'price' => 299.99,
                'old_price' => 349.99,
                'discount_percentage' => 14,
                'stock' => 35,
                'is_featured' => true,
                'sizes' => ['5-25 lbs', '5-50 lbs'],
                'colors' => ['Black'],
            ],
            [
                'vendor_id' => $vendors[1]->id,
                'category' => 'Sports & Outdoors',
                'subcategory' => 'Cycling',
                'brand' => null,
                'name' => 'Mountain Bike 29"',
                'description' => 'Professional mountain bike with 21-speed gear system and aluminum frame.',
                'price' => 599.99,
                'old_price' => 699.99,
                'discount_percentage' => 14,
                'stock' => 25,
                'is_featured' => true,
                'sizes' => ['Small', 'Medium', 'Large'],
                'colors' => ['Red', 'Black', 'Blue'],
            ],

            // Books
            [
                'vendor_id' => $vendors[0]->id,
                'category' => 'Books & Media',
                'subcategory' => 'Books',
                'brand' => null,
                'name' => 'The Complete Programming Collection',
                'description' => 'Collection of 10 bestselling programming books covering Python, JavaScript, and more.',
                'price' => 149.99,
                'old_price' => null,
                'discount_percentage' => 0,
                'stock' => 50,
                'is_featured' => false,
                'sizes' => null,
                'colors' => null,
            ],
        ];

        foreach ($sampleProducts as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            $subCategory = SubCategory::where('name', $productData['subcategory'])->first();
            $brand = $productData['brand'] ? Brand::where('name', $productData['brand'])->first() : null;

            $product = Product::create([
                'user_id' => $productData['vendor_id'],
                'category_id' => $category->id,
                'sub_category_id' => $subCategory->id,
                'brand_id' => $brand?->id,
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'old_price' => $productData['old_price'],
                'discount_percentage' => $productData['discount_percentage'],
                'stock' => $productData['stock'],
                'is_active' => true,
                'is_featured' => $productData['is_featured'],
                'sizes' => $productData['sizes'],
                'colors' => $productData['colors'],
                'condition' => 'new',
                'average_rating' => rand(35, 50) / 10, // Random rating between 3.5 and 5.0
                'total_reviews' => rand(10, 100),
                'views' => rand(100, 1000),
                'total_sales' => rand(50, 500),
            ]);

            // Create sample product images
            for ($i = 1; $i <= 3; $i++) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => "products/product-{$product->id}-{$i}.jpg",
                    'order' => $i,
                    'is_primary' => $i === 1,
                ]);
            }

            // Create variants if product has sizes and colors
            if ($productData['sizes'] && $productData['colors']) {
                $sizeArray = is_array($productData['sizes']) ? $productData['sizes'] : [$productData['sizes']];
                $colorArray = is_array($productData['colors']) ? $productData['colors'] : [$productData['colors']];

                foreach ($sizeArray as $size) {
                    foreach ($colorArray as $color) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'color' => $color,
                            'stock' => rand(10, 50),
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }

        // =====================================================
        // 2. CREATE USER ADDRESSES
        // =====================================================

        echo "Creating user addresses...\n";

        foreach ($customers as $customer) {
            UserAddress::create([
                'user_id' => $customer->id,
                'label' => 'Home',
                'full_name' => $customer->name,
                'phone' => $customer->phone,
                'address_line1' => rand(100, 999) . ' Main Street',
                'city' => 'Tashkent',
                'state' => 'Tashkent',
                'postal_code' => '100000',
                'country' => 'Uzbekistan',
                'is_default' => true,
            ]);

            UserAddress::create([
                'user_id' => $customer->id,
                'label' => 'Work',
                'full_name' => $customer->name,
                'phone' => $customer->phone,
                'address_line1' => rand(100, 999) . ' Business Avenue',
                'city' => 'Tashkent',
                'state' => 'Tashkent',
                'postal_code' => '100001',
                'country' => 'Uzbekistan',
                'is_default' => false,
            ]);
        }

        // =====================================================
        // 3. CREATE SAMPLE ORDERS
        // =====================================================

        echo "Creating sample orders...\n";

        $products = Product::all();
        $orderStatuses = ['pending', 'processing', 'shipped', 'delivered'];
        $paymentMethods = ['cash_on_delivery', 'credit_card'];
        $paymentStatuses = ['pending', 'paid'];

        for ($i = 1; $i <= 20; $i++) {
            $customer = $customers->random();
            $address = $customer->addresses()->where('is_default', true)->first();

            $order = Order::create([
                'order_number' => 'ORD-' . date('Ymd') . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'user_id' => $customer->id,
                'customer_name' => $address->full_name,
                'customer_email' => $customer->email,
                'customer_phone' => $address->phone,
                'shipping_address' => $address->address_line1,
                'city' => $address->city,
                'state' => $address->state,
                'postal_code' => $address->postal_code,
                'country' => $address->country,
                'subtotal' => 0,
                'shipping_cost' => 4.99,
                'tax' => 0,
                'discount' => 0,
                'total' => 0,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'payment_status' => $paymentStatuses[array_rand($paymentStatuses)],
                'status' => $orderStatuses[array_rand($orderStatuses)],
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Add 1-5 random products to the order
            $orderProducts = $products->random(rand(1, 5));
            $subtotal = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $itemTotal = $product->price * $quantity;
                $subtotal += $itemTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'vendor_id' => $product->user_id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total' => $itemTotal,
                    'vendor_status' => $order->status,
                ]);
            }

            // Update order totals
            $tax = $subtotal * 0.15; // 15% tax
            $total = $subtotal + $order->shipping_cost + $tax;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
        }

        // =====================================================
        // 4. CREATE SAMPLE REVIEWS
        // =====================================================

        echo "Creating sample reviews...\n";

        $reviewComments = [
            'Excellent product! Highly recommended.',
            'Very satisfied with the quality and fast delivery.',
            'Good value for money. Would buy again.',
            'The product exceeded my expectations.',
            'Great quality and excellent customer service.',
            'Perfect! Just what I was looking for.',
            'Amazing product, will definitely recommend to friends.',
            'Good product but shipping took a bit longer than expected.',
            'Quality is good but a bit pricey.',
            'Satisfactory product, meets basic expectations.',
        ];

        foreach ($products->take(30) as $product) {
            $numReviews = rand(3, 10);
            for ($j = 0; $j < $numReviews; $j++) {
                Review::create([
                    'user_id' => $customers->random()->id,
                    'product_id' => $product->id,
                    'rating' => rand(3, 5),
                    'comment' => $reviewComments[array_rand($reviewComments)],
                    'is_approved' => true,
                    'is_verified_purchase' => rand(0, 1) == 1,
                    'helpful_count' => rand(0, 20),
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }

        // =====================================================
        // 5. CREATE SAMPLE CART ITEMS
        // =====================================================

        echo "Creating sample cart items...\n";

        foreach ($customers as $customer) {
            $cartProducts = $products->random(rand(2, 5));
            foreach ($cartProducts as $product) {
                Cart::create([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3),
                    'price' => $product->price,
                    'size' => $product->sizes ? (is_array($product->sizes) ? $product->sizes[0] : null) : null,
                    'color' => $product->colors ? (is_array($product->colors) ? $product->colors[0] : null) : null,
                ]);
            }
        }

        // =====================================================
        // 6. CREATE SAMPLE WISHLIST ITEMS
        // =====================================================

        echo "Creating sample wishlist items...\n";

        foreach ($customers as $customer) {
            $wishlistProducts = $products->random(rand(3, 8));
            foreach ($wishlistProducts as $product) {
                Wishlist::create([
                    'user_id' => $customer->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        // =====================================================
        // 7. CREATE FLASH SALE
        // =====================================================

        echo "Creating flash sale...\n";

        $flashSale = FlashSale::create([
            'name' => 'Weekend Flash Sale',
            'description' => 'Amazing deals for the weekend! Up to 50% off on selected items.',
            'starts_at' => now(),
            'ends_at' => now()->addDays(3),
            'is_active' => true,
        ]);

        // Add some products to flash sale
        $flashSaleProducts = $products->random(10);
        foreach ($flashSaleProducts as $product) {
            $discountPercentage = rand(20, 50);
            $flashPrice = $product->price * (1 - $discountPercentage / 100);

            $flashSale->products()->attach($product->id, [
                'flash_price' => $flashPrice,
                'discount_percentage' => $discountPercentage,
                'quantity_limit' => rand(20, 100),
                'quantity_sold' => rand(0, 10),
                'per_user_limit' => 3,
            ]);
        }

        echo "\n✅ Sample data seeding completed successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Total Products: " . Product::count() . "\n";
        echo "Total Orders: " . Order::count() . "\n";
        echo "Total Reviews: " . Review::count() . "\n";
        echo "Total Cart Items: " . Cart::count() . "\n";
        echo "Total Wishlist Items: " . Wishlist::count() . "\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
