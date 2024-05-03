<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;

Route::get('/', function () {
    return view('auth.dashboardawal');
})->name('dash');

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');
Route::get('/verify',function (){
    return view('auth.verify');
})->name('verify');

Route::post('/', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::post('/users', [UserController::class, 'register'])->name('register');