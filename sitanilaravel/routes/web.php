<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetaniController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetaniProfileController; // ← Import controller baru

/*
|--------------------------------------------------------------------------|
| Web Routes                                                               |
|--------------------------------------------------------------------------|
*/

// 1. Welcome (root)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// 2. Login (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
         ->name('login');
    Route::post('/login', [AuthController::class, 'login'])
         ->name('login.process');
    // Tampilkan form reset password
    Route::get('/resetpassword', [AuthController::class, 'showResetPasswordForm'])
    ->name('password.request');

    // Proses form reset password
    Route::post('/resetpassword', [AuthController::class, 'processResetPassword'])
    ->name('password.reset');

    // Tampilkan form registrasi
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])
    ->name('register.form');

    // Proses form registrasi
    Route::post('/register', [AuthController::class, 'processRegistration'])
            ->name('register.process');
});

// 3. Semua route berikut hanya untuk user yang sudah login
Route::middleware('auth')->group(function () {

    // 3a. Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])
         ->name('dashboard');
    Route::delete('/dashboard/{id}', [DashboardController::class, 'destroy'])
         ->name('dashboard.destroy');

    // 3b. Dashboard Petani (riwayat deteksi)
    Route::get('/dashboard-petani', [PetaniController::class, 'index'])
         ->name('dashboard.petani');
    Route::delete('/dashboard-petani/{id}', [PetaniController::class, 'destroy'])
         ->name('dashboard.petani.destroy');

    // 3c. Profil Admin (peran admin)
    Route::get('/admin/profile', [AdminController::class, 'showProfile'])
         ->name('admin.profile');
    Route::post('/admin/profile', [AdminController::class, 'updateProfile'])
         ->name('admin.profile.update');
    Route::delete('/admin/petani/{id}', [AdminController::class, 'destroyPetani'])
         ->name('admin.petani.destroy');

    // 3d. Deteksi untuk Petani
    Route::get('/petani/deteksi', [PetaniController::class, 'showDeteksiForm'])
         ->name('petani.deteksi.form');
    Route::post('/petani/deteksi', [PetaniController::class, 'processDeteksi'])
         ->name('petani.deteksi.process');

    // 3e. Profil Petani
    Route::get('/petani/profile', [PetaniProfileController::class, 'show'])
         ->name('petani.profile');
    Route::post('/petani/profile', [PetaniProfileController::class, 'update'])
         ->name('petani.profile.update');

    // 3f. Logout
    Route::post('/logout', [AuthController::class, 'logout'])
         ->name('logout');

    Route::get('/admin/trend', [DashboardController::class, 'trend'])
    ->name('admin.trend');

    Route::get('/admin/trend-data', [DashboardController::class, 'trendDataJson'])
         ->name('admin.trend.data');
});
