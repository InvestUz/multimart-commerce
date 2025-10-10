@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>

    <form action="{{ route('orders.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Contact Information</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Full Name *</label>
                            <input type="text" name="customer_name" value="{{ auth()->user()->name }}" 
                                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            @error('customer_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Email *</label>
                                <input type="email" name="customer_email" value="{{ auth()->user()->email }}" 
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                @error('customer_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Phone Number *</label>
                                <input type="tel" name="customer_phone" value="{{ auth()->user()->phone }}" 
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                @error('customer_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Shipping Address</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Full Address *</label>
                            <textarea name="shipping_address" rows="3" 
                                      class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" 
                                      required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">City</label>
                                <input type="text" name="city" value="{{ old('city') }}" 
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Postal Code</label>
                                <input type="text" name="postal_code" value="{{ old('postal_code') }}" 
                                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Payment Method</h2>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-primary">
                            <input type="radio" name="payment_method" value="cash_on_delivery" checked class="mr-3">
                            <div>
                                <p class="font-semibold">Cash on Delivery</p>
                                <p class="text-sm text-gray-600">Pay when you receive your order</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-primary">
                            <input type="radio" name="payment_method" value="credit_card" class="mr-3">
                            <div>
                                <p class="font-semibold">Credit Card</p>
                                <p class="text-sm text-gray-600">Pay securely with your credit card</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer hover:border-primary">
                            <input type="radio" name="payment_method" value="paypal" class="mr-3">
                            <div>
                                <p class="font-semibold">PayPal</p>
                                <p class="text-sm text-gray-600">Pay with your PayPal account</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-4">Order Notes (Optional)</h2>
                    <textarea name="notes" rows="3" placeholder="Any special instructions?" 
                              class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-3 mb-4 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                        <div class="flex gap-3">
                            <div class="w-16 h-16 flex-shrink-0">
                                @if($item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                         alt="{{ $item->product->name }}" 
                                         class="w-full h-full object-cover rounded">
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                                <p class="text-sm font-semibold">${{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                            <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-semibold">${{ number_format($shipping, 2) }}</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between">
                            <span class="text-lg font-bold">Total</span>
                            <span class="text-lg font-bold text-primary">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg hover:bg-green-600 font-semibold mt-6">
                        Place Order
                    </button>

                    <p class="text-xs text-gray-600 text-center mt-4">
                        By placing your order, you agree to our terms and conditions
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
