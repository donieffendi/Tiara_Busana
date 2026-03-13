<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoDetail extends Model
{
    use HasFactory;

    protected $table = 'pobsnd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "rec", "no_bukti", "ID", "KD_BRG", "NA_BRG","BARCODE","qty", "harga", 
        "total", "KET", "GOL", "flag", "per", "SISA", "KDLAKU"
    ];
}
