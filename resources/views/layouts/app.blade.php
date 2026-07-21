<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Patanyumba') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700,800,900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 50:'#e6f5f1',100:'#b3e0d4',200:'#80cbc0',300:'#4db5a8',400:'#1a9f8e',500:'#024938',600:'#023d30',700:'#013028',800:'#01241f',900:'#001816' },
                        gold: { 50:'#fff5e0',100:'#ffe6b3',200:'#ffd680',300:'#ffc64d',400:'#ffb71a',500:'#f9ac00',600:'#d49700',700:'#b07c00',800:'#8c6100',900:'#684600' }
                    }
                }
            }
        }
    </script>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="font-['Nunito',sans-serif] antialiased bg-gray-50 min-h-screen">
    <div id="app">
        <nav class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <a class="flex items-center gap-2.5" href="{{ url('/') }}">
                        <img src="{{ asset('logo/whitelogo.png') }}" alt="Patanyumba" class="w-9 h-9 object-contain">
                        <span class="text-lg font-extrabold text-gray-800">{{ config('app.name', 'Patanyumba') }}</span>
                    </a>

                    <!-- Mobile menu button -->
                    <button class="md:hidden text-gray-600 hover:text-gray-900" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center gap-6">
                        @guest
                            @if (Route::has('login'))
                                <a class="text-sm font-semibold text-gray-600 hover:text-emerald-600 transition-colors" href="{{ route('login') }}">Login</a>
                            @endif
                            @if (Route::has('register'))
                                <a class="text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-emerald-800 hover:from-emerald-700 hover:to-emerald-900 px-5 py-2 rounded-lg shadow-sm hover:shadow-md transition-all" href="{{ route('register') }}">Get Started</a>
                            @endif
                        @else
                            <div class="relative" id="userDropdown">
                                <button class="flex items-center gap-2 text-sm font-semibold text-gray-700 hover:text-emerald-600 transition-colors" onclick="document.getElementById('userMenu').classList.toggle('hidden')">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span>{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-100 py-1 z-50">
                                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                </div>
                            </div>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @endguest
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div id="mobileMenu" class="hidden md:hidden pb-4 space-y-2">
                    @guest
                        @if (Route::has('login'))
                            <a class="block px-4 py-2 text-sm font-semibold text-gray-600 hover:text-emerald-600 transition-colors" href="{{ route('login') }}">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="block px-4 py-2 text-sm font-bold text-emerald-600" href="{{ route('register') }}">Get Started</a>
                        @endif
                    @else
                        <a class="block px-4 py-2 text-sm font-semibold text-gray-600 hover:text-emerald-600 transition-colors" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    @endguest
                </div>
            </div>
        </nav>

        <main class="">
            @yield('content')
        </main>
    </div>
</body>
</html>
