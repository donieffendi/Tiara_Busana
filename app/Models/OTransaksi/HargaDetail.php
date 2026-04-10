<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaDetail extends Model
{
    use HasFactory;

    protected $table = 'bhrgd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [

        "REC", "NO_BUKTI", "ID", "KD_BRG", "BARCODE", 
        "NA_BRG", "JNS", "HARGAJL", "HARGAKSR", "HARGA",
        "KET", "SISA", "DTH"
    ];
}
