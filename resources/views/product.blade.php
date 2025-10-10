@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2 text-gray-600">
            <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
            <li><span class="mx-2">/</span></li>
            @if($product->category)
            <li><a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-blue-600">{{ $product->category->name }}</a></li>
            <li><span class="mx-2">/</span></li>
            @endif
            <li class="text-gray-900">{{ Str::limit($product->name, 50) }}</li>
        </ol>
    </nav>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Images -->
        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-4">
                @if($product->images->count() > 0)
                <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                @else
                <div class="w-full h-96 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-400">No image available</span>
                </div>
                @endif
            </div>

            <!-- Thumbnail Images -->
            @if($product->images->count() > 1)
            <div class="grid grid-cols-4 gap-2">
                @foreach($product->images as $image)
                <div class="cursor-pointer border-2 border-gray-200 hover:border-blue-500 rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $image->image_path) }}"
                        alt="{{ $product->name }}"
                        class="w-full h-24 object-cover thumbnail-image"
                        onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')">
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <!-- Product Info -->
        <div>
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Product Name -->
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                <!-- Vendor -->
                <div class="mb-4">
                    <span class="text-sm text-gray-600">Sold by: </span>
                    <span class="text-sm font-semibold text-blue-600">{{ $product->vendor->shop_name ?? $product->vendor->name }}</span>
                </div>

                <!-- Rating -->
                @if($product->reviews->count() > 0)
                <div class="flex items-center mb-4">
                    <div class="flex">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->reviews_avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                    </div>
                    <span class="ml-2 text-sm text-gray-600">{{ number_format($product->reviews_avg_rating, 1) }} ({{ $product->reviews->count() }} reviews)</span>
                </div>
                @endif

                <!-- Price -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <span class="text-4xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                        @if($product->compare_price && $product->compare_price > $product->price)
                        <span class="text-xl text-gray-500 line-through ml-4">${{ number_format($product->compare_price, 2) }}</span>
                        @endif
                    </div>
                    @if($product->compare_price && $product->compare_price > $product->price)
                    <div class="mt-2">
                        <span class="bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded">
                            Save {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->stock > 0)
                    <p class="text-green-600 font-semibold">
                        <span class="inline-block w-3 h-3 bg-green-600 rounded-full mr-2"></span>
                        In Stock ({{ $product->stock }} available)
                    </p>
                    @if($product->stock < 10)
                        <p class="text-orange-600 text-sm mt-1">Only {{ $product->stock }} left - order soon!</p>
                        @endif
                        @else
                        <p class="text-red-600 font-semibold">
                            <span class="inline-block w-3 h-3 bg-red-600 rounded-full mr-2"></span>
                            Out of Stock
                        </p>
                        @endif
                </div>

                <!-- Add to Cart Form -->
                @if($product->stock > 0 && $product->is_active)
                @auth
                <form action="{{ route('cart.store') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="flex items-center mb-4">
                        <label for="quantity" class="mr-4 font-semibold">Quantity:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button type="button" onclick="decrementQuantity()" class="px-4 py-2 text-gray-600 hover:bg-gray-100">-</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="w-20 text-center border-x border-gray-300 py-2 focus:outline-none">
                            <button type="button" onclick="incrementQuantity({{ $product->stock }})" class="px-4 py-2 text-gray-600 hover:bg-gray-100">+</button>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition">
                            Add to Cart
                        </button>

                        <button type="button" onclick="document.getElementById('wishlistForm').submit()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 p-3 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <form id="wishlistForm" action="{{ route('wishlist.toggle') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                </form>
                @else
                <div class="space-y-4">
                    <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition">
                        Login to Purchase
                    </a>
                    <p class="text-sm text-gray-600 text-center">or <a href="{{ route('register') }}" class="text-blue-600 hover:underline">create an account</a></p>
                </div>
                @endauth
                @else
                <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-3 px-6 rounded-lg cursor-not-allowed">
                    Out of Stock
                </button>
                @endif

                <!-- Product Meta -->
                <div class="border-t mt-6 pt-6">
                    <dl class="space-y-2 text-sm">
                        <div class="flex">
                            <dt class="text-gray-600 w-32">SKU:</dt>
                            <dd class="text-gray-900 font-medium">{{ $product->sku }}</dd>
                        </div>
                        @if($product->category)
                        <div class="flex">
                            <dt class="text-gray-600 w-32">Category:</dt>
                            <dd class="text-gray-900">
                                <a href="{{ route('category.show', $product->category->slug) }}" class="text-blue-600 hover:underline">
                                    {{ $product->category->name }}
                                </a>
                            </dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description & Reviews Tabs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b">
            <nav class="flex">
                <button onclick="showTab('description')" id="descriptionTab" class="px-6 py-4 font-semibold border-b-2 border-blue-600 text-blue-600">
                    Description
                </button>
                <button onclick="showTab('reviews')" id="reviewsTab" class="px-6 py-4 font-semibold text-gray-600 hover:text-gray-900">
                    Reviews ({{ $product->reviews->count() }})
                </button>
            </nav>
        </div>

        <!-- Description Tab -->
        <div id="descriptionContent" class="p-6">
            <h2 class="text-2xl font-bold mb-4">Product Description</h2>
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($product->description)) !!}
            </div>
        </div>

        <!-- Reviews Tab -->
        <div id="reviewsContent" class="p-6 hidden">
            <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

            <!-- Review Summary -->
            @if($product->reviews->count() > 0)
            <div class="flex items-center gap-8 mb-8 pb-8 border-b">
                <div class="text-center">
                    <div class="text-5xl font-bold">{{ number_format($product->reviews_avg_rating, 1) }}</div>
                    <div class="flex justify-center my-2">
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= round($product->reviews_avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            @endfor
                    </div>
                    <div class="text-sm text-gray-600">{{ $product->reviews->count() }} reviews</div>
                </div>

                <div class="flex-1">
                    @foreach([5,4,3,2,1] as $star)
                    @php
                    $count = $product->reviews->where('rating', $star)->count();
                    $percentage = $product->reviews->count() > 0 ? ($count / $product->reviews->count()) * 100 : 0;
                    @endphp
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm w-12">{{ $star }} star</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 w-12 text-right">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Write Review Form -->
            @auth
            @if(!$product->reviews->where('user_id', auth()->id())->count())
            <div class="mb-8 bg-gray-50 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-4">Write a Review</h3>
                <form action="{{ route('reviews.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" class="hidden" required>
                                <label for="rating{{ $i }}" class="cursor-pointer">
                                    <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 rating-star" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </label>
                                @endfor
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Comment *</label>
                        <textarea name="comment" id="comment" rows="4" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>

                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        Submit Review
                    </button>
                </form>
            </div>
            @endif
            @else
            <div class="mb-8 bg-gray-50 p-6 rounded-lg text-center">
                <p class="text-gray-700 mb-4">Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">login</a> to write a review</p>
            </div>
            @endauth

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($product->reviews()->where('is_approved', true)->latest()->get() as $review)
                <div class="border-b pb-6 last:border-b-0">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $review->user->name }}</div>
                            <div class="flex items-center mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    @endfor
                            </div>
                        </div>
                        <div class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</div>
                    </div>
                    <p class="text-gray-700">{{ $review->comment }}</p>

                    @auth
                    @if($review->user_id === auth()->id())
                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this review?')">
                            Delete Review
                        </button>
                    </form>
                    @endif
                    @endauth
                </div>
                @empty
                <p class="text-gray-500 text-center py-8">No reviews yet. Be the first to review this product!</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $relatedProduct)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <a href="{{ route('product.show', $relatedProduct->slug) }}">
                    @if($relatedProduct->images->first())
                    <img src="{{ asset('storage/' . $relatedProduct->images->first()->image_path) }}"
                        alt="{{ $relatedProduct->name }}"
                        class="w-full h-48 object-cover">
                    @else
                    <div class="w-full h-48 bg-gray-200"></div>
                    @endif
                </a>
                <div class="p-4">
                    <a href="{{ route('product.show', $relatedProduct->slug) }}">
                        <h3 class="font-semibold text-gray-900 hover:text-blue-600 mb-2">
                            {{ Str::limit($relatedProduct->name, 40) }}
                        </h3>
                    </a>
                    <p class="text-lg font-bold text-gray-900">${{ number_format($relatedProduct->price, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    function incrementQuantity(max) {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    function decrementQuantity() {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function showTab(tabName) {
        document.getElementById('descriptionContent').classList.add('hidden');
        document.getElementById('reviewsContent').classList.add('hidden');
        document.getElementById('descriptionTab').classList.remove('border-blue-600', 'text-blue-600');
        document.getElementById('reviewsTab').classList.remove('border-blue-600', 'text-blue-600');
        document.getElementById('descriptionTab').classList.add('text-gray-600');
        document.getElementById('reviewsTab').classList.add('text-gray-600');

        if (tabName === 'description') {
            document.getElementById('descriptionContent').classList.remove('hidden');
            document.getElementById('descriptionTab').classList.add('border-blue-600', 'text-blue-600');
            document.getElementById('descriptionTab').classList.remove('text-gray-600');
        } else {
            document.getElementById('reviewsContent').classList.remove('hidden');
            document.getElementById('reviewsTab').classList.add('border-blue-600', 'text-blue-600');
            document.getElementById('reviewsTab').classList.remove('text-gray-600');
        }
    }

    // Rating stars interaction
    document.querySelectorAll('input[name="rating"]').forEach((radio, index) => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.rating-star').forEach((star, starIndex) => {
                if (starIndex <= index) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.add('text-gray-300');
                    star.classList.remove('text-yellow-400');
                }
            });
        });
    });
</script>
@endsection