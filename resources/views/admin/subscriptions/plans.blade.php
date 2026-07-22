@extends('layouts.admin')

@section('title', 'Subscription Plans - Patanyumba Admin')
@section('page_title', 'Subscription Plans')

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
$audienceLabels = [
    'house_hunter' => 'House Hunters',
    'agent' => 'Agents',
    'both' => 'Everyone',
];
$audienceIcons = [
    'house_hunter' => '🏠',
    'agent' => '🏢',
    'both' => '👥',
];
$audienceColors = [
    'house_hunter' => 'bg-sky-50 text-sky-700 border-sky-200',
    'agent' => 'bg-purple-50 text-purple-700 border-purple-200',
    'both' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
];
$cycleLabels = [
    'one_time' => 'One Time', 'weekly' => 'Weekly', 'monthly' => 'Monthly',
    'quarterly' => 'Quarterly', 'yearly' => 'Yearly',
];
@endphp

{{-- Header --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="text-sm text-gray-500">Manage subscription plans for house hunters and agents</p>
    </div>
    <button onclick="openSidebar('add')" class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-1.5 shadow-sm transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Plan
    </button>
</div>

{{-- Plans Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6" id="plansGrid">
    @forelse($plans as $plan)
    <div class="bg-white rounded-xl border overflow-hidden hover:shadow-lg transition-all duration-300 flex flex-col" id="plan-{{ $plan->id }}">
        {{-- Top accent bar --}}
        <div class="h-1.5 @if($plan->target_audience==='house_hunter') bg-sky-500 @elseif($plan->target_audience==='agent') bg-purple-500 @else bg-emerald-500 @endif"></div>
        
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] text-gray-400 capitalize">{{ $cycleLabels[$plan->billing_cycle] ?? $plan->billing_cycle }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-medium border {{ $audienceColors[$plan->target_audience] ?? $audienceColors['both'] }}">
                            {{ $audienceIcons[$plan->target_audience] ?? '👥' }}
                            {{ $audienceLabels[$plan->target_audience] ?? 'Everyone' }}
                        </span>
                    </div>
                </div>
                @if($plan->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100" id="active-badge-{{ $plan->id }}">Active</span>
                @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100" id="active-badge-{{ $plan->id }}">Inactive</span>
                @endif
            </div>

            <div class="mb-3">
                <p class="text-2xl font-bold text-gray-900">TZS {{ number_format($plan->price) }}</p>
                @if($plan->unlock_limit)
                <p class="text-xs text-gray-400 mt-1">{{ $plan->unlock_limit }} unlocks included</p>
                @else
                <p class="text-xs text-gray-400 mt-1">Unlimited unlocks</p>
                @endif
            </div>

            @if($plan->description)
            <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $plan->description }}</p>
            @endif

            @if($plan->features && is_array($plan->features))
            <ul class="space-y-1 mb-4 flex-1">
                @foreach(array_slice($plan->features, 0, 4) as $feature)
                <li class="flex items-start gap-1.5 text-xs text-gray-600">
                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $feature }}
                </li>
                @endforeach
                @if(count($plan->features) > 4)
                <li class="text-[10px] text-gray-400 pl-5">+{{ count($plan->features) - 4 }} more</li>
                @endif
            </ul>
            @else
            <div class="flex-1"></div>
            @endif

            <div class="flex items-center gap-2 pt-3 border-t">
                <button onclick="editPlan({{ $plan->id }})" class="flex-1 px-3 py-1.5 text-xs font-medium text-sky-600 bg-sky-50 rounded-lg hover:bg-sky-100 flex items-center justify-center gap-1 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit
                </button>
                <button onclick="togglePlan({{ $plan->id }})" class="flex-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-all" id="toggle-btn-{{ $plan->id }}">
                    @if($plan->is_active)
                    <span class="text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Deactivate
                    </span>
                    @else
                    <span class="text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg flex items-center justify-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activate
                    </span>
                    @endif
                </button>
                <button onclick="deletePlan({{ $plan->id }})" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Delete">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-xl border p-12 text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-sm text-gray-400">No subscription plans yet</p>
            <button onclick="openSidebar('add')" class="text-xs text-emerald-600 font-medium hover:text-emerald-700">Create your first plan →</button>
        </div>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $plans->withQueryString()->links() }}</div>

