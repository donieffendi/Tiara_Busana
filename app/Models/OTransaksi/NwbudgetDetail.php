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
        "rec", "no_bukti", "ID", "KD_BRG", "NA_BRG","BARCODE","qty", "harga", 
        "total", "KET", "GOL", "flag", "per", "SISA", "KDLAKU"
    ];
}
