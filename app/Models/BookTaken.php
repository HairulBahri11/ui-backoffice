<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookTaken extends Model
{
    use HasFactory;

    protected $table = 'book_taken';

    protected $guarded = ['id'];
}
