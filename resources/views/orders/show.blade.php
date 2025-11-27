@extends('layouts.app')

@section('title', 'Order Details - #' . $order->order_number)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
        <a href="{{ route('orders.index') }}" class="text-blue-600 hover:text-blue-800">
            &larr; Back to Orders
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Order Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Order #{{ $order->order_number }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Placed on {{ $order->created_at->format('F d, Y') }}</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                        @if($order->status === 'completed') bg-green-100 text-green-800
                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                        @elseif($order->status === 'shipped') bg-indigo-100 text-indigo-800
                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Items</h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center border-b border-gray-200 pb-4">
                            @if($item->product->images->first())
                                <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-16 h-16 object-cover rounded-md">
                            @else
                                <div class="bg-gray-200 border-2 border-dashed rounded-md w-16 h-16 flex items-center justify-center">
                                    <span class="text-gray-500 text-xs">No Image</span>
                                </div>
                            @endif
                            
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    
                    @if($order->discount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount</span>
                            <span class="font-medium text-green-600">-${{ number_format($order->discount, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="text-base font-medium text-gray-900">Total</span>
                        <span class="text-base font-medium text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Shipping Address -->
                <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Shipping Address</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-medium">{{ $order->shippingAddress->name ?? auth()->user()->name }}</p>
                    <p class="text-gray-600">{{ $order->shippingAddress->address }}</p>
                    <p class="text-gray-600">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                    <p class="text-gray-600">{{ $order->shippingAddress->country }}</p>
                    <p class="text-gray-600 mt-2">{{ $order->shippingAddress->phone }}</p>
                </div>

                <!-- Payment Information -->
                <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Payment Information</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="font-medium">Payment Method</p>
                    <p class="text-gray-600 capitalize">{{ $order->payment_method }}</p>
                    
                    <p class="font-medium mt-3">Payment Status</p>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($order->payment_status === 'completed') bg-green-100 text-green-800
                        @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection