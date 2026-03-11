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

class RUsulanthController extends Controller
{
    public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');
		session()->put('filter_nobukti1', '');
        return view('oreport_usulanth.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperUsulanthReport(Request $request) 
	{
		$file 	= 'pon';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			
			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->cbg))
			{
				$filtercbg = " and po.CBG='".$request->cbg."' ";
			}
			
			$nobukti_1 = $request->nobukti;

			session()->put('filter_nobukti1', $request->nobukti);
			session()->put('filter_cbg', $request->cbg);
		

		$query = $db->prepare("CALL bsn_turun_harga_terima(?, ?, ?, ?)");
		$query->execute([$jnsx, $cbgx, $buktix, $userx]);


		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_usulanth.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_PO' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
				'KODES_1' => $kodes_1,
				'KODES_2' => $kodes_2,
				'KODES' => $query[$key]->KODES,
				'NAMAS' => $query[$key]->NAMAS,
				'KD_BRG' => $query[$key]->KD_BRG,
				'NA_BRG' => $query[$key]->NA_BRG,
				'QTY' => $query[$key]->QTY,
				'HARGA' => $query[$key]->HARGA,
				'TOTAL' => $query[$key]->TOTAL,
				'KET' => $query[$key]->KET,
				'GOL' => $query[$key]->GOL,
				'KIRIM' => $query[$key]->KIRIM,
				'SISA' => $query[$key]->SISA,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
