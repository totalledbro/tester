<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;
    protected $table = 'administrators';

    protected $fillable = [
        'namadepan', 'namablkg', 'email', 'password', 'verified_at'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}