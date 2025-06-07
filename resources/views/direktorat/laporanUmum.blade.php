{{-- resources/views/direktorat/ukm-management/show.blade.php --}}
<x-direktorat-app-layout>
    <x-slot name="header">
        Detail Verifikasi: {{ $ukmOrmawa->name }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('direktorat.ukm-ormawa.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <span class="material-icons mr-1">arrow_back</span>
                    Kembali ke Daftar UKM/Ormawa
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg shadow" role="alert" id="successMessageShow">
                    {{ session('success') }}
                    <button type="button" class="float-right font-semibold text-lg leading-none" onclick="document.getElementById('successMessageShow').style.display='none'">&times;</button>
                </div>
            @endif
             @if(session('error'))
                <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg shadow" role="alert" id="errorMessageShow">
                    {{ session('error') }}
                    <button type="button" class="float-right font-semibold text-lg leading-none" onclick="document.getElementById('errorMessageShow').style.display='none'">&times;</button>
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-lg overflow-hidden">
                {{-- Banner --}}
                @if($ukmOrmawa->banner_url)
                    <img src="{{ asset('storage/' . $ukmOrmawa->banner_url) }}" alt="Banner {{ $ukmOrmawa->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                        <span class="text-white text-2xl font-semibold">Banner Belum Diunggah</span>
                    </div>
                @endif

                <div class="p-6 md:p-8">
                    {{-- Header dengan Logo dan Nama (tetap sama) --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center mb-6">
                        @if($ukmOrmawa->logo_url)
                            <img src="{{ asset('storage/' . $ukmOrmawa->logo_url) }}" alt="Logo {{ $ukmOrmawa->name }}" class="w-24 h-24 object-contain rounded-lg border border-gray-200 shadow-md mr-0 mb-4 sm:mr-6 sm:mb-0">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 text-4xl font-bold mr-0 mb-4 sm:mr-6 sm:mb-0">
                                {{ strtoupper(substr($ukmOrmawa->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-800">{{ $ukmOrmawa->name }}</h1>
                            <div class="mt-1">
                                <span class="text-sm font-semibold px-2 py-0.5 rounded-full {{ $ukmOrmawa->type === 'UKM' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">{{ $ukmOrmawa->type }}</span>
                                <span class="ml-2 text-sm text-gray-600">Kategori: {{ $ukmOrmawa->category }}</span>
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Diajukan oleh: {{ $ukmOrmawa->pengurus->name ?? 'N/A' }} ({{ $ukmOrmawa->pengurus->email ?? 'N/A' }})
                            </div>
                                <div class="mt-1 text-sm text-gray-500">
                                Terakhir update profil: {{ ($ukmOrmawa->updated_at ?? $ukmOrmawa->created_at)->isoFormat('D MMMM YYYY, HH:mm') }}
                            </div>
                        </div>
                    </div>

                    {{-- Status Saat Ini dan Tombol CRUD (tetap sama) --}}
                     <div class="mb-6 p-4 bg-gray-50 rounded-md flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <span class="font-semibold text-gray-700">Status Saat Ini:</span>
                            @if($ukmOrmawa->status == 'approved')
                                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                            @elseif($ukmOrmawa->status == 'pending_verification')
                                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Verifikasi</span>
                            @elseif($ukmOrmawa->status == 'rejected')
                                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-700">Ditolak</span>
                            @elseif($ukmOrmawa->status == 'needs_update')
                                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-700">Perlu Revisi</span>
                            @else
                                <span class="ml-2 px-3 py-1 text-sm font-semibold rounded-full bg-gray-200 text-gray-700">{{ ucfirst(str_replace('_', ' ', $ukmOrmawa->status)) }}</span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('direktorat.ukm-ormawa.edit', $ukmOrmawa->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-yellow-500 rounded-md hover:bg-yellow-600 transition-colors">
                                <span class="material-icons text-base mr-1 align-middle">edit</span> Edit Data
                            </a>
                            <form action="{{ route('direktorat.ukm-ormawa.destroy', $ukmOrmawa->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus UKM/Ormawa ini secara permanen? Tindakan ini tidak dapat diurungkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                    <span class="material-icons text-base mr-1 align-middle">delete_forever</span> Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Detail Informasi (tetap sama) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 mb-8">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Deskripsi Singkat:</h3>
                            <p class="text-gray-600 text-sm">{{ $ukmOrmawa->description_short ?: '-' }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Visi:</h3>
                            <p class="text-gray-600 text-sm whitespace-pre-line">{{ $ukmOrmawa->visi ?: '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="font-semibold text-gray-700 mb-1">Deskripsi Lengkap:</h3>
                            <article class="prose prose-sm max-w-none text-gray-600 whitespace-pre-line">{{ $ukmOrmawa->description_full ?: '-' }}</article>
                        </div>
                        @if($ukmOrmawa->misi && count(array_filter((array) $ukmOrmawa->misi)) > 0)
                        <div class="md:col-span-2">
                            <h3 class="font-semibold text-gray-700 mb-1">Misi:</h3>
                            <ul class="list-disc list-inside text-gray-600 text-sm space-y-1">
                                @foreach((array) $ukmOrmawa->misi as $m)
                                    @if(!empty(trim($m))) <li>{{ $m }}</li> @endif
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Email Kontak:</h3>
                            <p class="text-gray-600 text-sm">{{ $ukmOrmawa->contact_email ?: '-' }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Instagram:</h3>
                            <p class="text-gray-600 text-sm">{{ $ukmOrmawa->contact_instagram ?: '-' }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-1">Pendaftaran Anggota:</h3>
                            <p class="text-gray-600 text-sm {{ $ukmOrmawa->is_registration_open ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}">
                                {{ $ukmOrmawa->is_registration_open ? 'Dibuka' : 'Ditutup' }}
                                @if($ukmOrmawa->is_registration_open && $ukmOrmawa->registration_deadline)
                                    (s/d {{ \Carbon\Carbon::parse($ukmOrmawa->registration_deadline)->isoFormat('D MMMM YYYY') }})
                                @endif
                            </p>
                        </div>
                        @if($ukmOrmawa->verification_notes)
                        <div class="md:col-span-2 p-3 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <h3 class="font-semibold text-yellow-800 mb-1">Catatan Verifikasi Terakhir dari Direktorat:</h3>
                            <p class="text-yellow-700 text-sm whitespace-pre-line">{{ $ukmOrmawa->verification_notes }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Form Aksi Verifikasi dengan Tombol Terpisah --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-700 mb-1">Tindakan Verifikasi</h2>
                        <p class="text-sm text-gray-500 mb-6">Pilih tindakan yang sesuai untuk profil UKM/Ormawa ini.</p>
                        
                        <div x-data="{ showNotesFor: '' }"> {{-- Alpine.js untuk toggle textarea catatan --}}
                            <div class="flex flex-wrap gap-3 items-start">
                                {{-- Tombol Setujui --}}
                                @if($ukmOrmawa->status !== 'approved')
                                <form action="{{ route('direktorat.ukm-ormawa.updateStatus', $ukmOrmawa->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Anda yakin ingin MENYETUJUI profil UKM/Ormawa ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <input type="hidden" name="verification_notes" value=""> {{-- Kosongkan catatan saat approve --}}
                                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 inline-flex items-center">
                                        <span class="material-icons text-base mr-1.5">check_circle</span>
                                        Setujui
                                    </button>
                                </form>
                                @endif

                                {{-- Tombol Minta Revisi --}}
                                @if($ukmOrmawa->status !== 'needs_update')
                                <button @click="showNotesFor = (showNotesFor === 'needs_update' ? '' : 'needs_update')" type="button" class="px-5 py-2.5 text-sm font-medium text-white bg-orange-500 rounded-md hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 inline-flex items-center">
                                    <span class="material-icons text-base mr-1.5">rate_review</span>
                                    Minta Revisi
                                </button>
                                @endif

                                {{-- Tombol Tolak --}}
                                @if($ukmOrmawa->status !== 'rejected')
                                <button @click="showNotesFor = (showNotesFor === 'rejected' ? '' : 'rejected')" type="button" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 inline-flex items-center">
                                    <span class="material-icons text-base mr-1.5">cancel</span>
                                    Tolak
                                </button>
                                @endif
                            </div>

                            {{-- Form untuk Minta Revisi (muncul kondisional) --}}
                            <form x-show="showNotesFor === 'needs_update'" x-transition action="{{ route('direktorat.ukm-ormawa.updateStatus', $ukmOrmawa->id) }}" method="POST" class="mt-4 p-4 border border-orange-300 rounded-md bg-orange-50">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="needs_update">
                                <label for="notes_needs_update" class="block text-sm font-medium text-orange-700 mb-1">Catatan untuk Revisi <span class="text-red-500">*</span></label>
                                <textarea name="verification_notes" id="notes_needs_update" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" placeholder="Jelaskan bagian mana yang perlu direvisi oleh pengurus...">{{ $ukmOrmawa->status === 'needs_update' ? $ukmOrmawa->verification_notes : old('verification_notes') }}</textarea>
                                <div class="mt-3">
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-md hover:bg-orange-600">Kirim Permintaan Revisi</button>
                                    <button @click="showNotesFor = ''" type="button" class="ml-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                                </div>
                            </form>

                            {{-- Form untuk Tolak (muncul kondisional) --}}
                            <form x-show="showNotesFor === 'rejected'" x-transition action="{{ route('direktorat.ukm-ormawa.updateStatus', $ukmOrmawa->id) }}" method="POST" class="mt-4 p-4 border border-red-300 rounded-md bg-red-50">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <label for="notes_rejected" class="block text-sm font-medium text-red-700 mb-1">Alasan Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="verification_notes" id="notes_rejected" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" placeholder="Jelaskan alasan penolakan profil UKM/Ormawa ini...">{{ $ukmOrmawa->status === 'rejected' ? $ukmOrmawa->verification_notes : old('verification_notes') }}</textarea>
                                 <div class="mt-3">
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Konfirmasi Penolakan</button>
                                    <button @click="showNotesFor = ''" type="button" class="ml-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function fadeOutAndHide(elementId) {
                    const element = document.getElementById(elementId);
                    if (element) {
                        setTimeout(() => {
                            element.style.transition = 'opacity 0.5s ease-out';
                            element.style.opacity = '0';
                            setTimeout(() => element.style.display = 'none', 500);
                        }, 7000); 
                    }
                }
                fadeOutAndHide('successMessageShow');
                fadeOutAndHide('errorMessageShow');
            });
        </script>
        @endpush
    </x-direktorat-app-layout>
    ```

