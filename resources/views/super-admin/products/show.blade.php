@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="{{ route('super-admin.products.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-3xl font-bold">Product Details</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Images -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Images</h2>
                @if($product->images->count() > 0)
                    <div class="space-y-4">
                        @foreach($product->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center">
                        <span class="text-gray-400">No images</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $product->name }}</h2>
                        <p class="text-gray-600">SKU: {{ $product->sku }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <form action="{{ route('super-admin.products.toggle-featured', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 {{ $product->is_featured ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white rounded-lg">
                                {{ $product->is_featured ? '★ Featured' : 'Feature' }}
                            </button>
                        </form>
                        <form action="{{ route('super-admin.products.toggle-status', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 {{ $product->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg">
                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Price</h3>
                        <p class="text-2xl font-bold text-green-600">${{ number_format($product->price, 2) }}</p>
                        @if($product->compare_price)
                            <p class="text-sm text-gray-500 line-through">${{ number_format($product->compare_price, 2) }}</p>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Stock</h3>
                        <p class="text-2xl font-bold {{ $product->stock < 10 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->stock }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $product->stock < 10 ? 'Low stock!' : 'In stock' }}</p>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
                    <p class="text-gray-700">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Additional Information</h2>
                <dl class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vendor</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ route('super-admin.vendors.show', $product->vendor) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $product->vendor->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Category</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->category->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Featured</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $product->is_featured ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_featured ? 'Yes' : 'No' }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Reviews -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Reviews ({{ $product->reviews->count() }})</h2>
                @if($product->reviews->count() > 0)
                    <div class="space-y-4">
                        @foreach($product->reviews()->latest()->take(5)->get() as $review)
                            <div class="border-b pb-4 last:border-b-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $review->user->name }}</span>
                                            <div class="flex ml-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $review->comment }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$review->is_approved)
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($product->reviews->count() > 5)
                        <a href="{{ route('super-admin.reviews.index', ['product' => $product->id]) }}" class="block text-center text-blue-600 hover:text-blue-800 mt-4">
                            View All Reviews
                        </a>
                    @endif
                @else
                    <p class="text-gray-500 text-center py-4">No reviews yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection