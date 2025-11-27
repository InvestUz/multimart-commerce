<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Cash on Delivery',
                'code' => 'cod',
                'description' => 'Pay when you receive your order',
                'image_path' => null,
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Uzum',
                'code' => 'uzum',
                'description' => 'Pay with Uzum payment system',
                'image_path' => null,
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Click',
                'code' => 'click',
                'description' => 'Pay with Click payment system',
                'image_path' => null,
                'is_active' => true,
                'sort_order' => 3
            ]
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::updateOrCreate(
                ['code' => $paymentMethod['code']],
                $paymentMethod
            );
        }
    }
}