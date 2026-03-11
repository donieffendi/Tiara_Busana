<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Piu;
use App\Models\OTransaksi\PiuDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

// ganti 2
class PiuController extends Controller
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
        if ( $request->flagz == 'P' ) {
            $this->judul = "Pembayaran piutang";
        } 
		
        $this->FLAGZ = $request->flagz;


    }

    public function index(Request $request)
    {

	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_piu.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ ]);
	
	
    }
	
// ganti 4

    public function getPiu(Request $request)
    {
// ganti 5

	if ($request->session()->has('periode')) 
		{
			$periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
		} else
		{
			$periode = '';
		}

		$this->setFlag($request);	
		
        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $piu = DB::SELECT("SELECT NO_ID, NO_BUKTI, 
                                TGL, KODEC, NAMAC, KOTA, TOTAL, BAYAR, NOTES, FLAG, POSTED, 
                                USRNM from piu 
                           where PER = '$periode' AND CBG = '$CBG' 
                           ORDER BY NO_BUKTI ");
	   	
		
// ganti 6
		
        return Datatables::of($piu)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    if ( Auth::user()->divisi=="programmer" ) 
                    {
                        // url untuk delete di index
                        $url = "'".url("piu/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";
                        // batas

                        $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="piu/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';					
                        $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


 						 												 
                        $btnPrivilege = 
                        '
                                <a class="dropdown-item" '.$btnEdit.'>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>	
                                <a class="dropdown-item btn btn-danger" href="piu/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a> 									
                                <hr></hr>
                                <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>
   
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    Delete
                                </a> 
                        ';
                    } 
                    else
                    {
                        $btnPrivilege = '';
                    }

                    $actionBtn = 
                    '
                    <div class="dropdown show" style="text-align: center">
                        <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-bars"></i>
                        </a>

                        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            

                            '.$btnPrivilege.'
                        </div>
                    </div>
                    ';
                    
                    return $actionBtn;
                })
                ->rawColumns(['action'])
                ->make(true);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        
        $this->validate($request,
// GANTI 9

        [
 
                'TGL'      => 'required',

            ]
        );

//////     nomer otomatis

        $kodecx = $request->KODEC;
        
        $xxx= DB::table('cust')->select('PKP')->where('KODEC', $kodecx)->get();

        $PPN = $xxx[0]->PKP ;
        
        
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;

		
        $periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
		
        $bulan	= session()->get('periode')['bulan'];
		$tahun	= substr(session()->get('periode')['tahun'],-2);

        $query = DB::table('piu')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ )
                ->where('CBG', $CBG )->where('PKP', $PPN )
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if( $PPN == '1'){

            if ($query != '[]') {
                $query = substr($query[0]->NO_BUKTI, -4);
                $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                $no_bukti = 'PY' . $CBG . $tahun . $bulan . '-' . $query;
            } else {
                $no_bukti = 'PY' . $CBG . $tahun . $bulan . '-0001';
            }
        } else {
            if ($query != '[]') {
                $query = substr($query[0]->NO_BUKTI, -4);
                $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                $no_bukti = 'PZ' . $CBG . $tahun . $bulan . '-' . $query;
            } else {
                $no_bukti = 'PZ' . $CBG . $tahun . $bulan . '-0001';
            }
        } 

        /////////////////////////////////////////////////////////////////

        $type1 = substr( $request['BNAMA'],0,3);

        if( $PPN == '1' ){

            if ( $type1 == 'KAS' )
            {          
                        $bulan    = session()->get('periode')['bulan'];
                        $tahun    = substr(session()->get('periode')['tahun'], -2);
                        $query2 = DB::table('kas')->select('NO_BUKTI')->where('PER', $periode)
                                        ->where('TYPE', 'BKM')->where('CBG', $CBG)->where('PKP', $PPN)
                                        ->orderByDesc('NO_BUKTI')->limit(1)->get();
                
                        if ($query2 != '[]') {
                            $query2 = substr($query2[0]->NO_BUKTI, -4);
                            $query2 = str_pad($query2 + 1, 4, 0, STR_PAD_LEFT);
                            $no_bukti2 = 'BKMY' . $CBG . $tahun . $bulan . '-' . $query2;
                        } else {
                            $no_bukti2 = 'BKMY' . $CBG . $tahun . $bulan . '-0001';
                        }
                        
            }
            else
            {
    
                        $bulan    = session()->get('periode')['bulan'];
                        $tahun    = substr(session()->get('periode')['tahun'], -2);
                        $query2 = DB::table('bank')->select('NO_BUKTI')->where('PER', $periode)
                                ->where('TYPE', 'BBM')->where('CBG', $CBG)->where('PKP', $PPN)->orderByDesc('NO_BUKTI')->limit(1)->get();
                
                        if ($query2 != '[]') {
                            $query2 = substr($query2[0]->NO_BUKTI, -4);
                            $query2 = str_pad($query2 + 1, 4, 0, STR_PAD_LEFT);
                            $no_bukti2 = 'BBMY' . $CBG . $tahun . $bulan . '-' . $query2;
                        } else {
                            $no_bukti2 = 'BBMY' . $CBG . $tahun . $bulan . '-0001';
                        }
                        
                
            }

        } else {

            if ( $type1 == 'KAS' )
            {          
                        $bulan    = session()->get('periode')['bulan'];
                        $tahun    = substr(session()->get('periode')['tahun'], -2);
                        $query2 = DB::table('kas')->select('NO_BUKTI')->where('PER', $periode)
                        ->where('TYPE', 'BKM')->where('CBG', $CBG)->where('PKP', $PPN)->orderByDesc('NO_BUKTI')->limit(1)->get();
                
                        if ($query2 != '[]') {
                            $query2 = substr($query2[0]->NO_BUKTI, -4);
                            $query2 = str_pad($query2 + 1, 4, 0, STR_PAD_LEFT);
                            $no_bukti2 = 'BKMZ' . $CBG . $tahun . $bulan . '-' . $query2;
                        } else {
                            $no_bukti2 = 'BKMZ' . $CBG . $tahun . $bulan . '-0001';
                        }
                        
            }
            else
            {
    
                        $bulan    = session()->get('periode')['bulan'];
                        $tahun    = substr(session()->get('periode')['tahun'], -2);
                        $query2 = DB::table('bank')->select('NO_BUKTI')->where('PER', $periode)
                                    ->where('TYPE', 'BBM')->where('CBG', $CBG)->where('PKP', $PPN)->orderByDesc('NO_BUKTI')->limit(1)->get();
                
                        if ($query2 != '[]') {
                            $query2 = substr($query2[0]->NO_BUKTI, -4);
                            $query2 = str_pad($query2 + 1, 4, 0, STR_PAD_LEFT);
                            $no_bukti2 = 'BBMZ' . $CBG . $tahun . $bulan . '-' . $query2;
                        } else {
                            $no_bukti2 = 'BBMZ' . $CBG . $tahun . $bulan . '-0001';
                        }
                        
                
            }

        }
        
    
		
        // Insert Header

