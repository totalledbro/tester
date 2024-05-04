<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;
use App\Models\Category;

Route::get('/', function () {
    return view('auth.dashboardawal');
})->name('dash');

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::get('/kategori', function () {
    $categories = Category::all();
    return view('admin.kategori',compact('categories'));
});
Route::get('/admins', function () {
    return view('admindasar');
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

Route::get('/categories', [CategoryController::class, 'index'])->name('products.index');