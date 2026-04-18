<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use Carbon\Carbon;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RTidakkirimController extends Controller
{
    public function report()
    {	
		$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_namas1', '');
		session()->put('filter_kodes2', '');
		session()->put('filter_namas2', '');
        return view('oreport_tidakkirim.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperTidakkirimReport(Request $request)
	{
		\Log::info('REQUEST MASUK', [
			'time' => now(),
			'ip' => request()->ip(),
			'ua' => request()->userAgent()
		]);

		$file = 'Surat_Tidakkirim';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(
			base_path('/app/reportc01/phpjasperxml/'.$file.'.jrxml')
		);

		$params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;

		// ================= SESSION =================
		session()->put('filter_kodes1', $request->kodes1);
		session()->put('filter_namas1', $request->namas1);
		session()->put('filter_kodes2', $request->kodes2);
		session()->put('filter_namas2', $request->namas2);
		session()->put('filter_periode', $request->per);

		// ================= FILTER =================
		$filterkodes = "WHERE 1=1";

		if (!empty($request->kodes1) && !empty($request->kodes2)) {
			$filterkodes .= " AND NO_SUPL BETWEEN '".$request->kodes1."' AND '".$request->kodes2."'";
		}

		// ================= FILTER BUTTON =================
		if ($request->has('filter')) {

			$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			$query = DB::select("
				SELECT 
					(@rownum := @rownum + 1) AS ROWNUM,
					NO_SUPL,
					NAMA,
					ALMT_K,
					KOTA,
					GOL_BRG,
					(BUDGET_AWL - BUDGET_LL) AS BUDGET,
					'' AS CETAK
				FROM nwmassup, (SELECT @rownum := 0) r
				$filterkodes
				AND (BUDGET_AWL - BUDGET_LL) < 0
				AND NOT (
					IFNULL(FLAG_SB1,'') <> ''
					AND IFNULL(FLAG_SB2,'') <> ''
					AND IFNULL(FLAG_SB3,'') <> ''
					AND IFNULL(CET_TEGUR,'') = 'T'
				)
			");

			return view('oreport_tidakkirim.report')
				->with(['per' => $per])
				->with(['hasil' => $query]);
		}

		// ================= CETAK =================
		$selected = json_decode($request->selected_suppliers, true);

		if (empty($selected)) {
			return back()->with('error', 'Pilih data yang akan dicetak!');
		}

		// 🔒 ================= ANTI DOUBLE REQUEST =================
		$sessionKey = 'block_cetak';

		if (session()->has($sessionKey)) {
			$last = session($sessionKey);

			if (now()->diffInSeconds($last) < 3) {
				\Log::info('DOUBLE REQUEST DIBLOK');

				// ⚠️ tetap tampilkan jasper TANPA update
				$in = "'" . implode("','", $selected) . "'";

				$query = DB::select("
					SELECT 
						(@rownum := @rownum + 1) AS ROWNUM,
						NO_SUPL,
						NAMA,
						ALMT_K,
						KOTA,
						GOL_BRG,
						(BUDGET_AWL - BUDGET_LL) AS BUDGET,
						'' AS CETAK
					FROM nwmassup, (SELECT @rownum := 0) r
					WHERE NO_SUPL IN ($in)
					AND (BUDGET_AWL - BUDGET_LL) < 0
				");

				$data = json_decode(json_encode($query), true);

				$PHPJasperXML->setData($data);
				ob_end_clean();
				$PHPJasperXML->outpage("I");

				return;
			}
		}

		// simpan waktu request pertama
		session([$sessionKey => now()]);

		DB::beginTransaction();

		try {

			$last = DB::table('nwmassup')->max('NO_TEGUR2');
			$no_urut = $last ? (int)$last + 1 : 1;

			$tgl = date('d/m/Y');

			foreach ($selected as $sup) {

				$no_teguran = str_pad($no_urut, 6, '0', STR_PAD_LEFT);

				// SB1
				$updated = DB::update("
					UPDATE nwmassup 
					SET FLAG_SB1 = 'X',
						P_SB1 = ?,
						CET_TEGUR = 'T',
						NO_TEGUR2 = ?
					WHERE NO_SUPL = ?
					AND (FLAG_SB1 IS NULL OR TRIM(FLAG_SB1) = '')
				", [$tgl, $no_teguran, $sup]);

				if ($updated) {
					$no_urut++;
					continue;
				}

				// SB2
				$updated = DB::update("
					UPDATE nwmassup 
					SET FLAG_SB2 = 'X',
						P_SB2 = ?,
						NO_TEGUR2 = ?
					WHERE NO_SUPL = ?
					AND (FLAG_SB2 IS NULL OR TRIM(FLAG_SB2) = '')
				", [$tgl, $no_teguran, $sup]);

				if ($updated) {
					$no_urut++;
					continue;
				}

				// SB3
				$updated = DB::update("
					UPDATE nwmassup 
					SET FLAG_SB3 = 'X',
						P_SB3 = ?,
						NO_TEGUR2 = ?
					WHERE NO_SUPL = ?
					AND (FLAG_SB3 IS NULL OR TRIM(FLAG_SB3) = '')
				", [$tgl, $no_teguran, $sup]);

				if ($updated) {
					$no_urut++;
					continue;
				}
			}

			// ambil data jasper
			$in = "'" . implode("','", $selected) . "'";

			$query = DB::select("
				SELECT 
					(@rownum := @rownum + 1) AS ROWNUM,
					NO_SUPL,
					NAMA,
					ALMT_K,
					KOTA,
					GOL_BRG,
					(BUDGET_AWL - BUDGET_LL) AS BUDGET,
					'' AS CETAK
				FROM nwmassup, (SELECT @rownum := 0) r
				WHERE NO_SUPL IN ($in)
				AND (BUDGET_AWL - BUDGET_LL) < 0
			");

			DB::commit();

			$data = json_decode(json_encode($query), true);

			$PHPJasperXML->setData($data);
			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {

			DB::rollBack();
			return back()->with('error', $e->getMessage());
		}
	}
	
}
