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

class RBrg_tidak_laku_jualController extends Controller
{
    public function report()
    {
		session()->put('filter_brg1', '');
		// session()->put('filter_nabrg1', '');

		session()->put('filter_brg2', '');
		// session()->put('filter_nabrg2', '');

        return view('oreport_brg_tidak_laku_jual.report')->with(['hasil' => []]);
    }



	public function jasperBrg_tidak_laku_jualReport(Request $request)
	{
		$file 	= 'rbrg_tidak_laku_jual';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;

			// Check Filter


			$filterbrg = "";
			if (!empty($request->brg1) && !empty($request->brg2))
			{
				$filterbrg = "WHERE a.KDBAR between '".$request->brg1."' and '".$request->brg2."' ";
			}

            $filternopiu = "";
			if (!empty($request->nopiu1) && !empty($request->nopiu2))
			{
				$filternopiu = "WHERE a.NO_PIU between '".$request->nopiu1."' and '".$request->nopiu2."' ";
			}

			session()->put('filter_brg1', $request->brg1);
			session()->put('filter_nopiu1', $request->nopiu1);
			// session()->put('filter_nabrg1', $request->nabrg1);
			session()->put('filter_brg2', $request->brg2);
			session()->put('filter_nopiu2', $request->nopiu2);
			// session()->put('filter_nabrg2', $request->nabrg2);

		$query = DB::SELECT("
			SELECT
				(@rownum := @rownum + 1) AS ROWNUM,
				a.KDBAR,
				a.NMBAR,
				a.SA,
				a.BL,
				a.RJ,
				a.JL,
				a.KR1,
				a.KR2,
				0 AS SALDO,
				0 AS STOKR,
				a.SUPP,
				b.NAMA
			FROM nwmasbar a
			JOIN nwmassup b ON a.SUPP = b.NO_SUPL
			CROSS JOIN (SELECT @rownum := 0) r
			$filterbrg $filternopiu
		");



		if($request->has('filter'))
		{
			return view('oreport_brg_tidak_laku_jual.report')->with(['hasil' => $query]);
		}

		$data=[];
		$data = json_decode(json_encode($query), true);
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}

}
