<x-app-layout>
    <div class="container mx-auto">
        {{-- Tombol Kembali --}}
        <div class="mb-6">
            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('ukm-ormawa.show', ['slug' => $item->slug]) }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors group text-sm font-medium">
                <span class="material-icons mr-1.5 group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Kembali ke Detail {{ $item->name }}
            </a>
        </div>

        <div class="bg-white p-6 md:p-8 rounded-xl shadow-xl max-w-2xl mx-auto">
            <div class="text-center mb-8">
                @if(isset($item->logo_url) && $item->logo_url)
                <img src="{{ asset('storage/' . $item->logo_url) }}" alt="Logo {{ $item->name }}" class="w-28 h-28 object-contain mx-auto mb-4 rounded-lg shadow-md border border-gray-200">
                @else
                <span class="material-icons text-7xl text-gray-300 mx-auto mb-4">groups</span>
                @endif
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Formulir Pendaftaran</h1>
                <p class="text-xl text-red-600 font-semibold mt-1">{{ $item->name }}</p>
                <p class="text-sm text-gray-500 mt-2">Lengkapi data di bawah ini dengan cermat untuk bergabung.</p>
                @if($item->registration_deadline)
                    <p class="text-sm text-red-500 mt-1 font-medium">Batas akhir pendaftaran: {{ \Carbon\Carbon::parse($item->registration_deadline)->translatedFormat('d F Y') }}</p>
                @endif
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
                    <p class="font-semibold mb-2 text-red-800">Oops! Ada beberapa hal yang perlu diperbaiki:</p>
                    <ul class="list-disc list-inside pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('ukm-ormawa.apply.submit', ['ukm_ormawa_slug' => $item->slug]) }}" method="POST">
                @csrf
                <div class="space-y-6">
                    {{-- Bagian Alasan Bergabung dan Keahlian --}}
                    <div>
                        <label for="reason_to_join" class="block text-sm font-medium text-gray-700 mb-1">1. Alasan Bergabung dengan {{ $item->name }} <span class="text-red-500">*</span></label>
                        <textarea name="reason_to_join" id="reason_to_join" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('reason_to_join') border-red-500 @enderror" placeholder="Jelaskan motivasi utama Anda...">{{ old('reason_to_join') }}</textarea>
                        @error('reason_to_join') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="skills_experience" class="block text-sm font-medium text-gray-700 mb-1">2. Pengalaman Organisasi / Keahlian Relevan (Opsional)</label>
                        <textarea name="skills_experience" id="skills_experience" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('skills_experience') border-red-500 @enderror" placeholder="Sebutkan pengalaman atau keahlian...">{{ old('skills_experience') }}</textarea>
                        @error('skills_experience') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="phone_contact" class="block text-sm font-medium text-gray-700 mb-1">3. Nomor HP Aktif (WhatsApp) <span class="text-red-500">*</span></label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">📞</span>
                            </div>
                            <input type="tel" name="phone_contact" id="phone_contact" value="{{ old('phone_contact', Auth::user()->phone_number ?? '') }}" required class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('phone_contact') border-red-500 @enderror" placeholder="Contoh: 081234567890">
                        </div>
                        @error('phone_contact') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    {{-- Bagian Alamat Baru --}}
                    <div class="border-t border-gray-100 pt-6 mt-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800">4. Informasi Alamat Domisili Saat Ini</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi <span class="text-red-500">*</span></label>
                                <select name="province" id="province" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('province') border-red-500 @enderror">
                                    <option value="">-- Pilih Provinsi --</option>
                                </select>
                                @error('province') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten <span class="text-red-500">*</span></label>
                                <select name="city" id="city" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('city') border-red-500 @enderror" disabled>
                                    <option value="">-- Pilih Kota/Kabupaten --</option>
                                </select>
                                @error('city') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="district" class="block text-sm font-medium text-gray-700 mb-1">Kecamatan <span class="text-red-500">*</span></label>
                                <select name="district" id="district" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('district') border-red-500 @enderror" disabled>
                                    <option value="">-- Pilih Kecamatan --</option>
                                </select>
                                @error('district') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="village" class="block text-sm font-medium text-gray-700 mb-1">Kelurahan/Desa <span class="text-red-500">*</span></label>
                                <select name="village" id="village" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('village') border-red-500 @enderror" disabled>
                                    <option value="">-- Pilih Kelurahan/Desa --</option>
                                </select>
                                @error('village') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label for="full_address" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap (Jalan, Nomor Rumah, RT/RW, dsb.) <span class="text-red-500">*</span></label>
                            <textarea name="full_address" id="full_address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 sm:text-sm py-2.5 px-3 @error('full_address') border-red-500 @enderror" placeholder="Contoh: Jl. Telekomunikasi No. 1, RT.01/RW.01, Sukapura">{{ old('full_address') }}</textarea>
                            @error('full_address') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Bagian Pernyataan Komitmen --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="commitment_checkbox" name="commitment_checkbox" type="checkbox" value="1" {{ old('commitment_checkbox') ? 'checked' : '' }} required class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded @error('commitment_checkbox') border-red-500 ring-red-500 @enderror">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="commitment_checkbox" class="font-medium text-gray-700">Pernyataan Komitmen <span class="text-red-500">*</span></label>
                                <p class="text-gray-500 text-xs">Dengan ini saya menyatakan bahwa semua informasi yang saya berikan adalah benar...</p>
                            </div>
                        </div>
                        @error('commitment_checkbox') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-3.5 border border-transparent text-base font-semibold rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors transform hover:scale-105">
                        <span class="material-icons mr-2">send</span>
                        Kirim Formulir Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </div>

