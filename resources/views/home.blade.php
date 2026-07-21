@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-8 border-b border-gray-100 bg-gradient-to-r from-emerald-50 to-white">
            <h1 class="text-2xl font-extrabold text-gray-800">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome to Patanyumba</p>
        </div>
        <div class="p-6">
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex items-center gap-3 p-4 rounded-lg bg-emerald-50 border border-emerald-100">
                <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-emerald-700 font-semibold">You are logged in!</p>
            </div>
        </div>
    </div>
</div>
@endsection
