<?php

// app/Console/Commands/AutoReturnBooks.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoReturnBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:auto-return';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically return books whose loan limit date is reached';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::today();
        $loans = Loan::where('limit_date', '<=', $today)
                    ->whereNull('return_date')
                    ->get();

        foreach ($loans as $loan) {
            $loan->update(['return_date' => $today]);
            $loan->book->increment('stock');
            $loan->user->increment('limit');
            Log::info("Loan ID {$loan->id} returned automatically.");
        }

        $this->info('Auto return process completed.');
        return 0;
    }
}

