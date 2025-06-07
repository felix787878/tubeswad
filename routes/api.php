<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pengurus\ManagedUkmOrmawaController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/cari-alamat', [ManagedUkmOrmawaController::class, 'cariAlamat'])->name('api.alamat.search');
// Pengurus Routes
// Route::middleware(['auth', 'role:pengurus'])->prefix('pengurus')->name('pengurus.')->group(function () {
//     Route::get('/cari-alamat', [ManagedUkmOrmawaController::class, 'cariAlamat'])->name('api.alamat.search');
// });

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});