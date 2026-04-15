<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brg;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

class BcdrakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_bcdrak.index');
    }

    public function getBcdrak( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $dept = session()->get('periode')['dept'];
        $cabang = session()->get('periode')['cabang'];

        $sub1 = $request->input('sub1');
        $supp1 = $request->input('supp1');

        // kalau belum ada filter → kosongkan
        if (empty($sub1) && empty($supp1)) {
            return Datatables::of(collect([]))->make(true);
        }

        $query = DB::table('brgbsn')
            ->select('NO_ID', 'KD_BRG', 'BARCODE', 'NA_BRG', 'CNT', 'NCNT', 'HJUAL');

        if (!empty($sub1)) {
            $query->where('KD_BRG', '=', "$sub1");
        }

        if (!empty($supp1)) {
            $query->where('CNT', '=', "$supp1");
        }

        $bcdrak = $query->orderBy('KD_BRG')->get();

        return Datatables::of($bcdrak)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan")
                {
                    // url untuk delete di index
                    $url = "'".url("bcdrak/delete/" . $row->NO_ID )."'";
                    // batas

                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a hidden class="dropdown-item" href="bcdrak/edit/?idx=' . $row->NO_ID . '&tipx=edit";>                                
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

    public function Barcode(Request $request)
    {
        $sub1  = $request->input('sub1');
        $supp1 = $request->input('supp1');
        $qty   = (int) $request->input('qty', 1);

        $file = 'bcdrak';
        $PHPJasperXML = new \PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $params = [
            "TGL_CTK" => date('d/m/Y')
        ];
        $PHPJasperXML->arrayParameter = $params;

        // QUERY
        $query = DB::table('brgbsn')
            ->select('KD_BRG','NA_BRG','BARCODE','CNT','NCNT','HJUAL');

        if (!empty($sub1)) {
            $query->where('KD_BRG', $sub1);
        }

        if (!empty($supp1)) {
            $query->where('CNT', $supp1);
        }

        $result = $query->orderBy('KD_BRG')->get();

        $resultArray = json_decode(json_encode($result), true);

        // =========================
        // 🔥 STEP 1: DUPLIKASI SESUAI QTY
        // =========================
        $temp = [];

        foreach ($resultArray as $row) {
            for ($i = 0; $i < $qty; $i++) {
                $temp[] = $row;
            }
        }

        // =========================
        // 🔥 STEP 2: GROUP 3 KOLOM
        // =========================
        $data = [];
        $chunks = array_chunk($temp, 3);

        foreach ($chunks as $items) {
            $row = [];

            for ($i = 0; $i < 3; $i++) {
                $no = $i + 1;

                $row["KD_BRG$no"]  = $items[$i]['KD_BRG'] ?? '';
                $row["NA_BRG$no"]  = $items[$i]['NA_BRG'] ?? '';
                $row["BARCODE$no"] = $items[$i]['BARCODE'] ?? '';
                $row["CNT$no"]     = $items[$i]['CNT'] ?? '';
                $row["NCNT$no"]    = $items[$i]['NCNT'] ?? '';
                $row["HJUAL$no"]   = $items[$i]['HJUAL'] ?? '';
            }

            $data[] = $row;
        }

        // =========================
        // KIRIM KE JASPER
        // =========================
        $PHPJasperXML->setData($data);

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

    public function cetak(Request $request, Brg $bcdrak){
        $file = 'vbrg';
        $data = [];
        $qty = max((int) $request->query('qty', 1), 1);
		$jumlahCetak = ceil($qty / 2); // dibagi 2

		for ($i = 0; $i < $jumlahCetak; $i++) {
            $data[] = [
                "KD_BRG"  => $bcdrak->KD_BRG . " ",
                "NA_BRG"  => $bcdrak->NA_BRG . " ",
                "KET_UK"  => $bcdrak->KET_UK . " ",
                "BARCODE" => $bcdrak->BARCODE . " ",
                "SUB"     => $bcdrak->SUB . " ",
                "SUPP"   => $bcdrak->SUPP . " ",
            ];
        }
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $cleanData = json_decode(json_encode($data), true);
        $PHPJasperXML->setData($data);
        $PHPJasperXML->arrayPageSetting["orientation"] = "L";
        $PHPJasperXML->arrayPageSetting["pageHeight"]  = 1 * 3.7795 * 18; // 1 mm = 3.7795 pixel, 1 ( jumlah row ) x 18 mm ( tinggi row )

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

    public function cetakBarcode(Request $request)
    {
        $no_bukti = $request->buktix;
        $file     = 'vbrg';
        $data     = DB::SELECT("SELECT belid.KD_BRG, belid.NA_BRG, belid.QTY, brg.KET_UK, brg.BARCODE, beli.TOTAL_QTY
                        FROM beli, belid, brg
                        WHERE beli.NO_BUKTI = belid.NO_BUKTI
                            AND belid.KD_BRG = brg.KD_BRG
                            AND belid.NO_BUKTI = '$no_bukti'");

        // dd($data);
        $finalData = [];
        foreach ($data as $row) {

			// bagi 2 dan bulatkan ke atas
			$qty = (int) $row->QTY;
			$jumlahCetak = ceil($qty / 2);

            for ($i = 0; $i < $jumlahCetak; $i++) {
                $finalData[] = [
                    'KD_BRG'  => $row->KD_BRG,
                    'NA_BRG'  => $row->NA_BRG,
                    'KET_UK'  => $row->KET_UK,
                    'BARCODE' => $row->BARCODE,
                ];
            }
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $cleanData = json_decode(json_encode($data), true);
        $PHPJasperXML->setData($finalData);
        $PHPJasperXML->arrayPageSetting["orientation"] = "L";
        $PHPJasperXML->arrayPageSetting["pageHeight"]  = 1 * 3.7795 * 18; // 1 mm = 3.7795 pixel, 1 ( jumlah row ) x 18 mm ( tinggi row )

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
}
