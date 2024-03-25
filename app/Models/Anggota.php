<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Laravel\Sanctum\HasapiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Authenticatable
{
    use HasFactory;
    protected $table = 'anggota';

    protected $fillable = [
        'namadepan', 'namablkg', 'email', 'password', 'verified_at', 'limit'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}