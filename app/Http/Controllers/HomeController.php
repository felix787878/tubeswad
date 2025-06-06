<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\UkmOrmawa;
use App\Models\UkmApplication;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
       // $announcements = Article::where('is_published', true)
                                //->latest()
                                //->take(3)
                                //->get();

        $openRegistrations = UkmOrmawa::where('status', 'approved') // <-- TAMBAHKAN CEK STATUS
                                      ->where('is_registration_open', true)
                                      ->where(function ($query) {
                                          $query->whereNull('registration_deadline')
                                                ->orWhere('registration_deadline', '>=', Carbon::today());
                                      })
                                      ->orderBy('registration_deadline', 'asc')
                                      ->take(2)
                                      ->get();

        $joinedUkms = collect();
        if (Auth::check()) {
            $joinedUkms = UkmApplication::where('user_id', Auth::id())
                                       ->where('status', 'approved')
                                       ->whereHas('ukmOrmawa', function ($q) { // Pastikan UKM terkait juga approved
                                           $q->where('status', 'approved');
                                       })
                                       ->with('ukmOrmawa')
                                       ->get()
                                       ->map(function ($application) {
                                           if ($application->ukmOrmawa) {
                                               return (object)[
                                                   'name' => $application->ukmOrmawa->name,
                                                   'slug' => $application->ukmOrmawa->slug,
                                                   'status' => 'Anggota Aktif',
                                                   'type' => $application->ukmOrmawa->type,
                                                   'image_url' => $application->ukmOrmawa->logo_url ? asset('storage/' . $application->ukmOrmawa->logo_url) : 'https://via.placeholder.com/80x80/'.($application->ukmOrmawa->type === 'UKM' ? 'FFC0CB' : 'ADD8E6').'/000000?text='.strtoupper(substr($application->ukmOrmawa->name, 0, 2)),
                                               ];
                                           }
                                           return null;
                                       })->filter();
        }
        
        //$articles = Article::where('is_published', true) 
                           //->latest()
                           //->take(5)
                          // ->get();

        return view('home', compact( 'openRegistrations', 'joinedUkms'));
    }
}