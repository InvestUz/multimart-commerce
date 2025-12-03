@extends('layouts.app')

@section('title', 'Notifications - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('account.sidebar')
        
        <!-- Main Content -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
                    @if($notifications->count() > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                @if($notifications->count() > 0)
                    <div class="space-y-4">
                        @foreach($notifications as $notification)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 
                                {{ $notification->read_at ? 'bg-gray-50' : 'bg-white border-l-4 border-l-blue-500' }}">
                                <div class="flex justify-between">
                                    <h3 class="font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</h3>
                                    <span class="text-sm text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-2 text-gray-600">
                                    {{ $notification->data['message'] ?? 'You have a new notification.' }}
                                </p>
                                @if(!$notification->read_at)
                                    <div class="mt-3">
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800">
                                                Mark as read
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">You don't have any notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection