<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScore extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'student_scores';
    protected $fillable = ['test_id', 'student_id', 'average_score', 'comment', 'date'];
}
