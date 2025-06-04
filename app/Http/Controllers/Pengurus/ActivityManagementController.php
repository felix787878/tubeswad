<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use App\Models\UkmApplication; // Untuk mengambil daftar anggota
use App\Models\UkmOrmawa;
use App\Models\User; // Untuk join pada query attendance
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;           
use Illuminate\Pagination\LengthAwarePaginator; // Untuk paginasi manual jika diperlukan

class ActivityManagementController extends Controller
{
    // ... (method index, create, store, edit, update, destroy sudah ada dari jawaban sebelumnya dan diasumsikan sudah benar) ...
    // [Pastikan method-method CRUD untuk Activity sudah lengkap di sini]
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa untuk mengelola kegiatan.');
        }

        $query = Activity::where('ukm_ormawa_id', $ukmOrmawa->id);

        // Filter berdasarkan nama kegiatan
        if ($request->filled('search_activity')) {
            $query->where('name', 'like', '%' . $request->search_activity . '%');
        }

        // Filter berdasarkan status kegiatan
        if ($request->filled('filter_status_kegiatan')) {
            $status = $request->filter_status_kegiatan;
            if ($status === 'upcoming') {
                $query->where('date_start', '>', now())->where('is_published', true);
            } elseif ($status === 'ongoing') {
                $query->where('date_start', '<=', now())->where(fn($q) => $q->whereNull('date_end')->orWhere('date_end', '>=', now()))->where('is_published', true);
            } elseif ($status === 'finished') {
                $query->whereNotNull('date_end')->where('date_end', '<', now())->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            } elseif ($status === 'published') {
                $query->where('is_published', true);
            }
        }

        $activities = $query->orderBy('date_start', 'desc')->paginate(10); 

        return view('pengurus.activities.index', compact('ukmOrmawa', 'activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa untuk menambah kegiatan.');
        }
        return view('pengurus.activities.create', compact('ukmOrmawa'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Tidak dapat menyimpan kegiatan.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'time_start' => 'required', 
            'time_end' => 'required',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'is_published' => 'sometimes|boolean',
        ]);

        $dataToStore = $validated;
        $dataToStore['ukm_ormawa_id'] = $ukmOrmawa->id;
        $dataToStore['user_id'] = $pengurus->id; 
        $dataToStore['is_published'] = $request->has('is_published');

        if ($request->hasFile('image_banner')) {
            $dataToStore['image_banner_url'] = $request->file('image_banner')->store('activity_banners', 'public');
        }

        Activity::create($dataToStore);

        return redirect()->route('pengurus.activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activity $activity)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa || $activity->ukm_ormawa_id !== $ukmOrmawa->id) {
            return redirect()->route('pengurus.activities.index')->with('error', 'Anda tidak berhak mengedit kegiatan ini.');
        }

        return view('pengurus.activities.edit', compact('activity', 'ukmOrmawa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activity $activity)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa || $activity->ukm_ormawa_id !== $ukmOrmawa->id) {
            return redirect()->route('pengurus.activities.index')->with('error', 'Anda tidak berhak mengupdate kegiatan ini.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'date_start' => 'required|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'time_start' => 'required',
            'time_end' => 'required',
            'location' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'is_published' => 'sometimes|boolean',
        ]);

        $dataToUpdate = $validated;
        $dataToUpdate['is_published'] = $request->has('is_published');

        if ($request->hasFile('image_banner')) {
            if ($activity->image_banner_url && Storage::disk('public')->exists($activity->image_banner_url)) {
                Storage::disk('public')->delete($activity->image_banner_url);
            }
            $dataToUpdate['image_banner_url'] = $request->file('image_banner')->store('activity_banners', 'public');
        }

        $activity->update($dataToUpdate);

        return redirect()->route('pengurus.activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activity $activity)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa || $activity->ukm_ormawa_id !== $ukmOrmawa->id) {
            return redirect()->route('pengurus.activities.index')->with('error', 'Anda tidak berhak menghapus kegiatan ini.');
        }

        if ($activity->image_banner_url && Storage::disk('public')->exists($activity->image_banner_url)) {
            Storage::disk('public')->delete($activity->image_banner_url);
        }

        $activity->delete();

        return redirect()->route('pengurus.activities.index')->with('success', 'Kegiatan berhasil dihapus.');
    }


    public function attendanceReport(Request $request)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa.');
        }

        // 1. Ambil kegiatan yang sudah selesai untuk filter (dari database)
        $completedActivities = Activity::where('ukm_ormawa_id', $ukmOrmawa->id)
                                       ->where(function($query) {
                                            // Kegiatan dianggap selesai jika tanggal akhirnya sudah lewat
                                            $query->whereNotNull('date_end')->where('date_end', '<', now());
                                            // Atau jika tidak ada tanggal akhir, tapi tanggal mulai sudah sangat lama (misal > 1 bulan lalu)
                                            // $query->orWhere(function($q) {
                                            //     $q->whereNull('date_end')->where('date_start', '<', now()->subMonth());
                                            // });
                                       })
                                       ->where('is_published', true) // Hanya kegiatan yang dipublikasi
                                       ->orderBy('date_start', 'desc')
                                       ->get();

        $selectedActivityId = $request->input('activity_id');
        $reportData = null; // Akan diisi dengan LengthAwarePaginator
        $reportType = null; 
        $activityName = null;
        $itemsPerPage = 15; // Jumlah item per halaman untuk paginasi
        
        if ($selectedActivityId) {
            $reportType = 'single_activity';
            $selectedActivity = Activity::where('id', $selectedActivityId)
                                        ->where('ukm_ormawa_id', $ukmOrmawa->id) // Pastikan kegiatan milik UKM ini
                                        ->first();
            
            if ($selectedActivity) {
                $activityName = $selectedActivity->name;
                // 2. Ambil data kehadiran nyata untuk kegiatan yang dipilih
                $reportData = ActivityAttendance::with('user') // Eager load data user
                                             ->where('activity_id', $selectedActivityId)
                                             ->join('users', 'activity_attendances.user_id', '=', 'users.id') // Untuk sorting by name
                                             ->orderBy('users.name')
                                             ->select('activity_attendances.*') // Hindari ambiguitas kolom ID
                                             ->paginate($itemsPerPage);
            } else {
                // Kegiatan tidak ditemukan atau tidak valid, buat paginator kosong
                $reportData = new LengthAwarePaginator([], 0, $itemsPerPage);
                $activityName = 'Kegiatan Tidak Valid';
            }

        } else { // Rekapitulasi Umum
            $reportType = 'overall_summary';
            
            // 3. Ambil semua anggota yang statusnya 'approved' untuk UKM ini
            $approvedMembers = UkmApplication::where('ukm_ormawa_id', $ukmOrmawa->id)
                                            ->where('status', 'approved')
                                            ->with('user') // Eager load data user
                                            ->get();
            
            $summaryItems = collect();

            if ($approvedMembers->isNotEmpty()) {
                // Ambil semua ID kegiatan yang sudah selesai dari UKM ini
                $completedActivityIds = Activity::where('ukm_ormawa_id', $ukmOrmawa->id)
                                                ->whereNotNull('date_end')
                                                ->where('date_end', '<', now())
                                                ->pluck('id');

                if ($completedActivityIds->isNotEmpty()) {
                    foreach ($approvedMembers as $application) {
                        if (!$application->user) continue; // Skip jika user tidak ada (seharusnya tidak terjadi)

                        $attendances = ActivityAttendance::where('user_id', $application->user_id)
                                                         ->whereIn('activity_id', $completedActivityIds)
                                                         ->get();

                        $kegiatan_diikuti = $attendances->count(); // Jumlah kegiatan yang ada record kehadirannya
                        $jumlah_hadir = $attendances->where('status', 'Hadir')->count();
                        $jumlah_absen_izin = $attendances->whereIn('status', ['Absen', 'Izin'])->count();
                        // Persentase kehadiran dari kegiatan yang ada recordnya
                        $persentase_kehadiran = $kegiatan_diikuti > 0 ? round(($jumlah_hadir / $kegiatan_diikuti) * 100) . '%' : 'N/A';
                        // Jika ingin persentase dari total kegiatan selesai UKM:
                        // $totalUkmCompletedActivities = $completedActivityIds->count();
                        // $persentase_kehadiran_total = $totalUkmCompletedActivities > 0 ? round(($jumlah_hadir / $totalUkmCompletedActivities) * 100) . '%' : 'N/A';


                        $summaryItems->push((object)[
                            'user' => $application->user,
                            'kegiatan_diikuti' => $kegiatan_diikuti,
                            'jumlah_hadir' => $jumlah_hadir,
                            'jumlah_absen' => $jumlah_absen_izin, // Ganti nama variabel agar konsisten
                            'persentase_kehadiran' => $persentase_kehadiran
                        ]);
                    }
                }
            }
            // Urutkan berdasarkan nama user
            $sortedSummaryItems = $summaryItems->sortBy(function($item) {
                return $item->user->name;
            });

            // Paginasi manual untuk collection
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageItems = $sortedSummaryItems->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage)->all();
            $reportData = new LengthAwarePaginator($currentPageItems, $sortedSummaryItems->count(), $itemsPerPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]);
        }

        return view('pengurus.attendance.index', compact(
            'ukmOrmawa',
            'completedActivities',
            'reportData',
            'selectedActivityId',
            'reportType',
            'activityName'
        ));
    }
}