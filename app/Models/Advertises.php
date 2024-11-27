<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertises extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'advertises';
    protected $fillable = ['title', 'banner', 'description'];
}
