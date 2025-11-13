@extends('layouts.app')

@section('title', $category->name . ' - ' . config('app.name'))

@section('content')
<div class="bg-white">
    <!-- Breadcrumb -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-700">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">{{ $category->name }}</li>
            </ol>
        </nav>
    </div>

    <!-- Category Header -->
    <div class="bg-gray-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center">
                @if($category->image)
                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-20 h-20 rounded-lg mr-4 object-cover">
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                    @if($category->description)
                    <p class="text-gray-600 mt-2">{{ $category->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-lg shadow p-6">
                    <!-- Sub-Categories -->
                    @if($subCategories->isNotEmpty())
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sub-Categories</h3>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}"
                                   class="text-gray-700 hover:text-indigo-600 {{ !request('sub_category') ? 'font-semibold text-indigo-600' : '' }}">
                                    All Products
                                </a>
                            </li>
                            @foreach($subCategories as $subCategory)
                            <li>
                                <a href="{{ route('category.show', $category->slug) }}?sub_category={{ $subCategory->id }}"
                                   class="text-gray-700 hover:text-indigo-600 {{ request('sub_category') == $subCategory->id ? 'font-semibold text-indigo-600' : '' }}">
                                    {{ $subCategory->name }} ({{ $subCategory->products_count }})
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Price Filter -->
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Price Range</h3>
                        <form method="GET" action="{{ route('category.show', $category->slug) }}">
                            <input type="hidden" name="sub_category" value="{{ request('sub_category') }}">
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600">Min Price</label>
                                    <input type="number" name="min_price" value="{{ request('min_price') }}"
                                           placeholder="$0" min="0"
                                           class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Max Price</label>
                                    <input type="number" name="max_price" value="{{ request('max_price') }}"
                                           placeholder="$1000" min="0"
                                           class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                    Apply Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Clear Filters -->
                    @if(request()->anyFilled(['min_price', 'max_price', 'sub_category']))
                    <div class="border-t pt-6">
                        <a href="{{ route('category.show', $category->slug) }}"
                           class="block w-full px-4 py-2 text-center border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Clear All Filters
                        </a>
                    </div>
                    @endif
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="flex-1">
                <!-- Sort and Display Options -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600">
                        Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                    </p>
                    <form method="GET" action="{{ route('category.show', $category->slug) }}" class="flex items-center space-x-2">
                        <input type="hidden" name="sub_category" value="{{ request('sub_category') }}">
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        <label class="text-sm text-gray-600">Sort by:</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="relevance" {{ request('sort') == 'relevance' ? 'selected' : '' }}>Relevance</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Customer Rating</option>
                        </select>
                    </form>
                </div>

                <!-- Products -->
                @if($products->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                        <a href="{{ route('product.show', $product->slug) }}" class="block relative">
                            @if($product->images->first())
                            <img src="{{ Storage::url($product->images->first()->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <i class="fas fa-image text-4xl text-gray-400"></i>
                            </div>
                            @endif

                            @if($product->compare_price && $product->compare_price > $product->price)
                            <span class="absolute top-2 left-2 px-2 py-1 bg-red-500 text-white text-xs font-semibold rounded">
                                -{{ number_format((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                            </span>
                            @endif
                        </a>

                        <div class="p-4">
                            <p class="text-xs text-gray-500 mb-1">{{ $product->vendor->shop_name }}</p>
                            <a href="{{ route('product.show', $product->slug) }}" class="block">
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
                                <div>
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                    <span class="text-xs text-gray-500 line-through block">${{ number_format($product->compare_price, 2) }}</span>
                                    @endif
                                </div>

                                @auth
                                @if($product->stock > 0)
                                <button onclick="addToCart({{ $product->id }})"
                                        class="px-3 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                                @else
                                <span class="text-xs text-red-600 font-semibold">Out of Stock</span>
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
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                    <p class="text-gray-600 mb-4">Try adjusting your filters or browse other categories.</p>
                    <a href="{{ route('category.show', $category->slug) }}"
                       class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                        Clear Filters
                    </a>
                </div>
                @endif
            </div>
        </div>
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

            // Show success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
            notification.textContent = 'Product added to cart!';
            document.body.appendChild(notification);

            setTimeout(() => notification.remove(), 3000);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to add product to cart');
    });
}
@endauth
</script>
@endpush
@endsection
