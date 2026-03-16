<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockbDetail extends Model
{
    use HasFactory;

    protected $table = 'lapbsnd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "id",
        "rec",
        "gol",
        "no_bukti",
        "kd_brg",
        "itemsub",
        "na_brg",
        "ket_uk",
        "ket_kem",
        "kd",
        "hj",
        "lph",
        "saldo",
        "tgl_trm",
        "qty_trm",
        "tgl_lbk",
        "tgl_at",
        "flag",
        "cbg",
    ];
}
