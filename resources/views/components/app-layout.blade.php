<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} - UKM Connect</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('/logo.png') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter">
    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="flex h-screen bg-gray-100">

        <div x-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            class="bg-red-800 text-white fixed inset-y-0 left-0 top-0 z-40 flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out md:relative"
            :class="{
                'w-64': !sidebarCollapsed,
                'w-20': sidebarCollapsed,
                'translate-x-0': sidebarOpen,
                '-translate-x-full': !sidebarOpen && window.innerWidth < 768
            }">

            <div class="flex items-center h-16 px-4 bg-red-900" :class="'justify-center'">
                {{-- Tombol Hamburger untuk Expand/Collapse di Desktop --}}
                <button @click="sidebarCollapsed = !sidebarCollapsed" class="text-white focus:outline-none hidden md:block" title="Toggle Sidebar">
                    <span class="material-icons">menu</span>
                </button>
            </div>

            {{-- Navigasi Sidebar --}}
            <nav class="flex-1 pt-2 pb-4 px-2 space-y-1 overflow-y-auto sidebar-scroll">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('home') ? 'bg-red-900' : '' }}" :class="{'justify-center': sidebarCollapsed}">
                    <span class="material-icons" :class="{'mr-3': !sidebarCollapsed}">dashboard</span>
                    <span class="transition-opacity duration-200" :class="{'opacity-0 hidden': sidebarCollapsed}">Dashboard</span>
                </a>
                <a href="{{ route('ukm-ormawa.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('ukm-ormawa.index') || request()->routeIs('ukm-ormawa.show') || request()->routeIs('ukm-ormawa.apply.form') ? 'bg-red-900' : '' }}" :class="{'justify-center': sidebarCollapsed}">
                    <span class="material-icons" :class="{'mr-3': !sidebarCollapsed}">list_alt</span>
                    <span class="transition-opacity duration-200" :class="{'opacity-0 hidden': sidebarCollapsed}">Daftar UKM/Ormawa</span>
                </a>
                <a href="{{ route('my-activities.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('my-activities.index') || request()->routeIs('activities.public.show') ? 'bg-red-900' : '' }}" :class="{'justify-center': sidebarCollapsed}">
                    <span class="material-icons" :class="{'mr-3': !sidebarCollapsed}">event_note</span>
                    <span class="transition-opacity duration-200" :class="{'opacity-0 hidden': sidebarCollapsed}">Kegiatan Kampus</span>
                </a>
                <a href="{{ route('settings.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-md hover:bg-red-700 {{ request()->routeIs('settings.index') ? 'bg-red-900' : '' }}" :class="{'justify-center': sidebarCollapsed}">
                    <span class="material-icons" :class="{'mr-3': !sidebarCollapsed}">settings</span>
                    <span class="transition-opacity duration-200" :class="{'opacity-0 hidden': sidebarCollapsed}">Pengaturan</span>
                </a>
            </nav>
        </aside>

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">
            {{-- Header Utama --}}
            <header class="bg-white text-gray-700 shadow-md z-10 sticky top-0">
                <div class="container-fluid mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        {{-- Tombol Hamburger untuk Mobile --}}
                        <button @click.stop="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                            <span class="material-icons">menu</span>
                        </button>

                        {{-- Logo di Header --}}
                        <div class="flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center">
                                <img src="{{ asset('/logo.png') }}" alt="Logo" class="h-8 w-auto mr-2">
                                <span class="text-xl font-semibold">UKM Connect</span>
                            </a>
                        </div>

                        <div class="flex-1"></div>

                        {{-- Menu Profil Pengguna --}}
                        <div class="flex items-center gap-4">
                             @auth
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

            {{-- Main Content (Slot untuk konten halaman) --}}
            <main class="flex-grow overflow-y-auto">
                <div class="container mx-auto px-6 py-8">
                    {{-- Di sinilah semua konten dari file Blade Anda akan ditampilkan --}}
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    {{-- Tempat untuk script yang di-push dari halaman konten --}}
    @stack('scripts')
</body>
</html>