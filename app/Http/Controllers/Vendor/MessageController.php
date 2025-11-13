<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = auth()->user()->vendorConversations()
            ->with(['user', 'lastMessage'])
            ->latest('updated_at')
            ->paginate(20);

        return view('vendor.messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        if ($conversation->vendor_id !== auth()->id()) {
            abort(403);
        }

        $conversation->load(['user', 'messages.sender']);

        // Mark messages as read
        $conversation->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('vendor.messages.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        if ($conversation->vendor_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $conversation->touch();

        return back()->with('success', 'Message sent successfully!');
    }
}
