<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Tagi;
use App\Models\OTransaksi\TagiDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class TagiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    var $judul = '';
    var $FLAGZ = '';
	
    function setFlag(Request $request)
    {
        if ( $request->flagz == 'BS' ) {
            $this->judul = "Form Bayar Busana";
        } else if ( $request->flagz == 'MT' ) {
            $this->judul = "Koreksi Stock Mutasi";
        }

        $this->FLAGZ = $request->flagz;

    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_tagi.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
	
		
    }
	
		public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
		
        $tagi = DB::SELECT("SELECT distinct PO.NO_BUKTI , PO.KODES, PO.NAMAS, 
		                  PO.ALAMAT, PO.KOTA from tagi, tagid 
                          WHERE PO.NO_BUKTI = POD.NO_BUKTI AND PO.GOL ='$golz' AND CBG = '$CBG'
                          AND POD.SISA > 0	");
        return response()->json($tagi);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;
		
		$tagi = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR, 
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from tagi
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($tagi);
    }


	public function index_posting(Request $request)
    {
 
        return view('otransaksi_tagi.post');
    }
	  
	//SHELVI
	
	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$tagid = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from tagid a, brg b 
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($tagid);
	}


    public function getTagi(Request $request)
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
		
        $tagi = DB::SELECT("SELECT no_bukti,NO_TRANS, no_kasir, tgl, penagih, notes, total, 
                                    posted, usrnm,namas,klb,PRNT,ppn
                            FROM tagibsn
                            where PER = '$periode' and type='$TYPE'  order by NO_BUKTI ");

        // ganti 6

        return Datatables::of($tagi)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("tagi/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="tagi/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';					
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

        $query = DB::table('tagi')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'BS')->where('CBG', $CBG)
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($query != '[]') {
            $query = substr($query[0]->NO_BUKTI, -4);
            $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = 'BS' . $CBG . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = 'BS' . $CBG . $tahun . $bulan . '-0001';
        }		

        $tagi = Tagi::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'FLAG'             => 'BS',
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'CBG'              => $CBG,				
                'TYPE'             => ($request['TYPE']==null) ? "" : $request['TYPE'],
                'NOTRANS'          => ($request['NOTRANS']==null) ? "" : $request['NOTRANS'],
                'NOREK'            => ($request['NOREK']==null) ? "" : $request['NOREK'],
                'NMBANK'           => ($request['NMBANK']==null) ? "" : $request['NMBANK'],
                'TGTF'             => date('Y-m-d', strtotime($request['TGTF'])),
                'KOTA'             => ($request['KOTA']==null) ? "" : $request['KOTA'],
                'ALAMAT'           => ($request['ALAMAT']==null) ? "" : $request['ALAMAT'],
                'RETUR'            => (float) str_replace(',', '', $request['TRETUR']),
                'TOTALX'           => (float) str_replace(',', '', $request['TTOTALX']),
                'BLAIN'            => (float) str_replace(',', '', $request['TBLAIN']),
                'LAIN'             => (float) str_replace(',', '', $request['TLAIN']),
                'BADM'             => (float) str_replace(',', '', $request['TBADM']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'REFUND'           => (float) str_replace(',', '', $request['TREFUND']),
                'PROMOS'           => (float) str_replace(',', '', $request['TPROMOS']),
				'created_by'       => Auth::user()->username,

                'KODES'            => ($request['KODES']==null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS']==null) ? "" : $request['NAMAS'],
                'GOLONGAN'         => ($request['GOLONGAN']==null) ? "" : $request['GOLONGAN'],
                'POSTED'           => ($request['POSTED']==null) ? "" : $request['POSTED'],
                'CARA'             => ($request['CARA']==null) ? "" : $request['CARA'],
                'EMAIL'            => ($request['EMAIL']==null) ? "" : $request['EMAIL'],
                'KLB'              => ($request['KLB']==null) ? "" : $request['KLB'],
                'ANB'              => ($request['ANB']==null) ? "" : $request['ANB'],
                
            ]
        );


		$REC        = $request->input('REC');
		$NO_TRM	    = $request->input('NO_TRM');
		$TGL_TRM	= $request->input('TGL_TRM');
		$TOTAL	    = $request->input('TOTAL');
		$PPN	    = $request->input('PPN');
		$TOTALX	    = $request->input('TOTALX');
		$NO_SP	    = $request->input('NO_SP');
		$ACNO	    = $request->input('ACNO');
		$NACNO	    = $request->input('NACNO');
		$KET	    = $request->input('KET');
		$CNT	    = $request->input('CNT');
		$ST_PJK	    = $request->input('ST_PJK');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new TagiDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;	
				$detail->NO_TRM	     = ($NO_TRM[$key]==null) ? "" :  $NO_TRM[$key];
                $detail->TGL_TRM     = date('Y-m-d', strtotime($TGL_TRM[$key]));
				$detail->TOTAL	     = (float) str_replace(',', '', $TOTAL[$key]);
				$detail->PPN	     = (float) str_replace(',', '', $PPN[$key]);
				$detail->TOTALX	     = (float) str_replace(',', '', $TOTALX[$key]);
				$detail->NO_SP	     = ($NO_SP[$key]==null) ? "" :  $NO_SP[$key];						
				$detail->ACNO	     = ($ACNO[$key]==null) ? "" :  $ACNO[$key];						
				$detail->NACNO	     = ($NACNO[$key]==null) ? "" :  $NACNO[$key];						
				$detail->KET	     = ($KET[$key]==null) ? "" :  $KET[$key];						
				$detail->CNT	     = ($CNT[$key]==null) ? "" :  $CNT[$key];						
				$detail->ST_PJK	     = ($ST_PJK[$key]==null) ? "" :  $ST_PJK[$key];						
                $detail->save();
            }
        }	
		
		$no_buktix = $no_bukti;
		
		$tagi = Tagi::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE tagi,  tagid
                            SET  tagid.ID =  tagi.NO_ID  WHERE  tagi.NO_BUKTI =  tagid.NO_BUKTI 
							AND  tagi.NO_BUKTI='$no_buktix';");

		
					 
        // return redirect('/tagi/edit/?idx=' . $tagi->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/tagi?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Tagi $tagi)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/tagi')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from tagi
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from tagi
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from tagi     
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from tagi    
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from tagi
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
			$tagi = Tagi::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$tagi = new Tagi;
                $tagi->TGL = Carbon::now();
				
				
		 }

        $no_bukti = $tagi->NO_BUKTI;
        $tagiDetail = DB::table('tagid')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $tagi,
			'detail'        => $tagiDetail

        ];
 
 		$sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM sup 
		                 ORDER BY NAMAS ASC" );
		
         
         return view('otransaksi_tagi.edit', $data)->with(['sup' => $sup])
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

    public function update(Request $request, Tagi $tagi)
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


        $tagi->update(
            [
                
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'FLAG'             => 'BS',
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
				'updated_by'       => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'CBG'              => $CBG,				
                'TYPE'             => ($request['TYPE']==null) ? "" : $request['TYPE'],
                'NOTRANS'          => ($request['NOTRANS']==null) ? "" : $request['NOTRANS'],
                'NOREK'            => ($request['NOREK']==null) ? "" : $request['NOREK'],
                'NMBANK'           => ($request['NMBANK']==null) ? "" : $request['NMBANK'],
                'TGTF'             => date('Y-m-d', strtotime($request['TGTF'])),
                'KOTA'             => ($request['KOTA']==null) ? "" : $request['KOTA'],
                'ALAMAT'           => ($request['ALAMAT']==null) ? "" : $request['ALAMAT'],
                'RETUR'            => (float) str_replace(',', '', $request['TRETUR']),
                'TOTALX'           => (float) str_replace(',', '', $request['TTOTALX']),
                'BLAIN'            => (float) str_replace(',', '', $request['TBLAIN']),
                'LAIN'             => (float) str_replace(',', '', $request['TLAIN']),
                'BADM'             => (float) str_replace(',', '', $request['TBADM']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'REFUND'           => (float) str_replace(',', '', $request['TREFUND']),
                'PROMOS'           => (float) str_replace(',', '', $request['TPROMOS']),

                'KODES'            => ($request['KODES']==null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS']==null) ? "" : $request['NAMAS'],
                'GOLONGAN'         => ($request['GOLONGAN']==null) ? "" : $request['GOLONGAN'],
                'POSTED'           => ($request['POSTED']==null) ? "" : $request['POSTED'],
                'CARA'             => ($request['CARA']==null) ? "" : $request['CARA'],
                'EMAIL'            => ($request['EMAIL']==null) ? "" : $request['EMAIL'],
                'KLB'              => ($request['KLB']==null) ? "" : $request['KLB'],
                'ANB'              => ($request['ANB']==null) ? "" : $request['ANB'],
            ]
        );

		$no_buktix = $tagi->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC        = $request->input('REC');

		$NO_TRM	    = $request->input('NO_TRM');
		$TGL_TRM	= $request->input('TGL_TRM');
		$TOTAL	    = $request->input('TOTAL');
		$PPN	    = $request->input('PPN');
		$TOTALX	    = $request->input('TOTALX');
		$NO_SP	    = $request->input('NO_SP');
		$ACNO	    = $request->input('ACNO');
		$NACNO	    = $request->input('NACNO');
		$KET	    = $request->input('KET');
		$CNT	    = $request->input('CNT');
		$ST_PJK	    = $request->input('ST_PJK');

        $query = DB::table('tagid')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = TagiDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'NO_TRM'     => ($NO_TRM[$i]==null) ? "" :  $NO_TRM[$i],
                        'TGL_TRM'    => ($TGL_TRM[$i] != '') ? date("Y-m-d", strtotime($TGL_TRM[$i])) : "",
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'PPN'        => (float) str_replace(',', '', $PPN[$i]),
                        'TOTALX'     => (float) str_replace(',', '', $TOTALX[$i]),
                        'NO_SP'      => ($NO_SP[$i]==null) ? "" : $NO_SP[$i],	
                        'ACNO'       => ($ACNO[$i]==null) ? "" : $ACNO[$i],
						'NACNO'      => ($NACNO[$i]==null) ? "" : $NACNO[$i],
						'KET'     	 => ($KET[$i]==null) ? "" : $KET[$i],
						'CNT'     	 => ($CNT[$i]==null) ? "" : $CNT[$i],
						'ST_PJK'     => ($ST_PJK[$i]==null) ? "" : $ST_PJK[$i],
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = TagiDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'NO_TRM'     => ($NO_TRM[$i]==null) ? "" :  $NO_TRM[$i],
                        'TGL_TRM'    => ($TGL_TRM[$i] != '') ? date("Y-m-d", strtotime($TGL_TRM[$i])) : "",
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'PPN'        => (float) str_replace(',', '', $PPN[$i]),
                        'TOTALX'     => (float) str_replace(',', '', $TOTALX[$i]),
                        'NO_SP'      => ($NO_SP[$i]==null) ? "" : $NO_SP[$i],	
                        'ACNO'       => ($ACNO[$i]==null) ? "" : $ACNO[$i],
						'NACNO'      => ($NACNO[$i]==null) ? "" : $NACNO[$i],
						'KET'     	 => ($KET[$i]==null) ? "" : $KET[$i],
						'CNT'     	 => ($CNT[$i]==null) ? "" : $CNT[$i],
						'ST_PJK'     => ($ST_PJK[$i]==null) ? "" : $ST_PJK[$i],
                        'FLAG'       => $this->FLAGZ,
                        'PER'        => $periode,					
                    ]
                );
            }
        }

 		$tagi = Tagi::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $tagi->NO_BUKTI;

        DB::SELECT("UPDATE tagi,  tagid
                    SET  tagid.ID =  tagi.NO_ID  WHERE  tagi.NO_BUKTI =  tagid.NO_BUKTI 
                    AND  tagi.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/tagi/edit/?idx=' . $tagi->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');	
        return redirect('/tagi?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Tagi $tagi)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('tagi')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        }
		
        $deleteTagi = Tagi::find($tagi->NO_ID);

        $deleteTagi->delete();

       return redirect('/tagi?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$tagi->NO_BUKTI.' berhasil dihapus');


    }
    
    public function cetak(Tagi $tagi)
    {
        $no_tagi = $tagi->NO_BUKTI;

        $file     = 'tagic';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT NO_BUKTI,  TGL, KODES, NAMAS, TOTAL_QTY, NOTES, TOTAL, ALAMAT, KOTA
			FROM tagi 
			WHERE tagi.NO_BUKTI='$no_tagi' 
			ORDER BY NO_BUKTI;
		");

        $xno_tagi1       = $query[0]->NO_BUKTI;
        $xtgl1         = $query[0]->TGL;
        $xkodes1       = $query[0]->KODES;
        $xnamas1       = $query[0]->NAMAS;
        $xtotal1       = $query[0]->TOTAL_QTY;
        $xnotes1       = $query[0]->NOTES;
        $xharga1       = $query[0]->TOTAL;
        $xalamat1      = $query[0]->ALAMAT;
        $xkota1        = $query[0]->KOTA;
        
        $PHPJasperXML->arrayParameter = array("HARGA1" => (float) $xharga1, "TOTAL1" => (float) $xtotal1, "NO_PO1" => (string) $xno_tagi1,
                                     "TGL1" => (string) $xtgl1,  "KODES1" => (string) $xkodes1,  "NAMAS1" => (string) $xnamas1, "NOTES1" => (string) $xnotes1, "ALAMAT1" => (string) $xalamat1, "KOTA1" => (string) $xkota1 );
        $PHPJasperXML->arraysqltable = array();


        $query2 = DB::SELECT("
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT, NO_PO,  IF ( FLAG='BL' , 'A','B' ) AS FLAG, AJU, BL, EMKL, KD_BRG, NA_BRG, KG, RPHARGA AS HARGA, RPTOTAL AS TOTAL, 0 AS BAYAR,  NOTES
			FROM beli 
			WHERE beli.NO_PO='$no_tagi'  UNION ALL 
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT,  NO_PO,  'C' AS FLAG, '' AS AJU, '' AS BL, '' AS EMKL,  '' AS KD_BRG, '' AS NA_BRG, 0 AS KG, 
			0 AS HARGA, 0 AS TOTAL, BAYAR, NOTES
			FROM hut 
			WHERE hut.NO_PO='$no_tagi' 
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
	
	
	public function getDetailTagi(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('tagid')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
	
}
