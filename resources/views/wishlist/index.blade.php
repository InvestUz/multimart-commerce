@extends('layouts.app')

@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">My Wishlist</h1>

    @if($wishlists->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold mb-2">Your wishlist is empty</h2>
            <p class="text-gray-600 mb-6">Start adding products to your wishlist!</p>
            <a href="{{ route('dashboard') }}" class="bg-primary text-white px-6 py-3 rounded-lg inline-block hover:bg-green-600">
                Continue Shopping
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($wishlists as $wishlist)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow" id="wishlist-item-{{ $wishlist->id }}">
                <!-- Product Image -->
                <div class="relative w-full h-48 bg-gray-200 overflow-hidden group">
                    @if($wishlist->product->primaryImage)
                        <img src="{{ asset('storage/' . $wishlist->product->primaryImage->image_path) }}" 
                             alt="{{ $wishlist->product->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif

                    <!-- Wishlist Remove Button -->
                    <button onclick="removeFromWishlist({{ $wishlist->id }})" 
                            class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors"
                            title="Remove from wishlist">
                        <i class="fas fa-heart"></i>
                    </button>

                    <!-- Category Badge -->
                    @if($wishlist->product->category)
                        <span class="absolute top-2 left-2 bg-primary text-white px-3 py-1 rounded-full text-sm">
                            {{ $wishlist->product->category->name }}
                        </span>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="p-4">
                    <a href="{{ route('product.show', $wishlist->product->slug) }}" 
                       class="text-lg font-semibold text-gray-800 hover:text-primary line-clamp-2">
                        {{ $wishlist->product->name }}
                    </a>

                    <p class="text-sm text-gray-600 mt-1">
                        by {{ $wishlist->product->user->store_name ?? $wishlist->product->user->name }}
                    </p>

                    <div class="flex items-center justify-between mt-3">
                        <div>
                            @if($wishlist->product->discount_price)
                                <p class="text-lg font-bold text-primary">{{ number_format($wishlist->product->discount_price, 2) }} So'm</p>
                                <p class="text-sm text-gray-400 line-through">{{ number_format($wishlist->product->price, 2) }} So'm</p>
                            @else
                                <p class="text-lg font-bold text-primary">{{ number_format($wishlist->product->price, 2) }} So'm</p>
                            @endif
                        </div>
                        <div class="text-yellow-400">
                            @for($i = 0; $i < 5; $i++)
                                @if($i < floor($wishlist->product->average_rating ?? 0))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <form action="{{ route('cart.store') }}" method="POST" class="mt-4" id="cart-form-{{ $wishlist->product->id }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $wishlist->product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        
                        <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-green-600 transition-colors font-semibold">
                            <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                        </button>
                    </form>

                    <!-- View Details Link -->
                    <a href="{{ route('product.show', $wishlist->product->slug) }}" 
                       class="w-full block text-center border border-gray-300 text-gray-700 py-2 rounded-lg mt-2 hover:bg-gray-50 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@push('scripts')
<script>
function removeFromWishlist(wishlistId) {
    if (!confirm('Remove this item from wishlist?')) return;

    fetch(`/wishlist/${wishlistId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const itemElement = document.getElementById(`wishlist-item-${wishlistId}`);
            if (itemElement) {
                itemElement.remove();
            }
            
            // Update header wishlist count
            updateWishlistCount();
            
            // Show success message
            showNotification(data.message, 'success');
            
            // Reload if wishlist is now empty
            setTimeout(() => {
                if (document.querySelectorAll('[id^="wishlist-item-"]').length === 0) {
                    location.reload();
                }
            }, 500);
        } else {
            showNotification(data.message || 'Error removing item', 'error');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        showNotification('Error removing item', 'error');
    });
}

function updateWishlistCount() {
    fetch('{{ route("wishlist.count") }}')
        .then(res => res.json())
        .then(data => {
            const wishlistCountElement = document.getElementById('wishlist-count');
            if (wishlistCountElement) {
                wishlistCountElement.textContent = data.count || '0';
            }
        })
        .catch(err => console.error('Error updating wishlist count:', err));
}

function showNotification(message, type) {
    const alertClass = type === 'success' 
        ? 'bg-green-100 border-green-400 text-green-700' 
        : 'bg-red-100 border-red-400 text-red-700';
    
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 ${alertClass} border px-4 py-3 rounded z-50`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => notification.remove(), 3000);
}

// Update wishlist count on page load
document.addEventListener('DOMContentLoaded', updateWishlistCount);
</script>
@endpush
@endsection