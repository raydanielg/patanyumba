<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function index()
    {
        $chats = SupportChat::with('user')
            ->orderBy('last_message_at', 'desc')
            ->paginate(20);

        return view('admin.support.index', compact('chats'));
    }

    public function show(SupportChat $chat)
    {
        $chat->load('user', 'messages');
        $chat->messages()->where('sender_type', 'user')->where('is_read', false)->update(['is_read' => true]);

        return view('admin.support.show', compact('chat'));
    }

    public function reply(Request $request, SupportChat $chat)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        SupportMessage::create([
            'support_chat_id' => $chat->id,
            'sender_type' => 'admin',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $chat->update(['last_message_at' => now()]);

        return redirect()->route('admin.support.show', $chat)->with('success', 'Reply sent.');
    }

    public function closeChat(SupportChat $chat)
    {
        $chat->update(['status' => 'closed']);
        return redirect()->route('admin.support.index')->with('success', 'Chat closed.');
    }
}
