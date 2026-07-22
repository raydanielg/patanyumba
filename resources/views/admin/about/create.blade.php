@extends('layouts.admin')

@section('title', 'Create About Content - Patanyumba Admin')
@section('page_title', 'Create About Content')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.about') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Create About Content</h2>
            <p class="text-xs text-gray-500">Add content for the about page</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.about.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Section</label>
                    <select name="section" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="main">Main</option>
                        <option value="mission">Mission</option>
                        <option value="vision">Vision</option>
                        <option value="values">Values</option>
                        <option value="stats">Stats</option>
                        <option value="how_it_works">How It Works</option>
                        <option value="team">Team</option>
                        <option value="contact">Contact</option>
                    </select>
                    <p class="text-[10px] text-gray-400 mt-1">Content is grouped by section in the app</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title</label>
                    <input type="text" name="title" required placeholder="e.g. Our Mission" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Content</label>
                    <textarea name="content" rows="6" placeholder="Write the content here..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all resize-none"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Use \n for line breaks in the app</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Icon (optional)</label>
                        <input type="text" name="icon" placeholder="e.g. home, target, eye" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="0" min="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Image URL (optional)</label>
                    <input type="url" name="image_url" placeholder="https://..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Stats JSON (optional, for stats section)</label>
                    <textarea name="stats" rows="3" placeholder='[{"label":"Users","value":"10,000+","icon":"users"}]' class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all font-mono text-xs"></textarea>
                    <p class="text-[10px] text-gray-400 mt-1">Only used for stats section. Format: array of {label, value, icon}</p>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-600">Active (visible in app)</span>
                    </label>
                </div>
            </div>
            <div class="flex gap-3 pt-6">
                <a href="{{ route('admin.about') }}" class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-center transition-all">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all">Create Content</button>
            </div>
        </form>
    </div>
</div>
@endsection
