@extends('layouts.admin')

@section('title', 'Settings - Patanyumba Admin')
@section('page_title', 'System Settings')

@section('content')
@php
$all = [];
foreach ($settings as $group => $items) {
    foreach ($items as $item) { $all[$item->key] = $item->value; }
}
function sv($all, $key, $default = '') { return $all[$key] ?? $default; }
function isOn($all, $key) { return ($all[$key] ?? 'false') === 'true'; }
@endphp

{{-- Toast --}}
<div id="ajaxToast" class="fixed top-6 right-6 z-50 hidden bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="ajaxToastMsg"></span>
</div>
@if(session('status'))
<script>setTimeout(() => { showToast('{{ session("status") }}'); }, 100);</script>
@endif

<div class="max-w-5xl mx-auto">
    {{-- Tab Navigation --}}
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex flex-wrap gap-1 -mb-px" id="tabNav">
            <button onclick="switchTab('general')" data-tab="general" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                General
            </button>
            <button onclick="switchTab('maintenance')" data-tab="maintenance" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.737-.314m-2.845 1.547c.55-.164 1.163-.188 1.737-.314m-2.845 1.547l-.012.014m2.845-1.561l.012.014M9.5 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v7"/></svg>
                Maintenance
            </button>
            <button onclick="switchTab('kyc')" data-tab="kyc" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                KYC
            </button>
            <button onclick="switchTab('features')" data-tab="features" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Features
            </button>
            <button onclick="switchTab('hero')" data-tab="hero" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Hero Images
            </button>
            <button onclick="switchTab('announcements')" data-tab="announcements" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                Announcements
            </button>
            <button onclick="switchTab('notifications')" data-tab="notifications" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Notifications
            </button>
            <button onclick="switchTab('payment')" data-tab="payment" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Payment
            </button>
            <button onclick="switchTab('seo')" data-tab="seo" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                SEO
            </button>
            <button onclick="switchTab('social')" data-tab="social" class="tab-btn px-4 py-2.5 text-xs font-medium border-b-2 transition-all flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                Social
            </button>
        </nav>
    </div>

    {{-- =================== GENERAL TAB =================== --}}
    <div id="tab-general" class="tab-content">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">General Settings</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">App Name</label>
                    <input type="text" data-key="app_name" value="{{ sv($all, 'app_name') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">App Tagline</label>
                    <input type="text" data-key="app_tagline" value="{{ sv($all, 'app_tagline') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">App Description</label>
                    <textarea data-key="app_description" rows="2" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ sv($all, 'app_description') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Support Email</label>
                    <input type="email" data-key="support_email" value="{{ sv($all, 'support_email') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Support Phone</label>
                    <input type="text" data-key="support_phone" value="{{ sv($all, 'support_phone') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Timezone</label>
                    <input type="text" data-key="timezone" value="{{ sv($all, 'timezone') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Default Currency</label>
                    <input type="text" data-key="default_currency" value="{{ sv($all, 'default_currency') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== MAINTENANCE TAB =================== --}}
    <div id="tab-maintenance" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Maintenance Mode</h3>
            </div>

            {{-- Big toggle --}}
            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-5 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Maintenance Mode</p>
                        <p class="text-xs text-gray-500">When enabled, the site shows a maintenance page to visitors</p>
                    </div>
                </div>
                <button onclick="toggleSetting('maintenance_mode', this)" data-key="maintenance_mode" class="toggle-switch relative inline-flex h-7 w-12 items-center rounded-full transition-colors {{ isOn($all, 'maintenance_mode') ? 'bg-amber-500' : 'bg-gray-300' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ isOn($all, 'maintenance_mode') ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Maintenance Message</label>
                <textarea data-key="maintenance_message" rows="3" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ sv($all, 'maintenance_message') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Maintenance Start (optional)</label>
                    <input type="datetime-local" data-key="maintenance_start" value="{{ sv($all, 'maintenance_start') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Maintenance End (optional)</label>
                    <input type="datetime-local" data-key="maintenance_end" value="{{ sv($all, 'maintenance_end') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== KYC TAB =================== --}}
    <div id="tab-kyc" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">KYC Verification Settings</h3>
            </div>

            <div class="space-y-3">
                @php
                $kycToggles = [
                    ['key' => 'kyc_verification_enabled', 'label' => 'KYC Verification', 'desc' => 'Enable/disable the entire KYC verification system'],
                    ['key' => 'kyc_required_for_listing', 'label' => 'Required for Listing', 'desc' => 'Users must complete KYC before listing properties'],
                    ['key' => 'kyc_required_for_contact', 'label' => 'Required for Contact', 'desc' => 'Users must complete KYC before contacting agents'],
                    ['key' => 'kyc_auto_approve', 'label' => 'Auto Approve', 'desc' => 'Automatically approve KYC documents without review'],
                ];
                @endphp
                @foreach($kycToggles as $t)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $t['label'] }}</p>
                        <p class="text-xs text-gray-500">{{ $t['desc'] }}</p>
                    </div>
                    <button onclick="toggleSetting('{{ $t['key'] }}', this)" data-key="{{ $t['key'] }}" class="toggle-switch relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0 {{ isOn($all, $t['key']) ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ isOn($all, $t['key']) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">KYC Expiry (days)</label>
                <input type="number" data-key="kyc_expiry_days" value="{{ sv($all, 'kyc_expiry_days', '365') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                <p class="text-[10px] text-gray-400 mt-1">Number of days before KYC verification expires</p>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== FEATURES TAB =================== --}}
    <div id="tab-features" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Feature Controls</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                $featureToggles = [
                    ['key' => 'property_listing_enabled', 'label' => 'Property Listings', 'desc' => 'Allow property listings', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['key' => 'featured_listings_enabled', 'label' => 'Featured Listings', 'desc' => 'Enable featured property listings', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>'],
                    ['key' => 'sponsored_listings_enabled', 'label' => 'Sponsored Listings', 'desc' => 'Enable sponsored property listings', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>'],
                    ['key' => 'property_unlock_enabled', 'label' => 'Property Unlock', 'desc' => 'Enable paid property unlock feature', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>'],
                    ['key' => 'subscriptions_enabled', 'label' => 'Subscriptions', 'desc' => 'Enable subscription plans', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>'],
                    ['key' => 'user_registration_enabled', 'label' => 'User Registration', 'desc' => 'Allow new user registrations', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'],
                    ['key' => 'agent_registration_enabled', 'label' => 'Agent Registration', 'desc' => 'Allow new agent registrations', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
                    ['key' => 'property_approval_required', 'label' => 'Property Approval', 'desc' => 'Require admin approval for new properties', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ];
                @endphp
                @foreach($featureToggles as $t)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $t['svg'] !!}</svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $t['label'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $t['desc'] }}</p>
                        </div>
                    </div>
                    <button onclick="toggleSetting('{{ $t['key'] }}', this)" data-key="{{ $t['key'] }}" class="toggle-switch relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0 {{ isOn($all, $t['key']) ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ isOn($all, $t['key']) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-4 pt-3 border-t">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Max Properties per Agent</label>
                    <input type="number" data-key="max_properties_per_agent" value="{{ sv($all, 'max_properties_per_agent', '50') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Max Images per Property</label>
                    <input type="number" data-key="max_images_per_property" value="{{ sv($all, 'max_images_per_property', '20') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== HERO IMAGES TAB =================== --}}
    <div id="tab-hero" class="tab-content hidden">
        <div class="space-y-4">
            @for($i = 1; $i <= 5; $i++)
            <div class="bg-white rounded-xl border p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Hero Slide {{ $i }}</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Image</label>
                        <div id="heroPreview{{ $i }}" class="relative rounded-lg overflow-hidden border h-32 bg-gray-100 mb-2 @if(!sv($all, "hero_image_$i")) hidden @endif">
                            <img id="heroImg{{ $i }}" src="{{ sv($all, "hero_image_$i") }}" class="w-full h-full object-cover" alt="Hero {{ $i }}">
                        </div>
                        <div id="heroPlaceholder{{ $i }}" class="rounded-lg border-2 border-dashed border-gray-300 h-32 flex items-center justify-center mb-2 @if(sv($all, "hero_image_$i")) hidden @endif">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[10px] text-gray-400">No image</p>
                            </div>
                        </div>
                        <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="uploadHero({{ $i }}, this)" class="hidden" id="heroFile{{ $i }}">
                        <div class="flex gap-2">
                            <button type="button" onclick="document.getElementById('heroFile{{ $i }}').click()" class="flex-1 px-3 py-2 text-xs font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 flex items-center justify-center gap-1.5 transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload
                            </button>
                            <button type="button" onclick="removeHero({{ $i }})" id="heroRemoveBtn{{ $i }}" class="px-3 py-2 text-xs font-bold text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-all @if(!sv($all, "hero_image_$i")) hidden @endif">Remove</button>
                        </div>
                        <p id="heroUploading{{ $i }}" class="text-[10px] text-emerald-600 mt-1 hidden">Uploading...</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Title</label>
                        <input type="text" data-key="hero_title_{{ $i }}" value="{{ sv($all, "hero_title_$i") }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Subtitle</label>
                        <input type="text" data-key="hero_subtitle_{{ $i }}" value="{{ sv($all, "hero_subtitle_$i") }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                </div>
            </div>
            @endfor
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== ANNOUNCEMENTS TAB =================== --}}
    <div id="tab-announcements" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Announcement Banner</h3>
            </div>

            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-sm font-medium text-gray-900">Enable Announcement</p>
                    <p class="text-xs text-gray-500">Show a banner announcement across the platform</p>
                </div>
                <button onclick="toggleSetting('announcement_enabled', this)" data-key="announcement_enabled" class="toggle-switch relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0 {{ isOn($all, 'announcement_enabled') ? 'bg-emerald-500' : 'bg-gray-300' }}">
                    <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ isOn($all, 'announcement_enabled') ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Announcement Text</label>
                <textarea data-key="announcement_text" rows="2" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ sv($all, 'announcement_text') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Banner Type</label>
                    <select data-key="announcement_type" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        @foreach(['info' => 'Info (Blue)', 'success' => 'Success (Green)', 'warning' => 'Warning (Amber)', 'danger' => 'Danger (Red)'] as $val => $label)
                        <option value="{{ $val }}" @if(sv($all, 'announcement_type') === $val) selected @endif>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link Text</label>
                    <input type="text" data-key="announcement_link_text" value="{{ sv($all, 'announcement_link_text') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Link URL (optional)</label>
                <input type="text" data-key="announcement_link" value="{{ sv($all, 'announcement_link') }}" placeholder="https://..." class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>

            {{-- Preview --}}
            <div>
                <p class="text-xs font-semibold text-gray-700 mb-2">Preview</p>
                @php $annType = sv($all, 'announcement_type', 'info'); @endphp
                <div class="rounded-lg p-3 flex items-center justify-between
                    @if($annType === 'info') bg-sky-50 border border-sky-200
                    @elseif($annType === 'success') bg-emerald-50 border border-emerald-200
                    @elseif($annType === 'warning') bg-amber-50 border border-amber-200
                    @else bg-red-50 border border-red-200 @endif">
                    <p class="text-sm
                        @if($annType === 'info') text-sky-700
                        @elseif($annType === 'success') text-emerald-700
                        @elseif($annType === 'warning') text-amber-700
                        @else text-red-700 @endif">{{ sv($all, 'announcement_text') }}</p>
                    @if(sv($all, 'announcement_link'))
                    <a href="{{ sv($all, 'announcement_link') }}" class="text-xs font-medium underline
                        @if($annType === 'info') text-sky-600
                        @elseif($annType === 'success') text-emerald-600
                        @elseif($annType === 'warning') text-amber-600
                        @else text-red-600 @endif">{{ sv($all, 'announcement_link_text') }}</a>
                    @endif
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== NOTIFICATIONS TAB =================== --}}
    <div id="tab-notifications" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Notification Settings</h3>
            </div>

            <div class="space-y-3">
                @php
                $notifToggles = [
                    ['key' => 'notify_new_registration', 'label' => 'New User Registration', 'desc' => 'Notify when a new user registers', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'],
                    ['key' => 'notify_new_property', 'label' => 'New Property Listed', 'desc' => 'Notify when a new property is listed', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
                    ['key' => 'notify_kyc_submission', 'label' => 'KYC Submission', 'desc' => 'Notify when a user submits KYC documents', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.062-.18-2.087-.514-3.056z"/>'],
                    ['key' => 'notify_payment_received', 'label' => 'Payment Received', 'desc' => 'Notify when a payment is received', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                    ['key' => 'notify_property_report', 'label' => 'Property Reported', 'desc' => 'Notify when a property is reported', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>'],
                    ['key' => 'notify_subscription_expiry', 'label' => 'Subscription Expiry', 'desc' => 'Notify when a subscription is about to expire', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                    ['key' => 'email_notifications_enabled', 'label' => 'Email Notifications', 'desc' => 'Send email notifications in addition to in-app', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
                ];
                @endphp
                @foreach($notifToggles as $t)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white border flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $t['svg'] !!}</svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $t['label'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $t['desc'] }}</p>
                        </div>
                    </div>
                    <button onclick="toggleSetting('{{ $t['key'] }}', this)" data-key="{{ $t['key'] }}" class="toggle-switch relative inline-flex h-7 w-12 items-center rounded-full transition-colors flex-shrink-0 {{ isOn($all, $t['key']) ? 'bg-emerald-500' : 'bg-gray-300' }}">
                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition-transform {{ isOn($all, $t['key']) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                    </button>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- =================== PAYMENT TAB =================== --}}
    <div id="tab-payment" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Payment Settings</h3>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-700 mb-2">Payment Methods</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                    $payMethods = [
                        ['key' => 'mpesa_enabled', 'label' => 'M-Pesa', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm3 18h4M7 6h10v8H7V6z"/>'],
                        ['key' => 'airtel_money_enabled', 'label' => 'Airtel Money', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2zm3 18h4M7 6h10v8H7V6z"/>'],
                        ['key' => 'halopesa_enabled', 'label' => 'HaloPesa', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                        ['key' => 'tpesa_enabled', 'label' => 'T-Pesa', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>'],
                        ['key' => 'mixx_yas_enabled', 'label' => 'Mixx Yas', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                        ['key' => 'card_payment_enabled', 'label' => 'Visa/Mastercard', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>'],
                        ['key' => 'cash_payment_enabled', 'label' => 'Cash', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2-4h10a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6a2 2 0 012-2zm7 5a1 1 0 11-2 0 1 1 0 012 0z"/>'],
                    ];
                    @endphp
                    @foreach($payMethods as $m)
                    <div class="flex flex-col items-center gap-2 p-3 rounded-xl border {{ isOn($all, $m['key']) ? 'border-emerald-300 bg-emerald-50/50' : 'border-gray-200 bg-gray-50' }}">
                        <div class="w-10 h-10 rounded-lg bg-white border flex items-center justify-center">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $m['svg'] !!}</svg>
                        </div>
                        <span class="text-xs font-medium text-gray-700">{{ $m['label'] }}</span>
                        <button onclick="toggleSetting('{{ $m['key'] }}', this)" data-key="{{ $m['key'] }}" class="toggle-switch relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ isOn($all, $m['key']) ? 'bg-emerald-500' : 'bg-gray-300' }}">
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ isOn($all, $m['key']) ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unlock Fee (TZS)</label>
                    <input type="number" data-key="unlock_fee" value="{{ sv($all, 'unlock_fee', '1000') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Featured Fee (TZS)</label>
                    <input type="number" data-key="featured_fee" value="{{ sv($all, 'featured_fee', '15000') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Sponsored Fee (TZS)</label>
                    <input type="number" data-key="sponsored_fee" value="{{ sv($all, 'sponsored_fee', '25000') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== SEO TAB =================== --}}
    <div id="tab-seo" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">SEO Settings</h3>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Meta Keywords</label>
                <input type="text" data-key="meta_keywords" value="{{ sv($all, 'meta_keywords') }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                <p class="text-[10px] text-gray-400 mt-1">Comma-separated keywords</p>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Meta Description</label>
                <textarea data-key="meta_description" rows="2" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ sv($all, 'meta_description') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Google Analytics ID</label>
                    <input type="text" data-key="google_analytics_id" value="{{ sv($all, 'google_analytics_id') }}" placeholder="G-XXXXXXXXXX" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Facebook Pixel ID</label>
                    <input type="text" data-key="facebook_pixel_id" value="{{ sv($all, 'facebook_pixel_id') }}" placeholder="1234567890" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>

    {{-- =================== SOCIAL TAB =================== --}}
    <div id="tab-social" class="tab-content hidden">
        <div class="bg-white rounded-xl border p-6 space-y-5">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">Social Media Links</h3>
            </div>
            <div class="space-y-4">
                @php
                $socials = [
                    ['key' => 'social_facebook', 'label' => 'Facebook', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>', 'placeholder' => 'https://facebook.com/...'],
                    ['key' => 'social_instagram', 'label' => 'Instagram', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>', 'placeholder' => 'https://instagram.com/...'],
                    ['key' => 'social_twitter', 'label' => 'Twitter / X', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>', 'placeholder' => 'https://twitter.com/...'],
                    ['key' => 'social_whatsapp', 'label' => 'WhatsApp', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>', 'placeholder' => '+255 700 000 000'],
                    ['key' => 'social_youtube', 'label' => 'YouTube', 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>', 'placeholder' => 'https://youtube.com/...'],
                ];
                @endphp
                @foreach($socials as $s)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 border flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $s['svg'] !!}</svg>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $s['label'] }}</label>
                        <input type="text" data-key="{{ $s['key'] }}" value="{{ sv($all, $s['key']) }}" placeholder="{{ $s['placeholder'] }}" class="setting-input w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>
                </div>
                @endforeach
            </div>
            @include('admin.settings._save_btn')
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden'); toast.classList.add('flex');
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red|amber)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : type === 'warning' ? 'bg-amber-500' : 'bg-emerald-600');
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.classList.add('hidden'), 300); }, 3000);
}

// Tab switching
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('text-emerald-600', 'border-emerald-500');
        b.classList.add('text-gray-500', 'border-transparent');
    });
    const btn = document.querySelector(`[data-tab="${tab}"]`);
    btn.classList.remove('text-gray-500', 'border-transparent');
    btn.classList.add('text-emerald-600', 'border-emerald-500');
}

// Init first tab
switchTab('general');

// Toggle setting (AJAX)
async function toggleSetting(key, btn) {
    try {
        const res = await fetch('{{ route("admin.settings.toggle") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ key })
        });
        const data = await res.json();
        if (data.success) {
            // Update toggle visual
            if (data.enabled) {
                btn.classList.remove('bg-gray-300', 'bg-amber-500');
                btn.classList.add('bg-emerald-500');
                btn.querySelector('span').classList.remove('translate-x-1');
                btn.querySelector('span').classList.add('translate-x-6');
            } else {
                // Special color for maintenance
                if (key === 'maintenance_mode') {
                    btn.classList.remove('bg-emerald-500');
                    btn.classList.add('bg-gray-300');
                } else {
                    btn.classList.remove('bg-emerald-500');
                    btn.classList.add('bg-gray-300');
                }
                btn.querySelector('span').classList.remove('translate-x-6');
                btn.querySelector('span').classList.add('translate-x-1');
            }
            const msgType = key === 'maintenance_mode' ? (data.enabled ? 'warning' : 'success') : 'success';
            showToast(data.message, msgType);
        }
    } catch { showToast('Network error', 'error'); }
}

// Save settings (AJAX) - collects all inputs in the same tab
document.querySelectorAll('.save-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const container = this.closest('.tab-content');
        const inputs = container.querySelectorAll('.setting-input');
        const data = {};
        inputs.forEach(input => { data[input.dataset.key] = input.value; });

        this.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Saving...';
        this.disabled = true;

        try {
            const res = await fetch('{{ route("admin.settings.update") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const resp = await res.json();
            if (resp.success) { showToast(resp.message); }
            else { showToast(resp.message || 'Failed', 'error'); }
        } catch { showToast('Network error', 'error'); }
        this.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Changes';
        this.disabled = false;
    });
});
</script>
@endsection
