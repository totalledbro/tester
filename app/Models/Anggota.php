<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasapiTokens, HasFactory, Notifiable;
    protected $table = 'anggota';

    protected $fillable = [
        'namadepan', 'namablkg', 'email', 'password', 'verified_at', 'limit'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}