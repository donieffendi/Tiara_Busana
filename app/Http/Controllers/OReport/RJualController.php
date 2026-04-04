<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Cust;
use App\Models\Master\Cbg;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RJualController extends Controller
{

  	public function report()
    {
		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
		session()->put('filter_cbg', '');

		$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');

		session()->put('filter_nabrg1', '');
		session()->put('filter_kdgd1', '');
	
        return view('oreport_jual.report')->with(['cbg' => $cbg])->with(['per' => $per])->with(['hasil' => []]);
    }
	  

	public function jasperJualReport(Request $request) 
	{
		$file 	= 'Laporan_SP_Per_PLU';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter

			$cbg = $request->cbg;
			$per = $request->per;

			$bulan = substr($per,0,2);
			$tahun = substr($per,3,4);

			$filterplu = '';
			if(!empty($request->kode1))
			{
				$filterplu = " AND b.KDBAR = '$request->kode1'";
			}
			

			session()->put('filter_cbg', $request->cbg);
			session()->put('filter_periode', $request->per);
			session()->put('filter_kode1', $request->kode1);
			session()->put('filter_nama1', $request->nama1);
			
		$query = DB::SELECT("
			SELECT a.NO_BUKTI, a.TGL, a.JTEMPO, a.KODEC, a.NAMAC, 
				a.TOTAL_QTY, a.TOTAL, a.TDPP AS DPP, a.TPPN AS PPN, 
				a.NETT, b.KDBAR, b.NMBAR 
			FROM jual a, juald b
			WHERE a.NO_BUKTI = b.NO_BUKTI
			AND a.PER = '$per' 
			AND a.CBG = '$cbg'
			$filterplu
			ORDER BY b.KDBAR
		");
      
		if($request->has('filter'))
		{
			$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
			$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			return view('oreport_jual.report')->with(['cbg' => $cbg])->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}