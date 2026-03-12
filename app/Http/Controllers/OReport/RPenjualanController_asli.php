<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Cust;
use App\Models\Master\Perid;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RPenjualanController extends Controller
{
 	public function report()
    {
		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA='MA'");
		session()->put('filter_cbg', '');
		$per = Perid::query()->get();
		session()->put('filter_periode', '');
		
		session()->put('filter_CNT', '');
		session()->put('filter_NA_CNT', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_penjualan.report')->with(['per' => $per])->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	

	public function jasperPenjualanReport(Request $request)
	{
		// dd($request->all());
		$query = $this->prosesPenjualanBC($request);

		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA='MA'");
		$per = Perid::query()->get();

		return view('oreport_penjualan.report',[
			'hasil'=>$query,
			'cbg'=>$cbg,
			'per'=>$per
		]);
	}

	public function prosesPenjualanBC(Request $request)
	{
		$cbg = $request->cbg;
		$cnt = $request->CNT;
		$tgl1 = date('Y-m-d',strtotime($request->tglDr));
		$tgl2 = date('Y-m-d',strtotime($request->tglSmp));

		$periode = $request->per;
		$bulan = substr($periode,0,2);

		if($cbg != 'ALL')
		{

			$data = DB::select("
				SELECT B.*, A.tg_smp, A.KSR,
					A.tgl as initanggal,
					A.usrnm,
					A.CBG as cabang,
					1 as posted
				FROM {$cbg}.jual{$bulan} A
				JOIN {$cbg}.juald{$bulan} B
					ON A.no_bukti=B.no_bukti
				WHERE A.flag='JL'
				AND length(B.kd_brg)>7
				AND A.cbg=?
				AND (?='' OR left(B.kd_brg,5)=?)
				AND A.tgl BETWEEN ? AND ?
				ORDER BY A.tg_smp,B.KD_BRG,A.ksr,A.no_bukti,B.bukti2
			",[$cbg,$cnt,$cnt,$tgl1,$tgl2]);

		}
		else
		{

			$cabang = DB::table('toko')->get();
			$sql = "";

			foreach($cabang as $i=>$c)
			{

				$query = "
				SELECT juald{$bulan}.*, jual{$bulan}.tg_smp,
					jual{$bulan}.KSR,
					jual{$bulan}.tgl as initanggal,
					jual{$bulan}.usrnm,
					'ALL' as cabang,
					1 as posted
				FROM {$c->KODE}.juald{$bulan}
				JOIN {$c->KODE}.jual{$bulan}
					ON juald{$bulan}.no_bukti=jual{$bulan}.no_bukti
				WHERE jual{$bulan}.flag='JL'
				AND length(juald{$bulan}.kd_brg)>7
				AND jual{$bulan}.cbg='{$c->KODE}'
				AND jual{$bulan}.tgl BETWEEN '$tgl1' AND '$tgl2'
				";

				if($i==0)
					$sql = $query;
				else
					$sql .= " UNION ALL ".$query;

			}

			$sql .= " ORDER BY KD_BRG,ksr,no_bukti,tg_smp,bukti2";

			$data = DB::select($sql);

		}

		return $data;
	}
	
}
