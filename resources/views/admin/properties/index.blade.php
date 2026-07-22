@extends('layouts.admin')

@section('title', 'Properties - Patanyumba Admin')
@section('page_title', 'Property Management')

@section('content')
{{-- Session Toast --}}
@if(session('status'))
<div id="sessionToast" class="fixed top-6 right-6 z-50 bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('status') }}
</div>
<script>setTimeout(() => { const t = document.getElementById('sessionToast'); if(t) { t.style.opacity = '0'; t.style.transform = 'translateY(-10px)'; setTimeout(() => t.remove(), 300); } }, 3000);</script>
@endif

{{-- AJAX Toast --}}
<div id="ajaxToast" class="fixed top-6 right-6 z-50 hidden bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="ajaxToastMsg"></span>
</div>

{{-- Header Bar --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, region..." class="w-64 pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','expired'=>'Expired','rented'=>'Rented'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <select name="type" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Types</option>
            @foreach(['apartment'=>'Apartment','house'=>'House','commercial'=>'Commercial','land'=>'Land','studio'=>'Studio','maisonette'=>'Maisonette'] as $val=>$label)
            <option value="{{ $val }}" @if(request('type')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            Filter
        </button>
    </form>
    <div class="flex items-center gap-2">
        <button id="bulkDeleteBtn" onclick="bulkDelete()" class="hidden px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600 items-center gap-1.5 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Delete Selected (<span id="selectedCount">0</span>)
        </button>
        <button onclick="openAddModal()" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-1.5 shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Add Property
        </button>
    </div>
</div>

{{-- Properties Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="propertiesTable">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                </th>
                <th class="px-5 py-3 font-medium">Title</th>
                <th class="px-5 py-3 font-medium">Landlord</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Price</th>
                <th class="px-5 py-3 font-medium">Location</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr></thead>
            <tbody id="propertiesBody">
                @forelse($properties as $property)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="row-{{ $property->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="prop-checkbox w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $property->id }}" onchange="updateBulkBar()">
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            @if($property->is_featured)
                            <svg class="w-3.5 h-3.5 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                            <span class="text-xs font-medium text-gray-900 max-w-xs truncate">{{ $property->title }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-[10px]">
                                {{ strtoupper(substr($property->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="text-xs text-gray-700">{{ $property->user?->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">
                            {{ $property->property_type }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($property->price) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $property->region }}, {{ $property->district }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                            @if($property->status==='approved') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($property->status==='pending') bg-amber-50 text-amber-700 border border-amber-100
                            @elseif($property->status==='rejected') bg-red-50 text-red-700 border border-red-100
                            @else bg-gray-50 text-gray-700 border border-gray-100 @endif" id="status-badge-{{ $property->id }}">
                            {{ $property->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.properties.show', $property) }}" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-all" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            @if($property->status==='pending')
                            <button onclick="approveProperty({{ $property->id }})" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-all" title="Approve">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <button onclick="rejectProperty({{ $property->id }})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Reject">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            @endif
                            <button onclick="toggleFeatured({{ $property->id }}, this)" class="p-1.5 rounded-lg transition-all {{ $property->is_featured ? 'text-amber-500 hover:bg-amber-50' : 'text-gray-300 hover:bg-gray-50' }}" title="{{ $property->is_featured ? 'Unfeature' : 'Feature' }}">
                                <svg class="w-4 h-4" fill="{{ $property->is_featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            </button>
                            <button onclick="deleteProperty({{ $property->id }}, '{{ addslashes($property->title) }}')" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <p class="text-sm text-gray-400">No properties found</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $properties->withQueryString()->links() }}</div>

{{-- Add Property Modal --}}
<div id="addPropertyModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between sticky top-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Add New Property</h3>
                    <p class="text-xs text-emerald-100/80">Create a new property listing</p>
                </div>
            </div>
            <button onclick="closeAddModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addPropertyForm" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="e.g. 3 Bedroom Apartment in Mikocheni" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Landlord <span class="text-red-500">*</span></label>
                    <select name="user_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="">Select landlord...</option>
                        @foreach($landlords as $landlord)
                        <option value="{{ $landlord->id }}">{{ $landlord->name }} ({{ $landlord->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Property Type <span class="text-red-500">*</span></label>
                    <select name="property_type" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        @foreach(['apartment'=>'Apartment','house'=>'House','commercial'=>'Commercial','land'=>'Land','studio'=>'Studio','maisonette'=>'Maisonette'] as $val=>$label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Price (TZS) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" required min="0" placeholder="e.g. 500000" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Region <span class="text-red-500">*</span></label>
                    <input type="text" name="region" required placeholder="e.g. Dar es Salaam" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">District <span class="text-red-500">*</span></label>
                    <input type="text" name="district" required placeholder="e.g. Kinondoni" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ward</label>
                    <input type="text" name="ward" placeholder="e.g. Mikocheni" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bedrooms</label>
                    <input type="number" name="bedrooms" min="0" placeholder="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bathrooms</label>
                    <input type="number" name="bathrooms" min="0" placeholder="0" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Area (sqm)</label>
                    <input type="number" name="area_sqm" min="0" placeholder="e.g. 120" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_furnished" value="1" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm text-gray-700">Furnished</span>
                    </label>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
                    <textarea name="description" rows="3" placeholder="Property description..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
                </div>
            </div>
            <div id="formErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create Property
                </button>
                <button type="button" onclick="closeAddModal()" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Property?</h3>
            <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete <span id="deletePropName" class="font-semibold text-gray-900"></span>? This action cannot be undone.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmDeleteBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete</button>
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

{{-- Bulk Delete Confirmation Modal --}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Multiple Properties?</h3>
            <p class="text-sm text-gray-500 mt-1">You are about to delete <span id="bulkDeleteCount" class="font-bold text-red-500"></span> properties. This action cannot be undone.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button onclick="confirmBulkDelete()" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete All</button>
            <button onclick="closeBulkDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    const msgEl = document.getElementById('ajaxToastMsg');
    msgEl.textContent = msg;
    toast.classList.remove('hidden');
    toast.classList.add('flex');
    toast.style.transform = 'translateY(0)';
    toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : 'bg-emerald-600');
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3000);
}

function openAddModal() {
    const m = document.getElementById('addPropertyModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeAddModal() {
    const m = document.getElementById('addPropertyModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    document.getElementById('addPropertyForm').reset();
    document.getElementById('formErrors').classList.add('hidden');
}

document.getElementById('addPropertyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Creating...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("admin.properties.store") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new FormData(this)
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            closeAddModal();
            setTimeout(() => location.reload(), 800);
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error occurred');
            const el = document.getElementById('formErrors');
            el.innerHTML = errs; el.classList.remove('hidden');
        }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});

let deletePropId = null;
function deleteProperty(id, name) {
    deletePropId = id;
    document.getElementById('deletePropName').textContent = name;
    const m = document.getElementById('deleteModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
    const m = document.getElementById('deleteModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    deletePropId = null;
}
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deletePropId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/properties/${deletePropId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + deletePropId);
            if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            showToast(data.message);
            closeDeleteModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

async function approveProperty(id) {
    try {
        const res = await fetch(`/admin/properties/${id}/approve`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('status-badge-' + id);
            badge.textContent = 'approved';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-emerald-50 text-emerald-700 border border-emerald-100';
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

async function rejectProperty(id) {
    try {
        const res = await fetch(`/admin/properties/${id}/reject`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('status-badge-' + id);
            badge.textContent = 'rejected';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-red-50 text-red-700 border border-red-100';
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

async function toggleFeatured(id, btn) {
    try {
        const res = await fetch(`/admin/properties/${id}/toggle-featured`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const svg = btn.querySelector('svg');
            if (data.is_featured) {
                svg.setAttribute('fill', 'currentColor');
                btn.className = 'p-1.5 rounded-lg transition-all text-amber-500 hover:bg-amber-50';
                btn.title = 'Unfeature';
            } else {
                svg.setAttribute('fill', 'none');
                btn.className = 'p-1.5 rounded-lg transition-all text-gray-300 hover:bg-gray-50';
                btn.title = 'Feature';
            }
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.prop-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.prop-checkbox:checked');
    const count = checked.length;
    const bar = document.getElementById('bulkDeleteBtn');
    document.getElementById('selectedCount').textContent = count;
    if (count > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
}

function bulkDelete() {
    const count = document.querySelectorAll('.prop-checkbox:checked').length;
    if (count === 0) return;
    document.getElementById('bulkDeleteCount').textContent = count;
    const m = document.getElementById('bulkDeleteModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeBulkDeleteModal() {
    const m = document.getElementById('bulkDeleteModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}
async function confirmBulkDelete() {
    const ids = Array.from(document.querySelectorAll('.prop-checkbox:checked')).map(cb => cb.value);
    try {
        const res = await fetch('{{ route("admin.properties.bulk-delete") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids })
        });
        const data = await res.json();
        if (data.success) {
            ids.forEach(id => {
                const row = document.getElementById('row-' + id);
                if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            });
            showToast(data.message);
            closeBulkDeleteModal();
            document.getElementById('bulkDeleteBtn').classList.add('hidden');
            document.getElementById('selectAll').checked = false;
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
}

document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); } });
});
</script>
@endsection
