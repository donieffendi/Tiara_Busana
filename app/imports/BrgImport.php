<?php

namespace App\Imports;

use App\Http\Controllers\Master;
// ganti 1

use App\Models\Master\Brg;

use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

// ganti 2
class BrgImport implements ToModel
{
    public function model(array $row)
    {
        return new Brg([
            'KD_BRG' => $row[0], // Adjust column index based on your file
            'NA_BRG' => $row[1],
            'JENIS' => $row[2],
            'SATUAN' => $row[3],
            'MERK' => $row[4],
            'PN' => $row[5],
            'KALI' => $row[6],
            'SATUAN_BELI' => $row[7],
            'TYPE_KOM' => $row[8],
            'KOM' => $row[9],
        ]);
    }
}