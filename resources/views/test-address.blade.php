@extends('layouts.app')

@section('title', 'Test Address')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Test Address Creation</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Add New Address</h2>
        
        <form id="test-address-form">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Label</label>
                <select name="label" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <option value="Home">Home</option>
                    <option value="Work">Work</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="full_name" value="John Doe" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" value="1234567890" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 1</label>
                <input type="text" name="address_line1" value="123 Main St" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                    <input type="text" name="city" value="Test City" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" name="state" value="Test State" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                    <input type="text" name="postal_code" value="12345" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                    <input type="text" name="country" value="Test Country" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600">Set as default address</span>
                </label>
            </div>
            
            <div class="flex items-center justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Add Address
                </button>
            </div>
        </form>
        
        <div id="result" class="mt-4 p-4 hidden"></div>
    </div>
</div>

<script>
document.getElementById('test-address-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route("test.address.create") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('result');
        resultDiv.classList.remove('hidden');
        
        if (data.success) {
            resultDiv.className = 'mt-4 p-4 bg-green-100 text-green-800 rounded';
            resultDiv.innerHTML = `<strong>Success:</strong> ${data.message}`;
        } else {
            resultDiv.className = 'mt-4 p-4 bg-red-100 text-red-800 rounded';
            resultDiv.innerHTML = `<strong>Error:</strong> ${data.message}`;
        }
    })
    .catch(error => {
        const resultDiv = document.getElementById('result');
        resultDiv.classList.remove('hidden');
        resultDiv.className = 'mt-4 p-4 bg-red-100 text-red-800 rounded';
        resultDiv.innerHTML = `<strong>Error:</strong> An unexpected error occurred.`;
        console.error('Error:', error);
    });
});
</script>
@endsection