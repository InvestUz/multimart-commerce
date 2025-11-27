@extends('layouts.vendor')

@section('title', 'Store Settings')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Store Settings</h1>
        <p class="text-gray-600 mt-1">Manage your store information and settings</p>
    </div>

    <!-- Settings Form -->
    <form method="POST" action="{{ route('vendor.store.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Store Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Store Information</h3>

                    <div class="space-y-4">
                        <!-- Shop Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="shop_name"
                                   value="{{ old('shop_name', $vendor->shop_name) }}"
                                   required
                                   placeholder="Your shop name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('shop_name') border-red-500 @enderror">
                            @error('shop_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Shop Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Shop Description
                            </label>
                            <textarea name="shop_description"
                                      rows="4"
                                      placeholder="Tell customers about your shop..."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('shop_description') border-red-500 @enderror">{{ old('shop_description', $vendor->shop_description) }}</textarea>
                            @error('shop_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>

                    <div class="space-y-4">
                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="text"
                                   name="phone"
                                   value="{{ old('phone', $vendor->phone) }}"
                                   placeholder="e.g., +1 (555) 123-4567"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address
                            </label>
                            <input type="text"
                                   name="address"
                                   value="{{ old('address', $vendor->address) }}"
                                   placeholder="Street address"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text"
                                   name="city"
                                   value="{{ old('city', $vendor->city) }}"
                                   placeholder="City"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                State/Province
                            </label>
                            <input type="text"
                                   name="state"
                                   value="{{ old('state', $vendor->state) }}"
                                   placeholder="State or Province"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('state') border-red-500 @enderror">
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code
                            </label>
                            <input type="text"
                                   name="postal_code"
                                   value="{{ old('postal_code', $vendor->postal_code) }}"
                                   placeholder="Postal Code"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <input type="text"
                                   name="country"
                                   value="{{ old('country', $vendor->country) }}"
                                   placeholder="Country"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('country') border-red-500 @enderror">
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Store Logo -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Store Logo</h3>

                    <div class="space-y-4">
                        @if($vendor->shop_logo)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $vendor->shop_logo) }}" 
                                     alt="Store Logo" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <button type="button"
                                        onclick="removeLogo()"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-100 border-2 border-dashed rounded-lg w-full h-32 flex items-center justify-center">
                                <span class="text-gray-500">No logo uploaded</span>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload New Logo
                            </label>
                            <input type="file"
                                   name="shop_logo"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('shop_logo') border-red-500 @enderror">
                            @error('shop_logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Recommended size: 300x300 pixels</p>
                        </div>
                    </div>
                </div>

                <!-- Store Banner -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Store Banner</h3>

                    <div class="space-y-4">
                        @if($vendor->shop_banner)
                            <div class="relative">
                                <img src="{{ asset('storage/' . $vendor->shop_banner) }}" 
                                     alt="Store Banner" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <button type="button"
                                        onclick="removeBanner()"
                                        class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded-full hover:bg-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @else
                            <div class="bg-gray-100 border-2 border-dashed rounded-lg w-full h-32 flex items-center justify-center">
                                <span class="text-gray-500">No banner uploaded</span>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload New Banner
                            </label>
                            <input type="file"
                                   name="shop_banner"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('shop_banner') border-red-500 @enderror">
                            @error('shop_banner')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Recommended size: 1200x300 pixels</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="space-y-4">
                        <button type="submit"
                                class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function removeLogo() {
        if (confirm('Are you sure you want to remove the store logo?')) {
            // Submit a form to remove the logo
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('vendor.store.settings.update') }}';
            form.innerHTML = `
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_logo" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function removeBanner() {
        if (confirm('Are you sure you want to remove the store banner?')) {
            // Submit a form to remove the banner
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('vendor.store.settings.update') }}';
            form.innerHTML = `
                @csrf
                @method('PUT')
                <input type="hidden" name="remove_banner" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection