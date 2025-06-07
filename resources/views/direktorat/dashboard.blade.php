<x-direktorat-app-layout>
    <x-slot name="header">
        {{ __('Dashboard Direktorat Kemahasiswaan') }}
    </x-slot>

    <div class="space-y-8">
        {{-- Welcome Message --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h1 class="text-2xl font-bold text-indigo-700">Selamat Datang, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600 mt-1">Panel kontrol Direktorat Kemahasiswaan UKM Connect.</p>
        </div>

        {{-- Statistik Utama --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="bg-white p-5 rounded-lg shadow-md transform hover:scale-105 transition-transform">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <span class="material-icons text-2xl">groups</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total UKM/Ormawa</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalUkmOrmawa ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-md transform hover:scale-105 transition-transform">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                        <span class="material-icons text-2xl">hourglass_top</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Menunggu Verifikasi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $pendingVerification ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-md transform hover:scale-105 transition-transform">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <span class="material-icons text-2xl">verified_user</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">UKM/Ormawa Terverifikasi</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $approvedUkmOrmawa ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-lg shadow-md transform hover:scale-105 transition-transform">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <span class="material-icons text-2xl">school</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Mahasiswa</p> {{-- Bisa juga total pengguna --}}
                        <p class="text-2xl font-bold text-gray-800">{{ $totalMahasiswa ?? 0 }}</p>
                    </div>
                </div>
            </div>
             <div class="bg-white p-5 rounded-lg shadow-md transform hover:scale-105 transition-transform">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-teal-100 text-teal-600 mr-4">
                        <span class="material-icons text-2xl">person_add</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Pendaftar ke UKM/Ormawa</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalPendaftarUkm ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar UKM/Ormawa Menunggu Verifikasi --}}
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-700">UKM/Ormawa Menunggu Verifikasi Terbaru</h2>
                @if($pendingVerification > 0)
                <a href="{{ route('direktorat.ukm-ormawa.index', ['status' => 'pending_verification']) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                    Lihat Semua &rarr;
                </a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama UKM/Ormawa</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diajukan Oleh</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($recentPendingUkm as $ukm)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $ukm->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $ukm->category }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ukm->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ukm->pengurus->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ukm->created_at?->isoFormat('D MMM YY, HH:mm') ?? 'Not Available' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('direktorat.ukm-ormawa.show', $ukm->id) }}" class="text-indigo-600 hover:text-indigo-900 inline-flex items-center px-3 py-1.5 border border-indigo-300 rounded-md text-xs hover:bg-indigo-50">
                                        <span class="material-icons text-sm mr-1">visibility</span>
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <span class="material-icons text-3xl text-green-500 mb-2">check_circle_outline</span>
                                        <span>Tidak ada UKM/Ormawa yang menunggu verifikasi saat ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-direktorat-app-layout>