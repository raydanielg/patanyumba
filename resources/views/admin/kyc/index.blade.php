@extends('layouts.admin')

@section('title', 'KYC Verification - Patanyumba Admin')
@section('page_title', 'KYC Verification')

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

{{-- Summary Cards --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-400 mb-1">PENDING</p>
                <p class="text-xl font-bold text-amber-600">{{ $documents->where('status','pending')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-400 mb-1">APPROVED</p>
                <p class="text-xl font-bold text-emerald-600">{{ $documents->where('status','approved')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[10px] font-medium text-gray-400 mb-1">REJECTED</p>
                <p class="text-xl font-bold text-red-500">{{ $documents->where('status','rejected')->count() }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Header Bar --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, doc number..." class="w-56 pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Add Document
        </button>
    </div>
</div>

{{-- KYC Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="kycTable">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                </th>
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Document Type</th>
                <th class="px-5 py-3 font-medium">Doc Number</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Submitted</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr></thead>
            <tbody id="kycBody">
                @php
                $docTypeIcons = [
                    'national_id' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/>',
                    'passport' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    'voters_id' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                    'business_license' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'brela_cert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>',
                    'tin_cert' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>',
                    'utility_bill' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>',
                    'title_deed' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>',
                    'sale_agreement' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>',
                    'authorization_letter' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>',
                    'selfie' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>',
                ];
                $docTypeLabels = [
                    'national_id' => 'National ID', 'passport' => 'Passport', 'voters_id' => 'Voter\'s ID',
                    'business_license' => 'Business License', 'brela_cert' => 'BRELA Certificate',
                    'tin_cert' => 'TIN Certificate', 'utility_bill' => 'Utility Bill',
                    'title_deed' => 'Title Deed', 'sale_agreement' => 'Sale Agreement',
                    'authorization_letter' => 'Authorization Letter', 'selfie' => 'Selfie',
                ];
                @endphp
                @forelse($documents as $doc)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="row-{{ $doc->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="kyc-checkbox w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $doc->id }}" onchange="updateBulkBar()">
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                @if($doc->user?->avatar_url)
                                    <img src="{{ $doc->user->avatar_url }}" alt="{{ $doc->user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($doc->user?->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $doc->user?->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $doc->user?->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $docTypeIcons[$doc->document_type] ?? $docTypeIcons['national_id'] !!}</svg>
                            <span class="text-xs text-gray-700">{{ $docTypeLabels[$doc->document_type] ?? str_replace('_', ' ', $doc->document_type) }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $doc->document_number ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                            @if($doc->status==='approved') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($doc->status==='pending') bg-amber-50 text-amber-700 border border-amber-100
                            @else bg-red-50 text-red-700 border border-red-100 @endif" id="status-badge-{{ $doc->id }}">
                            {{ $doc->status }}
                        </span>
                        @if($doc->status==='rejected' && $doc->rejection_reason)
                        <div class="mt-1 text-[9px] text-red-400 max-w-[200px] truncate" title="{{ $doc->rejection_reason }}">{{ $doc->rejection_reason }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="reviewDocument({{ $doc->id }})" class="p-1.5 rounded-lg text-sky-600 hover:bg-sky-50 transition-all" title="Review Document">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            @if($doc->status==='pending')
                            <button onclick="approveDoc({{ $doc->id }})" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-all" title="Approve">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <button onclick="openRejectModal({{ $doc->id }})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Reject">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            @endif
                            <button onclick="deleteDoc({{ $doc->id }})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-gray-400">No KYC documents found</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $documents->withQueryString()->links() }}</div>

{{-- Review / Document Viewer Modal --}}
<div id="reviewModal" class="fixed inset-0 bg-black/60 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full overflow-hidden max-h-[92vh] flex flex-col">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Document Review</h3>
                    <p class="text-xs text-emerald-100/80" id="reviewSubtitle">Reviewing KYC document</p>
                </div>
            </div>
            <button onclick="closeReviewModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Document Viewer --}}
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 overflow-hidden flex items-center justify-center min-h-[400px] relative">
                        <div id="docViewer" class="w-full h-full flex items-center justify-center">
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm text-gray-400">Loading document...</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <a id="docDownloadLink" href="#" target="_blank" class="px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 rounded-lg hover:bg-emerald-100 flex items-center gap-1.5 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download
                        </a>
                        <button id="docZoomIn" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 flex items-center gap-1.5 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                            Zoom In
                        </button>
                        <button id="docZoomOut" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 flex items-center gap-1.5 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
                            Zoom Out
                        </button>
                    </div>
                </div>

                {{-- Document Info --}}
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">User Info</h4>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <div id="reviewAvatar" class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-sm">U</div>
                                <div>
                                    <p id="reviewUserName" class="text-sm font-medium text-gray-900">—</p>
                                    <p id="reviewUserEmail" class="text-[10px] text-gray-400">—</p>
                                </div>
                            </div>
                            <div class="pt-2 border-t">
                                <p class="text-[10px] text-gray-400">KYC Status</p>
                                <p id="reviewUserKyc" class="text-xs font-medium capitalize text-gray-900">—</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-4">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Document Info</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between"><span class="text-gray-400 text-xs">Type</span><span id="reviewDocType" class="font-medium text-gray-900 text-xs">—</span></div>
                            <div class="flex justify-between"><span class="text-gray-400 text-xs">Number</span><span id="reviewDocNumber" class="font-mono text-xs text-gray-900">—</span></div>
                            <div class="flex justify-between"><span class="text-gray-400 text-xs">Status</span><span id="reviewDocStatus" class="font-medium capitalize text-xs text-gray-900">—</span></div>
                            <div class="flex justify-between"><span class="text-gray-400 text-xs">Submitted</span><span id="reviewDocDate" class="text-xs text-gray-900">—</span></div>
                            <div id="reviewDocReviewedRow" class="flex justify-between hidden">
                                <span class="text-gray-400 text-xs">Reviewed</span>
                                <span id="reviewDocReviewed" class="text-xs text-gray-900">—</span>
                            </div>
                        </div>
                        <div id="reviewRejectionBox" class="mt-3 pt-3 border-t hidden">
                            <p class="text-[10px] text-gray-400 mb-1">Rejection Reason</p>
                            <p id="reviewRejectionReason" class="text-xs text-red-600 bg-red-50 rounded-lg p-2"></p>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div id="reviewActions" class="space-y-2">
                        <button onclick="approveDoc(currentReviewId); closeReviewModal();" class="w-full px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center justify-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Approve Document
                        </button>
                        <button onclick="closeReviewModal(); openRejectModal(currentReviewId);" class="w-full px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 flex items-center justify-center gap-2 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Reject Document
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Add Document Modal --}}
<div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Add KYC Document</h3>
                    <p class="text-xs text-emerald-100/80">Upload a new verification document</p>
                </div>
            </div>
            <button onclick="closeAddModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addForm" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">User <span class="text-red-500">*</span></label>
                <select name="user_id" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="">Select user...</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Document Type <span class="text-red-500">*</span></label>
                <select name="document_type" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    @foreach($docTypeLabels as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Document Number</label>
                <input type="text" name="document_number" placeholder="e.g. 12345678" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">File Path <span class="text-red-500">*</span></label>
                <input type="text" name="file_path" required placeholder="e.g. kyc/user_1/national_id.jpg" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                <p class="text-[10px] text-gray-400 mt-1">Enter the file path or upload URL</p>
            </div>
            <div id="formErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Add Document
                </button>
                <button type="button" onclick="closeAddModal()" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Reject Document</h3>
                    <p class="text-xs text-gray-400">Provide a reason for rejection</p>
                </div>
            </div>
            <textarea id="rejectReason" rows="3" required placeholder="e.g. Document is blurry, please upload a clearer copy..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 outline-none transition-all"></textarea>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmRejectBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Reject Document</button>
            <button onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Document?</h3>
            <p class="text-sm text-gray-500 mt-1">This KYC document will be permanently deleted. This action cannot be undone.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmDeleteBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete</button>
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

{{-- Bulk Delete Modal --}}
<div id="bulkDeleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Multiple Documents?</h3>
            <p class="text-sm text-gray-500 mt-1">You are about to delete <span id="bulkDeleteCount" class="font-bold text-red-500"></span> documents. This action cannot be undone.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button onclick="confirmBulkDelete()" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete All</button>
            <button onclick="closeBulkDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let currentReviewId = null;
let deleteDocId = null;
let rejectDocId = null;
let zoomLevel = 1;

const docTypeLabels = {
    national_id: 'National ID', passport: 'Passport', voters_id: "Voter's ID",
    business_license: 'Business License', brela_cert: 'BRELA Certificate',
    tin_cert: 'TIN Certificate', utility_bill: 'Utility Bill',
    title_deed: 'Title Deed', sale_agreement: 'Sale Agreement',
    authorization_letter: 'Authorization Letter', selfie: 'Selfie',
};

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden'); toast.classList.add('flex');
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : 'bg-emerald-600');
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.classList.add('hidden'), 300); }, 3000);
}

// Review Modal
async function reviewDocument(id) {
    currentReviewId = id;
    zoomLevel = 1;
    const modal = document.getElementById('reviewModal');
    modal.classList.remove('hidden'); modal.classList.add('flex');

    try {
        const res = await fetch(`/admin/kyc/${id}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const doc = data.document;
            const user = doc.user;
            document.getElementById('reviewSubtitle').textContent = docTypeLabels[doc.document_type] || doc.document_type;
            document.getElementById('reviewUserName').textContent = user?.name || 'Unknown';
            document.getElementById('reviewUserEmail').textContent = user?.email || '—';
            document.getElementById('reviewUserKyc').textContent = user?.kyc_status || '—';
            document.getElementById('reviewAvatar').textContent = (user?.name || 'U').charAt(0).toUpperCase();
            document.getElementById('reviewDocType').textContent = docTypeLabels[doc.document_type] || doc.document_type;
            document.getElementById('reviewDocNumber').textContent = doc.document_number || '—';
            document.getElementById('reviewDocStatus').textContent = doc.status;
            document.getElementById('reviewDocDate').textContent = new Date(doc.created_at).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

            // Document viewer
            const viewer = document.getElementById('docViewer');
            const ext = doc.file_path.split('.').pop().toLowerCase();
            const fullPath = '/' + doc.file_path;

            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'].includes(ext)) {
                viewer.innerHTML = `<img id="docImage" src="${fullPath}" alt="Document" class="max-w-full max-h-[500px] object-contain transition-transform" style="transform: scale(${zoomLevel})">`;
            } else if (['pdf'].includes(ext)) {
                viewer.innerHTML = `<iframe src="${fullPath}" class="w-full h-[500px] border-0"></iframe>`;
            } else {
                viewer.innerHTML = `<div class="text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><p class="text-sm text-gray-400 mb-2">Preview not available</p><a href="${fullPath}" target="_blank" class="text-xs text-emerald-600 font-medium">Open file</a></div>`;
            }

            document.getElementById('docDownloadLink').href = fullPath;

            // Show/hide actions based on status
            const actions = document.getElementById('reviewActions');
            actions.style.display = doc.status === 'pending' ? 'block' : 'none';
        }
    } catch { showToast('Failed to load document', 'error'); }
}

