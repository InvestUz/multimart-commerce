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
        <a href="{{ route('vendor.orders.index') }}" 
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
                        <p class="text-gray-900 mt-1 whitespace-pre-line">{{ $order->shipping_address }}</p>
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

            <!-- Your Order Items -->
            @if($order->items && $order->items->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Your Items in this Order</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                        <div class="flex gap-4">
                            @if($item->product && $item->product->primaryImage)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-20 h-20 object-cover rounded-lg">
                            </div>
                            @endif
                            
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                        <p class="text-sm text-gray-600">SKU: {{ $item->product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">{{ number_format($item->total, 2) }} So'm</p>
                                        <p class="text-sm text-gray-600">{{ number_format($item->price, 2) }} So'm × {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between mt-3 pt-3 border-t">
                                    <div>
                                        <span class="text-sm text-gray-600">Item Status: </span>
                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                                            @if($item->vendor_status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($item->vendor_status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($item->vendor_status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($item->vendor_status === 'delivered') bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($item->vendor_status) }}
                                        </span>
                                    </div>
                                    
                                    @if($item->vendor_status !== 'delivered')
                                    <button onclick="openStatusModal({{ $item->id }}, '{{ $item->vendor_status }}')" 
                                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                        Update Status →
                                    </button>
                                    @endif
                                </div>

                                @if($item->vendor_notes)
                                <div class="mt-2 text-sm">
                                    <span class="text-gray-600">Your Notes:</span>
                                    <p class="text-gray-900 mt-1">{{ $item->vendor_notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Customer Notes -->
            @if($order->notes)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Customer Notes</h2>
                <p class="text-gray-900 whitespace-pre-line">{{ $order->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Overall Order Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Order Status</h2>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Order Status</label>
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

            <!-- Your Earnings -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow-md p-6 border border-green-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Earnings</h2>
                <div class="text-center">
                    <p class="text-sm text-gray-600 mb-2">Total from Your Items</p>
                    <p class="text-4xl font-bold text-green-600">{{ number_format($vendorTotal ?? 0, 2) }} So'm</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
                
                @php
                    $allPending = $order->items->every(fn($item) => $item->vendor_status === 'pending');
                    $allProcessing = $order->items->every(fn($item) => $item->vendor_status === 'processing');
                    $canApprove = $order->items->contains(fn($item) => $item->vendor_status === 'pending');
                    $canShip = $order->items->contains(fn($item) => in_array($item->vendor_status, ['pending', 'processing']));
                @endphp

                <div class="space-y-3">
                    @if($canApprove)
                    <button onclick="bulkUpdateStatus('processing')" 
                            class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        Approve All Pending Items
                    </button>
                    @endif

                    @if($canShip)
                    <button onclick="bulkUpdateStatus('shipped')" 
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-shipping-fast"></i>
                        Mark All as Shipped
                    </button>
                    @endif

                    <button onclick="bulkUpdateStatus('delivered')" 
                            class="w-full px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-box-check"></i>
                        Mark All as Delivered
                    </button>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Timeline</h2>
                
                <div class="space-y-3 text-sm">
                    <div>
                        <label class="font-medium text-gray-600">Order Created:</label>
                        <p class="text-gray-900">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y H:i') }}</p>
                    </div>
                    @if($order->paid_at)
                    <div>
                        <label class="font-medium text-gray-600">Payment Received:</label>
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

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Update Item Status</h3>
            <button onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="vendor_status" id="vendor_status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="vendor_notes" id="vendor_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Add any notes about this item..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeStatusModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Update Status
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openStatusModal(orderItemId, currentStatus) {
    document.getElementById('statusForm').action = `/vendor/orders/${orderItemId}/update-status`;
    document.getElementById('vendor_status').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('statusForm').reset();
}

function bulkUpdateStatus(status) {
    if (!confirm(`Are you sure you want to update all items to "${status}"?`)) {
        return;
    }

    // You'll need to add a bulk update route or update items one by one
    const items = @json($order->items->pluck('id'));
    
    // For now, we'll submit individual requests
    let completed = 0;
    items.forEach(itemId => {
        fetch(`/vendor/orders/${itemId}/update-status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                vendor_status: status,
                vendor_notes: `Bulk updated to ${status}`
            })
        })
        .then(response => response.json())
        .then(data => {
            completed++;
            if (completed === items.length) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeStatusModal();
    }
});

// Close modal on outside click
document.getElementById('statusModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeStatusModal();
    }
});
</script>
@endpush
@endsection