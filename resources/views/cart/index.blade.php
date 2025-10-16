@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">Shopping Cart</h1>

    @if($cartItems->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Add some products to get started!</p>
            <a href="{{ route('dashboard') }}" class="bg-primary text-white px-6 py-3 rounded-lg inline-block hover:bg-green-600">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-lg shadow-sm p-4" id="cart-item-{{ $item->id }}">
                    <div class="flex gap-4">
                        <!-- Product Image -->
                        <div class="w-24 h-24 flex-shrink-0">
                            @if($item->product->primaryImage)
                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-full h-full object-cover rounded">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1">
                            <a href="{{ route('product.show', $item->product->slug) }}" class="font-semibold hover:text-primary">
                                {{ $item->product->name }}
                            </a>
                            <p class="text-sm text-gray-600">
                                by {{ $item->product->user->store_name ?? $item->product->user->name }}
                            </p>
                            @if($item->size)
                                <p class="text-sm text-gray-600">Size: {{ $item->size }}</p>
                            @endif
                            @if($item->color)
                                <p class="text-sm text-gray-600">Color: {{ $item->color }}</p>
                            @endif
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center border rounded">
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                            class="px-3 py-1 hover:bg-gray-100 text-lg">−</button>
                                    <span class="px-4 py-1 border-x min-w-12 text-center" id="quantity-{{ $item->id }}">{{ $item->quantity }}</span>
                                    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                            class="px-3 py-1 hover:bg-gray-100 text-lg">+</button>
                                </div>
                                <button onclick="removeFromCart({{ $item->id }})" 
                                        class="text-red-500 hover:text-red-700 text-sm">
                                    <i class="fas fa-trash"></i> Remove
                                </button>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="text-right">
                            <p class="text-lg font-bold text-primary">{{ number_format($item->price, 2) }} So'm</p>
                            <p class="text-sm text-gray-600">Subtotal: <span id="subtotal-{{ $item->id }}">{{ number_format($item->price * $item->quantity, 2) }}</span> So'm</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-20">
                    <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold" id="cart-subtotal">{{ number_format($subtotal, 2) }} So'm</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-semibold">{{ number_format(4.99, 2) }} So'm</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <span class="text-lg font-bold">Total</span>
                            <span class="text-lg font-bold text-primary" id="cart-total">{{ number_format($total, 2) }} So'm</span>
                        </div>
                    </div>

                    <a href="{{ route('checkout') }}" class="block w-full bg-primary text-white text-center py-3 rounded-lg hover:bg-green-600 font-semibold mb-3">
                        Proceed to Checkout
                    </a>
                    <a href="{{ route('dashboard') }}" class="block w-full border border-gray-300 text-center py-3 rounded-lg hover:bg-gray-50">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
const SHIPPING_COST = 4.99;

function updateQuantity(cartId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartId);
        return;
    }

    fetch(`/cart/${cartId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ quantity: quantity })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update quantity display
            document.getElementById(`quantity-${cartId}`).textContent = quantity;
            
            // Update item subtotal
            document.getElementById(`subtotal-${cartId}`).textContent = data.subtotal.toFixed(2);
            
            // Update cart totals
            document.getElementById(`cart-subtotal`).textContent = data.cart_subtotal.toFixed(2) + ' So\'m';
            document.getElementById(`cart-total`).textContent = data.cart_total.toFixed(2) + ' So\'m';
            
            // Update header cart count
            updateCartCount();
        } else {
            alert(data.message || 'Error updating cart');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error updating cart');
    });
}

function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`/cart/${cartId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const itemElement = document.getElementById(`cart-item-${cartId}`);
            if (itemElement) {
                itemElement.remove();
            }
            
            // Update cart totals
            document.getElementById(`cart-subtotal`).textContent = data.cart_subtotal.toFixed(2) + ' So\'m';
            document.getElementById(`cart-total`).textContent = data.cart_total.toFixed(2) + ' So\'m';
            
            // Update header cart count
            updateCartCount();
            
            // Reload if cart is now empty
            if (data.cart_count === 0) {
                setTimeout(() => location.reload(), 500);
            }
        } else {
            alert(data.message || 'Error removing item');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Error removing item');
    });
}

function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(res => res.json())
        .then(data => {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.count;
            }
        })
        .catch(err => console.error('Error updating cart count:', err));
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', updateCartCount);
</script>
@endpush
@endsection