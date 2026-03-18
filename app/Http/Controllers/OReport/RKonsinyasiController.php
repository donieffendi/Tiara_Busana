<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Perid;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RKonsinyasiController extends Controller
{

   	public function report()
    {
		$per = Perid::query()->get();
		session()->put('filter_periode', '');

		session()->put('filter_cnt', '');
		session()->put('filter_ncnt', '');

        return view('oreport_konsinyasi.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	

	public function jasperKonsinyasiReport(Request $request) 
	{
		$file 	= 'tpiun';
		$PHPJasperXML = new PHPJasperXML('en', 'TCPDF');
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
		$per   = $request->per;
		$rekap = $request->rekap ?? 0;

		// 🔹 Ambil PPN
		$ppnData = DB::select("CALL PPNPER(?)", [$per]);
		$ppnx = $ppnData[0]->PPN ?? 0;

		session()->put('filter_cnt', $request->cnt);
		session()->put('filter_ncnt', $request->ncnt);
		session()->put('filter_periode', $request->per);
		session()->put('filter_rekap', $request->rekap);
		// =====================================================
		// 🔵 FILTER (KoolReport)
		// =====================================================
		
		if($request->has('filter'))
		{
			if($rekap == 1)
			{
				// 🔹 REKAP
				$query = DB::SELECT("
					SELECT cnt,conter,PER AS per,
					SUM(nilai_nota) as nett,
					MIN(tgl_jual) as tgl_min,
					MAX(tgl_jual) as tgl_max
					FROM (
						SELECT *,
						nilai_jual-nilai_margin+ptiara as nilai_nota,
						IF(ST_PJK='P1', ROUND((nilai_jual/(1 + ?) * ?)),0) as ppn,
						(nilai_jual - IF(ST_PJK='P1', ROUND((nilai_jual/(1 + ?) * ?)),0)) as nett
						FROM (
							SELECT 
								CONCAT(TRIM(rkjdbsn.cnt),'   ',TRIM(rkjdbsn.na_cnt)) as conter,
								rkjdbsn.tgl_jual,
								rkjdbsn.cnt,
								rkjdbsn.ST_PJK,
								rkjdbsn.na_cnt,
								SUM(rkjdbsn.qty) as qty,
								SUM(rkjdbsn.tharga) as tharga,
								rkjdbsn.dis,
								rkjdbsn.par,
								SUM(rkjdbsn.ptiara) as ptiara,
								SUM(rkjdbsn.psup) as psup,
								SUM(nilai_jual) as nilai_jual,
								ROUND(SUM(rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100)) as nilai_margin,
								rkjdbsn.margin,
								rkjdbsn.PER,
								supbsn.kodes,
								supbsn.namas
							FROM rkjdbsn
							LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
							WHERE rkjdbsn.per = ?
							GROUP BY rkjdbsn.cnt, rkjdbsn.tgl_jual
						) ss
					) jjj
					GROUP BY cnt
					ORDER BY cnt
				", [$ppnx,$ppnx,$ppnx,$ppnx,$per]);
			}
			else
			{
				// 🔹 NON REKAP → tampil data existing
				$query = DB::SELECT("
					SELECT NO_BUKTI,CNT as cnt,NACNT AS ncnt,PER AS per,KODES,NAMAS,KOTA,TOTAL
					FROM belibsnz
					WHERE PER = ?
					ORDER BY NO_BUKTI
				", [$per]);
			}

			return view('oreport_konsinyasi.report')
				->with(['hasil' => $query])
				->with(['per' => Perid::all()]);
		}

		// =====================================================
		// 🔴 CETAK (JASPER)
		// =====================================================
		if($rekap == 1)
		{
			// 🔹 REKAP
			$query = DB::SELECT("
				SELECT cnt,conter,per,
				SUM(nilai_nota) as nett,
				MIN(tgl_jual) as tgl_min,
				MAX(tgl_jual) as tgl_max
				FROM (
					SELECT *,
					nilai_jual-nilai_margin+ptiara as nilai_nota,
					IF(ST_PJK='P1', ROUND((nilai_jual/(1 + ?) * ?)),0) as ppn,
					(nilai_jual - IF(ST_PJK='P1', ROUND((nilai_jual/(1 + ?) * ?)),0)) as nett
					FROM (
						SELECT 
							CONCAT(TRIM(rkjdbsn.cnt),'   ',TRIM(rkjdbsn.na_cnt)) as conter,
							rkjdbsn.tgl_jual,
							rkjdbsn.cnt,
							rkjdbsn.ST_PJK,
							rkjdbsn.na_cnt,
							SUM(rkjdbsn.qty) as qty,
							SUM(rkjdbsn.tharga) as tharga,
							rkjdbsn.dis,
							rkjdbsn.par,
							SUM(rkjdbsn.ptiara) as ptiara,
							SUM(rkjdbsn.psup) as psup,
							SUM(nilai_jual) as nilai_jual,
							ROUND(SUM(rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100)) as nilai_margin,
							rkjdbsn.margin,
							rkjdbsn.PER,
							supbsn.kodes,
							supbsn.namas
						FROM rkjdbsn
						LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
						WHERE rkjdbsn.per = ?
						GROUP BY rkjdbsn.cnt, rkjdbsn.tgl_jual
					) ss
				) jjj
				GROUP BY cnt
				ORDER BY cnt
			", [$ppnx,$ppnx,$ppnx,$ppnx,$per]);
		}
		else
		{
			// 🔥 NON REKAP → PROSES INSERT
			DB::beginTransaction();

			try {

				$dataCounter = DB::SELECT("
					SELECT cntbsn.cnt,cntbsn.na_cnt,
						supbsn.kodes,supbsn.namas,supbsn.b_kota
					FROM cntbsn
					LEFT JOIN supbsn ON cntbsn.sup=supbsn.kodes
					WHERE cntbsn.kw_ret='Y'
				");

				foreach ($dataCounter as $row)
				{
					$cek = DB::table('belibsnz')
						->where('gol',1)
						->where('flag','LL')
						->where('per',$per)
						->where('cnt',$row->cnt)
						->first();

					if(!$cek)
					{
						$no = DB::table('notrans')
							->where('trans','LL')
							->where('PER', substr($per,-4))
							->first();

						$field = "NOM".substr($per,0,2);
						$r1 = $no->$field + 1;

						DB::table('notrans')
							->where('trans','LL')
							->update([$field => $r1]);

						$kode = 'LL'.substr($per,-2).substr($per,0,2);
						$bukti = $kode.'-'.str_pad($r1,4,'0',STR_PAD_LEFT).'Z';

						DB::table('belibsnz')->insert([
							'NO_BUKTI' => $bukti,
							'TGL' => now(),
							'FLAG' => 'LL',
							'PER' => $per,
							'CBG' => Auth::user()->CBG ?? '',
							'CNT' => $row->cnt,
							'NCNT' => $row->na_cnt,
							'KODES' => $row->kodes,
							'NAMAS' => $row->namas,
							'KOTA' => $row->b_kota,
							'TOTAL' => -4000,
							'gol' => 1
						]);

						DB::table('belibsnzd')->insert([
							'NO_BUKTI' => $bukti,
							'REC' => 1,
							'TOTAL' => -4000,
							'ACNOD' => '61.800.122',
							'NACNOD' => 'OPERASIONAL DEPARTEMEN',
							'KET' => 'LL '.$bukti.' '.$row->namas
						]);
					}
				}

				DB::commit();

			} catch (\Exception $e) {
				DB::rollback();
				dd($e->getMessage());
			}

			$query = DB::SELECT("
				SELECT NO_BUKTI,CNT,NCNT,KODES,NAMAS,KOTA,TOTAL
				FROM belibsnz
				WHERE PER = ?
			", [$per]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			$data[] = (array) $value;
		}

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
