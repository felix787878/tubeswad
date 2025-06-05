<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UkmOrmawaController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\RegistrationOpeningController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UkmOrmawaRegistrationController;
use App\Http\Controllers\Pengurus\PengurusDashboardController;
use App\Http\Controllers\Pengurus\ManagedUkmOrmawaController; // Tambahkan ini di atas
use App\Http\Controllers\Pengurus\MemberManagementController; // <--- TAMBAHKAN INI
use App\Http\Controllers\Pengurus\ActivityManagementController;
use App\Http\Controllers\Direktorat\DirektoratDashboardController;
use App\Http\Controllers\Direktorat\UkmManagementController; // Akan digunakan nanti
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;  // Pastikan ini ada


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');

Route::middleware(['auth', 'role:admin,mahasiswa'])->group(function () {
    Route::post('/articles/{article}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
});

/**
*    - Middleware: `auth` dan `role:admin`
*    - Prefix URL: `/admin`
*    - Rute-rute terkait CRUD artikel:
*        - GET `/articles` → `ArticleController@index` (route name: `admin.index`)
*        - GET `/articles/create` → `ArticleController@create` (route name: `admin.create`)
*        - POST `/articles` → `ArticleController@store` (route name: `admin.store`)
*        - GET `/articles/{article}/edit` → `ArticleController@edit` (route name: `admin.edit`)
*        - PUT `/articles/{article}` → `ArticleController@update` (route name: `admin.update`)
*        - DELETE `/articles/{article}` → `ArticleController@destroy` (route name: `admin.destroy`)
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index'])->name('admin.index');
    Route::get('/articles/create', [ArticleController::class, 'create'])->name('admin.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('admin.store');
    Route::get('/articles/{article}/edit', [ArticleController::class, 'edit'])->name('admin.edit');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('admin.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('admin.destroy');
});

Route::middleware(['auth'])->group(function () {
    // ... rute lain yang sudah ada ...

    Route::get('/ukm-ormawa', [UkmOrmawaController::class, 'index'])->name('ukm-ormawa.index');
    Route::get('/ukm-ormawa/{slug}', [UkmOrmawaController::class, 'show'])->name('ukm-ormawa.show'); // Rute untuk halaman detail
    Route::get('/kegiatan-saya', [UserActivityController::class, 'index'])->name('my-activities.index');
    Route::get('/lowongan-pendaftaran', [RegistrationOpeningController::class, 'index'])->name('registration-openings.index');
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/pengaturan/akun', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
    Route::get('/apply/{ukm_ormawa_slug}', [UkmOrmawaRegistrationController::class, 'showApplicationForm'])->name('ukm-ormawa.apply.form');
    Route::post('/apply/{ukm_ormawa_slug}', [UkmOrmawaRegistrationController::class, 'submitApplication'])->name('ukm-ormawa.apply.submit');

});

Route::middleware(['auth', 'role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', [PengurusDashboardController::class, 'index'])->name('dashboard');
    
    // Menggunakan satu method di controller untuk menampilkan form create atau edit
    Route::get('/ukm-ormawa/kelola', [ManagedUkmOrmawaController::class, 'editOrCreate'])->name('ukm-ormawa.edit'); // Tetap .edit untuk kemudahan, controller yg atur
    Route::post('/ukm-ormawa/buat', [ManagedUkmOrmawaController::class, 'store'])->name('ukm-ormawa.store'); // Route untuk menyimpan UKM baru
    Route::put('/ukm-ormawa/kelola', [ManagedUkmOrmawaController::class, 'update'])->name('ukm-ormawa.update'); // Route untuk update

    // ... (rute manajemen anggota dan kegiatan lainnya) ...
    Route::get('/members', [MemberManagementController::class, 'index'])->name('members.index');
    Route::get('/members/{application}/show', [MemberManagementController::class, 'showApplication'])->name('members.show');
    Route::patch('/members/{application}/status', [MemberManagementController::class, 'updateStatus'])->name('members.updateStatus');
    
    Route::resource('activities', ActivityManagementController::class)->except(['show']);
    Route::get('/attendance-reports', [ActivityManagementController::class, 'attendanceReport'])->name('attendance.reports');
});

Route::middleware(['auth', 'role:direktorat'])->prefix('direktorat')->name('direktorat.')->group(function () {
    Route::get('/dashboard', [DirektoratDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/ukm-ormawa', [UkmManagementController::class, 'index'])->name('ukm-ormawa.index');
    Route::get('/ukm-ormawa/{ukmOrmawa}/show', [UkmManagementController::class, 'show'])->name('ukm-ormawa.show');
    Route::patch('/ukm-ormawa/{ukmOrmawa}/update-status', [UkmManagementController::class, 'updateStatus'])->name('ukm-ormawa.updateStatus');
    
    // CRUD routes by Direktorat
    Route::get('/ukm-ormawa/{ukmOrmawa}/edit', [UkmManagementController::class, 'edit'])->name('ukm-ormawa.edit');
    Route::put('/ukm-ormawa/{ukmOrmawa}', [UkmManagementController::class, 'update'])->name('ukm-ormawa.update');
    Route::delete('/ukm-ormawa/{ukmOrmawa}', [UkmManagementController::class, 'destroy'])->name('ukm-ormawa.destroy');
});

Route::get('/proxy/goapi/regional/{endpoint}', function (Request $request, $endpoint) {
    $apiKey = env('GOAPI_API_KEY', 'FALLBACK_KEY_JIKA_TIDAK_ADA_DI_ENV'); // Ambil dari .env
    $baseUrl = 'https://api.goapi.io/regional';

    if (empty($apiKey) || $apiKey === 'FALLBACK_KEY_JIKA_TIDAK_ADA_DI_ENV') {
        \Log::error('GOAPI_API_KEY is not set correctly in .env file or is using fallback.');
        return response()->json([
            'status' => 'error',
            'message' => 'API key for GoAPI is not configured on the server.'
        ], 500);
    }

    $queryParams = $request->query();
    $queryParams['api_key'] = $apiKey;

    try {
        // \Log::info("Proxying (from web.php) GoAPI request to: {$baseUrl}/{$endpoint} with params: ", $queryParams);
        $response = Http::timeout(15)->get("{$baseUrl}/{$endpoint}", $queryParams);

        if ($response->successful()) {
            return $response->json(); 
        } else {
            \Log::error("GoAPI Error (from web.php) - Status: " . $response->status() . " Body: " . $response->body());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch data from GoAPI',
                'goapi_status' => $response->status(),
                'goapi_body' => $response->json() ?: $response->body()
            ], $response->status() == 0 ? 500 : $response->status());
        }
    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        \Log::error('GoAPI Connection Exception (from web.php): ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Could not connect to GoAPI: ' . $e->getMessage()], 503);
    } catch (\Exception $e) {
        \Log::error('GoAPI Generic Proxy Exception (from web.php): ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred.'], 500);
    }
})->where('endpoint', '(provinsi|kota|kecamatan|kelurahan)')
  ->name('proxy.goapi.regional'); // Memberi nama route (opsional tapi baik)