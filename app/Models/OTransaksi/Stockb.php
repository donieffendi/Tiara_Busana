<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Stockb extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'lapbsn';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "no_bukti",
        "tgl",
        "sub",
        "tg_smp",
        "usrnm",
        "cbg",
        "gol",
        "posted",
        "flag",
        "sls",
        "hps",
        "cnt",
        "ncnt",
        "kodes",
        "namas",
    ];
}
