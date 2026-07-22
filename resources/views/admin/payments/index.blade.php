@extends('layouts.admin')

@section('title', 'Payments - Patanyumba Admin')
@section('page_title', 'Payment Management')

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

@php
$methodLabels = [
    'cash' => 'Cash', 'mpesa' => 'M-Pesa', 'airtel_money' => 'Airtel Money',
    'mixx_yas' => 'Mixx Yas', 'halopesa' => 'HaloPesa', 'tpesa' => 'T-Pesa',
    'visa' => 'Visa', 'mastercard' => 'Mastercard',
];
$methodIcons = [
    'cash' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2-4h10a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm7 5a1 1 0 11-2 0 1 1 0 012 0z"/>',
    'mpesa' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm3 18h4M7 6h10v8H7V6z"/>',
    'airtel_money' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm3 18h4M7 6h10v8H7V6z"/>',
    'mixx_yas' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
    'halopesa' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
    'tpesa' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>',
    'visa' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
    'mastercard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>',
];
$methodColors = [
    'cash' => 'bg-gray-50 text-gray-700 border-gray-200',
    'mpesa' => 'bg-red-50 text-red-700 border-red-200',
    'airtel_money' => 'bg-red-50 text-red-700 border-red-200',
    'mixx_yas' => 'bg-amber-50 text-amber-700 border-amber-200',
    'halopesa' => 'bg-sky-50 text-sky-700 border-sky-200',
    'tpesa' => 'bg-purple-50 text-purple-700 border-purple-200',
    'visa' => 'bg-blue-50 text-blue-700 border-blue-200',
    'mastercard' => 'bg-orange-50 text-orange-700 border-orange-200',
];
@endphp

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">TOTAL REVENUE</span>
            <p class="text-xl font-bold mt-1">TZS {{ number_format($totalRevenue) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl border border-amber-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-amber-50">TRANSACTIONS</span>
            <p class="text-xl font-bold mt-1">{{ number_format($totalTransactions) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl border border-sky-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-sky-100">AVG. TRANSACTION</span>
            <p class="text-xl font-bold mt-1">TZS {{ $totalTransactions > 0 ? number_format($totalRevenue / $totalTransactions) : 0 }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl border border-purple-400 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-purple-100">SUCCESS RATE</span>
            <p class="text-xl font-bold mt-1">{{ $totalTransactions > 0 ? round(Payment::where('status','success')->count() / $totalTransactions * 100) : 0 }}%</p>
        </div>
    </div>
</div>

{{-- Header Bar --}}
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <div class="relative">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tx ID, user..." class="w-52 pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['pending'=>'Pending','success'=>'Success','failed'=>'Failed','refunded'=>'Refunded'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <select name="type" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Types</option>
            @foreach(['unlock'=>'Unlock','subscription'=>'Subscription','featured'=>'Featured','sponsored'=>'Sponsored'] as $val=>$label)
            <option value="{{ $val }}" @if(request('type')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <select name="method" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Methods</option>
            @foreach($methodLabels as $val=>$label)
            <option value="{{ $val }}" @if(request('method')===$val) selected @endif>{{ $label }}</option>
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Payment
        </button>
    </div>
</div>

{{-- Payments Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="paymentsTable">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                </th>
                <th class="px-5 py-3 font-medium">Tx ID</th>
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Method</th>
                <th class="px-5 py-3 font-medium">Amount</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Date</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr></thead>
            <tbody id="paymentsBody">
                @forelse($payments as $payment)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="row-{{ $payment->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="pay-checkbox w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $payment->id }}" onchange="updateBulkBar()">
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ \Str::limit($payment->tx_id, 14) }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full overflow-hidden bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-[10px] flex-shrink-0">
                                @if($payment->user?->avatar_url)
                                    <img src="{{ $payment->user->avatar_url }}" alt="{{ $payment->user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($payment->user?->name ?? 'U', 0, 1)) }}
                                @endif
                            </div>
                            <span class="text-xs text-gray-700">{{ $payment->user?->name ?? 'Unknown' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">{{ $payment->payment_type }}</span></td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $methodColors[$payment->method] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $methodIcons[$payment->method] ?? $methodIcons['cash'] !!}</svg>
                            {{ $methodLabels[$payment->method] ?? str_replace('_', ' ', $payment->method) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($payment->amount) }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                            @if($payment->status==='success') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($payment->status==='pending') bg-amber-50 text-amber-700 border border-amber-100
                            @elseif($payment->status==='refunded') bg-sky-50 text-sky-700 border border-sky-100
                            @else bg-red-50 text-red-700 border border-red-100 @endif" id="status-badge-{{ $payment->id }}">
                            {{ $payment->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="viewPayment({{ $payment->id }})" class="p-1.5 rounded-lg text-sky-600 hover:bg-sky-50 transition-all" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                            <button onclick="openStatusModal({{ $payment->id }}, '{{ $payment->status }}')" class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 transition-all" title="Update Status">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </button>
                            <button onclick="deletePayment({{ $payment->id }})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        <p class="text-sm text-gray-400">No payments found</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $payments->withQueryString()->links() }}</div>

{{-- Add Payment Modal --}}
<div id="addModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between sticky top-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Record Payment</h3>
                    <p class="text-xs text-emerald-100/80">Add a new payment transaction</p>
                </div>
            </div>
            <button onclick="closeAddModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addForm" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Payment Type <span class="text-red-500">*</span></label>
                    <select name="payment_type" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        @foreach(['unlock'=>'Unlock','subscription'=>'Subscription','featured'=>'Featured','sponsored'=>'Sponsored'] as $val=>$label)
                        <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Amount (TZS) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" required min="0" step="any" placeholder="e.g. 50000" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Property (optional)</label>
                    <select name="property_id" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="">None</option>
                        @foreach($properties as $prop)
                        <option value="{{ $prop->id }}">{{ \Str::limit($prop->title, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($methodLabels as $val => $label)
                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-gray-200 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all method-label" data-method="{{ $val }}">
                            <input type="radio" name="method" value="{{ $val }}" required class="w-3.5 h-3.5 text-emerald-600 focus:ring-emerald-500 method-radio">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $methodIcons[$val] !!}</svg>
                            <span class="text-xs font-medium text-gray-700">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Provider Tx ID</label>
                    <input type="text" name="provider_tx_id" placeholder="e.g. MP240123456789" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="success">Success</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>
            <div id="formErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Record Payment
                </button>
                <button type="button" onclick="closeAddModal()" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- View Payment Modal --}}
<div id="viewModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
        <div class="bg-gradient-to-r from-sky-500 to-sky-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Payment Details</h3>
                    <p class="text-xs text-sky-100/80" id="viewTxId">—</p>
                </div>
            </div>
            <button onclick="closeViewModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 space-y-3">
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">User</p><p id="viewUser" class="text-xs font-medium text-gray-900">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Property</p><p id="viewProperty" class="text-xs font-medium text-gray-900">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Type</p><p id="viewType" class="text-xs font-medium text-gray-900 capitalize">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Method</p><p id="viewMethod" class="text-xs font-medium text-gray-900">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Amount</p><p id="viewAmount" class="text-xs font-bold text-emerald-600">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Status</p><p id="viewStatus" class="text-xs font-medium capitalize">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Provider Tx ID</p><p id="viewProviderTx" class="text-xs font-mono text-gray-900">—</p></div>
                <div class="bg-gray-50 rounded-lg p-3"><p class="text-[10px] text-gray-400 mb-0.5">Date</p><p id="viewDate" class="text-xs text-gray-900">—</p></div>
            </div>
        </div>
    </div>
</div>

{{-- Status Update Modal --}}
<div id="statusModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Update Status</h3>
                    <p class="text-xs text-gray-400">Change payment status</p>
                </div>
            </div>
            <select id="statusSelect" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 outline-none transition-all">
                <option value="pending">Pending</option>
                <option value="success">Success</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
            </select>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmStatusBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-amber-500 rounded-lg hover:bg-amber-600 transition-all">Update</button>
            <button onclick="closeStatusModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
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
            <h3 class="text-base font-bold text-gray-900">Delete Payment?</h3>
            <p class="text-sm text-gray-500 mt-1">This payment record will be permanently deleted.</p>
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
            <h3 class="text-base font-bold text-gray-900">Delete Multiple Payments?</h3>
            <p class="text-sm text-gray-500 mt-1">You are about to delete <span id="bulkDeleteCount" class="font-bold text-red-500"></span> payments. This action cannot be undone.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button onclick="confirmBulkDelete()" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete All</button>
            <button onclick="closeBulkDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let deletePayId = null;
let statusPayId = null;

const methodLabels = {
    cash: 'Cash', mpesa: 'M-Pesa', airtel_money: 'Airtel Money',
    mixx_yas: 'Mixx Yas', halopesa: 'HaloPesa', tpesa: 'T-Pesa',
    visa: 'Visa', mastercard: 'Mastercard',
};
const statusClasses = {
    success: 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-emerald-50 text-emerald-700 border border-emerald-100',
    pending: 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-amber-50 text-amber-700 border border-amber-100',
    failed: 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-red-50 text-red-700 border border-red-100',
    refunded: 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-sky-50 text-sky-700 border border-sky-100',
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

// Method radio highlight
document.querySelectorAll('.method-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.method-label').forEach(l => {
            l.classList.remove('border-emerald-500', 'bg-emerald-50');
            l.classList.add('border-gray-200');
        });
        const label = this.closest('.method-label');
        label.classList.remove('border-gray-200');
        label.classList.add('border-emerald-500', 'bg-emerald-50');
    });
});

// Add Modal
function openAddModal() { const m = document.getElementById('addModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeAddModal() { const m = document.getElementById('addModal'); m.classList.add('hidden'); m.classList.remove('flex'); document.getElementById('addForm').reset(); document.getElementById('formErrors').classList.add('hidden'); document.querySelectorAll('.method-label').forEach(l => { l.classList.remove('border-emerald-500', 'bg-emerald-50'); l.classList.add('border-gray-200'); }); }

document.getElementById('addForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Recording...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("admin.payments.store") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new FormData(this) });
        const data = await res.json();
        if (res.ok && data.success) { showToast(data.message); closeAddModal(); setTimeout(() => location.reload(), 800); }
        else { const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error'); const el = document.getElementById('formErrors'); el.innerHTML = errs; el.classList.remove('hidden'); }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = orig; btn.disabled = false;
});

// View Modal
async function viewPayment(id) {
    const m = document.getElementById('viewModal'); m.classList.remove('hidden'); m.classList.add('flex');
    try {
        const res = await fetch(`/admin/payments/${id}`, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const p = data.payment;
            document.getElementById('viewTxId').textContent = p.tx_id;
            document.getElementById('viewUser').textContent = p.user?.name || 'Unknown';
            document.getElementById('viewProperty').textContent = p.property?.title || '—';
            document.getElementById('viewType').textContent = p.payment_type;
            document.getElementById('viewMethod').textContent = methodLabels[p.method] || p.method;
            document.getElementById('viewAmount').textContent = 'TZS ' + Number(p.amount).toLocaleString();
            document.getElementById('viewStatus').textContent = p.status;
            document.getElementById('viewProviderTx').textContent = p.provider_tx_id || '—';
            document.getElementById('viewDate').textContent = new Date(p.created_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }
    } catch { showToast('Failed to load', 'error'); }
}
function closeViewModal() { const m = document.getElementById('viewModal'); m.classList.add('hidden'); m.classList.remove('flex'); }

// Status Modal
function openStatusModal(id, currentStatus) {
    statusPayId = id;
    document.getElementById('statusSelect').value = currentStatus;
    const m = document.getElementById('statusModal'); m.classList.remove('hidden'); m.classList.add('flex');
}
function closeStatusModal() { const m = document.getElementById('statusModal'); m.classList.add('hidden'); m.classList.remove('flex'); statusPayId = null; }
document.getElementById('confirmStatusBtn').addEventListener('click', async function() {
    if (!statusPayId) return;
    const status = document.getElementById('statusSelect').value;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/payments/${statusPayId}/status`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ status }) });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('status-badge-' + statusPayId);
            badge.textContent = status;
            badge.className = statusClasses[status] || statusClasses.pending;
            showToast(data.message); closeStatusModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Update'; this.disabled = false;
});

// Delete
function deletePayment(id) { deletePayId = id; const m = document.getElementById('deleteModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeDeleteModal() { const m = document.getElementById('deleteModal'); m.classList.add('hidden'); m.classList.remove('flex'); deletePayId = null; }
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deletePayId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/payments/${deletePayId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + deletePayId);
            if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            showToast(data.message); closeDeleteModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

// Select All
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.pay-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.pay-checkbox:checked');
    const count = checked.length;
    const bar = document.getElementById('bulkDeleteBtn');
    document.getElementById('selectedCount').textContent = count;
    if (count > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
}

function bulkDelete() {
    const count = document.querySelectorAll('.pay-checkbox:checked').length;
    if (count === 0) return;
    document.getElementById('bulkDeleteCount').textContent = count;
    const m = document.getElementById('bulkDeleteModal'); m.classList.remove('hidden'); m.classList.add('flex');
}
function closeBulkDeleteModal() { const m = document.getElementById('bulkDeleteModal'); m.classList.add('hidden'); m.classList.remove('flex'); }
async function confirmBulkDelete() {
    const ids = Array.from(document.querySelectorAll('.pay-checkbox:checked')).map(cb => cb.value);
    try {
        const res = await fetch('{{ route("admin.payments.bulk-delete") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ ids }) });
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
