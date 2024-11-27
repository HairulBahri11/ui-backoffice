<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceDetailPoint extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'attendance_detail_points';
    protected $fillable = ['attendance_detail_id', 'point_category_id', 'point'];
}
