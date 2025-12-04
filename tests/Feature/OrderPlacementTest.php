<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\UserAddress;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $vendor;
    protected $product;
    protected $address;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Create test vendor
        $this->vendor = User::factory()->create([
            'role' => 'vendor',
            'email_verified_at' => now(),
        ]);

        // Create test product
        $this->product = Product::factory()->create([
            'user_id' => $this->vendor->id,
            'name' => 'Test Product',
            'price' => 100.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Create user address
        $this->address = UserAddress::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create payment method
        PaymentMethod::create([
            'name' => 'Cash on Delivery',
            'code' => 'cod',
            'is_active' => true,
            'order' => 1,
        ]);
    }

    /** @test */
    public function user_can_place_order_with_cart_items()
    {
        // Add product to cart
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price,
        ]);

        // Place order
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
            'notes' => 'Test order',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function order_creation_includes_all_required_fields()
    {
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'vendor_id' => $this->vendor->id,
        ]);
    }

    /** @test */
    public function order_placement_decrements_product_stock()
    {
        $initialStock = $this->product->stock;

        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => $this->product->price,
        ]);

        $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
        ]);

        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->stock);
    }

    /** @test */
    public function order_with_coupon_applies_discount()
    {
        $coupon = Coupon::create([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'is_active' => true,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);

        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'TEST10',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'discount' => 10.00,
            'coupon_id' => $coupon->id,
        ]);
    }

    /** @test */
    public function cannot_place_order_with_invalid_coupon()
    {
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
            'coupon_code' => 'INVALID',
        ]);

        $response->assertSessionHas('error');
    }

    /** @test */
    public function cannot_place_order_with_insufficient_stock()
    {
        $this->product->update(['stock' => 1]);

        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'price' => $this->product->price,
        ]);

        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'shipping_address_id' => $this->address->id,
            'billing_address_id' => $this->address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHas('error');
    }
}
