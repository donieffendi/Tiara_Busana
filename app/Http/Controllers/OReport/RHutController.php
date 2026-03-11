<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Sup;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RHutController extends Controller
{

    public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		$kodes = Sup::orderBy('KODES')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', 'ZZZ');
		session()->put('filter_namas1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		
        return view('oreport_hut.report')->with(['kodes' => $kodes])->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	 
	 
	public function jasperHutReport(Request $request) 
	{
		$file 	= 'hutn';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
	
			// Check Filter
			
			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->gol))
			{
				$filtergol = " and hut.GOL='".$request->gol."' ";
			}
			
			// if (!empty($request->kodes))
			// {
			// 	$filterkodes = " and hut.KODES='".$request->kodes."' ";
			// }
		
			if (!empty($request->kodes) && !empty($request->kodes2))
			{
				$filterkodes = "  and hut.KODES between '".$request->kodes."' and '".$request->kodes2."'  ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and hut.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}
			
			if (!empty($request->cbg))
			{
				$filtercbg = " and hut.CBG='".$request->cbg."' ";
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
			session()->put('filter_cbg', $request->cbg);
			
		$query = DB::SELECT("SELECT hut.NO_BUKTI,
									-- hut.NO_PO,
									hut.TGL,hut.KODES,hut.NAMAS,hutd.NO_FAKTUR,
									hutd.TOTAL,hutd.BAYAR, hutd.SISA,hut.GOL, hut.TYPE, 
									hut.BACNO, hut.BNAMA
							from hut,hutd 
							WHERE hut.NO_BUKTI=hutd.NO_BUKTI 
							$filtertgl $filtergol $filterkodes $filtercbg
			             order by hut.KODES,hut.TGL, hut.NO_BUKTI;
		");
		
		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_hut.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'BACNO' => $query[$key]->BACNO,
				'BNAMA' => $query[$key]->BNAMA,
				'TYPE' => $query[$key]->TYPE,
				'NO_PO' => $query[$key]->NO_PO,
				'TGL' => $query[$key]->TGL,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
				'KODES_1' => $kodes_1,
				'KODES_2' => $kodes_2,
				'KODES' => $query[$key]->KODES,
				'NAMAS' => $query[$key]->NAMAS,
				'NO_FAKTUR' => $query[$key]->NO_FAKTUR,
				'TOTAL' => $query[$key]->TOTAL,
				'BAYAR' => $query[$key]->BAYAR,
				'SISA' => $query[$key]->SISA,
				'GOL' => $query[$key]->GOL,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
