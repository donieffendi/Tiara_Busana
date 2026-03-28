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

class RSupnolController extends Controller
{
    public function report()
    {
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		session()->put('filter_kodes2', '');
		session()->put('filter_namas2', '');

        return view('oreport_supnol.report')->with(['hasil' => []]);
    }
	
	
	 
	public function jasperSupnolReport(Request $request) 
	{
		$file 	= 'Laporan_Suplier_Tidak_Ada_Budget';
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
		

		$query = DB::select("
			SELECT 
				(@rownum := @rownum + 1) AS ROWNUM,
				NO_SUPL,
				NAMA,
				ALMT_K,
				KOTA,
				GOL_BRG,
				BUDGET,
				0 AS QTY,
				'' AS CETAK
			FROM nwmassup, (SELECT @rownum := 0) r
			$filterkodes
			AND BUDGET = 0
		");


		if($request->has('filter'))
		{

			return view('oreport_supnol.report')->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
