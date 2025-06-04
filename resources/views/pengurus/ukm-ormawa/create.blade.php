{{-- resources/views/pengurus/ukm-ormawa/create.blade.php --}}
<x-pengurus-app-layout>
    <x-slot name="header">
        {{ __('Buat Profil UKM/Ormawa Baru') }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Formulir Pembuatan Profil UKM/Ormawa</h1>
                    <p class="text-gray-600 mb-6">Lengkapi detail UKM/Ormawa yang akan Anda kelola. Setelah disimpan, profil akan diajukan untuk verifikasi oleh Admin Direktorat.</p>

                    @if(session('error'))
                        <div class="mb-6 p-4 rounded-md bg-red-100 border-red-300 text-red-700 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif
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


                    <form method="POST" action="{{ route('pengurus.ukm-ormawa.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Bagian Informasi Dasar --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700">Nama UKM/Ormawa <span class="text-red-500">*</span></label>
                                    <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Contoh: UKM Tari Saman" />
                                    @error('name') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700">Tipe <span class="text-red-500">*</span></label>
                                    <select name="type" id="type" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="" disabled {{ old('type') ? '' : 'selected' }}>Pilih Tipe</option>
                                        <option value="UKM" {{ old('type') == 'UKM' ? 'selected' : '' }}>UKM</option>
                                        <option value="Ormawa" {{ old('type') == 'Ormawa' ? 'selected' : '' }}>Ormawa</option>
                                    </select>
                                    @error('type') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="category" class="block font-medium text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <input id="category" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="category" value="{{ old('category') }}" required placeholder="Contoh: Seni & Budaya, Olahraga" />
                                @error('category') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Deskripsi, Visi, Misi --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                             <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail UKM/Ormawa</h3>
                            <div class="mt-4">
                                <label for="description_short" class="block font-medium text-sm text-gray-700">Deskripsi Singkat (maks. 500 karakter)</label>
                                <textarea id="description_short" name="description_short" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Jelaskan secara singkat tentang UKM/Ormawa Anda.">{{ old('description_short') }}</textarea>
                                @error('description_short') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                             <div class="mt-4">
                                <label for="description_full" class="block font-medium text-sm text-gray-700">Deskripsi Lengkap</label>
                                <textarea id="description_full" name="description_full" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan deskripsi lengkap tentang UKM/Ormawa Anda, sejarah, kegiatan umum, dll.">{{ old('description_full') }}</textarea>
                                @error('description_full') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                             <div class="mt-4">
                                <label for="visi" class="block font-medium text-sm text-gray-700">Visi</label>
                                <textarea id="visi" name="visi" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan visi UKM/Ormawa Anda.">{{ old('visi') }}</textarea>
                                @error('visi') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                             <div class="mt-4">
                                <label for="misi_input" class="block font-medium text-sm text-gray-700">Misi (pisahkan tiap misi dengan baris baru)</label>
                                <textarea id="misi_input" name="misi_input" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Tuliskan setiap poin misi dalam baris baru. Contoh:&#10;- Melestarikan budaya&#10;- Mengembangkan kreativitas">{{ old('misi_input') }}</textarea>
                                @error('misi_input') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Bagian Kontak --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontak</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_email" class="block font-medium text-sm text-gray-700">Email Kontak</label>
                                    <input id="contact_email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="Contoh: info@ukmanda.com" />
                                    @error('contact_email') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="contact_instagram" class="block font-medium text-sm text-gray-700">Instagram Kontak (misal: @ukm_connect)</label>
                                    <input id="contact_instagram" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" type="text" name="contact_instagram" value="{{ old('contact_instagram') }}" placeholder="Contoh: @ukmtarisaman" />
                                    @error('contact_instagram') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Bagian Gambar --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                             <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo & Banner</h3>
                            <div class="mb-4">
                                <label for="logo_url_file" class="block font-medium text-sm text-gray-700 mb-2">Logo UKM/Ormawa (Opsional, Max 2MB)</label>
                                <input id="logo_url_file" type="file" name="logo_url_file" accept="image/*" onchange="previewImage(event, 'logo')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                <div id="logoPreviewContainer" class="mt-2 hidden">
                                    <p class="text-sm text-gray-500">Preview Logo:</p>
                                    <img id="logoPreview" class="h-20 w-auto rounded mt-1 border border-gray-200 shadow-sm" src="#" alt="Preview Logo" />
                                </div>
                                @error('logo_url_file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                             <div class="mt-6">
                                <label for="banner_url_file" class="block font-medium text-sm text-gray-700 mb-2">Banner UKM/Ormawa (Opsional, Max 4MB)</label>
                                <input id="banner_url_file" type="file" name="banner_url_file" accept="image/*" onchange="previewImage(event, 'banner')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                <div id="bannerPreviewContainer" class="mt-2 hidden">
                                    <p class="text-sm text-gray-500">Preview Banner:</p>
                                    <img id="bannerPreview" class="w-full h-32 object-cover rounded mt-1 border border-gray-200 shadow-sm" src="#" alt="Preview Banner" />
                                </div>
                                @error('banner_url_file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        {{-- Tombol Aksi --}}
                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('pengurus.dashboard') }}" class="px-4 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-3">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg hover:shadow-xl">
                                <span class="material-icons text-base mr-2">add_circle_outline</span>
                                Buat & Ajukan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewImage(event, type) {
            const file = event.target.files[0];
            let previewElementId, previewContainerId;
            if (type === 'logo') {
                previewElementId = 'logoPreview';
                previewContainerId = 'logoPreviewContainer';
            } else if (type === 'banner') {
                previewElementId = 'bannerPreview';
                previewContainerId = 'bannerPreviewContainer';
            } else { return; }

            const preview = document.getElementById(previewElementId);
            const previewContainer = document.getElementById(previewContainerId);

            if (file) {
                preview.src = URL.createObjectURL(file);
                previewContainer.classList.remove('hidden');
            } else {
                preview.src = ''; 
                previewContainer.classList.add('hidden');
            }
        }
    </script>
    @endpush
</x-pengurus-app-layout>