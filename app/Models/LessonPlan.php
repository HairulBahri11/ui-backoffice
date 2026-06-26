<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Teacher;
use App\Models\Price;
use App\Models\Days;

class LessonPlan extends Model
{
    use HasFactory;

    protected $table = 'lesson_plan';

    protected $guarded = ['id'];

    // Relasi ke Teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    // Relasi ke Price (Class)
    public function price()
    {
        return $this->belongsTo(Price::class, 'class', 'id');
    }

    // Relasi ke day1
    public function day1()
    {
        return $this->belongsTo(Days::class, 'day1', 'id');
    }

    // Relasi ke day2
    public function day2()
    {
        return $this->belongsTo(Days::class, 'day2', 'id');
    }
}
