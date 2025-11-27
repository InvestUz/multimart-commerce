@extends('layouts.app')

@section('title', 'Search Results' . ($query ? ' for "' . $query . '"' : '') . ' - ' . config('app.name'))

@section('content')
<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Search Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">
                Search Results
                @if($query)
                <span class="text-gray-600">for "{{ $query }}"</span>
                @endif
            </h1>
            <p class="text-gray-600">{{ $products->total() }} products found</p>
        </div>

        <!-- Search Form -->
        <div class="bg-gray-50 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('search') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="q" value="{{ $query }}"
                               placeholder="Search for products..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}"
                               placeholder="$0" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}"
                               placeholder="$1000" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Customer Rating</option>
                        </select>
                    </div>
                </div>

                @if(request()->anyFilled(['q', 'category', 'min_price', 'max_price']))
                <div class="flex justify-end">
                    <a href="{{ route('search') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Clear All Filters
                    </a>
                </div>
                @endif
            </form>
        </div>

        <!-- Products Grid -->
        @if($products->isNotEmpty())
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <a href="{{ route('product.show', $product->slug) }}">
                    @if($product->images->first())
                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                         alt="{{ $product->name }}"
                         class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-4xl text-gray-400"></i>
                    </div>
                    @endif
                </a>

                <div class="p-4">
                    <p class="text-xs text-gray-500 mb-1">{{ $product->vendor->shop_name }}</p>
                    <a href="{{ route('product.show', $product->slug) }}">
                        <h3 class="text-sm font-medium text-gray-900 mb-2 line-clamp-2 hover:text-indigo-600">
                            {{ $product->name }}
                        </h3>
                    </a>

                    <div class="flex items-center mb-2">
                        <div class="flex text-yellow-400 text-xs">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= ($product->reviews_avg_rating ?? 0) ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 ml-1">({{ $product->reviews_count }})</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        @auth
                        @if($product->stock > 0)
                        <button onclick="addToCart({{ $product->id }})"
                                class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        @endif
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $products->withQueryString()->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-semibold text-gray-900 mb-2">No products found</h3>
            <p class="text-gray-600 mb-6">
                @if($query)
                We couldn't find any products matching "{{ $query }}"
                @else
                Try searching for products above
                @endif
            </p>
            <a href="{{ route('home') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Browse All Products
            </a>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
@auth
function addToCart(productId) {
    fetch('{{ route("cart.store") }}', {
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
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            alert('Product added to cart!');
        } else {
            alert(data.message);
        }
    });
}
@endauth
</script>
@endpush
@endsection
