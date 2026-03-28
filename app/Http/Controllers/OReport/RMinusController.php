<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
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

class RMinusController extends Controller
{
    public function report()
    {	
		$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		session()->put('filter_kodes2', '');
		session()->put('filter_namas2', '');

        return view('oreport_minus.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperMinusReport(Request $request) 
	{
		$file 	= 'Laporan_Budget_Minus';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;
		
			
		$filterkodes = "";

		if (!empty($request->kodes1) && !empty($request->kodes2)) {
			$filterkodes .= " WHERE NO_SUPL BETWEEN '".$request->kodes1."' AND '".$request->kodes2."'";
		}

		session()->put('filter_kodes1', $request->kodes1);
		session()->put('filter_namas1', $request->namas1);
		session()->put('filter_kodes2', $request->kodes2);
		session()->put('filter_namas2', $request->namas2);
		session()->put('filter_periode', $request->per);
		

		$query = DB::select("
			SELECT 
				(@rownum := @rownum + 1) AS ROWNUM,
				NO_SUPL,
				NAMA,
				BUDGET,
				BUDGET_LL AS QTY,
				'X' AS TEGURAN1,
				'X' AS TEGURAN2,
				'X' AS TEGURAN3
			FROM nwmassup, (SELECT @rownum := 0) r
			$filterkodes AND (BUDGET - BUDGET_LL) < 0
		");

		if($request->has('filter'))
		{	
			$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
			return view('oreport_minus.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		$data = json_decode(json_encode($query), true);
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
