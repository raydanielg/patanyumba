@extends('layouts.admin')

@section('title', 'Support Chat - Patanyumba Admin')
@section('page_title', 'Support Chats')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Support Chats</h2>
            <p class="text-xs text-gray-500">Manage user support conversations</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="divide-y">
            @foreach($chats as $chat)
            <a href="{{ route('admin.support.show', $chat) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-all">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                    {{ strtoupper(substr($chat->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ $chat->user->name ?? 'Unknown User' }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $chat->user->email ?? '' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($chat->status === 'open')
                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Open
                    </span>
                    @else
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Closed</span>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-1">{{ $chat->last_message_at?->diffForHumans() ?? 'No messages' }}</p>
                </div>
                @php($unread = $chat->messages()->where('sender_type', 'user')->where('is_read', false)->count())
                @if($unread > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0">{{ $unread }}</span>
                @endif
            </a>
            @endforeach
        </div>
        @if($chats->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7z"/></svg>
            <p class="text-sm text-gray-400">No support chats yet.</p>
        </div>
        @endif
    </div>

    <div class="mt-4">
        {{ $chats->links() }}
    </div>
</div>
@endsection
