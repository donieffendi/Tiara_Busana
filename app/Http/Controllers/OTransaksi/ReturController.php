<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Retur;
use App\Models\OTransaksi\ReturDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class ReturController extends Controller
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
        if ( $request->flagz == 'KB' ) {
            $this->judul = "Stock Opname";
        } else if ( $request->flagz == 'RO' ) {
            $this->judul = "Retur ke TGZ";
        }

        $this->FLAGZ = $request->flagz;

    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_retur.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
	
		
    }
	
		public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
		
        $retur = DB::SELECT("SELECT distinct PO.NO_BUKTI , PO.KODES, PO.NAMAS, 
		                  PO.ALAMAT, PO.KOTA from retur, returd 
                          WHERE PO.NO_BUKTI = POD.NO_BUKTI AND PO.GOL ='$golz' AND CBG = '$CBG'
                          AND POD.SISA > 0	");
        return resreturnse()->json($retur);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;
		
		$retur = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR, 
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from retur
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($retur);
    }

    
    public function browse_posting(Request $request)
    {
        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $CBG = Auth::user()->CBG;

		$cari = $request->CARI;
		
		if ($cari == ''){
			
            $posting = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NAMAS, TOTAL_QTY, 
                                            NOTES
                                        FROM retur
                                        WHERE NO_BUKTI =''AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
				
               
        } else if ($cari != ''){
			
            $posting = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NAMAS, TOTAL_QTY,
                                            NOTES
                                        FROM retur
                                        WHERE NO_BUKTI = '$cari'AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
        } 

        return response()->json($posting);
    }




	public function post(Request $request)
    {
 
        return view('otransaksi_retur.post');
    }
	  
	//SHELVI
	
	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$returd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from returd a, brg b 
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($returd);
	}


    public function browse_detail2(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$returd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X 
                            from returd a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($returd);
	}
    // ganti 4



    public function getRetur(Request $request)
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
		
        $retur = DB::SELECT("SELECT * FROM bretur where per='$periode' and flag='$FLAGZ' order by NO_BUKTI");

        // ganti 6

        return Datatables::of($retur)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("retur/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="retur/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';					
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

        $query = DB::table('retur')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'KZ')->where('CBG', $CBG)
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($query != '[]') {
            $query = substr($query[0]->NO_BUKTI, -4);
            $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = 'KZ' . $CBG . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = 'KZ' . $CBG . $tahun . $bulan . '-0001';
        }		

        $retur = Retur::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'FLAG'             => 'KZ',
                'CNT'              => ($request['CNT']==null) ? "" : $request['CNT'],				
                'NCNT'             => ($request['NCNT']==null) ? "" : $request['NCNT'],				
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],				
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
				'CBG'              => $CBG,
            ]
        );


		$REC        = $request->input('REC');
		$KD_BRG	    = $request->input('KD_BRG');
		$BARCODE	= $request->input('BARCODE');
		$NA_BRG	    = $request->input('NA_BRG');
		$TGL_CAIR	= $request->input('TGL_CAIR');
		$QTYK	    = $request->input('QTYK');
		$HARGA	    = $request->input('HARGA');
		$QTY	    = $request->input('QTY');
		$KET	    = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new ReturDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;	
				$detail->KD_BRG	     = ($KD_BRG[$key]==null) ? "" :  $KD_BRG[$key];
				$detail->BARCODE	 = ($BARCODE[$key]==null) ? "" :  $BARCODE[$key];
				$detail->NA_BRG	     = ($NA_BRG[$key]==null) ? "" :  $NA_BRG[$key];
				$detail->TGL_CAIR    = date('Y-m-d', strtotime($TGL_CAIR[$key]));
				$detail->QTYK	     = (float) str_replace(',', '', $QTYK[$key]);
				$detail->HARGA	     = (float) str_replace(',', '', $HARGA[$key]);
				$detail->QTY	     = (float) str_replace(',', '', $QTY[$key]);
				$detail->KET	     = ($KET[$key]==null) ? "" :  $KET[$key];						
                $detail->save();
            }
        }	
		
		$no_buktix = $no_bukti;
		
		$retur = Retur::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE retur,  returd
                            SET  returd.ID =  retur.NO_ID  WHERE  retur.NO_BUKTI =  returd.NO_BUKTI 
							AND  retur.NO_BUKTI='$no_buktix';");

		
					 
        // return redirect('/retur/edit/?idx=' . $retur->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/retur?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Retur $retur)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/retur')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from retur
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from retur
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from retur     
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from retur    
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from retur
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
			$retur = Retur::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$retur = new Retur;
                $retur->TGL = Carbon::now();
				
				
		 }

        $no_bukti = $retur->NO_BUKTI;
        $returDetail = DB::table('returd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $retur,
			'detail'        => $returDetail

        ];
 
 		$sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM sup 
		                 ORDER BY NAMAS ASC" );
		
         
         return view('otransaksi_retur.edit', $data)->with(['sup' => $sup])
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

    public function update(Request $request, Retur $retur)
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


        $retur->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'FLAG'             => 'KZ',
                'CNT'              => ($request['CNT']==null) ? "" : $request['CNT'],				
                'NCNT'             => ($request['NCNT']==null) ? "" : $request['NCNT'],				
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],				
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
				'CBG'              => $CBG,
            ]
        );

		$no_buktix = $retur->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC        = $request->input('REC');
		$KD_BRG	    = $request->input('KD_BRG');
		$BARCODE	= $request->input('BARCODE');
		$NA_BRG	    = $request->input('NA_BRG');
		$TGL_CAIR	= $request->input('TGL_CAIR');
		$QTYK	    = $request->input('QTYK');
		$HARGA	    = $request->input('HARGA');
		$QTY	    = $request->input('QTY');
		$KET	    = $request->input('KET');

        $query = DB::table('returd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = ReturDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'KD_BRG'     => ($KD_BRG[$i]==null) ? "" :  $KD_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i]==null) ? "" : $BARCODE[$i],	
                        'NA_BRG'     => ($NA_BRG[$i]==null) ? "" : $NA_BRG[$i],	
                        'TGL_MULAI'  => ($TGL_MULAI[$i] != '') ? date("Y-m-d", strtotime($TGL_MULAI[$i])) : "",
                        'QTYK'       => (float) str_replace(',', '', $QTYK[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
						'QTY'        => (float) str_replace(',', '', $QTY[$i]),	
						'KET'     	 => ($KET[$i]==null) ? "" : $KET[$i],
                        
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = ReturDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KD_BRG'     => ($KD_BRG[$i]==null) ? "" :  $KD_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i]==null) ? "" : $BARCODE[$i],	
                        'NA_BRG'     => ($NA_BRG[$i]==null) ? "" : $NA_BRG[$i],	
                        'TGL_MULAI'  => ($TGL_MULAI[$i] != '') ? date("Y-m-d", strtotime($TGL_MULAI[$i])) : "",
                        'QTYK'       => (float) str_replace(',', '', $QTYK[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
						'QTY'        => (float) str_replace(',', '', $QTY[$i]),	
						'KET'     	 => ($KET[$i]==null) ? "" : $KET[$i],
                        'FLAG'       => $this->FLAGZ,
                        'PER'        => $periode,					
                    ]
                );
            }
        }

 		$retur = Retur::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $retur->NO_BUKTI;

        DB::SELECT("UPDATE retur,  returd
                    SET  returd.ID =  retur.NO_ID  WHERE  retur.NO_BUKTI =  returd.NO_BUKTI 
                    AND  retur.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/retur/edit/?idx=' . $retur->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');	
        return redirect('/retur?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Retur $retur)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('retur')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        }
		
        $deleteRetur = Retur::find($retur->NO_ID);

        $deleteRetur->delete();

       return redirect('/retur?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$retur->NO_BUKTI.' berhasil dihapus');


    }
    
    public function cetak(Retur $retur)
    {
        $no_retur = $retur->NO_BUKTI;

        $file     = 'returc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT NO_BUKTI,  TGL, KODES, NAMAS, TOTAL_QTY, NOTES, TOTAL, ALAMAT, KOTA
			FROM retur 
			WHERE retur.NO_BUKTI='$no_retur' 
			ORDER BY NO_BUKTI;
		");

        $xno_retur1       = $query[0]->NO_BUKTI;
        $xtgl1         = $query[0]->TGL;
        $xkodes1       = $query[0]->KODES;
        $xnamas1       = $query[0]->NAMAS;
        $xtotal1       = $query[0]->TOTAL_QTY;
        $xnotes1       = $query[0]->NOTES;
        $xharga1       = $query[0]->TOTAL;
        $xalamat1      = $query[0]->ALAMAT;
        $xkota1        = $query[0]->KOTA;
        
        $PHPJasperXML->arrayParameter = array("HARGA1" => (float) $xharga1, "TOTAL1" => (float) $xtotal1, "NO_PO1" => (string) $xno_retur1,
                                     "TGL1" => (string) $xtgl1,  "KODES1" => (string) $xkodes1,  "NAMAS1" => (string) $xnamas1, "NOTES1" => (string) $xnotes1, "ALAMAT1" => (string) $xalamat1, "KOTA1" => (string) $xkota1 );
        $PHPJasperXML->arraysqltable = array();


        $query2 = DB::SELECT("
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT, NO_PO,  IF ( FLAG='BL' , 'A','B' ) AS FLAG, AJU, BL, EMKL, KD_BRG, NA_BRG, KG, RPHARGA AS HARGA, RPTOTAL AS TOTAL, 0 AS BAYAR,  NOTES
			FROM beli 
			WHERE beli.NO_PO='$no_retur'  UNION ALL 
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT,  NO_PO,  'C' AS FLAG, '' AS AJU, '' AS BL, '' AS EMKL,  '' AS KD_BRG, '' AS NA_BRG, 0 AS KG, 
			0 AS HARGA, 0 AS TOTAL, BAYAR, NOTES
			FROM hut 
			WHERE hut.NO_PO='$no_retur' 
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
	
	
	
	function posting (Request $request, Retur $retur)
	{

        $REC = $request->input('REC');
		$CEKX = $request->input('CEKX');
        $NO_IDX = $request->input('NO_ID');
        $NO_BUKTIX = $request->input('NO_BUKTI');
        $TGLX = $request->input('TGL');
        $NO_SURATSX = $request->input('NO_SURATS');
        $NAMACX = $request->input('NAMAC');
        $NETTX = $request->input('NETT');
        $NO_FPX = $request->input('NO_FPX');
        $TGL_FPX = $request->input('TGL_FPX');	

        $USRNMX = Auth::user()->USERNAME;
				
        session()->put('posttimer', time());
 
        $hasil = "";
        // ddd($TGL_FPX);
        if ($REC) {
            foreach ($REC as $key => $value) {
				
					$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
					$bulan    = session()->get('periode')['bulan'];
					$tahun    = substr(session()->get('periode')['tahun'], -2);
						
				
				// $NETTXZ = (float) str_replace(',', '', $NETTX[$key]);

				$NO_IDXZ = $NO_IDX[$key];
				
				
				// $HUTHGXZ = $HUTHGX[$key];
				
	
				$CEK11 = $CEKX[$key];
				
				
				// $NO_BUKTIXZ = ($NO_BUKTIX[$key] == null) ? "" :  $NO_BUKTIX[$key];
				// $TGLXZ = ($TGLX[$key] == null) ? "" :  $TGLX[$key];

				// $NO_SURATSXZ = ($NO_SURATSX[$key] == null) ? "" :  $NO_SURATSX[$key];
				// $NAMACXZ = ($NAMACX[$key] == null) ? "" :  $NAMACX[$key];
		
				$NO_FPXZ = ($NO_FPX[$key] == null) ? "" :  $NO_FPX[$key];
				$TGL_FPXZ = ($TGL_FPX[$key] == null) ? "" :  date('Y-m-d', strtotime($TGL_FPX[$key]));
	
				
				if ( $CEK11 == 1 )
			    {
								
                    DB::SELECT("UPDATE jual
                                SET NO_FP = '$NO_FPXZ',
                                    TGL_FP = '$TGL_FPXZ'
                                WHERE NO_ID ='$NO_IDXZ' ");
                
							
				}

					// IF CEK
							
            } // FOR 
			
			
        }
        else
        {
            $hasil = $hasil ."Tidak ada No Bukti yang dipilih! ; ";
        }

		
		return redirect('/retur/post')->with('statusInsert', 'No Bukti berhasil diupdate');		
	
		
		
	}
	
	
	public function getDetailRetur(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('returd')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
	
}
