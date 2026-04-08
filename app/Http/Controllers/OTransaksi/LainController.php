<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Beli;
use App\Models\OTransaksi\BeliDetail;
use App\Models\OTransaksi\Belibsnz;
use App\Models\OTransaksi\BelibsnzDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class LainController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resbelinse
     */
    var $judul = '';
    var $FLAGZ = '';
	
    function setFlag(Request $request)
    {
        if ( $request->flagz == 'TL' ) {
            $this->judul = "Transaksi Lain - Lain";
        } else if ( $request->flagz == '' ) {
            $this->judul = "";
        }

        $this->FLAGZ = $request->flagz;

    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_lain.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
	
		
    }
	
		public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
		
        $lain = DB::SELECT("SELECT distinct PO.NO_BUKTI , PO.KODES, PO.NAMAS, 
		                  PO.ALAMAT, PO.KOTA from beli, belid 
                          WHERE PO.NO_BUKTI = POD.NO_BUKTI AND PO.GOL ='$golz' AND CBG = '$CBG'
                          AND POD.SISA > 0	");
        return response()->json($lain);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;
		
		$lain = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR, 
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from beli
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($lain);
    }


	public function index_posting(Request $request)
    {
 
        return view('otransaksi_lain.post');
    }
	  
	//SHELVI
	
	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$laind = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from belid a, brg b 
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($laind);
	}


    public function browse_detail2(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$laind = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X 
                            from belid a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($laind);
	}
    // ganti 4



    public function getLain(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;
		
        $lain = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, total, KODES, NAMAS, usrnm, POSTED, flag, REF 
                            FROM belibsnz 
                            WHERE PER = '$periode' and flag = '$FLAGZ'
                            ORDER BY NO_BUKTI");

                             

        // ganti 6

        return Datatables::of($lain)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("lain/delete/" . $row->NO_ID . "/?flagz=" . $row->flag)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="lain/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul . '"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')" ';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="lain/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a> 									
                                <hr></hr>
                                <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>
   
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    Delete
                                </a> 
                        ';
                } else {
                    $btnPrivilege = '';
                }

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
			
	
			->addColumn('cek', function ($row) {
                return
                    '
                    <input type="checkbox" name="cek[]" class="form-control cek" ' . (($row->POSTED == 1) ? "checked" : "") . '  value="' . $row->NO_ID . '" ' . (($row->POSTED == 2) ? "disabled" : "") . '></input> 				
                    ';
            
            })			
			
            ->rawColumns(['action','cek'])
            ->make(true);
    }


