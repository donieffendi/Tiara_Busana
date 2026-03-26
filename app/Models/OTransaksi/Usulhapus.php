<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class Usulhapus extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwusul_hapus_brg';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable = 
    [
        "NO_BUKTI",
        "PER",
        "TGL",
        "NOTES",
        "USRNM",
        "TG_SMP"
    ];
}
