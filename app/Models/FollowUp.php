<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;
    protected $table = 'follow_up';

    /**
     * Get the user that owns the FollowUp
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(Price::class, 'old_price_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'old_teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(Students::class, 'student_id');
    }

    public function score()
    {
        return $this->hasMany(StudentScore::class, 'student_id');
    }
}
