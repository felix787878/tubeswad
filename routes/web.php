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
    
    // Route untuk manajemen UKM/Ormawa oleh Direktorat (daftar, verifikasi, dll.)
    // Ini akan kita buat di langkah selanjutnya, untuk sekarang kita buat placeholder
    Route::get('/ukm-ormawa', function () { 
        // Sementara, ambil data seperti di dashboard atau buat method khusus di UkmManagementController
        $ukmOrmawas = \App\Models\UkmOrmawa::orderBy('status')->orderBy('name')->paginate(10);
        return view('direktorat.ukm-management.index', compact('ukmOrmawas')); 
    })->name('ukm-ormawa.index');

    Route::get('/ukm-ormawa/{ukmOrmawa}/show', function (\App\Models\UkmOrmawa $ukmOrmawa) {
        // return view('direktorat.ukm-management.show', compact('ukmOrmawa'));
        return redirect()->route('direktorat.ukm-ormawa.index')->with('warning', 'Halaman detail verifikasi belum diimplementasikan sepenuhnya.');
    })->name('ukm-ormawa.show');
    
    Route::patch('/ukm-ormawa/{ukmOrmawa}/update-status', function (\Illuminate\Http\Request $request, \App\Models\UkmOrmawa $ukmOrmawa) {
        // Logika untuk update status oleh direktorat
        // $ukmOrmawa->status = $request->status;
        // $ukmOrmawa->save();
        // return back()->with('success', 'Status UKM/Ormawa berhasil diperbarui.');
        return redirect()->route('direktorat.ukm-ormawa.index')->with('warning', 'Fitur update status belum diimplementasikan sepenuhnya.');
    })->name('ukm-ormawa.updateStatus');


});