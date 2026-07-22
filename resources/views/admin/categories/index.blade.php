@extends('layouts.admin')

@section('title', 'Categories - Patanyumba Admin')
@section('page_title', 'Property Categories')

@section('content')
@php
$iconMap = [
    'home_work' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'apartment' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5',
    'bed' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0H5m6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1',
    'store' => 'M3 9l2-5h14l2 5M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18M3 9l1.5 3M21 9l-1.5 3M9 21V12h6v9',
    'business' => 'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
    'landscape' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
    'house' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1',
    'villa' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1',
    'warehouse' => 'M3 9l2-5h14l2 5M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18',
    'garage' => 'M3 9l2-5h14l2 5M3 9v11a1 1 0 001 1h16a1 1 0 001-1V9M3 9h18M7 21v-5h10v5',
    'farm' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1',
    'plot' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
];
@endphp

<div id="ajaxToast" class="fixed top-6 right-6 z-50 hidden bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-center gap-2 transition-all duration-300">
    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="ajaxToastMsg"></span>
</div>
@if(session('status'))
<script>setTimeout(() => { showToast('{{ session("status") }}'); }, 100);</script>
@endif

<div class="max-w-5xl mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Categories</h2>
            <p class="text-xs text-gray-500">Manage property categories shown on mobile app</p>
        </div>
        <button type="button" onclick="openModal()" class="px-4 py-2.5 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 rounded-lg hover:from-emerald-700 hover:to-emerald-900 flex items-center gap-2 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Add Category
        </button>
    </div>

    {{-- Categories List --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div id="categoriesList" class="divide-y">
            @foreach($categories as $cat)
            <div class="flex items-center gap-4 p-4 hover:bg-gray-50 transition-all" data-id="{{ $cat->id }}">
                {{-- Drag handle --}}
                <svg class="w-5 h-5 text-gray-300 cursor-grab drag-handle flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>

                {{-- Image / Icon --}}
                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if($cat->image)
                    <img src="{{ $cat->image }}" class="w-full h-full object-cover" alt="{{ $cat->name }}">
                    @elseif(isset($iconMap[$cat->icon]))
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconMap[$cat->icon] }}"/></svg>
                    @else
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    @endif
                </div>

                {{-- Name --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900">{{ $cat->name }}</p>
                    <p class="text-[10px] text-gray-400">Order: {{ $cat->sort_order }}</p>
                </div>

                {{-- Status toggle --}}
                <button onclick="toggleCategory({{ $cat->id }}, this)" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors flex-shrink-0 {{ $cat->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $cat->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>

                {{-- Actions --}}
                <button onclick="editCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->icon }}', '{{ $cat->image }}')" class="text-gray-400 hover:text-emerald-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>
                <button onclick="deleteCategory({{ $cat->id }})" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
            @endforeach
        </div>
        @if($categories->isEmpty())
        <div class="p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            <p class="text-sm text-gray-400">No categories yet. Click "Add Category" to create one.</p>
        </div>
        @endif
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="categoryModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal()"></div>
    <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="modalTitle" class="text-sm font-bold text-gray-900">Add Category</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="categoryForm" onsubmit="submitCategory(event)" enctype="multipart/form-data">
                <input type="hidden" id="categoryId" value="">
                <div class="space-y-4">
                    {{-- Image --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Image (optional)</label>
                        <div id="imgPreview" class="hidden rounded-lg overflow-hidden border h-28 bg-gray-100 mb-2">
                            <img id="imgPreviewSrc" src="" class="w-full h-full object-cover">
                        </div>
                        <div id="imgPlaceholder" class="rounded-lg border-2 border-dashed border-gray-300 h-28 flex items-center justify-center mb-2">
                            <div class="text-center">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p class="text-[10px] text-gray-400">Upload image</p>
                            </div>
                        </div>
                        <input type="file" accept="image/jpeg,image/png,image/jpg,image/webp" id="categoryImage" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('categoryImage').click()" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg hover:bg-emerald-100 transition-all">Choose Image</button>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Name</label>
                        <input type="text" id="categoryName" required placeholder="e.g. Houses" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    </div>

                    {{-- Icon --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Icon (optional)</label>
                        <select id="categoryIcon" class="w-full px-3 py-2.5 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            <option value="">-- Select icon --</option>
                            @foreach($iconMap as $key => $path)
                            <option value="{{ $key }}">{{ ucfirst(str_replace('_', ' ', $key)) }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Icon shows when no image is uploaded</p>
                    </div>
                </div>

                <div class="flex gap-3 pt-5">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" id="submitBtn" class="flex-1 px-4 py-2.5 text-sm font-bold text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-all">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';

function showToast(msg, type = 'success') {
    const toast = document.getElementById('ajaxToast');
    document.getElementById('ajaxToastMsg').textContent = msg;
    toast.classList.remove('hidden', 'bg-emerald-600', 'bg-red-600');
    toast.classList.add(type === 'error' ? 'bg-red-600' : 'bg-emerald-600');
    setTimeout(() => toast.classList.add('hidden'), 3000);
}

function openModal() {
    document.getElementById('modalTitle').textContent = 'Add Category';
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryName').value = '';
    document.getElementById('categoryIcon').value = '';
    document.getElementById('categoryImage').value = '';
    document.getElementById('imgPreview').classList.add('hidden');
    document.getElementById('imgPlaceholder').classList.remove('hidden');
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function editCategory(id, name, icon, image) {
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = name;
    document.getElementById('categoryIcon').value = icon || '';
    if (image) {
        document.getElementById('imgPreviewSrc').src = image;
        document.getElementById('imgPreview').classList.remove('hidden');
        document.getElementById('imgPlaceholder').classList.add('hidden');
    } else {
        document.getElementById('imgPreview').classList.add('hidden');
        document.getElementById('imgPlaceholder').classList.remove('hidden');
    }
    document.getElementById('categoryImage').value = '';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function previewImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('imgPreviewSrc').src = e.target.result;
        document.getElementById('imgPreview').classList.remove('hidden');
        document.getElementById('imgPlaceholder').classList.add('hidden');
    };
    reader.readAsDataURL(file);
}

async function submitCategory(e) {
    e.preventDefault();
    const id = document.getElementById('categoryId').value;
    const btn = document.getElementById('submitBtn');
    btn.innerHTML = 'Saving...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('name', document.getElementById('categoryName').value);
    formData.append('icon', document.getElementById('categoryIcon').value);
    const file = document.getElementById('categoryImage').files[0];
    if (file) formData.append('image', file);

    const url = id ? '{{ route("admin.categories.update", "") }}/' + id : '{{ route("admin.categories.store") }}';
    const method = id ? 'POST' : 'POST';
    if (id) formData.append('_method', 'PUT');

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message);
            closeModal();
            setTimeout(() => location.reload(), 800);
        } else {
            showToast(data.message || 'Failed', 'error');
        }
    } catch {
        showToast('Network error', 'error');
    }
    btn.innerHTML = 'Save';
    btn.disabled = false;
}

async function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    try {
        const res = await fetch('{{ route("admin.categories.destroy", "") }}/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ _method: 'DELETE' })
        });
        const data = await res.json();
        if (data.success) {
            showToast(data.message);
            setTimeout(() => location.reload(), 800);
        }
    } catch { showToast('Network error', 'error'); }
}

async function toggleCategory(id, btn) {
    try {
        const res = await fetch('{{ route("admin.categories.toggle", "") }}/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        });
        const data = await res.json();
        if (data.success) {
            if (data.is_active) {
                btn.classList.remove('bg-gray-300');
                btn.classList.add('bg-emerald-500');
                btn.querySelector('span').classList.remove('translate-x-1');
                btn.querySelector('span').classList.add('translate-x-6');
            } else {
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-gray-300');
                btn.querySelector('span').classList.remove('translate-x-6');
                btn.querySelector('span').classList.add('translate-x-1');
            }
            showToast(data.message);
        }
    } catch { showToast('Network error', 'error'); }
}

// Drag and drop reorder
let dragSrc = null;
document.querySelectorAll('.drag-handle').forEach(handle => {
    handle.addEventListener('dragstart', (e) => {
        dragSrc = e.target.closest('[data-id]');
        dragSrc.style.opacity = '0.5';
    });
    handle.addEventListener('dragend', (e) => {
        if (dragSrc) dragSrc.style.opacity = '1';
    });
    handle.setAttribute('draggable', 'true');
});

document.getElementById('categoriesList').addEventListener('dragover', (e) => {
    e.preventDefault();
    const after = getDragAfterElement(document.getElementById('categoriesList'), e.clientY);
    const dragging = document.querySelector('[data-id][style*="opacity: 0.5"]');
    if (dragging && after) {
        document.getElementById('categoriesList').insertBefore(dragging, after);
    } else if (dragging && !after) {
        document.getElementById('categoriesList').appendChild(dragging);
    }
});

function getDragAfterElement(container, y) {
    const els = [...container.querySelectorAll('[data-id]:not([style*="opacity: 0.5"])')];
    return els.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) {
            return { offset, element: child };
        }
        return closest;
    }, { offset: -Infinity }).element;
}

document.getElementById('categoriesList').addEventListener('drop', async (e) => {
    e.preventDefault();
    const ids = [...document.querySelectorAll('#categoriesList [data-id]')].map(el => parseInt(el.dataset.id));
    try {
        await fetch('{{ route("admin.categories.reorder") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ orders: ids })
        });
        showToast('Order saved');
    } catch { showToast('Failed to save order', 'error'); }
});
</script>
@endsection
