@extends('layouts.auth')

@section('title', 'Verify Email - Patanyumba')

@section('content')
<div class="w-full">
    <div class="bg-white rounded-none sm:rounded-2xl shadow-none sm:shadow-xl border-0 sm:border border-gray-100 overflow-hidden min-h-screen sm:min-h-0">
        {{-- Header --}}
        <div class="px-6 sm:px-8 py-8 text-center border-b border-gray-100">
            <img src="{{ asset('logo/whitelogo.png') }}" alt="Patanyumba" class="w-16 h-16 mx-auto object-contain mb-3">
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
