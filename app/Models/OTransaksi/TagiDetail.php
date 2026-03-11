<?php

namespace App\Models\OTransaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagiDetail extends Model
{
    use HasFactory;

    protected $table = 'tagid';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

    protected $fillable =
    [

        "REC", "NO_BUKTI", "ID", "NO_TRM", "TGL_TRM", 
        "TOTAL", "PPN", "TOTALX", "NO_SP", "ACNO",
        "NACNO", "KET", "CNT", "ST_PJK", "FLAG", "PER"
    ];
}
