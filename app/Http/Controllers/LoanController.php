<?php

namespace App\Http\Controllers;

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

        return response()->json(['success' => 'Buku berhasil dikembalikan.']);
    }
    
}
