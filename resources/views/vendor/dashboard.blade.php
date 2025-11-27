@extends('layouts.vendor')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Products</h3>
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $activeProducts }} active</p>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Orders</h3>
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $totalOrders }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ $pendingOrders }} pending</p>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Total Revenue</h3>
                <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">From paid orders</p>
        </div>

        <!-- Pending Earnings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-600">Pending Earnings</h3>
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-3xl font-bold text-gray-900">${{ number_format($pendingEarnings, 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Awaiting payout</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                <a href="{{ route('vendor.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All →
                </a>
            </div>

            @if($recentOrders->count() > 0)
                <div class="space-y-3">
                    @foreach($recentOrders as $orderItem)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $orderItem->order->order_number ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $orderItem->product->name ?? 'Product deleted' }}</p>
                                <p class="text-xs text-gray-500">Customer: {{ $orderItem->order->user->name ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ number_format($orderItem->total, 2) }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full mt-1
                                    @if($orderItem->order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($orderItem->order->status === 'cancelled') bg-red-100 text-red-800
                                    @elseif($orderItem->order->status === 'processing') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($orderItem->order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No orders yet</p>
                </div>
            @endif
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Top Selling Products</h2>
                <a href="{{ route('vendor.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                    View All →
                </a>
            </div>

            @if($topProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                @if($product->images->count() > 0)
                                    <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                                         alt="{{ $product->name }}"
                                         class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">${{ number_format($product->price, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ $product->total_sold ?? 0 }} sold</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No products yet</p>
                </div>
            @endif
        </div>

        <!-- Low Stock Alert -->
        @if($lowStockProducts->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Low Stock Alert</h2>
                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">{{ $lowStockProducts->count() }}</span>
                </div>

                <div class="space-y-3">
                    @foreach($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">{{ $product->stock }} left</p>
                                <a href="{{ route('vendor.products.edit', $product) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800">
                                    Restock →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Monthly Revenue Chart Placeholder -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Revenue (Last 6 Months)</h2>

            @if($monthlyRevenue->count() > 0)
                <div class="space-y-3">
                    @foreach($monthlyRevenue as $data)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">{{ date('M Y', strtotime($data->month . '-01')) }}</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full"
                                         style="width: {{ ($data->revenue / $monthlyRevenue->max('revenue')) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 w-20 text-right">
                                    ${{ number_format($data->revenue, 0) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-sm text-gray-500">No revenue data yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
