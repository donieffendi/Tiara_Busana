<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Master\Cust;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RKartupController extends Controller
{
    public function kartu()
    {
		$cust = Cust::where('KODEC', '<>','ZZ')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodec1', '');
		session()->put('filter_namac1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		
        return view('oreport_piu.kartu')->with(['kodec' => $cust])->with(['hasil' => []]);
    }
	

	 
    public function sisa()
    {
		session()->put('filter_kodes', '');
		session()->put('filter_namas', '');
		session()->put('filter_alamat', '');
		session()->put('filter_kota', '');
		session()->put('filter_telp', '');
		
        return view('oreport_piu.sisa')->with(['hasil' => []]);
    }
	


	public function jasperPiuSisaReport(Request $request) 
	{
		$file 	= 'sisap';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));

		$kodes = $request->KODES;

		session()->put('filter_kodes', $request->KODES);
		session()->put('filter_namas', $request->NAMAS);
		session()->put('filter_alamat', $request->ALAMAT);
		session()->put('filter_kota', $request->KOTA);
		session()->put('filter_telp', $request->TELP);

		$query = DB::SELECT("SELECT KODES, NAMAS, P_ALMT ALAMAT, P_KOTA KOTA, P_TLP TELP FROM SUPBSN WHERE KODES='$kodes'");
		
		if($request->has('filter'))
		{
			return view('oreport_piu.sisa')->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
	public function jasperPiuKartu(Request $request) 
	{
		$file 	= 'kartup';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
	
			// Ganti format tanggal input agar sama dengan database
			$tglDrD = date("Y-m-d", strtotime($request['tglDr']));
            $tglSmpD = date("Y-m-d", strtotime($request['tglSmp']));
			
			// Convert tanggal agar ambil start of day/end of day
			$tglDr = Carbon::parse($request->tglDr)->startOfDay();
            $tglSmp = Carbon::parse($request->tglSmp)->endOfDay();
			
			//$periode = date("m/Y", strtotime($request['tglDr']));
			//$bulan = date("m", strtotime($request['tglDr']));
			//$tahun = date("Y", strtotime($request['tglDr']));
			
			$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
     		$bulan = substr($periode,0,2);
	    	$tahun = substr($periode,3,4);
			$cust = '';
			$tgawal = $tahun.'-'.$bulan.'-01';
			
			if (!empty($request->kodec))
			{
				$cust = " AND KODEC='".$request->kodec."' ";
			}
		
			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodec1', $request->kodec);
			session()->put('filter_namac1', $request->NAMAC);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);

		$queryakum = DB::SELECT("SET @akum:=0;");
		$query = DB::SELECT("
            	SELECT *, if(@kodec<>KODEC,@akum:=TOTAL-BAYAR,@akum:=@akum+TOTAL-BAYAR) as SALDO,@kodec:=KODEC as ganti from

		(

			SELECT NO_BUKTI, TGL, KODEC, NAMAC, PER$bulan AS TOTAL, 0 AS BAYAR , 1 as URUTAN
			from jualx where YER='$tahun' and PER$bulan<>0 $cust union all

			SELECT NO_BUKTI, TGL, KODEC, NAMAC, 0 AS TOTAL, BAYAR  , 2 as URUTAN
			from piu where PER='$periode' $cust 
			order by KODEC, TGL, NO_BUKTI, URUTAN ASC
				
		)  as kartup;
		");

		if($request->has('filter'))
		{
			return view('oreport_piu.kartu')->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'KODEC' => $query[$key]->KODEC,
				'NAMAC' => $query[$key]->NAMAC,
				'TOTAL' => $query[$key]->TOTAL,
				'BAYAR' => $query[$key]->BAYAR,
				'SALDO' => $query[$key]->SALDO,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}