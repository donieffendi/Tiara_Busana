<?php

namespace App\Imports;

use App\Http\Controllers\Master;
// ganti 1

use App\Models\Master\Cust;

use Maatwebsite\Excel\Concerns\ToModel;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

// ganti 2
class CustImport implements ToModel
{
    public function model(array $row)
    {
        return new Cust([
            'KODEC' => $row[0], // Adjust column index based on your file
            'NAMAC' => $row[1],
            'ALAMAT' => $row[2],
            'KOTA' => $row[3],
            'NPWP' => $row[4],
            'NAMA_PJK' => $row[5],
        ]);
    }
}