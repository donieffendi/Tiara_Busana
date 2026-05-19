<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use App\Models\Master\Perid;

use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;
use PhpParser\Node\Stmt\Foreach_;

class HpsHrgBeliController extends Controller
{
	
   public function index()
    {
	
        return view('otransaksi_hps_hrgBeli.index');
    }
	
   
	public function jasperVbrgReport(Request $request) 
	{
		$file 	= 'vbrgdw_hps';
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

			return view('otransaksi_ubbrgdw.report')->with(['per' => $per])->with(['cbg' => $cbg])->with(['hasil' => $query]);
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
    public function store(Request $request)
    {
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);
        $kodes  = $request->input('KODES');
        $cbg = Auth::user()->CBG;

        $NO_BUKTI = $request->NO_BUKTI;
        $existsNoBukti = DB::table('vbrgdw')
            ->where('NO_BUKTI', $NO_BUKTI)
            ->exists();
        
        $query = DB::table('vbrgdw')->select('NO_BUKTI')->where('NO_BUKTI', 'like', '#' . $cbg . $tahun . $bulan . '%')
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if($existsNoBukti) {
            $no_bukti = $request->NO_BUKTI;
        } else {
            if ($query != '[]') {
                $query = substr($query[0]->NO_BUKTI, -4);
                $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                $no_bukti = '#' . $cbg .  $tahun . $bulan . '-' . $query;
            } else {
                $no_bukti = '#' . $cbg .  $tahun . $bulan . '-0001';
            }
        }
        
        $REC = $request->input('REC');
        $KD_BRG     = $request->input('KD_BRG');
        $HARGALAMA        = $request->input('HARGALAMA');
        $DISCLAMA        = $request->input('DISCLAMA');
        $DISCLAMA2        = $request->input('DISCLAMA2');
        $DISCLAMA3        = $request->input('DISCLAMA3');
        $DISCLAMA4        = $request->input('DISCLAMA4');
        $PPNLAMA        = $request->input('PPNLAMA');
        $HARGA        = $request->input('HARGA');
        $DISC        = $request->input('DISC');
        $DISC2        = $request->input('DISC2');
        $DISC3        = $request->input('DISC3');
        $DISC4        = $request->input('DISC4');
        $PPN       = $request->input('PPN');

// dd($HARGALAMA);
        if ($REC) {
            foreach ($REC as $key => $value) {
                DB::table('vbrgdw')
                    ->where('KODES', $kodes)
                    ->where('KD_BRG', $KD_BRG[$key])
                    ->update([
                        'NO_BUKTI'  => $no_bukti,
                        'HARGALAMA' => (float) str_replace(',', '', $HARGALAMA[$key] ?? 0),
                        'DISCLAMA'  => (float) str_replace(',', '', $DISCLAMA[$key] ?? 0),
                        'DISCLAMA2' => (float) str_replace(',', '', $DISCLAMA2[$key] ?? 0),
                        'DISCLAMA3' => (float) str_replace(',', '', $DISCLAMA3[$key] ?? 0),
                        'DISCLAMA4' => (float) str_replace(',', '', $DISCLAMA4[$key] ?? 0),
                        'PPNLAMA'   => (float) str_replace(',', '', $PPNLAMA[$key] ?? 0),
                        'HARGA'     => (float) str_replace(',', '', $HARGA[$key] ?? 0),
                        'DISC'      => (float) str_replace(',', '', $DISC[$key] ?? 0),
                        'DISC2'     => (float) str_replace(',', '', $DISC2[$key] ?? 0),
                        'DISC3'     => (float) str_replace(',', '', $DISC3[$key] ?? 0),
                        'DISC4'     => (float) str_replace(',', '', $DISC4[$key] ?? 0),
                        'PPN'       => (float) str_replace(',', '', $PPN[$key] ?? 0),
                        
                    ]);
            }
        }

        // return view('otransaksi_hps_hrgBeli.index');
        return redirect('/hps_hrgBeli')
            ->with('statusInsert', 'Data berhasil disimpan. No Bukti: ' . $NO_BUKTI);

    }
    
	public function browse(Request $request)
    {
        $periode = $request->get('periode');

        // $query = DB::table('vbrgdw')
        //     ->select(('*'))
        //     ->where('vbrgdw.NO_BUKTI', 'LIKE', '#%')
        //     ->get();

        $query = DB::SELECT("SELECT * FROM vbrgdw WHERE NO_BUKTI LIKE '#%' ");

        /** @var \stdClass $row */
        // foreach ($query as $row) {
        //     $row->POSTED = DB::table('vbrgdw')
        //         ->where('NO_BUKTI', $row->NO_BUKTI)->limit(1)
        //         ->value('POSTED');
        // }

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
                                <a class="dropdown-item"' . ($row->POSTED  == 1 ? '  onclick= "alert(\'Usulan ' . $row->NO_BUKTI . ' Sudah diselesaikan!\')" href="#" ' : ' href="hps_hrgBeli/edit/?idx=' . $row->NO_ID . '&tipx=edit"') . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="' . url('hps_hrgBeli/cetak/' . $row->NO_ID) . '">
									<i class="fa fa-print" aria-hidden="true"></i> Print Usulan
								</a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="' . url('hps_hrgBeli/cetak/' . $row->NO_ID) . '?tipe=pengesahan">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print Pengesahan
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

			
            ->rawColumns(['action'])
            ->make(true);
    }

