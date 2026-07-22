@extends('layouts.admin')

@section('title', 'Support Chat - Patanyumba Admin')
@section('page_title', 'Support Chat')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.support') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div class="flex-1">
            <h2 class="text-lg font-bold text-gray-900">{{ $chat->user->name ?? 'Unknown' }}</h2>
            <p class="text-xs text-gray-500">{{ $chat->user->email ?? '' }}</p>
        </div>
        @if($chat->status === 'open')
        <form action="{{ route('admin.support.close', $chat) }}" method="POST" onsubmit="return confirm('Close this chat?')">
            @csrf
            <button type="submit" class="px-3 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-all">Close Chat</button>
        </form>
        @else
        <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">Closed</span>
        @endif
    </div>

    {{-- Messages --}}
    <div class="bg-white rounded-xl border p-6 mb-4 max-h-[500px] overflow-y-auto space-y-4">
        @foreach($chat->messages as $msg)
        <div class="flex {{ $msg->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[75%] {{ $msg->sender_type === 'admin' ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-2xl px-4 py-2.5">
                <p class="text-sm">{{ $msg->message }}</p>
                <p class="text-[10px] {{ $msg->sender_type === 'admin' ? 'text-emerald-200' : 'text-gray-400' }} mt-1">{{ $msg->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @endforeach
        @if($chat->messages->isEmpty())
        <div class="text-center py-8">
            <p class="text-sm text-gray-400">No messages yet</p>
        </div>
        @endif
    </div>

    @if($chat->status === 'open')
    {{-- Reply form --}}
    <div class="bg-white rounded-xl border p-4">
        <form action="{{ route('admin.support.reply', $chat) }}" method="POST">
            @csrf
            <div class="flex gap-3">
                <textarea name="message" required rows="2" placeholder="Type your reply..." class="flex-1 px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all resize-none"></textarea>
                <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Send
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
