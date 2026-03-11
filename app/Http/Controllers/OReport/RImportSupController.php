<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
// ganti 1
use Carbon\Carbon;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use App\Imports\SupImport;
use Maatwebsite\Excel\Facades\Excel;


// ganti 2
class RImportSupController extends Controller
{
    
	 public function importc()
    {

        return view('freport_import_sup.report');
		
    }
	
	
	public function ImportSupProses(Request $request)
    {
		// menangkap file excel
		$file = $request->file('file');
 
		// membuat nama file unik
	//	$nama_file = rand().$file->getClientOriginalName();
 
		// upload ke folder file_siswa di dalam folder public
	//	$file->move('file',$nama_file);
 
		// import data
	//	Excel::import(new CustImport, public_path('/file/'.$nama_file));
 
      
	
         Excel::import(new SupImport, $request->file('file'));
		
		

        return redirect()->back()->with('success', 'File imported successfully!');
    }
	
	
	
	
}