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
            return response()->json(['message' => 'Anda sudah mencapai limit pinjam.'], 400);
        }

        // Check if the book is in stock
        if ($book->stock <= 0) {
            return response()->json(['message' => 'Buku ini sedang habis dipinjam, coba lagi nanti.'], 400);
        }

        // Check if the book is already loaned and not yet returned
        $existingLoan = Loan::where('book_id', $book->id)->where('user_id', $user->id)->whereNull('return_date')->first();
        if ($existingLoan) {
            return response()->json(['message' => 'Anda telah meminjam buku ini.'], 400);
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

        return response()->json(['message' => 'Peminjaman buku berhasil ditambahkan'], 200);
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
        $perPage = (int) $request->get('perPage', 10);
        $search = $request->get('search', '');
        
        $query = Loan::with('user', 'book');
        
        if ($search) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', '%' . $search . '%')
                      ->orWhere('last_name', 'like', '%' . $search . '%');
            })->orWhereHas('book', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            });
        }
        
        $loans = $query->orderBy('loan_date', 'desc')->paginate($perPage);
        
        // Render the partial view into HTML
        $html = view('partial.loan_table', compact('loans'))->render();
        
        if ($request->ajax()) {
            // Return JSON response with the HTML view
            return response()->json([
                'currentPage' => $loans->currentPage(),
                'lastPage' => $loans->lastPage(),
                'url' => route('datapinjam'), // Base URL for pagination links
                'html' => $html
            ]);
        }
        
        // Return the regular Blade view with data for non-AJAX requests
        return view('admin.datapinjam', compact('loans', 'perPage', 'search', 'html'));
    }
    
    
    
    public function adminDashboard()
{
    $books = Book::all();
    $userCount = User::where('role', 'anggota')->count();

    $years = range(date('Y') - 4, date('Y'));
    $yearlyData = [];
    $monthsIndonesian = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    foreach ($years as $year) {
        $loanData = Loan::selectRaw('COUNT(*) as count, DATE_FORMAT(loan_date, "%Y-%m") as month')
            ->whereYear('loan_date', $year)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $data = collect();
        foreach ($loanData as $loan) {
            $data->put($loan->month, $loan->count);
        }

        $months = collect();
        $counts = collect();
        for ($i = 1; $i <= 12; $i++) {
            $month = sprintf('%s %d', $monthsIndonesian[$i - 1], $year);
            $months->push($month);
            $counts->push($data->get(sprintf('%d-%02d', $year, $i), 0));
        }

        $yearlyData[] = [
            'year' => $year,
            'months' => $months,
            'counts' => $counts
        ];
    }

    return view('admin.stats', compact('books', 'userCount', 'yearlyData'));
}

public function getDailyLoans(Request $request)
{
    $year = $request->input('year');
    $monthName = $request->input('month');

    $months = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 'Mei' => 5, 'Juni' => 6, 
        'Juli' => 7, 'Agustus' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
    ];

    if (!isset($months[$monthName])) {
        return response()->json(['error' => 'Invalid month name'], 400);
    }
    $month = $months[$monthName];

    $dailyLoans = Loan::selectRaw('DAY(loan_date) as day, COUNT(*) as count')
        ->whereYear('loan_date', $year)
        ->whereMonth('loan_date', $month)
        ->groupBy('day')
        ->get()
        ->keyBy('day');

    $daysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;
    $data = [];
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $data[] = $dailyLoans->get($day, (object) ['count' => 0])->count;
    }

    return response()->json([
        'days' => range(1, $daysInMonth),
        'data' => $data
    ]);
}

public function getDailyLoanDetails(Request $request)
{
    $date = $request->input('date');

    $loans = Loan::with('user', 'book')
        ->whereDate('loan_date', $date)
        ->get();

    return response()->json($loans);
}


    public function printLoans(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        Carbon::setLocale('id');
        $query = Loan::with('user', 'book');

        if ($startDate && $endDate) {
            $query->whereBetween('loan_date', [$startDate, $endDate]);
        }

        $loans = $query->get();

        return view('partial.printview', [
            'loans' => $loans,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
}