@push('scripts')
<script>
//     {{-- jibi/resources/views/ukm-ormawa/apply.blade.php --}}
//     {{-- ... (bagian HTML tetap sama) ... --}}
    // TIDAK PERLU GOAPI_KEY DI SINI LAGI KARENA DIHANDLE BACKEND
    // const GOAPI_KEY = '62ed8eb0-a748-58a2-37fd-eb75a19e'; 
    
    // URL ke proxy di backend Laravel Anda
    // Sesuaikan path '/api' jika Anda meletakkan route di web.php (hapus /api)
    const LARAVEL_PROXY_BASE_URL = '/proxy/goapi/regional'; 

    async function fetchGoApiData(endpoint, params = {}) {
        // Endpoint: 'provinsi', 'kota', 'kecamatan', 'kelurahan'
        // Params: objek seperti { provinsi_id: '11' }

        const url = new URL(`${window.location.origin}${LARAVEL_PROXY_BASE_URL}/${endpoint}`);
        
        // Parameter filter (misalnya, id_provinsi untuk kota) sekarang dikirim ke proxy Laravel
        for (const key in params) {
            if (params[key]) {
                url.searchParams.append(key, params[key]);
            }
        }
        
        console.log("Requesting Laravel Proxy:", url.toString());

        try {
            const response = await axios.get(url.toString()); // Tidak perlu api_key di sini lagi
            console.log("Laravel Proxy Response for " + endpoint + ":", response);

            // Struktur respons dari proxy Anda (yang meneruskan dari GoAPI)
            if (response.data && response.data.data && Array.isArray(response.data.data)) {
                return response.data.data;
            } else if (Array.isArray(response.data)) { 
                 return response.data;
            } else if (response.data && response.data.status === 'error') { // Jika proxy mengembalikan error terstruktur
                console.error(`Error from Laravel Proxy (${endpoint}):`, response.data.message);
                if(response.data.goapi_body && response.data.goapi_body.message) {
                    console.error(`GoAPI Original Message: ${response.data.goapi_body.message}`);
                }
            } else {
                console.error(`Unexpected Laravel Proxy Response Structure for ${endpoint}:`, response.data);
            }
            return [];
        } catch (error) {
            console.error(`Network/Request Error fetching data from Laravel Proxy (${endpoint}):`, error.response ? error.response.data : error.message, error);
            if (error.response && error.response.data && error.response.data.message) {
                console.error(`Specific error from catch: ${error.response.data.message}`);
            }
            return [];
        }
    }

    // Fungsi populateDropdown tetap sama seperti sebelumnya
    // valueForOption: field API yang akan jadi value di <option> (NAMA DAERAH)
    // textForOption: field API yang akan jadi teks yang terlihat di <option> (NAMA DAERAH)
    // idFieldFromApi: field dari API yang berisi ID unik (misal 'id')
    function populateDropdown(dropdownElement, data, valueForOption = 'name', textForOption = 'name', idFieldFromApi = 'id', placeholderText = 'Pilih') {
        const currentSelectedName = dropdownElement.dataset.oldValueName; 

        dropdownElement.innerHTML = `<option value="">-- ${placeholderText} --</option>`;
        
        if (data && data.length > 0) {
            data.forEach(item => {
                if (item && item[textForOption] !== undefined && item[valueForOption] !== undefined && item[idFieldFromApi] !== undefined) {
                    const option = document.createElement('option');
                    option.value = item[valueForOption]; 
                    option.textContent = item[textForOption]; 
                    option.dataset.id = item[idFieldFromApi]; 

                    if (currentSelectedName && item[textForOption] === currentSelectedName) {
                        option.selected = true;
                    }
                    dropdownElement.appendChild(option);
                }
            });
            dropdownElement.disabled = false;
        } else {
            dropdownElement.disabled = true;
        }
    }

    // Bagian document.addEventListener('DOMContentLoaded', ...) dan event listener lainnya tetap sama
    // seperti pada respons saya sebelumnya, yang memanggil fetchGoApiData dan populateDropdown.
    // Pastikan parameter untuk fetchGoApiData di event listener sudah benar (provinsi_id, kota_id, kecamatan_id)
    document.addEventListener('DOMContentLoaded', async () => {
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city');
        const districtSelect = document.getElementById('district');
        const villageSelect = document.getElementById('village');

        provinceSelect.dataset.oldValueName = "{{ old('province') }}";
        citySelect.dataset.oldValueName = "{{ old('city') }}";
        districtSelect.dataset.oldValueName = "{{ old('district') }}";
        villageSelect.dataset.oldValueName = "{{ old('village') }}";

        async function initAndRestore() {
            citySelect.disabled = true; citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            districtSelect.disabled = true; districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.disabled = true; villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

            const provinces = await fetchGoApiData('provinsi');
            populateDropdown(provinceSelect, provinces, 'name', 'name', 'id', 'Pilih Provinsi');

            if (provinceSelect.dataset.oldValueName) {
                provinceSelect.value = provinceSelect.dataset.oldValueName;
                if (provinceSelect.value) {
                    await provinceSelect.dispatchEvent(new Event('change'));
                    if (citySelect.dataset.oldValueName) {
                        await new Promise(resolve => setTimeout(resolve, 1200)); 
                        citySelect.value = citySelect.dataset.oldValueName;
                        if (citySelect.value) {
                            await citySelect.dispatchEvent(new Event('change'));
                            if (districtSelect.dataset.oldValueName) {
                                await new Promise(resolve => setTimeout(resolve, 1200));
                                districtSelect.value = districtSelect.dataset.oldValueName;
                                if (districtSelect.value) {
                                   await districtSelect.dispatchEvent(new Event('change'));
                                    if (villageSelect.dataset.oldValueName) {
                                        await new Promise(resolve => setTimeout(resolve, 1200));
                                        villageSelect.value = villageSelect.dataset.oldValueName;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        provinceSelect.addEventListener('change', async () => {
            const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
            const provinceId = selectedOption ? selectedOption.dataset.id : null; 

            citySelect.disabled = true; citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
            districtSelect.disabled = true; districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.disabled = true; villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
            
            if (provinceId) {
                citySelect.innerHTML = '<option value="">Memuat Kota...</option>';
                // Sesuaikan nama parameter ini ('provinsi_id') jika berbeda di GoAPI
                const cities = await fetchGoApiData('kota', { provinsi_id: provinceId }); 
                populateDropdown(citySelect, cities, 'name', 'name', 'id', 'Pilih Kota/Kabupaten');
            }
        });

        citySelect.addEventListener('change', async () => {
            const selectedOption = citySelect.options[citySelect.selectedIndex];
            const cityId = selectedOption ? selectedOption.dataset.id : null;

            districtSelect.disabled = true; districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            villageSelect.disabled = true; villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

            if (cityId) {
                districtSelect.innerHTML = '<option value="">Memuat Kecamatan...</option>';
                 // Sesuaikan nama parameter ini ('kota_id') jika berbeda di GoAPI
                const districts = await fetchGoApiData('kecamatan', { kota_id: cityId }); 
                populateDropdown(districtSelect, districts, 'name', 'name', 'id', 'Pilih Kecamatan');
            }
        });

        districtSelect.addEventListener('change', async () => {
            const selectedOption = districtSelect.options[districtSelect.selectedIndex];
            const districtId = selectedOption ? selectedOption.dataset.id : null;

            villageSelect.disabled = true; villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';

            if (districtId) {
                villageSelect.innerHTML = '<option value="">Memuat Kelurahan...</option>';
                 // Sesuaikan nama parameter ini ('kecamatan_id') jika berbeda di GoAPI
                const villages = await fetchGoApiData('kelurahan', { kecamatan_id: districtId });
                populateDropdown(villageSelect, villages, 'name', 'name', 'id', 'Pilih Kelurahan/Desa');
            }
        });

        await initAndRestore();
    });
</script>
@endpush
</x-app-layout>