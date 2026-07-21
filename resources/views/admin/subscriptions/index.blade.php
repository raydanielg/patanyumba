@extends('layouts.admin')

@section('title', 'Active Subscriptions - Patanyumba Admin')
@section('page_title', 'Active Subscriptions')

@section('content')
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Plan</th>
                <th class="px-5 py-3 font-medium">Starts</th>
                <th class="px-5 py-3 font-medium">Ends</th>
                <th class="px-5 py-3 font-medium">Unlocks Used</th>
                <th class="px-5 py-3 font-medium">Status</th>
            </tr></thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $sub->user?->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $sub->plan?->name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $sub->starts_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $sub->ends_at->format('d M Y') }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $sub->unlocks_used }}{{ $sub->plan?->unlock_limit ? ' / '.$sub->plan->unlock_limit : '' }}</td>
                    <td class="px-5 py-3">
                        @if($sub->status==='active')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                        @elseif($sub->status==='expired')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Expired</span>
                        @elseif($sub->status==='cancelled')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Cancelled</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No subscriptions found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $subscriptions->withQueryString()->links() }}</div>
@endsection
