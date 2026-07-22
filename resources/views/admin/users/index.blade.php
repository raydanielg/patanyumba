@extends('layouts.admin')

@section('title', 'Users - Patanyumba Admin')
@section('page_title', 'User Management')

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
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="w-64 pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        </div>
        <select name="role" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Roles</option>
            @foreach(['admin'=>'Admin','landlord'=>'Landlord','agent'=>'Agent','tenant'=>'Tenant'] as $val=>$label)
            <option value="{{ $val }}" @if(request('role')===$val) selected @endif>{{ $label }}</option>
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
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Add User
        </button>
    </div>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="usersTable">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-4 py-3 w-10">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                </th>
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Role</th>
                <th class="px-5 py-3 font-medium">KYC</th>
                <th class="px-5 py-3 font-medium">Phone</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Joined</th>
                <th class="px-5 py-3 font-medium text-right">Actions</th>
            </tr></thead>
            <tbody id="usersBody">
                @forelse($users as $user)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="row-{{ $user->id }}">
                    <td class="px-4 py-3">
                        <input type="checkbox" class="user-checkbox w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" value="{{ $user->id }}" onchange="updateBulkBar()">
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $user->name }}</p>
                                <p class="text-[10px] text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                            @if($user->role==='admin') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($user->role==='landlord') bg-amber-50 text-amber-700 border border-amber-100
                            @elseif($user->role==='agent') bg-sky-50 text-sky-700 border border-sky-100
                            @else bg-gray-50 text-gray-700 border border-gray-100 @endif">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                            @if($user->kyc_status==='approved') bg-emerald-50 text-emerald-700 border border-emerald-100
                            @elseif($user->kyc_status==='pending') bg-amber-50 text-amber-700 border border-amber-100
                            @elseif($user->kyc_status==='rejected') bg-red-50 text-red-700 border border-red-100
                            @else bg-gray-50 text-gray-500 border border-gray-100 @endif">
                            {{ $user->kyc_status }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $user->phone ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <button onclick="toggleStatus({{ $user->id }}, this)" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border transition-all cursor-pointer
                            @if($user->is_active) bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100
                            @else bg-red-50 text-red-700 border-red-100 hover:bg-red-100 @endif">
                            @if($user->is_active) Active @else Suspended @endif
                        </button>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.users.show', $user) }}" class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition-all" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                            <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-all" title="Delete User">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-12 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm text-gray-400">No users found</p>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $users->withQueryString()->links() }}</div>

{{-- Add User Modal --}}
<div id="addUserModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-800 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Add New User</h3>
                    <p class="text-xs text-emerald-100/80">Create a new system user account</p>
                </div>
            </div>
            <button onclick="closeAddModal()" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="addUserForm" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="John Doe" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required placeholder="user@example.com" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Phone</label>
                    <input type="text" name="phone" placeholder="+255 7XX XXX XXX" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="tenant">Tenant</option>
                        <option value="landlord">Landlord</option>
                        <option value="agent">Agent</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimum 6 characters" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <div id="formErrors" class="hidden bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-600"></div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Create User
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
            <h3 class="text-base font-bold text-gray-900">Delete User?</h3>
            <p class="text-sm text-gray-500 mt-1">Are you sure you want to delete <span id="deleteUserName" class="font-semibold text-gray-900"></span>? This action cannot be undone.</p>
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
            <h3 class="text-base font-bold text-gray-900">Delete Multiple Users?</h3>
            <p class="text-sm text-gray-500 mt-1">You are about to delete <span id="bulkDeleteCount" class="font-bold text-red-500"></span> users. This action cannot be undone.</p>
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
    const m = document.getElementById('addUserModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeAddModal() {
    const m = document.getElementById('addUserModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    document.getElementById('addUserForm').reset();
    document.getElementById('formErrors').classList.add('hidden');
}

document.getElementById('addUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const orig = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg> Creating...';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route("admin.users.store") }}', {
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

let deleteUserId = null;
function deleteUser(id, name) {
    deleteUserId = id;
    document.getElementById('deleteUserName').textContent = name;
    const m = document.getElementById('deleteModal');
    m.classList.remove('hidden'); m.classList.add('flex');
}
function closeDeleteModal() {
    const m = document.getElementById('deleteModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    deleteUserId = null;
}
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deleteUserId) return;
    this.innerHTML = '<svg class="w-4 h-4 animate-spin mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path stroke-width="3" d="M4 12a8 8 0 018-8"/></svg>';
    this.disabled = true;
    try {
        const res = await fetch(`/admin/users/${deleteUserId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + deleteUserId);
            if (row) { row.style.transition = 'all 0.3s'; row.style.opacity = '0'; row.style.transform = 'translateX(-20px)'; setTimeout(() => row.remove(), 300); }
            showToast(data.message);
            closeDeleteModal();
        } else { showToast(data.message || 'Failed', 'error'); }
    } catch { showToast('Network error', 'error'); }
    this.innerHTML = 'Delete'; this.disabled = false;
});

async function toggleStatus(id, btn) {
    try {
        const res = await fetch(`/admin/users/${id}/toggle-status`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            if (data.is_active) {
                btn.textContent = 'Active';
                btn.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100 cursor-pointer';
            } else {
                btn.textContent = 'Suspended';
                btn.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border bg-red-50 text-red-700 border-red-100 hover:bg-red-100 cursor-pointer';
            }
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => { cb.checked = this.checked; });
    updateBulkBar();
});

function updateBulkBar() {
    const checked = document.querySelectorAll('.user-checkbox:checked');
    const count = checked.length;
    const bar = document.getElementById('bulkDeleteBtn');
    document.getElementById('selectedCount').textContent = count;
    if (count > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
    else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
}

function bulkDelete() {
    const count = document.querySelectorAll('.user-checkbox:checked').length;
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
    const ids = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
    try {
        const res = await fetch('{{ route("admin.users.bulk-delete") }}', {
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
