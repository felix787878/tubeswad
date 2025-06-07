<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Direktorat Kemahasiswaan</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        {{-- Sidebar Direktorat --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 bg-indigo-800 text-indigo-100 transform transition duration-300 ease-in-out md:translate-x-0 md:static md:inset-0"
            :class="{'translate-x-0 ease-out': sidebarOpen, '-translate-x-full ease-in': !sidebarOpen}"
            @click.away="sidebarOpen = false">
            <div class="flex items-center justify-center mt-8 px-4">
                <div class="flex flex-col items-center">
                    <span class="material-icons text-5xl text-indigo-300 mb-2">admin_panel_settings</span>
                    <span class="text-white text-xl font-semibold mt-1 px-2 text-center leading-tight">Direktorat</span>
                    <span class="text-indigo-300 text-xs mt-1">Panel Kontrol</span>
                </div>
            </div>

            <nav class="mt-10 px-2 space-y-1">
                <a href="{{ route('direktorat.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('direktorat.dashboard') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                    <span class="material-icons mr-3 text-base">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('direktorat.ukm-ormawa.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('direktorat.ukm-ormawa.index') || request()->routeIs('direktorat.ukm-ormawa.show') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                    <span class="material-icons mr-3 text-base">fact_check</span>
                    Verifikasi UKM/Ormawa
                </a>
                <a href="{{ route('direktorat.laporan-umum') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('direktorat.direktorat.laporan-umum') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}"> 
                    <span class="material-icons mr-3 text-base">summarize</span>
                    Laporan Umum
                </a>

                <div class="border-t border-indigo-700 my-4"></div>
                <a href="{{ route('direktorat.settings.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('settings.index') ? 'bg-indigo-900 text-white' : 'text-indigo-200 hover:bg-indigo-700 hover:text-white' }}">
                    <span class="material-icons mr-3 text-base">settings</span>
                    Pengaturan Akun
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow relative z-20">
                <div class="max-w-full mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <button @click.stop="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">{{ $header ?? 'Panel Direktorat Kemahasiswaan' }}</h1>
                    
                    <div x-data="{ profileMenuOpen: false }" class="flex items-center ml-4">
                        <div class="relative">
                            <div>
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" id="user-menu-button-direktorat" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <span class="material-icons text-3xl text-gray-500 hover:text-gray-700">account_circle</span>
                                </button>
                            </div>
                            <div x-show="profileMenuOpen"
                                 @click.outside="profileMenuOpen = false"
                                 x-transition
                                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-30"
                                 role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button-direktorat" tabindex="-1"
                                 style="display: none;">
                                <div class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    <span class="mt-1 inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                                <div class="border-t border-gray-100"></div>
                                <a href="{{ route('direktorat.settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" @click="profileMenuOpen = false">
                                    <span class="material-icons text-base mr-2 align-middle">manage_accounts</span>Pengaturan Akun
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700" role="menuitem" tabindex="-1">
                                        <span class="material-icons text-base mr-2 align-middle">logout</span>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>