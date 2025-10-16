@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Vendor Dashboard</h1>
        <a href="{{ route('vendor.products.create') }}" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus mr-2"></i> Add New Product
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Products</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalProducts }}</p>
                    <p class="text-green-600 text-sm mt-1">{{ $activeProducts }} Active</p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-box text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold mt-2">{{ $totalOrders }}</p>
                    <p class="text-yellow-600 text-sm mt-1">{{ $pendingOrders }} Pending</p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-shopping-cart text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-gray-600 text-sm mt-1">All time</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Out of Stock</p>
                    <p class="text-3xl font-bold mt-2">{{ $outOfStock }}</p>
                    <p class="text-red-600 text-sm mt-1">Need attention</p>
                </div>
                <div class="bg-red-100 p-4 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Recent Orders</h2>
                <a href="{{ route('vendor.orders.index') }}" class="text-primary hover:underline text-sm">View All</a>
            </div>

            @if($recentOrders->isEmpty())
                <p class="text-gray-500 text-center py-8">No orders yet</p>
            @else
                <div class="space-y-3">
                    @foreach($recentOrders->take(5) as $orderItem)
                    <div class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50">
                        <div class="w-12 h-12 flex-shrink-0">
                            @if($orderItem->product && $orderItem->product->primaryImage)
                                <img src="{{ asset('storage/' . $orderItem->product->primaryImage->image_path) }}" 
                                     alt="{{ $orderItem->product_name }}" 
                                     class="w-full h-full object-cover rounded">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $orderItem->product_name }}</p>
                            <p class="text-xs text-gray-600">Order #{{ $orderItem->order->order_number }}</p>
                            <p class="text-xs text-gray-600">{{ $orderItem->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-primary">${{ number_format($orderItem->total, 2) }}</p>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($orderItem->order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($orderItem->order->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($orderItem->order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Top Selling Products</h2>
                <a href="{{ route('vendor.products.index') }}" class="text-primary hover:underline text-sm">View All</a>
            </div>

            @if($topProducts->isEmpty())
                <p class="text-gray-500 text-center py-8">No products yet</p>
            @else
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                    <div class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50">
                        <div class="w-12 h-12 flex-shrink-0">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover rounded">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-600">Sales: {{ $product->total_sales }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-primary">${{ number_format($product->price, 2) }}</p>
                            <p class="text-xs text-gray-600">Stock: {{ $product->stock }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- <!-- Low Stock Alert -->
    @if($lowStockProducts->isNotEmpty())
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-8">
        <div class="flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mt-1"></i>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-yellow-800 mb-2">Low Stock Alert</h3>
                <p class="text-yellow-700 mb-4">The following products are running low on stock:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($lowStockProducts as $product)
                    <div class="bg-white p-3 rounded-lg border">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 flex-shrink-0">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                         alt="{{ $product->name }}" 
                                         class="w-full h-full object-cover rounded">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">{{ $product->name }}</p>
                                <p class="text-xs text-red-600 font-semibold">Only {{ $product->stock }} left</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif --}}
</div>
@endsection
