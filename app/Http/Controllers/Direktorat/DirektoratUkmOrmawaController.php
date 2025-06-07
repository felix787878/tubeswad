<?php

namespace App\Http\Controllers\Direktorat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UkmOrmawa;
use Illuminate\Support\Str;
use Carbon\Carbon; // Pastikan Carbon diimport

class DirektoratUkmOrmawaController extends Controller
{
    public function index(Request $request)
    {
        $query = UkmOrmawa::where('status', 'approved') // Hanya yang disetujui Ditmawa
                          ->whereNotNull('pengurus_id'); // <-- TAMBAHAN: Hanya yang sudah ada pengurusnya

        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }
        
        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        if ($request->filled('filter_category')) {
            $query->where('category', $request->filter_category);
        }
        
        $ukmOrmawas = $query->orderBy('name')->paginate(9);

        return view('direktorat.laporanUmum', compact('ukmOrmawas'));
    }

    public function show($slug)
    {
        // Mahasiswa hanya bisa lihat detail UKM yang sudah approved dan punya pengurus
        $item = UkmOrmawa::where('slug', $slug)
                         ->where('status', 'approved')
                         ->whereNotNull('pengurus_id') // <-- TAMBAHAN: Opsional, jika detail juga harus mengikuti aturan yang sama
                         ->firstOrFail();

        return view('direktorat.laporanUmumShow', compact('item'));
    }
}