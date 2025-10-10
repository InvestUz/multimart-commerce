@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">My Orders</h1>

    @if($orders->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold mb-2">No orders yet</h2>
            <p class="text-gray-600 mb-6">Start shopping to see your orders here!</p>
            <a href="{{ route('dashboard') }}" class="bg-primary text-white px-6 py-3 rounded-lg inline-block hover:bg-green-600">
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">Order #{{ $order->order_number }}</h3>
                        <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                            @if($order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($order->status) }}
                        </span>
                        <p class="text-xl font-bold text-primary mt-2">${{ number_format($order->total, 2) }}</p>
                    </div>
                </div>

                <!-- Order Items Preview -->
                <div class="border-t pt-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($order->items->take(4) as $item)
                        <div class="flex gap-3">
                            <div class="w-16 h-16 flex-shrink-0">
                                @if($item->product && $item->product->primaryImage)
                                    <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-full h-full object-cover rounded">
                                @else
                                    <div class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium truncate">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-600">Qty: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($order->items->count() > 4)
                        <p class="text-sm text-gray-600 mt-2">and {{ $order->items->count() - 4 }} more item(s)</p>
                    @endif
                </div>

                <!-- Actions -->
                <div class="flex gap-3 mt-4 pt-4 border-t">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="flex-1 text-center border border-primary text-primary py-2 rounded-lg hover:bg-primary hover:text-white transition">
                        View Details
                    </a>
                    
                    @if($order->canBeCancelled())
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this order?')"
                                class="w-full border border-red-500 text-red-500 py-2 rounded-lg hover:bg-red-500 hover:text-white transition">
                            Cancel Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection