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

class RBeliController extends Controller
{

   public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', 'ZZZ');
		session()->put('filter_namas1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_brg1', '');
		session()->put('filter_nabrg1', '');


        return view('oreport_beli.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	 
	public function jasperBeliReport(Request $request) 
	{
		$file 	= 'belin';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter

			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->gol))
			{
				$filtergol = " and beli.GOL='".$request->gol."' ";
			}
		
			if (!empty($request->kodes) && !empty($request->kodes2))
			{
				$filterkodes = " and beli.KODES between '".$request->kodes."' and '".$request->kodes2."' ";
			}
			
			if (!empty($request->cbg))
			{
				$filtercbg = " and beli.CBG='".$request->cbg."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and beli.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}	

			if (!empty($request->brg1))
			{
				$filterbrg = " and belid.KD_BRG='".$request->brg1."' ";
			}
			
			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));
			$kodes_1 = $request->kodes;
			$kodes_2 = $request->kodes2;
			

			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodes1', $request->kodes);
			session()->put('filter_kodes2', $request->kodes2);
			session()->put('filter_namas1', $request->NAMAS);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_brg1', $request->brg1);
			session()->put('filter_nabrg1', $request->nabrg1);
			session()->put('filter_flag', $request->flag);
			session()->put('filter_cbg', $request->cbg);
		

		
			$query = DB::SELECT("SELECT trim(NO_BUKTI) as NO_BUKTI, TGL, JTEMPO, KODES, NAMAS,
										TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
								FROM beli
								WHERE FLAG = 'BL' $filtertgl $filterkodes $filtercbg
			");
			

		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_beli.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'JTEMPO' => $query[$key]->JTEMPO,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
				'KODES_1' => $kodes_1,
				'KODES_2' => $kodes_2,
				'KODES' => $query[$key]->KODES,
				'NAMAS' => $query[$key]->NAMAS,
				'QTY' => $query[$key]->TOTAL_QTY,
				'TOTAL' => $query[$key]->TOTAL,
				'DPP' => $query[$key]->TOTAL,
				'PPN' => $query[$key]->PPN,
				'NETT' => $query[$key]->NETT,
				'NOTES' => $query[$key]->NOTES,

			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}