@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Sub-Categories</h1>
        <a href="{{ route('super-admin.sub-categories.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Sub-Category
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $message }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-4 py-2 text-left">Category</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Name</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Icon</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Color</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Order</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Status</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($subCategories as $subCategory)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 px-4 py-2">{{ $subCategory->category->name }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $subCategory->name }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <i class="fas {{ $subCategory->icon }}"></i> {{ $subCategory->icon }}
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="inline-block w-6 h-6 rounded" style="background-color: {{ $subCategory->color }};"></span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2">{{ $subCategory->order }}</td>
                        <td class="border border-gray-300 px-4 py-2">
                            <span class="px-3 py-1 rounded text-sm {{ $subCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $subCategory->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="border border-gray-300 px-4 py-2 text-center">
                            <a href="{{ route('super-admin.sub-categories.show', $subCategory) }}" class="text-blue-500 hover:underline mr-3">View</a>
                            <a href="{{ route('super-admin.sub-categories.edit', $subCategory) }}" class="text-yellow-500 hover:underline mr-3">Edit</a>
                            <form action="{{ route('super-admin.sub-categories.destroy', $subCategory) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="border border-gray-300 px-4 py-2 text-center text-gray-500">No sub-categories found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $subCategories->links() }}
    </div>
</div>
@endsection
