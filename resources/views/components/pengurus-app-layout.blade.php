<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Pengurus</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    {{-- `sidebarOpen` adalah untuk mengontrol sidebar di mobile --}}
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-100">
        @include('layouts.partials.pengurus-sidebar')

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow relative z-20"> {{-- Naikkan z-index header sedikit --}}
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    {{-- Tombol untuk toggle sidebar di mobile --}}
                    <button @click.stop="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">{{ $header ?? (Auth::user()->createdUkmOrmawa->name ?? 'Dashboard Pengurus') }}</h1>
                    
                    {{-- Dropdown Profil Pengurus --}}
                    {{-- `profileMenuOpen` adalah variabel baru khusus untuk dropdown profil ini --}}
                    <div x-data="{ profileMenuOpen: false }" class="flex items-center ml-4">
                        <div class="relative">
                            <div>
                                <button @click="profileMenuOpen = !profileMenuOpen" type="button" class="max-w-xs bg-white rounded-full flex items-center text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-300" id="user-menu-button-pengurus" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <span class="material-icons text-3xl text-gray-500 hover:text-gray-700">account_circle</span>
                                </button>
                            </div>
                            {{-- Dropdown panel --}}
                            <div x-show="profileMenuOpen"
                                 @click.outside="profileMenuOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-30" {{-- Naikkan z-index dropdown --}}
                                 role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button-pengurus" tabindex="-1"
                                 style="display: none;" {{-- Alpine akan mengontrol ini, cegah FOUC --}}>
                                <div class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                    <span class="mt-1 inline-block text-xs font-medium px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">{{ ucfirst(Auth::user()->role) }}</span>
                                </div>
                                <div class="border-t border-gray-100"></div>
                                <a href="{{ route('pengurus.settings.index') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem" tabindex="-1" @click="profileMenuOpen = false"> {{-- Tambahkan @click untuk menutup saat item dipilih --}}
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
    {{-- HAPUS SCRIPT LAMA YANG MUNGKIN BERTABRAKAN --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            const userMenuButtonPengurus = document.getElementById('user-menu-button-pengurus');
            const userMenuPengurus = document.getElementById('user-menu-pengurus'); // ID INI TIDAK ADA DI PANEL DROPDOWN

            if (userMenuButtonPengurus && userMenuPengurus) {
                // ... logika lama ...
            }
        });
    </script> --}}
    @stack('scripts')
</body>
</html>