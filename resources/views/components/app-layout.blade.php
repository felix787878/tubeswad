<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - UKM Connect</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('/logo.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: rgba(255,255,255,0.3); border-radius: 3px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>
<body class="h-full font-inter">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">
        {{-- Sidebar --}}
        <aside 
            id="sidebar" 
            class="bg-red-800 text-white w-64 fixed inset-y-0 left-0 top-0 z-40 transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex-shrink-0 flex flex-col sidebar-scroll"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            @click.away="sidebarOpen = false">
            {{-- Logo di Sidebar --}}
            <div class="flex items-center justify-center h-16 bg-red-900 flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center text-white">
                    <img src="{{ asset('/logo.png') }}" alt="Logo" class="h-8 w-auto mr-2">
                    <span class="text-xl font-semibold">UKM Connect</span>
                </a>
            </div>
            {{-- Navigasi Sidebar --}}
            <nav class="flex-1 pt-2 pb-4 px-2 space-y-1 overflow-y-auto">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('home') ? 'bg-red-900' : '' }}">
                    <span class="material-icons mr-3">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('ukm-ormawa.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('ukm-ormawa.index') || request()->routeIs('ukm-ormawa.show') || request()->routeIs('ukm-ormawa.apply.form') ? 'bg-red-900' : '' }}">
                    <span class="material-icons mr-3">list_alt</span>
                    Daftar UKM/Ormawa
                </a>
                <a href="{{ route('my-activities.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('my-activities.index') || request()->routeIs('activities.public.show') ? 'bg-red-900' : '' }}">
                    <span class="material-icons mr-3">event_note</span>
                    Kegiatan Kampus
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('settings.index') ? 'bg-red-900' : '' }}">
                    <span class="material-icons mr-3">settings</span>
                    Pengaturan
                </a>
            </nav>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col">
            {{-- Header --}}
            <header class="bg-white text-gray-700 shadow-md z-10 sticky top-0">
                <div class="container-fluid mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                            <span class="material-icons">menu</span>
                        </button>
                        <div class="flex-1"></div>
                        <div class="flex items-center gap-4">
                            <div class="hidden sm:block">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative text-gray-400 focus-within:text-gray-600">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="material-icons text-sm">search</span>
                                    </div>
                                    <input id="search" name="search" class="block w-full bg-gray-100 placeholder-gray-500 text-gray-900 border border-transparent rounded-md py-2 pl-10 pr-3 leading-5 focus:outline-none focus:bg-white focus:border-gray-300 sm:text-sm" placeholder="Search..." type="search">
                                </div>
                            </div>
                            @auth
                                <button class="p-1 rounded-full text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <span class="material-icons">notifications</span>
                                </button>
                                <div x-data="{ profileMenuOpen: false }" class="ml-3 relative">
                                    <div>
                                        <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="max-w-xs bg-red-700 rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-700 focus:ring-white" id="user-menu-button">
                                            <span class="sr-only">Open user menu</span>
                                            <span class="material-icons text-3xl text-red-200 hover:text-white">account_circle</span>
                                        </button>
                                    </div>
                                    <div x-show="profileMenuOpen" @click.outside="profileMenuOpen = false" x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" style="display: none;">
                                        <div class="px-4 py-3"><p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p><p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p><span class="mt-1 inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-gray-200 text-gray-700">{{ ucfirst(Auth::user()->role) }}</span></div><div class="border-t border-gray-100"></div><a href="{{ route('settings.index') }}" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><span class="material-icons text-base mr-2 align-middle">manage_accounts</span>Profil & Pengaturan Akun</a><div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700"><span class="material-icons text-base mr-2 align-middle">logout</span>Logout</button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-medium hover:text-red-600">Login</a>
                                <a href="{{ route('register') }}" class="ml-4 text-sm font-medium hover:text-red-600">Register</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            {{-- Area Konten Utama --}}
            <main class="flex-grow">
                <div class="container mx-auto px-6 py-8">
                    {{ $slot }} {{-- Ganti @yield('content') dengan ini --}}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>