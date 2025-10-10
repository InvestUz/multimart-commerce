@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold mb-8">My Orders</h1>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search orders..." 
                       class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
            </div>
            <div>
                <select name="status" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                </select>
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-600">
                    <i class="fas fa-search mr-2"></i> Filter
                </button>
                <a href="{{ route('vendor.orders.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Orders List -->
    @if($orderItems->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
            <h2 class="text-2xl font-semibold mb-2">No orders yet</h2>
            <p class="text-gray-600">Orders containing your products will appear here</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orderItems as $item)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">Order #{{ $item->order->order_number }}</h3>
                        <p class="text-sm text-gray-600">Placed on {{ $item->created_at->format('M d, Y h:i A') }}</p>
                        <p class="text-sm text-gray-600">Customer: {{ $item->order->customer_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold
                            @if($item->order->status === 'delivered') bg-green-100 text-green-800
                            @elseif($item->order->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($item->order->status === 'shipped') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($item->order->status) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-4 border-t pt-4">
                    <div class="w-20 h-20 flex-shrink-0">
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

                    <div class="flex-1">
                        <p class="font-semibold">{{ $item->product_name }}</p>
                        <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                        @if($item->size)
                            <p class="text-sm text-gray-600">Size: {{ $item->size }}</p>
                        @endif
                        @if($item->color)
                            <p class="text-sm text-gray-600">Color: {{ $item->color }}</p>
                        @endif
                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                    </div>

                    <div class="text-right">
                        <p class="text-lg font-bold text-primary">${{ number_format($item->total, 2) }}</p>
                        <p class="text-sm text-gray-600">${{ number_format($item->price, 2) }} each</p>
                    </div>
                </div>

                <div class="flex gap-3 mt-4 pt-4 border-t">
                    <a href="{{ route('vendor.orders.show', $item->order) }}" 
                       class="flex-1 text-center border border-primary text-primary py-2 rounded-lg hover:bg-primary hover:text-white transition">
                        View Details
                    </a>
                    
                    @if($item->order->status !== 'cancelled' && $item->order->status !== 'delivered')
                    <button onclick="openStatusModal({{ $item->id }}, '{{ $item->vendor_status }}')" 
                            class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-green-600">
                        Update Status
                    </button>
                    @endif
                </div>

                @if($item->vendor_notes)
                <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600"><strong>Your Notes:</strong> {{ $item->vendor_notes }}</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $orderItems->links() }}
        </div>
    @endif
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Update Order Status</h3>
            <button onclick="closeStatusModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="vendor_status" id="vendor_status" 
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Notes (Optional)</label>
                <textarea name="vendor_notes" rows="3" 
                          class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" 
                          placeholder="Add any notes about this order..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeStatusModal()" 
                        class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-green-600">
                    Update Status
                </button>
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
</script>
@endpush
@endsection

