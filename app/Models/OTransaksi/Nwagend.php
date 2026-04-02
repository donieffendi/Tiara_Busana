<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Nwagend extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwagend';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI",
        "SP",
        "KODES",
        "NAMAS",
        "TGL",
        "JT",
        "PER",
        "ALMT_K",
        "KOTA",
        "NOTES",
        "POSTED",
        "TG_POST",
        "USRNM",
        "TOTAL",
        "FLAG",
        "DPP",
        "PROM",
        "PPN",
        "NETT",
        "TG_SMP",
        "CBG",
        // "POT_PROM",
        "ST_PJK",
        "ST_NOTA"
    ];
}
