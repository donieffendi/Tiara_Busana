<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Master\Brg;
// ganti 1

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

// ganti 2
class RKarstkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function kartu()
    {
		session()->put('filter_brg1', '');
		session()->put('filter_nabrg1', '');
		session()->put('filter_brg2', '');
		session()->put('filter_nabrg2', '');
		session()->put('filter_tglDr', now()->format('d-m-Y'));
		session()->put('filter_tglSmp', now()->format('d-m-Y'));
		$brg = Brg::orderBy('KD_BRG', 'ASC')->get();
// GANTI 3 //
        return view('oreport_brg.kartu')->with(['brg' => $brg])->with(['hasil' => []]);
		
    }
	


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function jasperStokKartu(Request $request) 
	{
		$file 	= 'karstk';
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
			$filterbrg = " AND KD_BRG<>'' " ;
			$filterbeli = " AND belid.KD_BRG<>'' " ;
			$filtersurats = " AND suratsd.KD_BRG<>'' " ;
			$filterstock = " AND stockbd.KD_BRG<>'' " ;
			if($request->brg1)
			{
				$filterbrg = " AND KD_BRG between '".$request->brg1."' and '".$request->brg2."' " ;
				$filterbeli = " AND belid.KD_BRG between'".$request->brg1."' and '".$request->brg2."' " ;
				$filtersurats = " AND suratsd.KD_BRG between '".$request->brg1."' and '".$request->brg2."' " ;
				$filterstock = " AND stockbd.KD_BRG between '".$request->brg1."' and '".$request->brg2."' " ;
			}
            $tgawal = $tahun.'-'.$bulan.'-01';
		
			session()->put('filter_brg1', $request->brg1);
			session()->put('filter_nabrg1', $request->nabrg1);
			session()->put('filter_brg2', $request->brg2);
			session()->put('filter_nabrg2', $request->nabrg2);
			session()->put('filter_tglDr', $request->tglDr);
			session()->put('filter_tglSmp', $request->tglSmp);

		$queryakum = DB::SELECT("SET @akum:=0;");
		$query = DB::SELECT("
        	SELECT *, if(@kdbrg<>KD_BRG,@akum:=AWAL+MASUK-KELUAR+LAIN,@akum:=@akum+AWAL+MASUK-KELUAR+LAIN) as SALDO,@kdbrg:=KD_BRG as ganti, URUTAN from
		(
			SELECT ' ' AS NO_BUKTI, '$tglDrD'  AS TGL, KD_BRG AS KD_BRG, NA_BRG AS NA_BRG, 
			'SALDO AWAL' URAIAN, 
			SUM(AWAL) AS AWAL, 0 MASUK, 0 KELUAR, 0 AS LAIN, 1 as URUTAN
			from
			(

				SELECT CONCAT(KD_BRG,'-',CBG) AS KD_BRG , NA_BRG, AW$bulan AS AWAL 
				from brgd WHERE KD_BRG='$brg' and YER='$tahun'
				
				UNION ALL
				
				SELECT CONCAT(belid.KD_BRG,'-',belid.CBG) AS KD_BRG, belid.NA_BRG, belid.QTY AS AWAL 
				from beli, belid where beli.NO_BUKTI = belid.NO_BUKTI and beli.TGL<'$tglDrD' 
				and belid.KD_BRG='$brg' and beli.PER='$periode' and  belid.QTY <> 0  union all
		
				SELECT CONCAT(suratsd.KD_BRG,'-',suratsd.CBG) AS KD_BRG, suratsd.NA_BRG, ( suratsd.QTY * -1 ) AS AWAL 
				from surats, suratsd where surats.NO_BUKTI = suratsd.NO_BUKTI and surats.TGL<'$tglDrD' 
				and suratsd.KD_BRG='$brg' and surats.PER='$periode' and  suratsd.QTY <> 0  union all
				
				
				SELECT CONCAT(stockbd.KD_BRG,'-',stockbd.CBG) as KD_BRG, stockbd.NA_BRG, stockbd.QTY AS AWAL 
				from stockb, stockbd where stockb.NO_BUKTI = stockbd.NO_BUKTI and stockb.TGL<'$tglDrD' 
				and stockbd.KD_BRG='$brg' and stockb.PER='$periode' 

				
			) as AWAL00
			group by KD_BRG 
			UNION ALL

			SELECT beli.NO_BUKTI,  beli.TGL, CONCAT(belid.KD_BRG,'-',belid.CBG) AS KD_BRG, belid.NA_BRG, CONCAT('BELI-',TRIM(beli.NAMAS)) AS URAIAN, 0 AWAL, belid.QTY AS MASUK, 0 AS KELUAR, 0 AS LAIN,  2 as URUTAN 
			from beli, belid where beli.TGL BETWEEN '$tglDrD' and '$tglSmpD' $filterbeli and belid.QTY <> 0 and beli.PER='$periode' union all


			SELECT surats.NO_BUKTI, surats.TGL, CONCAT(suratsd.KD_BRG,'-',suratsd.CBG) AS KD_BRG,  suratsd.NA_BRG, CONCAT('JUAL-',TRIM(surats.NAMAC)) AS URAIAN, 0 AWAL, 0 AS MASUK, suratsd.QTY AS KELUAR,  0 AS LAIN, 4 as URUTAN  
			from surats, suratsd where surats.TGL BETWEEN '$tglDrD' and '$tglSmpD' $filtersurats and surats.PER='$periode' union all

			SELECT stockb.NO_BUKTI, stockb.TGL, CONCAT(stockbd.KD_BRG,'-',stockbd.CBG) as KD_BRG, stockbd.NA_BRG, CONCAT('KOREKSI-') AS URAIAN, 0 AWAL, 0 AS MASUK, 0 AS KELUAR, stockbd.QTY AS LAIN, 5 as URUTAN  
			from stockb, stockbd where stockb.NO_BUKTI = stockbd.NO_BUKTI and stockb.TGL BETWEEN '$tglDrD' and '$tglSmpD' $filterstock and stockb.PER='$periode' 
			
			order by KD_BRG, TGL, NO_BUKTI, URUTAN ASC
			
		) as kartustok  ;
		");

		$brg = Brg::where('KD_BRG', '<>','ZZ')->get();
		if($request->has('filter'))
		{
			return view('oreport_brg.kartu')->with(['brg' => $brg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				// 'KD_BRG' => $query[$key]->KD_BRG,
                'KD_BRG'    => "`".strval($query[$key]->KD_BRG),
                'CBG'    => "`".strval($query[$key]->CBG),
				'NA_BRG' => $query[$key]->NA_BRG,
				'URAIAN' => $query[$key]->URAIAN,
				'AWAL' => $query[$key]->AWAL,
				'MASUK' => $query[$key]->MASUK,
				'KELUAR' => $query[$key]->KELUAR,
				'LAIN' => $query[$key]->LAIN,
				'AKHIR' => $query[$key]->SALDO,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
