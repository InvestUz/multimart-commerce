@extends('layouts.app')

@section('title', 'Shopping Cart - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if($cartItems->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                <div class="bg-white rounded-lg shadow p-6 flex gap-4" id="cart-item-{{ $item->id }}">
                    <!-- Product Image -->
                    <a href="{{ route('product.show', $item->product->slug) }}" class="flex-shrink-0">
                        @if($item->product->images->first())
                        <img src="{{ Storage::url($item->product->images->first()->image_path) }}"
                             alt="{{ $item->product->name }}"
                             class="w-24 h-24 object-cover rounded-lg">
                        @else
                        <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                        @endif
                    </a>

                    <!-- Product Details -->
                    <div class="flex-1">
                        <div class="flex justify-between">
                            <div>
                                <p class="text-xs text-gray-500">{{ $item->product->vendor->shop_name }}</p>
                                <a href="{{ route('product.show', $item->product->slug) }}"
                                   class="text-lg font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $item->product->name }}
                                </a>
                                @if($item->variant)
                                <p class="text-sm text-gray-600 mt-1">Variant: {{ $item->variant->name }}</p>
                                @endif

                                <!-- Stock Status -->
                                @if($item->product->stock < $item->quantity)
                                <p class="text-sm text-red-600 mt-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Only {{ $item->product->stock }} left in stock
                                </p>
                                @endif
                            </div>

                            <!-- Remove Button -->
                            <button onclick="removeFromCart({{ $item->id }})"
                                    class="text-red-600 hover:text-red-800 h-fit">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <!-- Quantity and Price -->
                        <div class="flex justify-between items-center mt-4">
                            <div class="flex items-center space-x-3">
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100"
                                        {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="font-semibold" id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100"
                                        {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <div class="text-right">
                                <p class="text-lg font-bold text-gray-900" id="total-{{ $item->id }}">
                                    ${{ number_format($item->price * $item->quantity, 2) }}
                                </p>
                                <p class="text-sm text-gray-500">${{ number_format($item->price, 2) }} each</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Continue Shopping -->
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('home') }}" class="text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Clear entire cart?')">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash mr-2"></i>Clear Cart
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $cartItems->count() }} items)</span>
                            <span id="subtotal-display">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span id="total-display">${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <a href="{{ route('orders.checkout') }}"
                       class="block w-full px-6 py-3 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 font-semibold mb-3">
                        Proceed to Checkout
                    </a>

                    <!-- Coupon Code -->
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm font-medium text-gray-700 mb-2">Have a coupon code?</p>
                        <form class="flex gap-2">
                            <input type="text" placeholder="Enter code"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <button type="submit"
                                    class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900 text-sm">
                                Apply
                            </button>
                        </form>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t space-y-2 text-sm text-gray-600">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                            <span>Secure checkout</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-truck text-indigo-600 mr-2"></i>
                            <span>Free shipping on orders over $50</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-undo text-blue-600 mr-2"></i>
                            <span>30-day returns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
            <p class="text-gray-600 mb-6">Looks like you haven't added anything to your cart yet.</p>
            <a href="{{ route('home') }}"
               class="inline-block px-8 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">
                Start Shopping
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;

    fetch(`/cart/update/${cartId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update quantity display
            document.getElementById(`qty-${cartId}`).textContent = newQuantity;

            // Update item total
            const pricePerItem = parseFloat(document.getElementById(`total-${cartId}`).textContent.replace('$', '')) / parseInt(document.getElementById(`qty-${cartId}`).textContent);
            document.getElementById(`total-${cartId}`).textContent = `$${(pricePerItem * newQuantity).toFixed(2)}`;

            // Update subtotal and total
            document.getElementById('subtotal-display').textContent = `$${data.subtotal.toFixed(2)}`;
            document.getElementById('total-display').textContent = `$${data.subtotal.toFixed(2)}`;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to update quantity');
    });
}

function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) return;

    fetch(`/cart/${cartId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove item from DOM
            document.getElementById(`cart-item-${cartId}`).remove();

            // Update cart count
            document.getElementById('cart-count').textContent = data.cart_count;

            // Reload if cart is empty
            if (data.cart_count === 0) {
                location.reload();
            } else {
                // Recalculate totals
                let subtotal = 0;
                document.querySelectorAll('[id^="total-"]').forEach(el => {
                    subtotal += parseFloat(el.textContent.replace('$', ''));
                });
                document.getElementById('subtotal-display').textContent = `$${subtotal.toFixed(2)}`;
                document.getElementById('total-display').textContent = `$${subtotal.toFixed(2)}`;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to remove item');
    });
}
</script>
@endpush
@endsection
