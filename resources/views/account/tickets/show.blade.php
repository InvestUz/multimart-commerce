@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id . ' - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar -->
        @include('account.sidebar')
        
        <!-- Main Content -->
        <div class="md:w-3/4">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Ticket #{{ $ticket->id }}</h1>
                        <p class="text-gray-600">{{ $ticket->subject }}</p>
                    </div>
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                        @if($ticket->status == 'open') bg-yellow-100 text-yellow-800
                        @elseif($ticket->status == 'in_progress') bg-blue-100 text-blue-800
                        @elseif($ticket->status == 'resolved') bg-green-100 text-green-800
                        @elseif($ticket->status == 'closed') bg-gray-100 text-gray-800
                        @else bg-purple-100 text-purple-800 @endif">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Created</p>
                        <p class="font-medium">{{ $ticket->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <p class="font-medium">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($ticket->priority == 'high') bg-red-100 text-red-800
                                @elseif($ticket->priority == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Last Updated</p>
                        <p class="font-medium">{{ $ticket->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
                </div>
            </div>

            <!-- Replies Section -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Replies</h2>

                @if($replies->count() > 0)
                    <div class="space-y-6">
                        @foreach($replies as $reply)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between mb-2">
                                    <div class="font-medium text-gray-900">
                                        {{ $reply->user->name }}
                                        @if(!$reply->is_customer)
                                            <span class="ml-2 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                Support Team
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        {{ $reply->created_at->format('M d, Y H:i') }}
                                    </span>
                                </div>
                                <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No replies yet.</p>
                    </div>
                @endif

                <!-- Reply Form (only if ticket is not closed) -->
                @if($ticket->status != 'closed')
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Add a Reply</h3>
                        <form action="{{ route('account.tickets.reply', $ticket) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <textarea name="message" rows="4" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Type your reply here..." required></textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                                    Send Reply
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection