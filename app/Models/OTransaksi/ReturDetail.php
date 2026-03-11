<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturDetail extends Model
{
    use HasFactory;

    protected $table = 'returd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BRG", "NA_BRG", 
        "BARCODE", "QTYK", "HARGA", "QTY", "KET", "TGL_MULAI"
    ];
}
