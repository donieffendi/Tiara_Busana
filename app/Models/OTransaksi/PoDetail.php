<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    use HasFactory;

    protected $table = 'pod';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BRG", "NA_BRG","SATUAN","QTY", "HARGA", 
        "TOTAL", "KET", "GOL", "FLAG", "PER", "SISA", "LAKU"
    ];
}
