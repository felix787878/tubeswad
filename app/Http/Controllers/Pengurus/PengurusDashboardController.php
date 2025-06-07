<?php

namespace App\Http\Controllers\Pengurus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UkmApplication;
use App\Models\Article; // Jika Anda ingin menampilkan artikel terkait UKM-nya
use App\Models\Activity; // Jika Anda ingin menampilkan statistik kegiatan
use App\Models\User;
use Carbon\Carbon;

class PengurusDashboardController extends Controller
{
    public function index()
    {
        $pengurus = Auth::user();
        $ukmOrmawa = $pengurus->managesUkmOrmawa; // Ini bisa null jika pengurus baru
        // Data default jika $ukmOrmawa adalah null, agar view tidak error
        $memberCount = 0;
        $newApplicationsCount = 0;
        $publishedArticlesCount = 0; // Atau ambil artikel umum jika pengurus belum punya UKM
        $chartData = ['labels' => [], 'data' => []];
        $recentApplications = collect();

        if ($ukmOrmawa) {
            // Statistik Dashboard hanya jika ukmOrmawa ada
            $memberCount = UkmApplication::where('ukm_ormawa_id', $ukmOrmawa->id)
                                        ->where('status', 'approved')
                                        ->count();
            
            $newApplicationsCount = UkmApplication::where('ukm_ormawa_id', $ukmOrmawa->id)
                                                ->where('status', 'pending')
                                                ->count();
            

            // Data untuk Chart Pendaftaran Anggota per Bulan
            $months = [];
            $data = [];
            for ($i = 5; $i >= 0; $i--) { 
                $date = Carbon::now()->subMonths($i);
                $months[] = $date->locale('id')->isoFormat('MMM');
                $count = UkmApplication::where('ukm_ormawa_id', $ukmOrmawa->id)
                                       ->whereMonth('created_at', $date->month)
                                       ->whereYear('created_at', $date->year)
                                       ->count();
                $data[] = $count;
            }

            $chartData = [
                'labels' => $months,
                'data' => $data
            ];
            
            $recentApplications = UkmApplication::with('user')
                                              ->where('ukm_ormawa_id', $ukmOrmawa->id)
                                              ->orderBy('created_at', 'desc')
                                              ->take(5)
                                              ->get();
        }
        // JANGAN REDIRECT LAGI DARI SINI JIKA $ukmOrmawa NULL
        // if (!$ukmOrmawa) {
        //     return redirect()->route('home')->with('error', 'Anda tidak terhubung dengan UKM/Ormawa manapun untuk mengakses dashboard pengurus.');
        // }

        return view('pengurus.dashboard', compact(
            'ukmOrmawa', // Akan bernilai null jika belum ada
            'memberCount',
            'newApplicationsCount',
            'publishedArticlesCount',
            'chartData',
            'recentApplications'
        ));
    }
}