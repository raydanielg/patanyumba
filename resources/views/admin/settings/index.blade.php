@extends('layouts.admin')

@section('title', 'Settings - Patanyumba Admin')
@section('page_title', 'System Settings')

@section('content')
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')
    @foreach($settings as $group => $items)
    <div class="bg-white rounded-xl border p-5 mb-4">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 capitalize">{{ $group }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($items as $setting)
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">{{ str_replace('_', ' ', ucfirst($setting->key)) }}</label>
                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none">
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    @if($settings->isEmpty())
    <div class="bg-white rounded-xl border p-8 text-center text-gray-400 text-sm">No settings configured yet</div>
    @endif

    <div class="flex items-center gap-3">
        <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700">Save Settings</button>
    </div>
</form>
@endsection
