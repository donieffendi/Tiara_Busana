<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Tagi extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'tagi';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [

        "NO_BUKTI", "TGL", "PER", "FLAG", "NOTES",
		"USRNM", "TG_SMP", "CBG", "TYPE", "NOTRANS",
        "NOREK", "NMBANK", "TGTF", "KOTA", "ALAMAT",
        "RETUR", "TOTALX", "BLAIN", "LAIN", "BADM",
        "PPN", "REFUND", "TOTAL", "PROMOSD", "created_by",
        "updated_by",
        "KODES", "NAMAS", "GOLONGAN", "POSTED", "CARA",
        "EMAIL", "KLB", "ANB"
    ];
}
