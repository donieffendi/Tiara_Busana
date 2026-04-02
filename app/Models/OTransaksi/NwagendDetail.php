<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NwagendDetail extends Model
{
    use HasFactory;

    protected $table = 'nwagendd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "ID",
        "REC",
        "NO_BUKTI",
        "TGO",
        "TGL_MULAI",
        "BARCODE",
        "KD_BRG",
        "NA_BRG",
        "KDLAKU",
        "KEMASAN",
        "QTYK",
        "QTY",
        "SATUAN",
        "NACC",
        "KET",
        "HARGAK",
        "HARGA",
        "TOTAL",
        "FLAG",
        "PER",
        "GOL",
        "FLAG2",
        "TYPE",
        "HARGAS",
        "NOPOL",
        "BLT",
        "KODE",
        "RAK",
        "PINDAH",
        "PPN",
        "DISKON1",
        "DISKON2",
        "DISKON3",
        "DISKON4",
        "BPROM",
        "SISAPO",
        "BUKTI_BL",
        "BUKTI_PO",
        "QTY_BL",
        "HARGA_BL",
        "HARGA_JL",
        "MARGIN",
        "POT_PROM",
        "HPS",
        "KET_KEM",
        "BRG_IDG",
        "BRG_IDT",
        "TYP",
        "JNS"
    ];
}
