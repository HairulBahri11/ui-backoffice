<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentStudents extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'parent_students';
    protected $fillable = ['parent_id','student_id'];
}
