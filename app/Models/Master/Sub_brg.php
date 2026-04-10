<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Sub_brg extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwaotprice';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable =
    [
        'SUB',
        'KELOMPOK',
        'DEPT',
        'USRNM',
        'TG_SMP'
    ];
}
