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

class RRubahharga_discbudgetController extends Controller
{

  	public function report()
    {
		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
		session()->put('filter_cbg', '');

		$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');

		session()->put('filter_nabrg1', '');
		session()->put('filter_kdgd1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_rubahharga_discbudget.report')->with(['cbg' => $cbg])->with(['per' => $per])->with(['hasil' => []]);
    }


	public function jasperRubahharga_discbudgetReport(Request $request)
	{
		$file 	= 'Laporan_Diskon_Budget';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));

			// Check Filter

			$cbg = $request->cbg;
			$per = $request->per;

			$bulan = substr($per,0,2);
			$tahun = substr($per,3,4);

			$plu = $request->kode1;

			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}


			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));

			session()->put('filter_cbg', $request->cbg);
			session()->put('filter_periode', $request->per);
			session()->put('filter_kode1', $request->kode1);
			session()->put('filter_nama1', $request->nama1);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);

		$query = DB::SELECT(" SELECT NO_BUKTI, TGL, JTEMPO, KODEC, NAMAC,
									TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
							FROM jual
							WHERE FLAG = 'JL' AND PER = '$per' AND CBG = '$cbg' $filtertgl
							ORDER BY NO_BUKTI;

		");

		if($request->has('filter'))
		{
			$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
			$per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			return view('oreport_rubahharga_discbudget.report')->with(['cbg' => $cbg])->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];

		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}

}
