<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);
    
        $user = User::findOrFail($validatedData['user_id']);
        $book = Book::findOrFail($validatedData['book_id']);
    
        // Check if the user has reached their loan limit
        $currentLoans = Loan::where('user_id', $user->id)->whereNull('return_date')->count();
        if ($currentLoans >= $user->limit) {
            return redirect()->route('jelajahi')->with('error', 'User has reached their loan limit.');
        }
    
        // Check if the book is in stock
        if ($book->stock <= 0) {
            return redirect()->route('jelajahi')->with('error', 'Book is out of stock.');
        }
    
        // Check if the book is already loaned and not yet returned
        $existingLoan = Loan::where('book_id', $book->id)->whereNull('return_date')->first();
        if ($existingLoan) {
            return redirect()->route('jelajahi')->with('error', 'This book is already loaned out.');
        }
    
        // Decrease the book stock
        $book->decrement('stock');
    
        // Create the loan
        $loan = Loan::create([
            'user_id' => $validatedData['user_id'],
            'book_id' => $validatedData['book_id'],
            'loan_date' => Carbon::today(),
            'limit_date' => Carbon::today()->addWeek(),
        ]);
    
        // Decrease the user's loan limit
        $user->decrement('limit');
    
        return redirect()->route('jelajahi')->with('success', 'Loan created successfully.');
    }
    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        
        if ($loan->return_date) {
            return response()->json(['error' => 'Buku sudah dikembalikan.'], 400);
        }

        $loan->update(['return_date' => Carbon::now()]);

        $book = $loan->book;
        $book->increment('stock');
        
        $user = $loan->user;
        $user->increment('limit');
        return response()->json(['success' => 'Buku berhasil dikembalikan.']);
    }
    
    public function showLoans()
    {
        $user = Auth::user();
        $loans = $user->loans()->with('book')->get();
        $loanLimit = $user->limit; // User's loan limit

        return view('loans.index', [
            'loans' => $loans,
            'loanLimit' => $loanLimit
        ]);
    }

    public function readBook($id)
    {
        if (!Auth::check()) {
            return redirect()->route('403')->with('error', 'Unauthorized access.');
        }
        $user = Auth::user();
        $loan = Loan::find($id);
    
        if (!$loan || $loan->user_id != $user->id) {
            return redirect()->route('403')->with('error', 'Unauthorized access.');
        }
    
        return view('auth.baca', compact('loan'));
    }
    

    public function getBookPdf($id)
    {
        $loan = Loan::with('book')->findOrFail($id);

        if (is_null($loan->return_date)) {
            $pdfPath = storage_path('app/public/' . $loan->book->pdf_url);
            return response()->file($pdfPath);
        } else {
            abort(404, 'File not found.');
        }
    }

    public function showAllLoans(Request $request)
    {
        $sortColumn = $request->get('sort', 'loan_date');
        $sortDirection = $request->get('direction', 'desc');
    
        $loans = Loan::with('user', 'book')
                     ->orderBy($sortColumn, $sortDirection)
                     ->get();
    
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.loan_table_rows', compact('loans'))->render()
            ]);
        }
    
        return view('admin.datapinjam', compact('loans', 'sortColumn', 'sortDirection'));
    }
    
}
