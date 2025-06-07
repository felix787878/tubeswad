{{-- resources/views/pengurus/ukm-ormawa/edit.blade.php --}}
<x-pengurus-app-layout>
    <x-slot name="header">
        {{ __('Kelola Profil ' . $ukmOrmawa->name) }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800 mb-1">Kelola Profil UKM/Ormawa Anda</h1>
                            <p class="text-gray-600">Perbarui informasi {{ $ukmOrmawa->name }}.</p>
                        </div>
                        <div class="mt-1">
                            <span class="font-medium text-sm">Status Profil:</span>
                            @if($ukmOrmawa->status == 'approved')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Disetujui & Terpublikasi
                                </span>
                            @elseif($ukmOrmawa->status == 'pending_verification')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Verifikasi
                                </span>
                            @elseif($ukmOrmawa->status == 'rejected')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-700">
                                    Ditolak
                                </span>
                            @elseif($ukmOrmawa->status == 'needs_update')
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Perlu Revisi
                                </span>
                            @else
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ ucfirst(str_replace('_', ' ', $ukmOrmawa->status)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Perubahan pada detail penting mungkin memerlukan verifikasi ulang dari Admin Direktorat.</p>


                    {{-- Notifikasi Session --}}
                    @if(session('success') || session('error'))
                        @php
                            // ... (notifikasi session tetap sama) ...
                        @endphp
                    @endif

                    <form method="POST" action="{{ route('pengurus.ukm-ormawa.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ... (SEMUA FIELD FORM LAINNYA TETAP SAMA: Informasi Dasar, Detail, Kontak, Gambar) ... --}}
                        {{-- Bagian Informasi Dasar --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700">Nama UKM/Ormawa <span class="text-red-500">*</span></label>
                                    <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="name" value="{{ old('name', $ukmOrmawa->name) }}" required autofocus placeholder="Contoh: UKM Tari Saman" />
                                    @error('name') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700">Tipe <span class="text-red-500">*</span></label>
                                    <select name="type" id="type" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="UKM" {{ old('type', $ukmOrmawa->type) == 'UKM' ? 'selected' : '' }}>UKM</option>
                                        <option value="Ormawa" {{ old('type', $ukmOrmawa->type) == 'Ormawa' ? 'selected' : '' }}>Ormawa</option>
                                    </select>
                                    @error('type') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="category" class="block font-medium text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <input id="category" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="category" value="{{ old('category', $ukmOrmawa->category) }}" required placeholder="Contoh: Seni & Budaya, Olahraga" />
                                @error('category') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Deskripsi, Visi, Misi --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail UKM/Ormawa</h3>
                            <div class="mt-4">
                                <label for="description_short" class="block font-medium text-sm text-gray-700">Deskripsi Singkat (maks. 500 karakter)</label>
                                <textarea id="description_short" name="description_short" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Jelaskan secara singkat tentang UKM/Ormawa Anda.">{{ old('description_short', $ukmOrmawa->description_short) }}</textarea>
                                @error('description_short') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="mt-4">
                                <label for="description_full" class="block font-medium text-sm text-gray-700">Deskripsi Lengkap</label>
                                <textarea id="description_full" name="description_full" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan deskripsi lengkap tentang UKM/Ormawa Anda, sejarah, kegiatan umum, dll.">{{ old('description_full', $ukmOrmawa->description_full) }}</textarea>
                                @error('description_full') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="mt-4">
                                <label for="visi" class="block font-medium text-sm text-gray-700">Visi</label>
                                <textarea id="visi" name="visi" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan visi UKM/Ormawa Anda.">{{ old('visi', $ukmOrmawa->visi) }}</textarea>
                                @error('visi') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="mt-4">
                                <label for="misi_input" class="block font-medium text-sm text-gray-700">Misi (pisahkan tiap misi dengan baris baru)</label>
                                <textarea id="misi_input" name="misi_input" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan setiap poin misi dalam baris baru. Contoh:&#10;- Melestarikan budaya&#10;- Mengembangkan kreativitas">{{ old('misi_input', is_array($ukmOrmawa->misi) ? implode("\n", $ukmOrmawa->misi) : ($ukmOrmawa->misi ?? '')) }}</textarea>
                                @error('misi_input') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Kontak --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontak</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_email" class="block font-medium text-sm text-gray-700">Email Kontak</label>
                                    <input id="contact_email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="contact_email" value="{{ old('contact_email', $ukmOrmawa->contact_email) }}" placeholder="Contoh: info@ukmanda.com" />
                                    @error('contact_email') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="contact_instagram" class="block font-medium text-sm text-gray-700">Instagram Kontak (misal: @ukm_connect)</label>
                                    <input id="contact_instagram" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="contact_instagram" value="{{ old('contact_instagram', $ukmOrmawa->contact_instagram) }}" placeholder="Contoh: @ukmtarisaman" />
                                    @error('contact_instagram') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Lokasi dengan Pencarian API --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Lokasi UKM/Ormawa</h3>

                            {{-- Input untuk Pencarian Alamat --}}
                            <div class="mt-4">
                                <label for="location_search" class="block font-medium text-sm text-gray-700">Cari Alamat (Ketik Nama Kecamatan/Desa)</label>
                                <div class="relative">
                                    <input id="location_search" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="alamat_lengkap" value="{{ old('alamat_lengkap', $ukmOrmawa->alamat_lengkap ?? '') }}" placeholder="Contoh: Soreang" autocomplete="off" />
                                    <div id="search-spinner" class="absolute top-1/2 right-3 transform -translate-y-1/2 hidden">
                                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-gray-900"></div>
                                    </div>
                                </div>
                                <div id="search-results" class="mt-2 border border-gray-300 rounded-md bg-white shadow-lg max-h-60 overflow-y-auto hidden">
                                    {{-- Hasil pencarian API akan ditampilkan di sini --}}
                                </div>
                                @error('alamat_lengkap') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            {{-- Hidden Inputs untuk menyimpan data terstruktur --}}
                            <input type="hidden" name="provinsi" id="provinsi" value="{{ old('provinsi', $ukmOrmawa->provinsi ?? '') }}">
                            <input type="hidden" name="kabkota" id="kabkota" value="{{ old('kabkota', $ukmOrmawa->kabkota ?? '') }}">
                            <input type="hidden" name="kecamatan" id="kecamatan" value="{{ old('kecamatan', $ukmOrmawa->kecamatan ?? '') }}">
                            <input type="hidden" name="desakel" id="desakel" value="{{ old('desakel', $ukmOrmawa->desakel ?? '') }}">
                            
                            {{-- Input untuk Google Maps Link (tetap sama) --}}
                            <div class="mt-6">
                                <label for="Maps_link" class="block font-medium text-sm text-gray-700">Link Google Maps (Opsional)</label>
                                <input id="Maps_link" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="url" name="Maps_link" value="{{ old('Maps_link', $ukmOrmawa->Maps_link ?? '') }}" placeholder="Contoh: https://maps.app.goo.gl/contohlink123" />
                                @error('Maps_link') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Gambar --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo & Banner</h3>
                            <div class="mb-4">
                                <label for="logo_url_file" class="block font-medium text-sm text-gray-700 mb-2">Logo UKM/Ormawa (Max 2MB)</label>
                                <input id="logo_url_file" type="file" name="logo_url_file" accept="image/*" onchange="previewImage(event, 'logo')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                @if($ukmOrmawa->logo_url)
                                    <div class="mt-2 text-sm text-gray-500">
                                        Logo Saat Ini:
                                        <img src="{{ asset('storage/' . $ukmOrmawa->logo_url) }}" alt="Logo Saat Ini" class="h-20 w-auto rounded mt-1 border border-gray-200 shadow-sm">
                                    </div>
                                @endif
                                <div id="logoPreviewContainer" class="mt-2 @if(!old('logo_url_file')) hidden @endif">
                                    <p class="text-sm text-gray-500">Preview Logo Baru:</p>
                                    <img id="logoPreview" class="h-20 w-auto rounded mt-1 border border-gray-200 shadow-sm" src="{{ old('logo_url_file') ? '#' : '' }}" alt="Preview Logo" />
                                </div>
                                @error('logo_url_file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>

                            <div class="mt-6">
                                <label for="banner_url_file" class="block font-medium text-sm text-gray-700 mb-2">Banner UKM/Ormawa (Max 4MB)</label>
                                <input id="banner_url_file" type="file" name="banner_url_file" accept="image/*" onchange="previewImage(event, 'banner')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                @if($ukmOrmawa->banner_url)
                                    <div class="mt-2 text-sm text-gray-500">
                                        Banner Saat Ini:
                                        <img src="{{ asset('storage/' . $ukmOrmawa->banner_url) }}" alt="Banner Saat Ini" class="w-full h-32 object-cover rounded mt-1 border border-gray-200 shadow-sm">
                                    </div>
                                @endif
                                <div id="bannerPreviewContainer" class="mt-2 @if(!old('banner_url_file')) hidden @endif">
                                    <p class="text-sm text-gray-500">Preview Banner Baru:</p>
                                    <img id="bannerPreview" class="w-full h-32 object-cover rounded mt-1 border border-gray-200 shadow-sm" src="{{ old('banner_url_file') ? '#' : '' }}" alt="Preview Banner" />
                                </div>
                                @error('banner_url_file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Pengaturan Pendaftaran (HAPUS is_visible DARI SINI) --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Pendaftaran Anggota</h3>
                            <div class="mt-4">
                                <label for="is_registration_open" class="inline-flex items-center">
                                    <input id="is_registration_open" type="checkbox" name="is_registration_open" value="1" {{ old('is_registration_open', $ukmOrmawa->is_registration_open) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Buka Pendaftaran Anggota</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1 ml-6">Jika dicentang, pendaftaran anggota untuk UKM/Ormawa ini akan dibuka di halaman publik.</p>
                            </div>

                            <div class="mt-4 ml-6">
                                <label for="registration_deadline" class="block font-medium text-sm text-gray-700">Batas Akhir Pendaftaran (Opsional)</label>
                                <input id="registration_deadline" class="block mt-1 w-full md:w-1/2 rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="date" name="registration_deadline" value="{{ old('registration_deadline', $ukmOrmawa->registration_deadline ? $ukmOrmawa->registration_deadline->format('Y-m-d') : '') }}" />
                                @error('registration_deadline') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                <p class="text-xs text-gray-500 mt-1">Tanggal terakhir pendaftar dapat mengajukan permohonan. Kosongkan jika tidak ada batas waktu.</p>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                <span class="material-icons text-base mr-2">save</span>
                                Simpan Perubahan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchApiUrl = "{{ route('api.alamat.search') }}";

        const searchInput = document.getElementById('location_search');
        const resultsContainer = document.getElementById('search-results');
        const spinner = document.getElementById('search-spinner');

        // Hidden fields
        const provinsiInput = document.getElementById('provinsi');
        const kabkotaInput = document.getElementById('kabkota');
        const kecamatanInput = document.getElementById('kecamatan');
        const desakelInput = document.getElementById('desakel');
        
        const debounce = (func, delay) => {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), delay);
            };
        };

        const fetchAddress = async (keyword) => {
            if (keyword.length < 3) {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');
            resultsContainer.classList.remove('hidden');
            resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Mencari...</div>';

            try {
                const response = await fetch(`${searchApiUrl}?keyword=${keyword}`);
                const data = await response.json();

                spinner.classList.add('hidden');
                resultsContainer.innerHTML = ''; 

                if (data.status === 200 && data.result.length > 0) {
                    const uniqueResults = [];
                    const trackedKeys = new Set(); // Menggunakan Set untuk performa yang lebih baik

                    data.result.forEach(item => {
                        // Membuat kunci unik dari kombinasi alamat
                        const key = `${item.desakel}|${item.kecamatan}|${item.kabkota}|${item.provinsi}`;
                        
                        // Jika kunci ini belum pernah ada, tambahkan ke hasil unik
                        if (!trackedKeys.has(key)) {
                            trackedKeys.add(key);
                            uniqueResults.push(item);
                        }
                    });
                    // Cek jika ada hasil setelah difilter
                    if(uniqueResults.length === 0) {
                        resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Alamat tidak ditemukan.</div>';
                        return;
                    }

                    // Gunakan hasil yang sudah unik (uniqueResults) untuk ditampilkan
                    uniqueResults.forEach(item => {
                        const resultItem = document.createElement('div');
                        resultItem.className = 'p-3 hover:bg-indigo-50 cursor-pointer border-b border-gray-200 text-sm';
                        
                        let displayAddress = `${item.desakel}, ${item.kecamatan}, ${item.kabkota}, ${item.provinsi}`;
                        resultItem.textContent = displayAddress;

                        resultItem.addEventListener('click', () => {
                            searchInput.value = displayAddress;
                            provinsiInput.value = item.provinsi;
                            kabkotaInput.value = item.kabkota;
                            kecamatanInput.value = item.kecamatan;
                            desakelInput.value = item.desakel;
                            resultsContainer.classList.add('hidden');
                        });
                        resultsContainer.appendChild(resultItem);
                    });

                } else {
                    resultsContainer.innerHTML = '<div class="p-3 text-sm text-gray-500">Alamat tidak ditemukan.</div>';
                }
            } catch (error) {
                spinner.classList.add('hidden');
                resultsContainer.innerHTML = '<div class="p-3 text-sm text-red-500">Terjadi kesalahan saat memuat data.</div>';
                console.error('API Fetch Error:', error);
            }
        };

        searchInput.addEventListener('keyup', debounce((e) => {
            fetchAddress(e.target.value);
        }, 500)); 
    });
</script>
@endpush
</x-pengurus-app-layout>