// ganti 10
		
        $piu = Piu::create(
            [
                'NO_BUKTI'         => $no_bukti,	
				'TGL'              => date('Y-m-d', strtotime($request['TGL'])),		
                'PER'              => $periode,
                'KODEC'            => ($request['KODEC']==null) ? "" : $request['KODEC'],			
                'NAMAC'            => ($request['NAMAC']==null) ? "" : $request['NAMAC'],
                'BACNO'            => ($request['BACNO']==null) ? "" : $request['BACNO'],			
                'BNAMA'            => ($request['BNAMA']==null) ? "" : $request['BNAMA'],
				'FLAG'             => $FLAGZ,
				'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'BAYAR'            => (float) str_replace(',', '', $request['TBAYAR']),
                'LAIN'             => (float) str_replace(',', '', $request['TLAIN']),
                
				'KODEP'            => ($request['KODEP']==null) ? "" : $request['KODEP'],
				'NAMAP'            => ($request['NAMAP']==null) ? "" : $request['NAMAP'],
                'KOM'             => (float) str_replace(',', '', $request['KOM']),
                'TKOM'             => (float) str_replace(',', '', $request['TKOM']),
                'HARI'             => (float) str_replace(',', '', $request['HARI']),

				'USRNM'            => Auth::user()->username,
				'TG_SMP'           => Carbon::now(),
				'CBG'              => $CBG,
				// 'PKP'              => $PPN
				'PKP'             => ($request['PKP']==null) ? "" : $request['PKP']
            ]
        );


		$REC	    = $request->input('REC');
		$NO_FAKTUR	= $request->input('NO_FAKTUR');
		$TOTAL	= $request->input('TOTAL');
		$BAYAR	= $request->input('BAYAR');		
		$SISA	= $request->input('SISA');

		$TGL_FAKTUR	= $request->input('TGL_FAKTUR');
		$LAIN	= $request->input('LAIN');
		
		// Check jika value detail ada/tidak
		if ($REC) {
			foreach ($REC as $key => $value) {
				// Declare new data di Model
				$detail	= new Piudetail;
				
				// Insert ke Database
				$detail->NO_BUKTI = $no_bukti;			
				$detail->REC	= $REC[$key];
				$detail->PER	= $periode;
				$detail->FLAG	= $FLAGZ;
				$detail->NO_FAKTUR = ($NO_FAKTUR[$key]==null) ? "" :  $NO_FAKTUR[$key];
				$detail->TOTAL	= (float) str_replace(',', '', $TOTAL[$key]);
				$detail->BAYAR	= (float) str_replace(',', '', $BAYAR[$key]);					
				$detail->SISA	= (float) str_replace(',', '', $SISA[$key]);	

                $detail->TGL_FAKTUR   = date('Y-m-d', strtotime($TGL_FAKTUR[$key]));
				$detail->LAIN	= (float) str_replace(',', '', $LAIN[$key]);	
				$detail->save();
			}
		}
		

//  ganti 11


       $no_buktix = $no_bukti;
		
		$piu = Piu::where('NO_BUKTI', $no_buktix )->first();

        DB::SELECT("UPDATE piu, cust
                    SET piu.NAMAC = cust.NAMAC, piu.ALAMAT = cust.ALAMAT, piu.KOTA = cust.KOTA, piu.PKP=cust.PKP, piu.HARI = cust.HARI  WHERE piu.KODEC = cust.KODEC 
                    AND piu.NO_BUKTI='$no_buktix';");
                    

        DB::SELECT("UPDATE piu, account
                            SET piu.BNAMA = account.NAMA  WHERE piu.BACNO = account.ACNO 
							AND piu.NO_BUKTI='$no_buktix';");
							

        DB::SELECT("UPDATE piu, piud
                            SET piud.ID = piu.NO_ID  WHERE piu.NO_BUKTI =
							piud.NO_BUKTI 
							AND piu.NO_BUKTI='$no_buktix';");

		
        $variablell = DB::select('call piuins(?,?)', array($no_bukti, $no_bukti2));
					 
        // return redirect('/piu/edit/?idx=' . $piu->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/piu?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
	 
// ganti 12
	 
   
   public function edit( Request $request , Piu $piu )
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect('/piu')
			       ->with('status', 'Maaf Periode sudah ditutup!')
                   ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        }
		
		$this->setFlag($request);
		
        $tipx = $request->tipx;

		$idx = $request->idx;
		
        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
		if ( $idx =='0' && $tipx=='undo'  )
	    {
			$tipx ='top';
			
		   }
		   
		 
		   
		if ($tipx=='search') {
			
		   	
    	   $buktix = $request->buktix;
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from piu
		                 where PER ='$per' and NO_BUKTI = '$buktix'
                         AND CBG = '$CBG'						 
                         AND PKP = '$PPN'						 
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from piu
		                 where PER ='$per' AND CBG = '$CBG'   
                         AND PKP = '$PPN'						 
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from piu     
		             where PER ='$per' AND CBG = '$CBG'
                     AND PKP = '$PPN'						 
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from piu    
		             where PER ='$per' AND CBG = '$CBG'
                     AND PKP = '$PPN'						 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from piu
						where PER ='$per' AND CBG = '$CBG'
                        AND PKP = '$PPN'						 
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
			$piu = Piu::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$piu = new Piu;
                $piu->TGL = Carbon::now();
      
				
		 }

        $no_bukti = $piu->NO_BUKTI;
	    $piudetail = DB::table('piud')->where('NO_BUKTI', $no_bukti)->get();	
		
		
		$data = [
            'header'        => $piu,
            'detail'        => $piudetail,
			
        ];
 
         
         return view('otransaksi_piu.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' =>$this->FLAGZ, 'judul'=> $this->judul ]);
      
    }


// ganti 18

    public function update(Request $request, Piu $piu )
    {
		
        $this->validate($request,
        [
		
// ganti 19
  //              'NO_PO'       => 'required',
                'TGL'      => 'required'

            ]
        );
		
// ganti 20

        $variablell = DB::select('call piudel(?,?)', array($piu['NO_BUKTI'], '0'));		

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        // ganti 20
        $periode = $request->session()->get('periode')['bulan']. '/' . $request->session()->get('periode')['tahun'];
	
	
        $piu->update(
            [
				'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'KODEC'            => ($request['KODEC']==null) ? "" : $request['KODEC'],	
				'NAMAC'			   =>($request['NAMAC']==null) ? "" : $request['NAMAC'],
				'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'BAYAR'            => (float) str_replace(',', '', $request['TBAYAR']),
                'LAIN'             => (float) str_replace(',', '', $request['TLAIN']),
                'BACNO'            => ($request['BACNO']==null) ? "" : $request['BACNO'],			
                'BNAMA'            => ($request['BNAMA']==null) ? "" : $request['BNAMA'],                
				'KODEP'            => ($request['KODEP']==null) ? "" : $request['KODEP'],
				'NAMAP'            => ($request['NAMAP']==null) ? "" : $request['NAMAP'],
                'KOM'             => (float) str_replace(',', '', $request['KOM']),
                'TKOM'             => (float) str_replace(',', '', $request['TKOM']),
                'HARI'             => (float) str_replace(',', '', $request['HARI']),

				'USRNM'            => Auth::user()->username,
				'TG_SMP'           => Carbon::now(),
				'CBG'              => $CBG,
				'PKP'             => ($request['PKP']==null) ? "" : $request['PKP'],
				'FLAG'              => $FLAGZ	
            ]
        );



		$no_buktix = $piu->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');
		
        $REC	= $request->input('REC');
		$NO_FAKTUR = $request->input('NO_FAKTUR');
		$BAYAR	= $request->input('BAYAR');
		$TOTAL	= $request->input('TOTAL');
		$SISA	= $request->input('SISA');

		$TGL_FAKTUR	= $request->input('TGL_FAKTUR');
		$LAIN	= $request->input('LAIN');

         $query = DB::table('piud')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i=0;$i<$length;$i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = Piudetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
				        'PER'        => $periode,
                        'FLAG'       => $FLAGZ,	  							
                        'NO_FAKTUR'  => ($NO_FAKTUR[$i]==null) ? "" :  $NO_FAKTUR[$i],
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'BAYAR'      => (float) str_replace(',', '', $BAYAR[$i]),
                        'SISA'      => (float) str_replace(',', '', $SISA[$i]),
           
                        'TGL_FAKTUR'   => ($TGL_FAKTUR[$i] != '') ? date("Y-m-d", strtotime($TGL_FAKTUR[$i])) : "",
                        'LAIN'      => (float) str_replace(',', '', $LAIN[$i]),

                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = Piudetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],
    
                    [
                        'REC'        => $REC[$i],
                        'NO_FAKTUR'  => ($NO_FAKTUR[$i]==null) ? "" :  $NO_FAKTUR[$i],	
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'BAYAR'      => (float) str_replace(',', '', $BAYAR[$i]),
                        'SISA'      => (float) str_replace(',', '', $SISA[$i]),
           
                        'TGL_FAKTUR'   => ($TGL_FAKTUR[$i] != '') ? date("Y-m-d", strtotime($TGL_FAKTUR[$i])) : "",
                        'LAIN'      => (float) str_replace(',', '', $LAIN[$i]),
                        
                    ]
                );
            }
        }


