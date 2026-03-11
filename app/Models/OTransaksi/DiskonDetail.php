<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskonDetail extends Model
{
    use HasFactory;

    protected $table = 'diskond';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BRG", "NA_BRG", 
        "DIS", "PAR", "QTY", "TGLMX", "TGLSX", "KET"
    ];
}
