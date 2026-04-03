<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
use App\Models\Master\Perid;
use App\Models\Master\Cbg;

use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RSupController extends Controller
{

   public function report()
    {
		// $per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        // session()->put('filter_periode', '');

		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', '');
        // session()->put('filter_tglDari', date("d-m-Y"));
        // session()->put('filter_tglSampai', date("d-m-Y"));
		// session()->put('filter_budget', '');
		
        // return view('oreport_sup.report')->with(['per' => $per])->with(['hasil' => []]);
        return view('oreport_sup.report')->with(['hasil' => []]);
    }
	

	 
	public function jasperSupReport(Request $request) 
	{
		$file 	= 'Laporan_barang_yang_Diorder_per_Suplier';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;
		
		$periode = $request->per;

        $bulan = substr($periode,0,2);
        $tahun = substr($periode,3,4);
			
		$filterkodes = '';
		if (!empty($request->kodes1) && !empty($request->kodes2))
        {
            $filterkodes = " WHERE SUPP	 >= '".$request->kodes1."' AND SUPP	 <= '".$request->kodes2."' ";
        }

        // if (!empty($request->tglDr) && !empty($request->tglSmp))
        // {
        //     $tglDrD = date("Y-m-d", strtotime($request->tglDr));
        //     $tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
        //     $filtertgl = " AND TGL >= '".$tglDrD."' AND TGL <= '".$tglSmpD."' ";
        // }

        session()->put('filter_periode', $request->per);
        session()->put('filter_kodes1', $request->kodes1);
        session()->put('filter_kodes2', $request->kodes2);
        // session()->put('filter_tglDari', $request->tglDr);
        // session()->put('filter_tglSampai', $request->tglSmp);
        // session()->put('filter_budget', $request->budget);
		
		// $queryakum = DB::SELECT("SET @tglx:=last_day(concat('$tahun','-','$bulan','-01'));");
		$query = DB::select("
			SELECT 
				(@rownum := @rownum + 1) AS ROWNUM,
				SUB,
				KDBAR,
				NMBAR,
				SUPP,
				HB,
				QTY_BELI1,
				TG_BELI1,
				0 AS STOCKR,
				0 AS QTY,
				'_____________' AS KET

			FROM nwmasbar, (SELECT @rownum := 0) r
			$filterkodes
		");

		if($request->has('filter'))
		{
			// $per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			// return view('oreport_sup.report')->with(['per' => $per])->with(['hasil' => $query]);
			return view('oreport_sup.report')->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