///////////////////////////////////////////

//  ganti 21

		
        DB::SELECT("UPDATE piu, cust
                    SET piu.NAMAC = cust.NAMAC, piu.ALAMAT = cust.ALAMAT, piu.KOTA = cust.KOTA, piu.PKP=cust.PKP, piu.HARI = cust.HARI  WHERE piu.KODEC = cust.KODEC 
                    AND piu.NO_BUKTI='$no_buktix';");


        DB::SELECT("UPDATE piu, account
                            SET piu.BNAMA = account.NAMA  WHERE piu.BACNO = account.ACNO 
							AND piu.NO_BUKTI='$no_buktix';");
							

        DB::SELECT("UPDATE piu, piud
                            SET piud.ID = piu.NO_ID  WHERE piu.NO_BUKTI =
							piud.NO_BUKTI 
							AND piu.NO_BUKTI='$no_buktix';");
							
        $variablell = DB::select('call piuins(?,?)', array($piu['NO_BUKTI'], 'X'));
		

 		$piu = piu::where('NO_BUKTI', $no_buktix )->first();
					 
        // return redirect('/piu/edit/?idx=' . $piu->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');	
        return redirect('/piu?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
	
	
		
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
	 
// ganti 22
	 
    public function destroy(Request $request,  Piu $piu)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED AS POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('piu')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        }
		
        $variablell = DB::select('call piudel(?,?)', array($piu['NO_BUKTI'], '1'));
		
		
// ganti 23
        $deletepiu = piu::find($piu->NO_ID);

// ganti 24

        $deletepiu->delete();

// ganti 
       return redirect('/piu?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$piu->NO_BUKTI.' berhasil dihapus');

	
		
    }
    
   
    public function cetak (Piu $piu)
    {
        $no_piu = $piu->NO_BUKTI;

        $file     = 'piuc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT piu.NO_BUKTI, piu.TGL, piu.KODEC, piu.NAMAC, piu.ALAMAT, piu.KOTA, 
                                    piu.BACNO, piu.NOTES,
                                    piud.NO_FAKTUR, piud.TOTAL, piud.BAYAR, piud.SISA, piu.USRNM
                            FROM piu, piud 
                            WHERE piu.NO_BUKTI='$no_piu' AND piu.NO_BUKTI = piud.NO_BUKTI 
                            ;
		");

            DB::SELECT("UPDATE piu SET POSTED = 1 WHERE NO_BUKTI='$no_piu';");

        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'KODEC'    => $query[$key]->KODEC,
                'NAMAC'    => $query[$key]->NAMAC,
                'ALAMAT'    => $query[$key]->ALAMAT,
                'KOTA'    => $query[$key]->KOTA,
                'BACNO'    => $query[$key]->BACNO,
                'NACNO'    => $query[$key]->NACNO,
                'NOTES'    => $query[$key]->NOTES,
                'NO_FAKTUR'    => $query[$key]->NO_FAKTUR,
                'TOTAL'    => $query[$key]->TOTAL,
                'BAYAR'    => $query[$key]->BAYAR,
                'SISA'    => $query[$key]->SISA,
                'USRNM'    => $query[$key]->USRNM
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
 
    public function getDetailpiu(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('piud')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
    
}
