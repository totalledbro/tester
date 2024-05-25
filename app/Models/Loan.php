<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    use HasFactory;
    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'return_date',
        'limit_date',
        'limit,'
    ];

    protected $dates = [
        'loan_date',
        'return_date',
        'limit_date',
    ];
}
