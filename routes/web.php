<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('beranda');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::patch('/dashboard/pengajuan/{application}', [DashboardController::class, 'update'])->name('dashboard.pengajuan.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
