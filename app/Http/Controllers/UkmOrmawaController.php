<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UkmOrmawa;
use Illuminate\Support\Str;

class UkmOrmawaController extends Controller
{
    public function index(Request $request)
    {
        $query = UkmOrmawa::where('status', 'approved'); // <-- HANYA TAMPILKAN YANG APPROVED

        if ($request->filled('search_name')) {
            $query->where('name', 'like', '%' . $request->search_name . '%');
        }
        // ... (filter lainnya tetap sama) ...
        if ($request->filled('filter_type')) {
            $query->where('type', $request->filter_type);
        }

        if ($request->filled('filter_category')) {
            $query->where('category', $request->filter_category);
        }
        
        $ukmOrmawas = $query->orderBy('name')->paginate(9);

        return view('ukm-ormawa.index', compact('ukmOrmawas'));
    }

    public function show($slug)
    {
        // Mahasiswa hanya bisa lihat detail UKM yang sudah approved
        $item = UkmOrmawa::where('slug', $slug)
                         ->where('status', 'approved') // <-- HANYA TAMPILKAN DETAIL YANG APPROVED
                         ->firstOrFail();

        return view('ukm-ormawa.show', compact('item'));
    }
}