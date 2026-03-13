<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Sup extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'supbsn';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable = 
    [
        "KODES",
        "NAMAS",
        "TYPE",
        "GSUP",
        "PEMILIK",
        "P_TELP",
        "EMAIL",
        "P_ALMT",
        "P_KOTA",
        "G_ALMT",
        "R_ALMT",
        "P_TLP",
        "P_FAX",
        "R_TLP",
        "P_POS",
        "TGL_M",
        "UPD_TGL",
        "TGL_PNG",
        "TGL_K",
        "KET_PRB",
        "B_BANK",
        "NPWP",
        "B_KOTA",
        "NM_NPWP",
        "B_NAMA",
        "NPPKP",
        "B_ACC",
        "AL_NPWP",
        "CARA",
        "KT_NPWP",
        "ADA_CNT",
        "BAY",
        "BAN",
        "C_SP",
        "USRNM",
        "TG_SMP"
    ];
}
