<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\So;
use App\Models\OTransaksi\SoDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class SoController extends Controller
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
        if ( $request->flagz == 'SO' && $request->golz == 'B') {
            $this->judul = "Sales Order Bahan Baku";
        } else if ( $request->flagz == 'SO' && $request->golz == 'J') {
            $this->judul = "Sales Order Barang";
        } else if ( $request->flagz == 'SO' && $request->golz == 'D') {
            $this->judul = "Sales Order Dropship";
        }
		
        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;


    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_so.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ ]);
	
		
    }
	
	public function browse(Request $request)
    {
        $golz = $request->GOL;
        $kodec = $request->KODEC;

        $CBG = Auth::user()->CBG;
		
        $so = DB::SELECT("SELECT so.NO_BUKTI , so.JTEMPO, so.TGL, so.KODEC, so.NAMAC 
                          from so
                          WHERE GOL ='$golz'
                          AND POSTED = 1
                          GROUP BY NO_BUKTI");
        return response()->json($so);
    }

    public function so_detail(Request $request)
    {
       
        $sod = DB::SELECT("SELECT so.NO_BUKTI AS NO_SO , so.JTEMPO, so.TGL, so.KODEC, so.NAMAC, 
                                    so.ALAMAT, so.KOTA, sod.KD_BRG, sod.NA_BRG, sod. QTY, sod.HARGA, sod.KIRIM2 AS KIRIM, sod.SISA2 AS SISA,
                                    sod.TOTAL, sod.PPN, sod.DPP, sod.DISK, sod.SATUAN, sod.TYPE_KOM, 
                                    sod.KOM, sod.TKOM, so.TOTAL_TKOM, so.PKP, sod.LOKASI, sod.BERAT AS BERAT,
                                    sod.QTY2, sod.KALI, so.TOTAL_QTY2, sod.DISK1, sod.DISK2, sod.DISK3, sod.DISK4, sod.DISK5, sod.KET
                            from so, sod 
                            where so.NO_BUKTI = sod.NO_BUKTI 
                            AND so.NO_BUKTI='".$request->nobukti."' 
                            ORDER BY so.NO_BUKTI ");
	

		return response()->json($sod);
	}


	public function browseso(Request $request)
    {
        $golz = $request->GOL;
        $kodec = $request->KODEC;

        $CBG = Auth::user()->CBG;
		
        $so = DB::SELECT("SELECT DISTINCT so.NO_BUKTI , so.TGL, so.KODEC, so.NAMAC 
                          from so, sod
                          WHERE so.NO_BUKTI = sod.NO_BUKTI and 
                          so.GOL ='D'
                          AND sod.SISA > 0
                          AND so.POSTED = 1 ");
        return response()->json($so);
    }
    
    public function browseuang()
    {
        $CBG = Auth::user()->CBG;
		
		$so = DB::SELECT("SELECT NO_BUKTI,TGL, KODEC, NAMAC, TOTAL, BAYAR, (TOTAL-BAYAR) AS SISA ,
                             ALAMAT, KOTA from so
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($so);
    }


	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_SO)
		{
	
			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_SO."' AND a.KD_BHN = b.KD_BHN ";
		}
		$sod = DB::SELECT("SELECT a.REC, a.KD_BHN, a.NA_BHN, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                             b.SATUAN AS SATUANX , a.DPP, a.PPN
                            from sod a, bhn b 
                            $filterbukti ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}


    public function browse_detail2(Request $request)
    {
		$filterbukti = '';
		if($request->NO_SO)
		{
	
			$filterbukti = " WHERE NO_BUKTI='".$request->NO_SO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$sod = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, 
                            b.SATUAN AS SATUANX, a.DPP AS DPP, a.PPN AS PPN
                            from sod a, brg b
                            $filterbukti AND a.KD_BRG = b.KD_BRG
                            ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}
	
	
	public function browse_detail3(Request $request)
    {
		$filterbukti = '';
		if($request->NO_SO)
		{
	
			$filterbukti = " WHERE NO_BUKTI='".$request->NO_SO."'  ";
		}
		$sod = DB::SELECT("SELECT REC, KD_BRG, NA_BRG, QTY, HARGA, 
                            SATUAN, QTY2 AS XQTY, KALI, TOTAL, PPN, DPP, `DISK`
                            from sod 
                            $filterbukti 
                            ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}
	
	
	public function index_posting(Request $request)
    {
 
        return view('otransaksi_so.post');
    }
	
    public function getSo(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;
		
		$this->setFlag($request);

        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;

        $so = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NAMAC, TOTAL, TOTAL_QTY, NOTES, USRNM, 
                                POSTED, FLAG, GOL, TYPE
                        from so  WHERE PER='$periode' and FLAG ='$this->FLAGZ' 
                        AND GOL ='$this->GOLZ' AND CBG = '$CBG' 
                        ORDER BY NO_BUKTI ");
	  
	   
        // ganti 6

        return Datatables::of($so)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("so/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="so/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL .'"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';
                    // $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="return confirm(&quot; Apakah anda yakin ingin hapus? &quot;)" href="so/delete/' . $row->NO_ID . '/?flagz=' . $row->FLAG . '&golz=' . $row->GOL . '&typez=' . $row->TYPE .'" ';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="so/cetak/' . $row->NO_ID . '">
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
                        <a class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-hasbelipup="true" aria-expanded="false">
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
 //               'NO_PO'       => 'required',
                'TGL'      => 'required',

            ]
        );


        $kodecx = $request->KODEC;
        
        $xxx= DB::table('cust')->select('PKP')->where('KODEC', $kodecx)->get();

        $PPN = $xxx[0]->PKP ;
        
        
        //////     nomer otomatis
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;

		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

	    $query = DB::table('so')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'SO')->where('CBG', $CBG)
                    ->where('GOL', $this->GOLZ)->where('PKP', $PPN)->orderByDesc('NO_BUKTI')->limit(1)->get();

        if( $GOLZ=='B'){

            if( $PPN =='1' ){
                
                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'SY' . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'SY' . $CBG . $tahun . $bulan . '-0001';
                }

            }

        } elseif($GOLZ=='J') {

            if( $PPN =='1' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'SY'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'SY'  . $CBG . $tahun . $bulan . '-0001';
                }

            } else {

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'SZ'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'SZ'  . $CBG . $tahun . $bulan . '-0001';
                }
                
            }

        } elseif($GOLZ=='D') {

            if( $PPN =='1' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'SD' . 'Y' . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'SD' . 'Y' . $CBG . $tahun . $bulan . '-0001';
                }
 
            } else {

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'SD'  . 'Z'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'SD'  . 'Z'  . $CBG . $tahun . $bulan . '-0001';
                }
                
            }

            

        }

        
	
