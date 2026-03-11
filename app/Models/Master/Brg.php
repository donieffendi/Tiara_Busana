<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Brg extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'brgbsn';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable = 
    [
        'CNT',
        'NA_CNT',
        'KD_BRG',
        'NA_BRG',
        'BARCODE',
        'JNS',
        'SUP',
        'HJUAL',
        'DIS_KHS',
        'DIS_STS',
        'BELI',
        'HBNET',
        'DIS1',
        'DIS2',
        'DIS3',
        'DIS4',
        'ST_HRG',
        'DIS_PRO',
        'LS_CNT',
        'DTH',
        'TTH',
        'JN_CNT',
        'DIST_CUST',
        'DIS_CUSN',
        'ST_CNT',
        'DIS_TGLM',
        'DIS_TGLS',
        'SC_CNT',
        'ST_NOTA',
        'MARGIN',
        'ST_ORD',
        'ST_PJK',
        'CBAYAR',
        'KEL_PT',
        'LBAYAR',
        'KEL_BRG',
        'KW_RET',
        'BASIC',
        'KW_LBL',
        'KATEGORI'
    ];
}
