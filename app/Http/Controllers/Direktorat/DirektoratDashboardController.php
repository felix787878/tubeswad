<?php

namespace App\Http\Controllers\Direktorat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UkmOrmawa;
use App\Models\User;
use App\Models\UkmApplication;

class DirektoratDashboardController extends Controller
{
    public function index()
    {
        $totalUkmOrmawa = UkmOrmawa::count();
        $pendingVerification = UkmOrmawa::where('status', 'pending_verification')->count();
        $approvedUkmOrmawa = UkmOrmawa::where('status', 'approved')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        // Menghitung jumlah mahasiswa unik yang pernah mendaftar ke salah satu UKM/Ormawa
        $totalPendaftarUkm = UkmApplication::distinct('user_id')->count('user_id'); 

        $recentPendingUkm = UkmOrmawa::where('status', 'pending_verification')
                                    ->with('pengurus') // Eager load data pengurus
                                    ->orderBy('created_at', 'desc')
                                    ->take(5)
                                    ->get();
        
        return view('direktorat.dashboard', compact(
            'totalUkmOrmawa',
            'pendingVerification',
            'approvedUkmOrmawa',
            'totalMahasiswa',
            'totalPendaftarUkm', // Ganti nama variabel agar lebih jelas
            'recentPendingUkm'
        ));
    }
}