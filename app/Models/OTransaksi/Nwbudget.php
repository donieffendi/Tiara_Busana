<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Nwbudget extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwbudget';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI", "TGL", "JTEMPO", "PER","KODES", "NAMAS", "ALAMAT", "KOTA", "FLAG", "GOL", 
        "R_SALDO", "Q_SALDO", "NOTES", "USRNM", "TG_SMP",
        "CBG", "POSTED", "CNT", "NA_CNT"

        
    ];
}
