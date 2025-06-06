<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Activity;
use App\Models\ActivityAttendance;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user(); 
        $joinedActivityIds = [];
        $participationDetails = []; 

        if ($user) {
            $attendances = ActivityAttendance::where('user_id', $user->id)->get();
            $joinedActivityIds = $attendances->pluck('activity_id')->toArray();
            foreach($attendances as $att) {
                $participationDetails[$att->activity_id] = $att->status;
            }
        }

        $allPublishedActivities = Activity::where('is_published', true)
                                        ->with('ukmOrmawa') 
                                        ->orderBy('date_start', 'asc') 
                                        ->get();

        $today = Carbon::today();
        $upcoming_activities = [];
        $past_activities = [];

        foreach ($allPublishedActivities as $activity) {
            $isRegistered = in_array($activity->id, $joinedActivityIds);
            
            $statusKeikutsertaan = 'Lihat Detail'; 
            if ($isRegistered) {
                $statusKeikutsertaan = $participationDetails[$activity->id] ?? 'Terdaftar';
            }

            $registrationIsOpenForThisActivity = $activity->is_registration_open &&
                                  (!$activity->registration_deadline_activity || Carbon::parse($activity->registration_deadline_activity)->endOfDay()->isFuture());

            // Perbaiki pengecekan isActivityUpcoming
            $activityEndDate = $activity->date_end ? Carbon::parse($activity->date_end) : Carbon::parse($activity->date_start);
            $isActivityUpcoming = $activityEndDate->endOfDay()->isAfter($today) || $activityEndDate->endOfDay()->isToday();


            $activityData = (object)[
                'id' => $activity->id,
                'name' => $activity->name,
                'organizer' => $activity->ukmOrmawa->name ?? 'Penyelenggara Tidak Diketahui',
                'date_start_obj' => $activity->date_start, 
                'date_start' => $activity->date_start->format('Y-m-d'),
                'date_end' => $activity->date_end ? $activity->date_end->format('Y-m-d') : $activity->date_start->format('Y-m-d'),
                'time_start' => $activity->time_start,
                'time_end' => $activity->time_end,
                'location' => $activity->location,
                'status_keikutsertaan' => $statusKeikutsertaan,
                'type' => $activity->type,
                'image_url' => $activity->image_banner_url ? asset('storage/' . $activity->image_banner_url) : 'https://via.placeholder.com/400x200/E0E0E0/BDBDBD?text=No+Image',
                'is_registered' => $isRegistered,
                'registration_is_open' => $registrationIsOpenForThisActivity,
                'is_upcoming' => $isActivityUpcoming,
            ];
            
            if ($activityData->is_upcoming) {
                $upcoming_activities[] = $activityData;
            } else {
                // Hanya masukkan ke riwayat jika user memang terdaftar/berpartisipasi
                if($isRegistered) {
                     $activityData->status_keikutsertaan = $participationDetails[$activity->id] ?? 'Selesai'; 
                    $past_activities[] = $activityData;
                }
            }
        }
        
        usort($past_activities, function($a, $b) {
             return strcmp($b->date_start, $a->date_start);
        });

        return view('my-activities.index', compact('upcoming_activities', 'past_activities'));
    }
}