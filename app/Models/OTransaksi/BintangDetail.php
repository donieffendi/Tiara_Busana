<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BintangDetail extends Model
{
    use HasFactory;

    protected $table = 'nwbintangd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "ID",
        "REC",
        "PER",
        "NO_BUKTI",
        "KDBAR",
        "NMBAR",
        "NO_SUPL",
        "NAMA",
        "CEK",
        "KET",
    ];
}