{{-- Slide-in Sidebar --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden opacity-0 transition-opacity duration-300"></div>
<div id="planSidebar" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 flex flex-col">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-white" id="sidebarTitle">Add Plan</h3>
                <p class="text-xs text-emerald-100/80" id="sidebarSubtitle">Create a new subscription plan</p>
            </div>
        </div>
        <button onclick="closeSidebar()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Form --}}
    <form id="planForm" class="flex-1 overflow-y-auto p-6 space-y-5">
        @csrf
        <input type="hidden" id="editPlanId" value="">
        <input type="hidden" id="formMode" value="add">

        {{-- Plan Name --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Plan Name <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" required placeholder="e.g. Premium Monthly" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
        </div>

        {{-- Slug --}}
        <div id="slugField">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Slug <span class="text-red-500">*</span></label>
            <input type="text" id="slug" name="slug" required placeholder="premium-monthly" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            <p class="text-[10px] text-gray-400 mt-1">Unique identifier (lowercase, hyphens)</p>
        </div>

        {{-- Price + Billing Cycle --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Price (TZS) <span class="text-red-500">*</span></label>
                <input type="number" id="price" name="price" required min="0" step="any" placeholder="50000" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Billing Cycle <span class="text-red-500">*</span></label>
                <select id="billing_cycle" name="billing_cycle" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    @foreach($cycleLabels as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Target Audience --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Target Audience <span class="text-red-500">*</span></label>
            <p class="text-[10px] text-gray-400 mb-2">Who is this plan for?</p>
            <div class="grid grid-cols-3 gap-2">
                <label class="audience-label flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-sky-400 hover:bg-sky-50/50 transition-all" data-audience="house_hunter">
                    <input type="radio" name="target_audience" value="house_hunter" required class="hidden audience-radio">
                    <span class="text-2xl">🏠</span>
                    <span class="text-[10px] font-semibold text-gray-700">House<br>Hunter</span>
                </label>
                <label class="audience-label flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-purple-400 hover:bg-purple-50/50 transition-all" data-audience="agent">
                    <input type="radio" name="target_audience" value="agent" required class="hidden audience-radio">
                    <span class="text-2xl">🏢</span>
                    <span class="text-[10px] font-semibold text-gray-700">Agent</span>
                </label>
                <label class="audience-label flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-emerald-400 hover:bg-emerald-50/50 transition-all" data-audience="both">
                    <input type="radio" name="target_audience" value="both" required class="hidden audience-radio">
                    <span class="text-2xl">👥</span>
                    <span class="text-[10px] font-semibold text-gray-700">Everyone</span>
                </label>
            </div>
        </div>

        {{-- Unlock Limit --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Unlock Limit</label>
            <input type="number" id="unlock_limit" name="unlock_limit" min="0" placeholder="Leave empty for unlimited" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            <p class="text-[10px] text-gray-400 mt-1">Number of property unlocks included</p>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Description</label>
            <textarea id="description" name="description" rows="2" placeholder="Brief description of the plan..." class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all"></textarea>
        </div>

        {{-- Features --}}
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Features</label>
            <p class="text-[10px] text-gray-400 mb-2">One feature per line</p>
            <textarea id="features" name="features" rows="4" placeholder="Unlimited property views&#10;Priority support&#10;Advanced search filters" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all font-mono text-xs"></textarea>
        </div>

        {{-- Errors --}}
        <div id="formErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
    </form>

    {{-- Footer --}}
    <div class="px-6 py-4 border-t bg-gray-50 flex items-center gap-3 flex-shrink-0">
        <button type="button" id="submitBtn" onclick="submitPlan()" class="flex-1 px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center justify-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span id="submitText">Create Plan</span>
        </button>
        <button type="button" onclick="closeSidebar()" class="px-6 py-2.5 text-sm font-medium text-gray-600 bg-white border rounded-lg hover:bg-gray-100 transition-all">Cancel</button>
    </div>
</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-center">
            <div class="w-14 h-14 mx-auto rounded-full bg-red-50 flex items-center justify-center mb-4">
                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900">Delete Plan?</h3>
            <p class="text-sm text-gray-500 mt-1">This subscription plan will be permanently deleted.</p>
        </div>
        <div class="px-6 pb-6 flex items-center gap-3">
            <button id="confirmDeleteBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-red-500 rounded-lg hover:bg-red-600 transition-all">Delete</button>
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
let deletePlanId = null;
let plansData = @json($plans->items());

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden'); toast.classList.add('flex');
    toast.style.transform = 'translateY(0)'; toast.style.opacity = '1';
    toast.className = toast.className.replace(/bg-(emerald|red)-\d+/g, '');
    toast.classList.add(type === 'error' ? 'bg-red-500' : 'bg-emerald-600');
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(-10px)'; setTimeout(() => toast.classList.add('hidden'), 300); }, 3000);
}

// Audience radio highlight
document.querySelectorAll('.audience-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.audience-label').forEach(l => {
            l.classList.remove('border-sky-500', 'bg-sky-50', 'border-purple-500', 'bg-purple-50', 'border-emerald-500', 'bg-emerald-50');
            l.classList.add('border-gray-200');
        });
        const label = this.closest('.audience-label');
        label.classList.remove('border-gray-200');
        const aud = label.dataset.audience;
        if (aud === 'house_hunter') label.classList.add('border-sky-500', 'bg-sky-50');
        else if (aud === 'agent') label.classList.add('border-purple-500', 'bg-purple-50');
        else label.classList.add('border-emerald-500', 'bg-emerald-50');
    });
});

// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    if (document.getElementById('formMode').value === 'add') {
        document.getElementById('slug').value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }
});

// Sidebar
function openSidebar(mode) {
    const overlay = document.getElementById('sidebarOverlay');
    const sidebar = document.getElementById('planSidebar');
    overlay.classList.remove('hidden');
    setTimeout(() => overlay.style.opacity = '1', 10);
    setTimeout(() => sidebar.style.transform = 'translateX(0)', 10);

    if (mode === 'add') {
        document.getElementById('formMode').value = 'add';
        document.getElementById('sidebarTitle').textContent = 'Add Plan';
        document.getElementById('sidebarSubtitle').textContent = 'Create a new subscription plan';
        document.getElementById('submitText').textContent = 'Create Plan';
        document.getElementById('slugField').style.display = 'block';
        document.getElementById('planForm').reset();
        document.getElementById('editPlanId').value = '';
        document.getElementById('formErrors').classList.add('hidden');
        resetAudienceHighlight();
    }
}

function closeSidebar() {
    const overlay = document.getElementById('sidebarOverlay');
    const sidebar = document.getElementById('planSidebar');
    overlay.style.opacity = '0';
    sidebar.style.transform = 'translateX(100%)';
    setTimeout(() => overlay.classList.add('hidden'), 300);
}

function resetAudienceHighlight() {
    document.querySelectorAll('.audience-label').forEach(l => {
        l.classList.remove('border-sky-500', 'bg-sky-50', 'border-purple-500', 'bg-purple-50', 'border-emerald-500', 'bg-emerald-50');
        l.classList.add('border-gray-200');
    });
}

function setAudienceHighlight(audience) {
    resetAudienceHighlight();
    const label = document.querySelector(`.audience-label[data-audience="${audience}"]`);
    if (label) {
        label.classList.remove('border-gray-200');
        if (audience === 'house_hunter') label.classList.add('border-sky-500', 'bg-sky-50');
        else if (audience === 'agent') label.classList.add('border-purple-500', 'bg-purple-50');
        else label.classList.add('border-emerald-500', 'bg-emerald-50');
        const radio = label.querySelector('.audience-radio');
        if (radio) radio.checked = true;
    }
}

// Edit plan - populate sidebar
function editPlan(id) {
    const plan = plansData.find(p => p.id == id);
    if (!plan) { showToast('Plan not found', 'error'); return; }

    document.getElementById('formMode').value = 'edit';
    document.getElementById('editPlanId').value = id;
    document.getElementById('sidebarTitle').textContent = 'Edit Plan';
    document.getElementById('sidebarSubtitle').textContent = 'Update subscription plan';
    document.getElementById('submitText').textContent = 'Save Changes';
    document.getElementById('slugField').style.display = 'none';

    document.getElementById('name').value = plan.name;
    document.getElementById('price').value = plan.price;
    document.getElementById('billing_cycle').value = plan.billing_cycle;
    document.getElementById('unlock_limit').value = plan.unlock_limit || '';
    document.getElementById('description').value = plan.description || '';
    document.getElementById('features').value = plan.features ? (Array.isArray(plan.features) ? plan.features.join('\n') : '') : '';
    setAudienceHighlight(plan.target_audience);

    document.getElementById('formErrors').classList.add('hidden');
    openSidebar('edit');
}

// Submit (Add or Edit)
async function submitPlan() {
    const mode = document.getElementById('formMode').value;
    const form = document.getElementById('planForm');
    const btn = document.getElementById('submitBtn');
    const origText = document.getElementById('submitText').textContent;

    // Validate required fields
    if (!document.getElementById('name').value.trim()) { showToast('Name is required', 'error'); return; }
    if (!document.getElementById('price').value) { showToast('Price is required', 'error'); return; }
    const audience = document.querySelector('input[name="target_audience"]:checked');
    if (!audience) { showToast('Select target audience', 'error'); return; }

    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Saving...';
    btn.disabled = true;

    const formData = new FormData(form);
    const isEdit = mode === 'edit';
    const planId = document.getElementById('editPlanId').value;
    const url = isEdit ? `/admin/subscriptions/plans/${planId}` : '{{ route("admin.subscriptions.plans.store") }}';
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method,
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (res.ok && data.success) {
            showToast(data.message);
            closeSidebar();
            setTimeout(() => location.reload(), 800);
        } else {
            const errs = data.errors ? Object.values(data.errors).flat().join('<br>') : (data.message || 'Error');
            const el = document.getElementById('formErrors');
            el.innerHTML = errs; el.classList.remove('hidden');
        }
    } catch { showToast('Network error', 'error'); }
    btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> ${origText}`;
    btn.disabled = false;
}

// Toggle active/inactive
async function togglePlan(id) {
    try {
        const res = await fetch(`/admin/subscriptions/plans/${id}/toggle`, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const badge = document.getElementById('active-badge-' + id);
            const btn = document.getElementById('toggle-btn-' + id);
            if (data.is_active) {
                badge.textContent = 'Active';
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100';
                btn.innerHTML = '<span class="text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg flex items-center justify-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>Deactivate</span>';
            } else {
                badge.textContent = 'Inactive';
                badge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100';
                btn.innerHTML = '<span class="text-emerald-600 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg flex items-center justify-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>Activate</span>';
            }
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

// Delete
function deletePlan(id) { deletePlanId = id; const m = document.getElementById('deleteModal'); m.classList.remove('hidden'); m.classList.add('flex'); }
function closeDeleteModal() { const m = document.getElementById('deleteModal'); m.classList.add('hidden'); m.classList.remove('flex'); deletePlanId = null; }
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deletePlanId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/subscriptions/plans/${deletePlanId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const card = document.getElementById('plan-' + deletePlanId);
            if (card) { card.style.transition = 'all 0.3s'; card.style.opacity = '0'; card.style.transform = 'scale(0.95)'; setTimeout(() => card.remove(), 300); }
            showToast(data.message); closeDeleteModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

// Close sidebar on overlay click
document.getElementById('sidebarOverlay').addEventListener('click', closeSidebar);

// Close modals on backdrop click
document.querySelectorAll('[id$="Modal"]').forEach(m => {
    m.addEventListener('click', function(e) { if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); } });
});
</script>
@endsection
