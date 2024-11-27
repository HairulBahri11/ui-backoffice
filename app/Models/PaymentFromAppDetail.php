<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentFromAppDetail extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'payment_from_app_details';
    protected $fillable = ['payment_from_app_id', 'description', 'total'];
}
