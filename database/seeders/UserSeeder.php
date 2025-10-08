<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@multimart.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+998901234567',
            'address' => 'Tashkent, Uzbekistan',
            'is_active' => true,
        ]);

        // Vendor 1 - Apple Store
        User::create([
            'name' => 'Apple Store',
            'email' => 'apple@multimart.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234568',
            'address' => 'Tashkent City, Uzbekistan',
            'store_name' => 'Apple Official Store',
            'store_description' => 'Official Apple products including iPhone, iPad, MacBook, Apple Watch, and accessories. Authorized retailer with genuine products and warranty.',
            'is_active' => true,
        ]);

        // Vendor 2 - Alibaba
        User::create([
            'name' => 'Alibaba Shop',
            'email' => 'alibaba@multimart.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234569',
            'address' => 'Chilanzar, Tashkent',
            'store_name' => 'Alibaba Wholesale',
            'store_description' => 'Wholesale products - clothes, electronics, home goods, auto parts and more. Best prices guaranteed!',
            'is_active' => true,
        ]);

        // Vendor 3 - Baby World
        User::create([
            'name' => 'Baby World',
            'email' => 'babyworld@multimart.com',
            'password' => Hash::make('password'),
            'role' => 'vendor',
            'phone' => '+998901234570',
            'address' => 'Yunusabad, Tashkent',
            'store_name' => 'Baby World Store',
            'store_description' => 'Everything for babies and kids - clothes, toys, accessories. Quality products for your little ones.',
            'is_active' => true,
        ]);

        // Regular Customer
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@multimart.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+998901234571',
            'address' => 'Mirzo Ulugbek, Tashkent, Uzbekistan',
            'is_active' => true,
        ]);
    }
}
