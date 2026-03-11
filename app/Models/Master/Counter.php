<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Counter extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'cntbsn';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable = 
    [
        'CNT',
        'NA_CNT',
        'SUP',
        'NAMAS',
        'LS_CNT', 
        'DIS_CUST',
        'JN_CNT',
        'DIS_TGLMP',
        'DIS_TGLSP',
        'ST_CNT',
        'DIS_KHS',
        'DIS_STS',
        'SC_CNT',
        'MARGIN',
        'BASIC',
        'ST_NOTA',
        'POT_PROM',
        'ST_ORD',
        'KEL_PT',
        'ST_PJK',
        'PER_NON',
        'CBAYAR',
        'AKTIF',
        'BUKAC',
        'TUTUPC',
        'LBAYAR',
        'BLOKIR',
        'KW_RET',
        'BLOKIRB',
        'KW_LBL',
        'PER_MIN',
        'B_MIN',
        'KALIB'
    ];
}
