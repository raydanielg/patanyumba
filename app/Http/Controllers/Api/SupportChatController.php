<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function index(Request $request)
    {
        $chat = SupportChat::where('user_id', $request->user()->id)
            ->where('status', 'open')
            ->with(['messages' => function ($q) {
                $q->orderBy('id');
            }])
            ->first();

        if (!$chat) {
            $chat = SupportChat::create([
                'user_id' => $request->user()->id,
                'subject' => 'General Support',
                'status' => 'open',
                'last_message_at' => now(),
            ]);
        }

        $unreadAdmin = $chat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => $chat,
            'unread_admin_messages' => $unreadAdmin,
        ]);
    }

    public function messages(Request $request, SupportChat $chat)
    {
        if ($request->user()->id !== $chat->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = $chat->messages()->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    public function sendMessage(Request $request, SupportChat $chat)
    {
        if ($request->user()->id !== $chat->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $msg = SupportMessage::create([
            'support_chat_id' => $chat->id,
            'sender_type' => 'user',
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $chat->update(['last_message_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent',
            'data' => $msg,
        ], 201);
    }

    public function markRead(Request $request, SupportChat $chat)
    {
        if ($request->user()->id !== $chat->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $chat->messages()
            ->where('sender_type', 'admin')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read',
        ]);
    }

    public function closeChat(Request $request, SupportChat $chat)
    {
        if ($request->user()->id !== $chat->user_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $chat->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Chat closed',
        ]);
    }
}
