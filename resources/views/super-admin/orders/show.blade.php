@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Order Details</h1>
                <p class="text-gray-600 mt-1">Order #{{ $order->order_number }}</p>
            </div>
            <a href="{{ route('super-admin.orders.index') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                ← Back to Orders
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Order Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Customer Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Customer Name</label>
                            <p class="text-gray-900 mt-1">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Email</label>
                            <p class="text-gray-900 mt-1">{{ $order->customer_email }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Phone</label>
                            <p class="text-gray-900 mt-1">{{ $order->customer_phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Shipping Address</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-sm font-medium text-gray-600">Address</label>
                            <p class="text-gray-900 mt-1">{{ $order->shipping_address }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">City</label>
                            <p class="text-gray-900 mt-1">{{ $order->city }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">State</label>
                            <p class="text-gray-900 mt-1">{{ $order->state ?? '—' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Postal Code</label>
                            <p class="text-gray-900 mt-1">{{ $order->postal_code }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Country</label>
                            <p class="text-gray-900 mt-1">{{ $order->country }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                @if($order->items && $order->items->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Order Items</h2>
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex justify-between items-center py-3 border-b last:border-b-0">
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $item->product_name ?? 'Product' }}</p>
                                <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</p>
                                <p class="text-sm text-gray-600">${{ number_format($item->price, 2) }} each</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Notes -->
                @if($order->notes || $order->admin_notes)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Notes</h2>
                    @if($order->notes)
                    <div class="mb-4">
                        <label class="text-sm font-medium text-gray-600">Customer Notes</label>
                        <p class="text-gray-900 mt-1">{{ $order->notes }}</p>
                    </div>
                    @endif
                    @if($order->admin_notes)
                    <div>
                        <label class="text-sm font-medium text-gray-600">Admin Notes</label>
                        <p class="text-gray-900 mt-1">{{ $order->admin_notes }}</p>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Sidebar - Status & Actions -->
            <div class="space-y-6">
                <!-- Order Status -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Status</h2>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Status</label>
                            <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Payment Status</label>
                            <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold
                                @if($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-600">Payment Method</label>
                            <p class="text-gray-900 mt-1">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal:</span>
                            <span>${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping:</span>
                            <span>${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Tax:</span>
                            <span>${{ number_format($order->tax, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount:</span>
                            <span>-${{ number_format($order->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-3 border-t">
                            <span>Total:</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Actions</h2>
                    
                    <!-- Update Status Form -->
                    <form action="{{ route('super-admin.orders.update-status', $order) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Update Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Admin Notes</label>
                            <textarea name="admin_notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Add notes...">{{ $order->admin_notes }}</textarea>
                        </div>

                        <button type="submit" 
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                            Update Status
                        </button>
                    </form>

                    <!-- Approve Order Button (if pending) -->
                    @if($order->status === 'pending')
                    <form action="{{ route('super-admin.orders.update-status', $order) }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="status" value="processing">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            ✓ Approve Order
                        </button>
                    </form>
                    @endif
                </div>

                <!-- Timeline -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Timeline</h2>
                    
                    <div class="space-y-3 text-sm">
                        <div>
                            <label class="font-medium text-gray-600">Created:</label>
                            <p class="text-gray-900">{{ $order->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($order->paid_at)
                        <div>
                            <label class="font-medium text-gray-600">Paid:</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($order->paid_at)->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        @if($order->shipped_at)
                        <div>
                            <label class="font-medium text-gray-600">Shipped:</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($order->shipped_at)->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                        @if($order->delivered_at)
                        <div>
                            <label class="font-medium text-gray-600">Delivered:</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($order->delivered_at)->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection