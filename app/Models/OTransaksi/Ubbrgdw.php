<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Ubbrgdw extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'ubbrgdw';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable =
    [
        "NO_BUKTI", "TGL", "PER",  "NOTES", "POSTED", "KODES", "NAMAS", "KOTA", "WILAYAH","ALAMAT",
        "FLAG", "GOL", "KET", "created_by"
    ];
}
