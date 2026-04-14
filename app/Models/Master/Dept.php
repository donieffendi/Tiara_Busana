<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


// ganti 1
class Dept extends Model
{
    use HasFactory;

// ganti 2
    protected $table = 'nwdept';
    protected $primaryKey = 'NO_ID';
    public $timestamps = false;

// ganti 3
    protected $fillable =
    [
        'KD_DEPT',
        'NA_DEPT',
        'USRNM',
        'TG_SMP'
    ];
}
