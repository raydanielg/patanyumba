@extends('layouts.admin')

@section('title', 'Property Details - Patanyumba Admin')
@section('page_title', 'Property Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Property Info --}}
    <div class="bg-white rounded-xl border p-5">
        <h2 class="text-lg font-bold text-gray-900 mb-3">{{ $property->title }}</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Type</span><span class="font-medium capitalize text-gray-900">{{ $property->property_type }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Price</span><span class="font-semibold text-gray-900">TZS {{ number_format($property->price) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Region</span><span class="text-gray-900">{{ $property->region }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">District</span><span class="text-gray-900">{{ $property->district }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Bedrooms</span><span class="text-gray-900">{{ $property->bedrooms }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Bathrooms</span><span class="text-gray-900">{{ $property->bathrooms }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Furnished</span><span class="text-gray-900">{{ $property->is_furnished ? 'Yes' : 'No' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Available</span><span class="text-gray-900">{{ $property->is_available ? 'Yes' : 'No' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Views</span><span class="text-gray-900">{{ number_format($property->views_count) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Unlocks</span><span class="text-gray-900">{{ number_format($property->unlock_count) }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="font-medium capitalize text-gray-900">{{ $property->status }}</span></div>
        </div>
        @if($property->description)
        <div class="mt-4 pt-4 border-t">
            <p class="text-xs text-gray-400 mb-1">Description</p>
            <p class="text-sm text-gray-700">{{ $property->description }}</p>
        </div>
        @endif
        <div class="mt-4 flex flex-wrap gap-2">
            @if($property->status==='pending')
            <form action="{{ route('admin.properties.approve', $property) }}" method="POST">@csrf <button class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Approve</button></form>
            <form action="{{ route('admin.properties.reject', $property) }}" method="POST">@csrf <button class="px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Reject</button></form>
            @endif
            <form action="{{ route('admin.properties.toggle-featured', $property) }}" method="POST">@csrf <button class="px-3 py-1.5 text-xs font-medium {{ $property->is_featured ? 'text-gold-700 bg-gold-50 hover:bg-gold-100' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }} rounded-lg">{{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        </div>
    </div>

    {{-- Images & Reviews --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Landlord --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Landlord</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm">
                    {{ strtoupper(substr($property->user?->name ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $property->user?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $property->user?->email }}</p>
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Reviews</h3></div>
            <div class="p-5 space-y-3">
                @forelse($property->reviews as $review)
                <div class="border-b last:border-0 pb-3 last:pb-0">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-xs font-medium text-gray-900">{{ $review->user?->name ?? 'Anonymous' }}</p>
                        <div class="flex items-center gap-1">
                            @for($i = 0; $i < 5; $i++)
                            <svg class="w-3 h-3 {{ $i < $review->rating ? 'text-gold-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-xs text-gray-500">{{ $review->comment ?? 'No comment' }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">No reviews</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
