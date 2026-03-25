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

class RFakturpjController extends Controller
{
    public function report()
    {
        $per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        session()->put('filter_periode', '');

		$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
		session()->put('filter_cbg', '');

        return view('oreport_fakturpj.report')->with(['per' => $per])->with(['cbg' => $cbg])->with(['hasil' => []]);
    }
	
	
	 
	public function jasperFakturpjReport(Request $request) 
	{
		$file 	= 'Laporan_Barang_Datang';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
		$periode = $request->per;
		$cbg = $request->cbg;

        $bulan = substr($periode,0,2);
        $tahun = substr($periode,3,4);
			
		if(!empty($cbg))
		{
			$filtercbg = " AND beli.CBG = '$cbg'";		
		}

        session()->put('filter_periode', $request->per);
		session()->put('filter_cbg', $request->cbg);
		

		$query = DB::SELECT("SELECT trim(NO_BUKTI) as NO_BUKTI, TGL, JTEMPO, KODES, NAMAS,
										TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
							FROM beli
							WHERE FLAG = 'BL' AND PER = '$periode' $filtercbg
                            ORDER BY NO_BUKTI ASC");


		if($request->has('filter'))
		{
			$per = DB::SELECT("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
			$cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");

			return view('oreport_fakturpj.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		
        $data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}