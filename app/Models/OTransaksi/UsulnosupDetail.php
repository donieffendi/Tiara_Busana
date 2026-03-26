<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsulnosupDetail extends Model
{
    use HasFactory;

    protected $table = 'nwusul_ubah_nosupd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "ID",
        "REC",
        "NO_BUKTI",
        "PER",
        "NO_BARU",
        "NO_SUPL",
        "NAMA",
    ];
}
