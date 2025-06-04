<?php

namespace App\Http\Controllers\Direktorat;

use App\Http\Controllers\Controller;
use App\Models\UkmOrmawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Untuk hapus gambar jika UKM dihapus
use Illuminate\Support\Str;

class UkmManagementController extends Controller
{
    /**
     * Display a listing of the UKM/Ormawa.
     */
    public function index(Request $request)
    {
        $query = UkmOrmawa::query()->with('pengurus'); // Eager load pengurus

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhereHas('pengurus', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $ukmOrmawas = $query->orderByRaw("FIELD(status, 'pending_verification', 'needs_update', 'approved', 'rejected')")
                           ->orderBy('updated_at', 'desc')
                           ->paginate(15);

        return view('direktorat.ukm-management.index', compact('ukmOrmawas'));
    }

    /**
     * Display the specified UKM/Ormawa for verification/details.
     */
    public function show(UkmOrmawa $ukmOrmawa)
    {
        // $ukmOrmawa sudah di-resolve oleh Route Model Binding
        return view('direktorat.ukm-management.show', compact('ukmOrmawa'));
    }

    /**
     * Update the status and verification notes of the specified UKM/Ormawa.
     */
    public function updateStatus(Request $request, UkmOrmawa $ukmOrmawa)
    {
        $request->validate([
            'status' => 'required|in:pending_verification,approved,rejected,needs_update',
            'verification_notes' => 'nullable|string|max:2000',
        ]);

        $ukmOrmawa->status = $request->status;
        if ($request->filled('verification_notes')) {
            $ukmOrmawa->verification_notes = $request->verification_notes;
        } else {
            // Kosongkan notes jika statusnya approved atau pending, 
            // tapi biarkan jika rejected atau needs_update dan notes tidak diisi sekarang
            if (in_array($request->status, ['approved', 'pending_verification'])) {
                 $ukmOrmawa->verification_notes = null;
            }
        }
        $ukmOrmawa->save();

        return redirect()->route('direktorat.ukm-ormawa.show', $ukmOrmawa)->with('success', 'Status UKM/Ormawa berhasil diperbarui.');
    }

    /**
     * Show the form for editing the specified UKM/Ormawa by Direktorat.
     */
    public function edit(UkmOrmawa $ukmOrmawa)
    {
        return view('direktorat.ukm-management.edit', compact('ukmOrmawa'));
    }

    /**
     * Update the specified UKM/Ormawa in storage by Direktorat.
     */
    public function update(Request $request, UkmOrmawa $ukmOrmawa)
    {
        // Validasi mirip dengan ManagedUkmOrmawaController, tapi tanpa unique check pada user pengurus
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
            'status' => 'required|in:pending_verification,approved,rejected,needs_update',
            'verification_notes' => 'nullable|string|max:2000',
            'pengurus_id' => 'nullable|exists:users,id' // Jika direktorat bisa mengganti pengurus
        ]);

        $dataToUpdate = $validated;
        $dataToUpdate['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo_url_file')) {
            if ($ukmOrmawa->logo_url && Storage::disk('public')->exists($ukmOrmawa->logo_url)) {
                Storage::disk('public')->delete($ukmOrmawa->logo_url);
            }
            $dataToUpdate['logo_url'] = $request->file('logo_url_file')->store('ukm_logos', 'public');
        }
        // ... (handle banner_url_file sama seperti di atas) ...

        if (!empty($validated['misi_input'])) {
            $misiArray = array_map('trim', explode("\n", $validated['misi_input']));
            $misiArray = array_filter($misiArray);
            $dataToUpdate['misi'] = $misiArray;
        } else {
            $dataToUpdate['misi'] = [];
        }
        
        $dataToUpdate['is_registration_open'] = $request->has('is_registration_open');
        
        // Jika pengurus diubah, update juga di tabel users
        if ($request->filled('pengurus_id') && $ukmOrmawa->pengurus_id != $request->pengurus_id) {
            // Hapus assignment dari pengurus lama jika ada
            if ($ukmOrmawa->pengurus) {
                $ukmOrmawa->pengurus->manages_ukm_ormawa_id = null;
                $ukmOrmawa->pengurus->save();
            }
            // Assign ke pengurus baru
            $newPengurus = User::find($request->pengurus_id);
            if ($newPengurus) {
                $newPengurus->manages_ukm_ormawa_id = $ukmOrmawa->id;
                $newPengurus->save();
            }
        }


        $ukmOrmawa->update($dataToUpdate);

        return redirect()->route('direktorat.ukm-ormawa.show', $ukmOrmawa)->with('success', 'Data UKM/Ormawa berhasil diperbarui oleh Direktorat.');
    }

    /**
     * Remove the specified UKM/Ormawa from storage.
     */
    public function destroy(UkmOrmawa $ukmOrmawa)
    {
        // Hapus logo dan banner jika ada
        if ($ukmOrmawa->logo_url && Storage::disk('public')->exists($ukmOrmawa->logo_url)) {
            Storage::disk('public')->delete($ukmOrmawa->logo_url);
        }
        if ($ukmOrmawa->banner_url && Storage::disk('public')->exists($ukmOrmawa->banner_url)) {
            Storage::disk('public')->delete($ukmOrmawa->banner_url);
        }

        // Hapus assignment dari pengurusnya
        if ($ukmOrmawa->pengurus) {
            $ukmOrmawa->pengurus->manages_ukm_ormawa_id = null;
            $ukmOrmawa->pengurus->save();
        }
        
        $ukmOrmawa->delete();

        return redirect()->route('direktorat.ukm-ormawa.index')->with('success', 'UKM/Ormawa berhasil dihapus.');
    }
}