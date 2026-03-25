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

class RRcnorder9Controller extends Controller
{
    public function report()
    {
        return view('oreport_rcnorder9.report')->with(['hasil' => []]);
    }
	
	
	 
	public function jasperRcnorder9Report(Request $request) 
	{
		$file 	= 'Laporan_Order_Barang_Kode9';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
		if ($request->session()->has('periode')) 
		{
			$periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
		} else
		{
			$periode = '';
		}
		
		

		$query = DB::SELECT("SELECT trim(NO_BUKTI) as NO_BUKTI, TGL, JTEMPO, KODES, NAMAS,
										TOTAL_QTY, TOTAL, TDPP AS DPP, TPPN AS PPN, NETT
							FROM beli
							WHERE FLAG = 'BL' AND PER = '$periode'
                            ORDER BY NO_BUKTI ASC");


		if($request->has('filter'))
		{
			return view('oreport_rcnorder9.report')->with(['hasil' => $query]);
		}

		$data=[];
		
        $data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}