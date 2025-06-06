<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityAttendance; // Model untuk mencatat pendaftaran/kehadiran
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityController extends Controller
{
    /**
     * Display the specified publicly available activity.
     *
     * @param  \App\Models\Activity  $activity
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showPublic(Activity $activity)
    {
        // Pastikan kegiatan ini memang boleh dilihat publik (sudah dipublikasikan)
        if (!$activity->is_published) {
            return abort(404, 'Kegiatan tidak ditemukan atau belum dipublikasikan.');
        }

        // Eager load relasi yang mungkin dibutuhkan di view detail
        $activity->load('ukmOrmawa'); // Untuk menampilkan nama UKM/Ormawa penyelenggara

        $userParticipationStatus = null;
        $isRegistered = false;
        $canUnregister = false;

        // Cek apakah pendaftaran untuk kegiatan ini secara umum dibuka oleh pengurus
        $registrationIsOpenForActivity = $activity->is_registration_open &&
                                          (!$activity->registration_deadline_activity || Carbon::parse($activity->registration_deadline_activity)->endOfDay()->isFuture());

        if (Auth::check()) {
            $user = Auth::user();
            // Cek apakah user sudah mendaftar/hadir di kegiatan ini
            $attendance = ActivityAttendance::where('activity_id', $activity->id)
                                           ->where('user_id', $user->id)
                                           ->first();
            if ($attendance) {
                $isRegistered = true;
                $userParticipationStatus = $attendance->status; // Misal: 'Terdaftar', 'Hadir', 'Absen'
                
                // Cek apakah user bisa batal daftar: status 'Terdaftar' dan kegiatan belum mulai/berlangsung
                if ($userParticipationStatus === 'Terdaftar' && Carbon::parse($activity->date_start)->isFuture()) {
                    $canUnregister = true;
                }
            }
        }

        return view('activities.show-public', compact('activity', 'userParticipationStatus', 'isRegistered', 'registrationIsOpenForActivity', 'canUnregister'));
    }

    /**
     * Register the authenticated user to the specified activity.
     */
    public function registerToActivity(Request $request, Activity $activity)
    {
        $user = Auth::user();

        // Validasi dasar
        if (!$activity->is_published || !$activity->is_registration_open) {
            return back()->with('error', 'Pendaftaran untuk kegiatan ini tidak dibuka atau kegiatan tidak dipublikasikan.');
        }

        if ($activity->registration_deadline_activity && Carbon::parse($activity->registration_deadline_activity)->endOfDay()->isPast()) {
            return back()->with('error', 'Batas waktu pendaftaran untuk kegiatan ini telah berakhir.');
        }
        
        // Kegiatan sudah lewat tidak bisa didaftari
        if (Carbon::parse($activity->date_start)->isPast()) {
            return back()->with('error', 'Kegiatan ini sudah berlalu.');
        }

        // Cek apakah sudah terdaftar
        $existingAttendance = ActivityAttendance::where('activity_id', $activity->id)
                                               ->where('user_id', $user->id)
                                               ->first();
        if ($existingAttendance) {
            return back()->with('warning', 'Anda sudah terdaftar pada kegiatan ini dengan status: ' . $existingAttendance->status);
        }

        // Buat entri pendaftaran baru
        ActivityAttendance::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => 'Terdaftar', // Status awal setelah mendaftar
            // 'notes' => 'Mendaftar melalui sistem pada ' . now(), // Opsional
        ]);

        return back()->with('success', 'Anda berhasil terdaftar pada kegiatan "' . $activity->name . '". Mohon tunggu informasi selanjutnya dari penyelenggara.');
    }

    /**
     * Unregister the authenticated user from the specified activity.
     */
    public function unregisterFromActivity(Request $request, Activity $activity)
    {
        $user = Auth::user();
        $attendance = ActivityAttendance::where('activity_id', $activity->id)
                                       ->where('user_id', $user->id)
                                       ->where('status', 'Terdaftar') // Hanya bisa batal jika statusnya 'Terdaftar'
                                       ->first();

        if ($attendance) {
            // Logika tambahan: cegah batal jika kegiatan sudah sangat dekat atau sudah mulai
            // Misalnya, tidak boleh batal jika H-1 atau kegiatan sudah berlangsung
            if (Carbon::parse($activity->date_start)->isPast() || Carbon::parse($activity->date_start)->isToday()) {
                 return back()->with('error', 'Tidak bisa membatalkan pendaftaran karena kegiatan akan segera dimulai atau sudah berlangsung.');
            }

            $attendance->delete();
            return back()->with('success', 'Pendaftaran Anda untuk kegiatan "' . $activity->name . '" telah berhasil dibatalkan.');
        }
        return back()->with('error', 'Anda tidak terdaftar dengan status "Terdaftar" atau tidak dapat membatalkan pendaftaran untuk kegiatan ini.');
    }
}