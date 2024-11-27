<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentBillDetail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'payment_bill_detail';
    protected $fillable = ['id_payment_bill', 'student_id', 'category', 'price', 'unique_code', 'payment', 'status'];
}
