<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use App\Models\UkmApplication;
use App\Models\UkmOrmawa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityManagementController extends Controller
{
    public function index(Request $request)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa untuk mengelola kegiatan.');
        }

        $query = Activity::where('ukm_ormawa_id', $ukmOrmawa->id);

        if ($request->filled('search_activity')) {
            $query->where('name', 'like', '%' . $request->search_activity . '%');
        }

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

    public function create()
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa untuk menambah kegiatan.');
        }
        return view('pengurus.activities.create', compact('ukmOrmawa'));
    }

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
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_published' => 'sometimes|boolean',
            'is_registration_open' => 'sometimes|boolean',
            'registration_deadline_activity' => 'nullable|date|required_if:is_registration_open,true|after_or_equal:today',
        ]);

        $dataToStore = $validated;
        unset($dataToStore['image_banner']); 

        $dataToStore['ukm_ormawa_id'] = $ukmOrmawa->id;
        $dataToStore['user_id'] = $pengurus->id; 
        $dataToStore['is_published'] = $request->has('is_published');
        $dataToStore['is_registration_open'] = $request->has('is_registration_open');
        
        if (!$request->has('is_registration_open')) {
            $dataToStore['registration_deadline_activity'] = null;
        }


        if ($request->hasFile('image_banner')) {
            $dataToStore['image_banner_url'] = $request->file('image_banner')->store('activity_banners', 'public');
        }

        Activity::create($dataToStore);

        return redirect()->route('pengurus.activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Activity $activity)
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa || $activity->ukm_ormawa_id !== $ukmOrmawa->id) {
            return redirect()->route('pengurus.activities.index')->with('error', 'Anda tidak berhak mengedit kegiatan ini.');
        }

        return view('pengurus.activities.edit', compact('activity', 'ukmOrmawa'));
    }

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
            'image_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
            'is_published' => 'sometimes|boolean',
            'is_registration_open' => 'sometimes|boolean',
            'registration_deadline_activity' => 'nullable|date|required_if:is_registration_open,true|after_or_equal:today',
        ]);

        $dataToUpdate = $validated;
        unset($dataToUpdate['image_banner']);

        $dataToUpdate['is_published'] = $request->has('is_published');
        $dataToUpdate['is_registration_open'] = $request->has('is_registration_open');
        
        if (!$request->has('is_registration_open')) {
            $dataToUpdate['registration_deadline_activity'] = null;
        }

        if ($request->hasFile('image_banner')) {
            if ($activity->image_banner_url && Storage::disk('public')->exists($activity->image_banner_url)) {
                Storage::disk('public')->delete($activity->image_banner_url);
            }
            $dataToUpdate['image_banner_url'] = $request->file('image_banner')->store('activity_banners', 'public');
        }

        $activity->update($dataToUpdate);

        return redirect()->route('pengurus.activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

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
        // ... (kode attendanceReport tetap sama seperti sebelumnya) ...
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa;

        if (!$ukmOrmawa) {
            return redirect()->route('pengurus.dashboard')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa.');
        }

        $completedActivities = Activity::where('ukm_ormawa_id', $ukmOrmawa->id)
                                       ->where(function($query) {
                                            $query->whereNotNull('date_end')->where('date_end', '<', now());
                                       })
                                       ->where('is_published', true)
                                       ->orderBy('date_start', 'desc')
                                       ->get();
        
        if ($completedActivities->isEmpty() && env('APP_ENV') == 'local') { 
            $completedActivities = collect([
                (object)[
                    'id' => 1, 
                    'name' => 'Workshop Fotografi Dasar (Contoh Selesai) - ' . $ukmOrmawa->name,
                    'date_start' => Carbon::now()->subMonth()->toDateString() 
                ],
                (object)[
                    'id' => 3, 
                    'name' => 'Pelatihan Kepemimpinan (Contoh Selesai) - ' . $ukmOrmawa->name,
                    'date_start' => Carbon::now()->subWeeks(2)->toDateString()
                ],
            ]);
        }

        $selectedActivityId = $request->input('activity_id');
        $reportData = null; 
        $reportType = null; 
        $activityName = null;
        $itemsPerPage = 15;
        
        if ($selectedActivityId) {
            $reportType = 'single_activity';
            $selectedActivity = Activity::where('id', $selectedActivityId)
                                        ->where('ukm_ormawa_id', $ukmOrmawa->id)
                                        ->first();
            
            if (!$selectedActivity && $completedActivities->contains('id', (int)$selectedActivityId) && env('APP_ENV') == 'local') {
                 $selectedActivity = $completedActivities->firstWhere('id', (int)$selectedActivityId); 
            }

            $activityName = $selectedActivity ? $selectedActivity->name : 'Kegiatan Tidak Ditemukan';

            if ($selectedActivity) {
                if ($selectedActivity instanceof Activity) {
                    $reportData = ActivityAttendance::with('user')
                                             ->where('activity_id', $selectedActivityId)
                                             ->join('users', 'activity_attendances.user_id', '=', 'users.id')
                                             ->orderBy('users.name')
                                             ->select('activity_attendances.*')
                                             ->paginate($itemsPerPage);
                } else { 
                    $dummyItems = collect();
                     if($selectedActivityId == 1){ 
                         $dummyItems = collect([
                            (object)['user' => (object)['name' => 'Budi Santoso', 'nim' => '102022300010'], 'status' => 'Hadir', 'notes' => '-'],
                            (object)['user' => (object)['name' => 'Citra Lestari', 'nim' => '102022300011'], 'status' => 'Absen', 'notes' => 'Tanpa keterangan'],
                        ]);
                    } elseif ($selectedActivityId == 3) {
                         $dummyItems = collect([
                            (object)['user' => (object)['name' => 'Eko Prasetyo', 'nim' => '102022300014'], 'status' => 'Hadir', 'notes' => '-'],
                        ]);
                    }
                    $reportData = new LengthAwarePaginator(
                        $dummyItems->forPage($request->page ?: 1, $itemsPerPage), $dummyItems->count(), $itemsPerPage, $request->page ?: 1,
                        ['path' => $request->url(), 'query' => $request->query()]
                    );
                }
            } else {
                $reportData = new LengthAwarePaginator([], 0, $itemsPerPage);
            }

        } else { 
            $reportType = 'overall_summary';
            $approvedMembers = UkmApplication::where('ukm_ormawa_id', $ukmOrmawa->id)
                                            ->where('status', 'approved')
                                            ->with('user')
                                            ->get();
            $summaryItems = collect();

            if ($approvedMembers->isNotEmpty()) {
                $completedActivityIdsForUkm = Activity::where('ukm_ormawa_id', $ukmOrmawa->id)
                                                ->whereNotNull('date_end')
                                                ->where('date_end', '<', now())
                                                ->pluck('id');

                foreach ($approvedMembers as $application) {
                    if (!$application->user) continue;

                    $attendances = collect();
                    if($completedActivityIdsForUkm->isNotEmpty()){
                        $attendances = ActivityAttendance::where('user_id', $application->user_id)
                                                         ->whereIn('activity_id', $completedActivityIdsForUkm)
                                                         ->get();
                    }
                    
                    $kegiatan_diikuti = $attendances->count();
                    $jumlah_hadir = $attendances->where('status', 'Hadir')->count();
                    $jumlah_absen_izin = $attendances->whereIn('status', ['Absen', 'Izin'])->count();
                    $persentase_kehadiran = $kegiatan_diikuti > 0 ? round(($jumlah_hadir / $kegiatan_diikuti) * 100) . '%' : 'N/A';

                    $summaryItems->push((object)[
                        'user' => $application->user,
                        'kegiatan_diikuti' => $kegiatan_diikuti,
                        'jumlah_hadir' => $jumlah_hadir,
                        'jumlah_absen' => $jumlah_absen_izin,
                        'persentase_kehadiran' => $persentase_kehadiran
                    ]);
                }
            }
            $sortedSummaryItems = $summaryItems->sortBy(fn($item) => $item->user->name);
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $currentPageItems = $sortedSummaryItems->slice(($currentPage - 1) * $itemsPerPage, $itemsPerPage)->all();
            $reportData = new LengthAwarePaginator($currentPageItems, $sortedSummaryItems->count(), $itemsPerPage, $currentPage, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]);
        }

        return view('pengurus.attendance.index', compact(
            'ukmOrmawa', 'completedActivities', 'reportData', 
            'selectedActivityId', 'reportType', 'activityName'
        ));
    }
}