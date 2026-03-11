<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Retur extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'retur';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI", "TGL", "PER", "FLAG", "NOTES",
        "TOTAL_QTY", "TOTAL",
		"USRNM", "TG_SMP", "CBG", "CNT", "NCNT"
    ];
}
