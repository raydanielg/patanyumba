@extends('layouts.admin')

@section('title', 'Reports - Patanyumba Admin')
@section('page_title', 'Reports & Complaints')

@section('content')
{{-- Filters --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['open'=>'Open','investigating'=>'Investigating','resolved'=>'Resolved','dismissed'=>'Dismissed'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

{{-- Reports Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Reporter</th>
                <th class="px-5 py-3 font-medium">Reason</th>
                <th class="px-5 py-3 font-medium">Description</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Date</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($reports as $report)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $report->reporter?->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">{{ str_replace('_', ' ', $report->reason) }}</span></td>
                    <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $report->description }}</td>
                    <td class="px-5 py-3">
                        @if($report->status==='open')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Open</span>
                        @elseif($report->status==='investigating')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Investigating</span>
                        @elseif($report->status==='resolved')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Resolved</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Dismissed</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $report->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        @if($report->status==='open' || $report->status==='investigating')
                        <div class="flex items-center gap-2">
                            <button onclick="openResolveModal({{ $report->id }})" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Resolve</button>
                            <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST" class="inline">@csrf <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 font-medium">Dismiss</button></form>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">Closed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No reports found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $reports->withQueryString()->links() }}</div>

{{-- Resolve Modal --}}
<div id="resolveModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Resolve Report</h3>
        <form id="resolveForm" method="POST" action="">
            @csrf
            <textarea name="resolution_note" rows="3" required placeholder="Resolution note..." class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none mb-4"></textarea>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Resolve</button>
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResolveModal(id) {
    document.getElementById('resolveForm').action = '/admin/reports/' + id + '/resolve';
    document.getElementById('resolveModal').classList.remove('hidden');
}
</script>
@endsection
