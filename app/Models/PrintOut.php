<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintOut extends Model
{
    use HasFactory;

    protected $table = 'print_out';

    protected $guarded = ['id'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function price()
    {
        return $this->belongsTo(Price::class, 'class_id', 'id');
    }

    public function day1()
    {
        return $this->belongsTo(Days::class, 'day1_id', 'id');
    }

    public function day2()
    {
        return $this->belongsTo(Days::class, 'day2_id', 'id');
    }

    public function documentPrintouts()
    {
        return $this->hasMany(DocumentPrintout::class, 'id_printout', 'id');
    }
}
