<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetail extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'attendance_details';
    protected $fillable = ['attendance_id', 'student_id', 'is_absent', 'total_point', 'is_permission', 'is_alpha'];
}
