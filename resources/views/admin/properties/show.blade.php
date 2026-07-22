@extends('layouts.admin')

@section('title', 'Property Details - Patanyumba Admin')
@section('page_title', 'Property Details')

@section('content')
{{-- AJAX Toast --}}
<div id="ajaxToast" class="fixed top-6 right-6 z-50 hidden bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="ajaxToastMsg"></span>
</div>

<div class="mb-4">
    <a href="{{ route('admin.properties') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-emerald-600 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Properties
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Property Info --}}
    <div class="bg-white rounded-xl border p-5">
        <h2 class="text-lg font-bold text-gray-900 mb-1">{{ $property->title }}</h2>
        <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">
                {{ str_replace('_', ' ', $property->property_type) }}
            </span>
            @if($property->listing_type === 'multi_unit')
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">
                {{ $property->total_units }} Units
            </span>
            @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Single Unit</span>
            @endif
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Price</span><span class="font-semibold text-gray-900">TZS {{ number_format($property->price) }}<span class="text-[10px] text-gray-400 font-normal">/{{ $property->rental_period }}</span></span></div>
            @if($property->price_min && $property->price_max)
            <div class="flex justify-between"><span class="text-gray-400">Price Range</span><span class="text-gray-900">{{ number_format($property->price_min) }} - {{ number_format($property->price_max) }}</span></div>
            @endif
            <div class="flex justify-between"><span class="text-gray-400">Region</span><span class="text-gray-900">{{ $property->region }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">District</span><span class="text-gray-900">{{ $property->district }}</span></div>
            @if($property->ward)<div class="flex justify-between"><span class="text-gray-400">Ward</span><span class="text-gray-900">{{ $property->ward }}</span></div>@endif
            @if($property->street)<div class="flex justify-between"><span class="text-gray-400">Street</span><span class="text-gray-900">{{ $property->street }}</span></div>@endif
            @if($property->contact_phone)
            <div class="flex justify-between"><span class="text-gray-400">Contact</span><span class="text-gray-900">{{ $property->contact_phone }}</span></div>
            @endif
            <div class="flex justify-between"><span class="text-gray-400">Bedrooms</span><span class="text-gray-900">{{ $property->bedrooms }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Bathrooms</span><span class="text-gray-900">{{ $property->bathrooms }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Area</span><span class="text-gray-900">{{ $property->area_sqm ? $property->area_sqm . ' sqm' : '—' }}</span></div>
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
            <form action="{{ route('admin.properties.toggle-featured', $property) }}" method="POST">@csrf <button class="px-3 py-1.5 text-xs font-medium {{ $property->is_featured ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-gray-600 bg-gray-100 hover:bg-gray-200' }} rounded-lg">{{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button></form>
        </div>
    </div>

    {{-- Right Column --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Media Management --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900">Media (Images & Videos)</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">{{ $property->images->where('media_type','image')->count() }} imgs, {{ $property->images->where('media_type','video')->count() }} vids</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openUploadModal('image')" class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Add Image
                    </button>
                    <button onclick="openUploadModal('video')" class="px-3 py-1.5 text-xs font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 flex items-center gap-1.5 transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Add Video
                    </button>
                </div>
            </div>
            <div class="p-5">
                @if($property->images->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-sm text-gray-400">No media uploaded yet</p>
                </div>
                @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach($property->images->sortBy('sort_order') as $media)
                    <div class="relative group rounded-lg overflow-hidden border border-gray-100" id="media-{{ $media->id }}">
                        @if($media->media_type === 'video')
                        <div class="aspect-video bg-gray-900 relative">
                            @if($media->thumbnail_url || $media->image_path)
                            <img src="{{ $media->thumbnail_url ? ($media->thumbnail_url) : ($media->image_path) }}" class="w-full h-full object-cover" alt="Video thumbnail">
                            @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center bg-black/30">
                                <div class="w-10 h-10 rounded-full bg-white/90 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-900 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <p class="text-[10px] font-medium text-sky-600 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                Video
                            </p>
                        </div>
                        @else
                        <div class="aspect-video bg-gray-50">
                            <img src="{{ $media->image_path && str_starts_with($media->image_path, 'http') ? $media->image_path : ($media->image_path ? asset('storage/' . $media->image_path) : '') }}" class="w-full h-full object-cover" alt="Property image" onerror="this.src='{{ asset('images/placeholder.png') }}';this.onerror=null;">
                        </div>
                        <div class="p-2 flex items-center justify-between">
                            <p class="text-[10px] font-medium text-gray-600">Image</p>
                            @if($media->is_primary)
                            <span class="text-[9px] px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-600 border border-amber-100 font-medium">Primary</span>
                            @endif
                        </div>
                        @endif
                        <button onclick="deleteMedia({{ $media->id }})" class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600" title="Delete">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Landlord --}}
        <div class="bg-white rounded-xl border p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Landlord</h3>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full overflow-hidden bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-sm flex-shrink-0">
                    @if($property->user?->avatar_url)
                        <img src="{{ $property->user->avatar_url }}" alt="{{ $property->user->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($property->user?->name ?? 'U', 0, 1)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $property->user?->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $property->user?->email }}</p>
                </div>
                @if($property->user?->phone)
                <a href="tel:{{ $property->user->phone }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $property->user->phone }}
                </a>
                @endif
            </div>
        </div>

        {{-- Units Management --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-semibold text-gray-900">Units</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">{{ $property->units->count() }} / {{ $property->total_units }}</span>
                </div>
                @if($property->listing_type === 'multi_unit')
                <button onclick="openAddUnitModal()" class="px-3 py-1.5 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center gap-1.5 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Unit
                </button>
                @endif
            </div>
            <div class="divide-y">
                @forelse($property->units as $unit)
                <div class="p-4 hover:bg-gray-50/50 transition-colors" id="unit-row-{{ $unit->id }}">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr($unit->unit_name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $unit->unit_name }}</p>
                                    @if($unit->unit_number)<span class="text-[10px] text-gray-400">#{{ $unit->unit_number }}</span>@endif
                                    @if($unit->is_available)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-100">Available</span>
                                    @else
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-medium bg-red-50 text-red-600 border border-red-100">Occupied</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3 mt-0.5 text-[10px] text-gray-400">
                                    @if($unit->price)<span class="font-medium text-gray-700">TZS {{ number_format($unit->price) }}</span>@endif
                                    @if($unit->bedrooms)<span>{{ $unit->bedrooms }} bed</span>@endif
                                    @if($unit->bathrooms)<span>{{ $unit->bathrooms }} bath</span>@endif
                                    @if($unit->area_sqm)<span>{{ $unit->area_sqm }} sqm</span>@endif
                                    @if($unit->floor_number)<span>Floor {{ $unit->floor_number }}</span>@endif
                                    @if($unit->is_furnished)<span class="text-emerald-500">Furnished</span>@endif
                                </div>
                                @if($unit->description)
                                <p class="text-[10px] text-gray-400 mt-1 truncate">{{ $unit->description }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button onclick="editUnit({{ $unit->id }})" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-all" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button onclick="deleteUnit({{ $unit->id }}, '{{ addslashes($unit->unit_name) }}')" class="p-1.5 rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center">
                    <svg class="w-10 h-10 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <p class="text-sm text-gray-400">No units added yet</p>
                    @if($property->listing_type === 'multi_unit')
                    <button onclick="openAddUnitModal()" class="mt-3 px-4 py-2 text-xs font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all">Add First Unit</button>
                    @endif
                </div>
                @endforelse
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
                            <svg class="w-3 h-3 {{ $i < $review->rating ? 'text-amber-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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

{{-- Add/Edit Unit Modal --}}
<div id="unitModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-sky-600 to-sky-800 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white" id="unitModalTitle">Add Unit</h3>
                    <p class="text-xs text-sky-100/80">Add a new unit to this property</p>
                </div>
            </div>
            <button onclick="closeUnitModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="unitForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="unit_id" id="unitIdInput" value="">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Name <span class="text-red-500">*</span></label>
                    <input type="text" name="unit_name" required placeholder="e.g. Room 101" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unit Number</label>
                    <input type="text" name="unit_number" placeholder="e.g. 101" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Price (TZS)</label>
                    <input type="number" name="price" min="0" step="1000" placeholder="e.g. 150000" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Floor Number</label>
                    <input type="number" name="floor_number" min="0" placeholder="e.g. 1" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bedrooms</label>
                    <input type="number" name="bedrooms" min="0" placeholder="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bathrooms</label>
                    <input type="number" name="bathrooms" min="0" placeholder="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Area (sqm)</label>
                    <input type="number" name="area_sqm" min="0" placeholder="e.g. 45" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Max Occupants</label>
                    <input type="number" name="max_occupants" min="1" placeholder="e.g. 2" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_furnished" value="1" class="w-4 h-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-gray-700">Furnished</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="2" placeholder="Unit description..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-200 outline-none transition-all"></textarea>
                </div>
            </div>
            <div id="unitFormErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
            <div class="flex items-center gap-3 pt-2 border-t">
                <button type="submit" id="unitSubmitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-sky-600 to-sky-800 rounded-lg hover:from-sky-700 hover:to-sky-900 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span id="unitSubmitText">Add Unit</span>
                </button>
                <button type="button" onclick="closeUnitModal()" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Unit Modal --}}
<div id="deleteUnitModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Unit?</h3>
            <p class="text-sm text-gray-500 mt-1">Delete <span id="deleteUnitName" class="font-semibold text-gray-900"></span>?</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmDeleteUnitBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete</button>
            <button onclick="closeDeleteUnitModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const PROPERTY_ID = {{ $property->id }};

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden'); toast.classList.add('flex');
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : 'bg-emerald-600');
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.classList.add('hidden'), 300); }, 3000);
}

function openAddUnitModal() {
    document.getElementById('unitModalTitle').textContent = 'Add Unit';
    document.getElementById('unitSubmitText').textContent = 'Add Unit';
    document.getElementById('unitIdInput').value = '';
    document.getElementById('unitForm').reset();
    document.getElementById('unitFormErrors').classList.add('hidden');
    const m = document.getElementById('unitModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}

function editUnit(unitId) {
    fetch('/admin/properties/' + PROPERTY_ID + '/units/' + unitId, {
        headers: { 'Accept': 'application/json' }
    }).then(r => r.json()).then(data => {
        if (data.success) {
            const u = data.unit;
            document.getElementById('unitModalTitle').textContent = 'Edit Unit';
            document.getElementById('unitSubmitText').textContent = 'Update Unit';
            document.getElementById('unitIdInput').value = u.id;
            document.querySelector('input[name="unit_name"]').value = u.unit_name;
            document.querySelector('input[name="unit_number"]').value = u.unit_number || '';
            document.querySelector('input[name="price"]').value = u.price || '';
            document.querySelector('input[name="floor_number"]').value = u.floor_number || '';
            document.querySelector('input[name="bedrooms"]').value = u.bedrooms || '';
            document.querySelector('input[name="bathrooms"]').value = u.bathrooms || '';
            document.querySelector('input[name="area_sqm"]').value = u.area_sqm || '';
            document.querySelector('input[name="max_occupants"]').value = u.max_occupants || '';
            document.querySelector('input[name="is_furnished"]').checked = u.is_furnished;
            document.querySelector('textarea[name="description"]').value = u.description || '';
            document.getElementById('unitFormErrors').classList.add('hidden');
            const m = document.getElementById('unitModal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
    }).catch(() => showToast('Failed to load unit', 'error'));
}

function closeUnitModal() {
    const m = document.getElementById('unitModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}

document.getElementById('unitForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('unitSubmitBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Saving...';
    btn.disabled = true;

    const unitId = document.getElementById('unitIdInput').value;
    const isEdit = unitId !== '';
    const url = isEdit
        ? '/admin/properties/' + PROPERTY_ID + '/units/' + unitId
        : '/admin/properties/' + PROPERTY_ID + '/units';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            closeUnitModal();
            setTimeout(() => location.reload(), 800);
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error');
            const el = document.getElementById('unitFormErrors');
            el.innerHTML = errs; el.classList.remove('hidden');
        }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});

let deleteUnitId = null;
function deleteUnit(unitId, name) {
    deleteUnitId = unitId;
    document.getElementById('deleteUnitName').textContent = name;
    const m = document.getElementById('deleteUnitModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteUnitModal() {
    const m = document.getElementById('deleteUnitModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    deleteUnitId = null;
}
document.getElementById('confirmDeleteUnitBtn').addEventListener('click', async function() {
    if (!deleteUnitId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch('/admin/properties/' + PROPERTY_ID + '/units/' + deleteUnitId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('unit-row-' + deleteUnitId);
            if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            showToast(data.message);
            closeDeleteUnitModal();
            setTimeout(() => location.reload(), 1000);
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); } });
});
</script>
@endsection
