<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UkmOrmawaController;
use App\Http\Controllers\UserActivityController;
use App\Http\Controllers\RegistrationOpeningController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UkmOrmawaRegistrationController;

use App\Http\Controllers\Pengurus\PengurusDashboardController;
use App\Http\Controllers\Pengurus\ManagedUkmOrmawaController;
use App\Http\Controllers\Pengurus\MemberManagementController;
use App\Http\Controllers\Pengurus\ActivityManagementController as PengurusActivityController;
use App\Http\Controllers\Pengurus\PengurusSettingsController;

use App\Http\Controllers\ActivityController; // Untuk publik dan pendaftaran kegiatan

use App\Http\Controllers\Direktorat\DirektoratDashboardController;
use App\Http\Controllers\Direktorat\UkmManagementController;
use App\Http\Controllers\Direktorat\DirektoratSettingsController;
use App\Http\Controllers\Direktorat\DirektoratUkmOrmawaController;

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show'); // Asumsi {article} adalah ID

// Authenticated User Routes (Mahasiswa, Pengurus, Admin, Direktorat)
Route::middleware(['auth'])->group(function () {
    // Comments (Mahasiswa & Admin)
    Route::middleware(['role:admin,mahasiswa'])->group(function () {
        Route::post('/articles/{article}/comments', [CommentController::class, 'store']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
    });

    // UKM & Ormawa Pages
    Route::get('/ukm-ormawa', [UkmOrmawaController::class, 'index'])->name('ukm-ormawa.index');
    Route::get('/ukm-ormawa/{slug}', [UkmOrmawaController::class, 'show'])->name('ukm-ormawa.show');
    
    // Kegiatan Pages
    Route::get('/kegiatan-saya', [UserActivityController::class, 'index'])->name('my-activities.index');
    Route::get('/kegiatan/{activity}', [ActivityController::class, 'showPublic'])->name('activities.public.show')->where('activity', '[0-9]+');
    Route::post('/kegiatan/{activity}/register', [ActivityController::class, 'registerToActivity'])->name('activities.register');
    Route::post('/kegiatan/{activity}/unregister', [ActivityController::class, 'unregisterFromActivity'])->name('activities.unregister');

    // Pengaturan Akun
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/pengaturan/akun', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');

    // Pendaftaran ke UKM/Ormawa
    Route::get('/apply/{ukm_ormawa_slug}', [UkmOrmawaRegistrationController::class, 'showApplicationForm'])->name('ukm-ormawa.apply.form');
    Route::post('/apply/{ukm_ormawa_slug}', [UkmOrmawaRegistrationController::class, 'submitApplication'])->name('ukm-ormawa.apply.submit');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/articles', [ArticleController::class, 'index'])->name('index'); // admin.index
    Route::resource('articles', ArticleController::class)->except(['index', 'show']); // Menggunakan resource untuk CRUD lain

    // Pengaturan Akun
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/pengaturan/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/pengaturan/akun', [SettingsController::class, 'deleteAccount'])->name('settings.account.delete');
});

