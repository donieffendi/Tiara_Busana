<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoDetail extends Model
{
    use HasFactory;

    protected $table = 'sod';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "REC", "NO_BUKTI", "ID",  "KD_BRG", "FLAG", "NA_BRG", "SATUAN", "QTY", "SISA", "SISA2", "HARGA", 
        "TOTAL", "KET", "TOTAL_QTY", "KD_BHN", "NA_BHN", "PER", "GOL", "AHARGA", "BHARGA", "CHARGA",
        "DHARGA", "EHARGA", "DPP", "PPNX", "FHARGA", "GHARGA", "DISK", "KD_GRUP", "TYPE_KOM", 
        "KOM", "TKOM", "LOKASI", "BERAT", "XSO", "SEDIA", "CBG", "QTY2", "SATUAN2", "KALI", "TBERAT",
        "DISK1", "DISK2", "DISK3", "DISK4", "DISK5"
    ];
}
