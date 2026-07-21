<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Patanyumba') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('logo/logoone.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('logo/logoone.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('logo/logoone.png') }}">
    <link rel="shortcut icon" href="{{ asset('logo/logoone.png') }}">

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
                        emerald: { 50:'#e8f5f3',100:'#c8e6e0',200:'#97cec4',300:'#66b5a8',400:'#359c8c',500:'#128C7E',600:'#0e7065',700:'#075E54',800:'#054540',900:'#032e2b' },
                        gold: { 50:'#e8faf0',100:'#c2f5d9',200:'#97ebbb',300:'#6de19d',400:'#42d77f',500:'#25D366',600:'#1bb55a',700:'#179148',800:'#136d38',900:'#0e4a26' }
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
