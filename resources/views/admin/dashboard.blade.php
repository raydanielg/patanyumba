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
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold">Welcome back, {{ Auth::user()->name ?? 'Admin' }}</h2>
            <p class="text-emerald-200/80 text-sm mt-1">Here's what's happening with Patanyumba today.</p>
        </div>
        <div class="hidden sm:flex items-center gap-2 text-xs text-emerald-200/60">
            <span class="w-2 h-2 bg-gold-400 rounded-full animate-pulse"></span>
            System operational
        </div>
    </div>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Users','value'=>number_format($stats['totalUsers']),'change'=>'+'.$stats['newUsersThisWeek'].' this week','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Total Revenue','value'=>'TZS '.$fmt((float)$stats['totalRevenue']),'change'=>'This week: TZS '.$fmt((float)$stats['revenueThisWeek']),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'gold-50','sub'=>'gold-100'],
        ['label'=>'Properties','value'=>number_format($stats['totalProperties']),'change'=>'+'.$stats['newPropertiesThisWeek'].' this week','icon'=>'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
        ['label'=>'Success Rate','value'=>$stats['successRate'].'%','change'=>$stats['pendingApprovals'].' pending approvals','icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200']
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

{{-- Quick Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-400 mb-1">LANDLORDS</p>
        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalLandlords']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-400 mb-1">TENANTS</p>
        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalTenants']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-400 mb-1">AGENTS</p>
        <p class="text-lg font-bold text-gray-900">{{ number_format($stats['totalAgents']) }}</p>
    </div>
    <div class="bg-white rounded-xl border p-4">
        <p class="text-[10px] font-medium text-gray-400 mb-1">PENDING KYC</p>
        <p class="text-lg font-bold text-amber-600">{{ number_format($stats['pendingKyc']) }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Revenue Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border p-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Revenue Overview</h3>
                <p class="text-xs text-gray-400">Last 14 days</p>
            </div>
        </div>
        @php
        $revMax = max($dailyRevenue) ?: 1;
        $revMin = min($dailyRevenue);
        $chartW = 100;
        $chartH = 100;
        $points = [];
        foreach ($dailyRevenue as $i => $rev) {
            $x = $chartW / (count($dailyRevenue) - 1) * $i;
            $y = $chartH - ((float)$rev / $revMax) * $chartH;
            $points[] = [$x, $y];
        }
        $linePath = collect($points)->map(fn($p) => round($p[0],2).','.round($p[1],2))->implode(' ');
        $areaPath = '0,'.$chartH.' '.$linePath.' '.$chartW.','.$chartH;
        @endphp
        <div class="relative">
            <svg viewBox="0 0 {{ $chartW }} {{ $chartH+10 }}" preserveAspectRatio="none" class="w-full h-44 overflow-visible">
                <defs>
                    <linearGradient id="revGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.25"/>
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                <polygon points="{{ $areaPath }}" fill="url(#revGradient)" />
                <polyline points="{{ $linePath }}" fill="none" stroke="#10b981" stroke-width="0.8" stroke-linejoin="round" stroke-linecap="round" vector-effect="non-scaling-stroke" />
                @foreach($points as $i => $p)
                <circle cx="{{ round($p[0],2) }}" cy="{{ round($p[1],2) }}" r="0.8" fill="#10b981" vector-effect="non-scaling-stroke" class="hover:r-1.5 transition-all">
                    <title>{{ $dailyLabels[$i] }}: TZS {{ number_format($dailyRevenue[$i]) }}</title>
                </circle>
                @endforeach
            </svg>
            <div class="flex justify-between mt-2 px-0.5">
                @foreach($dailyLabels as $i => $label)
                @if($i % 2 === 0)
                <span class="text-[9px] text-gray-400 font-medium">{{ \Carbon\Carbon::parse('now')->subDays(13-$i)->format('d M') }}</span>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Property Types --}}
    <div class="bg-white rounded-xl border p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Property Types</h3>
        @php $totalProps = $propertyTypes->sum() ?: 1; @endphp
        <div class="space-y-3">
            @foreach([
                'apartment' => ['Apartment', 'bg-emerald-500'],
                'house' => ['House', 'bg-gold-500'],
                'commercial' => ['Commercial', 'bg-sky-500'],
                'land' => ['Land', 'bg-violet-500'],
                'studio' => ['Studio', 'bg-rose-500'],
                'maisonette' => ['Maisonette', 'bg-amber-500'],
            ] as $type => $info)
            @php $count = $propertyTypes->get($type, 0); @endphp
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-0.5">
                        <p class="text-xs font-medium text-gray-900">{{ $info[0] }}</p>
                        <p class="text-xs font-semibold text-gray-900">{{ $count }}</p>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="{{ $info[1] }} h-1.5 rounded-full" style="width: {{ ($count / $totalProps) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Transactions --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Transactions</h3>
            <a href="{{ route('admin.payments') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Tx ID</th>
                    <th class="px-5 py-2.5 font-medium">User</th>
                    <th class="px-5 py-2.5 font-medium">Amount</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($recentTransactions as $tx)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-2.5 font-mono text-xs text-gray-500">{{ \Str::limit($tx->tx_id, 12) }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-700">{{ $tx->user?->name ?? 'Unknown' }}</td>
                        <td class="px-5 py-2.5 text-xs font-semibold text-gray-900">TZS {{ number_format($tx->amount) }}</td>
                        <td class="px-5 py-2.5">
                            @if($tx->status === 'success')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Success</span>
                            @elseif($tx->status === 'pending')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Failed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No transactions yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top Landlords --}}
    <div class="bg-white rounded-xl border overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Top Landlords</h3>
            <a href="{{ route('admin.users') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($topLandlords as $landlord)
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                    {{ strtoupper(substr($landlord->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $landlord->name }}</p>
                    <p class="text-xs text-gray-400">{{ $landlord->email }}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-gray-900">{{ $landlord->properties_count }}</p>
                    <p class="text-[10px] text-gray-400">properties</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No landlords yet</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
