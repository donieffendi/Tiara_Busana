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

class RUjController extends Controller
{

   public function report()
    {
		session()->put('filter_CNT1', '');
		session()->put('filter_CNT2', '');

        return view('oreport_uj.report')->with(['hasil' => []]);
    }
	

	 	 
	public function jasperUjReport(Request $request) 
	{
		$file 	= 'umj';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
			
		$kode1 = $request->CNT1;
		$kode2 = $request->CNT2;
		
		session()->put('filter_CNT1', $request->CNT1);
		session()->put('filter_CNT2', $request->CNT2);

		$query = DB::SELECT("SELECT RIGHT(TRIM(brgbsn.NO_DTH),5) as NO_DTH, brgbsn.BARCODE, brgbsn.KD_BRG, 
									brgbsn.NA_BRG, brgbsn.CNT, brgbsn.NCNT, brgbsn.DTH,brgbsn.TTH, 
									brgbsn.HJUAL, brgbsn.TGL_JUAL, brgbsn.TGL_TRM,AA.AK 
							FROM brgbsn,(SELECT KD_BRG,SUM(AK00) AS AK 
											FROM brgbsnd WHERE AK00>0 GROUP BY KD_BRG) AS AA 
							WHERE brgbsn.CNT>='$kode1' and brgbsn.cnt<='$kode2' 
							AND brgbsn.KD_BRG=AA.KD_BRG AND ( brgbsn.DTH>0 or AA.AK>0 )  ;
		");
		
		if($request->has('filter'))
		{
			return view('oreport_uj.report')->with(['hasil' => $query]);
		}

		$data=[];

		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
