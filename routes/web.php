<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAdministrator;
use App\Http\Middleware\RedirectIfNotAnggota;

Route::get('/', function () {
    $books = Book::with('category')->orderBy('id', 'desc')->limit(3)->get();
    return view('auth.dashboardawal', compact('books'));
})->name('dash');

Route::middleware([RedirectIfAdministrator::class])->group(function () {
    Route::get('/jelajahi', function () {
        $loans = Loan::all();
        $books = Book::with('category')->orderBy('id', 'desc')->get();
        return view('auth.jelajahi', compact('books', 'loans'));
    })->name('jelajahi');

    Route::get('/kategori', function(){
        $categories = Category::all();
        return view('auth.kategori', compact('categories'));
    })->name('kategori');

    Route::get('/tentang-kami', function(){
        return view('auth.tentangkami');
    })->name('tentangkami');
});

Route::middleware([AdminMiddleware::class])->group(function () {
    Route::get('/admin', function () {
        $books = Book::all();
        return view('admin.stats');
    })->name('admin');

    Route::get('/datapinjam', [LoanController::class, 'showAllLoans'])->name('datapinjam');
    Route::get('/print', [LoanController::class, 'printLoans'])->name('print');

    Route::get('/datakategori', function () {
        $categories = Category::all();
        return view('admin.datakategori', compact('categories'));
    })->name('datakategori');

    Route::get('/buku', [BookController::class, 'showAllBooks'])->name('buku');
    Route::get('/dashboard', [LoanController::class, 'adminDashboard'])->name('stats');
    Route::get('/daily-loans', [LoanController::class, 'getDailyLoans'])->name('daily-loans');
    Route::get('/daily-loan-details', [LoanController::class, 'getDailyLoanDetails'])->name('daily-loan-details');
});

Route::middleware([RedirectIfNotAnggota::class])->group(function () {
    Route::get('/pinjaman', function () {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'You need to be logged in to access this page.');
        }

        $loans = Loan::with('book')->where('user_id', $user->id)->get();
        $loanLimit = $user->limit; // Fetch the dynamic loan limit from the user model

        return view('auth.pinjaman', compact('loans', 'loanLimit'));
    })->name('pinjaman');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (!$user) {
        return redirect()->route('login')->with('error', 'User not found.');
    }

    if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
        return redirect()->route('login')->with('error', 'Invalid verification link.');
    }

    if ($user->hasVerifiedEmail()) {
        Auth::login($user);
        return view('auth.verify');  // Return the success view
    }

    $user->markEmailAsVerified();
    Auth::login($user);

    return view('auth.verify');  // Return the success view
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::get('/403', function () {
    return view('error.403');
})->name('403');

Route::get('/admins', function () {
    return view('admindasar');
});

Route::post('/', [LoginController::class, 'actionlogin'])->name('actionlogin');
Route::get('actionlogout', [LoginController::class, 'actionlogout'])->name('actionlogout')->middleware('auth');

Route::post('/users', [UserController::class, 'register'])->name('register');
Route::post('/change-password', [UserController::class, 'changePassword'])->name('changePassword');
Route::post('/forgot-password', [UserController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [UserController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [UserController::class, 'reset'])->name('password.update');
Route::get('/reset-berhasil', function () {
    return view('auth.resetberhasil');
})->name('resetberhasil');

Route::get('/categories', [CategoryController::class, 'index'])->name('products.index');
Route::post('/categories', [CategoryController::class, 'add'])->name('addcategory');
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('editcategory');
Route::post('/categories/{category}', [CategoryController::class, 'update'])->name('updatecategory');
Route::delete('/categories/{category}', [CategoryController::class, 'delete'])->name('deletecategory');
Route::get('/kategori/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/category/{id}/check', [CategoryController::class, 'checkCategoryUsage'])->name('checkcategory');

Route::get('/books',[BookController::class, 'index'])->name('books.index');
Route::post('/books',[BookController::class, 'add'])->name('addbook');
Route::post('/books/{book}',[BookController::class, 'update'])->name('updatebook');
Route::delete('/books/{book}',[BookController::class, 'delete'])->name('deletebook');

Route::get('/loans',[LoanController::class, 'index'])->name('loans.index');
Route::post('/loans',[LoanController::class, 'store'])->name('addloan');
Route::get('/baca/{id}', [LoanController::class, 'readBook'])->name('baca');
Route::get('/get-book-pdf/{id}', [LoanController::class, 'getBookPdf'])->name('get.book.pdf');

Route::post('/return-book/{id}', [LoanController::class, 'returnBook'])->name('return.book');
