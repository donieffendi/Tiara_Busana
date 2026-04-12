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

class RCetakbcdController extends Controller
{
    public function report()
    {	
		session()->put('filter_kd', '');
		session()->put('filter_jm', '');

        return view('oreport_cetakbcd.report');
    }
	
	
	 
	public function jasperCetakbcdReport(Request $request) 
	{
		$file 	= 'cetakbcd';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$params = [
			'TGL_CTK' => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;

		$KD = $request->kd;
		$JM = $request->jm;

		session()->put('filter_kd', $KD);
		session()->put('filter_jm', $JM);

		if ($request->filter == 'KODE'){
			$query = DB::SELECT("SELECT BARCODE, CNT, KD_BRG, NA_BRG, HJUAL, JNS, LPAD('$jm',4,'0') as JUM, '$jm' as QTY FROM brgbsn where KD_BRG='$KD'");
		} else {
			$query = DB::SELECT("SELECT * from
									( 
										SELECT A.NO_BUKTI as REF, A.TGL, C.BARCODE, C.CNT, C.KD_BRG, C.NA_BRG,
												C.HJUAL, C.JNS, LPAD(B.QTY,4,'0') as JUM, B.QTY as QTY,B.REC
										from belibsnz A, belibsnzd B,brgbsn C
										where  A.NO_BUKTI='$KD' and A.NO_BUKTI=B.NO_BUKTI and B.KD_BRG=C.KD_BRG
										union all
										SELECT A.NO_BUKTI as REF,A.TGL,C.BARCODE,C.CNT, C.KD_BRG, C.NA_BRG,
												C.HJUAL, C.JNS, LPAD(B.QTY,4,'0') as JUM, B.QTY as QTY,B.REC
										from belibsn A, belibsnd B, brgbsn C
										where A.NO_BUKTI='$KD' and A.NO_BUKTI=B.NO_BUKTI and B.KD_BRG=C.KD_BRG
									) as tt
								order by REC");
		}

		// dd($JM);
		$result = json_decode(json_encode($query), true);

		$data = [];

		foreach ($result as $row) {
			for ($i = 0; $i < (int)$JM; $i++) {
				$data[] = $row;
			}
		}

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
