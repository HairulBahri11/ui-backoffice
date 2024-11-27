<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFromApp extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'payment_from_apps';
    protected $fillable = ['student_id', 'transaction_id', 'date', 'total', 'status'];
}
