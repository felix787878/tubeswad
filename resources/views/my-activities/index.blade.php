<x-app-layout>
    {{-- Notifikasi Sesi --}}
    @if(session('error') || session('success') || session('warning'))
        @php
            $type = session('error') ? 'error' : (session('success') ? 'success' : 'warning');
            $message = session($type);
            $colors = [
                'error' => 'bg-red-100 border-red-300 text-red-700',
                'success' => 'bg-green-100 border-green-300 text-green-700',
                'warning' => 'bg-yellow-100 border-yellow-300 text-yellow-700',
            ];
            $bgColor = $colors[$type];
            $id = $type . 'MessageDashboard';
        @endphp
        <div id="{{ $id }}" class="{{ $bgColor }} p-4 rounded-lg mb-6 relative text-sm border transition-opacity duration-300">
            <span>{{ $message }}</span>
            <button type="button" class="absolute top-1/2 right-3 transform -translate-y-1/2 font-semibold text-xl" onclick="document.getElementById('{{ $id }}').style.display='none'">&times;</button>
        </div>
    @endif
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Daftar Kegiatan Kampus</h1>
        <p class="text-gray-600 mt-1">Jelajahi semua kegiatan menarik yang dipublikasikan oleh UKM & Ormawa.</p>
    </div>
    <div class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-4 sm:space-x-6" aria-label="Tabs">
            <button id="tab-upcoming"
                    class="tab-button whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-sm text-indigo-600 border-indigo-500"
                    aria-current="page" onclick="showTab('upcoming')">
                Semua Kegiatan
            </button>
            <button id="tab-history"
                    class="tab-button whitespace-nowrap pb-3 px-1 border-b-2 font-semibold text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 border-transparent"
                    onclick="showTab('history')">
                Riwayat Partisipasi Saya
            </button>
        </nav>
    </div>

    <div>
        <div id="content-upcoming" class="tab-content">
            @if(count($upcoming_activities) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($upcoming_activities as $activity)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
                            <a href="{{ route('activities.public.show', $activity->id) }}"> 
                                <img class="w-full h-44 object-cover" src="{{ $activity->image_url }}" alt="Gambar {{ $activity->name }}">
                            </a>
                            <div class="p-5 flex flex-col flex-grow">
                                <div class="mb-2">
                                    <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-800 self-start">{{ $activity->type ?? 'Kegiatan' }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-800 mb-1">
                                     <a href="{{ route('activities.public.show', $activity->id) }}" class="hover:text-indigo-600 transition-colors">{{ $activity->name }}</a>
                                </h3>
                                <p class="text-sm text-gray-500 mb-1"><span class="font-medium">Oleh:</span> {{ $activity->organizer }}</p>
                                <p class="text-sm text-gray-500 mb-1 flex items-center">
                                    <span class="material-icons text-base mr-1.5 text-gray-400">calendar_today</span>
                                    {{ \Carbon\Carbon::parse($activity->date_start)->locale('id')->translatedFormat('d M Y') }}
                                    @if($activity->date_start != $activity->date_end && !empty($activity->date_end))
                                    - {{ \Carbon\Carbon::parse($activity->date_end)->locale('id')->translatedFormat('d M Y') }}
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 mb-1 flex items-center">
                                    <span class="material-icons text-base mr-1.5 text-gray-400">schedule</span>
                                    {{ $activity->time_start }} - {{ $activity->time_end }} WIB
                                </p>
                                <p class="text-sm text-gray-500 mb-3 flex items-center">
                                    <span class="material-icons text-base mr-1.5 text-gray-400">location_on</span>
                                    {{ $activity->location }}
                                </p>

                                <div class="mt-auto pt-4 border-t border-gray-200 text-center space-y-2">
                                    <a href="{{ route('activities.public.show', $activity->id) }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                        Lihat Detail Lengkap
                                    </a>
                                    @if (Auth::check())
                                        @if ($activity->is_registered)
                                            <div class="mt-2">
                                                <span class="px-3 py-1.5 text-sm font-semibold rounded-full 
                                                    @if($activity->status_keikutsertaan == 'Hadir') bg-green-100 text-green-700 
                                                    @elseif($activity->status_keikutsertaan == 'Terdaftar') bg-blue-100 text-blue-700
                                                    @else bg-gray-100 text-gray-700 @endif">
                                                    <span class="material-icons text-base align-middle mr-1">
                                                        {{ $activity->status_keikutsertaan == 'Hadir' ? 'check_circle' : ($activity->status_keikutsertaan == 'Terdaftar' ? 'event_available' : 'info') }}
                                                    </span>
                                                    {{ $activity->status_keikutsertaan }}
                                                </span>
                                                @if($activity->status_keikutsertaan == 'Terdaftar' && $activity->is_upcoming)
                                                <form action="{{ route('activities.unregister', $activity->id) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Anda yakin ingin membatalkan pendaftaran kegiatan ini?')">
                                                    @csrf
                                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 hover:underline">(Batal Ikut)</button>
                                                </form>
                                                @endif
                                            </div>
                                        @elseif ($activity->registration_is_open && $activity->is_upcoming)
                                            <form action="{{ route('activities.register', $activity->id) }}" method="POST" class="mt-2">
                                                @csrf
                                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                                    <span class="material-icons text-base mr-1.5">how_to_reg</span>
                                                    Ikuti Kegiatan Ini
                                                </button>
                                            </form>
                                        @else
                                            <div class="mt-2">
                                                <button disabled class="w-full cursor-not-allowed inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-200 rounded-md">
                                                    <span class="material-icons text-base mr-1.5">lock</span>
                                                    Pendaftaran Ditutup
                                                </button>
                                            </div>
                                        @endif
                                    @else 
                                        <div class="mt-2">
                                            <a href="{{ route('login', ['redirect' => route('activities.public.show', $activity->id) ]) }}" class="w-full inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-500 rounded-md hover:bg-gray-600 transition-colors">
                                                Login untuk Ikut
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                 <div class="text-center py-12 bg-white rounded-lg shadow-md">
                    <span class="material-icons text-6xl text-gray-400 mb-3">event_busy</span>
                    <p class="text-xl text-gray-600">Belum ada kegiatan yang dipublikasikan atau akan datang.</p>
                </div>
            @endif
        </div>

        <div id="content-history" class="tab-content hidden">
             @if(count($past_activities) > 0)
                <div class="bg-white rounded-lg shadow-md overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penyelenggara</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Partisipasi Anda</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($past_activities as $activity)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('activities.public.show', $activity->id) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $activity->name }}</a>
                                        <div class="text-xs text-gray-500">{{ $activity->type ?? 'Kegiatan' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $activity->organizer }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($activity->date_end ?? $activity->date_start)->locale('id')->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if(Str::lower($activity->status_keikutsertaan) == 'hadir') bg-green-100 text-green-800
                                            @elseif(Str::lower($activity->status_keikutsertaan) == 'absen' || Str::lower($activity->status_keikutsertaan) == 'tidak hadir') bg-red-100 text-red-700
                                            @elseif(Str::lower($activity->status_keikutsertaan) == 'izin') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $activity->status_keikutsertaan }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-lg shadow-md">
                    <span class="material-icons text-6xl text-gray-400 mb-3">history_toggle_off</span>
                    <p class="text-xl text-gray-600">Anda belum memiliki riwayat partisipasi kegiatan.</p>
                </div>
            @endif
        </div>
    </div>
@push('scripts')
<script>
    function showTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.classList.add('hidden');
        });
        document.querySelectorAll('.tab-button').forEach(function(button) {
            button.classList.remove('text-indigo-600', 'border-indigo-500');
            button.classList.add('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'border-transparent');
            button.removeAttribute('aria-current');
        });

        document.getElementById('content-' + tabName).classList.remove('hidden');
        const activeButton = document.getElementById('tab-' + tabName);
        activeButton.classList.add('text-indigo-600', 'border-indigo-500');
        activeButton.classList.remove('text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'border-transparent');
        activeButton.setAttribute('aria-current', 'page');
    }
</script>
@endpush
</x-app-layout>