<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use App\Models\Master\Vbrg;
use App\Models\Master\Perid;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RubbrgdwController extends Controller
{
	
   public function report()
    {
	
        return view('oreport_ubbrgdw.report');
    }
	
   
	public function jasperVbrgReport(Request $request) 
	{
		$file 	= 'vbrgpr';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
		
        if ($request->session()->has('periode')) 
		{
			$periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
		} else
		{
			$periode = '';
		}
		
		if($request['perio'])
		{
			$periode = $request['perio'];
		}
		
		if($request['cbg'])
		{
			$cbg = $request['cbg'];
		}

			
		if (!empty($request->cbg))
		{
			$filtercbg = " and vbrgd.CBG='".$request->cbg."' ";
		}

		if (!empty($request->KD_BRG))
		{
			$filterkode = " and vbrg.KD_BRG='".$request->KD_BRG."' ";
		}
		
		
		session()->put('filter_cbg', $request->cbg);
		session()->put('filter_per', $periode);
		session()->put('filter_kode1', $request->KD_BRG);
		session()->put('filter_nama1', $request->NA_BRG);

		$bulan = substr($periode,0,2);
		$tahun = substr($periode,3,4);
		
		$queryakum = DB::SELECT("SET @akum:=0;");
		$query = DB::SELECT("SELECT vbrg.KD_BRG,vbrg.NA_BRG,vbrgd.AW$bulan as AW, vbrgd.MA$bulan as MA, 
		    vbrgd.KE$bulan as KE,vbrgd.LN$bulan as LN,vbrgd.AK$bulan as AK, 
			vbrgd.HRT$bulan as HRT,vbrgd.NIW$bulan as NIW,vbrgd.NIM$bulan as NIM,vbrgd.NIK$bulan as NIK,
		vbrgd.NIL$bulan as NIL,vbrgd.NIR$bulan as NIR
		FROM vbrg,vbrgd
		WHERE vbrg.KD_BRG=vbrgd.KD_BRG and vbrgd.YER='$tahun'
		$filtercbg $filterkode
		group by KD_BRG
		order by KD_BRG;
		");

		

		if($request->has('filter'))
		{
			$per = Perid::query()->get();
			$cbg = Cbg::groupBy('CBG')->get();

			return view('oreport_ubbrgdw.report')->with(['per' => $per])->with(['cbg' => $cbg])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'KD_BRG' => $query[$key]->KD_BRG,
                // 'KD_BRG'    => "`".strval($query[$key]->KD_BRG),
				'NA_BRG' => $query[$key]->NA_BRG,
				'AW' => $query[$key]->AW,
				'MA' => $query[$key]->MA,
				'KE' => $query[$key]->KE,
				'LN' => $query[$key]->LN,
				'AK' => $query[$key]->AK,
				'HRT' => $query[$key]->HRT,
				'HRT_2' => $query[$key]->HRT_2,
				'NIW' => $query[$key]->NIW,
				'NIM' => $query[$key]->NIM,
				'NIK' => $query[$key]->NIK,
				'NIL' => $query[$key]->NIL,
				'NIR' => $query[$key]->NIR,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}

	public function browse(Request $request)
    {
        $periode = $request->get('periode');
        
        $query = DB::table('ubbrgdw')
            ->select([
				'ubbrgdw.NO_ID',
                'ubbrgdw.NO_BELI',
                'ubbrgdw.TGL',
                'ubbrgdw.KODES',
                'ubbrgdw.NAMAS',
                'ubbrgdw.ALAMAT',
                'ubbrgdw.KOTA',
                'ubbrgdw.KET',
                'ubbrgdw.USRNM',
                'ubbrgdw.POSTED'
            ]);

        // Filter by period if provided
        if ($periode) {
            $period = explode('/', $periode);
            if (count($period) == 2) {
                $month = str_pad($period[0], 2, '0', STR_PAD_LEFT);
                $year = $period[1];
                $query->whereRaw("DATE_FORMAT(TGL, '%m/%Y') = ?", [$month . '/' . $year]);
            }
        }

        return DataTables::of($query)
			->addColumn('action', function ($row) {
					//CEK POSTED di index dan edit

					// url untuk delete di index
					// batas

					// $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BELI . ' sudah diposting!\')" href="#" ' : ' href="po/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
// <a class="dropdown-item" ' . $btnEdit . '>
//                                 <i class="fas fa-edit"></i>
//                                     Edit
//                                 </a>

					$btnPrivilege =
						'
                                
                                <a class="dropdown-item btn btn-danger" ' . (($row->KET  =="") ? '  onclick= "alert(\'Harga untuk Transaksi ' . $row->NO_BUKTI . ' Belum Ditentukan!\')" href="#" ' : ' target="_blank" href="po/cetak/' . $row->NO_ID . '"') . '>
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a>
                                <hr></hr>

                        ';

				$actionBtn =
					'
                    <div class="dropdown show" style="text-align: center">
                        <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">


                            ' . $btnPrivilege . '
                        </div>
                    </div>
                    ';

				return $actionBtn;
			})

			->editColumn('TGL', function ($row) {
                return date('d-m-Y', strtotime($row->TGL));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function browse_detail(Request $request)
    {
        $id = $request->get('id');
        $no_bukti = $request->get('no_bukti');
        
        // Jika menggunakan no_bukti, cari ID dulu
        if ($no_bukti && !$id) {
            $header = DB::table('ubbrgdw')->where('NO_BELI', $no_bukti)->first();
            if ($header) {
                $id = $header->NO_ID;
            }
        }
        
        $details = DB::table('ubbrgdwd')
            ->select([
                'ubbrgdwd.REC',
                'ubbrgdwd.KD_BRG',
                'ubbrgdwd.NA_BRG',
                'ubbrgdwd.QTY',
                'ubbrgdwd.HARGALAMA',
                'ubbrgdwd.HARGA',
                'ubbrgdwd.DISKLAMA',
                'ubbrgdwd.DISK',
                'ubbrgdwd.DISKLAMA2',
                'ubbrgdwd.DISK2',
                'ubbrgdwd.DISKLAMA3',
                'ubbrgdwd.DISK3',
                'ubbrgdwd.DISKLAMA4',
                'ubbrgdwd.DISK4',
                'ubbrgdwd.TOTAL',
                'ubbrgdwd.KET'
            ])
            ->where('ID', $id)
            ->orderBy('REC')
            ->get();

        return response()->json(['data' => $details]);
    }

    public function edit(Request $request)
    {
        $idx = $request->get('idx');
        
        $header = DB::table('ubbrgdw')->where('NO_ID', $idx)->first();
        $details = DB::table('ubbrgdwd')->where('NO_ID', $idx)->orderBy('REC')->get();
        
        return view('oreport_ubbrgdw.edit', compact('header', 'details'));
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        
        try {
            DB::beginTransaction();
            
            // Delete detail records first
            DB::table('ubbrgdwd')->where('NO_ID', $id)->delete();
            
            // Delete header record
            DB::table('ubbrgdw')->where('NO_ID', $id)->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ]);
        }
    }
	
}
