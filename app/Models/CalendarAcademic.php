<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarAcademic extends Model
{
    use HasFactory;
    protected $table = 'calendar_academic';
    protected $fillable = [
        'created_by', 
        'title', 
        'detail', 
        'start', 
        'end', 
        'category'
    ];

    // Relasi ke User/Staff jika diperlukan
    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by', 'id');
    }
}

