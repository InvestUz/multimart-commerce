<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Coupon;
use App\Models\ShippingMethod;
use App\Models\Tax;
use App\Models\Setting;
use App\Models\Page;
use Carbon\Carbon;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates essential/initial data required for the marketplace to function.
     */
    public function run(): void
    {
        // =====================================================
        // 1. CREATE USERS (Super Admin, Vendors, Customers)
        // =====================================================

        echo "Creating users...\n";

        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@onebazar.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+998901234567',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Vendors
        $vendor1 = User::create([
            'name' => 'Electronics Store',
            'email' => 'vendor1@onebazar.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234568',
            'store_name' => 'TechHub Electronics',
            'store_description' => 'Your one-stop shop for all electronics and gadgets',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $vendor2 = User::create([
            'name' => 'Fashion Boutique',
            'email' => 'vendor2@onebazar.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234569',
            'store_name' => 'StyleMart Fashion',
            'store_description' => 'Premium fashion and accessories for everyone',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $vendor3 = User::create([
            'name' => 'Home & Living',
            'email' => 'vendor3@marketplace.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234570',
            'store_name' => 'HomeComfort Store',
            'store_description' => 'Quality home decor and furniture',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Customers
        $customer1 = User::create([
            'name' => 'John Doe',
            'email' => 'customer@onebazar.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+998901234571',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $customer2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'customer2@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+998901234572',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // =====================================================
        // 2. CREATE CATEGORIES & SUBCATEGORIES
        // =====================================================

        echo "Creating categories and subcategories...\n";

        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'icon' => 'fa-laptop',
                'color' => '#3B82F6',
                'is_active' => true,
                'order' => 1,
                'subcategories' => [
                    'Smartphones',
                    'Laptops',
                    'Tablets',
                    'Cameras',
                    'Audio & Headphones',
                    'Smart Watches',
                    'Gaming Consoles',
                ]
            ],
            [
                'name' => 'Fashion',
                'slug' => 'fashion',
                'icon' => 'fa-shirt',
                'color' => '#EC4899',
                'is_active' => true,
                'order' => 2,
                'subcategories' => [
                    "Men's Clothing",
                    "Women's Clothing",
                    "Kids Clothing",
                    'Shoes',
                    'Bags & Accessories',
                    'Jewelry',
                    'Watches',
                ]
            ],
            [
                'name' => 'Home & Living',
                'slug' => 'home-living',
                'icon' => 'fa-home',
                'color' => '#10B981',
                'is_active' => true,
                'order' => 3,
                'subcategories' => [
                    'Furniture',
                    'Kitchen & Dining',
                    'Bedding',
                    'Home Decor',
                    'Lighting',
                    'Storage & Organization',
                ]
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'icon' => 'fa-futbol',
                'color' => '#F59E0B',
                'is_active' => true,
                'order' => 4,
                'subcategories' => [
                    'Exercise & Fitness',
                    'Outdoor Recreation',
                    'Sports Apparel',
                    'Cycling',
                    'Camping & Hiking',
                ]
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'icon' => 'fa-book',
                'color' => '#8B5CF6',
                'is_active' => true,
                'order' => 5,
                'subcategories' => [
                    'Books',
                    'Movies & TV Shows',
                    'Music',
                    'Video Games',
                ]
            ],
            [
                'name' => 'Beauty & Health',
                'slug' => 'beauty-health',
                'icon' => 'fa-heart',
                'color' => '#EF4444',
                'is_active' => true,
                'order' => 6,
                'subcategories' => [
                    'Skincare',
                    'Makeup',
                    'Hair Care',
                    'Fragrances',
                    'Health & Wellness',
                ]
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'slug' => $categoryData['slug'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'is_active' => $categoryData['is_active'],
                'order' => $categoryData['order'],
            ]);

            // Create subcategories
            foreach ($categoryData['subcategories'] as $subIndex => $subCategoryName) {
                SubCategory::create([
                    'category_id' => $category->id,
                    'name' => $subCategoryName,
                    'slug' => Str::slug($subCategoryName),
                    'icon' => 'fa-box',
                    'color' => $categoryData['color'],
                    'is_active' => true,
                    'order' => $subIndex + 1,
                ]);
            }
        }

        // =====================================================
        // 3. CREATE BRANDS
        // =====================================================

        echo "Creating brands...\n";

        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'is_featured' => true],
            ['name' => 'Samsung', 'slug' => 'samsung', 'is_featured' => true],
            ['name' => 'Sony', 'slug' => 'sony', 'is_featured' => true],
            ['name' => 'Nike', 'slug' => 'nike', 'is_featured' => true],
            ['name' => 'Adidas', 'slug' => 'adidas', 'is_featured' => true],
            ['name' => 'Zara', 'slug' => 'zara', 'is_featured' => false],
            ['name' => 'H&M', 'slug' => 'hm', 'is_featured' => false],
            ['name' => 'IKEA', 'slug' => 'ikea', 'is_featured' => true],
            ['name' => 'Dell', 'slug' => 'dell', 'is_featured' => false],
            ['name' => 'HP', 'slug' => 'hp', 'is_featured' => false],
            ['name' => 'Canon', 'slug' => 'canon', 'is_featured' => false],
            ['name' => 'Nikon', 'slug' => 'nikon', 'is_featured' => false],
        ];

        foreach ($brands as $index => $brandData) {
            Brand::create([
                'name' => $brandData['name'],
                'slug' => $brandData['slug'],
                'description' => "Quality products from {$brandData['name']}",
                'is_active' => true,
                'is_featured' => $brandData['is_featured'],
                'order' => $index + 1,
            ]);
        }

        // =====================================================
        // 4. CREATE BANNERS
        // =====================================================

        echo "Creating banners...\n";

        $banners = [
            [
                'title' => 'Summer Sale - Up to 50% Off',
                'description' => 'Shop the hottest deals of the season',
                'image_path' => 'banners/summer-sale.jpg',
                'link' => '/category/fashion',
                'type' => 'slider',
                'position' => 'home_slider',
                'button_text' => 'Shop Now',
                'order' => 1,
            ],
            [
                'title' => 'New Electronics Arrivals',
                'description' => 'Check out the latest gadgets and technology',
                'image_path' => 'banners/electronics.jpg',
                'link' => '/category/electronics',
                'type' => 'slider',
                'position' => 'home_slider',
                'button_text' => 'Explore',
                'order' => 2,
            ],
            [
                'title' => 'Home Essentials',
                'description' => 'Upgrade your living space',
                'image_path' => 'banners/home-living.jpg',
                'link' => '/category/home-living',
                'type' => 'promotional',
                'position' => 'home_middle',
                'button_text' => 'Discover',
                'order' => 1,
            ],
        ];

        foreach ($banners as $bannerData) {
            Banner::create([
                'title' => $bannerData['title'],
                'description' => $bannerData['description'],
                'image_path' => $bannerData['image_path'],
                'link' => $bannerData['link'],
                'type' => $bannerData['type'],
                'position' => $bannerData['position'],
                'button_text' => $bannerData['button_text'],
                'order' => $bannerData['order'],
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'clicks' => 0,
            ]);
        }

        // =====================================================
        // 5. CREATE COUPONS
        // =====================================================

        echo "Creating coupons...\n";

        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 50,
                'max_uses' => 1000,
                'description' => '10% off for new customers',
            ],
            [
                'code' => 'SAVE20',
                'type' => 'percentage',
                'value' => 20,
                'min_purchase' => 100,
                'max_uses' => 500,
                'description' => '20% off on orders above $100',
            ],
            [
                'code' => 'FLAT50',
                'type' => 'fixed',
                'value' => 50,
                'min_purchase' => 200,
                'max_uses' => 200,
                'description' => '$50 off on orders above $200',
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::create([
                'code' => $couponData['code'],
                'type' => $couponData['type'],
                'value' => $couponData['value'],
                'min_purchase' => $couponData['min_purchase'],
                'max_uses' => $couponData['max_uses'],
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
            ]);
        }

        // =====================================================
        // 6. CREATE SHIPPING METHODS
        // =====================================================

        echo "Creating shipping methods...\n";

        $shippingMethods = [
            [
                'name' => 'Standard Delivery',
                'description' => 'Regular delivery within 5-7 business days',
                'cost' => 4.99,
                'free_shipping_threshold' => 100,
                'estimated_delivery' => '5-7 business days',
                'order' => 1,
            ],
            [
                'name' => 'Express Delivery',
                'description' => 'Fast delivery within 2-3 business days',
                'cost' => 9.99,
                'free_shipping_threshold' => 200,
                'estimated_delivery' => '2-3 business days',
                'order' => 2,
            ],
            [
                'name' => 'Same Day Delivery',
                'description' => 'Get your order today (selected areas only)',
                'cost' => 14.99,
                'free_shipping_threshold' => null,
                'estimated_delivery' => 'Same day',
                'order' => 3,
            ],
            [
                'name' => 'Free Shipping',
                'description' => 'Free delivery for orders above $100',
                'cost' => 0,
                'free_shipping_threshold' => 100,
                'estimated_delivery' => '7-10 business days',
                'order' => 4,
            ],
        ];

        foreach ($shippingMethods as $method) {
            ShippingMethod::create([
                'name' => $method['name'],
                'description' => $method['description'],
                'cost' => $method['cost'],
                'free_shipping_threshold' => $method['free_shipping_threshold'],
                'estimated_delivery' => $method['estimated_delivery'],
                'is_active' => true,
                'order' => $method['order'],
            ]);
        }

        // =====================================================
        // 7. CREATE TAXES
        // =====================================================

        echo "Creating tax rules...\n";

        $taxes = [
            [
                'name' => 'Uzbekistan VAT',
                'country' => 'Uzbekistan',
                'state' => null,
                'city' => null,
                'rate' => 15.00,
                'type' => 'percentage',
                'priority' => 1,
            ],
            [
                'name' => 'Tashkent City Tax',
                'country' => 'Uzbekistan',
                'state' => 'Tashkent',
                'city' => 'Tashkent',
                'rate' => 2.00,
                'type' => 'percentage',
                'priority' => 2,
            ],
        ];

        foreach ($taxes as $taxData) {
            Tax::create([
                'name' => $taxData['name'],
                'country' => $taxData['country'],
                'state' => $taxData['state'],
                'city' => $taxData['city'],
                'rate' => $taxData['rate'],
                'type' => $taxData['type'],
                'is_active' => true,
                'priority' => $taxData['priority'],
            ]);
        }

        // =====================================================
        // 8. CREATE SETTINGS
        // =====================================================

        echo "Creating site settings...\n";

        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Onebazar', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Your one-stop online marketplace', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_logo', 'value' => 'logo.png', 'type' => 'image', 'group' => 'general'],
            ['key' => 'site_favicon', 'value' => 'favicon.ico', 'type' => 'image', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'support@onebazar.com', 'type' => 'text', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '+998901234567', 'type' => 'text', 'group' => 'general'],
            ['key' => 'contact_address', 'value' => 'Tashkent, Uzbekistan', 'type' => 'text', 'group' => 'general'],

            // Email Settings
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'type' => 'text', 'group' => 'email'],
            ['key' => 'smtp_port', 'value' => '587', 'type' => 'number', 'group' => 'email'],
            ['key' => 'smtp_username', 'value' => '', 'type' => 'text', 'group' => 'email'],
            ['key' => 'smtp_password', 'value' => '', 'type' => 'text', 'group' => 'email'],

            // Payment Settings
            ['key' => 'currency', 'value' => 'USD', 'type' => 'text', 'group' => 'payment'],
            ['key' => 'currency_symbol', 'value' => '$', 'type' => 'text', 'group' => 'payment'],
            ['key' => 'enable_cash_on_delivery', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'enable_credit_card', 'value' => 'true', 'type' => 'boolean', 'group' => 'payment'],

            // Vendor Settings
            ['key' => 'vendor_commission_rate', 'value' => '15', 'type' => 'number', 'group' => 'vendor'],
            ['key' => 'vendor_auto_approve', 'value' => 'false', 'type' => 'boolean', 'group' => 'vendor'],
            ['key' => 'min_payout_amount', 'value' => '50', 'type' => 'number', 'group' => 'vendor'],

            // Order Settings
            ['key' => 'order_prefix', 'value' => 'ORD', 'type' => 'text', 'group' => 'order'],
            ['key' => 'auto_cancel_days', 'value' => '7', 'type' => 'number', 'group' => 'order'],

            // Social Media
            ['key' => 'facebook_url', 'value' => 'https://facebook.com', 'type' => 'text', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com', 'type' => 'text', 'group' => 'social'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com', 'type' => 'text', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com', 'type' => 'text', 'group' => 'social'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        // =====================================================
        // 9. CREATE CMS PAGES
        // =====================================================

        echo "Creating CMS pages...\n";

        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h1>About Us</h1><p>Welcome to Onebazar, your number one source for all things shopping. We\'re dedicated to giving you the very best products, with a focus on quality, customer service, and uniqueness.</p><p>Founded in 2024, Onebazar has come a long way from its beginnings. When we first started out, our passion for providing the best shopping experience drove us to create this platform, and gave us the impetus to turn hard work and inspiration into a booming online marketplace.</p>',
                'meta_description' => 'Learn more about Onebazar',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'content' => '<h1>Terms & Conditions</h1><p>These terms and conditions outline the rules and regulations for the use of Onebazar.</p><h2>License</h2><p>Unless otherwise stated, Onebazar and/or its licensors own the intellectual property rights for all material on Onebazar.</p>',
                'meta_description' => 'Terms and conditions for using Onebazar',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>At Onebazar, accessible from www.multimart.com, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by Onebazar and how we use it.</p>',
                'meta_description' => 'Privacy policy of Onebazar',
                'is_active' => true,
                'order' => 3,
            ],
            [
                'title' => 'Return & Refund Policy',
                'slug' => 'return-refund-policy',
                'content' => '<h1>Return & Refund Policy</h1><p>Thank you for shopping at Onebazar. If you are not entirely satisfied with your purchase, we\'re here to help.</p><h2>Returns</h2><p>You have 30 calendar days to return an item from the date you received it.</p>',
                'meta_description' => 'Return and refund policy',
                'is_active' => true,
                'order' => 4,
            ],
            [
                'title' => 'Shipping Information',
                'slug' => 'shipping-information',
                'content' => '<h1>Shipping Information</h1><p>We offer various shipping methods to ensure your products arrive safely and on time.</p><h2>Shipping Methods</h2><ul><li>Standard Delivery: 5-7 business days</li><li>Express Delivery: 2-3 business days</li><li>Same Day Delivery: Available in selected areas</li></ul>',
                'meta_description' => 'Shipping information and delivery options',
                'is_active' => true,
                'order' => 5,
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h1>Contact Us</h1><p>Have questions? We\'d love to hear from you.</p><p><strong>Email:</strong> support@onebazar.com</p><p><strong>Phone:</strong> +998901234567</p><p><strong>Address:</strong> Tashkent, Uzbekistan</p>',
                'meta_description' => 'Get in touch with us',
                'is_active' => true,
                'order' => 6,
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }

        echo "\n✅ Initial data seeding completed successfully!\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "Super Admin: admin@onebazar.com / password\n";
        echo "Vendor 1: vendor1@onebazar.com / password\n";
        echo "Vendor 2: vendor2@onebazar.com / password\n";
        echo "Customer: customer@onebazar.com / password\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }
}
