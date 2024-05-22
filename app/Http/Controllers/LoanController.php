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
     * Display a listing of the loans.
     */
    public function index()
    {
        $loans = Loan::with(['user', 'book'])->get();
        return view('loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new loan.
     */
    public function create()
    {
        $users = User::all();
        $books = Book::all();
        return view('loans.create', compact('users', 'books'));
    }

    /**
     * Store a newly created loan in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'loan_date' => 'required|date',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $book = Book::findOrFail($validatedData['book_id']);

        // Check if the user has reached their loan limit
        $currentLoans = Loan::where('user_id', $user->id)->whereNull('return_date')->count();
        if ($currentLoans >= $user->limit) {
            return redirect()->route('loans.index')->with('error', 'User has reached their loan limit.');
        }

        // Check if the book is in stock
        if ($book->stock <= 0) {
            return redirect()->route('loans.index')->with('error', 'Book is out of stock.');
        }

        // Decrease the book stock
        $book->decrement('stock');

        // Create the loan
        $loan = Loan::create([
            'user_id' => $validatedData['user_id'],
            'book_id' => $validatedData['book_id'],
            'loan_date' => $validatedData['loan_date'],
            'limit_date' => Carbon::parse($validatedData['loan_date'])->addWeek(),
        ]);

        return redirect()->route('jelajahi')->with('success', 'Loan created successfully.');
    }

    /**
     * Show the form for editing the specified loan.
     */
    public function edit(Loan $loan)
    {
        $users = User::all();
        $books = Book::all();
        return view('loans.edit', compact('loan', 'users', 'books'));
    }

    /**
     * Update the specified loan in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        // Validate the return date
        $validatedData = $request->validate([
            'return_date' => 'required|date|after_or_equal:loan_date',
        ]);

        // Check if the return date is today's date
        $today = Carbon::today();
        $returnDate = Carbon::parse($validatedData['return_date']);
        
        if ($today->eq($returnDate)) {
            // Today is the return date, auto-return the book
            $this->autoReturnBook($loan);
            return redirect()->route('loans.index')->with('success', 'Book returned successfully.');
        }

        // Update the loan with the return date
        $loan->update(['return_date' => $validatedData['return_date']]);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    /**
     * Manually return the specified loan.
     */
    public function return(Loan $loan)
    {
        // Increase the book stock
        $loan->book->increment('stock');

        // Set the return date to today
        $loan->update(['return_date' => Carbon::today()]);

        return redirect()->route('loans.index')->with('success', 'Book returned successfully.');
    }

    /**
     * Auto-return the specified loan.
     */
    protected function autoReturnBook(Loan $loan)
    {
        // Increase the book stock
        $loan->book->increment('stock');

        // Set the return date to today
        $loan->update(['return_date' => Carbon::today()]);
    }
}
