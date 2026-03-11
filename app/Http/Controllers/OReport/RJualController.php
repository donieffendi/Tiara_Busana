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
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		$kodec = Cust::orderBy('KODEC')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodec1', '');
		session()->put('filter_kodec2', '');
		session()->put('filter_namac1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_brg1', '');
		session()->put('filter_nabrg1', '');
		session()->put('filter_kdgd1', '');
	
        return view('oreport_jual.report')->with(['kodec' => $kodec])->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	  

	public function jasperJualReport(Request $request) 
	{
		$file 	= 'jualn';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter

			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}
			
			// if (!empty($request->kodec))
			// {
			// 	$filterkodec = " and so.KODEC='".$request->kodec."' ";
			// } 
		
			if (!empty($request->kodec) && !empty($request->kodec2))
			{
				$filterkodec = " and KODEC >= '".$request->kodec."' and KODEC <= '".$request->kodec2."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " AND TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}
			
			if (!empty($request->cbg))
			{
				$filtercbg = " and CBG='".$request->cbg."' ";
			}
			
			
			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));
			

			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodec1', $request->kodec);
			session()->put('filter_kodec2', $request->kodec2);
			session()->put('filter_namac1', $request->NAMAC);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_cbg', $request->cbg);
			
		$query = DB::SELECT(" SELECT NO_BUKTI, TGL, JTEMPO, KODEC, NAMAC, 
									TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
							FROM jual
							WHERE FLAG = 'JL' 
							$filtertgl  $filterkodec $filtercbg
							ORDER BY NO_BUKTI;

		");
      
		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_jual.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
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
				'KODEC_1' => $kodec_1,
				'KODEC_2' => $kodec_2,
				'KODEC' => $query[$key]->KODEC,
				'NAMAC' => $query[$key]->NAMAC,
				'TRUCK' => $query[$key]->TRUCK,
				'QTY' => $query[$key]->TOTAL_QTY,
				'DPP' => $query[$key]->DPP,
				'PPN' => $query[$key]->PPN,
				'TOTAL' => $query[$key]->TOTAL,
				'NETT' => $query[$key]->NETT,

			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}