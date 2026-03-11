<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Perid;

use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RBgroupController extends Controller
{

   	public function report(Request $request)
    {
		$tglx1 = '01-'.$request->session()->get('periode')['bulan'].'-'.$request->session()->get('periode')['tahun'] ;
        $d1 = '31';
		
		$bulan = $request->session()->get('periode')['bulan'];
		$tahun = $request->session()->get('periode')['tahun'];

		
		if ( $bulan=='04'  OR  $bulan=='06'  OR  $bulan=='09'  OR  $bulan=='11'  )
		{
			$d1 = '30';
		}
		
		if ( $bulan=='02' )
		{	
			if ( fmod($tahun,4) == 0 )
				$d1 = '29';
			else
				$d1 = '28';
		
        }

		$tglx2 = $d1.'-'.$request->session()->get('periode')['bulan'].'-'.$request->session()->get('periode')['tahun'] ;

		session()->put('filter_divisi', 'SP1');
		session()->put('filter_tgl1', $tglx1);

		// $query1 = DB::SELECT("SELECT 
		// 					(SELECT SUM(QTY) AS QTY FROM belid WHERE FLAG2='BH' AND PER='01/2023' GROUP BY PER) AS KEMARIN,
		// 					(SELECT SUM(QTY) AS QTY FROM belid WHERE FLAG2='BH' AND PER='02/2023' GROUP BY PER) AS SEKARANG");
		
        // return view('oreport_bahan.report')->with(['kodes' => $kodes])->with(['per' => $per])->with(['hasil' => []]);
        // return view('oreport_bgroup.report')->with(['hasil' => []])->with(['card1' => $query1]);
        return view('oreport_bgroup.report')->with(['hasil' => []]);
    }
	

	public function jasperBgroupReport(Request $request) 
	{
		$file 	= 'Laporan_LPB_Harian';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		$tgl1 = date("Y-m-d", strtotime($request->TGL1));

		$query = DB::SELECT("SELECT MONTH(beli.TGL) AS urut,CONCAT(YEAR(beli.TGL),'-',DATE_FORMAT(beli.TGL,'%M')) AS paymentDate, SUM(belid.QTY) AS amount,
								YEAR(beli.TGL) AS `year`,MONTH(beli.TGL) AS `month`,DATE_FORMAT(beli.TGL,'%M') AS NAMA
								FROM beli,belid WHERE beli.NO_BUKTI=belid.NO_BUKTI GROUP BY paymentDate ORDER BY urut,paymentDate asc");

		session()->put('filter_divisi', $request->DIVISI);
		session()->put('filter_tgl1', $request->TGL1);

		
		if($request->has('filter'))
		{
			return view('oreport_bgroup.report')->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				"BARANG" => $query[$key]->BARANG,
				"SATUAN" => $query[$key]->SATUAN,
				"QTY" => $query[$key]->QTY,
				"NO_BUKTI" => $query[$key]->NO_BUKTI,
				"PER" => $query[$key]->PER,
				"TGL" => $query[$key]->TGL,
				"NO_PO" => $query[$key]->NO_PO,
				"NO_PP" => $query[$key]->NO_PP,
				"REC" => $query[$key]->REC,

			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