function closeReviewModal() {
    const m = document.getElementById('reviewModal');
    m.classList.add('hidden'); m.classList.remove('flex');
}

// Zoom
document.getElementById('docZoomIn').addEventListener('click', () => {
    zoomLevel = Math.min(zoomLevel + 0.25, 3);
    const img = document.getElementById('docImage');
    if (img) img.style.transform = `scale(${zoomLevel})`;
});
document.getElementById('docZoomOut').addEventListener('click', () => {
    zoomLevel = Math.max(zoomLevel - 0.25, 0.5);
    const img = document.getElementById('docImage');
    if (img) img.style.transform = `scale(${zoomLevel})`;
});

// Approve (AJAX)
async function approveDoc(id) {
    try {
        const res = await fetch(`/admin/kyc/${id}/approve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('status-badge-' + id);
            badge.textContent = 'approved';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-emerald-50 text-emerald-700 border border-emerald-100';
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

// Reject Modal
function openRejectModal(id) {
    rejectDocId = id;
    document.getElementById('rejectReason').value = '';
    const m = document.getElementById('rejectModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeRejectModal() {
    const m = document.getElementById('rejectModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    rejectDocId = null;
}
document.getElementById('confirmRejectBtn').addEventListener('click', async function() {
    if (!rejectDocId) return;
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) { document.getElementById('rejectReason').focus(); return; }
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/kyc/${rejectDocId}/reject`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ rejection_reason: reason })
        });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('status-badge-' + rejectDocId);
            badge.textContent = 'rejected';
            badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-red-50 text-red-700 border border-red-100';
            showToast(data.message);
            closeRejectModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Reject Document'; this.disabled = false;
});

