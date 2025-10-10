
@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <a href="{{ route('orders.index') }}" class="inline-flex items-center text-primary hover:underline mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Back to Orders
    </a>

    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold mb-2">Order #{{ $order->order_number }}</h1>
                <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                @if($order->status === 'delivered') bg-green-100 text-green-800
                @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                @elseif($order->status === 'shipped') bg-blue-100 text-blue-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                {{ ucfirst($order->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Order Items</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex gap-4 pb-4 border-b last:border-b-0">
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
                            @if($item->product)
                                <a href="{{ route('product.show', $item->product->slug) }}" class="font-semibold hover:text-primary">
                                    {{ $item->product_name }}
                                </a>
                            @else
                                <p class="font-semibold">{{ $item->product_name }}</p>
                            @endif
                            
                            <p class="text-sm text-gray-600">by {{ $item->vendor->store_name ?? $item->vendor->name }}</p>
                            
                            @if($item->size)
                                <p class="text-sm text-gray-600">Size: {{ $item->size }}</p>
                            @endif
                            @if($item->color)
                                <p class="text-sm text-gray-600">Color: {{ $item->color }}</p>
                            @endif
                            
                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                            
                            <!-- Review Button if delivered -->
                            @if($order->status === 'delivered' && $item->product)
                                @php
                                    $hasReview = $item->product->reviews()->where('user_id', auth()->id())->exists();
                                @endphp
                                
                                @if(!$hasReview)
                                    <button onclick="openReviewModal({{ $item->product->id }}, '{{ $item->product->name }}')" 
                                            class="text-primary text-sm mt-2 hover:underline">
                                        <i class="fas fa-star mr-1"></i> Write a Review
                                    </button>
                                @else
                                    <p class="text-green-600 text-sm mt-2">
                                        <i class="fas fa-check-circle mr-1"></i> Reviewed
                                    </p>
                                @endif
                            @endif
                        </div>

                        <div class="text-right">
                            <p class="font-semibold">${{ number_format($item->price, 2) }}</p>
                            <p class="text-sm text-gray-600">Total: ${{ number_format($item->total, 2) }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary & Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-semibold">${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    @if($order->discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span class="font-semibold">-${{ number_format($order->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="border-t pt-2 flex justify-between">
                        <span class="text-lg font-bold">Total</span>
                        <span class="text-lg font-bold text-primary">${{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <p class="text-sm text-gray-600 mb-1">Payment Method</p>
                    <p class="font-semibold">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</p>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Customer Information</h2>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-semibold">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Phone</p>
                        <p class="font-semibold">{{ $order->customer_phone }}</p>
                    </div>
                </div>
            </div>

           <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Shipping Address</h2>
                <p class="text-gray-700">{{ $order->shipping_address }}</p>
                @if($order->city)
                    <p class="text-gray-700">{{ $order->city }}, {{ $order->postal_code }}</p>
                @endif
            </div>

            @if($order->notes)
            <!-- Order Notes -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold mb-4">Order Notes</h2>
                <p class="text-gray-700">{{ $order->notes }}</p>
            </div>
            @endif

            @if($order->canBeCancelled())
            <!-- Cancel Order -->
            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" onclick="return confirm('Are you sure you want to cancel this order?')"
                        class="w-full bg-red-500 text-white py-3 rounded-lg hover:bg-red-600 font-semibold">
                    Cancel Order
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold">Write a Review</h3>
            <button onclick="closeReviewModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="reviewForm" onsubmit="submitReview(event)">
            <input type="hidden" id="review_product_id" name="product_id">
            
            <div class="mb-4">
                <p class="font-semibold mb-2" id="review_product_name"></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Rating *</label>
                <div class="flex gap-2">
                    <button type="button" onclick="setRating(1)" class="star-btn text-2xl text-gray-300 hover:text-yellow-400">
                        <i class="fas fa-star"></i>
                    </button>
                    <button type="button" onclick="setRating(2)" class="star-btn text-2xl text-gray-300 hover:text-yellow-400">
                        <i class="fas fa-star"></i>
                    </button>
                    <button type="button" onclick="setRating(3)" class="star-btn text-2xl text-gray-300 hover:text-yellow-400">
                        <i class="fas fa-star"></i>
                    </button>
                    <button type="button" onclick="setRating(4)" class="star-btn text-2xl text-gray-300 hover:text-yellow-400">
                        <i class="fas fa-star"></i>
                    </button>
                    <button type="button" onclick="setRating(5)" class="star-btn text-2xl text-gray-300 hover:text-yellow-400">
                        <i class="fas fa-star"></i>
                    </button>
                </div>
                <input type="hidden" id="review_rating" name="rating" required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Your Review (Optional)</label>
                <textarea name="comment" rows="4" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent" 
                          placeholder="Share your experience with this product..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeReviewModal()" 
                        class="flex-1 border border-gray-300 py-2 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-green-600">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let selectedRating = 0;

function openReviewModal(productId, productName) {
    document.getElementById('review_product_id').value = productId;
    document.getElementById('review_product_name').textContent = productName;
    document.getElementById('reviewModal').classList.remove('hidden');
    selectedRating = 0;
    updateStars();
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.getElementById('reviewForm').reset();
}

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('review_rating').value = rating;
    updateStars();
}

function updateStars() {
    const stars = document.querySelectorAll('.star-btn');
    stars.forEach((star, index) => {
        if (index < selectedRating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
}

function submitReview(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);

    if (!data.rating) {
        alert('Please select a rating');
        return;
    }

    fetch('{{ route("reviews.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert(result.message);
            closeReviewModal();
            location.reload();
        } else {
            alert(result.message);
        }
    })
    .catch(error => {
        alert('Failed to submit review. Please try again.');
    });
}
</script>
@endpush
@endsection