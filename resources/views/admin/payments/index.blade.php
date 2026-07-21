@extends('layouts.admin')

@section('title', 'Payments - Patanyumba Admin')
@section('page_title', 'Payment Management')

@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-3 mb-6">
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl border border-emerald-500 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-emerald-100">TOTAL REVENUE</span>
            <p class="text-xl font-bold mt-1">TZS {{ number_format($totalRevenue) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-gold-400 to-gold-500 rounded-xl border border-gold-300 p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <span class="text-[10px] font-medium text-gold-50">TRANSACTIONS</span>
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
</div>

{{-- Filters --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
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
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

{{-- Payments Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Tx ID</th>
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Method</th>
                <th class="px-5 py-3 font-medium">Amount</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Date</th>
            </tr></thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ \Str::limit($payment->tx_id, 14) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $payment->user?->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">{{ $payment->payment_type }}</span></td>
                    <td class="px-5 py-3 text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $payment->method) }}</td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($payment->amount) }}</td>
                    <td class="px-5 py-3">
                        @if($payment->status==='success')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Success</span>
                        @elseif($payment->status==='pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @elseif($payment->status==='refunded')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-50 text-sky-700 border border-sky-100">Refunded</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Failed</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No payments found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $payments->withQueryString()->links() }}</div>
@endsection