// Pengurus Routes
Route::middleware(['auth', 'role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
    Route::get('/dashboard', [PengurusDashboardController::class, 'index'])->name('dashboard');
    Route::get('/ukm-ormawa/kelola', [ManagedUkmOrmawaController::class, 'editOrCreate'])->name('ukm-ormawa.edit');// Akan ke create jika belum ada
    Route::get('/ukm-ormawa/buat', [ManagedUkmOrmawaController::class, 'create'])->name('ukm-ormawa.store');
    Route::post('/ukm-ormawa/buat', [ManagedUkmOrmawaController::class, 'store'])->name('ukm-ormawa.store');
    Route::put('/ukm-ormawa/kelola', [ManagedUkmOrmawaController::class, 'update'])->name('ukm-ormawa.update');
    
    Route::get('/members', [MemberManagementController::class, 'index'])->name('members.index');
    Route::get('/members/{application}/show', [MemberManagementController::class, 'showApplication'])->name('members.show');
    Route::patch('/members/{application}/status', [MemberManagementController::class, 'updateStatus'])->name('members.updateStatus');
    Route::delete('/members/{application}', [MemberManagementController::class, 'destroy'])->name('members.destroy');
    
    Route::resource('activities', PengurusActivityController::class)->except(['show']);
    Route::get('/attendance-reports', [PengurusActivityController::class, 'attendanceReport'])->name('attendance.reports');

    // Pengaturan Akun
    Route::get('/pengaturan', [PengurusSettingsController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil', [PengurusSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/pengaturan/password', [PengurusSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/pengaturan/akun', [PengurusSettingsController::class, 'deleteAccount'])->name('settings.account.delete');
});

// Direktorat Routes
Route::middleware(['auth', 'role:direktorat'])->prefix('direktorat')->name('direktorat.')->group(function () {
    Route::get('/dashboard', [DirektoratDashboardController::class, 'index'])->name('dashboard');

    Route::get('/laporan-umum', [DirektoratUkmOrmawaController::class, 'index'])->name('laporan-umum');
    // Route::get('/verif-ukm-ormawa/{ukmOrmawa}/show', [DirektoratUkmOrmawaController::class, 'show'])->name('laporan-umum.show');
    Route::get('/laporan-umum/{slug}', [DirektoratUkmOrmawaController::class, 'show'])->name('laporan-umum.show');

    Route::get('/verif-ukm-ormawa', [UkmManagementController::class, 'index'])->name('ukm-ormawa.index');
    Route::get('/verif-ukm-ormawa/{ukmOrmawa}/show', [UkmManagementController::class, 'show'])->name('ukm-ormawa.show');
    Route::patch('/verif-ukm-ormawa/{ukmOrmawa}/update-status', [UkmManagementController::class, 'updateStatus'])->name('ukm-ormawa.updateStatus');
    Route::get('/verif-ukm-ormawa/{ukmOrmawa}/edit', [UkmManagementController::class, 'edit'])->name('ukm-ormawa.edit');
    Route::put('/verif-ukm-ormawa/{ukmOrmawa}', [UkmManagementController::class, 'update'])->name('ukm-ormawa.update');
    Route::delete('/verif-ukm-ormawa/{ukmOrmawa}', [UkmManagementController::class, 'destroy'])->name('ukm-ormawa.destroy');

    // Pengaturan Akun
    Route::get('/pengaturan', [DirektoratSettingsController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan/profil', [DirektoratSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::post('/pengaturan/password', [DirektoratSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::delete('/pengaturan/akun', [DirektoratSettingsController::class, 'deleteAccount'])->name('settings.account.delete');
});

// Proxy API GoAPI Regional (jika diletakkan di web.php)
Route::get('/proxy/goapi/regional/{endpoint}', function (Request $request, $endpoint) {
    $apiKey = env('GOAPI_API_KEY'); 
    $baseUrl = 'https://api.goapi.io/regional';

    if (empty($apiKey)) {
        \Log::error('GOAPI_API_KEY is not set in .env file.');
        return response()->json(['status' => 'error', 'message' => 'API key for GoAPI is not configured on the server.'], 500);
    }
    $queryParams = $request->query();
    $queryParams['api_key'] = $apiKey;
    try {
        $response = Http::timeout(15)->get("{$baseUrl}/{$endpoint}", $queryParams);
        if ($response->successful()) {
            return $response->json(); 
        } else {
            \Log::error("GoAPI Error (from web.php proxy) - Status: " . $response->status() . " Body: " . $response->body());
            return response()->json([
                'status' => 'error', 'message' => 'Failed to fetch data from GoAPI',
                'goapi_status' => $response->status(), 'goapi_body' => $response->json() ?: $response->body()
            ], $response->status() == 0 ? 500 : $response->status());
        }
    } catch (\Illuminate\Http\Client\ConnectionException $e) {
        \Log::error('GoAPI Connection Exception (from web.php proxy): ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'Could not connect to GoAPI: ' . $e->getMessage()], 503);
    } catch (\Exception $e) {
        \Log::error('GoAPI Generic Proxy Exception (from web.php proxy): ' . $e->getMessage());
        return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred while proxying to GoAPI.'], 500);
    }
})->where('endpoint', '(provinsi|kota|kecamatan|kelurahan)')->name('proxy.goapi.regional');