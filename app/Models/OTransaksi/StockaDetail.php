<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockaDetail extends Model
{
    use HasFactory;

    protected $table = 'stockad';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BHN", "NA_BHN", "SATUAN" , "QTY", "QTYC", "QTYR", "KET"
    ];
}
