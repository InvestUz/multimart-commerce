@extends('layouts.vendor')

@section('title', 'Order Details - #' . $order->order_number)

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
        <a href="{{ route('vendor.orders.index') }}" class="text-blue-600 hover:text-blue-800">
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
                    <!-- We'll show the status of the first item as the overall order status for the vendor -->
                    @php
                        $vendorStatus = $order->items->first()->vendor_status;
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        @if($vendorStatus === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($vendorStatus === 'processing') bg-blue-100 text-blue-800
                        @elseif($vendorStatus === 'shipped') bg-indigo-100 text-indigo-800
                        @elseif($vendorStatus === 'delivered') bg-green-100 text-green-800
                        @elseif($vendorStatus === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($vendorStatus) }}
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
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-500">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                @if($item->size || $item->color)
                                    <p class="text-sm text-gray-500">
                                        @if($item->size) Size: {{ $item->size }} @endif
                                        @if($item->color) Color: {{ $item->color }} @endif
                                    </p>
                                @endif
                                <!-- Show individual item status -->
                                <p class="text-sm mt-1">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($item->vendor_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($item->vendor_status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($item->vendor_status === 'shipped') bg-indigo-100 text-indigo-800
                                        @elseif($item->vendor_status === 'delivered') bg-green-100 text-green-800
                                        @elseif($item->vendor_status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($item->vendor_status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($item->price, 2) }}</p>
                                <p class="text-sm text-gray-500">${{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Summary -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @if($order->discount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Discount</span>
                                <span class="font-medium text-green-600">-${{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium">${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-base font-medium border-t border-gray-200 pt-2">
                            <span>Total</span>
                            <span>${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping Information -->
            <div class="space-y-6">
                <!-- Customer Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Customer Information</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Name:</span> 
                            <span class="text-gray-600">{{ $order->user->name }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Email:</span> 
                            <span class="text-gray-600">{{ $order->user->email }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Phone:</span> 
                            <span class="text-gray-600">{{ $order->customer_phone ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Shipping Address -->
                @if($order->shippingAddress)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-3">Shipping Address</h3>
                        <div class="text-sm text-gray-600">
                            <p>{{ $order->shippingAddress->full_name }}</p>
                            <p>{{ $order->shippingAddress->address_line1 }}</p>
                            @if($order->shippingAddress->address_line2)
                                <p>{{ $order->shippingAddress->address_line2 }}</p>
                            @endif
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->state }} {{ $order->shippingAddress->postal_code }}</p>
                            <p>{{ $order->shippingAddress->country }}</p>
                        </div>
                    </div>
                @endif

                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Payment Information</h3>
                    <div class="space-y-2">
                        <p class="text-sm">
                            <span class="font-medium">Method:</span> 
                            <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                        </p>
                        <p class="text-sm">
                            <span class="font-medium">Status:</span> 
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @if($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Order Status Update -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Update Order Status</h3>
                    <form id="update-status-form">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="order-status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="pending" {{ $vendorStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $vendorStatus === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $vendorStatus === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $vendorStatus === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $vendorStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Update Status
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Mark as Shipped -->
                @if($vendorStatus === 'processing')
                <div class="bg-white border border-gray-200 rounded-lg p-4 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Mark as Shipped</h3>
                    <form id="ship-order-form">
                        @csrf
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tracking Number</label>
                                <input type="text" name="tracking_number" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter tracking number">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Carrier</label>
                                <input type="text" name="carrier" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter carrier name">
                            </div>
                            <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                                Mark as Shipped
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update Order Status
document.getElementById('update-status-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const status = document.getElementById('order-status').value;
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'Updating...';
    
    try {
        const response = await fetch('{{ route('vendor.orders.update-status', $order) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showNotification('success', data.message);
            // Reload page to see changes
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Failed to update status');
            button.disabled = false;
            button.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'An error occurred. Please try again.');
        button.disabled = false;
        button.textContent = originalText;
    }
});

// Ship Order
document.getElementById('ship-order-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const button = this.querySelector('button[type="submit"]');
    const originalText = button.textContent;
    
    button.disabled = true;
    button.textContent = 'Shipping...';
    
    try {
        const response = await fetch('{{ route('vendor.orders.ship', $order) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('success', data.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('error', data.message || 'Failed to ship order');
            button.disabled = false;
            button.textContent = originalText;
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('error', 'An error occurred. Please try again.');
        button.disabled = false;
        button.textContent = originalText;
    }
});

// Notification function
function showNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
@endsection