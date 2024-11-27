<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Students extends Model
{
    public $timestamps = false;
    protected $table = 'student';
    use HasFactory;

    /**
     * Get all of the comments for the Students
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function score()
    {
        return $this->hasMany(StudentScore::class, 'student_id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'id_teacher');
    }
}
