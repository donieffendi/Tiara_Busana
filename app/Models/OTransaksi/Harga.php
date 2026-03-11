<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Harga extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'harga';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [

        "NO_BUKTI", "TGL", "PER", "FLAG", "NOTES",
		"USRNM", "TG_SMP", "CBG", "CNT", "NCNT",
        "KODES", "NAMAS", "HARGAJL", "created_by",
        "updated_by", "POSTED"
    ];
}
