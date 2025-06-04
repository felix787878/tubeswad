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
     * Show the form for editing the specified resource or creating a new one.
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // Pastikan pengurus ini belum mengelola UKM lain
        if ($user->managesUkmOrmawa) {
            return redirect()->route('pengurus.ukm-ormawa.edit')->with('error', 'Anda sudah mengelola sebuah UKM/Ormawa. Anda hanya bisa mengelola satu.');
        }

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
        ]);

        $dataToCreate = $validated; // Inisialisasi $dataToCreate dengan hasil validasi
        
        // Hapus 'misi_input' dari $dataToCreate karena akan diproses terpisah menjadi 'misi' (array)
        // Hapus juga 'logo_url_file' dan 'banner_url_file' karena akan menjadi 'logo_url' dan 'banner_url'
        unset($dataToCreate['misi_input'], $dataToCreate['logo_url_file'], $dataToCreate['banner_url_file']);


        $dataToCreate['slug'] = Str::slug($validated['name']);
        $dataToCreate['pengurus_id'] = $user->id; 
        $dataToCreate['status'] = 'pending_verification'; 
        $dataToCreate['is_registration_open'] = false; 

        if ($request->hasFile('logo_url_file')) {
            $dataToCreate['logo_url'] = $request->file('logo_url_file')->store('ukm_logos', 'public');
        }
        if ($request->hasFile('banner_url_file')) {
            $dataToCreate['banner_url'] = $request->file('banner_url_file')->store('ukm_banners', 'public');
        }

        if (!empty($validated['misi_input'])) {
            $misiArray = array_map('trim', explode("\n", $validated['misi_input']));
            $misiArray = array_filter($misiArray);
            $dataToCreate['misi'] = $misiArray;
        } else {
            $dataToCreate['misi'] = [];
        }
        
        $ukmOrmawa = UkmOrmawa::create($dataToCreate);

        $user->manages_ukm_ormawa_id = $ukmOrmawa->id;
        $user->save();

        return redirect()->route('pengurus.dashboard')->with('success', 'Profil UKM/Ormawa berhasil dibuat dan diajukan untuk verifikasi!');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $ukmOrmawa = $user->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.ukm-ormawa.create')->with('error', 'Anda belum membuat profil UKM/Ormawa.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:ukm_ormawas,name,' . $ukmOrmawa->id,
            'type' => 'required|in:UKM,Ormawa',
            'category' => 'required|string|max:255',
            'logo_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner_url_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'description_short' => 'nullable|string|max:500',
            'description_full' => 'nullable|string',
            'visi' => 'nullable|string',
            'misi_input' => 'nullable|string', // Ini untuk input dari form
            'contact_email' => 'nullable|email|max:255',
            'contact_instagram' => 'nullable|string|max:255',
            'is_registration_open' => 'sometimes|boolean',
            'registration_deadline' => 'nullable|date|after_or_equal:today',
        ]);

        // Inisialisasi $dataToUpdate dengan array kosong atau field yang pasti ada
        $dataToUpdate = [];

        // Salin semua field dari $validated yang ada di $fillable model UkmOrmawa
        // kecuali field file dan field yang diproses secara khusus (misi_input)
        $fillableFields = (new UkmOrmawa)->getFillable();
        foreach ($validated as $key => $value) {
            if ($key !== 'misi_input' && $key !== 'logo_url_file' && $key !== 'banner_url_file' && in_array($key, $fillableFields)) {
                $dataToUpdate[$key] = $value;
            }
        }
        // Isi field lain yang tidak langsung dari $validated
        $dataToUpdate['name'] = $validated['name'];
        $dataToUpdate['type'] = $validated['type'];
        $dataToUpdate['category'] = $validated['category'];
        if(isset($validated['description_short'])) $dataToUpdate['description_short'] = $validated['description_short'];
        if(isset($validated['description_full'])) $dataToUpdate['description_full'] = $validated['description_full'];
        if(isset($validated['visi'])) $dataToUpdate['visi'] = $validated['visi'];
        if(isset($validated['contact_email'])) $dataToUpdate['contact_email'] = $validated['contact_email'];
        if(isset($validated['contact_instagram'])) $dataToUpdate['contact_instagram'] = $validated['contact_instagram'];
        if(isset($validated['registration_deadline'])) $dataToUpdate['registration_deadline'] = $validated['registration_deadline'];


        // Jangan update slug jika nama tidak berubah, atau jika slug sudah di-custom
        if ($ukmOrmawa->name !== $validated['name'] || empty($ukmOrmawa->slug)) {
            $dataToUpdate['slug'] = Str::slug($validated['name']);
        }


        if ($request->hasFile('logo_url_file')) {
            if ($ukmOrmawa->logo_url && Storage::disk('public')->exists($ukmOrmawa->logo_url)) {
                Storage::disk('public')->delete($ukmOrmawa->logo_url);
            }
            $dataToUpdate['logo_url'] = $request->file('logo_url_file')->store('ukm_logos', 'public');
        }

        if ($request->hasFile('banner_url_file')) {
            if ($ukmOrmawa->banner_url && Storage::disk('public')->exists($ukmOrmawa->banner_url)) {
                Storage::disk('public')->delete($ukmOrmawa->banner_url);
            }
            $dataToUpdate['banner_url'] = $request->file('banner_url_file')->store('ukm_banners', 'public');
        }

        // Proses input misi yang sudah diperbaiki
        if ($request->filled('misi_input')) { // Cek apakah 'misi_input' diisi di request
            $misiArray = array_map('trim', explode("\n", $request->input('misi_input')));
            $misiArray = array_filter($misiArray); 
            $dataToUpdate['misi'] = $misiArray; 
        } else {
            // Jika 'misi_input' tidak diisi atau kosong di form, kita set sebagai array kosong di database
            // Ini berarti jika pengurus menghapus semua teks misi di form, misinya akan kosong.
            $dataToUpdate['misi'] = [];
        }
        
        $dataToUpdate['is_registration_open'] = $request->has('is_registration_open');
        
        $ukmOrmawa->update($dataToUpdate);

        $message = 'Data UKM/Ormawa berhasil diperbarui!';

        return redirect()->route('pengurus.ukm-ormawa.edit')->with('success', $message);
    }
}