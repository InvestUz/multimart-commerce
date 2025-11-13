@extends('layouts.app')

@section('title', $brand->name . ' - Brands - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">Home</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('brands.index') }}" class="text-gray-600 hover:text-gray-900">Brands</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-medium">{{ $brand->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Brand Header -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-8">
            <div class="flex items-center space-x-6">
                <!-- Brand Logo -->
                @if($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}"
                         alt="{{ $brand->name }}"
                         class="w-24 h-24 object-contain">
                @else
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center">
                        <span class="text-4xl font-bold text-gray-400">
                            {{ substr($brand->name, 0, 1) }}
                        </span>
                    </div>
                @endif

                <!-- Brand Info -->
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $brand->name }}</h1>
                    @if($brand->description)
                        <p class="mt-2 text-gray-600">{{ $brand->description }}</p>
                    @endif
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container mx-auto px-4 py-8">
        @if($products->count() > 0)
            <!-- Products Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden group">
                        <a href="{{ route('product.show', $product->slug) }}">
                            <!-- Product Image -->
                            <div class="relative overflow-hidden bg-gray-100 aspect-square">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Discount Badge -->
                                @if($product->discount_percentage > 0)
                                    <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-semibold px-2 py-1 rounded">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                @endif

                                <!-- Featured Badge -->
                                @if($product->is_featured)
                                    <span class="absolute top-2 right-2 bg-yellow-400 text-gray-900 text-xs font-semibold px-2 py-1 rounded">
                                        Featured
                                    </span>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <!-- Vendor Name -->
                                <p class="text-xs text-gray-500 mb-1">{{ $product->vendor->name }}</p>

                                <!-- Product Name -->
                                <h3 class="text-sm font-medium text-gray-900 line-clamp-2 mb-2 group-hover:text-blue-600 transition-colors">
                                    {{ $product->name }}
                                </h3>

                                <!-- Rating -->
                                @if($product->reviews_avg_rating)
                                    <div class="flex items-center mb-2">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($product->reviews_avg_rating))
                                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-500 ml-1">
                                            ({{ $product->reviews_count }})
                                        </span>
                                    </div>
                                @endif

                                <!-- Price -->
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg font-bold text-gray-900">
                                        ${{ number_format($product->price, 2) }}
                                    </span>
                                    @if($product->old_price)
                                        <span class="text-sm text-gray-500 line-through">
                                            ${{ number_format($product->old_price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No products found</h3>
                <p class="mt-2 text-sm text-gray-500">This brand doesn't have any products yet.</p>
                <div class="mt-6">
                    <a href="{{ route('brands.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        View All Brands
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
