<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TandareturDetail extends Model
{
    use HasFactory;

    protected $table = 'nwtandareturd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "ID",
        "REC",
        "NO_BUKTI",
        "PER",
        "KDBAR",
        "NMBAR",
        "KET_UK",
        "KET_KEM",
        "RETUR",
        "RETUR_B",
        "CBG",
        "USRNM",
        "TG_SMP"
    ];
}
