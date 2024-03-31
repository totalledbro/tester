<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministratorController;
use App\Http\Controllers\AnggotaController;
Route::get('/', function () {
    return view('auth.coba');
});

Route::get('/daftar', function () {
    return view('auth.daftar');
});

Route::get('/verifikasi', function () {
    return view('auth.verifikasi');
});

Route::get('/administrators', [AdministratorController::class, 'index'])->name('administrators.index');
Route::get('/administrators/create', [AdministratorController::class, 'create'])->name('administrators.create');
Route::post('/administrators', [AdministratorController::class, 'store'])->name('administrators.store');
Route::get('/administrators/{id}', [AdministratorController::class, 'show'])->name('administrators.show');
Route::get('/administrators/{id}/edit', [AdministratorController::class, 'edit'])->name('administrators.edit');
Route::put('/administrators/{id}', [AdministratorController::class, 'update'])->name('administrators.update');
Route::delete('/administrators/{id}', [AdministratorController::class, 'destroy'])->name('administrators.destroy');
Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.create');
Route::get('/anggota/{id}', [AnggotaController::class, 'show'])->name('anggota.show');
Route::get('/anggota/{id}/edit', [AnggotaController::class, 'edit'])->name('anggota.edit');
Route::put('/anggota/{id}', [AnggotaController::class, 'update'])->name('anggota.update');
Route::delete('/anggota/{id}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
Route::post('/login', [AnggotaController::class, 'login']);
Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');