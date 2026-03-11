<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Deli;
use App\Models\OTransaksi\DeliDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class DeliController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resbelinse
     */
    var $judul = '';
    var $FLAGZ = '';
    var $GOLZ = '';
	
    function setFlag(Request $request)
    {
        if ( $request->flagz == 'DO' && $request->golz == 'B' ) {
            $this->judul = "Delivery Order Bahan";
        } else if ( $request->flagz == 'DO' && $request->golz == 'J' ){
            $this->judul = "Delivery Order";
        } else if ( $request->flagz == 'DO' && $request->golz == 'D' ){
            $this->judul = "Delivery Order Dropship";
        }

        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;


    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_deli.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ ]);
	
		
    }

    public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $deli = DB::SELECT("SELECT distinct deli.NO_BUKTI, deli.NO_SO, deli.KODEC, deli.NAMAC, 
		                  deli.ALAMAT, deli.KOTA, deli.KODEP, deli.NAMAP, deli.KOM, 
                          deli.RING, deli.SOPIR, deli.TRUCK
                          from deli, delid 
                          WHERE deli.NO_BUKTI = deliD.NO_BUKTI AND deli.GOL ='$golz' 
                          AND deli.CBG = '$CBG' AND deli.PPN = '$PPN' AND delid.SISA > 0	");
        return response()->json($deli);
    }

    public function browseSo(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
		$so = DB::SELECT("SELECT sod.NO_ID, so.NO_BUKTI, so.TGL, so.NAMAC, so.KODEC, so.ALAMAT, so.KOTA,
                                sod.KD_BRG, sod.NA_BRG, sod.SATUAN, sod.QTY, sod.KIRIM, sod.HARGA,
                                sod.SISA, so.KODEP, so.NAMAP, so.RING, so.KOM from so, sod 
                        WHERE so.NO_BUKTI=sod.NO_BUKTI AND so.CBG = '$CBG' AND so.PKP = '$PPN'
                        and sod.SISA>0 
                        -- and so.KODEC='".$request->kodec."' 
                        AND so.GOL ='$golz' AND so.POSTED = 1
                        GROUP BY NO_BUKTI");
		return response()->json($so);
	}
	
	public function browse_detail(Request $request)
    {

        // $filterbukti = '';
        // if($request->NO_SO)
        // {

        //     $filterbukti = " WHERE NO_BUKTI='".$request->NO_SO."' ";
        // }
        $sod = DB::SELECT("SELECT REC, KD_BHN, NA_BHN, SATUAN , QTY, HARGA, KIRIM, SISA, TOTAL, KET, 
                                KD_BRG, NA_BRG, PPN, DPP, DISK
                            from sod
                            where NO_BUKTI='".$request->nobukti."' ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}

    public function browse_delid(Request $request)
    {

        // $filterbukti = '';
        // if($request->NO_SO)
        // {

        //     $filterbukti = " WHERE NO_BUKTI='".$request->NO_SO."' ";
        // }
        $sod = DB::SELECT("SELECT REC, SATUAN , QTY, HARGA, KIRIM, SISA, TOTAL, KET, 
                                KD_BRG, NA_BRG, DPP, PPN, QTY_KIRIM, DISK
                            from delid
                            where NO_BUKTI='".$request->nobukti."' ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}
    // ganti 4



    public function getDeli(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $deli = DB::SELECT("SELECT * from deli  WHERE PER='$periode' and FLAG ='$this->FLAGZ' 
                            and GOL ='$this->GOLZ' AND CBG = '$CBG'  ORDER BY NO_BUKTI ");
	  
	   
        // ganti 6

        return Datatables::of($deli)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("deli/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="deli/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul  . '&golz=' . $row->GOL  . '"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="deli/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a> 
                                <a class="dropdown-item btn btn-danger" href="deli/cetak2/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print Lokasi
                                </a> 
                                <a class="dropdown-item btn btn-danger" href="deli/cetak3/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print SPM
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
                'NO_BUKTI'   => 'required',
                'TGL'        => 'required',
                // 'KODEC'      => 'required',

            ]
        );

        //////     nomer otomatis
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('deli')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ)
                ->where('GOL', $this->GOLZ)->where('CBG', $CBG)->orderByDesc('NO_BUKTI')->limit(1)->get();
		
    
        if( $GOLZ=='J'){

            if($PPN=='1'){

                if ($query != '[]')
                {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'DY'. $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'DY'. $CBG . $tahun . $bulan . '-0001' ;
                }	

            }else {

                if ($query != '[]')
                {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'DZ'. $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'DZ'. $CBG . $tahun . $bulan . '-0001' ;
                }	

            }
            

        } elseif($GOLZ=='D') {

            if($PPN=='1'){

                if ($query != '[]')
                {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'DRY'. $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'DRY'. $CBG . $tahun . $bulan . '-0001' ;
                }	

            }else {

                if ($query != '[]')
                {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'DRZ'. $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'DRZ'. $CBG . $tahun . $bulan . '-0001' ;
                }	

            }

        }
        


			

        $deli = Deli::create(
            [
                'NO_BUKTI'      => $no_bukti,
                'TGL'           => date('Y-m-d', strtotime($request['TGL'])),
                'PER'           => $periode,			
                'FLAG'          => $FLAGZ,							
                'GOL'           => $GOLZ,
                // 'NO_SO'         => ($request['NO_SO']==null) ? "" : $request['NO_SO'],
                // 'NO_DO'         => ($request['NO_DO']==null) ? "" : $request['NO_DO'],
                'TRUCK'         => ($request['TRUCK']==null) ? "" : $request['TRUCK'],
                'SOPIR'         => ($request['SOPIR']==null) ? "" : $request['SOPIR'],
                // 'VIA'           => ($request['VIA']==null) ? "" : $request['VIA'],
                // 'KODEC'         => ($request['KODEC']==null) ? "" : $request['KODEC'],	
				// 'NAMAC'		    =>($request['NAMAC']==null) ? "" : $request['NAMAC'],
				// 'ALAMAT'		=>($request['ALAMAT']==null) ? "" : $request['ALAMAT'],
				// 'KOTA'		    =>($request['KOTA']==null) ? "" : $request['KOTA'],
				'NOTES'			=>($request['NOTES']==null) ? "" : $request['NOTES'],
				'KODEP'			=>($request['KODEP']==null) ? "" : $request['KODEP'],
				'NAMAP'			=>($request['NAMAP']==null) ? "" : $request['NAMAP'],
				'RING'			=>($request['RING']==null) ? "" : $request['RING'],
                'PKP'           => (float) str_replace(',', '', $request['PKP']),
                'KOM'           => (float) str_replace(',', '', $request['KOM']),
                'TOTAL_QTY'     => (float) str_replace(',', '', $request['TQTY']),
                'TOTAL_QTY2'     => (float) str_replace(',', '', $request['TOTAL_QTY2']),
                'TOTAL'      	=> (float) str_replace(',', '', $request['TTOTAL']),
                'TDISK'      	=> (float) str_replace(',', '', $request['TDISK']),
                'HARI'      	=> (float) str_replace(',', '', $request['HARI']),
                'TOTAL_TKOM'      	=> (float) str_replace(',', '', $request['TOTAL_TKOM']),
                'MAXB'      	=> (float) str_replace(',', '', $request['MAXB']),
                'TBERAT'      	=> (float) str_replace(',', '', $request['TOTAL_BERAT']),
				'USRNM'         => Auth::user()->username,
				'TG_SMP'        => Carbon::now(),
				'CBG'           => $CBG,
            ]
        );


		$REC	= $request->input('REC');
		$NO_SO	= $request->input('NO_SO');
		// $TYP	= $request->input('TYP');
		// $NO_TERIMA = $request->input('NO_TERIMA');
		$KD_BRG	= $request->input('KD_BRG');
		$NA_BRG	= $request->input('NA_BRG');
		$SATUAN	= $request->input('SATUAN');
		$XQTY	= $request->input('XQTY');
		$KALI	= $request->input('KALI');
		$QTY	= $request->input('QTY');
		$HARGA	= $request->input('HARGA');
		$TOTAL	= $request->input('TOTAL');
		$PPNX	= $request->input('PPNX');	
		$DPP = $request->input('DPP');	

		$DISK = $request->input('DISK');	 
        $DISKA        = $request->input('DISKA');  
        $DISKB        = $request->input('DISKB');  
        $DISKC        = $request->input('DISKC');  
        $DISKD        = $request->input('DISKD');  
        $DISKE        = $request->input('DISKE');  

		$KET	= $request->input('KET');	
		$TYPE_KOM	= $request->input('TYPE_KOM');	
		$KOM	= $request->input('KOM');	
		$TKOM	= $request->input('TKOM');	
		$LOKASI	= $request->input('LOKASI');	
		$BERAT	= $request->input('BERAT');	
		$TBERAT	= $request->input('TBERAT');	

		$KODEC	= $request->input('KODEC');	
		$NAMAC	= $request->input('NAMAC');	
		$ALAMAT	= $request->input('ALAMAT');	
		$KOTA	= $request->input('KOTA');	
		$PKP	= $request->input('PKP');	

		// Check jika value detail ada/tidak
		if ($REC) {
			foreach ($REC as $key => $value) {
				// Declare new data di Model
				$detail	= new DeliDetail;
				$idDeli = DB::table('deli')->select('NO_ID')->where('NO_BUKTI', $no_bukti)->get();
				// Insert ke Database
				$detail->NO_BUKTI = $no_bukti;	
				// $detail->NO_SO	= $NO_SO[$key];
				$detail->REC	= $REC[$key];
				$detail->PER	= $periode;
				$detail->FLAG	= $FLAGZ;
				$detail->GOL	= $GOLZ;
				$detail->CBG	= $CBG;
				// $detail->TYP	= ($TYP[$key]==null) ? '' : $TYP[$key];
				// $detail->NO_TERIMA	= ($NO_TERIMA[$key]==null) ? '' : $NO_TERIMA[$key];
				$detail->NO_SO	= ($GOLZ == 'B' ) ? ($NO_SO[$key]==null) : $NO_SO[$key];
				$detail->KD_BRG	= ($GOLZ == 'B' ) ? ($KD_BRG[$key]==null) : $KD_BRG[$key];
				$detail->NA_BRG	= ($GOLZ == 'B' ) ? ($NA_BRG[$key]==null) : $NA_BRG[$key];
				$detail->SATUAN	= ($SATUAN[$key]==null) ? '' : $SATUAN[$key];
				$detail->QTY	= (float) str_replace(',', '', $QTY[$key]);
				$detail->QTY2	= (float) str_replace(',', '', $XQTY[$key]);
				$detail->KALI	= (float) str_replace(',', '', $KALI[$key]);
				$detail->SISA	= (float) str_replace(',', '', $QTY[$key]);
				$detail->HARGA	= (float) str_replace(',', '', $HARGA[$key]);
				$detail->TOTAL	= (float) str_replace(',', '', $TOTAL[$key]);
				$detail->KET	= ($KET[$key]==null) ? '' : $KET[$key];
				$detail->PPN	= (float) str_replace(',', '', $PPNX[$key]);
				$detail->DPP	= (float) str_replace(',', '', $DPP[$key]);

				$detail->DISK	= (float) str_replace(',', '', $DISK[$key]);
                $detail->DISK1       = (float) str_replace(',', '', $DISKA[$key]); 
                $detail->DISK2       = (float) str_replace(',', '', $DISKB[$key]); 
                $detail->DISK3       = (float) str_replace(',', '', $DISKC[$key]); 
                $detail->DISK4       = (float) str_replace(',', '', $DISKD[$key]); 
                $detail->DISK5       = (float) str_replace(',', '', $DISKE[$key]); 

				$detail->ID	    = $idDeli[0]->NO_ID;
				$detail->TYPE_KOM	= ($TYPE_KOM[$key]==null) ? '' : $TYPE_KOM[$key];
				$detail->KOM	= (float) str_replace(',', '', $KOM[$key]);
				$detail->TKOM	= (float) str_replace(',', '', $TKOM[$key]);
				// $detail->ID_SOD	= ($ID_SOD[$key]==null) ? '' : $ID_SOD[$key];

				$detail->KODEC	= ($KODEC[$key]==null) ? '' : $KODEC[$key];
				$detail->NAMAC	= ($NAMAC[$key]==null) ? '' : $NAMAC[$key];
				$detail->ALAMAT	= ($ALAMAT[$key]==null) ? '' : $ALAMAT[$key];
				$detail->KOTA	= ($KOTA[$key]==null) ? '' : $KOTA[$key];
				$detail->PKP	= (float) str_replace(',', '', $PKP[$key]);
				$detail->LOKASI	= ($LOKASI[$key]==null) ? '' : $LOKASI[$key];
				$detail->BERAT	= (float) str_replace(',', '', $BERAT[$key]);
				$detail->TBERAT	= (float) str_replace(',', '', $TBERAT[$key]);
				
                // $detail->JTEMPO     = date('Y-m-d', strtotime($JTEMPO[$key]));

				$detail->save();
			}
		}	
		
		$no_buktix = $no_bukti;
		
		$deli = Deli::where('NO_BUKTI', $no_buktix )->first();

        DB::SELECT("CALL deliins('$no_buktix')");

        DB::SELECT("UPDATE deli, cust
                     SET deli.NAMAC = cust.NAMAC, deli.ALAMAT = cust.ALAMAT, deli.KOTA = cust.KOTA, deli.PKP=cust.PKP, deli.HARI = cust.HARI  WHERE deli.KODEC = cust.KODEC 
                     AND deli.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE deli,  delid
                            SET  delid.ID =  deli.NO_ID  WHERE  deli.NO_BUKTI =  delid.NO_BUKTI 
							AND  deli.NO_BUKTI='$no_buktix';");

		
					 
        // return redirect('/deli/edit/?idx=' . $deli->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/deli?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Deli $deli)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/deli')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from deli
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from deli
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from deli     
		             where PER ='$per' 
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG' and NO_BUKTI < 
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from deli    
		             where PER ='$per'  
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG' and NO_BUKTI > 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from deli
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
			$deli = Deli::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$deli = new Deli;
                $deli->TGL = Carbon::now();
                $deli->JTEMPO = Carbon::now();
				
				
		 }

        $no_bukti = $deli->NO_BUKTI;
        $deliDetail = DB::table('delid')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $deli,
			'detail'        => $deliDetail

        ];
 
 		$sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM sup 
		                 ORDER BY NAMAS ASC" );
		
         
         return view('otransaksi_deli.edit', $data)->with(['sup' => $sup])
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'golz' =>$this->GOLZ, 'judul'=> $this->judul ]);
			 

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 18

    public function update(Request $request, Deli $deli)
    {

        $this->validate(
            $request,
            [

                'NO_BUKTI'   => 'required',
                'TGL'        => 'required',
            ]
        );

		$this->setFlag($request);
        $GOLZ = $this->GOLZ;
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];


        $deli->update(
            [
                'TGL'           => date('Y-m-d', strtotime($request['TGL'])),	
                'TRUCK'         => ($request['TRUCK']==null) ? "" : $request['TRUCK'],
                'SOPIR'         => ($request['SOPIR']==null) ? "" : $request['SOPIR'],
				'NOTES'			=>($request['NOTES']==null) ? "" : $request['NOTES'],
                'TOTAL_QTY'     => (float) str_replace(',', '', $request['TQTY']),
                'TOTAL_QTY2'     => (float) str_replace(',', '', $request['TOTAL_QTY2']),
                'TOTAL'      	=> (float) str_replace(',', '', $request['TTOTAL']),
                'TDISK'      	=> (float) str_replace(',', '', $request['TDISK']),
				'KODEP'			=>($request['KODEP']==null) ? "" : $request['KODEP'],
				'NAMAP'			=>($request['NAMAP']==null) ? "" : $request['NAMAP'],
				'RING'			=>($request['RING']==null) ? "" : $request['RING'],
                'KOM'           => (float) str_replace(',', '', $request['KOM']),
                'HARI'           => (float) str_replace(',', '', $request['HARI']),
                'MAXB'           => (float) str_replace(',', '', $request['MAXB']),
                'PKP'           => (float) str_replace(',', '', $request['PKP']),
                'TBERAT'           => (float) str_replace(',', '', $request['TOTAL_BERAT']),
				'USRNM'         => Auth::user()->username,						
                'GOL'           => $GOLZ,
                'FLAG'          => $FLAGZ,
				'TG_SMP'        => Carbon::now(),					
				'CBG'           => $CBG,					
                
            ]
        );

		$no_buktix = $deli->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');
		$REC	= $request->input('REC');
		$NO_SO	= $request->input('NO_SO');
		$KD_BRG	= $request->input('KD_BRG');
		$NA_BRG	= $request->input('NA_BRG');
		$SATUAN	= $request->input('SATUAN');
		$XQTY	= $request->input('XQTY');
		$KALI	= $request->input('KALI');
		$QTY	= $request->input('QTY');
		$HARGA	= $request->input('HARGA');
		$TOTAL	= $request->input('TOTAL');
		$PPNX	= $request->input('PPNX');	
		$DPP = $request->input('DPP');	
		$DISK = $request->input('DISK');	
        	
        $DISKA = $request->input('DISKA');	
        $DISKB = $request->input('DISKB');	
        $DISKC = $request->input('DISKC');	
        $DISKD = $request->input('DISKD');	
        $DISKE = $request->input('DISKE');	

		$KET	= $request->input('KET');	
		// $JTEMPO	= $request->input('JTEMPO');	
		$ID_SOD	= $request->input('ID_SOD');	
		$TYPE_KOM	= $request->input('TYPE_KOM');	
		$KOM	= $request->input('KOM');	
		$TKOM	= $request->input('TKOM');	
		$KODEC	= $request->input('KODEC');	
		$NAMAC	= $request->input('NAMAC');	
		$ALAMAT	= $request->input('ALAMAT');	
		$KOTA	= $request->input('KOTA');	
		$PKP	= $request->input('PKP');	
		$LOKASI	= $request->input('LOKASI');	
		$BERAT	= $request->input('BERAT');	
		$TBERAT	= $request->input('TBERAT');	
		
       // Delete yang NO_ID tidak ada di input
        $query = DB::table('delid')->where('NO_BUKTI', $deli->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i=0;$i<$length;$i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = DeliDetail::create(
                    [
                        'NO_BUKTI'   => $deli->NO_BUKTI,
                        // 'NO_SO'      => ($NO_SO[$i]==null) ? "" :  $NO_SO[$i],
                        'REC'        => $REC[$i],
				        'PER'        => $deli->PER,	
				        'FLAG'       => 'SJ',					
				        'GOL'        => $GOLZ,						
				        'CBG'        => $CBG,				
                        'NO_SO'  => ($NO_SO[$i]==null) ? "" :  $NO_SO[$i],	
                        'KD_BRG'     => ($GOLZ == 'B' ) ? ($KD_BRG[$i]==null) :  $KD_BRG[$i],
                        'NA_BRG'     => ($GOLZ == 'B' ) ? ($NA_BRG[$i]==null) : $NA_BRG[$i],	
                        'SATUAN'     => ($SATUAN[$i]==null) ? "" : $SATUAN[$i],
						'QTY2'      	 => (float) str_replace(',', '', $XQTY[$i]),
						'KALI'      	 => (float) str_replace(',', '', $KALI[$i]),
						'QTY'      	 => (float) str_replace(',', '', $QTY[$i]),
						'SISA'       => (float) str_replace(',', '', $QTY[$i]),
						'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
						'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'PPN'        => (float) str_replace(',', '', $PPNX[$i]),
                        'DPP'        => (float) str_replace(',', '', $DPP[$i]),
                        'DISK'       => (float) str_replace(',', '', $DISK[$i]),
                        
                        'DISK1'      => (float) str_replace(',', '', $DISKA[$i]),
                        'DISK2'      => (float) str_replace(',', '', $DISKB[$i]),
                        'DISK3'      => (float) str_replace(',', '', $DISKC[$i]),
                        'DISK4'      => (float) str_replace(',', '', $DISKD[$i]),
                        'DISK5'      => (float) str_replace(',', '', $DISKE[$i]),

                        'KET'        => ($KET[$i]==null) ? "" : $KET[$i],
                        'TYPE_KOM'   => ($TYPE_KOM[$i]==null) ? "" : $TYPE_KOM[$i],
                        'KOM'        => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'        => (float) str_replace(',', '', $TKOM[$i]),
                        'ID'         => $deli->NO_ID,
                        // 'ID_SOD'     => ($ID_SOD[$i]==null) ? "" : $ID_SOD[$i],
                        'KODEC'   => ($KODEC[$i]==null) ? "" : $KODEC[$i],
                        'NAMAC'   => ($NAMAC[$i]==null) ? "" : $NAMAC[$i],
                        'ALAMAT'   => ($ALAMAT[$i]==null) ? "" : $ALAMAT[$i],
                        'KOTA'   => ($KOTA[$i]==null) ? "" : $KOTA[$i],
                        'PKP'        => (float) str_replace(',', '', $PKP[$i]),
                        'LOKASI'   => ($LOKASI[$i]==null) ? "" : $LOKASI[$i],
                        'BERAT'        => (float) str_replace(',', '', $BERAT[$i]),
                        'TBERAT'        => (float) str_replace(',', '', $TBERAT[$i]),
                        
                        // 'JTEMPO'     => ($JTEMPO[$i] != '') ? date("Y-m-d", strtotime($JTEMPO[$i])) : "",
                        
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $update = DeliDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $deli->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],
    
                    [
                        // 'NO_SO'      => ($NO_SO[$i]==null) ? "" :  $NO_SO[$i],
                        'REC'        => $REC[$i],
				        'FLAG'       => 'SJ',					
				        'GOL'        => $GOLZ,						
				        'CBG'        => $CBG,					
                        // 'TYP'        => ($TYP[$i]==null) ? "" :  $TYP[$i],
                        // 'NO_TERIMA'  => ($NO_TERIMA[$i]==null) ? "" :  $NO_TERIMA[$i],	
                        'KD_BRG'     => ($GOLZ == 'B' ) ? ($KD_BRG[$i]==null) :  $KD_BRG[$i],
                        'NA_BRG'     => ($GOLZ == 'B' ) ? ($NA_BRG[$i]==null) : $NA_BRG[$i],	
                        'SATUAN'     => ($SATUAN[$i]==null) ? "" : $SATUAN[$i],
						'QTY2'      	 => (float) str_replace(',', '', $XQTY[$i]),
						'KALI'      	 => (float) str_replace(',', '', $KALI[$i]),
						'QTY'      	 => (float) str_replace(',', '', $QTY[$i]),
						'SISA'       => (float) str_replace(',', '', $QTY[$i]),
						'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
						'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'PPN'        => (float) str_replace(',', '', $PPNX[$i]),
                        'DPP'        => (float) str_replace(',', '', $DPP[$i]),
                        'DISK'        => (float) str_replace(',', '', $DISK[$i]),
                        
                        'DISK1'      => (float) str_replace(',', '', $DISKA[$i]),
                        'DISK2'      => (float) str_replace(',', '', $DISKB[$i]),
                        'DISK3'      => (float) str_replace(',', '', $DISKC[$i]),
                        'DISK4'      => (float) str_replace(',', '', $DISKD[$i]),
                        'DISK5'      => (float) str_replace(',', '', $DISKE[$i]),
                        
                        'KET'        => ($KET[$i]==null) ? "" : $KET[$i],
                        'TYPE_KOM'   => ($TYPE_KOM[$i]==null) ? "" : $TYPE_KOM[$i],
                        'KOM'        => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'        => (float) str_replace(',', '', $TKOM[$i]),
                        'KODEC'   => ($KODEC[$i]==null) ? "" : $KODEC[$i],
                        'NAMAC'   => ($NAMAC[$i]==null) ? "" : $NAMAC[$i],
                        'ALAMAT'   => ($ALAMAT[$i]==null) ? "" : $ALAMAT[$i],
                        'KOTA'   => ($KOTA[$i]==null) ? "" : $KOTA[$i],
                        'PKP'        => (float) str_replace(',', '', $PKP[$i]),
                        // 'ID_SOD'     => ($ID_SOD[$i]==null) ? "" : $ID_SOD[$i],
                        'LOKASI'   => ($LOKASI[$i]==null) ? "" : $LOKASI[$i],
                        'BERAT'        => (float) str_replace(',', '', $BERAT[$i]),
                        'TBERAT'        => (float) str_replace(',', '', $TBERAT[$i]),
                        
                        // 'JTEMPO'     => ($JTEMPO[$i] != '') ? date("Y-m-d", strtotime($JTEMPO[$i])) : "",

                    ]
                );
            }
        }	   
	   
        // DB::SELECT("CALL deliins('$deli->NO_BUKTI')");

 		$deli = Deli::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $deli->NO_BUKTI;

        DB::SELECT("UPDATE deli, cust
                     SET deli.NAMAC = cust.NAMAC, deli.ALAMAT = cust.ALAMAT, deli.KOTA = cust.KOTA, deli.PKP=cust.PKP, deli.HARI = cust.HARI  WHERE deli.KODEC = cust.KODEC 
                     AND deli.NO_BUKTI='$no_bukti';");

        DB::SELECT("UPDATE deli,  delid
                    SET  delid.ID =  deli.NO_ID  WHERE  deli.NO_BUKTI =  delid.NO_BUKTI 
                    AND  deli.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/deli/edit/?idx=' . $deli->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');	
        return redirect('/deli?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Deli $deli)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('deli')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
        
         DB::SELECT("CALL delidel('$deli->NO_BUKTI')");
		
        $deleteDeli = Deli::find($deli->NO_ID);

        $deleteDeli->delete();

       return redirect('/deli?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ ])->with('statusHapus', 'Data '.$deli->NO_BUKTI.' berhasil dihapus');


    }
    
    public function cetak(Deli $deli)
    {
        $no_deli = $deli->NO_BUKTI;

        $file     = 'delic';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT deli.NO_BUKTI, deli.TGL, deli.KODEC, deli.NAMAC, deli.TOTAL_QTY, deli.NOTES, deli.ALAMAT, 
                                    deli.KOTA, delid.KD_BRG, delid.NA_BRG, delid.SATUAN, delid.QTY, delid.QTY2, 
                                    delid.HARGA, delid.TOTAL, delid.KET, deli.PPN, deli.NETT, deli.TRUCK, deli.SOPIR,
                                    delid.NO_SO, deli.USRNM, delid.LOKASI
                            FROM deli, delid 
                            WHERE deli.NO_BUKTI='$no_deli' AND deli.NO_BUKTI = delid.NO_BUKTI 
                            ;
		");

        
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'TGL_CETAK' => NOW(),
                'NO_SO'    => $query[$key]->NO_SO,
                'KODEC'    => $query[$key]->KODEC,
                'NAMAC'    => $query[$key]->NAMAC,
                'ALAMAT'    => $query[$key]->ALAMAT,
                'KOTA'    => $query[$key]->KOTA,
                'KG'       => $query[$key]->KG,
                'HARGA'    => $query[$key]->HARGA,
                'TOTAL'    => $query[$key]->TOTAL,
                'BAYAR'    => $query[$key]->BAYAR,
                'NOTES'    => $query[$key]->NOTES,
                'KD_BRG'    => $query[$key]->KD_BRG,
                'NA_BRG'    => $query[$key]->NA_BRG,
                'SATUAN'    => $query[$key]->SATUAN,
                'QTY'    => $query[$key]->QTY2,
                'PPN'    => $query[$key]->PPN,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET,
                'USRNM'    => $query[$key]->USRNM,
                'TRUCK'    => $query[$key]->TRUCK,
                'SOPIR'    => $query[$key]->SOPIR,
                'LOKASI'    => $query[$key]->LOKASI
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
        DB::SELECT("UPDATE deli SET POSTED = 1 WHERE deli.NO_BUKTI='$no_deli';");
    }

    public function cetak2(Deli $deli)
    {
        $no_deli = $deli->NO_BUKTI;

        $file     = 'delic_lokasi';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT  deli.NO_BUKTI, deli.TGL, deli.KODEC, deli.NAMAC, deli.TOTAL_QTY, deli.NOTES, deli.ALAMAT, 
                                    deli.KOTA, delid.KD_BRG, delid.NA_BRG, delid.SATUAN, delid.QTY, delid.QTY2, 
                                    delid.HARGA, delid.TOTAL, delid.KET, deli.PPN, deli.NETT, deli.TRUCK, deli.SOPIR,
                                    delid.NO_SO, deli.USRNM, delid.LOKASI
                            FROM deli, delid 
                            WHERE deli.NO_BUKTI = delid.NO_BUKTI 
                            AND deli.NO_BUKTI='$no_deli'
                            ;
		");

        
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'TGL_CETAK' => NOW(),
                'NO_SO'    => $query[$key]->NO_SO,
                'KODEC'    => $query[$key]->KODEC,
                'NAMAC'    => $query[$key]->NAMAC,
                'ALAMAT'    => $query[$key]->ALAMAT,
                'KOTA'    => $query[$key]->KOTA,
                'KG'       => $query[$key]->KG,
                'HARGA'    => $query[$key]->HARGA,
                'TOTAL'    => $query[$key]->TOTAL,
                'BAYAR'    => $query[$key]->BAYAR,
                'NOTES'    => $query[$key]->NOTES,
                'KD_BRG'    => $query[$key]->KD_BRG,
                'NA_BRG'    => $query[$key]->NA_BRG,
                'SATUAN'    => $query[$key]->SATUAN,
                'QTY'    => $query[$key]->QTY2,
                'PPN'    => $query[$key]->PPN,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET,
                'USRNM'    => $query[$key]->USRNM,
                'TRUCK'    => $query[$key]->TRUCK,
                'SOPIR'    => $query[$key]->SOPIR,
                'LOKASI'    => $query[$key]->LOKASI
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
        DB::SELECT("UPDATE deli SET POSTED = 1 WHERE deli.NO_BUKTI='$no_deli';");
    }
	
	public function cetak3 (Deli $deli)
    {
        $no_deli = $deli->NO_BUKTI;

        $file     = 'spmc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT  deli.NO_BUKTI, deli.TGL, deli.KODEC, deli.NAMAC, deli.TOTAL_QTY, deli.NOTES, deli.ALAMAT, 
                                    deli.KOTA, delid.KD_BRG, delid.NA_BRG, delid.SATUAN, delid.QTY, delid.QTY2, 
                                    delid.HARGA, delid.TOTAL, delid.KET, deli.PPN, deli.NETT, deli.TRUCK, deli.SOPIR,
                                    delid.NO_SO, deli.USRNM, delid.LOKASI
                            FROM deli, delid 
                            WHERE deli.NO_BUKTI='$no_deli' AND deli.NO_BUKTI = delid.NO_BUKTI 
                            ;
		");

        
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'TGL_CETAK' => NOW(),
                'NO_SO'    => $query[$key]->NO_SO,
                'KODEC'    => $query[$key]->KODEC,
                'NAMAC'    => $query[$key]->NAMAC,
                'ALAMAT'    => $query[$key]->ALAMAT,
                'KOTA'    => $query[$key]->KOTA,
                'KG'       => $query[$key]->KG,
                'HARGA'    => $query[$key]->HARGA,
                'TOTAL'    => $query[$key]->TOTAL,
                'BAYAR'    => $query[$key]->BAYAR,
                'NOTES'    => $query[$key]->NOTES,
                'KD_BRG'    => $query[$key]->KD_BRG,
                'NA_BRG'    => $query[$key]->NA_BRG,
                'SATUAN'    => $query[$key]->SATUAN,
                'QTY'    => $query[$key]->QTY2,
                'PPN'    => $query[$key]->PPN,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET,
                'USRNM'    => $query[$key]->USRNM,
                'TRUCK'    => $query[$key]->TRUCK,
                'SOPIR'    => $query[$key]->SOPIR,
                'LOKASI'    => $query[$key]->LOKASI
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
        DB::SELECT("UPDATE deli SET POSTED = 1 WHERE deli.NO_BUKTI='$no_deli';");
    }
	
	
	 public function posting(Request $request)
    {
      

    }
	
	
	public function getDetailDeli(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('delid')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
	
}
