<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistoryCategory extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'point_history_categories';
    protected $fillable = ['point_history_id', 'point_category_id'];
}
