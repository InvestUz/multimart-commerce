@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">@lang('My Wishlist')</h1>
        <p class="text-gray-600">{{ $wishlists->count() }} {{ $wishlists->count() != 1 ? 'items' : 'item' }} in wishlist</p>
    </div>

    @if($wishlists->isEmpty())
        // ... existing code (empty state) ...
    @else
        <!-- Wishlist Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @foreach($wishlists as $wishlist)
                <div class="card group" data-wishlist-id="{{ $wishlist->id }}">
                    <!-- Product Image -->
                    <div class="relative overflow-hidden bg-gray-100" style="padding-bottom: 100%;">
                        @if($wishlist->product->images->first())
                            <img src="{{ asset('storage/' . $wishlist->product->images->first()->image_path) }}" 
                                 alt="{{ $wishlist->product->name }}"
                                 class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Remove Button -->
                        <button type="button" 
                                onclick="removeFromWishlist({{ $wishlist->product->id }})"
                                class="absolute top-2 right-2 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center text-red-500 hover:bg-red-50 transition-colors z-10">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                        
                        <!-- Stock Badge -->
                        @if($wishlist->product->stock > 0)
                            <span class="absolute top-2 left-2 badge badge-success text-xs">
                                In Stock
                            </span>
                        @else
                            <span class="absolute top-2 left-2 badge badge-error text-xs">
                                Out of Stock
                            </span>
                        @endif
                        
                        <!-- Discount Badge -->
                        @if($wishlist->product->old_price > $wishlist->product->price)
                            <span class="absolute bottom-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                                -{{ number_format((($wishlist->product->old_price - $wishlist->product->price) / $wishlist->product->old_price) * 100) }}%
                            </span>
                        @endif
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-4">
                        <!-- Category -->
                        <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">
                            {{ $wishlist->product->category->name ?? 'Uncategorized' }}
                        </p>
                        
                        <!-- Product Name -->
                        <h3 class="text-base font-semibold text-gray-900 mb-2 line-clamp-2 hover:text-primary transition-colors">
                            <a href="{{ route('product.show', $wishlist->product->slug) }}">
                                {{ $wishlist->product->name }}
                            </a>
                        </h3>
                        
                        <!-- Price -->
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-xl font-bold text-gray-900">
                                ${{ number_format($wishlist->product->price, 2) }}
                            </span>
                            @if($wishlist->product->old_price > $wishlist->product->price)
                                <span class="text-sm text-gray-500 line-through">
                                    ${{ number_format($wishlist->product->old_price, 2) }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            @if($wishlist->product->stock > 0)
                                <button type="button" 
                                        onclick="addToCart({{ $wishlist->product->id }})"
                                        class="flex-1 btn-primary text-sm py-2">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Add
                                </button>
                            @else
                                <button type="button" disabled
                                        class="flex-1 px-3 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-medium cursor-not-allowed">
                                    Out of Stock
                                </button>
                            @endif
                            <a href="{{ route('product.show', $wishlist->product->slug) }}" 
                               class="btn-secondary text-sm px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Actions Footer -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-xl shadow-sm p-6">
            <div class="text-gray-700">
                Showing <span class="font-semibold text-gray-900">{{ $wishlists->count() }}</span> items
            </div>
            <div class="flex gap-3">
                <a href="{{ route('home') }}" class="btn-secondary">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Continue Shopping
                </a>
                <button onclick="clearWishlist()" class="btn-secondary text-red-600 hover:bg-red-50 border-red-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Clear All
                </button>
            </div>
        </div>
    @endif
</div>

<script>
function removeFromWishlist(productId) {
    if (confirm('Remove this product from your wishlist?')) {
        fetch('{{ route('wishlist.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('✓ Removed from wishlist', 'success');
                // Reload page after short delay
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Failed to remove product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to remove product', 'error');
        });
    }
}

function addToCart(productId) {
    fetch('{{ route('cart.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count in header
            const cartCountElement = document.getElementById('cart-count');
            const mobileCartCount = document.getElementById('mobile-cart-count');
            const mobileCartBadge = document.getElementById('mobile-cart-badge');
            
            if (cartCountElement) cartCountElement.textContent = data.cart_count;
            if (mobileCartCount) mobileCartCount.textContent = data.cart_count;
            if (mobileCartBadge) mobileCartBadge.textContent = data.cart_count;
            
            showToast('🛒 Added to cart!', 'success');
        } else {
            showToast(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to add to cart', 'error');
    });
}

function clearWishlist() {
    if (confirm('Are you sure you want to clear your entire wishlist?')) {
        fetch('{{ route('wishlist.clear') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('✓ Wishlist cleared', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Failed to clear wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to clear wishlist', 'error');
        });
    }
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
</script>
@endsection