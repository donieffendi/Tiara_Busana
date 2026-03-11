<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Pp extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'pp';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI","TGL", "NO_ORDER", "FLAG", "GOL", "PER","KODES", "NAMAS", "TOTAL_QTY", "NOTES",
		"USRNM", "TG_SMP", "ALAMAT", "KOTA", "GOL", "PKP", "CBG"
    ];
}
