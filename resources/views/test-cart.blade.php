@extends('layouts.app')

@section('title', 'Test Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Test Cart Functionality</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Test Add to Cart</h2>
        
        <form id="test-cart-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product ID</label>
                <input type="number" id="product_id" value="1" min="1" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" id="quantity" value="1" min="1" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Test Add to Cart
            </button>
        </form>
        
        <div id="result" class="mt-4 p-4 rounded-md hidden"></div>
    </div>
</div>

<script>
document.getElementById('test-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('product_id').value;
    const quantity = document.getElementById('quantity').value;
    
    fetch('{{ route("test.cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const resultDiv = document.getElementById('result');
        resultDiv.classList.remove('hidden', 'bg-red-100', 'bg-green-100');
        resultDiv.classList.add(data.success ? 'bg-green-100' : 'bg-red-100');
        resultDiv.innerHTML = '<p class="text-sm font-medium">' + data.message + '</p>';
    })
    .catch(error => {
        const resultDiv = document.getElementById('result');
        resultDiv.classList.remove('hidden', 'bg-red-100', 'bg-green-100');
        resultDiv.classList.add('bg-red-100');
        resultDiv.innerHTML = '<p class="text-sm font-medium">Error: ' + error.message + '</p>';
    });
});
</script>
@endsection