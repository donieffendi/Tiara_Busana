<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Sup extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwmassup';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable =
    [
        "NO_SUPL",
        "NAMA",
        "ALMT_K",
        "P_TLP",
        "TLP_K",
        "NO_FAX",
        "NO_TELEX",
        "ALMT_GD",
        "PEMILIK",
        "ALMT_R",
        "TLP_R",
        "NO_REK",
        "NAMA_B",
        "KOTA_B",
        "AN_B",
        "GOL_BRG",
        "JEN_BRG1",
        "BUDGET_AWL",
        "STM_PEMBL",
        "KD_PEMBY",
        "KET_PEMBY",
        "CARA",
        "BY",
        "BG_PERS",
        "DISC_PS",
        "ORDER",
        "STATUSNYA",
        "GOLONGAN",
        "KOD_MIN",
        "DIS_A",
        "DIS_B",
        "DIS_C",
        "PPN",
        "BEBAN",
        "ACC",
        "USRNM",
        "TG_SMP"
    ];
}
