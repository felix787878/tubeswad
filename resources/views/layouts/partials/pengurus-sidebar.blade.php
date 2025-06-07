<aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-800 text-slate-100 transform transition duration-300 ease-in-out md:translate-x-0 md:static md:inset-0"
    :class="{'translate-x-0 ease-out': sidebarOpen, '-translate-x-full ease-in': !sidebarOpen}"
    @click.away="sidebarOpen = false">
    <div class="flex items-center justify-center mt-8 px-4">
        <div class="flex flex-col items-center">
            @php $currentUkmOrmawa = Auth::user()->createdUkmOrmawa; @endphp {{-- Ambil dari Auth User langsung --}}

            @if($currentUkmOrmawa && $currentUkmOrmawa->logo_url)
                <img src="{{ asset('storage/' . $currentUkmOrmawa->logo_url) }}" alt="{{ $currentUkmOrmawa->name }} Logo" class="w-20 h-20 mx-auto rounded-full mb-2 object-cover border-2 border-slate-500 shadow-md">
            @elseif($currentUkmOrmawa)
                 <div class="w-20 h-20 mx-auto rounded-full bg-slate-600 flex items-center justify-center text-3xl mb-2 text-white font-semibold border-2 border-slate-500 shadow-md">
                    {{ strtoupper(substr($currentUkmOrmawa->name, 0, 1)) }}
                </div>
            @else
                {{-- Tampilan jika belum ada UKM/Ormawa --}}
                <span class="material-icons text-6xl text-slate-500 mb-2">pending_actions</span>
            @endif
            
            <span class="text-white text-lg font-semibold mt-1 px-2 text-center leading-tight">
                {{ $currentUkmOrmawa->name ?? 'Profil Belum Ada' }}
            </span>
            @if($currentUkmOrmawa)
                <span class="text-slate-300 text-xs mt-1">Pengurus {{ $currentUkmOrmawa->type }}</span>
            @else
                <span class="text-slate-400 text-xs mt-1">Buat profil UKM/Ormawa Anda</span>
            @endif
        </div>
    </div>

    <nav class="mt-10 px-2 space-y-1">
        <a href="{{ route('pengurus.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <span class="material-icons mr-3 text-base">dashboard</span>
            Dashboard
        </a>

        {{-- Ubah teks dan logika link Kelola Profil --}}
        <a href="{{ route('pengurus.ukm-ormawa.edit') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.ukm-ormawa.edit') || request()->routeIs('pengurus.ukm-ormawa.create') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <span class="material-icons mr-3 text-base">storefront</span>
            {{ $currentUkmOrmawa ? 'Kelola Profil UKM/Ormawa' : 'Buat Profil UKM/Ormawa' }}
        </a>
        
        {{-- Nonaktifkan menu lain jika belum ada UKM/Ormawa --}}
        @if($currentUkmOrmawa)
            <a href="{{ route('pengurus.members.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.members.index') || request()->routeIs('pengurus.members.show') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <span class="material-icons mr-3 text-base">groups</span>
                Daftar Anggota
            </a>
            <a href="{{ route('pengurus.activities.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.activities.index') || request()->routeIs('pengurus.activities.create') || request()->routeIs('pengurus.activities.edit') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <span class="material-icons mr-3 text-base">event_note</span>
                Daftar Kegiatan
            </a>
            <a href="{{ route('pengurus.attendance.reports') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('pengurus.attendance.reports') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                <span class="material-icons mr-3 text-base">playlist_add_check</span>
                Laporan Kehadiran
            </a>
        @else
            <span class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-slate-500 cursor-not-allowed" title="Buat profil UKM/Ormawa terlebih dahulu">
                <span class="material-icons mr-3 text-base">groups</span>
                Daftar Anggota
            </span>
             <span class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-slate-500 cursor-not-allowed" title="Buat profil UKM/Ormawa terlebih dahulu">
                <span class="material-icons mr-3 text-base">event_note</span>
                Daftar Kegiatan
            </span>
             <span class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-slate-500 cursor-not-allowed" title="Buat profil UKM/Ormawa terlebih dahulu">
                <span class="material-icons mr-3 text-base">playlist_add_check</span>
                Laporan Kehadiran
            </span>
        @endif

        <div class="border-t border-slate-700 my-4"></div>
        <a href="{{ route('pengurus.settings.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-md {{ request()->routeIs('settings.index') ? 'bg-slate-900 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
            <span class="material-icons mr-3 text-base">settings</span>
            Pengaturan Akun Saya
        </a>
        </a>
    </nav>
</aside>