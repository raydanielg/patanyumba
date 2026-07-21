@extends('layouts.auth')

@section('title', 'Verify Email - Patanyumba')

@section('content')
<div class="w-full">
    <div class="bg-white rounded-none sm:rounded-2xl shadow-none sm:shadow-xl border-0 sm:border border-gray-100 overflow-hidden min-h-screen sm:min-h-0">
        {{-- Header --}}
        <div class="px-6 sm:px-8 py-8 text-center border-b border-gray-100">
            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-900 flex items-center justify-center shadow-lg">
                <svg class="w-9 h-9 text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <h2 class="text-2xl font-extrabold text-gray-800">Verify Your Email</h2>
            <p class="text-gray-400 text-sm mt-1">Check your email for a verification link</p>
        </div>

        {{-- Content --}}
        <div class="p-6 sm:p-8">
            @if (session('resent'))
                <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">
                    A fresh verification link has been sent to your email address.
                </div>
            @endif

            <p class="text-sm text-gray-600 mb-6">
                Before proceeding, please check your email for a verification link.
                If you did not receive the email,
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700 transition-colors underline">click here to request another</button>.
                </form>
            </p>

            <a href="{{ route('home') }}" class="block w-full py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 rounded-lg shadow-md hover:shadow-lg transition-all text-center">
                Back to Home
            </a>
        </div>
    </div>

    <p class="mt-6 text-center text-xs text-gray-400 hidden sm:block">&copy; {{ date('Y') }} Patanyumba. All rights reserved.</p>
</div>
@endsection
