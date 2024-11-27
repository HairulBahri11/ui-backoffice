<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReview extends Model
{
    use HasFactory;
    protected $fillable = ['id_attendance', 'id_teacher', 'class', 'review_test', 'due_date', 'qty', 'is_done', 'type', 'test_id'];
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }
}
