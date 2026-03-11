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

class RKomisiController extends Controller
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
		session()->put('filter_kodet1', '');
		session()->put('filter_namat1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		
        return view('oreport_komisi.report')->with(['kodec' => $kodec])->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	public function getKomisiReport(Request $request)
    {
        $query = DB::table('piu')
			->join('piud', 'piu.NO_BUKTI', '=', 'piud.NO_BUKTI')
			->select('piu.NO_BUKTI','piu.NO_SO','piu.TGL', 'piu.KODEC','piu.NAMAC','piud.NO_FAKTUR','piud.TOTAL','piud.BAYAR','piud.SISA','piu.GOL')->get();
			
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
			
			if (!empty($request->kodec))
			{
				$query = $query->where('KODEC', $request->kodec);
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$query = $query->whereBetween('TGL', [$tglDrD, $tglSmp]);
			}
			
			return Datatables::of($query)->addIndexColumn()->make(true);
		}
		
    }	  

	public function jasperKomisiReport(Request $request) 
	{
		$file 	= 'komisin';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Check Filter
			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->kodet))
			{
				$filterkodet = " WHERE KODET='".$request->kodet."' ";
			}
			
			if (!empty($request->gol))
			{
				$filtergol = " and piu.GOL='".$request->gol."' ";
			}
			
			// if (!empty($request->kodec))
			// {
			// 	$filterkodec = " and piu.KODEC='".$request->kodec."' ";
			// } 
		
			if (!empty($request->kodec) && !empty($request->kodec2))
			{
				$filterkodec = " WHERE piu.KODEC between '".$request->kodec."' and '".$request->kodec2."' ";
			}
			
			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " where TGL_BAYAR between '".$tglDrD."' and '".$tglSmpD."' ";
			}
			
			if (!empty($request->cbg))
			{
				$filtercbg = " and piu.CBG='".$request->cbg."' ";
			}
			
			
			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodec1', $request->kodec);
			session()->put('filter_kodec2', $request->kodec2);
			session()->put('filter_namac1', $request->NAMAC);
			session()->put('filter_kodet1', $request->kodet);
			session()->put('filter_namat1', $request->NAMAT);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_cbg', $request->cbg);

		$query = DB::SELECT("SELECT jual.NO_BUKTI, jual.TGL, jual.KODEP, jual.NAMAP, jual.KODEC, jual.NAMAC,
		                            jual.NO_BAYAR, jual.TGL_BAYAR, jual.TOTAL, jual.DIFFC, jual.TOTAL_TKOM, 
									jual.TKOM_GET, jual.PERC
							from jual
							$filtertgl AND jual.FLAG <> 'TP'
                            order by KODEP, KODEC;
		");

		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_komisi.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}
        
		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'KODEP' => $query[$key]->KODEP,
				'NAMAP' => $query[$key]->NAMAP,
				'KODEC' => $query[$key]->KODEC,
				'NAMAC' => $query[$key]->NAMAC,
				'TGL' => $query[$key]->TGL,
				'TGL_BAYAR' => $query[$key]->TGL_BAYAR,
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'DIFFC' => $query[$key]->DIFFC,
				'TOTAL_TKOM' => $query[$key]->TOTAL_TKOM,
				'TKOM_GET' => $query[$key]->TKOM_GET,
				'PERC' => $query[$key]->PERC,
				
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
