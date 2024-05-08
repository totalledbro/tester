<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    use HasFactory;
    protected $table = 'books';
    protected $fillable = ['title', 'author', 'year', 'stock', 'pdf','category_id'];
}
