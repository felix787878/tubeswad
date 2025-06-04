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
    public function editOrCreate() // <-- PASTIKAN NAMA METHOD INI ADA DAN BENAR
    {
        $user = Auth::user();
        $ukmOrmawa = $user->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            // Jika pengurus belum punya UKM, arahkan ke form create
            return view('pengurus.ukm-ormawa.create'); // Anda perlu membuat view ini
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
            // is_registration_open & registration_deadline tidak di-set saat create awal oleh pengurus
            // status akan default 'pending_verification'
        ]);

        $dataToCreate = $validated;
        $dataToCreate['slug'] = Str::slug($validated['name']);
        $dataToCreate['pengurus_id'] = $user->id; // Set pengurus yang membuat
        $dataToCreate['status'] = 'pending_verification'; // Status awal
        $dataToCreate['is_registration_open'] = false; // Default pendaftaran tertutup

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

        // Update user pengurus untuk menandakan UKM yang dikelolanya
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
            // Seharusnya tidak terjadi jika alur sudah benar, tapi sebagai fallback
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
            'misi_input' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_instagram' => 'nullable|string|max:255',
            'is_registration_open' => 'sometimes|boolean',
            'registration_deadline' => 'nullable|date|after_or_equal:today',
            // Pengurus tidak mengubah status secara langsung, ini tugas Admin Direktorat
        ]);

        $dataToUpdate = $validated;
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

        if (!empty($validated['misi_input'])) {
            $misiArray = array_map('trim', explode("\n", $validated['misi_input']));
            $misiArray = array_filter($misiArray); 
            $dataToUpdate['misi'] = $misiArray;
        } else {
            // Jika input misi kosong, jangan hapus misi yang sudah ada kecuali memang disengaja
            // Untuk saat ini, jika kosong maka akan jadi array kosong, yang akan menghapus misi lama
            $dataToUpdate['misi'] = $ukmOrmawa->misi && empty($validated['misi_input']) ? $ukmOrmawa->misi : [];
            if (!empty($validated['misi_input'])) $dataToUpdate['misi'] = $misiArray; // override jika ada input
        }
        
        $dataToUpdate['is_registration_open'] = $request->has('is_registration_open');
        // $dataToUpdate['is_visible'] tidak diubah oleh pengurus


        $ukmOrmawa->update($dataToUpdate);

        $message = 'Data UKM/Ormawa berhasil diperbarui!';

        return redirect()->route('pengurus.ukm-ormawa.edit')->with('success', $message);
    }
}