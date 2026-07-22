@extends('layouts.admin')

@section('title', 'Create FAQ - Patanyumba Admin')
@section('page_title', 'Create FAQ')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.faqs') }}" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Create FAQ</h2>
            <p class="text-xs text-gray-500">Add a new frequently asked question</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border p-6">
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Category</label>
                    <input type="text" name="category" required placeholder="e.g. general, properties, subscription" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <p class="text-[10px] text-gray-400 mt-1">FAQs are grouped by category in the app</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Question</label>
                    <input type="text" name="question" required placeholder="e.g. How do I search for properties?" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Answer</label>
                    <textarea name="answer" required rows="5" placeholder="Type the answer here..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sort Order</label>
                        <input type="number" name="sort_order" value="0" min="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status</label>
                        <label class="flex items-center gap-2 mt-3">
                            <input type="checkbox" name="is_active" checked class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm text-gray-600">Active (visible in app)</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex gap-3 pt-6">
                <a href="{{ route('admin.faqs') }}" class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 text-center transition-all">Cancel</a>
                <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all">Create FAQ</button>
            </div>
        </form>
    </div>
</div>
@endsection
