<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'attendances';
    protected $fillable = ['price_id', 'day1', 'day2', 'course_time', 'date', 'teacher_id', 'activity', 'text_book', 'excercise_book', 'is_presence', 'id_test', 'date_review', 'date_test', 'is_class_new'];

    /**
     * Get all of the detail for the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detail()
    {
        return $this->hasMany(AttendanceDetail::class, 'attendance_id');
    }
}
