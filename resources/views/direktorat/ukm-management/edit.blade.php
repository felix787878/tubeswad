<x-direktorat-app-layout>
    <x-slot name="header">
        Edit Profil UKM/Ormawa (Direktorat): {{ $ukmOrmawa->name }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
             <div class="mb-6">
                <a href="{{ route('direktorat.ukm-ormawa.show', $ukmOrmawa->id) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <span class="material-icons mr-1">arrow_back</span>
                    Kembali ke Detail Verifikasi
                </a>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Profil UKM/Ormawa: {{ $ukmOrmawa->name }}</h1>
                    <p class="text-gray-600 mb-6">Anda dapat mengubah semua detail UKM/Ormawa ini sebagai Admin Direktorat.</p>

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded-md bg-green-100 border border-green-300 text-green-700 text-sm">
                            {{ session('success') }}
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

                    <form method="POST" action="{{ route('direktorat.ukm-ormawa.update', $ukmOrmawa->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Karena ini form edit --}}

                        {{-- Informasi Dasar (Sama seperti form pengurus) --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Dasar</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block font-medium text-sm text-gray-700">Nama UKM/Ormawa <span class="text-red-500">*</span></label>
                                    <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="name" value="{{ old('name', $ukmOrmawa->name) }}" required />
                                </div>
                                <div>
                                    <label for="type" class="block font-medium text-sm text-gray-700">Tipe <span class="text-red-500">*</span></label>
                                    <select name="type" id="type" required class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                        <option value="UKM" {{ old('type', $ukmOrmawa->type) == 'UKM' ? 'selected' : '' }}>UKM</option>
                                        <option value="Ormawa" {{ old('type', $ukmOrmawa->type) == 'Ormawa' ? 'selected' : '' }}>Ormawa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label for="category" class="block font-medium text-sm text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <input id="category" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="category" value="{{ old('category', $ukmOrmawa->category) }}" required />
                            </div>
                        </div>

                        {{-- Detail (Deskripsi, Visi, Misi - Sama seperti form pengurus) --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail UKM/Ormawa</h3>
                             <div class="mt-4">
                                <label for="description_short" class="block font-medium text-sm text-gray-700">Deskripsi Singkat</label>
                                <textarea id="description_short" name="description_short" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('description_short', $ukmOrmawa->description_short) }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label for="description_full" class="block font-medium text-sm text-gray-700">Deskripsi Lengkap</label>
                                <textarea id="description_full" name="description_full" rows="6" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('description_full', $ukmOrmawa->description_full) }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label for="visi" class="block font-medium text-sm text-gray-700">Visi</label>
                                <textarea id="visi" name="visi" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('visi', $ukmOrmawa->visi) }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label for="misi_input" class="block font-medium text-sm text-gray-700">Misi (pisahkan baris baru)</label>
                                <textarea id="misi_input" name="misi_input" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('misi_input', is_array($ukmOrmawa->misi) ? implode("\n", $ukmOrmawa->misi) : $ukmOrmawa->misi) }}</textarea>
                            </div>
                        </div>

                        {{-- Kontak (Sama seperti form pengurus) --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                             <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kontak</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="contact_email" class="block font-medium text-sm text-gray-700">Email Kontak</label>
                                    <input id="contact_email" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="email" name="contact_email" value="{{ old('contact_email', $ukmOrmawa->contact_email) }}" />
                                </div>
                                <div>
                                    <label for="contact_instagram" class="block font-medium text-sm text-gray-700">Instagram</label>
                                    <input id="contact_instagram" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="contact_instagram" value="{{ old('contact_instagram', $ukmOrmawa->contact_instagram) }}" />
                                </div>
                            </div>
                        </div>

                        {{-- Gambar (Sama seperti form pengurus, dengan preview) --}}
                         <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Logo & Banner</h3>
                            {{-- Logo --}}
                            <div class="mb-4">
                                <label for="logo_url_file" class="block font-medium text-sm text-gray-700 mb-1">Ganti Logo (Max 2MB)</label>
                                <input id="logo_url_file" type="file" name="logo_url_file" accept="image/*" onchange="previewImage(event, 'logo')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                @if($ukmOrmawa->logo_url)
                                    <p class="mt-2 text-xs text-gray-500">Logo saat ini:</p>
                                    <img src="{{ asset('storage/' . $ukmOrmawa->logo_url) }}" alt="Logo saat ini" class="h-20 w-auto rounded mt-1 border">
                                @endif
                                <div id="logoPreviewContainer" class="mt-2 hidden">
                                    <p class="text-xs text-gray-500">Preview logo baru:</p>
                                    <img id="logoPreview" class="h-20 w-auto rounded mt-1 border" src="#" alt="Preview Logo"/>
                                </div>
                            </div>
                            {{-- Banner --}}
                            <div class="mt-6">
                                <label for="banner_url_file" class="block font-medium text-sm text-gray-700 mb-1">Ganti Banner (Max 4MB)</label>
                                <input id="banner_url_file" type="file" name="banner_url_file" accept="image/*" onchange="previewImage(event, 'banner')" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
                                 @if($ukmOrmawa->banner_url)
                                    <p class="mt-2 text-xs text-gray-500">Banner saat ini:</p>
                                    <img src="{{ asset('storage/' . $ukmOrmawa->banner_url) }}" alt="Banner saat ini" class="w-full h-32 object-cover rounded mt-1 border">
                                @endif
                                <div id="bannerPreviewContainer" class="mt-2 hidden">
                                    <p class="text-xs text-gray-500">Preview banner baru:</p>
                                    <img id="bannerPreview" class="w-full h-32 object-cover rounded mt-1 border" src="#" alt="Preview Banner"/>
                                </div>
                            </div>
                        </div>

                        {{-- Pengaturan Pendaftaran & Status oleh Direktorat --}}
                        <div class="mb-8 p-6 bg-gray-50 rounded-lg shadow-inner">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pengaturan Administratif</h3>
                            <div class="mt-4">
                                <label for="is_registration_open" class="inline-flex items-center">
                                    <input id="is_registration_open" type="checkbox" name="is_registration_open" value="1" {{ old('is_registration_open', $ukmOrmawa->is_registration_open) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-700">Buka Pendaftaran Anggota</span>
                                </label>
                            </div>
                            <div class="mt-4">
                                <label for="registration_deadline" class="block font-medium text-sm text-gray-700">Batas Akhir Pendaftaran</label>
                                <input id="registration_deadline" class="block mt-1 w-full md:w-1/2 rounded-md shadow-sm border-gray-300" type="date" name="registration_deadline" value="{{ old('registration_deadline', $ukmOrmawa->registration_deadline ? $ukmOrmawa->registration_deadline->format('Y-m-d') : '') }}" />
                            </div>
                            <div class="mt-6">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status UKM/Ormawa <span class="text-red-500">*</span></label>
                                <select name="status" id="status" required class="block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm">
                                    <option value="approved" {{ old('status', $ukmOrmawa->status) == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                                    <option value="pending_verification" {{ old('status', $ukmOrmawa->status) == 'pending_verification' ? 'selected' : '' }}>Menunggu Verifikasi (Pending)</option>
                                    <option value="needs_update" {{ old('status', $ukmOrmawa->status) == 'needs_update' ? 'selected' : '' }}>Perlu Revisi (Needs Update)</option>
                                    <option value="rejected" {{ old('status', $ukmOrmawa->status) == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                                </select>
                            </div>
                             <div class="mt-4">
                                <label for="verification_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Verifikasi</label>
                                <textarea name="verification_notes" id="verification_notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm">{{ old('verification_notes', $ukmOrmawa->verification_notes) }}</textarea>
                            </div>
                            {{-- Jika ingin mengganti pengurus --}}
                            <div class="mt-6">
                                @php
                                    $pengurusUsers = \App\Models\User::where('role', 'pengurus')->orderBy('name')->get();
                                @endphp
                                <label for="pengurus_id" class="block text-sm font-medium text-gray-700 mb-1">Pengurus Penanggung Jawab (Opsional)</label>
                                <select name="pengurus_id" id="pengurus_id" class="block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm">
                                    <option value="">-- Tidak Diubah / Tidak Ada --</option>
                                    @foreach($pengurusUsers as $pUser)
                                        <option value="{{ $pUser->id }}" {{ old('pengurus_id', $ukmOrmawa->pengurus_id) == $pUser->id ? 'selected' : '' }}>
                                            {{ $pUser->name }} ({{ $pUser->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Pilih untuk mengganti pengurus. Mengganti akan melepaskan UKM ini dari pengurus lama (jika ada).</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end mt-6">
                             <a href="{{ route('direktorat.ukm-ormawa.show', $ukmOrmawa->id) }}" class="px-4 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 mr-3">
                                Batal
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase hover:bg-indigo-700">
                                Simpan Perubahan (Direktorat)
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
            let previewElementId = (type === 'logo') ? 'logoPreview' : 'bannerPreview';
            let previewContainerId = (type === 'logo') ? 'logoPreviewContainer' : 'bannerPreviewContainer';
            
            const preview = document.getElementById(previewElementId);
            const previewContainer = document.getElementById(previewContainerId);

            if (file) {
                preview.src = URL.createObjectURL(file);
                previewContainer.classList.remove('hidden');
            } else {
                preview.src = '#';
                if (!document.querySelector(`#${previewElementId}`).dataset.hasInitial) { // Cek jika tidak ada gambar awal
                    previewContainer.classList.add('hidden');
                }
            }
        }
         // Tandai jika ada gambar awal untuk logika preview
        document.addEventListener('DOMContentLoaded', function() {
            ['logo', 'banner'].forEach(type => {
                const imgElement = document.getElementById(`${type}Preview`);
                if (imgElement && imgElement.src && imgElement.src !== window.location.href + '#') { // Cek jika src bukan hanya '#'
                    imgElement.dataset.hasInitial = 'true';
                }
            });
        });
    </script>
    @endpush
</x-direktorat-app-layout>