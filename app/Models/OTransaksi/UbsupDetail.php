<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbsupDetail extends Model
{
    use HasFactory;

    protected $table = 'breturd';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [
        "ID",
        "rec",
        "no_bukti",
        "TGO",
        "tgl_mulai",
        "BARCODE",
        "KD_BRG",
        "NA_BRG",
        "kdlaku",
        "kemasan",
        "qtyk",
        "qty",
        "satuan",
        "nacc",
        "ket",
        "hargak",
        "harga",
        "total",
        "flag",
        "per",
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
