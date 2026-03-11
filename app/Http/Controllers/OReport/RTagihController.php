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

class RTagihController extends Controller
{

  	public function report()
    {
		$cbg = DB::SELECT("SELECT KODE from toko WHERE STA='MA'");
		session()->put('filter_cbg', '');

		session()->put('filter_CNT', '');
		session()->put('filter_NA_CNT', '');
		session()->put('filter_stok', '');
			
        return view('oreport_tagih.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	  

	public function jasperTagihReport(Request $request) 
	{
		$file 	= 'tagihn';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter
			if ($request->session()->has('periode')) {
            	$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
			} else {
				$periode = '';
			}

			$bulan = substr($periode, 0, 2);
			$tahun = substr($periode, 3, 4);

			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			$cabang = strtolower($cbg);

			$kode = $request->CNT;

			$stok = $request->stok;

			$filterstok = '';
			if($stok == 1){
				$filterstok = " HAVING STOK > 0 ";
			}
			
			session()->put('filter_CNT', $request->CNT);
			session()->put('filter_NA_CNT', $request->NA_CNT);
			session()->put('filter_stok', $request->stok);
			session()->put('filter_cbg', $request->cbg);
			

		$query = DB::SELECT("SELECT '$kode' CNT, b.CBG, a.NCNT, a.KD_BRG, a.NA_BRG, a.BARCODE,
									a.TGL_TRM,
									b.TGL_KSR as TGL_JUAL, TIMESTAMPDIFF(MONTH,b.TGL_KSR,CURDATE()) BLN,
									ROUND((a.JUL/(a.AWL+a.BEL-a.RET+a.REF-a.MIN+a.PLU) ) * 100,2) LAKU,
									AK$bulan as STOK
									FROM {$cabang}.brgbsn a, {$cabang}.brgbsnd b
									WHERE a.KD_BRG=b.KD_BRG AND b.CBG='$cbg'
									AND  if(date(a.TGL_TRM<>'2001-01-01'), TIMESTAMPDIFF(MONTH,a.TGL_TRM,CURDATE())>=6,
											if(date(b.TGL_KSR)<>'2001-01-01', TIMESTAMPDIFF(MONTH,b.TGL_KSR,CURDATE())>=6, false))
									AND a.CNT='$kode' $filterstok");

		if($request->has('filter'))
		{
			$cbg = DB::SELECT("SELECT KODE from toko WHERE STA='MA'");

			return view('oreport_tagih.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}