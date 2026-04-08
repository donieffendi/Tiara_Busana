<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NwbudgetDetail extends Model
{
    use HasFactory;

    protected $table = 'nwbudgetd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID", "KD_BRG", "NA_BRG","BARCODE","QTY", "HARGA",
        "TOTAL", "KET", "GOL", "FLAG", "PER", "SISA", "KDLAKU","BUDGET_BRG"
    ];
}
