@extends('layouts.admin')

@section('title', 'Properties - Patanyumba Admin')
@section('page_title', 'Property Management')

@section('content')
{{-- Filters --}}
<div class="mb-4 flex flex-wrap items-center gap-3">
    <form method="GET" class="flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, region..." class="w-64 px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
        <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Status</option>
            @foreach(['pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected','expired'=>'Expired','rented'=>'Rented'] as $val=>$label)
            <option value="{{ $val }}" @if(request('status')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <select name="type" class="px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 outline-none">
            <option value="">All Types</option>
            @foreach(['apartment'=>'Apartment','house'=>'House','commercial'=>'Commercial','land'=>'Land','studio'=>'Studio','maisonette'=>'Maisonette'] as $val=>$label)
            <option value="{{ $val }}" @if(request('type')===$val) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

{{-- Properties Table --}}
<div class="bg-white rounded-xl border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                <th class="px-5 py-3 font-medium">Title</th>
                <th class="px-5 py-3 font-medium">Landlord</th>
                <th class="px-5 py-3 font-medium">Type</th>
                <th class="px-5 py-3 font-medium">Price</th>
                <th class="px-5 py-3 font-medium">Location</th>
                <th class="px-5 py-3 font-medium">Status</th>
                <th class="px-5 py-3 font-medium">Actions</th>
            </tr></thead>
            <tbody>
                @forelse($properties as $property)
                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-3 text-xs font-medium text-gray-900 max-w-xs truncate">{{ $property->title }}</td>
                    <td class="px-5 py-3 text-xs text-gray-700">{{ $property->user?->name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize bg-gray-50 text-gray-700 border border-gray-100">
                            {{ $property->property_type }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">TZS {{ number_format($property->price) }}</td>
                    <td class="px-5 py-3 text-xs text-gray-500">{{ $property->region }}, {{ $property->district }}</td>
                    <td class="px-5 py-3">
                        @if($property->status==='approved')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">Approved</span>
                        @elseif($property->status==='pending')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-50 text-amber-700 border border-amber-100">Pending</span>
                        @elseif($property->status==='rejected')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-50 text-red-700 border border-red-100">Rejected</span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-700 border border-gray-100 capitalize">{{ $property->status }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.properties.show', $property) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View</a>
                            @if($property->status==='pending')
                            <form action="{{ route('admin.properties.approve', $property) }}" method="POST" class="inline">@csrf <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Approve</button></form>
                            <form action="{{ route('admin.properties.reject', $property) }}" method="POST" class="inline">@csrf <button type="submit" class="text-xs text-red-500 hover:text-red-600 font-medium">Reject</button></form>
                            @endif
                            <form action="{{ route('admin.properties.toggle-featured', $property) }}" method="POST" class="inline">@csrf
                                <button type="submit" class="text-xs {{ $property->is_featured ? 'text-gold-600 hover:text-gold-700' : 'text-gray-400 hover:text-gray-600' }} font-medium">{{ $property->is_featured ? 'Unfeature' : 'Feature' }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">No properties found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">{{ $properties->withQueryString()->links() }}</div>
@endsection
