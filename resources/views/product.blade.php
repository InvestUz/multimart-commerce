@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            <div class="mb-4">
                @if($product->images->first())
                <img id="main-image" src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                     alt="{{ $product->name }}"
                     class="w-full h-96 object-cover rounded-lg">
                @endif
            </div>

            @if($product->images->count() > 1)
            <div class="grid grid-cols-4 gap-2">
                @foreach($product->images as $image)
                <img src="{{ asset('storage/' . $image->image_path) }}"
                     alt="{{ $product->name }}"
                     onclick="changeImage('{{ asset('storage/' . $image->image_path) }}')"
                     class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75">
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Details -->
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>

            <div class="flex items-center mb-4">
                <div class="flex text-yellow-400 mr-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= ($product->reviews_avg_rating ?? 0) ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <span class="text-gray-600">({{ $product->reviews_count }} reviews)</span>
            </div>

            <div class="mb-6">
                <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                @if($product->compare_price && $product->compare_price > $product->price)
                <span class="text-xl text-gray-500 line-through ml-2">${{ number_format($product->compare_price, 2) }}</span>
                <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-sm font-semibold rounded">
                    Save {{ number_format((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                </span>
                @endif
            </div>

            <div class="mb-6">
                <p class="text-gray-600">{{ $product->short_description }}</p>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600">
                    Sold by: <a href="#" class="text-indigo-600 font-semibold">{{ $product->vendor->shop_name }}</a>
                </p>
                <p class="text-sm text-gray-600">
                    Category: <a href="{{ route('category.show', $product->category->slug) }}" class="text-indigo-600">
                        {{ $product->category->name }}
                    </a>
                </p>
                @if($product->brand)
                <p class="text-sm text-gray-600">
                    Brand: <a href="{{ route('brand.show', $product->brand->slug) }}" class="text-indigo-600">
                        {{ $product->brand->name }}
                    </a>
                </p>
                @endif
            </div>

            <!-- Stock Status -->
            <div class="mb-6">
                @if($product->stock > 0)
                <span class="text-green-600 font-semibold">
                    <i class="fas fa-check-circle"></i> In Stock ({{ $product->stock }} available)
                </span>
                @else
                <span class="text-red-600 font-semibold">
                    <i class="fas fa-times-circle"></i> Out of Stock
                </span>
                @endif
            </div>

            @auth
            @if($product->stock > 0)
            <!-- Quantity Selector -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <div class="flex items-center space-x-3">
                    <button onclick="decreaseQty()" class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                           class="w-20 text-center border border-gray-300 rounded-md">
                    <button onclick="increaseQty()" class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-100">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>

            <!-- Add to Cart & Wishlist -->
            <div class="flex space-x-4 mb-6">
                <button onclick="addToCart()" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">
                    <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                </button>
                <button onclick="toggleWishlist()" class="px-6 py-3 border border-gray-300 rounded-md hover:bg-gray-50">
                    <i class="far fa-heart text-xl"></i>
                </button>
            </div>
            @endif
            @else
            <div class="mb-6">
                <a href="{{ route('login') }}" class="block w-full px-6 py-3 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 font-semibold">
                    Login to Purchase
                </a>
            </div>
            @endauth

            <!-- Product SKU -->
            <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
        </div>
    </div>

    <!-- Product Description & Specifications -->
    <div class="mt-12">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button onclick="showTab('description')" id="tab-description"
                        class="border-b-2 border-indigo-600 py-4 px-1 text-sm font-medium text-indigo-600">
                    Description
                </button>
                <button onclick="showTab('reviews')" id="tab-reviews"
                        class="border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                    Reviews ({{ $product->reviews_count }})
                </button>
            </nav>
        </div>

        <div id="content-description" class="py-8">
            <div class="prose max-w-none">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <div id="content-reviews" class="py-8 hidden">
            @foreach($reviews as $review)
            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="font-semibold">{{ $review->user->name }}</p>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }} text-sm"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</p>
                </div>
                <h4 class="font-semibold mb-2">{{ $review->title }}</h4>
                <p class="text-gray-600">{{ $review->comment }}</p>

                @if($review->vendor_response)
                <div class="mt-4 ml-8 p-4 bg-gray-50 rounded">
                    <p class="text-sm font-semibold text-gray-900">Vendor Response:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $review->vendor_response }}</p>
                </div>
                @endif
            </div>
            @endforeach

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <a href="{{ route('product.show', $related->slug) }}">
                    @if($related->images->first())
                    <img src="{{ asset('storage/' . $related->images->first()->image_path) }}"
                         alt="{{ $related->name }}"
                         class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 line-clamp-2">{{ $related->name }}</h3>
                        <p class="text-lg font-bold text-gray-900 mt-2">${{ number_format($related->price, 2) }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function changeImage(src) {
        document.getElementById('main-image').src = src;
    }

    function showTab(tab) {
        // Hide all content
        document.getElementById('content-description').classList.add('hidden');
        document.getElementById('content-reviews').classList.add('hidden');

        // Reset all tabs
        document.getElementById('tab-description').className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700';
        document.getElementById('tab-reviews').className = 'border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700';

        // Show selected content and highlight tab
        document.getElementById('content-' + tab).classList.remove('hidden');
        document.getElementById('tab-' + tab).className = 'border-b-2 border-indigo-600 py-4 px-1 text-sm font-medium text-indigo-600';
    }

    function increaseQty() {
        const input = document.getElementById('quantity');
        const max = parseInt(input.max);
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }

    function decreaseQty() {
        const input = document.getElementById('quantity');
        const current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }

    @auth
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.getAttribute('content') : '';
    }

    function addToCart() {
        const quantity = document.getElementById('quantity').value;

        fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                product_id: {{ $product->id }},
                quantity: parseInt(quantity)
            })
        })
        .then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                // Update cart count in header if element exists
                const cartCountElement = document.getElementById('cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.cart_count;
                }
                // Show success message with option to view cart
                if (confirm('Product added to cart successfully! Would you like to view your cart now?')) {
                    window.location.href = '{{ route("cart.index") }}';
                }
            } else {
                alert(data.message || 'Failed to add product to cart');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding to cart. Please try again.');
        });
    }

    function toggleWishlist() {
        fetch('{{ route("wishlist.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                product_id: {{ $product->id }}
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
                // Update wishlist count in header if element exists
                const wishlistCountElement = document.getElementById('wishlist-count');
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = data.wishlist_count;
                }
                alert(data.message);
            } else {
                alert(data.message || 'Failed to update wishlist');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating wishlist. Please try again.');
        });
    }
    @endauth
</script>
@endpush
@endsection
