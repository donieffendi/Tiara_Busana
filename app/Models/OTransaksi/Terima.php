<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terima extends Model
{
    use HasFactory;

    protected $table = 'retur';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable = 
    [
        "NO_BUKTI","TGL", "CNT", "NCNT", "POSTED",
        "NO_PO", "FLAG", "GOL", "PER","KODES", "NAMAS",
        "REF", "MARGIN", "ST_NOTA", "ST_CNT", "POT_PROM",
        "KK_STS", "BASIC", "JTEMPO", "ST_PJK", "FORMAL",
        "NOTA_KHS", "NOTES", "BAYAR",
		"USRNM", "TG_SMP", "JUMLAH", "PROM", "DPP",
        "PPN", "NETT", "CBG"
    ];
}

