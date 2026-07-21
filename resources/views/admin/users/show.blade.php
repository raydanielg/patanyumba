@extends('layouts.admin')

@section('title', 'User Details - Patanyumba Admin')
@section('page_title', 'User Details')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- User Info --}}
    <div class="bg-white rounded-xl border p-5">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-xl">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-xs text-gray-400">{{ $user->email }}</p>
            </div>
        </div>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between"><span class="text-gray-400">Role</span><span class="font-medium capitalize text-gray-900">{{ $user->role }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Phone</span><span class="text-gray-900">{{ $user->phone ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">KYC</span><span class="font-medium capitalize text-gray-900">{{ $user->kyc_status }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Verification</span><span class="font-medium capitalize text-gray-900">{{ $user->verification_level }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Status</span><span class="font-medium {{ $user->is_active ? 'text-emerald-600' : 'text-red-500' }}">{{ $user->is_active ? 'Active' : 'Suspended' }}</span></div>
            <div class="flex justify-between"><span class="text-gray-400">Joined</span><span class="text-gray-900">{{ $user->created_at->format('d M Y') }}</span></div>
        </div>
        <div class="mt-4 flex gap-2">
            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">@csrf
                <button type="submit" class="px-3 py-1.5 text-xs font-medium {{ $user->is_active ? 'text-red-600 bg-red-50 hover:bg-red-100' : 'text-emerald-600 bg-emerald-50 hover:bg-emerald-100' }} rounded-lg">{{ $user->is_active ? 'Suspend' : 'Activate' }}</button>
            </form>
        </div>
    </div>

    {{-- Stats --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-400 mb-1">PROPERTIES</p>
                <p class="text-xl font-bold text-gray-900">{{ $user->properties->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-400 mb-1">PAYMENTS</p>
                <p class="text-xl font-bold text-gray-900">{{ $user->payments->count() }}</p>
            </div>
            <div class="bg-white rounded-xl border p-4">
                <p class="text-[10px] font-medium text-gray-400 mb-1">KYC DOCS</p>
                <p class="text-xl font-bold text-gray-900">{{ $user->kycDocuments->count() }}</p>
            </div>
        </div>

        {{-- Properties --}}
        <div class="bg-white rounded-xl border overflow-hidden">
            <div class="px-5 py-4 border-b"><h3 class="text-sm font-semibold text-gray-900">Properties</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                        <th class="px-5 py-2.5 font-medium">Title</th>
                        <th class="px-5 py-2.5 font-medium">Price</th>
                        <th class="px-5 py-2.5 font-medium">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse($user->properties as $property)
                        <tr class="border-t border-gray-100">
                            <td class="px-5 py-2.5 text-xs text-gray-700 truncate max-w-xs">{{ $property->title }}</td>
                            <td class="px-5 py-2.5 text-xs font-semibold text-gray-900">TZS {{ number_format($property->price) }}</td>
                            <td class="px-5 py-2.5"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">{{ $property->status }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400 text-xs">No properties</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
