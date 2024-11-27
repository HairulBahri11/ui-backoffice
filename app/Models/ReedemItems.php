<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReedemItems extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'redeem_items';
    protected $fillable = ['item', 'point'];
}
