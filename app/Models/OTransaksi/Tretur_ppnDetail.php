<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tretur_ppnDetail extends Model
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
        "NAMA",
        "KET",
        "RETUR"
    ];
}
