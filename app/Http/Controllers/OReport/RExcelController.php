<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
use App\Models\Master\Perid;

 
use Session;

use App\Imports\ExcelImport;
use Maatwebsite\Excel\Facades\Excel;


use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables; 
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RExcelController extends Controller
{
	
   public function report()
    {
		
		$per = Perid::query()->get();
		session()->put('filter_per', '');

        return view('oreport_excel.report')->with(['per' => $per])->with(['hasil' => []]);
    }
	
	public function import_excel(Request $request) 
	{

        if($request['perio'])
		{
			$periode = $request['perio'];
		}

        DB::SELECT("delete from sup ;");

		// validasi
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
 
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
		$nama_file = rand().$file->getClientOriginalName();
 
		// upload ke folder file_siswa di dalam folder public
		$file->move('file_excel',$nama_file);
 
		// import data
		Excel::import(new ExcelImport, public_path('/file_excel/'.$nama_file));
 
		// notifikasi dengan session
		Session::flash('sukses','Data Supplier Berhasil Diimport!');
 
		// alihkan halaman kembali

    

        DB::SELECT("update sup, supd set supd.id = sup.no_id where sup.kodes = supd.kodes ;");

        // DB::SELECT("update pegawaid set pot13=0 where per = '$periode'  ;");


        // DB::SELECT("update pegawaid a,sup b set a.pot13=b.hutang+b.baznas+b.sptp where a.noinduk=b.noinduk and  a.per = '$periode' and b.per='$periode' ;");

        return view('dashboard')->with('status', 'Proses selesai..');

	}
   	
}
