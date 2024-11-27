<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Staff extends Authenticatable
{
    public $timestamps = false;
    use HasFactory;
    protected $table = "staff";
    protected $fillable = ['name', 'username', 'password'];
    protected $hidden = [
        'password'
    ];
}
