<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
// ganti 1
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Cust;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class RSuratsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

  	public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		// $kodec = Cust::orderBy('KODEC')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodec1', '');
		session()->put('filter_kodec2', 'ZZZ');
		session()->put('filter_namac1', '');
		session()->put('filter_kodet1', '');
		session()->put('filter_namat1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_pilih', '');
	
        return view('oreport_surats.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
		
    }
	
		public function getSuratsReport(Request $request)
    {
        $query = DB::table('surats')
			->select('NO_BUKTI','TGL','NO_SO','KODEC','NAMAC','TOTAL','NOTES','GOL')
			->get();
			
		if ($request->ajax())
		{
			// Ganti format tanggal input agar sama dengan database
			$tglDrD = date("Y-m-d", strtotime($request['tglDr']));
            $tglSmpD = date("Y-m-d", strtotime($request['tglSmp']));
			
			// Convert tanggal agar ambil start of day/end of day
			//$tglDr = Carbon::parse($request->tglDr)->startOfDay();
            $tglSmp = Carbon::parse($request->tglSmp)->endOfDay();
			
			// Check Filter
			if (!empty($request->gol))
			{
				$query = $query->where('GOL', $request->gol);
			}
			
			// if (!empty($request->KODEC))
			// {
			// 	$query = $query->where('KODEC', $request->kodec);
			// }
		
			if (!empty($request->kodec) && !empty($request->kodec2))
			{
				$filterkodec = " WHERE KODEC between '".$request->kodec."' and '".$request->kodec2."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$query = $query->whereBetween('TGL', [$tglDrD, $tglSmp]);
			}
			
			return Datatables::of($query)->addIndexColumn()->make(true);
		}
		
    }	  


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

	public function jasperSuratsReport(Request $request) 
	{
		$file 	= 'suratsn';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter
			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->gol))
			{
				$filtergol = " and a.GOL='".$request->gol."' ";
			}
			
			// if (!empty($request->kodec))
			// {
			// 	$filterkodec = " and a.KODEC='".$request->kodec."' ";
			// }
		
			if (!empty($request->kodec) && !empty($request->kodec2))
			{
				$filterkodec = " and a.KODEC between '".$request->kodec."' and '".$request->kodec2."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and a.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}
			
			if (!empty($request->cbg))
			{
				$filtercbg = " and a.CBG='".$request->cbg."' ";
			}
			
			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));
			$kodec_1 = $request->kodec;
			$kodec_2 = $request->kodec2;
			

			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodec1', $request->kodec);
			session()->put('filter_kodec2', $request->kodec2);
			session()->put('filter_namac1', $request->NAMAC);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_cbg', $request->cbg);
			session()->put('filter_pilih', $request->PILIH);
		
			if (  $request->PILIH == '1' )
			{
				$query = DB::SELECT("SELECT a.NO_BUKTI, a.TGL, b.NO_SO, a.KODEC, a.NAMAC, a.TOTAL, a.NOTES, a.GOL, a.TRUCK,
							b.NA_BRG, b.QTY 
							from surats a, suratsd b 
							WHERE a.NO_BUKTI = b.NO_BUKTI and a.FLAG='JL' $filtertgl $filtergol $filterkodec $filtercbg 
							ORDER BY a.NO_BUKTI;
						");

			} else if (  $request->PILIH == '2' )
			{
				$query = DB::SELECT("SELECT a.NO_BUKTI, a.TGL, b.NO_SO, a.KODEC, a.NAMAC, a.TOTAL, a.NOTES, a.GOL, a.TRUCK,
							b.NA_BRG, b.QTY 
							from surats a, suratsd b 
							WHERE a.NO_BUKTI = b.NO_BUKTI and a.FLAG='JL' and a.KIRIM = '1' AND POSTED ='0' $filtertgl $filtergol $filterkodec $filtercbg 
							ORDER BY a.NO_BUKTI;
						");
			}  
	



		

		
		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_surats.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}
        
		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
				'KODEC_1' => $kodec_1,
				'KODEC_2' => $kodec_2,
				'NO_SO' => $query[$key]->NO_SO,
				'KODEC' => $query[$key]->KODEC,
				'NAMAC' => $query[$key]->NAMAC,
				'TOTAL' => $query[$key]->TOTAL,
				'NOTES' => $query[$key]->NOTES,
				'GOL' => $query[$key]->GOL,
				'NA_BRG' => $query[$key]->NA_BRG,
				'TRUCK' => $query[$key]->TRUCK,
				'QTY' => $query[$key]->QTY,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
	

	
}
