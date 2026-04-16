<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Cekbcd;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

class CekbcdController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_cekbcd.index');
    }

    public function getCekbcd( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $cekbcd = DB::SELECT("SELECT distinct a.KD_BRG, a.CNT, a.NCNT, a.BARCODE, a.NA_BRG
                                FROM brgbsn a
                                INNER JOIN brgbsn b ON a.BARCODE = b.BARCODE
                                WHERE a.KD_BRG <> b.KD_BRG AND a.BARCODE<>'' ORDER BY a.BARCODE ASC");

        return Datatables::of($cekbcd)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan")
                {
                    // url untuk delete di index
                    $url = "'".url("cekbcd/delete/" . $row->NO_ID )."'";
                    // batas

                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a hidden class="dropdown-item" href="cekbcd/edit/?idx=' . $row->NO_ID . '&tipx=edit";>                                
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <hr>
                                </hr>

                                <a hidden class="dropdown-item btn btn-danger" ' . $btnDelete . '>

                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    Delete
                                </a>
                        ';
                } else {
                    $btnPrivilege = '';
                }

                $actionBtn =
                    '
                    <div class="dropdown show" style="text-align: center">
                        <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">

                            ' . $btnPrivilege . '
                        </div>
                    </div>
                    ';

                return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function Print(Request $request)
    {
        $file = 'Cek_Barcode';
        $PHPJasperXML = new \PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $params = [
            "TGL_CTK" => date('d/m/Y')
        ];
        $PHPJasperXML->arrayParameter = $params;

        $query = DB::SELECT("SELECT distinct a.KD_BRG, a.CNT, a.NCNT, a.BARCODE, a.NA_BRG
                                FROM brgbsn a
                                INNER JOIN brgbsn b ON a.BARCODE = b.BARCODE
                                WHERE a.KD_BRG <> b.KD_BRG AND a.BARCODE<>'' ORDER BY a.BARCODE ASC");

        $data = [];
        
        $data = json_decode(json_encode($result), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
}