//////////////////////////////////////////////////////////////////////////////////

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resbelinse
     */
    public function store(Request $request)
    {


        $this->validate(
            $request,
            // GANTI 9

            [
                'TGL'      => 'required'

            ]
        );

        //////     nomer otomatis
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('belibsnz')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'LL')->where('CBG', $CBG)
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($query != '[]') {
            $query = substr($query[0]->NO_BUKTI, -4);
            $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = 'LL' . $CBG . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = 'LL' . $CBG . $tahun . $bulan . '-0001';
        }		

        $lain = Belibsnz::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'flag'             => 'LL',			
                'KODES'            => ($request['KODES']==null) ? "" : $request['KODES'],				
                'NAMAS'            => ($request['NAMAS']==null) ? "" : $request['NAMAS'],				
                'ACNO'            => ($request['BACNO']==null) ? "" : $request['BACNO'],				
                'NACNO'            => ($request['BNAMA']==null) ? "" : $request['BNAMA'],				
                'total'            => (float) str_replace(',', '', $request['TOTAL']),
                'usrnm'            => Auth::user()->username,
                'tg_smp'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
				'CBG'              => $CBG,
            ]
        );
		
		// $no_buktix = $no_bukti;
		
		// $lain = Beli::where('NO_BUKTI', $no_buktix )->first();

        // DB::SELECT("UPDATE beli,  belid
        //                     SET  belid.ID =  beli.NO_ID  WHERE  beli.NO_BUKTI =  belid.NO_BUKTI 
		// 					AND  beli.NO_BUKTI='$no_buktix';");
					 
        // return redirect('/lain/edit/?idx=' . $lain->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/lain?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Belibsnz $lain)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/lain')
		// 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }
		
		$this->setFlag($request);
		
        $tipx = $request->tipx;

		$idx = $request->idx;
		
        $CBG = Auth::user()->CBG;
		
		if ( $idx =='0' && $tipx=='undo'  )
	    {
			$tipx ='top';
			
		   }
		   
		 
		   
		if ($tipx=='search') {
			
		   	
    	   $buktix = $request->buktix;
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from Belibsnz
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
						 and NO_BUKTI = '$buktix' AND CBG = '$CBG'					 
		                 ORDER BY NO_BUKTI ASC  LIMIT 1" );
						 
			
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
		
					
		}
		
		if ($tipx=='top') {
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from Belibsnz
		                 where PER ='$per' 
						 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'  
		                 ORDER BY NO_BUKTI ASC  LIMIT 1" );
						 
		
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
		
					
		}
		
		
		if ($tipx=='prev' ) {
			
    	   $buktix = $request->buktix;
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from Belibsnz     
		             where PER ='$per' 
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and NO_BUKTI < 
					 '$buktix' ORDER BY NO_BUKTI DESC LIMIT 1" );
			

			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = $idx; 
			  }
			  
		}
		
		
		if ($tipx=='next' ) {
			
				
      	   $buktix = $request->buktix;
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from Belibsnz    
		             where PER ='$per'  
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and NO_BUKTI > 
					 '$buktix' ORDER BY NO_BUKTI ASC LIMIT 1" );
					 
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = $idx; 
			  }
			  
			
		}

		if ($tipx=='bottom') {
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from Belibsnz
						where PER ='$per'
						and FLAG ='$this->FLAGZ' AND CBG = '$CBG'  
		              ORDER BY NO_BUKTI DESC  LIMIT 1" );
					 
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
			  
			
		}

        
		if ( $tipx=='undo' || $tipx=='search' )
	    {
        
			$tipx ='edit';
			
		   }
		
		

       	if ( $idx != 0 ) 
		{
			$lain = Belibsnz::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$lain = new Belibsnz;
                $lain->TGL = Carbon::now();
				
				
		 }

        $no_bukti = $lain->NO_BUKTI;
        $lainDetail = DB::table('belibsnzd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $lain,
			'detail'        => $lainDetail

        ];
		
         
         return view('otransaksi_lain.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'judul'=> $this->judul ]);
			 

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 18

    public function update(Request $request, Belibsnz $lain)
    {

        $this->validate(
            $request,
            [

                'TGL'      => 'required'
            ]
        );

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];


        $lain->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'flag'             => 'LL',			
                'KODES'            => ($request['KODES']==null) ? "" : $request['KODES'],				
                'NAMAS'            => ($request['NAMAS']==null) ? "" : $request['NAMAS'],				
                'ACNO'            => ($request['BACNO']==null) ? "" : $request['BACNO'],				
                'NACNO'            => ($request['BNAMA']==null) ? "" : $request['BNAMA'],				
                'total'            => (float) str_replace(',', '', $request['TOTAL']),
                'usrnm'            => Auth::user()->username,
                'tg_smp'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
				'CBG'              => $CBG,
            ]
        );

		

 		// $lain = Beli::where('NO_BUKTI', $no_buktix )->first();

        // $no_bukti = $lain->NO_BUKTI;

        // DB::SELECT("UPDATE beli,  belid
        //             SET  belid.ID =  beli.NO_ID  WHERE  beli.NO_BUKTI =  belid.NO_BUKTI 
        //             AND  beli.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/lain/edit/?idx=' . $lain->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');	
        return redirect('/lain?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Beli $lain)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('lain')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        }
		
        $deleteLain = Beli::find($lain->NO_ID);

        $deleteLain->delete();

       return redirect('/lain?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$lain->NO_BUKTI.' berhasil dihapus');


    }
    
    public function cetak(Belibsnz $lain)
    {
        $no_lain = $lain->NO_BUKTI;

        $file     = 'transaksi lain - lain';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT NO_BUKTI, TGL, KODES, NAMAS, total, notes
			FROM belibsnz as beli
			WHERE beli.NO_BUKTI='$no_lain' 
			ORDER BY NO_BUKTI;
		");

        $PHPJasperXML->arrayParameter = [
            'TERBILANG' => trim($this->terbilang($query[0]->total)) . " rupiah",
        ];

        $cleanData = json_decode(json_encode($query), true);
        $PHPJasperXML->setData($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
    }
	
	
	
	 public function posting(Request $request)
    {
      

    }
	
	
	public function getDetailLain(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('belid')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	function terbilang($angka)
	{
		$angka = abs($angka);
		$baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

		if ($angka < 12) return " " . $baca[$angka];
		elseif ($angka < 20) return $this->terbilang($angka - 10) . " Belas";
		elseif ($angka < 100) return $this->terbilang(floor($angka / 10)) . " Puluh" . $this->terbilang($angka % 10);
		elseif ($angka < 200) return " seratus" . $this->terbilang($angka - 100);
		elseif ($angka < 1000) return $this->terbilang(floor($angka / 100)) . " Ratus" . $this->terbilang($angka % 100);
		elseif ($angka < 2000) return " seribu" . $this->terbilang($angka - 1000);
		elseif ($angka < 1000000) return $this->terbilang(floor($angka / 1000)) . " Ribu" . $this->terbilang($angka % 1000);
		elseif ($angka < 1000000000) return $this->terbilang(floor($angka / 1000000)) . " Juta" . $this->terbilang($angka % 1000000);
	}
	
	
}
