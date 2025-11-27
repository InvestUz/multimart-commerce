@extends('layouts.admin')

@section('title', 'Edit Payment Method')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-xl font-semibold text-gray-900">Edit Payment Method</h1>
            <p class="mt-2 text-sm text-gray-700">Update the details for this payment method.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('super-admin.payment-methods.index') }}" 
               class="inline-flex items-center justify-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:w-auto">
                Back to Payment Methods
            </a>
        </div>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="mt-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} errors with your submission</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul role="list" class="list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-8 bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('super-admin.payment-methods.update', $paymentMethod) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <div class="sm:col-span-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" 
                                   value="{{ old('name', $paymentMethod->name) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <div class="mt-1">
                            <input type="text" name="code" id="code" 
                                   value="{{ old('code', $paymentMethod->code) }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Unique identifier for this payment method (e.g., cod, paypal, stripe)</p>
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="3" 
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm">{{ old('description', $paymentMethod->description) }}</textarea>
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Current Image</label>
                        <div class="mt-1">
                            @if($paymentMethod->image_path)
                                <img src="{{ asset('storage/' . $paymentMethod->image_path) }}" 
                                     alt="{{ $paymentMethod->name }}" 
                                     class="h-16 w-16 rounded-md object-cover">
                            @else
                                <div class="h-16 w-16 rounded-md bg-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="sm:col-span-6">
                        <label for="image" class="block text-sm font-medium text-gray-700">Replace Image</label>
                        <div class="mt-1">
                            <input type="file" name="image" id="image" 
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm">
                            <p class="mt-2 text-sm text-gray-500">Upload a new image to represent this payment method (optional)</p>
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <div class="mt-1">
                            <input type="number" name="sort_order" id="sort_order" 
                                   value="{{ old('sort_order', $paymentMethod->sort_order) }}" min="0"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <div class="flex items-start">
                            <div class="flex h-5 items-center">
                                <input id="is_active" name="is_active" type="checkbox" 
                                       {{ old('is_active', $paymentMethod->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 rounded border-gray-300 text-gold-600 focus:ring-gold-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Active</label>
                                <p class="text-gray-500">Enable this payment method for customers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <a href="{{ route('super-admin.payment-methods.index') }}" 
                       class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex justify-center rounded-md border border-transparent bg-gold-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gold-700 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:ring-offset-2">
                        Update Payment Method
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection