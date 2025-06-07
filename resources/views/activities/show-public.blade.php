<x-app-layout>
<div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
    {{-- Tombol Kembali --}}
    <div class="mb-6">
        <a href="{{ route('my-activities.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors group text-sm font-medium">
            <span class="material-icons mr-1.5 group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Kembali ke Daftar Kegiatan Kampus
        </a>
    </div>

    {{-- Notifikasi Session --}}
    @if(session('success'))
        <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg shadow" role="alert" id="successMessageDetailActivity">
            {{ session('success') }}
            <button type="button" class="float-right font-semibold text-lg leading-none" onclick="document.getElementById('successMessageDetailActivity').style.display='none'">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg shadow" role="alert" id="errorMessageDetailActivity">
            {{ session('error') }}
            <button type="button" class="float-right font-semibold text-lg leading-none" onclick="document.getElementById('errorMessageDetailActivity').style.display='none'">&times;</button>
        </div>
    @endif
    @if(session('warning'))
        <div class="mb-6 p-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg shadow" role="alert" id="warningMessageDetailActivity">
            {{ session('warning') }}
            <button type="button" class="float-right font-semibold text-lg leading-none" onclick="document.getElementById('warningMessageDetailActivity').style.display='none'">&times;</button>
        </div>
    @endif


    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        @if($activity->image_banner_url)
            <img src="{{ asset('storage/' . $activity->image_banner_url) }}" alt="Banner {{ $activity->name }}" class="w-full h-56 md:h-72 object-cover">
        @else
            <div class="w-full h-56 md:h-72 bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                <span class="material-icons text-white text-6xl opacity-50">event_note</span>
            </div>
        @endif

        <div class="p-6 md:p-8">
            <div class="mb-4">
                <span class="inline-block bg-{{ $activity->ukmOrmawa && $activity->ukmOrmawa->type === 'UKM' ? 'blue' : 'green' }}-100 text-{{ $activity->ukmOrmawa && $activity->ukmOrmawa->type === 'UKM' ? 'blue' : 'green' }}-800 text-xs font-semibold px-2.5 py-1 rounded-full uppercase tracking-wider">
                    {{ $activity->type ?? 'Kegiatan Umum' }}
                </span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">{{ $activity->name }}</h1>
            
            <div class="text-sm text-gray-600 mb-6 space-y-1">
                <p class="flex items-center">
                    <span class="material-icons text-base mr-2 text-gray-500">corporate_fare</span>
                    Penyelenggara: 
                    @if($activity->ukmOrmawa)
                        <a href="{{ route('ukm-ormawa.show', $activity->ukmOrmawa->slug) }}" class="ml-1 text-indigo-600 hover:underline">{{ $activity->ukmOrmawa->name }}</a>
                    @else
                        <span class="ml-1">Informasi Penyelenggara Tidak Tersedia</span>
                    @endif
                </p>
                <p class="flex items-center">
                    <span class="material-icons text-base mr-2 text-gray-500">calendar_today</span>
                    Tanggal: 
                    <span class="ml-1 font-medium">
                        {{ $activity->date_start->translatedFormat('l, d F Y') }}
                        @if($activity->date_end && $activity->date_end->format('Y-m-d') !== $activity->date_start->format('Y-m-d'))
                            - {{ $activity->date_end->translatedFormat('l, d F Y') }}
                        @endif
                    </span>
                </p>
                <p class="flex items-center">
                    <span class="material-icons text-base mr-2 text-gray-500">schedule</span>
                    Waktu: <span class="ml-1 font-medium">{{ $activity->time_start }} - {{ $activity->time_end }} WIB</span>
                </p>
                <p class="flex items-center">
                    <span class="material-icons text-base mr-2 text-gray-500">location_on</span>
                    Lokasi: <span class="ml-1 font-medium">{{ $activity->location }}</span>
                </p>
                 @if($registrationIsOpenForActivity && $activity->registration_deadline_activity)
                <p class="flex items-center text-red-600">
                    <span class="material-icons text-base mr-2">timer</span>
                    Batas Pendaftaran: <span class="ml-1 font-medium">{{ $activity->registration_deadline_activity->translatedFormat('l, d F Y, HH:mm') }} WIB</span>
                </p>
                @endif
            </div>

            {{-- Tombol Aksi Pendaftaran/Status Partisipasi --}}
            @if(Auth::check())
                @if ($isRegistered)
                    <div class="mb-6 p-4 {{ $userParticipationStatus == 'Hadir' ? 'bg-green-50 border-green-500 text-green-700' : ($userParticipationStatus == 'Terdaftar' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'bg-gray-100 border-gray-300 text-gray-700') }} border-l-4 rounded-r-md">
                        <p class="font-semibold flex items-center">
                            <span class="material-icons text-lg mr-2">
                                @if($userParticipationStatus == 'Hadir') check_circle
                                @elseif($userParticipationStatus == 'Terdaftar') event_available
                                @elseif($userParticipationStatus == 'Absen') cancel
                                @elseif($userParticipationStatus == 'Izin') error_outline
                                @else info @endif
                            </span>
                            Status Partisipasi Anda: {{ ucfirst($userParticipationStatus) }}
                        </p>
                    </div>
                    @if($canUnregister)
                        <form action="{{ route('activities.unregister', $activity->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin membatalkan pendaftaran untuk kegiatan ini?')" class="mb-8">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                <span class="material-icons text-base mr-1.5">event_busy</span>
                                Batalkan Pendaftaran
                            </button>
                        </form>
                    @endif
                @elseif ($registrationIsOpenForActivity)
                    <form action="{{ route('activities.register', $activity->id) }}" method="POST" class="mb-8">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors transform hover:scale-105">
                            Ikuti Kegiatan Ini
                            <span class="material-icons ml-2">how_to_reg</span>
                        </button>
                    </form>
@elseif (($activity->date_end ?? $activity->date_start)->isPast())                     <div class="mb-6 p-3 bg-gray-100 border border-gray-300 text-gray-600 rounded-md text-sm">
                        Kegiatan ini telah selesai.
                    </div>
                @else
                    <div class="mb-6 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700 rounded-r-md text-sm">
                        Pendaftaran untuk kegiatan ini belum dibuka atau sudah ditutup.
                    </div>
                @endif
            @else
                <div class="mb-8">
                    <a href="{{ route('login', ['redirect' => route('activities.public.show', $activity->id)]) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-gray-500 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-colors">
                        <span class="material-icons text-lg mr-2">login</span>
                        Login untuk Mengikuti Kegiatan
                    </a>
                </div>
            @endif
            
            <div class="prose prose-indigo max-w-none text-gray-700 leading-relaxed">
                <h2 class="text-xl font-semibold text-gray-800 mt-0 mb-3 not-prose">Deskripsi Kegiatan</h2>
                <div class="text-justify">
                    {!! nl2br(e($activity->description)) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        ['successMessageDetailActivity', 'errorMessageDetailActivity', 'warningMessageDetailActivity'].forEach(id => {
            const el = document.getElementById(id);
            if(el) {
                setTimeout(() => {
                    if(el) { 
                        el.style.transition = 'opacity 0.5s ease';
                        el.style.opacity = '0';
                        setTimeout(() => { if(el) el.style.display = 'none'; }, 500);
                    }
                }, 7000);
            }
        });
    });
</script>
@endpush
</x-app-layout>