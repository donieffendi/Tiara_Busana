<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use App\Models\Master\Perid;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

class RPembelianController extends Controller
{
    /**
     * Halaman utama report - Route: /rkasirbantu
     */
    public function report(Request $request)
    {
		$per = Perid::query()->get();
		$perx = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
		session()->put('filter_periode', $perx);

        return view('oreport_pembelian.report')->with([
            'per' => $per,
            'hasilPembelian' => []
        ]);
    }

    public function getPembelianReport(Request $request)
    {
        $listPer = Perid::query()->get();
        $tab = $request->tab ?? 'detail';

        switch ($tab) {
            case 'detail':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
						'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Periode.'
                    ]);
                }
                $hasilPembelian = $this->getDetailPembelian($request->per);
            break;
            
            case 'Summary':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
						'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Sub Beli.'
                    ]);
                }
                $hasilPembelian = $this->getSummaryPembelian($request->per);
            break;

            case 'kasir':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
                        'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Report Retur.'
                    ]);
                }
                $hasilPembelian = $this->getKasirList($request->per);
            break;

			case 'rconter':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
                        'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Rekap Sub Retur.'
                    ]);
                }
                $hasilPembelian = $this->getRekapCounter($request->per);
            break;

			case 'rjual':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
                        'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Rekap Pembelian.'
                    ]);
                }
                $hasilPembelian = $this->getRekapPembelian($request->per);
            break;

			case 'rhari':
                if (empty($request->per)) {
                    return view('oreport_pembelian.report')->with([
                        'per' => $listPer,
                        'hasilPembelian' => [],
                        'error' => 'Periode harus dipilih untuk tab Rekap Harian.'
                    ]);
                }
                $hasilPembelian = $this->getRekapHarian($request->per);
            break;
        }

        return view('oreport_pembelian.report')->with([
			'per' => $listPer,
            'hasilPembelian' => $hasilPembelian,
            'tab' => $tab
        ]);
    }

    public function getPembelianReportAjax(Request $request)
	{
		$tab = $request->tab ?? 'detail';

		$periode = $request->per;
		$bulan = substr($periode,0,2);

		switch ($tab) {
			case 'detail':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Periode harus dipilih untuk tab Periode'
					], 400);
				}
				$data = $this->getDetailPembelian($periode);
			break;

			case 'summary':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Periode harus dipilih untuk tab Sub Beli.'
					], 400);
				}
				$data = $this->getSummaryPembelian($periode);
			break;

			case 'kasir':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Periode harus dipilih untuk tab Report Retur.'
					], 400);
				}
				$data = $this->getKasirList($periode);
			break;

			case 'rconter':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Periode harus dipilih untuk tab Sub Retur.'
					], 400);
				}
				$data = $this->getRekapCounter($periode);
			break;

			case 'rjual':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Periode harus dipilih untuk tab Sub Konsinyasi'
					], 400);
				}
				$data = $this->getRekapPembelian($periode);
			break;

			case 'rhari':
				if (empty($periode)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Transaksi Lain-Lain'
					], 400);
				}
				$data = $this->getRekapHarian($periode);
			break;
		}

		return response()->json([
			'success' => true,
			'data' => $data
		]);
	}



    /**
     * Generate laporan Jasper - Route: /jasper-kasirbantu-report
     * Implementasi dari logika Delphi untuk generate report
     */
    public function jasperPembelianReport(Request $request)
    {
        try {

            $file = 'report pembelian repretur'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			$cbg = Auth::user()->CBG;
            $per = $request->per;
            $sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,
					
					belibsnz.NO_BUKTI,
					belibsnz.TGL,
					belibsnz.REF,
					belibsnz.per,
					belibsnz.cnt,
					cntbsn.NA_CNT,
					belibsnz.kodes,
					belibsnz.namas,
					belibsnz.FLAG,
					belibsnz.total,
					belibsnz.PROM,
					belibsnz.dpp,
					belibsnz.ppn,
					belibsnz.nett
					
				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'RX'

				ORDER BY belibsnz.NO_BUKTI ASC
            ";

            $data = DB::select($sql, [$cbg, $per]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN PEMBELIAN PER SUB ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function jasperPembelianDetailReport(Request $request)
    {
        try {

            $file = 'report pembelian periode'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

            $per = $request->per;
            $sql = "SELECT 
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.kode as nmtoko,

					belibsnz.NO_BUKTI,
					belibsnz.TGL,
					belibsnz.REF,
					belibsnz.per,
					belibsnz.cnt,
					cntbsn.NA_CNT,
					belibsnz.kodes,
					belibsnz.namas as NAMAS,
					belibsnz.FLAG,
					belibsnz.total,
					belibsnz.PROM,
					belibsnz.dpp,
					belibsnz.ppn,
					belibsnz.nett  

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = 'tgz'

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'BS'

				ORDER BY belibsnz.NO_BUKTI ASC 
            ";

            $data = DB::select($sql, [$per]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN AGENDA PER TANGGAL ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function jasperPembelianSummaryReport(Request $request)
    {
        try {

            $file = 'report pembelian subbeli'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			$cbg = Auth::user()->CBG;
            $per = $request->per;
            $sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.kode as nmtoko,
					
					belibsnz.cnt as SUB,
					cntbsn.NA_CNT AS KELOMPOK,
					sum(belibsnz.total) as BRUTO,
					sum(belibsnz.PROM) as prom

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'BS'

				GROUP BY belibsnz.cnt
            ";

            $data = DB::select($sql, [$cbg, $per]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN PEMBELIAN PER SUB ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

	public function jasperPembelianSubReturReport(Request $request)
    {
        try {

            $file = 'report pembelian subbeli'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			$cbg = Auth::user()->CBG;
            $per = $request->per;
            $sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,
					
					belibsnz.cnt as SUB,
					cntbsn.NA_CNT as KELOMPOK,
					sum(belibsnz.total) as BRUTO,
					sum(belibsnz.PROM) as prom

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'RX'

				GROUP BY belibsnz.cnt";

            $data = DB::select($sql, [$cbg, $per]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN RETUR PER SUB ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

	public function jasperPembelianKonsinyasiReport(Request $request)
    {
        try {

            $file = 'report pembelian kons'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			$cbg = Auth::user()->CBG;
            $per = $request->per;
            $sql = "SELECT 
					AA.*,
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat

				FROM (
					SELECT 
						belibsnz.ACNO,
						belibsnz.per,
						belibsnz.cnt as sub,
						cntbsn.na_cnt as kelompok,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						SUM(belibsnz.total) as total,
						SUM(belibsnz.ppn) as ppn,
						SUM(belibsnz.nett) as nett,
						SUM(belibsnz.prom) as prom

					FROM belibsnz
					JOIN cntbsn ON belibsnz.cnt = cntbsn.CNT

					WHERE 
						cntbsn.st_cnt = 'K'
						AND belibsnz.per = ?
						AND belibsnz.FLAG = 'BK'

					GROUP BY belibsnz.cnt, belibsnz.ACNO, belibsnz.flag
				) AA

				JOIN toko ON toko.KODE = ?
				ORDER BY AA.sub";

            $data = DB::select($sql, [$per, $cbg]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN KONSIYIASI PER SUB ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

	public function jasperPembelianLainReport(Request $request)
    {
        try {

            $file = 'report pembelian lain'; 
            $PHPJasperXML = new PHPJasperXML();
            $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			$cbg = Auth::user()->CBG;
            $per = $request->per;
            $sql = "SELECT 
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,

					AA.NO_BUKTI,
					AA.TGL,
					AA.NACNO AS nacc,
					TRIM(AA.ACNO) AS ACNO,
					AA.KODES,
					AA.PER,
					UPPER(AA.ket) AS KET,
					IF(AA.total>0,AA.total,0) as DEBET,
					IF(AA.total<0,AA.total*-1,0) as KREDIT

				FROM (
					SELECT  
						belibsnz.tgl,
						belibsnz.notes AS KET,
						belibsnz.ACNO,
						belibsnz.NACNO,
						belibsnz.NO_BUKTI,
						belibsnz.per,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						belibsnz.TOTAL * -1 as total

					FROM tgz.belibsnz
					WHERE belibsnz.per = ?
					AND belibsnz.FLAG = 'LB'

					UNION ALL

					SELECT  
						belibsnz.tgl,
						belibsnzd.ket AS KET,
						belibsnzd.ACNOD AS ACNO,
						belibsnzd.NACNOD,
						belibsnz.NO_BUKTI,
						belibsnz.per,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						belibsnzd.TOTAL as total

					FROM tgz.belibsnz
					JOIN tgz.belibsnzd 
						ON belibsnz.NO_BUKTI = belibsnzd.NO_BUKTI

					WHERE belibsnz.per = ?
					AND belibsnz.FLAG = 'LL'

				) AA

				JOIN toko ON toko.KODE = ?

				ORDER BY AA.NO_BUKTI, AA.TGL";

            $data = DB::select($sql, [$per,$per, $cbg]);

            $cleanData = json_decode(json_encode($data), true);
       		$PHPJasperXML->setData($cleanData);

            // 
            $PHPJasperXML->arrayParameter = [
				'JUDULE' => "LAPORAN TRANSAKSI LAIN-LAIN ",
                "TGL_CTK" => date('d/m/Y')
            ];

            ob_end_clean();
            $PHPJasperXML->outpage("I");

        } catch (\Exception $e) {
            Log::error('Error Jasper Kasir: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getDetailPembelian($periode)
    {	
		$cbg = Auth::user()->CBG;

		$sql = "
				SELECT 
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,

					belibsnz.NO_BUKTI,
					belibsnz.TGL,
					belibsnz.REF,
					belibsnz.per,
					belibsnz.cnt,
					cntbsn.NA_CNT,
					belibsnz.kodes,
					belibsnz.namas,
					belibsnz.FLAG,
					belibsnz.total,
					belibsnz.PROM,
					belibsnz.dpp,
					belibsnz.ppn,
					belibsnz.nett  

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'BS'

				ORDER BY belibsnz.NO_BUKTI ASC
				";

		return DB::select($sql, [$cbg, $periode]);
    }


    private function getSummaryPembelian($periode)
    {
        $cbg = Auth::user()->CBG;

		$sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,
					
					belibsnz.cnt,
					cntbsn.NA_CNT,
					sum(belibsnz.total) as bruto,
					sum(belibsnz.PROM) as prom

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'BS'

				GROUP BY belibsnz.cnt";

		return DB::select($sql, [$cbg, $periode]);
    }

    public function getKasirList($periode)
	{
		$cbg = Auth::user()->CBG;

		$sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,
					
					belibsnz.NO_BUKTI,
					belibsnz.TGL,
					belibsnz.REF,
					belibsnz.per,
					belibsnz.cnt,
					cntbsn.NA_CNT,
					belibsnz.kodes,
					belibsnz.namas,
					belibsnz.FLAG,
					belibsnz.total,
					belibsnz.PROM,
					belibsnz.dpp,
					belibsnz.ppn,
					belibsnz.nett
					
				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'RX'

				ORDER BY belibsnz.NO_BUKTI ASC";

		return DB::select($sql, [$cbg, $periode]);
	}

	public function getRekapCounter($periode)
	{
		$cbg = Auth::user()->CBG;

		$sql = "SELECT toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,
					toko.nama_toko as nmtoko,
					
					belibsnz.cnt as sub,
					cntbsn.NA_CNT as kelompok,
					sum(belibsnz.total) as bruto,
					sum(belibsnz.PROM) as prom

				FROM tgz.belibsnz
				JOIN tgz.cntbsn ON belibsnz.cnt = cntbsn.CNT
				JOIN toko ON toko.KODE = ?

				WHERE 
					cntbsn.st_cnt = 'P'
					AND belibsnz.per = ?
					AND belibsnz.FLAG = 'RX'

				GROUP BY belibsnz.cnt";

		return DB::select($sql, [$cbg, $periode]);
	}

	public function getRekapPembelian($periode)
	{
		$cbg = Auth::user()->CBG;

		$sql = "SELECT 
					AA.*,
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat

				FROM (
					SELECT 
						belibsnz.ACNO,
						belibsnz.per,
						belibsnz.cnt as sub,
						cntbsn.na_cnt as kelompok,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						SUM(belibsnz.total) as total,
						SUM(belibsnz.ppn) as ppn,
						SUM(belibsnz.nett) as nett,
						SUM(belibsnz.prom) as prom

					FROM belibsnz
					JOIN cntbsn ON belibsnz.cnt = cntbsn.CNT

					WHERE 
						cntbsn.st_cnt = 'K'
						AND belibsnz.per = ?
						AND belibsnz.FLAG = 'BK'

					GROUP BY belibsnz.cnt, belibsnz.ACNO, belibsnz.flag
				) AA

				JOIN toko ON toko.KODE = ?
				ORDER BY AA.sub";

		return DB::select($sql, [$periode, $cbg]);
	}

	private function getRekapHarian($periode)
    {
        $cbg = Auth::user()->CBG;

		$sql = "SELECT 
					toko.na_toko,
					toko.typ_pers,
					toko.typ_npwp,
					toko.alamat,

					AA.NO_BUKTI,
					AA.TGL,
					AA.NACNO AS nacc,
					TRIM(AA.ACNO) AS ACNO,
					AA.KODES,
					AA.PER,
					UPPER(AA.ket) AS KET,
					IF(AA.total>0,AA.total,0) as DEBET,
					IF(AA.total<0,AA.total*-1,0) as KREDIT

				FROM (
					SELECT  
						belibsnz.tgl,
						belibsnz.notes AS KET,
						belibsnz.ACNO,
						belibsnz.NACNO,
						belibsnz.NO_BUKTI,
						belibsnz.per,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						belibsnz.TOTAL * -1 as total

					FROM tgz.belibsnz
					WHERE belibsnz.per = ?
					AND belibsnz.FLAG = 'LB'

					UNION ALL

					SELECT  
						belibsnz.tgl,
						belibsnzd.ket AS KET,
						belibsnzd.ACNOD AS ACNO,
						belibsnzd.NACNOD,
						belibsnz.NO_BUKTI,
						belibsnz.per,
						belibsnz.kodes,
						belibsnz.namas,
						belibsnz.FLAG,
						belibsnzd.TOTAL as total

					FROM tgz.belibsnz
					JOIN tgz.belibsnzd 
						ON belibsnz.NO_BUKTI = belibsnzd.NO_BUKTI

					WHERE belibsnz.per = ?
					AND belibsnz.FLAG = 'LL'

				) AA

				JOIN toko ON toko.KODE = ?

				ORDER BY AA.NO_BUKTI, AA.TGL";

		return DB::select($sql, [$periode, $periode, $cbg]);
    }
}