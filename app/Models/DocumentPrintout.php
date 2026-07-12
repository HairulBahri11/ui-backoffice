<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentPrintout extends Model
{
    use HasFactory;

    protected $table = 'document_print_out';
    protected $guarded = ['id'];

    public function printOut()
    {
        return $this->belongsTo(PrintOut::class, 'id_printout', 'id');
    }
}
