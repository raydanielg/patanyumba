@extends('layouts.admin')

@section('title', 'Subscriptions - Patanyumba Admin')
@section('page_title', 'Subscription Plans')

@section('content')
{{-- Add Plan Button --}}
<div class="mb-4 flex items-center justify-between">
    <p class="text-sm text-gray-500">Manage subscription plans and active subscriptions</p>
    <button onclick="document.getElementById('addPlanModal').classList.remove('hidden')" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Add Plan</button>
</div>

{{-- Plans Grid --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    @forelse($plans as $plan)
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">{{ $plan->name }}</h3>
                <p class="text-xs text-gray-400 capitalize">{{ $plan->billing_cycle }}</p>
            </div>
            @if($plan->is_active)
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
            @else
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-500 border border-gray-100">Inactive</span>
            @endif
        </div>
        <p class="text-2xl font-bold text-emerald-600">TZS {{ number_format($plan->price) }}</p>
        @if($plan->description)
        <p class="text-xs text-gray-500 mt-2">{{ $plan->description }}</p>
        @endif
        @if($plan->unlock_limit)
        <p class="text-xs text-gray-400 mt-2">{{ $plan->unlock_limit }} unlocks included</p>
        @else
        <p class="text-xs text-gray-400 mt-2">Unlimited unlocks</p>
        @endif
        <div class="mt-4">
            <form action="{{ route('admin.subscriptions.plans.toggle', $plan) }}" method="POST" class="inline">@csrf
                <button type="submit" class="text-xs {{ $plan->is_active ? 'text-red-500 hover:text-red-600' : 'text-emerald-600 hover:text-emerald-700' }} font-medium">{{ $plan->is_active ? 'Deactivate' : 'Activate' }}</button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-xl border p-8 text-center text-gray-400 text-sm">No subscription plans yet</div>
    @endforelse
</div>

{{-- Add Plan Modal --}}
<div id="addPlanModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Add Subscription Plan</h3>
        <form action="{{ route('admin.subscriptions.plans.store') }}" method="POST">
            @csrf
            <div class="space-y-3">
                <input type="text" name="name" required placeholder="Plan name" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <input type="text" name="slug" required placeholder="plan-slug" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <input type="number" name="price" required step="0.01" placeholder="Price (TZS)" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <select name="billing_cycle" required class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
                    @foreach(['one_time'=>'One Time','weekly'=>'Weekly','monthly'=>'Monthly','quarterly'=>'Quarterly','yearly'=>'Yearly'] as $val=>$label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="number" name="unlock_limit" placeholder="Unlock limit (empty = unlimited)" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
                <textarea name="description" rows="2" placeholder="Description" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none"></textarea>
            </div>
            <div class="flex items-center gap-3 mt-4">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Create</button>
                <button type="button" onclick="document.getElementById('addPlanModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
