<x-pengurus-app-layout>
    <x-slot name="header">
        {{ __('Tambah Kegiatan Baru untuk ') . $ukmOrmawa->name }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">Formulir Kegiatan Baru</h2>
                    <p class="text-gray-600 mb-6">Isi detail kegiatan yang akan diselenggarakan.</p>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md text-sm">
                            <p class="font-semibold">Oops! Ada beberapa kesalahan:</p>
                            <ul class="list-disc list-inside ml-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pengurus.activities.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-6">
                            {{-- Nama Kegiatan --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Workshop Public Speaking">
                                @error('name') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                                <textarea name="description" id="description" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Jelaskan detail kegiatan, tujuan, manfaat, dll.">{{ old('description') }}</textarea>
                                @error('description') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Tanggal Mulai & Selesai --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="date_start" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Kegiatan<span class="text-red-500">*</span></label>
                                    <input type="date" name="date_start" id="date_start" value="{{ old('date_start') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3">
                                    @error('date_start') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="date_end" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Kegiatan (Opsional)</label>
                                    <input type="date" name="date_end" id="date_end" value="{{ old('date_end') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3">
                                    @error('date_end') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Waktu Mulai & Selesai --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="time_start" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                                    <input type="time" name="time_start" id="time_start" value="{{ old('time_start') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3">
                                    @error('time_start') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="time_end" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                                    <input type="time" name="time_end" id="time_end" value="{{ old('time_end') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3">
                                    @error('time_end') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- ====================================================================== --}}
                            {{-- LOKASI DAN ALAMAT SEKRETARIAT --}}
                            {{-- ====================================================================== --}}
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <h3 class="text-md font-semibold text-gray-700 mb-2">Lokasi dan Alamat Sekretariat</h3>
                                <div class="space-y-6">
                                    {{-- Lokasi Kegiatan --}}
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kegiatan <span class="text-red-500">*</span></label>
                                        <input type="text" name="location" id="location" value="{{ old('location') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Aula Gedung X, Zoom Meeting (Online)">
                                        @error('location') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Alamat Lengkap Sekretariat --}}
                                    <div>
                                        <label for="alamat_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Sekretariat <span class="text-red-500">*</span></label>
                                        <textarea name="alamat_lengkap" id="alamat_lengkap" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Jl. Telekomunikasi No. 1, Gedung UKM Ruang 101">{{ old('alamat_lengkap') }}</textarea>
                                        @error('alamat_lengkap') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Provinsi & Kab/Kota --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                                            <input type="text" name="provinsi" id="provinsi" value="{{ old('provinsi') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Jawa Barat">
                                            @error('provinsi') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="kabkota" class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota <span class="text-red-500">*</span></label>
                                            <input type="text" name="kabkota" id="kabkota" value="{{ old('kabkota') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Kabupaten Bandung">
                                            @error('kabkota') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    {{-- Kecamatan & Desa/Kelurahan --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="kecamatan" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                                            <input type="text" name="kecamatan" id="kecamatan" value="{{ old('kecamatan') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Bojongsoang">
                                            @error('kecamatan') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="desakel" class="block text-sm font-medium text-gray-700 mb-1">Desa/Kelurahan <span class="text-red-500">*</span></label>
                                            <input type="text" name="desakel" id="desakel" value="{{ old('desakel') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Sukapura">
                                            @error('desakel') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    
                                    {{-- Link Google Maps --}}
                                    <div>
                                        <label for="Maps_link" class="block text-sm font-medium text-gray-700 mb-1">Link Google Maps (Opsional)</label>
                                        <input type="url" name="Maps_link" id="Maps_link" value="{{ old('Maps_link') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="https://maps.app.goo.gl/contohlink">
                                        @error('Maps_link') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Tipe Kegiatan --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="type" id="type" value="{{ old('type') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3" placeholder="Contoh: Workshop, Seminar, Lomba, Pertemuan Rutin">
                                @error('type') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Image Banner --}}
                            <div>
                                <label for="image_banner" class="block text-sm font-medium text-gray-700 mb-1">Gambar Banner Kegiatan (Opsional, Max 4MB)</label>
                                <input type="file" name="image_banner" id="image_banner" accept="image/*" onchange="previewActivityImage(event)" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <div id="imageActivityPreviewContainer" class="mt-2 hidden">
                                    <p class="text-sm text-gray-500">Preview Gambar:</p>
                                    <img id="imageActivityPreview" class="max-h-48 w-auto rounded mt-1 border border-gray-200 shadow-sm" alt="Preview Banner Kegiatan"/>
                                </div>
                                @error('image_banner') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Status Publikasi Kegiatan --}}
                            <div class="mt-4">
                                <label for="is_published" class="inline-flex items-center">
                                    <input id="is_published" type="checkbox" name="is_published" value="1" {{ old('is_published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Publikasikan Kegiatan Ini</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-1">Jika dicentang, kegiatan akan terlihat oleh publik/mahasiswa. Biarkan tidak dicentang untuk menyimpannya sebagai draft.</p>
                            </div>

                            {{-- PENGATURAN PENDAFTARAN KEGIATAN --}}
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <h3 class="text-md font-semibold text-gray-700 mb-2">Pengaturan Pendaftaran untuk Kegiatan Ini</h3>
                                <div class="mt-4">
                                    <label for="is_registration_open" class="inline-flex items-center">
                                        <input id="is_registration_open" type="checkbox" name="is_registration_open" value="1" {{ old('is_registration_open') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="toggleDeadlineActivity(this.checked)">
                                        <span class="ml-2 text-sm text-gray-700">Buka Pendaftaran untuk Kegiatan Ini</span>
                                    </label>
                                    <p class="text-xs text-gray-500 mt-1">Jika dicentang, mahasiswa dapat mendaftar untuk mengikuti kegiatan ini (jika kegiatan juga dipublikasikan).</p>
                                    @error('is_registration_open') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div id="deadline_activity_container" class="{{ old('is_registration_open') ? '' : 'hidden' }} mt-4">
                                    <label for="registration_deadline_activity" class="block text-sm font-medium text-gray-700 mb-1">Batas Akhir Pendaftaran Kegiatan</label>
                                    <input type="datetime-local" name="registration_deadline_activity" id="registration_deadline_activity" value="{{ old('registration_deadline_activity') }}" class="mt-1 block w-full md:w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3">
                                    <p class="text-xs text-gray-500 mt-1">Tanggal dan waktu terakhir mahasiswa bisa mendaftar. Kosongkan jika tidak ada batas waktu. Wajib diisi jika pendaftaran dibuka.</p>
                                    @error('registration_deadline_activity') <span class="text-xs text-red-600 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end space-x-3">
                            <a href="{{ route('pengurus.activities.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <span class="material-icons text-base mr-1.5">save</span>
                                Simpan Kegiatan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function previewActivityImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imageActivityPreview');
            const previewContainer = document.getElementById('imageActivityPreviewContainer');

            if (file) {
                preview.src = URL.createObjectURL(file);
                previewContainer.classList.remove('hidden');
            } else {
                preview.src = '';
                previewContainer.classList.add('hidden');
            }
        }

        function toggleDeadlineActivity(isRegistrationOpen) {
            const deadlineContainer = document.getElementById('deadline_activity_container');
            const deadlineInput = document.getElementById('registration_deadline_activity');
            if (isRegistrationOpen) {
                deadlineContainer.classList.remove('hidden');
            } else {
                deadlineContainer.classList.add('hidden');
                deadlineInput.value = ''; 
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const registrationCheckbox = document.getElementById('is_registration_open');
            if (registrationCheckbox) {
                 toggleDeadlineActivity(registrationCheckbox.checked);
            }
        });
    </script>
    @endpush
</x-pengurus-app-layout>