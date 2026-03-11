<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Jual;
use App\Models\OTransaksi\JualDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class JualController extends Controller
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
        if ( $request->flagz == 'JL' && $request->golz == 'B' ) {
            $this->judul = "Penjualan Bahan Baku";
        } else if ( $request->flagz == 'AJ' && $request->golz == 'B' ) {
            $this->judul = "Retur Penjualan Bahan Baku";
        } else if ( $request->flagz == 'JL' && $request->golz == 'J' ) {
            $this->judul = "Penjualan Barang";
        } else if ( $request->flagz == 'AJ' && $request->golz == 'J' ) {
            $this->judul = "Retur Penjualan Barang";
        } else if ( $request->flagz == 'JL' && $request->golz == 'D' ) {
            $this->judul = "Penjualan Dropship";
        } else if ( $request->flagz == 'AJ' && $request->golz == 'D' ) {
            $this->judul = "Retur Penjualan Dropship";
        }
		
        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;


    }
       	
    public function index(Request $request)
    {

	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_jual.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ ]);
	
	
    }

	public function post(Request $request)
    {
 
        return view('otransaksi_jual.post');
    }

    public function browse(Request $request)
    {
        $golz = $request->GOL;

		$CBG = Auth::user()->CBG;
		$PPN = Auth::user()->PPN;

        $jual = DB::SELECT("SELECT distinct jual.NO_BUKTI, jual.NO_SO, jual.KODEC, jual.NAMAC, 
		                  jual.ALAMAT, jual.KOTA, jual.PKP from jual, juald 
                          WHERE jual.NO_BUKTI = juald.NO_BUKTI AND jual.GOL ='$golz' AND jual.FLAG ='JL'
                          AND jual.CBG = '$CBG' 
                          ");
        return response()->json($jual);
    }

    public function browse_juald(Request $request)
    {

        // $filterbukti = '';
        // if($request->NO_SO)
        // {

        //     $filterbukti = " WHERE NO_BUKTI='".$request->NO_SO."' ";
        // }
        $sod = DB::SELECT("SELECT REC, SATUAN , QTY, HARGA, TOTAL, KET, 
                                KD_BRG, NA_BRG, DPP, PPN, QTY_KIRIM, DISK, NO_SO, TYPE_KOM, KOM, TKOM, QTY2, KALI
                            from juald
                            where NO_BUKTI='".$request->nobukti."' ORDER BY NO_BUKTI ");
	

		return response()->json($sod);
	}

    public function browseuang(Request $request)
    {

		$filterkodec = '';
	   
		$CBG = Auth::user()->CBG;

		if($request->KODEC)
		{
	
			$filterkodec = " AND KODEC='".$request->KODEC."' ";
		}
		
		$jual = DB::SELECT("SELECT NO_BUKTI, TGL, KODEC, 
                                    NAMAC, NETT AS TOTAL, BAYAR, SISA, 
						(SELECT SUM(TOTAL_TKOM) FROM jual ) AS TOTAL_TKOM 
                        from jual 
                        WHERE CBG = '$CBG'  AND SISA <> 0
                        $filterkodec
                        ORDER BY NO_BUKTI ");
 
        return response()->json($jual);
    }

    public function browse_faktur(Request $request)
    {


		$cari = $request->CARI;
		
		if ($cari == ''){
			
            $faktur = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NO_SURATS, NAMAC, NETT, NO_FP, TGL_FP
                                        FROM jual
                                        WHERE NO_FP ='' ");
							
        } else if ($cari != ''){
			
            $faktur = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NO_SURATS, NAMAC, NETT, NO_FP, TGL_FP
                                        FROM jual
                                        WHERE NO_BUKTI = '$cari' ");
        } 

        return response()->json($faktur);
    }
	
    // ganti 4

    public function getJual(Request $request)
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

        $jual = DB::SELECT("SELECT * from jual  where PER = '$periode' and FLAG ='$this->FLAGZ' 
                            AND GOL ='$this->GOLZ' AND CBG='$CBG'  ORDER BY NO_BUKTI ");
	   
        // ganti 6

        return Datatables::of($jual)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ( Auth::user()->divisi=="programmer" ) 
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("jual/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="jual/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';					
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="jsjualc/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a> 									
                                <hr></hr>
                                <a class="dropdown-item btn btn-danger"  ' . $btnDelete . '>
   
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

	
			
			



//////////////////////////////////////////////////////////////////////////////////


	
			

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

        $kodecx = $request->KODEC;
        
        $xxx= DB::table('cust')->select('PKP')->where('KODEC', $kodecx)->get();

        $PPN = $xxx[0]->PKP ;
        
        

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('jual')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ )
                ->where('CBG', $CBG)->where('PKP', $PPN)->where('GOL', $GOLZ)->orderByDesc('NO_BUKTI')->limit(1)->get();


        if( $FLAGZ=='JL' ){

            if( $GOLZ=='B'){

                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JL' . 'Y' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JL' . 'Y' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JL'  . 'Z'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JL'  . 'Z'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            } elseif($GOLZ=='J') {
    
    
                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JY' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JY' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JZ'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JZ'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            } elseif($GOLZ=='D') {
    
    
                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JD' . 'Y' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JD' . 'Y' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'JD'  . 'Z'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'JD'  . 'Z'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            }


        } elseif($FLAGZ=='AJ'){

            if( $GOLZ=='B'){

                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AY' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AY' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AZ'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AZ'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            } elseif($GOLZ=='J') {
    
    
                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AY' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AY' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AZ'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AZ'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            } elseif($GOLZ=='D') {
    
    
                if( $PPN =='1' ){
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AD' . 'Y' . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AD' . 'Y' . $CBG . $tahun . $bulan . '-0001';
                    }
     
                } else {
    
                    if ($query != '[]') {
                        $query = substr($query[0]->NO_BUKTI, -4);
                        $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                        $no_bukti = 'AD'  . 'Z'  . $CBG . $tahun . $bulan . '-' . $query;
                    } else {
                        $no_bukti = 'AD'  . 'Z'  . $CBG . $tahun . $bulan . '-0001';
                    }
                    
                }
    
            }

        }
        

        
		
//////////////////////////////////////////////////////////////////////////
       

        // Insert Header

        // ganti 10

        $jual = Jual::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'              => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
                'FLAG'             => $FLAGZ,						
                'GOL'              => $GOLZ,			
                // 'NO_SO'            => ($request['NO_SO'] == null) ? "" : $request['NO_SO'],
                'NO_JUAL'            => ($request['NO_JUAL'] == null) ? "" : $request['NO_JUAL'],
                'NO_SURATS'            => ($request['NO_SURATS'] == null) ? "" : $request['NO_SURATS'],
 
                'KODEC'            => ($request['KODEC'] == null) ? "" : $request['KODEC'],
                'NAMAC'            => ($request['NAMAC'] == null) ? "" : $request['NAMAC'],
                'ALAMAT'           => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'             => ($request['KOTA'] == null) ? "" : $request['KOTA'],

                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TYPE'            => ($request['TYPE'] == null) ? "" : $request['TYPE'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL_QTY2'        => (float) str_replace(',', '', $request['TOTAL_QTY2']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
                'TDPP'            => (float) str_replace(',', '', $request['TDPP']),
                'TPPN'            => (float) str_replace(',', '', $request['TPPN']),
                'NETT'            => (float) str_replace(',', '', $request['NETT']),
                'TDISK'            => (float) str_replace(',', '', $request['TDISK']),
                'SISA'            => (float) str_replace(',', '', $request['NETT']),
	   
                'KODEP'            => ($request['KODEP'] == null) ? "" : $request['KODEP'],
                'NAMAP'            => ($request['NAMAP'] == null) ? "" : $request['NAMAP'],
                'RING'            => ($request['RING'] == null) ? "" : $request['RING'],
                'KOM'            => (float) str_replace(',', '', $request['KOM']),
                'HARI'            => (float) str_replace(',', '', $request['HARI']),
                // 'PKP'            => (float) str_replace(',', '', $request['PKP']),
                'TOTAL_TKOM'            => (float) str_replace(',', '', $request['TOTAL_TKOM']),
                'DISKG'            => (float) str_replace(',', '', $request['DISKG']),

                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
                'CBG'              => $CBG,
                'TGL_FP'           => date('Y-m-d', strtotime($request['TGL_FP'])),
                'NO_FP'            => ($request['NO_FP'] == null) ? "" : $request['NO_FP'],
                'DISK_GLOBAL'             => (float) str_replace(',', '', $request['DISK_GLOBAL']),

                //'PKP'              => $PPN,
            ]
        );


		$REC        = $request->input('REC');
		$KD_BHN     = $request->input('KD_BHN');
        $NA_BHN     = $request->input('NA_BHN');
		$NO_SO     = $request->input('NO_SO');
		$KD_BRG     = $request->input('KD_BRG');
        $NA_BRG     = $request->input('NA_BRG');
        $SATUAN     = $request->input('SATUAN');
        $KET     = $request->input('KET');
        $XQTY        = $request->input('XQTY');
        $KALI        = $request->input('KALI');
        $QTY        = $request->input('QTY');
        $QTY_KIRIM        = $request->input('QTY_KIRIM');
        $HARGA        = $request->input('HARGA');
        $PPNX        = $request->input('PPNX');
        $DPP        = $request->input('DPP');
        $DISK        = $request->input('DISK'); 
        $DISKA        = $request->input('DISKA');  
        $DISKB        = $request->input('DISKB');  
        $DISKC        = $request->input('DISKC');  
        $DISKD        = $request->input('DISKD');  
        $DISKE        = $request->input('DISKE');  
	    $TOTAL        = $request->input('TOTAL');		 
	    $TYPE_KOM        = $request->input('TYPE_KOM');		 
	    $KOM        = $request->input('KOM');		 
	    $TKOM        = $request->input('TKOM');		 

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new jualdetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;		
                $detail->GOL 	     = $GOLZ;  	
                $detail->CBG 	     = $CBG;               
                $detail->KD_BHN      = ($KD_BHN[$key] == null) ? "" :  $KD_BHN[$key];
                $detail->NA_BHN      = ($NA_BHN[$key] == null) ? "" :  $NA_BHN[$key];          
                $detail->NO_SO      = ($NO_SO[$key] == null) ? "" :  $NO_SO[$key];
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->SATUAN      = ($SATUAN[$key] == null) ? "" :  $SATUAN[$key];				
                $detail->KET      = ($KET[$key] == null) ? "" :  $KET[$key];				
                $detail->QTY2         = (float) str_replace(',', '', $XQTY[$key]);
                $detail->KALI         = (float) str_replace(',', '', $KALI[$key]);
                $detail->QTY         = (float) str_replace(',', '', $QTY[$key]);
                $detail->QTY_KIRIM         = (float) str_replace(',', '', $QTY_KIRIM[$key]);
                $detail->HARGA         = (float) str_replace(',', '', $HARGA[$key]);
                $detail->PPN         = (float) str_replace(',', '', $PPNX[$key]);
                $detail->DPP         = (float) str_replace(',', '', $DPP[$key]);
                $detail->DISK         = (float) str_replace(',', '', $DISK[$key]);
                $detail->DISK1       = (float) str_replace(',', '', $DISKA[$key]); 
                $detail->DISK2       = (float) str_replace(',', '', $DISKB[$key]); 
                $detail->DISK3       = (float) str_replace(',', '', $DISKC[$key]); 
                $detail->DISK4       = (float) str_replace(',', '', $DISKD[$key]); 
                $detail->DISK5       = (float) str_replace(',', '', $DISKE[$key]); 

                $detail->TOTAL         = (float) str_replace(',', '', $TOTAL[$key]);				
                $detail->TYPE_KOM      = ($TYPE_KOM[$key] == null) ? "" :  $TYPE_KOM[$key];				
                $detail->KOM         = (float) str_replace(',', '', $KOM[$key]);				
                $detail->TKOM         = (float) str_replace(',', '', $TKOM[$key]);				
 		
                $detail->save();
            }
        }


        //  ganti 11
       $variablell = DB::select('call jualins(?)', array($no_bukti));

        $no_buktix = $no_bukti;
		
		$jual = Jual::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE jual, cust
                    SET jual.NAMAC = cust.NAMAC, jual.ALAMAT = cust.ALAMAT, jual.KOTA = cust.KOTA, jual.PKP=cust.PKP, jual.HARI = cust.HARI  WHERE jual.KODEC = cust.KODEC 
                    AND jual.NO_BUKTI='$no_bukti';");
                    
                    

        DB::SELECT("UPDATE jual, cust
                        SET jual.KODEP = cust.KODEP, jual.NAMAP = cust.NAMAP
                        WHERE jual.KODEC = cust.KODEC 
                        AND jual.NO_BUKTI='$no_buktix';");



        DB::SELECT("UPDATE jual, juald
                            SET juald.ID = jual.NO_ID  WHERE jual.NO_BUKTI = juald.NO_BUKTI 
							AND jual.NO_BUKTI='$no_buktix';");
					 
					 
        // return redirect('/jual/edit/?idx=' . $jual->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/jual?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);

    }


	
	public function edit_isifaktur()
    {
        return view('otransaksi_jual.edit_isifaktur');
    }

   public function edit( Request $request , Jual $jual)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect('/jual')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from jual
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ' 
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from jual
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ'   
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from jual     
		             where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ' 
                         AND CBG = '$CBG'
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from jual    
		                    where PER ='$per' and FLAG ='$this->FLAGZ'
                            and GOL ='$this->GOLZ' 
                            AND CBG = '$CBG'
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from jual
						 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ' 
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
			$jual = Jual::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$jual = new Jual;
                $jual->TGL = Carbon::now();
                $jual->JTEMPO = Carbon::now();
      
				
		 }

        $no_bukti = $jual->NO_BUKTI;
	    $jualdetail = DB::table('juald')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();	
		
		$data = [
            'header'        => $jual,
            'detail'        => $jualdetail,
			
        ];
 
         
      
         return view('otransaksi_jual.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ, 'judul'=> $this->judul ]);
      	 
 
 
    }

    // ganti 18

    public function update(Request $request, Jual $jual)
    {

        $this->validate(
            $request,
            [

                // ganti 19

                'TGL'      => 'required'


            ]
        );

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        // ganti 20
        $variablell = DB::select('call jualdel(?)', array($jual['NO_BUKTI']));

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $jual->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'              => date('Y-m-d', strtotime($request['JTEMPO'])),
                // 'NO_SO'            => ($request['NO_SO'] == null) ? "" : $request['NO_SO'],
                'NO_SURATS'            => ($request['NO_SURATS'] == null) ? "" : $request['NO_SURATS'],
                'NO_JUAL'            => ($request['NO_JUAL'] == null) ? "" : $request['NO_JUAL'],
 
                'KODEC'            => ($request['KODEC'] == null) ? "" : $request['KODEC'],
                'NAMAC'            => ($request['NAMAC'] == null) ? "" : $request['NAMAC'],
                'ALAMAT'           => ($request['ALAMAT'] == null) ? "" : $request['ALAMAT'],
                'KOTA'             => ($request['KOTA'] == null) ? "" : $request['KOTA'],


                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TYPE'            => ($request['TYPE'] == null) ? "" : $request['TYPE'],
                'TOTAL_QTY'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'TOTAL_QTY2'        => (float) str_replace(',', '', $request['TTOTAL_QTY2']),
                'TOTAL'            => (float) str_replace(',', '', $request['TTOTAL']),
                'TDPP'            => (float) str_replace(',', '', $request['TDPP']),
                'TPPN'            => (float) str_replace(',', '', $request['TPPN']),
                'NETT'            => (float) str_replace(',', '', $request['NETT']),
                'SISA'            => (float) str_replace(',', '', $request['NETT']),
                'TDISK'            => (float) str_replace(',', '', $request['TDISK']),
	   
                'KODEP'            => ($request['KODEP'] == null) ? "" : $request['KODEP'],
                'NAMAP'            => ($request['NAMAP'] == null) ? "" : $request['NAMAP'],
                'RING'            => ($request['RING'] == null) ? "" : $request['RING'],
                'KOM'            => (float) str_replace(',', '', $request['KOM']),
                'HARI'            => (float) str_replace(',', '', $request['HARI']),
                // 'PKP'            => (float) str_replace(',', '', $request['PKP']),
                'TOTAL_TKOM'            => (float) str_replace(',', '', $request['TOTAL_TKOM']),
                'DISKG'            => (float) str_replace(',', '', $request['DISKG']),

				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'updated_by'       => Auth::user()->username,
                'CBG'              => $CBG,
                'FLAG'             => $FLAGZ,						
                'GOL'              => $GOLZ,
                'TGL_FP'              => date('Y-m-d', strtotime($request['TGL_FP'])),
                'NO_FP'            => ($request['NO_FP'] == null) ? "" : $request['NO_FP'],
                'DISK_GLOBAL'            => (float) str_replace(',', '', $request['DISK_GLOBAL']),
                //'PKP'              => $PPN,
            ]
        );


		$no_buktix = $jual->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');
        $KD_BHN = $request->input('KD_BHN');
        $NA_BHN = $request->input('NA_BHN');
        $NO_SO = $request->input('NO_SO');
        $KD_BRG = $request->input('KD_BRG');
        $NA_BRG = $request->input('NA_BRG');
        $SATUAN = $request->input('SATUAN');		
        $KET = $request->input('KET');		
        $XQTY    = $request->input('XQTY');
        $KALI    = $request->input('KALI');
        $QTY    = $request->input('QTY');
        $QTY_KIRIM    = $request->input('QTY_KIRIM');
        $HARGA    = $request->input('HARGA');
        $PPNX    = $request->input('PPNX');
        $DPP    = $request->input('DPP');
        $DISK    = $request->input('DISK');
        $DISKA = $request->input('DISKA');	
        $DISKB = $request->input('DISKB');	
        $DISKC = $request->input('DISKC');	
        $DISKD = $request->input('DISKD');	
        $DISKE = $request->input('DISKE');	

        $TOTAL    = $request->input('TOTAL');	
        $TYPE_KOM    = $request->input('TYPE_KOM');	
        $KOM    = $request->input('KOM');	
        $TKOM    = $request->input('TKOM');	

        $query = DB::table('juald')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = jualdetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'KD_BHN'     => ($KD_BHN[$i] == null) ? "" :  $KD_BHN[$i],
                        'NA_BHN'     => ($NA_BHN[$i] == null) ? "" :  $NA_BHN[$i],
                        'NO_SO'     => ($NO_SO[$i] == null) ? "" :  $NO_SO[$i],
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'SATUAN'     => ($SATUAN[$i] == null) ? "" :  $SATUAN[$i],						
                        'KET'     => ($KET[$i] == null) ? "" :  $KET[$i],						
                        'QTY2'        => (float) str_replace(',', '', $XQTY[$i]),
                        'KALI'        => (float) str_replace(',', '', $KALI[$i]),
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'QTY_KIRIM'        => (float) str_replace(',', '', $QTY_KIRIM[$i]),
                        'HARGA'        => (float) str_replace(',', '', $HARGA[$i]),
                        'PPN'        => (float) str_replace(',', '', $PPNX[$i]),
                        'DPP'        => (float) str_replace(',', '', $DPP[$i]),
                        'DISK'        => (float) str_replace(',', '', $DISK[$i]),
                        'DISK1'      => (float) str_replace(',', '', $DISKA[$i]),
                        'DISK2'      => (float) str_replace(',', '', $DISKB[$i]),
                        'DISK3'      => (float) str_replace(',', '', $DISKC[$i]),
                        'DISK4'      => (float) str_replace(',', '', $DISKD[$i]),
                        'DISK5'      => (float) str_replace(',', '', $DISKE[$i]),

                        'TOTAL'        => (float) str_replace(',', '', $TOTAL[$i]),
                        'TYPE_KOM'     => ($TYPE_KOM[$i] == null) ? "" :  $TYPE_KOM[$i],						
                        'KOM'        => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'        => (float) str_replace(',', '', $TKOM[$i]),
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = jualdetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],
                      
                        'KD_BHN'     => ($KD_BHN[$i] == null) ? "" :  $KD_BHN[$i],
                        'NA_BHN'     => ($NA_BHN[$i] == null) ? "" :  $NA_BHN[$i],
                        'NO_SO'     => ($NO_SO[$i] == null) ? "" :  $NO_SO[$i],
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'SATUAN'     => ($SATUAN[$i] == null) ? "" :  $SATUAN[$i],							
                        'QTY2'        => (float) str_replace(',', '', $XQTY[$i]),
                        'KALI'        => (float) str_replace(',', '', $KALI[$i]),					
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],						
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'QTY_KIRIM'        => (float) str_replace(',', '', $QTY_KIRIM[$i]),
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

                        'TYPE_KOM'     => ($TYPE_KOM[$i] == null) ? "" :  $TYPE_KOM[$i],						
                        'KOM'        => (float) str_replace(',', '', $KOM[$i]),
                        'TKOM'        => (float) str_replace(',', '', $TKOM[$i]),
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'PER'        => $periode,
                        'CBG'        => $CBG,
                    ]
                );
            }
        }


        //  ganti 21
        $variablell = DB::select('call jualins(?)', array($jual['NO_BUKTI']));

 		$jual = jual::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $jual->NO_BUKTI;


        DB::SELECT("UPDATE jual, cust
                    SET jual.NAMAC = cust.NAMAC, jual.ALAMAT = cust.ALAMAT, jual.KOTA = cust.KOTA, jual.PKP=cust.PKP, jual.HARI = cust.HARI  WHERE jual.KODEC = cust.KODEC 
                    AND jual.NO_BUKTI='$no_bukti';");

        DB::SELECT("UPDATE jual, cust
                        SET jual.KODEP = cust.KODEP, jual.NAMAP = cust.NAMAP
                        WHERE jual.KODEC = cust.KODEC 
                        AND jual.NO_BUKTI='$no_buktix';");


         DB::SELECT("UPDATE jual,  juald
                     SET  juald.ID =  jual.NO_ID  WHERE  jual.NO_BUKTI =  juald.NO_BUKTI 
                     AND  jual.NO_BUKTI='$no_bukti';");
					 
        // return redirect('/jual/edit/?idx=' . $jual->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');	
        return redirect('/jual?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
	
	
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Jual $jual)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED AS POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('jual')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
		
       $variablell = DB::select('call jualdel(?)', array($jual['NO_BUKTI']));//


        // ganti 23
		
        $deletejual = jual::find($jual->NO_ID);

        // ganti 24

        $deletejual->delete();

        // ganti 
       return redirect('/jual?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ ])->with('statusHapus', 'Data '.$jual->NO_BUKTI.' berhasil dihapus');


    }
    
    public function jsjualc(Jual $jual)
    {
        $no_jual = $jual->NO_BUKTI;
		
		
		$pkp = $jual->PKP;
		
		if ( $pkp =='1' )
		{
          $file     = 'jualc';
		}
		else
		{
          $file     = 'jualc2';		    
		}
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
            SELECT jual.NO_BUKTI, jual.TGL, juald.KD_BRG, juald.NA_BRG,  jual.TGL, jual.USRNM, 
            jual.TPPN, jual.TDPP, jual.NETT,
			juald.SATUAN, juald.QTY, juald.HARGA, juald.DISK, juald.TOTAL, jual.KODEC, jual.NAMAC,
            jual.ALAMAT, jual.KOTA, jual.JTEMPO, juald.QTY2
			from jual, juald 
			WHERE jual.NO_BUKTI=juald.NO_BUKTI and jual.NO_BUKTI='$no_jual'
			ORDER BY jual.NO_BUKTI;
		");

        $data = [];

        $rec=1;
        $kdbrg = '';
        $nabrg = '';
        foreach ($query as $key => $value) {

            array_push($data, array(
                'NO_BUKTI' => $no_jual,
                // 'TGL'      => date("d/m/Y", strtotime($jual->TGL)),
                'TGL'      => $query[$key]->TGL,               
                'TGL_CETAK'   => NOW(),               
                'JTEMPO'      => $query[$key]->JTEMPO,               
                'REC'      => $rec,
                'KD_BRG'   => $query[$key]->KD_BRG,
                'NA_BRG'   => $query[$key]->NA_BRG,
                'QTY'      => $query[$key]->QTY2,				
				'SATUAN'    => $query[$key]->SATUAN,
		    	'USRNM'    => $query[$key]->USRNM,
		    	'HARGA'    => $query[$key]->HARGA,
		    	'DISK'    => $query[$key]->DISK,
		    	'TOTAL'    => $query[$key]->TOTAL,
		    	'DPP'    => $query[$key]->TDPP,
		    	'PPN'    => $query[$key]->TPPN,
		    	'NETT'    => $query[$key]->NETT,
		    	'KODEC'    => $query[$key]->KODEC,
		    	'NAMAC'    => $query[$key]->NAMAC,
		    	'ALAMAT'    => $query[$key]->ALAMAT,
		    	'KOTA'    => $query[$key]->KOTA,



            ));
            $rec++;
        }
	
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
        DB::SELECT("UPDATE jual SET POSTED = 1 WHERE jual.NO_BUKTI='$no_jual';");
    }
	
	
	
    public function getDetailjual(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('juald')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	function posting (Request $request, Jual $jual)
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

		
		return redirect('/jual/post')->with('statusInsert', 'No Bukti berhasil diupdate');		
	
		
		
	}
	
	
	
}
