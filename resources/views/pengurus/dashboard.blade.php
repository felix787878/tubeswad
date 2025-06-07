<x-pengurus-app-layout> {{-- Sesuaikan dengan nama layout Anda --}}
    <x-slot name="header">
        {{ __('Dashboard Pengurus') }}
    </x-slot>

    @if (Auth::user()->createdUkmOrmawa)
        {{-- Tampilan jika pengurus SUDAH punya UKM/Ormawa --}}
        <div class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-2xl font-semibold text-gray-800">
                    Selamat Datang, Pengurus {{ Auth::user()->createdUkmOrmawa->name }}!
                </h3>
                <p class="text-gray-600">Ini adalah halaman utama untuk mengelola {{ Auth::user()->createdUkmOrmawa->name }}.</p>
                 @if(Auth::user()->createdUkmOrmawa->status == 'pending_verification')
                    <div class="mt-4 p-3 bg-yellow-100 text-yellow-700 border border-yellow-300 rounded-md text-sm">
                        <span class="font-semibold">Perhatian:</span> Profil UKM/Ormawa Anda (<span class="font-medium">{{ Auth::user()->createdUkmOrmawa->name }}</span>) sedang menunggu verifikasi dari Admin Direktorat. Fitur publik mungkin belum aktif sampai disetujui.
                    </div>
                @elseif(Auth::user()->createdUkmOrmawa->status == 'needs_update')
                    <div class="mt-4 p-3 bg-orange-100 text-orange-700 border border-orange-300 rounded-md text-sm">
                        <span class="font-semibold">Perhatian:</span> Profil UKM/Ormawa Anda (<span class="font-medium">{{ Auth::user()->createdUkmOrmawa->name }}</span>) memerlukan revisi. Silakan periksa catatan dari Admin Direktorat dan perbarui profil Anda.
                    </div>
                 @elseif(Auth::user()->createdUkmOrmawa->status == 'rejected')
                    <div class="mt-4 p-3 bg-red-100 text-red-700 border border-red-300 rounded-md text-sm">
                        <span class="font-semibold">Informasi:</span> Pengajuan profil UKM/Ormawa Anda (<span class="font-medium">{{ Auth::user()->createdUkmOrmawa->name }}</span>) sebelumnya telah ditolak. Anda dapat mencoba mengajukan kembali dengan membuat profil baru jika diperlukan atau hubungi Admin Direktorat.
                    </div>
                @endif
            </div>

            {{-- Statistik (hanya tampil jika ukmOrmawa ada) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <span class="material-icons text-white text-2xl">group</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Jumlah Anggota</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $memberCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg p-6">
                    <div class="flex items-center">
                         <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <span class="material-icons text-white text-2xl">person_add</span>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 truncate">Pendaftar Baru</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $newApplicationsCount ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            
            </div>

            @if(!empty($chartData['labels']))
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Statistik Pendaftaran Anggota (Per Bulan)</h4>
                <div class="h-64 md:h-96">
                    <canvas id="registrationStatsChart"></canvas>
                </div>
            </div>
            @endif

            @if($recentApplications->isNotEmpty())
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-4">Pendaftar Terbaru</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        {{-- ... tabel recentApplications ... --}}
                         <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pendaftar</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($recentApplications as $application)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $application->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $application->created_at->format('d M Y, H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($application->status == 'pending') bg-yellow-100 text-yellow-800 @elseif($application->status == 'approved') bg-green-100 text-green-800 @elseif($application->status == 'rejected') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('pengurus.members.show', $application->id) }}" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    @else
        {{-- Tampilan jika pengurus BELUM punya UKM/Ormawa --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <div class="text-center py-12">
                <span class="material-icons text-6xl text-slate-400 mb-4">info</span>
                <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                    Anda Belum Mengelola UKM/Ormawa
                </h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    Untuk mulai menggunakan fitur dashboard pengurus, Anda perlu membuat profil UKM/Ormawa yang akan Anda kelola terlebih dahulu.
                </p>
                <a href="{{ route('pengurus.ukm-ormawa.edit') }}" {{-- Ini akan diarahkan ke create oleh controller --}}
                   class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition ease-in-out duration-150">
                    <span class="material-icons mr-2">add_circle_outline</span>
                    Buat Profil UKM/Ormawa Sekarang
                </a>
            </div>
        </div>
    @endif

    @push('scripts')
    {{-- ... (script Chart.js tetap sama, tapi pastikan tidak error jika chartData kosong) ... --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartData = {
                labels: {!! json_encode($chartData['labels']) !!}, // Bisa jadi array kosong
                data: {!! json_encode($chartData['data']) !!}   // Bisa jadi array kosong
            };

            if (document.getElementById('registrationStatsChart') && chartData.labels.length > 0) { // Hanya buat chart jika ada data
                const ctx = document.getElementById('registrationStatsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Pendaftaran Baru per Bulan',
                            data: chartData.data,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1,
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Script untuk notifikasi session agar hilang setelah beberapa detik
            function fadeOutAndHide(elementId) {
                const element = document.getElementById(elementId);
                if (element) {
                    setTimeout(() => {
                        element.style.transition = 'opacity 0.3s ease-out';
                        element.style.opacity = '0';
                        setTimeout(() => element.style.display = 'none', 300);
                    }, 7000);
                }
            }
            fadeOutAndHide('successMessageDashboardPengurus');
            fadeOutAndHide('errorMessageDashboardPengurus');
            fadeOutAndHide('warningMessageDashboardPengurus');
        });
    </script>
    @endpush
</x-pengurus-app-layout>