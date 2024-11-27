<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announces extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'announces';
    protected $fillable = ['banner', 'description', 'announce_for'];
}