    public function cetak($id, Request $request)
    {
        $tipe = $request->get('tipe');

        $no_vbrgdw = $id;

        if($tipe){
            $file     = 'hps-vbrgdw-l';
        }else{
            $file     = 'hps-vbrgdw-u';

        }
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        //pp.GUDANG setelah pp.NETT dihapus
        $query = DB::SELECT("SELECT vbrgdw.*,
                                    vbrg.KET_UK, vbrg.KET_KEM, VBRG.KLK, VBRG.MO1
                            FROM vbrgdw, vbrg
                            WHERE vbrgdw.NO_ID='$no_vbrgdw' AND vbrgdw.KD_BRG=vbrg.KD_BRG
                            ;
            
		");


        $data = [];
        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI'    => $query[0]->NO_BUKTI,
                // 'TGL'         => date('d/m/Y', strtotime($query[0]->TGL)),
                'TGL_NOW'         => now()->format('d/m/Y'),
                'KODES'       => $query[0]->KODES,
                'NAMAS'       => $query[0]->NAMAS,
                'KD_BRG'      => $query[$key]->KD_BRG,
                'NA_BRG'      => $query[$key]->NA_BRG,
                'KET'         => $query[$key]->KET == null ? '-' : $query[$key]->KET,
                'HARGA'       => $query[$key]->HARGA,
                'HARGALAMA'   => $query[$key]->HARGALAMA,
                'DISC'        => $query[$key]->DISC,
                'DISC2'       => $query[$key]->DISC2,
                'DISC3'       => $query[$key]->DISC3,
                'DISC4'       => $query[$key]->DISC4,
                'DISCLAMA'    => $query[$key]->DISCLAMA,
                'DISCLAMA2'   => $query[$key]->DISCLAMA2,
                'DISCLAMA3'   => $query[$key]->DISCLAMA3,
                'DISCLAMA4'   => $query[$key]->DISCLAMA4,
                'KET_UK'      => $query[$key]->KET_UK,
                'KET_KEM'     => $query[$key]->KET_KEM,
                'KLK'         => $query[$key]->KLK,
                'MO'         => $query[$key]->MO1,
                'PPN'         => $query[$key]->PPN,
            ));
        }
        if ($tipe) {
            DB::SELECT("UPDATE vbrgdw SET POSTED = 1 WHERE NO_ID='$no_vbrgdw';");
        }
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
        
        
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

        $details = DB::table('ubbrgdwd')->where('ID', $id)->where('NO_BUKTI', '<>', '')->orderBy('REC')->get();


        return response()->json(['data' => $details]);
    }

    public function edit(Request $request)
    {
        $idx = $request->idx;
        $tipx = $request->tipx;
        
        if ($tipx == 'new') {
            // Create empty object for new record
            $header = (object) [
                'NO_ID' => 0,
                'NO_BUKTI' => '',
                'NO_BELI' => '',
                'TGL' => date('Y-m-d'),
                'KODES' => '',
                'NAMAS' => '',
                'KODEC' => '',
                'NAMAC' => '',
                'PKP' => 0,
                'NOTES' => '',
                'TOTAL_QTY' => 0,
                'KET' => '',
                'USRNM' => auth()->user()->username ?? '',
                'POSTED' => 0
            ];
            $detail = collect(); // Empty collection
        } else {
            
            $data = DB::table('vbrgdw')->where('no_id', $idx)->first();
            $NO_BUKTI = $data->NO_BUKTI;
            $header = DB::table('vbrgdw')->where('NO_BUKTI', $NO_BUKTI)->first();
            $detail = DB::table('vbrgdw')->where('NO_BUKTI', $NO_BUKTI)->get()->toArray();;
        }
        // dd($NO_BUKTI);
        
        return view('otransaksi_hps_hrgBeli.edit', compact('header', 'detail', 'tipx'));
    }
    
    public function browse_kodes(Request $request)
    {
        $results = DB::table('zsup')
            // ->where('POSTED', 1)
            ->select("*")
            ->groupBy('KODES')
            ->orderBy('KODES', 'desc')
            ->get();
            
        return response()->json($results);
    }
    
    public function get_detail_by_kodes(Request $request)
    {
        $kodes = $request->get('kodes');
        // dd($kodes);
        // Get header data
        $header = DB::table('vbrgdw')
            ->where('KODES', $kodes)
            // ->where('POSTED', 1)
            ->first();
        
        if (!$header) {
            return response()->json(['error' => 'Data not found'], 404);
        }
        
        // Get detail data
        $details = DB::table('vbrgdw')
            ->where('KODES', $header->KODES)
            // ->where('HARGA', '')
            ->orderBy('KD_BRG')
            ->get();
            
        return response()->json([
            'header' => $header,
            'details' => $details
        ]);
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

    public function browseBrg(Request $request)
    {
        // dd('hai');

        $kodesx = $request->kodes;
    	$brg = DB::SELECT("SELECT NO_ID, KD_BRG, NA_BRG,HARGA,DISC,DISC2,DISC3,DISC4,PPN
                            FROM vbrgdw WHERE KODES = '$kodesx'
                            ORDER BY KD_BRG ");			
		
        return response()->json($brg);
    }
	
}
