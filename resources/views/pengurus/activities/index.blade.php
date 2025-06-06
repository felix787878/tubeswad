{{-- resources/views/pengurus/activities/index.blade.php --}}
<x-pengurus-app-layout>
    <x-slot name="header">
        {{ __('Daftar Kegiatan - ') . $ukmOrmawa->name }}
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifikasi, Filter, dan Pencarian tetap sama --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md shadow-sm">{{ session('success') }}</div>
            @endif
            
            <div class="mb-6 p-6 bg-white rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Manajemen Kegiatan</h2>
                    <a href="{{ route('pengurus.activities.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <span class="material-icons text-base mr-1">add</span>
                        Tambah Kegiatan
                    </a>
                </div>
                <form method="GET" action="{{ route('pengurus.activities.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label for="search_activity" class="block text-sm font-medium text-gray-700">Cari Kegiatan</label>
                            <input type="text" name="search_activity" id="search_activity" value="{{ request('search_activity') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm py-2 px-3">
                        </div>
                        <div>
                            <label for="filter_status_kegiatan" class="block text-sm font-medium text-gray-700">Filter Status</label>
                            <select name="filter_status_kegiatan" id="filter_status_kegiatan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm py-2 px-3">
                                <option value="">Semua Status</option>
                                <option value="published" {{ request('filter_status_kegiatan') == 'published' ? 'selected' : '' }}>Dipublikasikan</option>
                                <option value="draft" {{ request('filter_status_kegiatan') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="upcoming" {{ request('filter_status_kegiatan') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
                                <option value="ongoing" {{ request('filter_status_kegiatan') == 'ongoing' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                <option value="finished" {{ request('filter_status_kegiatan') == 'finished' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                           <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                <span class="material-icons text-sm mr-1">filter_list</span> Filter
                            </button>
                             <a href="{{ route('pengurus.activities.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            
            {{-- PERBAIKAN UTAMA ADA DI SINI --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="overflow-x-auto">
                    {{-- Ganti @if(count($upcoming_activities) > 0) dengan ini --}}
                    @if($activities->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                             <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                {{-- Ganti @foreach($upcoming_activities as $activity) dengan ini --}}
                                @foreach($activities as $activity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $activity->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $activity->type }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ \Carbon\Carbon::parse($activity->date_start)->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($activity->is_published)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                            <a href="{{ route('pengurus.activities.edit', $activity->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <span class="material-icons text-base align-middle">edit</span>
                                            </a>
                                            <form action="{{ route('pengurus.activities.destroy', $activity->id) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin menghapus kegiatan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                                    <span class="material-icons text-base align-middle">delete_forever</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-12 px-6">
                            <span class="material-icons text-5xl text-gray-400 mb-3">search_off</span>
                            <p class="text-xl text-gray-600">Tidak ada kegiatan ditemukan.</p>
                            <p class="text-sm text-gray-500 mt-1">Coba gunakan filter lain atau buat kegiatan baru.</p>
                        </div>
                    @endif
                </div>

                {{-- Pagination Links --}}
                @if ($activities->hasPages())
                    <div class="p-6 border-t border-gray-200">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-pengurus-app-layout>