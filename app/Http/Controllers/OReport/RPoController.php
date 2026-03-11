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

class RPoController extends Controller
{
    public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', 'ZZZ');
		session()->put('filter_namas1', '');
		session()->put('filter_brg1', '');
		session()->put('filter_nabrg1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_po.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperPoReport(Request $request) 
	{
		$file 	= 'pon';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter

			if (!empty($request->gol))
			{
				$filtergol = " and po.GOL='".$request->gol."' ";
			}
			
			// if (!empty($request->kodes))
			// {
			// 	$filterkodes = " and po.KODES='".$request->kodes."' ";
			// 
		
			if (!empty($request->kodes) && !empty($request->kodes2))
			{
				$filterkodes = " and po.KODES between '".$request->kodes."' and '".$request->kodes2."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and po.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}	
			
			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->cbg))
			{
				$filtercbg = " and po.CBG='".$request->cbg."' ";
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
			session()->put('filter_cbg', $request->cbg);
		
		// if( $filtergol == 'J' ){
			
		// 	$query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.KODES, po.NAMAS, 
		// 		pod.KD_BRG, pod.NA_BRG, pod.QTY, pod.HARGA, pod.TOTAL, 
		// 		po.NOTES, pod.SATUAN,
		// 		po.GOL, 
		// 		pod.KIRIM, po.SISA from po, pod
		// 		$filtertgl $filtergol $filterkodes $filtercbg
		// 		ORDER BY NO_BUKTI;
		// 	");	
		// } else {
		// 	$query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.KODES, po.NAMAS, 
		// 		pod.KD_BHN AS KD_BRG, pod.NA_BHN AS NA_BRG, pod.QTY, pod.HARGA, pod.TOTAL, 
		// 		po.NOTES, pod.SATUAN,
		// 		po.GOL, 
		// 		pod.KIRIM, po.SISA from po, pod
		// 		$filtertgl $filtergol $filterkodes $filtercbg
		// 		ORDER BY NO_BUKTI;
		// 	");	
		// }

		$query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.KODES, po.NAMAS, 
							pod.KD_BRG, pod.NA_BRG, pod.QTY, pod.HARGA, pod.TOTAL, 
							po.NOTES, pod.SATUAN,
							po.GOL, 
							pod.KIRIM, pod.SISA from po, pod where po.no_bukti = pod.no_bukti
							$filtertgl $filtergol $filterkodes $filtercbg
							ORDER BY NO_BUKTI;
		");	

		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_po.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
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
