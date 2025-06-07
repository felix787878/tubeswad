<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PengurusSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('pengurus.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255', // Ini akan jadi 'Username' di form
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nim' => 'nullable|string|max:20|unique:users,nim,' . $user->id, // Asumsi NIM juga unik jika diisi
            'phone_number' => 'nullable|string|max:15',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->nim = $request->nim; // Anda perlu menambahkan kolom 'nim', 'phone_number', 'bio' ke tabel users & model User jika belum ada
        $user->phone_number = $request->phone_number;
        $user->bio = $request->bio;
        $user->save();

        return back()->with('successProfile', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini salah.');
                }
            }],
            'new_password' => ['required', 'string', Password::min(6), 'confirmed'],
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('successPassword', 'Password berhasil diperbarui!');
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        // Validasi password sebelum hapus akun (sangat disarankan)
        if (!Hash::check($request->password_confirm_delete, $user->password)) {
            return back()->withErrors(['password_confirm_delete' => 'Password konfirmasi salah. Akun tidak dihapus.'])->withInput();
        }
$user = Auth::user();

        // Validasi password sebelum hapus akun (sangat disarankan)
        if (!Hash::check($request->password_confirm_delete, $user->password)) {
            return back()->withErrors(['password_confirm_delete' => 'Password konfirmasi salah. Akun tidak dihapus.'])->withInput();
        }

        Auth::logout();
        $user->delete();
        return redirect('/login')->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}