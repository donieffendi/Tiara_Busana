<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpDetail extends Model
{
    use HasFactory;

    protected $table = 'ppd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BHN", "NA_BHN", "QTY", "SATUAN", "GOL", "PER", "FLAG"
    ];
}
