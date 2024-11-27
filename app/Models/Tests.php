<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tests extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'tests';
    protected $fillable = ['name'];
}
