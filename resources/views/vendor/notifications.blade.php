@extends('layouts.vendor')

@section('title', 'Notifications')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
                <p class="text-gray-600 mt-1">View all your notifications</p>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">All Notifications</h2>
        </div>
        
        <div id="notifications-container" class="divide-y divide-gray-200">
            <!-- Notifications will be loaded here -->
            <div class="px-6 py-8 text-center">
                <div class="spinner-border animate-spin inline-block w-8 h-8 border-4 rounded-full border-gold-500 border-t-transparent"></div>
                <p class="mt-2 text-gray-500">Loading notifications...</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

function loadNotifications() {
    fetch('/notifications')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('notifications-container');
            
            if (data.notifications.length > 0) {
                container.innerHTML = '';
                
                data.notifications.forEach(notification => {
                    const notificationElement = document.createElement('div');
                    notificationElement.className = 'px-6 py-4 hover:bg-gray-50';
                    notificationElement.innerHTML = `
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                <p class="text-sm text-gray-500 mt-1">${notification.message}</p>
                                <p class="text-xs text-gray-400 mt-2">${formatDate(notification.created_at)}</p>
                            </div>
                        </div>
                    `;
                    container.appendChild(notificationElement);
                });
            } else {
                container.innerHTML = `
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                        <p class="mt-1 text-sm text-gray-500">You're all caught up!</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            const container = document.getElementById('notifications-container');
            container.innerHTML = `
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Failed to load notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">Please try again later.</p>
                </div>
            `;
        });
}

// Format date function
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit', 
        minute: '2-digit' 
    });
}
</script>
@endsection
