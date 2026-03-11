<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Cust;
use App\Models\Master\Perid;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RPantaubsnController extends Controller
{
 	public function report()
    {
		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA='MA'");
		session()->put('filter_cbg', '');

		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_pantau.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	

	public function jasperPantaubsnReport(Request $request) 
	{
		$file 	= 'pantaun';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Ganti format tanggal input agar sama dengan database
			$tglDrD = date("Y-m-d", strtotime($request['tglDr']));
            $tglSmpD = date("Y-m-d", strtotime($request['tglSmp']));
			
			// Check Filter
			$cbg = $request->cbg;

			$cabang = strtolower($cbg);

			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_cbg', $request->cbg);

		$query = DB::SELECT("CALL {$cabang}.bsn_turun_harga_terima('PANTAU_TH', '$cbg', '$tglDrD', '$tglSmpD')");

		if($request->has('filter'))
		{
			$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA='MA'");

			return view('oreport_pantau.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}
        
		$data=[];
		
		$data = json_decode(json_encode($query), true);
		
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