//////////////////////////////////////////////////////////////////////////

        $so = So::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'KODEC'            => ($request['KODEC'] == null) ? "" : $request['KODEC'],
                'NAMAC'            => ($request['NAMAC'] == null) ? "" : $request['NAMAC'],
                'ALAMAT'           => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'             => ($request['KOTA'] == null) ? "" : $request['KOTA'],
                'FLAG'             => 'SO',						
                'GOL'              => $GOLZ,
                'CBG'              => $CBG,
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL_QTY2'        => (float) str_replace(',', '', $request['TTOTAL_QTY2']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
                'TDPP'             => (float) str_replace(',', '', $request['TDPP']),
                'TPPN'             => (float) str_replace(',', '', $request['TPPN']),
                'KOM'              => (float) str_replace(',', '', $request['KOM']),
                'NETT'             => (float) str_replace(',', '', $request['NETT']),
                'DISK_GLOBAL'             => (float) str_replace(',', '', $request['DISK_GLOBAL']),
                'TDISK'            => (float) str_replace(',', '', $request['TDISK']),
                'HARI'             => (float) str_replace(',', '', $request['HARI']),
                'KODEP'            => ($request['KODEP'] == null) ? "" : $request['KODEP'],
                'NAMAP'            => ($request['NAMAP'] == null) ? "" : $request['NAMAP'],
                'RING'             => ($request['RING'] == null) ? "" : $request['RING'],
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
                'TOTAL_TKOM'       => (float) str_replace(',', '', $request['TOTAL_TKOM']),
                'LIM'              => (float) str_replace(',', '', $request['LIM']),
                'AK12'             => (float) str_replace(',', '', $request['AK12']),

                'NAMAC_2'          => ($request['NAMAC_2'] == null) ? "" : $request['NAMAC_2'],
                'ALAMAT_2'         => ($request['ALAMAT_2'] == null) ? "" : $request['ALAMAT_2'],
                'KOTA_2'           => ($request['KOTA_2'] == null) ? "" : $request['KOTA_2'],
                'NOTES2'            => ($request['NOTES2'] == null) ? "" : $request['NOTES2'],
            ]
        );


		$REC        = $request->input('REC');
		$KD_BRG     = $request->input('KD_BRG');
		$KD_GRUP     = $request->input('KD_GRUP');
        $NA_BRG     = $request->input('NA_BRG');
        $SATUAN     = $request->input('SATUAN');
        $XQTY        = $request->input('XQTY');
        $KALI        = $request->input('KALI');
        $QTY        = $request->input('QTY');
        $HARGA      = $request->input('HARGA');		
        $TOTAL      = $request->input('TOTAL');	
        $KET        = $request->input('KET');  
        $PPNX        = $request->input('PPNX');  
        $DPP        = $request->input('DPP');  
        $DISK        = $request->input('DISK');  
        $DISKA        = $request->input('DISKA');  
        $DISKB        = $request->input('DISKB');  
        $DISKC        = $request->input('DISKC');  
        $DISKD        = $request->input('DISKD');  
        $DISKE        = $request->input('DISKE');  

        $AHARGA        = $request->input('AHARGA');  
        $BHARGA        = $request->input('BHARGA');  
        $CHARGA        = $request->input('CHARGA');  
        $DHARGA        = $request->input('DHARGA');  
        $EHARGA        = $request->input('EHARGA');  
        $FHARGA        = $request->input('FHARGA');  
        $GHARGA        = $request->input('GHARGA');  
        $TYPE_KOM      = $request->input('TYPE_KOM');  
        $KOM           = $request->input('KOM');  
        $TKOM           = $request->input('TKOM');  
        $LOKASI        = $request->input('LOKASI');  
        $BERAT        = $request->input('BERAT');  
        $TBERAT        = $request->input('TBERAT'); 

        $XSO        = $request->input('XSO');  
        $SEDIA        = $request->input('SEDIA');  
        
        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new SoDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;		
                $detail->GOL 	     = $GOLZ;  		
                $detail->CBG 	     = $CBG;    	
               
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->KD_GRUP     = ($KD_GRUP[$key] == null) ? "" :  $KD_GRUP[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->SATUAN      = ($SATUAN[$key] == null) ? "" :  $SATUAN[$key];			
                $detail->QTY2         = (float) str_replace(',', '', $XQTY[$key]);
                $detail->KALI         = (float) str_replace(',', '', $KALI[$key]);
                $detail->QTY         = (float) str_replace(',', '', $QTY[$key]);
                $detail->SISA         = (float) str_replace(',', '', $QTY[$key]);
                $detail->SISA2         = (float) str_replace(',', '', $QTY[$key]);
                
                $detail->HARGA       = (float) str_replace(',', '', $HARGA[$key]);
                $detail->TOTAL       = (float) str_replace(',', '', $TOTAL[$key]); 
                $detail->PPN       = (float) str_replace(',', '', $PPNX[$key]); 
                $detail->DPP       = (float) str_replace(',', '', $DPP[$key]); 
                $detail->DISK1       = (float) str_replace(',', '', $DISKA[$key]); 
                $detail->DISK2       = (float) str_replace(',', '', $DISKB[$key]); 
                $detail->DISK3       = (float) str_replace(',', '', $DISKC[$key]); 
                $detail->DISK4       = (float) str_replace(',', '', $DISKD[$key]); 
                $detail->DISK5       = (float) str_replace(',', '', $DISKE[$key]); 
                $detail->DISK       = (float) str_replace(',', '', $DISK[$key]); 

                $detail->AHARGA       = (float) str_replace(',', '', $AHARGA[$key]); 
                $detail->BHARGA       = (float) str_replace(',', '', $BHARGA[$key]); 
                $detail->CHARGA       = (float) str_replace(',', '', $CHARGA[$key]); 
                $detail->DHARGA       = (float) str_replace(',', '', $DHARGA[$key]); 
                $detail->EHARGA       = (float) str_replace(',', '', $EHARGA[$key]); 
                $detail->FHARGA       = (float) str_replace(',', '', $FHARGA[$key]); 
                $detail->GHARGA       = (float) str_replace(',', '', $GHARGA[$key]); 
				$detail->TYPE_KOM     = ($TYPE_KOM[$key] == null) ? "" :  $TYPE_KOM[$key];				
                $detail->KOM          = (float) str_replace(',', '', $KOM[$key]); 
                $detail->TKOM         = (float) str_replace(',', '', $TKOM[$key]); 
				$detail->LOKASI       = ($LOKASI[$key] == null) ? "" :  $LOKASI[$key];	
                $detail->BERAT       = (float) str_replace(',', '', $BERAT[$key]); 
                $detail->TBERAT       = (float) str_replace(',', '', $TBERAT[$key]); 

                $detail->XSO       = (float) str_replace(',', '', $XSO[$key]); 
                $detail->SEDIA       = (float) str_replace(',', '', $SEDIA[$key]); 
                
				$detail->KET         = ($KET[$key] == null) ? "" :  $KET[$key];				
                $detail->save();
            }
        }	
		
		$no_buktix = $no_bukti;
		
		$so = So::where('NO_BUKTI', $no_buktix )->first();

        DB::SELECT("UPDATE so, cust
                    SET so.NAMAC = cust.NAMAC, so.ALAMAT = cust.ALAMAT, so.KOTA = cust.KOTA, so.PKP=cust.PKP, so.HARI = cust.HARI  WHERE so.KODEC = cust.KODEC 
                    AND so.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE so, sod
                            SET  sod.ID =  so.NO_ID  WHERE  so.NO_BUKTI =  sod.NO_BUKTI 
							AND  so.NO_BUKTI='$no_buktix';");

        $variablell = DB::select('call soins(?)', array($no_buktix));
		
					 
        // return redirect('/so/edit/?idx=' . $so->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ .'');
        return redirect('/so?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);

		
		
    }


    // ganti 15

   
   public function edit( Request $request , So $so)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect('/so')
			       ->with('status', 'Maaf Periode sudah ditutup!')
                   ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ]);
        }
		
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from so
		                 where PER ='$per' and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ' 
						 and NO_BUKTI = '$buktix'
                         AND CBG = '$CBG'						 
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from so
		                 where PER ='$per' 
						 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ'
                         AND CBG = '$CBG'     
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from so     
		             where PER ='$per' 
					 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ' 
                     AND CBG = '$CBG' and NO_BUKTI < 
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from so    
		             where PER ='$per'  
					 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ' 
                     AND CBG = '$CBG' and NO_BUKTI > 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from so
						where PER ='$per'
						and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ' 
                        AND CBG = '$CBG'   
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
			$so = So::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$so = new So;
                $so->TGL = Carbon::now();
                $so->JTEMPO = Carbon::now();
                $so->RING = 'LOKAL';                
				
				
		 }

        $no_bukti = $so->NO_BUKTI;
        $soDetail = DB::table('sod')->where('NO_BUKTI', $no_bukti)->get();
		
		$data = [
            'header'        => $so,
			'detail'        => $soDetail

        ];
 
         
         return view('otransaksi_so.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' =>$this->FLAGZ, 'golz' =>$this->GOLZ, 'judul' => $this->judul ]);
			 
 
      
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 18

    public function update(Request $request, So $so)
    {

        $this->validate(
            $request,
            [
                'TGL'      => 'required',
            ]
        );

        $variablell = DB::select('call sodel(?)', array($so['NO_BUKTI']));


		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $so->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'              => date('Y-m-d', strtotime($request['JTEMPO'])),
                'KODEC'            => ($request['KODEC'] == null) ? "" : $request['KODEC'],
                'NAMAC'            => ($request['NAMAC'] == null) ? "" : $request['NAMAC'],
                'ALAMAT'            => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'            => ($request['KOTA'] == null) ? "" : $request['KOTA'],
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL_QTY2'        => (float) str_replace(',', '', $request['TTOTAL_QTY2']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
                'KOM'            => (float) str_replace(',', '', $request['KOM']),
                'TDPP'            => (float) str_replace(',', '', $request['TDPP']),
                'TPPN'            => (float) str_replace(',', '', $request['TPPN']),
                'NETT'            => (float) str_replace(',', '', $request['NETT']),
                'DISK_GLOBAL'            => (float) str_replace(',', '', $request['DISK_GLOBAL']),
                'TDISK'            => (float) str_replace(',', '', $request['TDISK']),
                'HARI'            => (float) str_replace(',', '', $request['HARI']),
                'KODEP'            => ($request['KODEP'] == null) ? "" : $request['KODEP'],
                'NAMAP'            => ($request['NAMAP'] == null) ? "" : $request['NAMAP'],
                'RING'            => ($request['RING'] == null) ? "" : $request['RING'],
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'updated_by'       => Auth::user()->username,
                'FLAG'             => 'SO',						
                'GOL'              => $GOLZ,
                'CBG'              => $CBG,
                'TOTAL_TKOM'       => (float) str_replace(',', '', $request['TOTAL_TKOM']),
                'LIM'              => (float) str_replace(',', '', $request['LIM']),
                'AK12'             => (float) str_replace(',', '', $request['AK12']),

                'NAMAC_2'          => ($request['NAMAC_2'] == null) ? "" : $request['NAMAC_2'],
                'ALAMAT_2'         => ($request['ALAMAT_2'] == null) ? "" : $request['ALAMAT_2'],
                'KOTA_2'           => ($request['KOTA_2'] == null) ? "" : $request['KOTA_2'],
                'NOTES2'            => ($request['NOTES2'] == null) ? "" : $request['NOTES2'],
            ]
        );

		$no_buktix = $so->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');

        $KD_BRG = $request->input('KD_BRG');
        $KD_GRUP = $request->input('KD_GRUP');
        $NA_BRG = $request->input('NA_BRG');
        $SATUAN = $request->input('SATUAN');		
        $XQTY    = $request->input('XQTY');
        $KALI    = $request->input('KALI');
        $QTY    = $request->input('QTY');
        $HARGA    = $request->input('HARGA');
        $TOTAL    = $request->input('TOTAL');
        $KET = $request->input('KET');			
        $PPNX = $request->input('PPNX');			
        $DPP = $request->input('DPP');	
        $DISK = $request->input('DISK');	
        $DISKA = $request->input('DISKA');	
        $DISKB = $request->input('DISKB');	
        $DISKC = $request->input('DISKC');	
        $DISKD = $request->input('DISKD');	
        $DISKE = $request->input('DISKE');	

        $AHARGA        = $request->input('AHARGA');  
        $BHARGA        = $request->input('BHARGA');  
        $CHARGA        = $request->input('CHARGA');  
        $DHARGA        = $request->input('DHARGA');  
        $EHARGA        = $request->input('EHARGA');  
        $FHARGA        = $request->input('FHARGA');  
        $GHARGA        = $request->input('GHARGA');  
        $TYPE_KOM      = $request->input('TYPE_KOM');  
        $KOM           = $request->input('KOM');  
        $TKOM          = $request->input('TKOM');  
        $LOKASI          = $request->input('LOKASI');  
        $BERAT          = $request->input('BERAT');  
        $TBERAT          = $request->input('TBERAT'); 

        $XSO          = $request->input('XSO');  
        $SEDIA          = $request->input('SEDIA');  
        
        $query = DB::table('sod')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = SoDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'KD_GRUP'     => ($KD_GRUP[$i] == null) ? "" :  $KD_GRUP[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'SATUAN'     => ($SATUAN[$i] == null) ? "" :  $SATUAN[$i],						
                        'QTY2'        => (float) str_replace(',', '', $XQTY[$i]),
                        'KALI'        => (float) str_replace(',', '', $KALI[$i]),
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'SISA'        => (float) str_replace(',', '', $QTY[$i]),
                        'SISA2'        => (float) str_replace(',', '', $XQTY[$i]),
                        
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),
                        'PPN'      => (float) str_replace(',', '', $PPNX[$i]),
                        'DPP'      => (float) str_replace(',', '', $DPP[$i]),
                        'DISK'      => (float) str_replace(',', '', $DISK[$i]),
                        'DISK1'      => (float) str_replace(',', '', $DISKA[$i]),
                        'DISK2'      => (float) str_replace(',', '', $DISKB[$i]),
                        'DISK3'      => (float) str_replace(',', '', $DISKC[$i]),
                        'DISK4'      => (float) str_replace(',', '', $DISKD[$i]),
                        'DISK5'      => (float) str_replace(',', '', $DISKE[$i]),

                        'AHARGA'      => (float) str_replace(',', '', $AHARGA[$i]),
                        'BHARGA'      => (float) str_replace(',', '', $BHARGA[$i]),
                        'CHARGA'      => (float) str_replace(',', '', $CHARGA[$i]),
                        'DHARGA'      => (float) str_replace(',', '', $DHARGA[$i]),
                        'EHARGA'      => (float) str_replace(',', '', $EHARGA[$i]),
                        'FHARGA'      => (float) str_replace(',', '', $FHARGA[$i]),
                        'GHARGA'      => (float) str_replace(',', '', $GHARGA[$i]),
                        'TYPE_KOM'    => ($TYPE_KOM[$i] == null) ? "" :  $TYPE_KOM[$i],	
                        'KOM'         => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'        => (float) str_replace(',', '', $TKOM[$i]),
                        'BERAT'        => (float) str_replace(',', '', $BERAT[$i]),
                        'TBERAT'        => (float) str_replace(',', '', $TBERAT[$i]),

                        'XSO'        => (float) str_replace(',', '', $XSO[$i]),
                        'SEDIA'        => (float) str_replace(',', '', $SEDIA[$i]),
                        
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],	
                        'LOKASI'        => ($LOKASI[$i] == null) ? "" :  $LOKASI[$i],	
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = SoDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'KD_GRUP'     => ($KD_GRUP[$i] == null) ? "" :  $KD_GRUP[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'SATUAN'     => ($SATUAN[$i] == null) ? "" :  $SATUAN[$i],							
                        'QTY2'        => (float) str_replace(',', '', $XQTY[$i]),
                        'KALI'        => (float) str_replace(',', '', $KALI[$i]),
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'SISA'        => (float) str_replace(',', '', $QTY[$i]),
                        'SISA2'        => (float) str_replace(',', '', $XQTY[$i]),
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'PER'        => $periode,

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

                        'AHARGA'      => (float) str_replace(',', '', $AHARGA[$i]),
                        'BHARGA'      => (float) str_replace(',', '', $BHARGA[$i]),
                        'CHARGA'      => (float) str_replace(',', '', $CHARGA[$i]),
                        'DHARGA'      => (float) str_replace(',', '', $DHARGA[$i]),
                        'EHARGA'      => (float) str_replace(',', '', $EHARGA[$i]),
                        'FHARGA'      => (float) str_replace(',', '', $FHARGA[$i]),
                        'GHARGA'      => (float) str_replace(',', '', $GHARGA[$i]),
                        'TYPE_KOM'    => ($TYPE_KOM[$i] == null) ? "" :  $TYPE_KOM[$i],	
                        'KOM'         => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'         => (float) str_replace(',', '', $TKOM[$i]),
                        'BERAT'         => (float) str_replace(',', '', $BERAT[$i]),
                        'TBERAT'         => (float) str_replace(',', '', $TBERAT[$i]),

                        'XSO'        => (float) str_replace(',', '', $XSO[$i]),
                        'SEDIA'        => (float) str_replace(',', '', $SEDIA[$i]),
                      
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],							
                        'LOKASI'        => ($LOKASI[$i] == null) ? "" :  $LOKASI[$i],							
                    ]
                );
            }
        }

 		$so = So::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $so->NO_BUKTI;

        DB::SELECT("UPDATE so, cust
                    SET so.NAMAC = cust.NAMAC, so.ALAMAT = cust.ALAMAT, so.KOTA = cust.KOTA, so.PKP=cust.PKP, so.HARI = cust.HARI  WHERE so.KODEC = cust.KODEC 
                    AND so.NO_BUKTI='$no_bukti';");


        DB::SELECT("UPDATE so,  sod
                    SET  sod.ID =  so.NO_ID  WHERE  so.NO_BUKTI =  sod.NO_BUKTI 
                    AND  so.NO_BUKTI='$no_bukti';");

        $variablell = DB::select('call soins(?)', array($so['NO_BUKTI']));
					 
        // return redirect('/so/edit/?idx=' . $so->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');	
        return redirect('/so?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, So $so)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('so')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
		
        $variablell = DB::select('call sodel(?)', array($so['NO_BUKTI']));

		
        $deleteSo = So::find($so->NO_ID);

        $deleteSo->delete();

       return redirect('/so?flagz='.$FLAGZ.'&golz='.$GOLZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])->with('statusHapus', 'Data '.$so->NO_BUKTI.' berhasil dihapus');


    }
    
    
    
    public function cetak(Request $request, So $so)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
        $no_so = $so->NO_BUKTI;

        $file     = 'soc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT so.NO_BUKTI, so.TGL, if(so.GOL='DR', '' , so.KODEC) AS KODEC, 
                                    if(so.GOL='DR', so.NAMAC_2, so.NAMAC) AS NAMAC, 
                                    so.TOTAL_QTY, so.NOTES, if(so.GOL='DR', so.ALAMAT_2, so.ALAMAT) AS ALAMAT, 
                                    if(so.GOL='DR', so.KOTA_2, so.KOTA) AS KOTA, sod.KD_BRG, sod.NA_BRG, sod.SATUAN, sod.QTY2 as QTY, 
                                    sod.HARGA, sod.TOTAL, sod.KET, so.TPPN, so.NETT,
                                    so.JTEMPO, so.TDPP, so.TDISK, sod.DISK
                            FROM so, sod 
                            WHERE so.NO_BUKTI='$no_so' AND so.NO_BUKTI = sod.NO_BUKTI 
                            ;
		");

        
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'TGL_CETAK' => NOW(),
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
                'QTY'    => $query[$key]->QTY,
                'PPN'    => $query[$key]->TPPN,
                'TDPP'    => $query[$key]->TDPP,
                'TDISK'    => $query[$key]->TDISK,
                'DISK'    => $query[$key]->DISK,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
        DB::SELECT("UPDATE so SET POSTED = 1 WHERE NO_BUKTI='$no_so';");
    }
	
	
	
	 public function posting(Request $request)
    {
      

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
	
	public function getDetailSo(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('sod')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
	
	
}
