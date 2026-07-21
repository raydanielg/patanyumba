@extends('layouts.admin')

@section('title', 'Users - Patanyumba Admin')
@section('page_title', 'User Management')

@section('content')
{{-- Filters --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." class="w-64 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        <select name="role" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Roles</option>
            @foreach(['admin'=>'Admin','landlord'=>'Landlord','agent'=>'Agent','tenant'=>'Tenant'] as $val=>$label)
            <option value="{{ $val }}" @if(request('role')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

{{-- Users Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">User</th>
                <th class="px-5 py-3 font-medium">Role</th>
                <th class="px-5 py-3 font-medium">KYC</th>
                <th class="px-5 py-3 font-medium">Phone</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Joined</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($users as $user)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
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
                            @elseif($user->role==='landlord') bg-gold-50 text-gold-700 border border-gold-100
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
                        @if($user->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Active</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Suspended</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-xs text-gray-400">{{ $user->created_at->format('d M Y') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View</a>
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs {{ $user->is_active ? 'text-red-500 hover:text-red-600' : 'text-emerald-600 hover:text-emerald-700' }} font-medium">{{ $user->is_active ? 'Suspend' : 'Activate' }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No users found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $users->withQueryString()->links() }}
</div>
@endsection
