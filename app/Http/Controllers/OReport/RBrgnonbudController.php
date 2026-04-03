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

class RBrgnonbudController extends Controller
{
    public function report()
    {	
		$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');
		session()->put('filter_sub1', '');
		session()->put('filter_sub2', '');
		session()->put('filter_kdbar1', '');
		session()->put('filter_kdbar2', '');

        return view('oreport_brgnonbud.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperBrgnonbudReport(Request $request) 
	{
		$file 	= 'Laporan_Data_Barang_Non_Budget';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;
		
			
		$filtersub = "";
		if (!empty($request->sub1) && !empty($request->sub2)) {
			$filtersub .= " WHERE SUB BETWEEN '".$request->sub1."' AND '".$request->sub2."'";
		}

		$filterkode = "";
		if (!empty($request->kdbar1) && !empty($request->kdbar2)) {
			$filterkode .= " AND KDBAR BETWEEN '".$request->kdbar1."' AND '".$request->kdbar2."'";
		}

		session()->put('filter_sub1', $request->sub1);
		session()->put('filter_sub2', $request->sub2);
		session()->put('filter_kdbar1', $request->kdbar1);
		session()->put('filter_kdbar2', $request->kdbar2);
		session()->put('filter_periode', $request->per);
		

		$query = DB::select("
			SELECT 
				(@rownum := @rownum + 1) AS ROWNUM,
				SUB,
				KDBAR,
				NMBAR,
				KET_UK,
				HB,
				DIS_A,
				DIS_B,
				DIS_C,
				SUPP,
				CAT
			FROM nwmasbar, (SELECT @rownum := 0) r
			$filtersub 
			$filterkode
		");

		if($request->has('filter'))
		{	
			$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			return view('oreport_brgnonbud.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		$data = json_decode(json_encode($query), true);
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
