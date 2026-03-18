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

class RBrg_tidak_laku_bintangController extends Controller
{
    public function report()
    {
		$cbg = Cbg::groupBy('CBG')->get();
		session()->put('filter_cbg', '');

		session()->put('filter_gol', '');
		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', 'ZZZ');
		session()->put('filter_namas1', '');
		session()->put('filter_brg1', '');
		session()->put('filter_nabrg1', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_brg_tidak_laku_bintang.report')->with(['cbg' => $cbg])->with(['hasil' => []]);
    }



	public function jasperBrg_tidak_laku_bintangReport(Request $request)
	{
		$file 	= 'rbrg_tidak_laku_bintang';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));

			// Check Filter

			// if (!empty($request->gol))
			// {
			// 	$filtergol = " and po.GOL='".$request->gol."' ";
			// }

			// if (!empty($request->kodes))
			// {
			// 	$filterkodes = " and po.KODES='".$request->kodes."' ";
			//

			// if (!empty($request->kodes) && !empty($request->kodes2))
			// {
			// 	$filterkodes = " and po.KODES between '".$request->kodes."' and '".$request->kodes2."' ";
			// }

			if (!empty($request->tglDr) && !empty($request->tglSmp))
			{
				$tglDrD = date("Y-m-d", strtotime($request->tglDr));
				$tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
				$filtertgl = " and po.TGL between '".$tglDrD."' and '".$tglSmpD."' ";
			}

			if($request['cbg'])
			{
				$cbg = $request['cbg'];
			}

			// if (!empty($request->cbg))
			// {
			// 	$filtercbg = " and po.CBG='".$request->cbg."' ";
			// }

			$tgl_1 = date("Y-m-d", strtotime($request->tglDr));
			$tgl_2 = date("Y-m-d", strtotime($request->tglSmp));
			$kodes_1 = $request->kodes;
			$kodes_2 = $request->kodes2;

			session()->put('filter_gol', $request->gol);
			session()->put('filter_kodes1', $request->kodes);
			session()->put('filter_kodes2', $request->kodes2);
			session()->put('filter_namas1', $request->NAMAS);
			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_brg1', $request->brg1);
			session()->put('filter_nabrg1', $request->nabrg1);
			session()->put('filter_cbg', $request->cbg);

		if( $filtergol == 'A' ){

			$query = DB::SELECT("SELECT SP, KDBAR, SUB, CONCAT(NM_BAR+KET_UK) AS BARANG,
                                        HARGA, QTY_BELI, TG_BELI, STOCKR, QTY from brg
                                $filtertgl
                                ORDER BY SP;
			");
		} if ($filtergol == 'B') {
			$query = DB::SELECT("SELECT SP, KDBAR, SUB, CONCAT(NM_BAR+KET_UK) AS BARANG,
                                        HARGA, QTY_BELI, TG_BELI, STOCKR, QTY from brg
                                $filtertgl
                                ORDER BY SP;
			");
		} if ($filtergol == 'C') {
            $query = DB::SELECT("SELECT SP, KDBAR, SUB, CONCAT(NM_BAR+KET_UK) AS BARANG,
                                        HARGA, QTY_BELI, TG_BELI, STOCKR, QTY from brg
                                $filtertgl
                                ORDER BY SP;
			");
		}

		if($request->has('filter'))
		{
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_brg_tidak_laku_bintang.report')->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'SP' => $query[$key]->SP,
				'KDBAR' => $query[$key]->KDBAR,
				'TGL_1' => $tgl_1,
				'TGL_2' => $tgl_2,
				'SUB' => $query[$key]->SUB,
				'BARANG' => $query[$key]->BARANG,
				'HARGA' => $query[$key]->HARGA,
				'QTY_BELI' => $query[$key]->QTY_BELI,
				'TG_BELI' => $query[$key]->TG_BELI,
				'STOCKR' => $query[$key]->STOCKR,
				'QTY' => $query[$key]->QTY,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}

}
