<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\UkmOrmawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ManagedUkmOrmawaController extends Controller
{
    /**
     * Show the form for editing the managed UKM/Ormawa.
     */

    public function editOrCreate()
    {
        $user = Auth::user();
        $ukmOrmawa = $user->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            // Jika pengurus belum punya UKM, arahkan ke form create
            return view('pengurus.ukm-ormawa.create'); 
        }

        // Jika sudah punya, arahkan ke form edit
        return view('pengurus.ukm-ormawa.edit', compact('ukmOrmawa'));
    }

    public function create()
    {
        // Pengecekan: jika pengurus sudah punya UKM, jangan biarkan membuat lagi.
        // Langsung arahkan ke halaman edit.
        if (Auth::user()->managesUkmOrmawa) {
            return redirect()->route('pengurus.ukm-ormawa.edit')->with('info', 'Anda sudah mengelola UKM/Ormawa. Silakan edit data yang sudah ada.');
        }
        // Jika belum punya, tampilkan form untuk membuat UKM baru.
        return view('pengurus.ukm-ormawa.create');
    }
    
    public function edit()
    {
        // Mengambil UKM yang dikelola oleh user yang sedang login.
        // `firstOrFail()` akan menampilkan error 404 jika tidak ditemukan, lebih baik daripada null.
        $ukmOrmawa = Auth::user()->managesUkmOrmawa()->firstOrFail();
        
        return view('pengurus.ukm-ormawa.edit', compact('ukmOrmawa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $ukmOrmawa = Auth::user()->managesUkmOrmawa()->firstOrFail();

        // 1. Validasi data dari request
        $validated = $request->validate([
            // Aturan validasi unik diubah agar mengabaikan ID saat ini
            'name' => 'required|string|max:255|unique:ukm_ormawas,name,' . $ukmOrmawa->id,
            'type' => 'required|in:UKM,Ormawa',
            'category' => 'required|string|max:255',
            'logo_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'description_full' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi_input' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_instagram' => 'nullable|string|max:255',
            'is_registration_open' => 'nullable|boolean',
            'registration_deadline' => 'nullable|date',
            'alamat_lengkap' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kabkota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desakel' => 'required|string|max:255',
            'google_maps_link' => 'nullable|url|max:255',
        ]);

        // 2. Siapkan data untuk diupdate
        // Logika disederhanakan, kita mulai dengan semua data yang valid.
        $dataToUpdate = $validated;

        // Proses field yang butuh perlakuan khusus
        $dataToUpdate['slug'] = Str::slug($validated['name']);

        // Set status kembali ke "Menunggu Verifikasi" setiap kali ada update
        $dataToUpdate['status'] = 'Menunggu Verifikasi';

        // Handle checkbox 'is_registration_open'
        $dataToUpdate['is_registration_open'] = $request->has('is_registration_open');

        // Handle file upload Logo
        if ($request->hasFile('logo_url_file')) {
            // Hapus file lama jika ada
            if ($ukmOrmawa->logo_url && Storage::disk('public')->exists($ukmOrmawa->logo_url)) {
                Storage::disk('public')->delete($ukmOrmawa->logo_url);
            }
            $dataToUpdate['logo_url'] = $request->file('logo_url_file')->store('ukm_logos', 'public');
        }

        // Handle file upload Banner
        if ($request->hasFile('banner_url_file')) {
            // Hapus file lama jika ada
            if ($ukmOrmawa->banner_url && Storage::disk('public')->exists($ukmOrmawa->banner_url)) {
                Storage::disk('public')->delete($ukmOrmawa->banner_url);
            }
            $dataToUpdate['banner_url'] = $request->file('banner_url_file')->store('ukm_banners', 'public');
        }

        // Proses 'misi_input' (textarea) menjadi 'misi' (JSON array)
        if ($request->filled('misi_input')) {
            $misiArray = array_filter(array_map('trim', explode("\n", $validated['misi_input'])));
            $dataToUpdate['misi'] = $misiArray;
        } else {
            $dataToUpdate['misi'] = []; // Kosongkan misi jika input dihapus
        }
        
        // Hapus field-field sementara yang tidak ada di database
        unset($dataToUpdate['misi_input'], $dataToUpdate['logo_url_file'], $dataToUpdate['banner_url_file']);
        
        // 3. Update data di database
        $ukmOrmawa->update($dataToUpdate);

        return redirect()->route('pengurus.ukm-ormawa.edit')->with('success', 'Profil UKM/Ormawa berhasil diperbarui dan diajukan untuk verifikasi ulang.');
    }

    public function store(Request $request)
    {
        // 1. Validasi data
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ukm_ormawas,name',
            'type' => 'required|in:UKM,Ormawa',
            'category' => 'required|string|max:255',
            'logo_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'description_short' => 'nullable|string|max:500',
            'description_full' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi_input' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_instagram' => 'nullable|string|max:255',
            'is_registration_open' => 'nullable|boolean',
            'registration_deadline' => 'nullable|date',
            'alamat_lengkap' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kabkota' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'desakel' => 'required|string|max:255',
            'google_maps_link' => 'nullable|url|max:255',
        ]);

        // 2. Siapkan data untuk disimpan
        $dataToCreate = $validated;
        $dataToCreate['slug'] = Str::slug($validated['name']);
        $dataToCreate['status'] = 'Menunggu Verifikasi';
        $dataToCreate['is_registration_open'] = $request->has('is_registration_open');

        // 3. Proses file upload
        if ($request->hasFile('logo_url_file')) {
            $dataToCreate['logo_url'] = $request->file('logo_url_file')->store('ukm_logos', 'public');
        }

        if ($request->hasFile('banner_url_file')) {
            $dataToCreate['banner_url'] = $request->file('banner_url_file')->store('ukm_banners', 'public');
        }   

        // 4. Proses misi menjadi array JSON
        if ($request->filled('misi_input')) {
            $misiArray = array_filter(array_map('trim', explode("\n", $validated['misi_input'])));
            $dataToCreate['misi'] = $misiArray;
        } else {
            $dataToCreate['misi'] = [];
        }

        // Hapus field sementara
        unset($dataToCreate['misi_input'], $dataToCreate['logo_url_file'], $dataToCreate['banner_url_file']);

        // 5. Simpan UKM dan hubungkan ke user yang sedang login
        $ukmOrmawa = Auth::user()->createdUkmOrmawa()->create($dataToCreate);

        return redirect()->route('pengurus.ukm-ormawa.edit')->with('success', 'Profil UKM/Ormawa berhasil dibuat dan diajukan untuk verifikasi.');
    }
}
