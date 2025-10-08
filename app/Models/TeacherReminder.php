<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherReminder extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $table = 'teacher_reminder';
    protected $fillable = ['teacher_id', 'staff_id', 'created_at', 'description', 'status','category'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
