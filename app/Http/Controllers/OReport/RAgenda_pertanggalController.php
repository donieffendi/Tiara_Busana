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

class RAgenda_pertanggalController extends Controller
{
    public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_agenda_pertanggal.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }



	public function jasperAgenda_pertanggalReport(Request $request)
	{
		$file 	= 'ragenda_pertanggal';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));

			// Check Filter

			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and brg.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}

			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			if (!empty($request->cbg))
			{
				$filtercbg = " and po.CBG='".$request->cbg."' ";
			}

			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));

			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_cbg', $request->cbg);

			$query = DB::SELECT("SELECT AGD, AGD_TG, AGD_SP, SUP_NAMA, AGD_TOT_NET, TOT
                                from brg
                                $filtertgl $filterkodes
                                ORDER BY AGD;
			");



		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_agenda_pertanggal.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'AGD' => $query[$key]->AGD,
				'AGD_TG' => $query[$key]->AGD_TG,
				'AGD_SP' => $query[$key]->AGD_SP,
				'SUP_NAMA' => $query[$key]->SUP_NAMA,
				'AGD_TOT_NET' => $query[$key]->AGD_TOT_NET,
				'TOT' => $query[$key]->TOT,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}

}
