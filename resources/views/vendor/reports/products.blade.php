@extends('layouts.vendor')

@section('title', 'Product Reports')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Product Reports</h1>
            <p class="text-gray-600 mt-1">Analyze your product performance</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($topProducts as $product)
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $product->total_sold ?? 0 }} sold</p>
                            <p class="text-sm text-gray-500">${{ number_format($product->total_revenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        No product sales data available
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Low Stock Products</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($lowStockProducts as $product)
                    <div class="p-6 flex items-center">
                        <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                            @if($product->images->first())
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600">{{ $product->stock }} in stock</p>
                            <a href="{{ route('vendor.products.edit', $product) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800">
                                Restock
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        No low stock products
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Product Performance Summary -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Performance Summary</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $topProducts->count() }}</p>
                <p class="text-sm text-gray-600">Total Products</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $topProducts->sum('total_sold') }}</p>
                <p class="text-sm text-gray-600">Total Units Sold</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">${{ number_format($topProducts->sum('total_revenue'), 2) }}</p>
                <p class="text-sm text-gray-600">Total Revenue</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $lowStockProducts->count() }}</p>
                <p class="text-sm text-gray-600">Low Stock Items</p>
            </div>
        </div>
    </div>
</div>
@endsection