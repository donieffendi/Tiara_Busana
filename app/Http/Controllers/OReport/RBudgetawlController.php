<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Cust;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RBudgetawlController extends Controller
{

   public function report()
    {
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		session()->put('filter_kodes2', '');
		session()->put('filter_namas2', '');
		session()->put('filter_budget', '');

        return view('oreport_budgetawl.report')->with(['hasil' => []]);
    }
	

	 	 
	public function jasperBudgetawlReport(Request $request) 
	{
		$file 	= 'Laporan_Budget_awal';
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

		$filterbudget = "";
		if (!empty($request->budget)) {
			$filterbudget .= " AND BUDGET_AWL = '".$request->budget."'";
		}
		
		session()->put('filter_kodes1', $request->kodes1);
		session()->put('filter_namas1', $request->namas1);
		session()->put('filter_kodes2', $request->kodes2);
		session()->put('filter_namas2', $request->namas2);
		session()->put('filter_budget', $request->budget);

		$query = DB::SELECT("SELECT 
					(@rownum := @rownum + 1) AS ROWNUM,
					NO_SUPL,
					NAMA,
					ALMT_K,
					KOTA,
					GOL_BRG,
					BUDGET_AWL,
					'' AS CETAK,
					CAT AS KET
				FROM nwmassup, (SELECT @rownum := 0) r
				$filterkodes $filterbudget
		");
		
		if($request->has('filter'))
		{
			return view('oreport_budgetawl.report')->with(['hasil' => $query]);
		}

		$data=[];

		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		$PHPJasperXML->arrayParameter = [
                "TGL_CTK" => date('d/m/Y')
        ];
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
