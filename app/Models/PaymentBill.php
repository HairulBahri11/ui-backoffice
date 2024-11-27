<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentBill extends Model
{
    use HasFactory;

    protected $table = 'payment_bills';
    protected $fillable = ['total_price', 'class_type', 'created_at', 'updated_by'];
}
