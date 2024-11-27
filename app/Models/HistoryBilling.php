<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryBilling extends Model
{
    use HasFactory;

    protected $table = 'history_billing';
    protected $fillable = ['amount', 'created_by', 'unique_code'];
}
