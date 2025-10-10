@extends('layouts.app')

@section('title', 'Super Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Super Admin Dashboard</h1>
        <div class="text-sm text-gray-600">
            <i class="fas fa-calendar mr-2"></i>{{ now()->format('F d, Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-3xl font-bold mt-2">${{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-arrow-up mr-1"></i> From delivered orders
                    </p>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalOrders) }}</p>
                    <p class="text-yellow-600 text-sm mt-1">
                        <i class="fas fa-clock mr-1"></i> {{ $pendingOrders }} Pending
                    </p>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Products</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalProducts) }}</p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-check mr-1"></i> {{ $activeProducts }} Active
                    </p>
                </div>
                <div class="bg-purple-100 p-4 rounded-full">
                    <i class="fas fa-box text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Vendors -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">Total Vendors</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($totalVendors) }}</p>
                    <p class="text-green-600 text-sm mt-1">
                        <i class="fas fa-check mr-1"></i> {{ $activeVendors }} Active
                    </p>
                </div>
                <div class="bg-indigo-100 p-4 rounded-full">
                    <i class="fas fa-store text-indigo-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-gray-600 text-sm">Customers</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($totalCustomers) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-gray-600 text-sm">Categories</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($totalCategories) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-gray-600 text-sm">Pending Reviews</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($pendingReviews) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-gray-600 text-sm">Active Products</p>
            <p class="text-2xl font-bold mt-1">{{ number_format($activeProducts) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Recent Orders</h2>
                <a href="{{ route('super-admin.orders.index') }}" class="text-primary hover:underline text-sm">
                    View All →
                </a>
            </div>

            @if($recentOrders->isEmpty())
                <p class="text-gray-500 text-center py-8">No orders yet</p>
            @else
                <div class="space-y-3">
                    @foreach($recentOrders->take(5) as $order)
                    <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <p class="font-semibold text-sm">#{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-600">{{ $order->customer_name }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-primary">${{ number_format($order->total, 2) }}</p>
                            <span class="text-xs px-2 py-1 rounded-full
                                @if($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst($order->status) }}
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
                <a href="{{ route('super-admin.products.index') }}" class="text-primary hover:underline text-sm">
                    View All →
                </a>
            </div>

            @if($topSellingProducts->isEmpty())
                <p class="text-gray-500 text-center py-8">No products yet</p>
            @else
                <div class="space-y-3">
                    @foreach($topSellingProducts->take(5) as $product)
                    <div class="flex items-center gap-3 p-3 border rounded-lg hover:bg-gray-50">
                        <div class="w-12 h-12 flex-shrink-0">
                            @if($product->primaryImage)
                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover rounded">
                            @else
                                <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-600">by {{ $product->user->store_name ?? $product->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-primary">${{ number_format($product->price, 2) }}</p>
                            <p class="text-xs text-gray-600">{{ $product->total_sales }} sold</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Orders by Status -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Orders by Status</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach(['pending' => 'yellow', 'processing' => 'blue', 'shipped' => 'purple', 'delivered' => 'green', 'cancelled' => 'red'] as $status => $color)
            <div class="text-center p-4 border rounded-lg">
                <p class="text-gray-600 text-sm capitalize">{{ $status }}</p>
                <p class="text-2xl font-bold mt-2">{{ $ordersByStatus[$status] ?? 0 }}</p>
                <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $color }}-500" style="width: {{ $totalOrders > 0 ? (($ordersByStatus[$status] ?? 0) / $totalOrders * 100) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Top Vendors -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Top Vendors by Revenue</h2>
                <a href="{{ route('super-admin.vendors.index') }}" class="text-primary hover:underline text-sm">
                    View All →
                </a>
            </div>

            @if($topVendors->isEmpty())
                <p class="text-gray-500 text-center py-8">No vendors yet</p>
            @else
                <div class="space-y-3">
                    @foreach($topVendors as $vendor)
                    <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                        <div>
                            <p class="font-semibold">{{ $vendor->store_name ?? $vendor->name }}</p>
                            <p class="text-xs text-gray-600">{{ $vendor->email }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-primary">${{ number_format($vendor->order_items_sum_total ?? 0, 2) }}</p>
                            <span class="text-xs px-2 py-1 rounded-full {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pending Reviews -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Pending Reviews</h2>
                <a href="{{ route('super-admin.reviews.index') }}" class="text-primary hover:underline text-sm">
                    View All →
                </a>
            </div>

            @if($recentReviews->isEmpty())
                <p class="text-gray-500 text-center py-8">No pending reviews</p>
            @else
                <div class="space-y-3">
                    @foreach($recentReviews as $review)
                    <div class="p-3 border rounded-lg hover:bg-gray-50">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-semibold text-sm">{{ $review->user->name }}</p>
                            <div class="flex items-center text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-xs {{ $i <= $review->rating ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 truncate">{{ $review->product->name }}</p>
                        @if($review->comment)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $review->comment }}</p>
                        @endif
                        <div class="flex gap-2 mt-2">
                            <form action="{{ route('super-admin.reviews.approve', $review) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">
                                    Approve
                                </button>
                            </form>
                            <form action="{{ route('super-admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection