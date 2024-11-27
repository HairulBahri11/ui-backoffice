<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentScoreDetail extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'student_score_details';
    protected $fillable = ['student_score_id', 'test_item_id', 'score', 'created_at', 'updated_at'];
}
