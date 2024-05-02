<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('auth.dashboardawal');
});

Route::get('/daftar', function () {
    return view('welcome');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::post('/', [UserController::class, 'login'])->name('login');
Route::post('logout', [UserController::class, 'logout'])->name('logout');
Route::post('/users', [UserController::class, 'register'])->name('register');