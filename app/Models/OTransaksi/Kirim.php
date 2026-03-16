<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Kirim extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'bstocka';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI",
        "BKTK",
        "NO_TGZ",
        "REF",
        "NO_PO",
        "KODES",
        "NAMAS",
        "NO_TAGI",
        "NO_HUT",
        "NO_PJK",
        "TGL",
        "TGLA",
        "JTEMPO",
        "HARI",
        "PER",
        "GOLONGAN",
        "alamat",
        "kota",
        "wilayah",
        "via",
        "notes",
        "POSTED",
        "tgl_posted",
        "usrnm",
        "total_qty",
        "flag",
        "total",
        "BPROM",
        "PROM",
        "ppn",
        "nett",
        "ppnym",
        "na_file",
        "TERM",
        "disc",
        "tg_smp",
        "GOL",
        "CBG",
        "LAIN",
        "SISA",
        "BAYAR",
        "EXP",
        "TYPE",
        "KURIR",
        "DIVISI",
        "NO_LAMA",
        "CNT",
        "NCNT",
        "MARGIN",
        "POT_PROM",
        "ST_PJK",
        "ST_NOTA",
        "KK_STS",
        "FORMAL",
        "St_CNT",
        "BASIC",
        "NOTA_KHS",
        "DPP",
        "HPS",
        "TYP",
        "OUTLET"
    ];
}
