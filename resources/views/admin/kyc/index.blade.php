@extends('layouts.admin')

@section('title', 'KYC Verification - Patanyumba Admin')
@section('page_title', 'KYC Verification')

@section('content')
{{-- Filters --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

{{-- KYC Documents --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Document Type</th>
                <th class="px-5 py-3 font-medium">Doc Number</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Submitted</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($documents as $doc)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                {{ strtoupper(substr($doc->user?->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-900">{{ $doc->user?->name ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-gray-400">{{ $doc->user?->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-700 capitalize">{{ str_replace('_', ' ', $doc->document_type) }}</td>
                    <td class="px-5 py-3 text-xs font-mono text-gray-500">{{ $doc->document_number ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($doc->status==='approved')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                        @elseif($doc->status==='pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Rejected</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $doc->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        @if($doc->status==='pending')
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.kyc.approve', $doc) }}" method="POST" class="inline">@csrf <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Approve</button></form>
                            <button onclick="openRejectModal({{ $doc->id }})" class="text-xs text-red-500 hover:text-red-600 font-medium">Reject</button>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">Reviewed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No KYC documents found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $documents->withQueryString()->links() }}</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Document</h3>
        <form id="rejectForm" method="POST" action="">
            @csrf
            <textarea name="rejection_reason" rows="3" required placeholder="Reason for rejection..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none mb-4"></textarea>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">Reject</button>
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/kyc/' + id + '/reject';
    const modal = document.getElementById('rejectModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
</script>
@endsection
