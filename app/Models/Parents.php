<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Parents extends Authenticatable  implements JWTSubject
{
    public $timestamps = false;
    use HasFactory, Notifiable;
    protected $table = 'parents';
    protected $fillable = ['name', 'no_hp', 'gender', 'otp', 'password'];

    protected $hidden = [
        'password',
        'otp',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
}
