<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Po;
use App\Models\OTransaksi\PoDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class PoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    var $judul = '';
    var $FLAGZ = '';
    var $GOLZ = '';
	
    function setFlag(Request $request)
    {
        if ( $request->flagz == 'PO' && $request->golz == 'PB' ) {
            $this->judul = "Purchase Order";
        } else if ( $request->flagz == 'PO' && $request->golz == 'PZ' ) {
            $this->judul = "PO Outlet";
        } else if ( $request->flagz == 'PO' && $request->golz == 'PN' ) {
            $this->judul = "PO Non";
        }

        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;

    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_po.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ ]);
	
		
    }
	
	public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
        $po = DB::SELECT("SELECT distinct po.NO_BUKTI , po.KODES, po.NAMAS, 
		                  po.ALAMAT, po.KOTA, po.PKP, po.GUDANG, po.JTEMPO, po.NOTES from po, pod 
                          WHERE po.NO_BUKTI = pod.NO_BUKTI AND po.GOL ='$golz'
                          AND po.CBG = '$CBG' 
                        --   AND po.PKP ='$PPN' 
                          AND pod.SISA > 0 AND POSTED = 1
                          GROUP BY NO_BUKTI ");
        return response()->json($po);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;
		
		$po = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR, 
                                TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from po
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($po);
    }


	public function index_posting(Request $request)
    {
 
        return view('otransaksi_po.post');
    }
	  
	public function browse_pod(Request $request)
    {
        $golx = $request->GOL;

        

            $pod = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI, a.PPN, a.DPP, a.DISK,
                                a.QTY2 AS XQTY, a.KALI
                            from pod a, brg b 
                            where a.NO_BUKTI='".$request->nobukti."' AND a.KD_BRG = b.KD_BRG");

        

		return response()->json($pod);
	}
	
	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_PO."' AND a.KD_BHN = b.KD_BHN ";
		}
		$pod = DB::SELECT("SELECT a.REC, a.KD_BHN, a.NA_BHN, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI
                            from pod a, bhn b 
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($pod);
	}


    public function browse_detail2(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{
	
			$filterbukti = " WHERE NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$pod = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI 
                            from pod a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($pod);
	}
    // ganti 4



    public function getPo(Request $request)
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
		
	  
        $po = DB::SELECT("SELECT * FROM pobsn where per= '$periode'  AND FLAG= '$this->FLAGZ'  order by NO_BUKTI ");
	   
        // ganti 6

        return Datatables::of($po)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("po/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";
                    // batas
                    
                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="po/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="po/cetak/' . $row->NO_ID . '">
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $this->validate(
            $request,
            // GANTI 9

            [
 //               'NO_PO'       => 'required',
                'TGL'      => 'required'
                

            ]
        );

        //////     nomer otomatis

        $kodesx = $request->KODES;
        
        $xxx= DB::table('sup')->select('PKP')->where('KODES', $kodesx)->get();

        $PPN = $xxx[0]->PKP ;
        
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
        
        /////////////////////////////////////////
        

		/////////////////////////////////////////
		
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('po')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'PO')->where('CBG', $CBG)
                ->where('GOL', $this->GOLZ )->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($FLAGZ=='PO') {

            if( $GOLZ =='PB' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'PB'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'PB'  . $CBG . $tahun . $bulan . '-0001';
                }

            } elseif ( $GOLZ =='PZ' ) {

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'PZ'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'PZ'  . $CBG . $tahun . $bulan . '-0001';
                }
                
            } elseif ( $GOLZ =='PN' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'PN'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'PN'  . $CBG . $tahun . $bulan . '-0001';
                }
                
            }

        } 

        		

        $po = Po::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'ALAMAT'           => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'             => ($request['KOTA'] == null) ? "" : $request['KOTA'],
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
                'FLAG'             => 'PO',						
                'GOL'              => $GOLZ,
                'CBG'              => $CBG,
                'KET'              => ($request['KET'] == null) ? "" : $request['KET'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
            ]
        );


		$REC        = $request->input('REC');
		$KD_BRG     = $request->input('KD_BRG');
        $NA_BRG     = $request->input('NA_BRG');	
        $BARCODE    = $request->input('BARCODE');
        $QTY        = $request->input('QTY');
        $HARGA      = $request->input('HARGA');		
        $TOTAL      = $request->input('TOTAL');	
        $SISA       = $request->input('SISA');		
        $LAKU       = $request->input('LAKU');		

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new PoDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;		
                $detail->GOL 	     = $GOLZ; 		
                $detail->CBG 	     = $CBG;        
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->BARCODE     = ($BARCODE[$key] == null) ? "" :  $BARCODE[$key];
                $detail->QTY         = (float) str_replace(',', '', $QTY[$key]);
                $detail->HARGA       = (float) str_replace(',', '', $HARGA[$key]);
                $detail->TOTAL       = (float) str_replace(',', '', $TOTAL[$key]); 
                $detail->SISA        = (float) str_replace(',', '', $QTY[$key]); 
                $detail->LAKU        = (float) str_replace(',', '', $LAKU[$key]);		
                $detail->save();
            }
        }	
		
		$no_buktix = $no_bukti;
		
		$po = Po::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE po, sup
                    SET po.NAMAS = sup.NAMAS, po.ALAMAT = sup.ALAMAT, po.KOTA = sup.KOTA, po.PKP=sup.PKP, po.HARI = sup.HARI  WHERE po.KODES = sup.KODES 
                    AND po.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE po,  pod
                            SET  pod.ID =  po.NO_ID  WHERE  po.NO_BUKTI =  pod.NO_BUKTI 
							AND  po.NO_BUKTI='$no_buktix';");
        
        return redirect('/po?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
    }

   public function edit( Request $request , Po $po)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/po')
		// 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }
		
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from po
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ' 
                         AND CBG = '$CBG'
                         AND PKP = '$PPN'
						 and NO_BUKTI = '$buktix'						 
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from po
		                 where PER ='$per' 
						 and FLAG ='$this->FLAGZ' 
                         and GOL ='$this->GOLZ' 
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
		
		
		if ($tipx=='prev' ) {
			
    	   $buktix = $request->buktix;
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from po     
		             where PER ='$per' 
					 and FLAG ='$this->FLAGZ' 
                     and GOL ='$this->GOLZ' 
                     AND CBG = '$CBG'
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from po    
		             where PER ='$per'  
					 and FLAG ='$this->FLAGZ' 
                     and GOL ='$this->GOLZ'
                     AND CBG = '$CBG' 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from po
						where PER ='$per'
						and FLAG ='$this->FLAGZ'
                        and GOL ='$this->GOLZ'
                        AND CBG = '$CBG'    
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
			$po = Po::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$po = new Po;
                $po->TGL = Carbon::now();
                $po->JTEMPO = Carbon::now();
				
				
		 }

        $no_bukti = $po->NO_BUKTI;
        $poDetail = DB::table('pod')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $po,
			'detail'        => $poDetail

        ];
 
 		$sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM sup
		                 ORDER BY NAMAS ASC" );
		
         
         return view('otransaksi_po.edit', $data)->with(['sup' => $sup])
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ, 'judul'=> $this->judul ]);
			 

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Po $po)
    {

        $this->validate(
            $request,
            [

                'TGL'      => 'required'
            ]
        );

        // $variablell = DB::select('call podel(?)', array($po['NO_BUKTI']));


		$this->setFlag($request);
        $GOLZ = $this->GOLZ;
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];


        $po->update(
            [
                
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'ALAMAT'           => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'             => ($request['KOTA'] == null) ? "" : $request['KOTA'],
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
                'FLAG'             => 'PO',						
                'GOL'              => $GOLZ,
                'CBG'              => $CBG,
                'KET'              => ($request['KET'] == null) ? "" : $request['KET'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'updated_by'       => Auth::user()->username,
            ]
        );

		$no_buktix = $po->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');

        $KD_BRG     = $request->input('KD_BRG');
        $NA_BRG     = $request->input('NA_BRG');	
        $BARCODE    = $request->input('BARCODE');
        $QTY        = $request->input('QTY');
        $HARGA      = $request->input('HARGA');		
        $TOTAL      = $request->input('TOTAL');	
        $SISA       = $request->input('SISA');		
        $LAKU       = $request->input('LAKU');	

        $query = DB::table('pod')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = PoDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'SISA'       => (float) str_replace(',', '', $SISA[$i]),
                        'LAKU'       => (float) str_replace(',', '', $LAKU[$i]),
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = PoDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'PER'        => $periode,						
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'SISA'       => (float) str_replace(',', '', $SISA[$i]),
                        'LAKU'       => (float) str_replace(',', '', $LAKU[$i]),
                    ]
                );
            }
        }

 		$po = Po::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $po->NO_BUKTI;
        
        DB::SELECT("UPDATE po, sup
                    SET po.NAMAS = sup.NAMAS, po.ALAMAT = sup.ALAMAT, po.KOTA = sup.KOTA, po.PKP=sup.PKP, po.HARI = sup.HARI  WHERE po.KODES = sup.KODES 
                    AND po.NO_BUKTI='$no_buktix';");


        DB::SELECT("UPDATE po,  pod
                    SET  pod.ID =  po.NO_ID  WHERE  po.NO_BUKTI =  pod.NO_BUKTI 
                    AND  po.NO_BUKTI='$no_bukti';");

        // $variablell = DB::select('call poins(?)', array($po['NO_BUKTI']));
					 
        // return redirect('/po/edit/?idx=' . $po->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');	
        return redirect('/po?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Po $po)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;

        // ini dr mana $this->GOLZ?
        $GOLZ = $_GET['golz'];    
        $FLAGZ = $_GET['flagz'];
      
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('po')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
		
        $deletePo = Po::find($po->NO_ID);

        // $variablell = DB::select('call podel(?)', array($po['NO_BUKTI']));//


        $deletePo->delete();
        // return redirect('/po?flagz=' . $FLAGZ . '&golz=J')
        return redirect('/po?flagz='. $FLAGZ.'&golz='.$GOLZ )
        ->with(['judul' => $judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ])
        ->with('statusHapus', 'Data ' . $po->NO_BUKTI . ' berhasil dihapus');

        
    
 

    }
    
    public function cetak(Po $po)
    {
        $no_po = $po->NO_BUKTI;

        $file     = 'poc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.KODES, po.NAMAS, po.TOTAL_QTY, po.NOTES, po.ALAMAT, 
                                    po.KOTA, pod.KD_BRG, pod.NA_BRG, pod.SATUAN, pod.QTY2 AS QTY, 
                                    pod.HARGA, pod.TOTAL, pod.KET, po.TPPN, po.NETT, po.GUDANG, 
                                    po.JTEMPO, po.TDPP, po.TDISK, pod.DISK
                            FROM po, pod 
                            WHERE po.NO_BUKTI='$no_po' AND po.NO_BUKTI = pod.NO_BUKTI 
                            ;
		");

        
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'JTEMPO'      => $query[$key]->JTEMPO,
                'KODES'    => $query[$key]->KODES,
                'NAMAS'    => $query[$key]->NAMAS,
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
                'QTY'    => $query[$key]->QTY,
                'PPN'    => $query[$key]->TPPN,
                'NETT'    => $query[$key]->NETT,
                'TDPP'    => $query[$key]->TDPP,
                'TDISK'    => $query[$key]->TDISK,
                'DISK'    => $query[$key]->DISK,
                'KET'    => $query[$key]->KET,
                'GUDANG'    => $query[$key]->GUDANG
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

        DB::SELECT("UPDATE po SET POSTED = 1 WHERE po.NO_BUKTI='$no_po';");

    }
	
	
	
	 public function posting(Request $request)
    {
      
        $CEK = $request->input('cek');
        $NO_BUKTI = $request->input('NO_BUKTI');
		
        $usrnmx = Auth::user()->username;   
		 
        $hasil = "";

        if ($CEK) {
            foreach ($CEK as $key => $value) 
			{
				
                    //$STA = $request->input('STA');
					
					$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
					$bulan    = session()->get('periode')['bulan'];
					$tahun    = substr(session()->get('periode')['tahun'], -2);

			   $NO_BUKTIXZ  = $NO_BUKTI[$key];
			  

                    DB::SELECT("UPDATE po SET POSTED = 1 WHERE po.NO_BUKTI='$NO_BUKTIXZ'");
                  
			}
		}
		else
		{
			$hasil = $hasil ."Tidak ada PO yang dipilih! ; ";
		}

            if($hasil!='')
            {
                return redirect('/po/index-posting')->with('status', 'Proses Posting PO ..')->with('gagal', $hasil);
            }
            else
            {
                return redirect('/po/index-posting')->with('status', 'Posting Posting PO selesai..');
            }

    }
	
	
	public function jtempo ( Request $request)
    {
		$tgl = $request->input('TGL');
		$hari = substr($tgl,0,2);
		$bulan = substr($tgl,3,2);
		$tahun = substr($tgl,6,4);
		$harix = $request->HARI;
		
		$datex = Carbon::createFromDate($tahun, $bulan, $hari );

        $datex ->addDays($harix);
       
        $datey = $datex->format('d-m-Y');
		return  $datey;

		
	}
	
	
	public function getDetailPo(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('pod')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
}