// Add Modal
function openAddModal() { const m = document.getElementById('addModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeAddModal() { const m = document.getElementById('addModal'); m.classList.add('hidden'); m.classList.remove('flex'); document.getElementById('addForm').reset(); document.getElementById('formErrors').classList.add('hidden'); }

document.getElementById('addForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Adding...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("admin.kyc.store") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new FormData(this) });
        const data = await res.json();
        if (res.ok && data.success) { showToast(data.message); closeAddModal(); setTimeout(() => location.reload(), 800); }
        else { const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error'); const el = document.getElementById('formErrors'); el.innerHTML = errs; el.classList.remove('hidden'); }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});

// Delete
function deleteDoc(id) { deleteDocId = id; const m = document.getElementById('deleteModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeDeleteModal() { const m = document.getElementById('deleteModal'); m.classList.add('hidden'); m.classList.remove('flex'); deleteDocId = null; }
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deleteDocId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/kyc/${deleteDocId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + deleteDocId);
            if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            showToast(data.message); closeDeleteModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

// Select All
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.kyc-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.kyc-checkbox:checked');
    const count = checked.length;
    const bar = document.getElementById('bulkDeleteBtn');
    document.getElementById('selectedCount').textContent = count;
    if (count > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
}

function bulkDelete() {
    const count = document.querySelectorAll('.kyc-checkbox:checked').length;
    if (count === 0) return;
    document.getElementById('bulkDeleteCount').textContent = count;
    const m = document.getElementById('bulkDeleteModal'); m.classList.remove('hidden'); m.classList.add('flex');
}
function closeBulkDeleteModal() { const m = document.getElementById('bulkDeleteModal'); m.classList.add('hidden'); m.classList.remove('flex'); }
async function confirmBulkDelete() {
    const ids = Array.from(document.querySelectorAll('.kyc-checkbox:checked')).map(cb => cb.value);
    try {
        const res = await fetch('{{ route("admin.kyc.bulk-delete") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ ids }) });
        const data = await res.json();
        if (data.success) {
            ids.forEach(id => { const row = document.getElementById('row-' + id); if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); } });
            showToast(data.message); closeBulkDeleteModal();
            document.getElementById('bulkDeleteBtn').classList.add('hidden');
            document.getElementById('selectAll').checked = false;
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
}

// Close modals on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); } });
});
</script>
@endsection
