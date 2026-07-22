@extends('layouts.admin')

@section('title', 'About Content - Patanyumba Admin')
@section('page_title', 'About Content Management')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900">About Content</h2>
            <p class="text-xs text-gray-500">Manage about page content shown in the mobile app</p>
        </div>
        <a href="{{ route('admin.about.create') }}" class="px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Content
        </a>
    </div>

    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="divide-y">
            @foreach($contents as $item)
            <div class="flex items-start gap-4 p-4 hover:bg-gray-50 transition-all">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wide text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $item->section }}</span>
                        @if($item->is_active)
                        <span class="text-[10px] font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Active</span>
                        @else
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                        <span class="text-[10px] text-gray-400">Order: {{ $item->sort_order }}</span>
                    </div>
                    <p class="text-sm font-bold text-gray-900">{{ $item->title }}</p>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $item->content }}</p>
                    @if($item->stats)
                    <div class="flex gap-2 mt-2">
                        @foreach($item->stats as $stat)
                        <span class="text-[10px] font-semibold text-gray-500 bg-gray-50 px-2 py-1 rounded">{{ $stat['label'] }}: {{ $stat['value'] }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.about.edit', $item) }}" class="text-gray-400 hover:text-emerald-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    <form action="{{ route('admin.about.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this content?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @if($contents->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm text-gray-400">No content yet. Click "Add Content" to create one.</p>
        </div>
        @endif
    </div>

    <div class="mt-4">
        {{ $contents->links() }}
    </div>
</div>
@endsection
