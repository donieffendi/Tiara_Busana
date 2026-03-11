<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Master\Sup;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RKartuhController extends Controller
{

    public function kartu()
    {
		$sup = Sup::where('KODES', '<>','ZZ')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		
        return view('oreport_hut.kartu')->with(['kodes' => $sup])->with(['hasil' => []]);
    }
	

	 
	public function sisa()
    {
		$sup = Sup::where('KODES', '<>','ZZ')->get();
		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		// session()->put('filter_tglDari', date("d-m-Y"));
		// session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_perio', session()->get('periode')['bulan']. '/' . session()->get('periode')['tahun']);
		session()->put('filter_flag', '');
		
        return view('oreport_hut.sisa')->with(['kodes' => $sup])->with(['hasil' => []]);
    }
	

	public function jasperHutSisaReport(Request $request) 
	{
		$file 	= 'sisah';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Ganti format tanggal input agar sama dengan database
			$tglDrD = date("Y-m-d", strtotime($request['tglDr']));
            $tglSmpD = date("Y-m-d", strtotime($request['tglSmp']));
			
			// Convert tanggal agar ambil start of day/end of day
			$tglDr = Carbon::parse($request->tglDr)->startOfDay();
            $tglSmp = Carbon::parse($request->tglSmp)->endOfDay();
			
			$periode = date("m/Y", strtotime($request['tglDr']));
			$bulan = date("m", strtotime($request['tglDr']));
			$tahun = date("Y", strtotime($request['tglDr']));
			$tgawal = $tahun.'-'.$bulan.'-01';
			
			$periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
			if(!empty($request->perio))
			{
				$periode = $request->perio;
			}
			$bulan = substr($periode,0,2);
			$tahun = substr($periode,3,4);
			
			$filterkodes='';
			if (!empty($request->kodes))
			{
				$filterkodes = " and KODES='".$request->kodes."' ";
			}
			
			$filterflag='';
			if (!empty($request->flag))
			{
				if ($request->flag=="BELI")
				{
					$filterflag = " and FLAG in ('BL','BD','BN') ";
				}
				else if ($request->flag=="UM")
				{
					$filterflag = " and FLAG='UM' ";
				}
				else if ($request->flag=="THUT")
				{
					$filterflag = " and FLAG='TH' ";
				}
			}
			
			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodes1', $request->kodes);
			session()->put('filter_namas1', $request->NAMAS);
			// session()->put('filter_tglDari', $request->tglDr);
			// session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_perio', $request->perio);
			session()->put('filter_lebih30', $request->lebih30);
			session()->put('filter_flag', $request->flag);

		$query = DB::SELECT("
        	SELECT NO_BUKTI, TGL, trim(KODES) as KODES, NAMAS, (PER$bulan-PERB$bulan) as SISA, FLAG
			from belix WHERE PER$bulan-PERB$bulan<>0 $filterkodes $filterflag
			order by KODES,NO_BUKTI;
		");
		
		if($request->has('filter'))
		{
			return view('oreport_hut.sisa')->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => date("d/m/Y", strtotime($query[$key]->TGL)),
				'KODES' => $query[$key]->KODES,
				'NAMAS' => $query[$key]->NAMAS,
				'TOTAL' => $query[$key]->SISA,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
	public function jasperHutKartu(Request $request) 
	{
		
		$file 	= 'kartuh';
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
			$tgawal = $tahun.'-'.$bulan.'-01';
			
			$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
     		$bulan = substr($periode,0,2);
	    	$tahun = substr($periode,3,4);
		
			$sup = '';
			$filterbelix = '';
			if (!empty($request->kodes))
			{
				$filterbelix = " AND belix.KODES='".$request->kodes."' ";
				$sup = " AND KODES='".$request->kodes."' ";
			}

			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodes1', $request->kodes);
			session()->put('filter_namas1', $request->NAMAS);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
		
		$queryakum = DB::SELECT("SET @akum:=0;");
		$query = DB::SELECT("
        	SELECT *, if(@kodes<>KODES,@akum:=TOTAL-BAYAR,@akum:=@akum+TOTAL-BAYAR) as SALDO,@kodes:=KODES as ganti from
		(

			SELECT NO_BUKTI, TGL, KODES, NAMAS, PER$bulan AS TOTAL, 0 AS BAYAR, 1 as URUTAN 
			from belix where belix.YER='$tahun' and PER$bulan<>0 $filterbelix union all

			SELECT NO_BUKTI, TGL, KODES, NAMAS, 0 AS TOTAL, BAYAR, 2 as URUTAN 
			from hut where PER='$periode' $sup
			
			order by KODES, TGL, NO_BUKTI, URUTAN ASC
		) as  kartuh;
		");

		if($request->has('filter'))
		{
			return view('oreport_hut.kartu')->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'KODES' => $query[$key]->KODES,
				'NAMAS' => $query[$key]->NAMAS,
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
