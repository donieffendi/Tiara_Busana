<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Counter;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class CounterController extends Controller
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
        if ( $request->flagz == 'CT' ) {
            $this->judul = "Conter Pembelian";
        } else if ( $request->flagz == '' ) {
            $this->judul = "";
        }

        $this->FLAGZ = $request->flagz;

    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_counter.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
	
		
    }
	
		public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
		
        $counter = DB::SELECT("SELECT distinct cnt.NO_BUKTI , cnt.KODES, cnt.NAMAS, 
		                  cnt.ALAMAT, cnt.KOTA from cnt
                          WHERE cnt.NO_BUKTI = cntD.NO_BUKTI AND cnt.GOL ='$golz' AND CBG = '$CBG'
                          AND cntD.SISA > 0	");
        return response()->json($counter);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;
		
		$counter = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR, 
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from cnt
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($counter);
    }


	public function index_posting(Request $request)
    {
 
        return view('otransaksi_counter.post');
    }
	  
	


    public function getCounter(Request $request)
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
		
        $counter = DB::SELECT("SELECT no_bukti,tgl,total_qty,notes,type,posted
                                    FROM cnt
                                    where per='$periode' and flag='$FLAGZ' and cbg='$CBG' union all 
                            SELECT no_bukti,tgl,total_qty,notes,type,posted
                                    FROM cntd
                                    where per='$periode' and flag='$FLAGZ' and cbg='$CBG' 
                            order by NO_BUKTI");

        // ganti 6

        return Datatables::of($counter)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("counter/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="counter/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')" ';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="cetak/' . $row->NO_ID . '">
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
                'PER'      => 'required'

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

        $query = DB::table('cnt')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ)->where('CBG', $CBG)
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($query != '[]') {
            $query = substr($query[0]->NO_BUKTI, -4);
            $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = $FLAGZ . $CBG . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = $FLAGZ . $CBG . $tahun . $bulan . '-0001';
        }		

        $counter = Counter::create(
            [
                'NO_BUKTI'     => $no_bukti,
                // 'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'          => ($request['PER']==null) ? "" : $request['PER'],
                'KODES'        => ($request['KODES']==null) ? "" : $request['KODES'],
                'NAMAS'        => ($request['NAMAS']==null) ? "" : $request['NAMAS'],
                'ST_CNT'   => ($request['ST_CNT']==null) ? "" : $request['ST_CNT'],
                'ST_ORD'   => ($request['ST_ORD']==null) ? "" : $request['ST_ORD'],
                'KEL_PT'   => ($request['KEL_PT']==null) ? "" : $request['KEL_PT'],
                'FLAG'         => $FLAGZ,
                'QSB'          => (float) str_replace(',', '', $request['QSB']),
                'NSB'          => (float) str_replace(',', '', $request['NSB']),
                'QOL'          => (float) str_replace(',', '', $request['QOL']),
                'NOL'          => (float) str_replace(',', '', $request['NOL']),
                'TQOL'         => (float) str_replace(',', '', $request['TQOL']),
                'TNOL'         => (float) str_replace(',', '', $request['TNOL']),
                'QBU'         => (float) str_replace(',', '', $request['QBU']),
                'NBU'         => (float) str_replace(',', '', $request['NBU']),
                'QBL'         => (float) str_replace(',', '', $request['QBL']),
                'NBL'         => (float) str_replace(',', '', $request['NBL']),
                'TQBL'         => (float) str_replace(',', '', $request['TQBL']),
                'TNBL'         => (float) str_replace(',', '', $request['TNBL']),
                'USRNM'        => Auth::user()->username,
                'TG_SMP'       => Carbon::now(),
				'created_by'   => Auth::user()->username,
				'CBG'          => $CBG,
            ]
        );

		
		// $no_buktix = $no_bukti;
		
		// $counter = Counter::where('NO_BUKTI', $no_buktix )->first();


        // DB::SELECT("UPDATE cnt,  cntd
        //                     SET  cntd.ID =  cnt.NO_ID  WHERE  cnt.NO_BUKTI =  cntd.NO_BUKTI 
		// 					AND  cnt.NO_BUKTI='$no_buktix';");

		
					 
        // return redirect('/counter/edit/?idx=' . $counter->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/counter?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Counter $counter)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/counter')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from cnt
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from cnt
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from cnt     
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from cnt    
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from cnt
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
			$counter = Counter::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$counter = new Counter;
                $counter->TGL = Carbon::now();
				
				
		 }

        $no_bukti = $counter->NO_BUKTI;
        
		$data = [
            'header'        => $counter

        ];
 
 		$sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM sup 
		                 ORDER BY NAMAS ASC" );
		
         
         return view('otransaksi_counter.edit', $data)->with(['sup' => $sup])
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

    public function update(Request $request, Counter $counter)
    {

        $this->validate(
            $request,
            [

                'PER'      => 'required'
            ]
        );

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];


        $counter->update(
            [

                'NO_BUKTI'     => $no_bukti,
                // 'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'          => ($request['PER']==null) ? "" : $request['PER'],
                'KODES'        => ($request['KODES']==null) ? "" : $request['KODES'],
                'NAMAS'        => ($request['NAMAS']==null) ? "" : $request['NAMAS'],
                'ST_CNT'   => ($request['ST_CNT']==null) ? "" : $request['ST_CNT'],
                'ST_ORD'   => ($request['ST_ORD']==null) ? "" : $request['ST_ORD'],
                'KEL_PT'   => ($request['KEL_PT']==null) ? "" : $request['KEL_PT'],
                'FLAG'         => $FLAGZ,
                'QSB'          => (float) str_replace(',', '', $request['QSB']),
                'NSB'          => (float) str_replace(',', '', $request['NSB']),
                'QOL'          => (float) str_replace(',', '', $request['QOL']),
                'NOL'          => (float) str_replace(',', '', $request['NOL']),
                'TQOL'         => (float) str_replace(',', '', $request['TQOL']),
                'TNOL'         => (float) str_replace(',', '', $request['TNOL']),
                'QBU'         => (float) str_replace(',', '', $request['QBU']),
                'NBU'         => (float) str_replace(',', '', $request['NBU']),
                'QBL'         => (float) str_replace(',', '', $request['QBL']),
                'NBL'         => (float) str_replace(',', '', $request['NBL']),
                'TQBL'         => (float) str_replace(',', '', $request['TQBL']),
                'TNBL'         => (float) str_replace(',', '', $request['TNBL']),
                'USRNM'        => Auth::user()->username,
                'TG_SMP'       => Carbon::now(),
				'CBG'          => $CBG,
				'updated_by'   => Auth::user()->username,
            ]
        );

		
 		// $counter = Counter::where('NO_BUKTI', $no_buktix )->first();

        // $no_bukti = $counter->NO_BUKTI;

        // DB::SELECT("UPDATE cnt,  cntd
        //             SET  cntd.ID =  cnt.NO_ID  WHERE  cnt.NO_BUKTI =  cntd.NO_BUKTI 
        //             AND  cnt.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/counter/edit/?idx=' . $counter->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');	
        return redirect('/counter?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Counter $counter)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('counter')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        }
		
        $deleteCounter = Counter::find($counter->NO_ID);

        $deleteCounter->delete();

       return redirect('/counter?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$counter->NO_BUKTI.' berhasil dihapus');


    }
    
    public function cetak(Counter $counter)
    {
        $no_counter = $counter->NO_BUKTI;

        $file     = 'counterc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT NO_BUKTI,  TGL, KODES, NAMAS, TOTAL_QTY, NOTES, TOTAL, ALAMAT, KOTA
			FROM cnt 
			WHERE cnt.NO_BUKTI='$no_counter' 
			ORDER BY NO_BUKTI;
		");

        $xno_counter1       = $query[0]->NO_BUKTI;
        $xtgl1         = $query[0]->TGL;
        $xkodes1       = $query[0]->KODES;
        $xnamas1       = $query[0]->NAMAS;
        $xtotal1       = $query[0]->TOTAL_QTY;
        $xnotes1       = $query[0]->NOTES;
        $xharga1       = $query[0]->TOTAL;
        $xalamat1      = $query[0]->ALAMAT;
        $xkota1        = $query[0]->KOTA;
        
        $PHPJasperXML->arrayParameter = array("HARGA1" => (float) $xharga1, "TOTAL1" => (float) $xtotal1, "NO_PO1" => (string) $xno_counter1,
                                     "TGL1" => (string) $xtgl1,  "KODES1" => (string) $xkodes1,  "NAMAS1" => (string) $xnamas1, "NOTES1" => (string) $xnotes1, "ALAMAT1" => (string) $xalamat1, "KOTA1" => (string) $xkota1 );
        $PHPJasperXML->arraysqltable = array();


        $query2 = DB::SELECT("
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT, NO_PO,  IF ( FLAG='BL' , 'A','B' ) AS FLAG, AJU, BL, EMKL, KD_BRG, NA_BRG, KG, RPHARGA AS HARGA, RPTOTAL AS TOTAL, 0 AS BAYAR,  NOTES
			FROM beli 
			WHERE beli.NO_PO='$no_counter'  UNION ALL 
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT,  NO_PO,  'C' AS FLAG, '' AS AJU, '' AS BL, '' AS EMKL,  '' AS KD_BRG, '' AS NA_BRG, 0 AS KG, 
			0 AS HARGA, 0 AS TOTAL, BAYAR, NOTES
			FROM hut 
			WHERE hut.NO_PO='$no_counter' 
			ORDER BY TGL, FLAG, NO_BUKTI;
		");

        $data = [];

        foreach ($query2 as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query2[$key]->NO_BUKTI,
                'TGL'      => $query2[$key]->TGL,
                'KODES'    => $query2[$key]->KODES,
                'NAMAS'    => $query2[$key]->NAMAS,
                'ALAMAT'    => $query2[$key]->ALAMAT,
                'AJU'    => $query2[$key]->AJU,
                'BL'       => $query2[$key]->BL,
                'EMKL'    => $query2[$key]->EMKL,
                'KG'       => $query2[$key]->KG,
                'HARGA'    => $query2[$key]->HARGA,
                'TOTAL'    => $query2[$key]->TOTAL,
                'BAYAR'    => $query2[$key]->BAYAR,
                'NOTES'    => $query2[$key]->NOTES
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
    }
	
	
	
	 public function posting(Request $request)
    {
      

    }
	
	
}
