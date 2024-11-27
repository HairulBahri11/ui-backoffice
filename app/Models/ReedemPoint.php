<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReedemPoint extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'redeem_points';
    protected $fillable = ['item_id', 'point', 'student_id', 'qty', 'date'];
}
