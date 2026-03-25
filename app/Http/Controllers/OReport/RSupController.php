<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
use App\Models\Master\Perid;
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

class RSupController extends Controller
{

   public function report()
    {
		$per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        session()->put('filter_periode', '');

		session()->put('filter_kodes1', '');
		session()->put('filter_kodes2', '');
        session()->put('filter_tglDari', date("d-m-Y"));
        session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_budget', '');
		
        return view('oreport_sup.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	

	 
	public function jasperSupReport(Request $request) 
	{
		$file 	= 'suppr';
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
        session()->put('filter_budget', $request->budget);
		
		$queryakum = DB::SELECT("SET @tglx:=last_day(concat('$tahun','-','$bulan','-01'));");
		$query = DB::SELECT("
		SELECT '$periode'as PERIOD, supd.KODES, supd.NAMAS, supd.NO_ID, 
		supd.AW$bulan as AW, supd.MA$bulan as MA, 
		supd.KE$bulan as KE, supd.LN$bulan as LN, supd.ak$bulan as AK,
		coalesce(xxx.SATU,0) SATU, coalesce(xxx.DUA,0) DUA, coalesce(xxx.TIGA,0) TIGA,
		coalesce(xxx.SATU,0)+coalesce(xxx.DUA,0)+coalesce(xxx.TIGA,0) as SALDO 
		from sup,supd 
		left join 
		(
		    SELECT KODES, sum(if(DATEDIFF(@tglx,TGL)<30,belix.PER$bulan-belix.PERB$bulan,0)) as SATU,
		    sum(if(DATEDIFF(@tglx,TGL)BETWEEN 30 and 60,belix.PER$bulan-belix.PERB$bulan,0)) as DUA,
		    sum(if(DATEDIFF(@tglx,TGL)>60,belix.PER$bulan-belix.PERB$bulan,0)) as TIGA 
		    from belix 
		    where belix.YER='$tahun' and belix.PER$bulan-belix.PERB$bulan<>0
		    GROUP BY KODES
		) as xxx on supd.KODES=xxx.KODES
		where sup.KODES = supd.KODES  and supd.YER='$tahun'
		order by sup.KODES;
		");

		if($request->has('filter'))
		{
			$per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");

			return view('oreport_sup.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		
		$data = json_decode(json_encode($query), true);

		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
