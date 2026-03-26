<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsulhapusDetail extends Model
{
    use HasFactory;

    protected $table = 'nwusul_hapus_brgd';
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
        "KET"
    ];
}
