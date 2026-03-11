<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeliDetail extends Model
{
    use HasFactory;

    protected $table = 'belid';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BRG", "NA_BRG", "BARCODE", 
        "JNS", "QTY", 
        "HARGA", "MARGIN", "DISKON1", "DISKON2", "PER", "FLAG",
        "DISKON3", "DISKON4", "TOTAL",
        "HARGA_JL", "BLT", "CBG"
    ];
}
