<?php

namespace App\Http\Controllers;

use App\Models\UkmApplication;
use App\Models\UkmOrmawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UkmOrmawaRegistrationController extends Controller
{
    public function showApplicationForm($ukm_ormawa_slug)
    {
        $item = UkmOrmawa::where('slug', $ukm_ormawa_slug)->firstOrFail();

        if (!$item || $item->status !== 'approved' || !$item->is_registration_open) {
            return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])->with('error', 'Pendaftaran untuk ' . $item->name . ' saat ini tidak terbuka atau belum diverifikasi.');
        }
        if ($item->registration_deadline && $item->registration_deadline->isPast()) {
            return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])->with('error', 'Batas waktu pendaftaran untuk ' . $item->name . ' telah berakhir.');
        }

        $existingApplication = UkmApplication::where('user_id', Auth::id())
                                   ->where('ukm_ormawa_id', $item->id)
                                   ->whereIn('status', ['pending', 'approved'])
                                   ->first();

        if ($existingApplication) {
            return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])
                             ->with('warning', 'Anda sudah mendaftar atau pendaftaran Anda sedang diproses untuk ' . $item->name . '. Status saat ini: ' . ucfirst($existingApplication->status) . '.');
        }

        return view('ukm-ormawa.apply', compact('item'));
    }

    public function submitApplication(Request $request, $ukm_ormawa_slug)
    {
        $item = UkmOrmawa::where('slug', $ukm_ormawa_slug)->firstOrFail();
        if (!$item || $item->status !== 'approved' || !$item->is_registration_open) {
            return redirect()->route('ukm-ormawa.index')->with('error', 'Lowongan pendaftaran tidak ditemukan atau sudah ditutup.');
        }
        if ($item->registration_deadline && $item->registration_deadline->isPast()) {
            return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])->with('error', 'Batas waktu pendaftaran untuk ' . $item->name . ' telah berakhir.');
        }

        $existingApplication = UkmApplication::where('user_id', Auth::id())
                                   ->where('ukm_ormawa_id', $item->id)
                                   ->whereIn('status', ['pending', 'approved'])
                                   ->first();
        if ($existingApplication) {
            return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])
                             ->with('warning', 'Anda sudah mengirimkan pendaftaran untuk ' . $item->name . '. Mohon tunggu informasi selanjutnya.');
        }

        $validatedData = $request->validate([
            'reason_to_join' => 'required|string|min:20|max:2000',
            'skills_experience' => 'nullable|string|max:2000',
            'phone_contact' => 'required|string|regex:/^08[0-9]{8,13}$/',
            'province' => 'required|string|max:255',   // <-- TAMBAHKAN VALIDASI
            'city' => 'required|string|max:255',       // <-- TAMBAHKAN VALIDASI
            'district' => 'required|string|max:255',   // <-- TAMBAHKAN VALIDASI
            'village' => 'required|string|max:255',    // <-- TAMBAHKAN VALIDASI
            'full_address' => 'required|string|max:500', // <-- TAMBAHKAN VALIDASI
            'commitment_checkbox' => 'accepted',
        ],[
            'phone_contact.regex' => 'Format nomor HP tidak valid. Contoh: 081234567890 (10-15 digit).',
            'commitment_checkbox.accepted' => 'Anda harus menyetujui pernyataan komitmen.',
            'province.required' => 'Provinsi wajib diisi.',
            'city.required' => 'Kota/Kabupaten wajib diisi.',
            'district.required' => 'Kecamatan wajib diisi.',
            'village.required' => 'Kelurahan/Desa wajib diisi.',
            'full_address.required' => 'Alamat lengkap wajib diisi.',
        ]);

        UkmApplication::create([
            'user_id' => Auth::id(),
            'ukm_ormawa_id' => $item->id,
            'ukm_ormawa_name' => $item->name, // Jika masih dipertahankan
            'ukm_ormawa_slug' => $ukm_ormawa_slug, // Jika masih dipertahankan
            'reason_to_join' => $validatedData['reason_to_join'],
            'skills_experience' => $validatedData['skills_experience'],
            'phone_contact' => $validatedData['phone_contact'],
            'province' => $validatedData['province'],       // <-- SIMPAN DATA
            'city' => $validatedData['city'],               // <-- SIMPAN DATA
            'district' => $validatedData['district'],       // <-- SIMPAN DATA
            'village' => $validatedData['village'],          // <-- SIMPAN DATA
            'full_address' => $validatedData['full_address'], // <-- SIMPAN DATA
            'status' => 'pending',
        ]);

        return redirect()->route('ukm-ormawa.show', ['slug' => $ukm_ormawa_slug])
                         ->with('success', 'Selamat! Pendaftaran Anda untuk ' . $item->name . ' telah berhasil dikirim. Mohon tunggu proses verifikasi dari pengurus.');
    }
}