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

class RODCBelumDilayaniController extends Controller
{
    public function report()
    {
        $per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        session()->put('filter_periode', '');

		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', '');
        session()->put('filter_tglDari', date("d-m-Y"));
        session()->put('filter_tglSampai', date("d-m-Y"));
        return view('oreport_rodc_belumlayan.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperRODCBelumLayaniReport(Request $request) 
	{
		$file 	= 'Laporan_Barang_Belum_Dilayani';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
		$periode = $request->per;

        $bulan = substr($periode,0,2);
        $tahun = substr($periode,3,4);
			
		if (!empty($request->kodes1) && !empty($request->kodes2))
        {
            $filterkodes = " AND KODES >= '".$request->kodes1."' AND KODES <= '".$request->kodes2."' ";
        }

        if (!empty($request->tglDr) && !empty($request->tglSmp))
        {
            $tglDrD = date("Y-m-d", strtotime($request->tglDr));
            $tglSmpD = date("Y-m-d", strtotime($request->tglSmp));
            $filtertgl = " AND TGL >= '".$tglDrD."' AND TGL <= '".$tglSmpD."' ";
        }

        session()->put('filter_kodes1', $request->kodes1);
        session()->put('filter_kodes2', $request->kodes2);
        session()->put('filter_tglDari', $request->tglDr);
        session()->put('filter_tglSampai', $request->tglSmp);
        session()->put('filter_periode', $request->per);
		

		$query = DB::SELECT("SELECT trim(NO_BUKTI) as NO_BUKTI, TGL, JTEMPO, KODES, NAMAS,
										TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
							FROM beli
							WHERE FLAG = 'BL' AND PER = '$periode' $filtertgl $filterkodes
                            ORDER BY NO_BUKTI ASC");


		if($request->has('filter'))
		{
			$per = DB::SELECT("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			return view('oreport_rodc_belumlayan.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		
        $data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}