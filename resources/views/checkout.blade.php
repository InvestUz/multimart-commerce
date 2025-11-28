@extends('layouts.app')

@section('title', 'Checkout - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
        
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form - Single Step -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Customer Information -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Customer Information</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping & Billing Address -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-200">Shipping & Billing Address</h2>
                        
                        @if($shippingAddresses->isNotEmpty())
                            <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                                @foreach($shippingAddresses as $address)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <label class="flex items-start space-x-3">
                                            <input type="radio" name="shipping_address_id" value="{{ $address->id }}" 
                                                   class="mt-1 h-4 w-4 text-gold-600 border-gray-300 focus:ring-gold-500" 
                                                   {{ $address->is_default ? 'checked' : '' }}>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <span class="font-medium text-gray-900">{{ $address->label }}</span>
                                                    @if($address->is_default)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gold-100 text-gold-800">
                                                            Default
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <p class="font-medium">{{ $address->full_name }}</p>
                                                    <p>{{ $address->address_line1 }}</p>
                                                    @if($address->address_line2)
                                                        <p>{{ $address->address_line2 }}</p>
                                                    @endif
                                                    <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                    <p>{{ $address->country }}</p>
                                                    <p class="mt-1 text-gray-500">{{ $address->phone }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center mb-6">
                                <i class="fas fa-map-marker-alt text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500">No shipping addresses found.</p>
                                <p class="text-gray-500 text-sm mt-1">Please add an address to continue with your order.</p>
                                <a href="{{ route('account.addresses') }}" class="mt-4 inline-block px-4 py-2 bg-gold-600 text-white rounded-md hover:bg-gold-700 font-medium">
                                    Add New Address
                                </a>
                            </div>
                        @endif
                        
                        <div class="mb-6">
                            <a href="{{ route('account.addresses') }}" class="inline-flex items-center text-gold-600 hover:text-gold-800 font-medium">
                                <i class="fas fa-plus mr-1"></i> Add New Address
                            </a>
                        </div>
                        
                        <!-- Use same address for billing -->
                        <div class="pt-4 border-t border-gray-200">
                            <label class="flex items-center">
                                <input type="checkbox" name="same_as_shipping" class="h-4 w-4 text-gold-600 border-gray-300 rounded focus:ring-gold-500" checked>
                                <span class="ml-2 text-sm text-gray-600">Use this address as my billing address</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Billing Address (only shown if different from shipping) -->
                    <div id="billing-address-section" class="bg-white rounded-lg shadow-lg p-6 border border-gray-200 hidden">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-200">Billing Address</h2>
                        
                        @if($billingAddresses->isNotEmpty())
                            <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                                @foreach($billingAddresses as $address)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <label class="flex items-start space-x-3">
                                            <input type="radio" name="billing_address_id" value="{{ $address->id }}" 
                                                   class="mt-1 h-4 w-4 text-gold-600 border-gray-300 focus:ring-gold-500">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex justify-between items-start">
                                                    <span class="font-medium text-gray-900">{{ $address->label }}</span>
                                                    @if($address->is_default)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gold-100 text-gold-800">
                                                            Default
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <p class="font-medium">{{ $address->full_name }}</p>
                                                    <p>{{ $address->address_line1 }}</p>
                                                    @if($address->address_line2)
                                                        <p>{{ $address->address_line2 }}</p>
                                                    @endif
                                                    <p>{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                    <p>{{ $address->country }}</p>
                                                    <p class="mt-1 text-gray-500">{{ $address->phone }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-6 text-center mb-6">
                                <i class="fas fa-map-marker-alt text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500">No billing addresses found.</p>
                                <p class="text-gray-500 text-sm mt-1">Please add an address to continue with your order.</p>
                                <a href="{{ route('account.addresses') }}" class="mt-4 inline-block px-4 py-2 bg-gold-600 text-white rounded-md hover:bg-gold-700 font-medium">
                                    Add New Address
                                </a>
                            </div>
                        @endif
                        
                        <div class="mb-6">
                            <a href="{{ route('account.addresses') }}" class="inline-flex items-center text-gold-600 hover:text-gold-800 font-medium">
                                <i class="fas fa-plus mr-1"></i> Add New Address
                            </a>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-200">Payment Method</h2>
                        
                        <div class="space-y-4">
                            @forelse($paymentMethods as $index => $paymentMethod)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
                                    <label class="flex items-center space-x-3">
                                        <input type="radio" name="payment_method" value="{{ $paymentMethod->code }}" 
                                               class="h-4 w-4 text-gold-600 border-gray-300 focus:ring-gold-500" 
                                               {{ $index === 0 ? 'checked' : '' }}>
                                        <div class="flex items-center space-x-3">
                                            @if($paymentMethod->image_path)
                                                <img src="{{ asset('storage/' . $paymentMethod->image_path) }}" 
                                                     alt="{{ $paymentMethod->name }}" 
                                                     class="h-8 w-8 object-contain">
                                            @endif
                                            <div>
                                                <span class="font-medium">{{ $paymentMethod->name }}</span>
                                                <p class="text-gray-600 text-sm">{{ $paymentMethod->description }}</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @empty
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-600 text-sm">No payment methods available at the moment.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Order Notes</h2>
                        
                        <div>
                            <textarea name="notes" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-gold-500 focus:border-gold-500"
                                      placeholder="Any special instructions for your order..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Coupon Code -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Have a coupon code?</h2>
                        
                        <div class="flex space-x-2">
                            <input type="text" name="coupon_code" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-gold-500 focus:border-gold-500"
                                   placeholder="Enter coupon code" 
                                   value="{{ old('coupon_code') }}">
                            <button type="button" id="apply-coupon" 
                                    class="px-4 py-2 bg-gold-600 text-white rounded-md hover:bg-gold-700 transition duration-150 ease-in-out">
                                Apply
                            </button>
                        </div>
                        <div id="coupon-message" class="mt-2 text-sm hidden"></div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4 border border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-6 pb-2 border-b border-gray-200">Order Summary</h2>
                        
                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2" id="cart-items-container">
                            @foreach($cartItems as $item)
                                <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg transition duration-150 ease-in-out cart-item" data-cart-id="{{ $item->id }}">
                                    @if($item->product->images->first())
                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 object-cover rounded-md border border-gray-200">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center border border-gray-200">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 truncate">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $item->product->vendor->name ?? 'Vendor' }}</p>
                                        <div class="flex items-center mt-1 space-x-2">
                                            <div class="flex items-center border border-gray-300 rounded-md">
                                                <button type="button" class="decrease-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-cart-id="{{ $item->id }}">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <span class="px-2 py-1 qty-display">{{ $item->quantity }}</span>
                                                <button type="button" class="increase-qty px-2 py-1 text-gray-600 hover:bg-gray-100" data-cart-id="{{ $item->id }}">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 item-total">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                        <button type="button" class="remove-item mt-1 text-red-600 hover:text-red-800 text-sm" data-cart-id="{{ $item->id }}">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="space-y-3 mb-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600 discount-line hidden">
                                <span>Discount</span>
                                <span>-$0.00</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span>$10.00</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax</span>
                                <span>${{ number_format($subtotal * 0.1, 2) }}</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span>${{ number_format($subtotal + 10 + ($subtotal * 0.1), 2) }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" class="h-4 w-4 text-gold-600 border-gray-300 rounded focus:ring-gold-500">
                                <span class="ml-2 text-sm text-gray-600">I agree to the <a href="#" class="text-gold-600 hover:text-gold-800">Terms and Conditions</a></span>
                            </label>
                        </div>
                        
                        <button type="submit" 
                                class="w-full px-6 py-4 bg-gold-600 text-white text-center rounded-md hover:bg-gold-700 font-semibold text-lg shadow-md transition duration-150 ease-in-out transform hover:-translate-y-0.5">
                            Place Order
                        </button>
                        
                        <div class="mt-4 text-center">
                            <a href="{{ route('cart.index') }}" class="text-gold-600 hover:text-gold-800 text-sm font-medium">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Cart
                            </a>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-lock text-gold-500 mr-1"></i>
                                Secure checkout guaranteed
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sameAsShippingCheckbox = document.querySelector('input[name="same_as_shipping"]');
    const billingAddressSection = document.getElementById('billing-address-section');
    const shippingAddressRadios = document.querySelectorAll('input[name="shipping_address_id"]');
    const billingAddressRadios = document.querySelectorAll('input[name="billing_address_id"]');
    const form = document.querySelector('form[action="{{ route('orders.store') }}"]');
    
    // Function to sync billing address with shipping address
    function syncBillingWithShipping() {
        if (sameAsShippingCheckbox && sameAsShippingCheckbox.checked) {
            // Find the selected shipping address
            let selectedShippingId = null;
            shippingAddressRadios.forEach(radio => {
                if (radio.checked) {
                    selectedShippingId = radio.value;
                }
            });
            
            // If no shipping address is selected but there are options, select the first one
            if (!selectedShippingId && shippingAddressRadios.length > 0) {
                selectedShippingId = shippingAddressRadios[0].value;
                shippingAddressRadios[0].checked = true;
            }
            
            // Select the same billing address
            if (selectedShippingId) {
                billingAddressRadios.forEach(radio => {
                    if (radio.value === selectedShippingId) {
                        radio.checked = true;
                    }
                });
                
                // Also create a hidden input to ensure the billing address is submitted
                let hiddenInput = document.querySelector('input[name="billing_address_id"][type="hidden"]');
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'billing_address_id';
                    form.appendChild(hiddenInput);
                }
                hiddenInput.value = selectedShippingId;
                
                // Debug: Log the hidden input creation
                console.log('Created hidden input for billing address:', selectedShippingId);
            }
        } else {
            // Remove the hidden input if it exists
            const hiddenInput = document.querySelector('input[name="billing_address_id"][type="hidden"]');
            if (hiddenInput) {
                hiddenInput.remove();
                console.log('Removed hidden input for billing address');
            }
        }
    }
    
    if (sameAsShippingCheckbox) {
        sameAsShippingCheckbox.addEventListener('change', function() {
            if (this.checked) {
                billingAddressSection.classList.add('hidden');
                // Sync billing address with shipping address
                syncBillingWithShipping();
            } else {
                billingAddressSection.classList.remove('hidden');
                // Remove the hidden input if it exists
                const hiddenInput = document.querySelector('input[name="billing_address_id"][type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.remove();
                }
            }
        });
        
        // Also sync when shipping address changes
        shippingAddressRadios.forEach(radio => {
            radio.addEventListener('change', syncBillingWithShipping);
        });
    }
    
    // Initial sync on page load if "same as shipping" is checked
    if (sameAsShippingCheckbox && sameAsShippingCheckbox.checked) {
        syncBillingWithShipping();
    }
    
    // Add form submit handler to ensure all required data is present
    if (form) {
        form.addEventListener('submit', function(e) {
            // Debug: Log form data before submission
            console.log('Form submission initiated');
            
            // Validate that both shipping and billing addresses are selected
            const shippingAddressSelected = document.querySelector('input[name="shipping_address_id"]:checked');
            const billingAddressSelected = document.querySelector('input[name="billing_address_id"]:checked') || 
                                          document.querySelector('input[name="billing_address_id"][type="hidden"]');
            
            console.log('Shipping address selected:', shippingAddressSelected ? shippingAddressSelected.value : 'None');
            console.log('Billing address selected:', billingAddressSelected ? billingAddressSelected.value : 'None');
            
            if (!shippingAddressSelected) {
                e.preventDefault();
                alert('Please select a shipping address');
                return false;
            }
            
            if (!billingAddressSelected) {
                e.preventDefault();
                alert('Please select a billing address');
                return false;
            }
            
            // Validate payment method
            const paymentMethodSelected = document.querySelector('input[name="payment_method"]:checked');
            console.log('Payment method selected:', paymentMethodSelected ? paymentMethodSelected.value : 'None');
            
            if (!paymentMethodSelected) {
                e.preventDefault();
                alert('Please select a payment method');
                return false;
            }
            
            // Log all form data
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
        });
    }
    
    // Quantity adjustment and item removal functionality
    const cartContainer = document.getElementById('cart-items-container');
    
    if (cartContainer) {
        // Increase quantity
        cartContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('increase-qty')) {
                const cartId = e.target.getAttribute('data-cart-id');
                updateQuantity(cartId, 1);
            }
        });
        
        // Decrease quantity
        cartContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('decrease-qty')) {
                const cartId = e.target.getAttribute('data-cart-id');
                updateQuantity(cartId, -1);
            }
        });
        
        // Remove item
        cartContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                const cartId = e.target.getAttribute('data-cart-id');
                removeItem(cartId);
            }
        });
    }
    
    // Coupon code functionality
    const applyCouponButton = document.getElementById('apply-coupon');
    if (applyCouponButton) {
        applyCouponButton.addEventListener('click', function() {
            const couponCode = document.querySelector('input[name="coupon_code"]').value;
            if (!couponCode) {
                showMessage('Please enter a coupon code', 'error');
                return;
            }
            
            applyCoupon(couponCode);
        });
    }
    
    // Apply coupon on Enter key
    const couponInput = document.querySelector('input[name="coupon_code"]');
    if (couponInput) {
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyCouponButton.click();
            }
        });
    }
    
    // Update quantity function
    function updateQuantity(cartId, change) {
        const itemElement = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
        const qtyDisplay = itemElement.querySelector('.qty-display');
        const itemTotalElement = itemElement.querySelector('.item-total');
        
        let currentQty = parseInt(qtyDisplay.textContent);
        let newQty = currentQty + change;
        
        // Ensure quantity doesn't go below 1
        if (newQty < 1) newQty = 1;
        
        // Update quantity display immediately
        qtyDisplay.textContent = newQty;
        
        // Send AJAX request to update quantity
        fetch(`/cart/update/${cartId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantity: newQty
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update item total
                const price = parseFloat(itemTotalElement.textContent.replace('$', '')) / currentQty;
                const newTotal = price * newQty;
                itemTotalElement.textContent = '$' + newTotal.toFixed(2);
                
                // Update subtotal
                updateOrderSummary();
            } else {
                // Revert quantity if update failed
                qtyDisplay.textContent = currentQty;
                alert(data.message || 'Failed to update quantity');
            }
        })
        .catch(error => {
            // Revert quantity if update failed
            qtyDisplay.textContent = currentQty;
            console.error('Error:', error);
            alert('Failed to update quantity: ' + error.message);
        });
    }
    
    // Remove item function
    function removeItem(cartId) {
        if (!confirm('Are you sure you want to remove this item from your cart?')) {
            return;
        }
        
        const itemElement = document.querySelector(`.cart-item[data-cart-id="${cartId}"]`);
        
        fetch(`/cart/${cartId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Remove item from DOM
                itemElement.remove();
                
                // Update order summary
                updateOrderSummary();
                
                // Show message if cart is empty
                if (document.querySelectorAll('.cart-item').length === 0) {
                    cartContainer.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-gray-500">Your cart is empty</p>
                            <a href="{{ route('home') }}" class="mt-4 inline-block text-gold-600 hover:text-gold-800">
                                Continue Shopping
                            </a>
                        </div>
                    `;
                }
            } else {
                alert(data.message || 'Failed to remove item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to remove item: ' + error.message);
        });
    }
    
    // Update order summary (subtotal, tax, total)
    function updateOrderSummary() {
        // This would normally fetch updated totals from the server
        // For now, we'll just recalculate based on visible items
        let subtotal = 0;
        
        document.querySelectorAll('.cart-item').forEach(item => {
            const itemTotal = parseFloat(item.querySelector('.item-total').textContent.replace('$', ''));
            subtotal += itemTotal;
        });
        
        // Update subtotal display
        const subtotalElement = document.querySelector('.space-y-3 .flex.justify-between:nth-child(1) span:last-child');
        if (subtotalElement) {
            subtotalElement.textContent = '$' + subtotal.toFixed(2);
        }
        
        // Update tax (10% of subtotal)
        const tax = subtotal * 0.1;
        const taxElement = document.querySelector('.space-y-3 .flex.justify-between:nth-child(3) span:last-child');
        if (taxElement) {
            taxElement.textContent = '$' + tax.toFixed(2);
        }
        
        // Update total (subtotal + shipping + tax)
        const shipping = 10; // Fixed shipping cost
        const total = subtotal + shipping + tax;
        const totalElement = document.querySelector('.border-t.pt-3.flex.justify-between span:last-child');
        if (totalElement) {
            totalElement.textContent = '$' + total.toFixed(2);
        }
    }
    
    // Show message function
    function showMessage(message, type) {
        const messageElement = document.getElementById('coupon-message');
        if (messageElement) {
            messageElement.textContent = message;
            messageElement.className = 'mt-2 text-sm ' + (type === 'error' ? 'text-red-600' : 'text-green-600');
            messageElement.classList.remove('hidden');
        }
    }
    
    // Apply coupon function
    function applyCoupon(couponCode) {
        fetch('{{ route('cart.apply-coupon') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                coupon_code: couponCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                // Update order summary with discount
                updateOrderSummaryWithDiscount(data);
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Failed to apply coupon', 'error');
        });
    }
    
    // Update order summary with discount
    function updateOrderSummaryWithDiscount(data) {
        // Show discount line
        const discountLine = document.querySelector('.discount-line');
        if (discountLine) {
            discountLine.classList.remove('hidden');
            discountLine.querySelector('span:last-child').textContent = '-$' + data.discount.toFixed(2);
        }
        
        // Update tax
        const taxElement = document.querySelector('.space-y-3 .flex.justify-between:nth-child(4) span:last-child');
        if (taxElement) {
            taxElement.textContent = '$' + data.tax.toFixed(2);
        }
        
        // Update total
        const totalElement = document.querySelector('.border-t.pt-3.flex.justify-between span:last-child');
        if (totalElement) {
            totalElement.textContent = '$' + data.total.toFixed(2);
        }
    }
});
</script>
@endsection