<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'price';
    protected $fillable = ['program', 'level', 'pointbook', 'registration', 'book', 'agenda', 'course', 'priceperday', 'execisebook'];
}
