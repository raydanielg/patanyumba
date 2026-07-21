@extends('layouts.admin')

@section('title', 'Admin Dashboard - Patanyumba')
@section('page_title', 'Dashboard Overview')

@section('content')
@php
$fmt = fn($n) => $n >= 1000000000 ? number_format($n/1000000000,2).'B' : ($n >= 1000000 ? number_format($n/1000000,2).'M' : ($n >= 1000 ? number_format($n/1000,1).'K' : number_format($n)));
@endphp

{{-- Welcome Banner --}}
<div class="mb-6 bg-gradient-to-br from-emerald-700 to-emerald-900 rounded-xl p-6 text-white relative overflow-hidden">
    <div class="absolute top-0 right-0 w-48 h-48 bg-gold-500/10 rounded-full -mr-16 -mt-16"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-emerald-400/10 rounded-full -ml-12 -mb-12"></div>
    <div class="relative z-10">
        <h2 class="text-xl font-bold">Welcome back, {{ Auth::user()->name ?? 'Admin' }}</h2>
        <p class="text-emerald-200/80 text-sm mt-1">Here's what's happening with Patanyumba today.</p>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Users','value'=>number_format($stats['totalUsers']),'change'=>'+'.$stats['newUsersThisWeek'].' this week','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Total Properties','value'=>number_format($stats['totalProperties']),'change'=>'+'.$stats['newListingsThisWeek'].' this week','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'gold-50','sub'=>'gold-100'],
        ['label'=>'Active Listings','value'=>number_format($stats['activeListings']),'change'=>$stats['pendingApprovals'].' pending approval','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
        ['label'=>'Total Views','value'=>$fmt($stats['totalViews']),'change'=>$fmt($stats['viewsThisWeek']).' this week','icon'=>'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200']
    ] as $card)
    <div class="bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden hover:shadow-lg transition-shadow">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
        </div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Views Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Platform Views</h3>
                <p class="text-xs text-gray-400">Last 14 days</p>
            </div>
        </div>
        @php $viewsMax = max($dailyViews) ?: 1; @endphp
        <div class="flex items-end gap-[4px] h-44">
            @foreach($dailyViews as $i => $views)
            @php $pct = min(100, ($views / $viewsMax) * 100); $isToday = $i === count($dailyViews)-1; @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group cursor-pointer" title="{{ $dailyLabels[$i] }}: {{ number_format($views) }} views">
                <div class="w-full bg-gray-50 rounded-t-md relative h-36 overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 rounded-t-md transition-all duration-300 {{ $isToday ? 'bg-emerald-500' : 'bg-emerald-300 hover:bg-emerald-400' }}" style="height: {{ max($pct, 3) }}%"></div>
                </div>
                <span class="text-[9px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse('now')->subDays(13-$i)->format('d') }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Property Types --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Property Types</h3>
        <div class="space-y-3">
            @foreach([
                ['name'=>'Apartments','total'=>450,'count'=>120,'color'=>'bg-emerald-500'],
                ['name'=>'Houses','total'=>320,'count'=>85,'color'=>'bg-gold-500'],
                ['name'=>'Commercial','total'=>180,'count'=>45,'color'=>'bg-sky-500'],
                ['name'=>'Land','total'=>100,'count'=>30,'color'=>'bg-violet-500'],
            ] as $type)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <p class="text-xs font-medium text-gray-900">{{ $type['name'] }}</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $type['total'] }}</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="{{ $type['color'] }} h-1.5 rounded-full" style="width: {{ ($type['total'] / 1050) * 100 }}%"></div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $type['count'] }} listings</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Listings --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Listings</h3>
            <a href="#" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Title</th>
                    <th class="px-5 py-2.5 font-medium">Location</th>
                    <th class="px-5 py-2.5 font-medium">Price</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($recentListings as $listing)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-2.5 text-xs text-gray-700">{{ $listing->title ?? 'N/A' }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-500">{{ $listing->location ?? 'N/A' }}</td>
                        <td class="px-5 py-2.5 text-xs font-semibold text-gray-900">TSh {{ number_format($listing->price ?? 0) }}</td>
                        <td class="px-5 py-2.5">
                            @if(($listing->status ?? 'active') === 'active')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                            @elseif($listing->status === 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No listings yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Agents --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Top Agents</h3>
            <a href="#" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($topAgents as $agent)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                    {{ strtoupper(substr($agent->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $agent->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">{{ $agent->email ?? '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">{{ $agent->listings_count ?? 0 }}</p>
                    <p class="text-[10px] text-gray-400">listings</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No agents yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
