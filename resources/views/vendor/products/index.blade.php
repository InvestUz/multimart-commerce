@extends('layouts.app')

@section('title', 'My Products')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">My Products</h1>
            <a href="{{ route('vendor.products.create') }}"
                class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus mr-2"></i> Add New Product
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..."
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <select name="category" id="category_filter"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="sub_category" id="sub_category_filter"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Sub-Categories</option>
                    </select>
                </div>
                <div>
                    <select name="status"
                        class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                    <a href="{{ route('vendor.products.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Table -->
        @if ($products->isEmpty())
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h2 class="text-2xl font-semibold mb-2">No products yet</h2>
                <p class="text-gray-600 mb-6">Start adding products to your store!</p>
                <a href="{{ route('vendor.products.create') }}"
                    class="bg-primary text-white px-6 py-3 rounded-lg inline-block hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i> Add Your First Product
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sub-Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 flex-shrink-0">
                                            @if ($product->primaryImage)
                                                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-cover rounded">
                                            @else
                                                <div
                                                    class="w-full h-full bg-gray-200 rounded flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold truncate">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-600">SKU: {{ $product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    @if ($product->subCategory)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                                            {{ $product->subCategory->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold">${{ number_format($product->price, 2) }}</p>
                                    @if ($product->old_price)
                                        <p class="text-xs text-gray-500 line-through">
                                            ${{ number_format($product->old_price, 2) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full
                                @if ($product->stock > 10) bg-green-100 text-green-800
                                @elseif($product->stock > 0) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                        {{ $product->stock }} units
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('vendor.products.toggle-status', $product) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                            class="px-3 py-1 text-xs rounded-full
                                    {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <p class="font-semibold">{{ $product->total_sales }}</p>
                                    <p class="text-xs text-gray-600">{{ $product->views }} views</p>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('vendor.products.edit', $product) }}"
                                            class="text-green-600 hover:text-green-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('vendor.products.destroy', $product) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Load sub-categories when category changes
            document.getElementById('category_filter').addEventListener('change', function() {
                const categoryId = this.value;
                const subCategorySelect = document.getElementById('sub_category_filter');

                if (!categoryId) {
                    subCategorySelect.innerHTML = '<option value="">All Sub-Categories</option>';
                    return;
                }

                fetch(`/api/sub-categories/by-category/${categoryId}`)
                    .then(res => res.json())
                    .then(subCategories => {
                        subCategorySelect.innerHTML = '<option value="">All Sub-Categories</option>';
                        subCategories.forEach(sub => {
                            const option = document.createElement('option');
                            option.value = sub.id;
                            option.textContent = sub.name;
                            subCategorySelect.appendChild(option);
                        });
                    })
                    .catch(err => console.error('Error loading sub-categories:', err));
            });

            // Load initial sub-categories if category is already selected
            document.addEventListener('DOMContentLoaded', function() {
                const categorySelect = document.getElementById('category_filter');
                if (categorySelect.value) {
                    categorySelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
@endsection
