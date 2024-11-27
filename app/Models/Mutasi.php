<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    public $timestamps = false;
    use HasFactory;
    protected $table = 'mutasi_siswa';

    /**
     * Get the user that owns the Mutasi
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Price::class, 'price_id');
    }
    public function score()
    {
        return $this->belongsTo(StudentScore::class, 'score_id');
    }
}
