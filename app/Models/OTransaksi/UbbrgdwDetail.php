<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


//ganti 1
class UbbrgdwDetail extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'ubbrgdwd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

//ganti 3
    protected $fillable =
    [
        "REC","NO_BUKTI", "TGL", "PER",  "NOTES", "POSTED", "KODES", "NAMAS", "KOTA", "WILAYAH","ALAMAT",
        "FLAG", "GOL", "KET", "created_by", "KD_BRG", "NA_BRG", "QTY", "HARGA", "DISK", "DISK2","DISK3",
        "DISK4", "HARGALAMA", "DISKLAMA", "DISKLAMA2", "DISKLAMA3","DISKLAMA4", "KODES", "NAMAS"
    ];
}
