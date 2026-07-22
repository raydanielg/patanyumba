@extends('layouts.admin')

@section('title', 'Edit About Content - Patanyumba Admin')
@section('page_title', 'Edit About Content')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.about') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Edit About Content</h2>
            <p class="text-xs text-gray-500">Update content details</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.about.update', $about) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Section</label>
                    <select name="section" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        @php $sections = ['main','mission','vision','values','stats','how_it_works','team','contact']; @endphp
                        @foreach($sections as $sec)
                        <option value="{{ $sec }}" {{ $about->section === $sec ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $sec)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title</label>
                    <input type="text" name="title" value="{{ $about->title }}" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Content</label>
                    <textarea name="content" rows="6" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all resize-none">{{ $about->content }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Icon (optional)</label>
                        <input type="text" name="icon" value="{{ $about->icon }}" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ $about->sort_order }}" min="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Image URL (optional)</label>
                    <input type="url" name="image_url" value="{{ $about->image_url }}" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Stats JSON (optional)</label>
                    <textarea name="stats" rows="3" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all font-mono text-xs">{{ $about->stats ? json_encode($about->stats) : '' }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" {{ $about->is_active ? 'checked' : '' }} class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-600">Active (visible in app)</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 pt-6">
                <a href="{{ route('admin.about') }}" class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-center transition-all">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all">Update Content</button>
            </div>
        </form>
    </div>
</div>
@endsection
