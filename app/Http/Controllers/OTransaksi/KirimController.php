<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Kirim;
use App\Models\OTransaksi\KirimDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class KirimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Reskirimnse
     */
	 
    var $judul = '';
    var $FLAGZ = '';
    var $GOLZ = '';
	
    function setFlag(Request $request)
    {
        if ( $request->flagz == 'KR' && $request->golz == 'KO' ) {
            $this->judul = "Pelayanan Outlet";
        } else if ( $request->flagz == 'RB' && $request->golz == 'B' ) {
            $this->judul = "Retur Pemkiriman Bahan Baku";
        } else if ( $request->flagz == 'BL' && $request->golz == 'J' ) {
            $this->judul = "Pemkiriman Barang";
        } else if ( $request->flagz == 'RB' && $request->golz == 'J' ) {
            $this->judul = "Retur Pemkiriman Barang";
        } else if ( $request->flagz == 'BL' && $request->golz == 'N' ) {
            $this->judul = "Pemkiriman Non";
        } else if ( $request->flagz == 'RB' && $request->golz == 'N' ) {
            $this->judul = "Retur Pemkiriman Non";
        } 
		
        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;
        


    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_kirim.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ , 'golz' => $this->GOLZ]);
	
		
    }
	

	public function index_posting(Request $request)
    {
 
        return view('otransaksi_kirim.post');
    }

    public function browse(Request $request)
    {
        $golz = $request->GOL;

		$CBG = Auth::user()->CBG;
		$PPN = Auth::user()->PPN;

        $kirim = DB::SELECT("SELECT distinct stocka.NO_BUKTI , stocka.KODES, stocka.NAMAS, 
		                  stocka.ALAMAT, stocka.KOTA, stocka.PKP, stocka.NO_PO, stocka.GUDANG from stocka, stockad 
                          WHERE stocka.NO_BUKTI = stockad.NO_BUKTI AND stocka.FLAG='BL' 
                          AND stocka.GOL ='$golz'
                          AND stocka.CBG = '$CBG'
                        --   AND stocka.PKP = '$PPN' 
                          ");
        return response()->json($kirim);
    }

    public function browse_kirimd(Request $request)
    {
        $golx = $request->GOL;

        $kirimd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.SISA, 
                            a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, a.PPN, a.DPP, a.DISK,
                            a.QTY2 AS XQTY, a.KALI
                        from stockad a, brg b 
                        where a.NO_BUKTI='".$request->nobukti."' AND a.KD_BRG = b.KD_BRG");

		return response()->json($kirimd);
	}
	
	
    public function browseuang(Request $request)
    {
        //	$kirim = DB::table('kirim')->select('NO_BUKTI', 'TGL', 'KODES','NAMAS', 'ALAMAT','KOTA', 'PERB','PERBB', 'SISA' )->where('PERB', '<>' ,'PERBB')->where('LNS', '<>',1)->where('GOL', 'Y')->orderBy('KODES', 'ASC')->get();
        $filterkodes = '';
	   
		$CBG = Auth::user()->CBG;

		if($request->KODES)
		{
	
			// $filterkodes = " WHERE SISA <> 0 AND KODES='".$request->KODES."' ";
			$filterkodes = " AND  KODES='".$request->KODES."' ";
		}
		
		$kirim = DB::SELECT("SELECT NO_BUKTI, TGL, KODES, 
		            NAMAS, NETT as TOTAL, BAYAR, SISA from stocka  WHERE stocka.CBG = '$CBG' and SISA <> 0
		            $filterkodes 
                    ORDER BY NO_BUKTI ");
 
        return response()->json($kirim);
    }
	
    // ganti 4



    public function getKirim(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

		$this->setFlag($request);	
        
		$CBG = Auth::user()->CBG;
		$PPN = Auth::user()->PPN;

        $kirim = DB::SELECT("SELECT no_bukti, tgl, kodes, namas, no_po, total_qty, total, nett, usrnm, posted,outlet
                                    FROM BSTOCKA
                                    where PER = '$periode' AND CBG= '$CBG' AND FLAG= '$FLAG' and outlet= '$OUTLET' 
                            union all 
                            SELECT no_bukti, tgl, kodes, namas, no_po, total_qty, total, nett, usrnm, posted,outlet
                                    FROM BSTOCKAZ
                                    where PER = PER = '$periode' AND CBG= '$CBG' AND FLAG= '$FLAG' and outlet= '$OUTLET'
                                    order by NO_BUKTI
                          ");

	   
        // ganti 6

        return Datatables::of($kirim)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ( (Auth::user()->divisi=="programmer" ) || (Auth::user()->divisi=="gudang" ))
				{
                    //CEK POSTED di index dan edit
                    $url = "'".url("kirim/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";

                    // $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';					
                    if (Auth::user()->divisi == 'gudang') {
                        // khusus gudang, cek CETAK
                        $btnEdit = ($row->CETAK == 1)
                            ? ' onclick="alert(\'LPB ini sudah dicetak, tidak bisa edit.\')" href="#" '
                            : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    } else {
                        // user lain, tetap cek POSTED
                        $btnEdit = ($row->POSTED == 1)
                            ? ' onclick="alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" '
                            : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    }
                    
                    
                    // $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="return confirm(&quot; Apakah anda yakin ingin hapus? &quot;)" href="kirim/delete/' . $row->NO_ID . '/?flagz=' . $row->FLAG . '&golz=' . $row->GOL .'" ';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege = '
                            <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i> Edit
                            </a>';

                        if (Auth::user()->divisi != 'gudang') {
                            $btnPrivilege .= '
                                <a class="dropdown-item btn btn-danger" href="kirim/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </a>';
                        }

                        if (Auth::user()->divisi == 'gudang') {
                            $btnPrivilege .= '
                                <a class="dropdown-item btn btn-danger" href="kirim/cetak2/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print SPB
                                </a>';
                        }

                        $btnPrivilege .= '
                            <hr></hr>
                            <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>
                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                            </a>';
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


			
			
			
			
///            ->rawColumns(['action'])
 //           ->make(true);
//    }



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
                'TGL'      => 'required',
                'KODES'       => 'required'

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
 

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('stocka')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ )->where('GOL', $GOLZ )->where('CBG', $CBG)
                   ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($FLAGZ=='KR') {

            if( $GOLZ =='KO' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'KO'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'KO'  . $CBG . $tahun . $bulan . '-0001';
                }

            } elseif ( $GOLZ =='' ) {

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = ''  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = ''  . $CBG . $tahun . $bulan . '-0001';
                }
                
            } elseif ( $GOLZ =='' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = ''  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = ''  . $CBG . $tahun . $bulan . '-0001';
                }
                
            }

        } 
        

		
//////////////////////////////////////////////////////////////////////////
       

        // Insert Header

        // ganti 10

        $kirim = Kirim::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
				'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
				'NO_PO'            => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'REF'              => ($request['REF'] == null) ? "" : $request['REF'],
                'MARGIN'           => (float) str_replace(',', '', $request['MARGIN']),
                'ST_NOTA'          => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'ST_CNT'           => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'POT_PROM'         => (float) str_replace(',', '', $request['POT_PROM']),
                'KK_STS'           => ($request['KK_STS'] == null) ? "" : $request['KK_STS'],
                'BASIC'            => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'ST_PJK'           => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FORMAL'           => ($request['FORMAL'] == null) ? "" : $request['FORMAL'],
                'NOTA_KHS'         => ($request['NOTA_KHS'] == null) ? "" : $request['NOTA_KHS'],
                'FLAG'             => $FLAGZ,					
                'GOL'              => $GOLZ,					
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],				
                'BAYAR'            => (float) str_replace(',', '', $request['BAYAR']),
                'JUMLAH'           => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'              => (float) str_replace(',', '', $request['TDPP']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'NETT'             => (float) str_replace(',', '', $request['TNETT']),
				'PROM'             => (float) str_replace(',', '', $request['TPROM']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
                // 'CBG'              => $CBG,
                'CBG'            => ($request['CBG'] == null) ? "" : $request['CBG'],				
            
                ]
        );


		$REC        = $request->input('REC');
		$KD_BRG     = $request->input('KD_BRG');
        $BARCODE    = $request->input('BARCODE');
        $NA_BRG     = $request->input('NA_BRG');
        $JNS        = $request->input('JNS');
        $QTY        = $request->input('QTY');
        $HARGA      = $request->input('HARGA');
        $MARGIN     = $request->input('MARGIN');
        $DISKON1    = $request->input('DISKON1');
        $DISKON2    = $request->input('DISKON2');
        $DISKON3    = $request->input('DISKON3');
        $DISKON4    = $request->input('DISKON4');
        $TOTAL      = $request->input('TOTAL');		
        $HARGA_JL   = $request->input('HARGA_JL');		
        $BLT        = $request->input('BLT');	  

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new kirimdetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;		
                $detail->GOL         = $GOLZ;		
                $detail->CBG         = $CBG;		
               
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->BARCODE      = ($BARCODE[$key] == null) ? "" :  $BARCODE[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];			
                $detail->JNS      = ($JNS[$key] == null) ? "" :  $JNS[$key];			
                $detail->QTY         = (float) str_replace(',', '', $QTY[$key]);			
                $detail->HARGA         = (float) str_replace(',', '', $HARGA[$key]);
                $detail->MARGIN           = (float) str_replace(',', '', $MARGIN[$key]);			
                $detail->DISKON1      = (float) str_replace(',', '', $DISKON1[$key]);
                $detail->DISKON2       = (float) str_replace(',', '', $DISKON2[$key]);
                $detail->DISKON3       = (float) str_replace(',', '', $DISKON3X[$key]);
                $detail->DISKON4       = (float) str_replace(',', '', $DISKON4[$key]);
                $detail->TOTAL       = (float) str_replace(',', '', $TOTAL[$key]);
                $detail->HARGA_JL       = (float) str_replace(',', '', $HARGA_JL[$key]); 
                $detail->BLT       = (float) str_replace(',', '', $BLT[$key]); 	
                $detail->save();
            }
        }	
		
		


        //  ganti 11

		$no_buktix = $no_bukti;
		
		$kirim = Kirim::where('NO_BUKTI', $no_buktix )->first();



        DB::SELECT("UPDATE stocka, sup
                    SET stocka.NAMAS = sup.NAMAS, stocka.ALAMAT = sup.ALAMAT, stocka.KOTA = sup.KOTA, stocka.PKP=sup.PKP  WHERE stocka.KODES = sup.KODES 
                    AND stocka.NO_BUKTI='$no_buktix';");
                    

        DB::SELECT("UPDATE stocka,  stockad
                            SET  stockad.ID = stocka.NO_ID  WHERE  stocka.NO_BUKTI =  stockad.NO_BUKTI 
							AND  stocka.NO_BUKTI='$no_buktix';");

		

        $variablell = DB::select('call kirimins(?)', array($no_buktix));
       
        // return redirect('/kirim/edit/?idx=' . $kirim->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);

					
    }


    // ganti 15

   
   public function edit( Request $request , Kirim $kirim)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect('/kirim')
			       ->with('status', 'Maaf Periode sudah ditutup!')
                   ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ]);
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from stocka
		                 where PER ='$per' and FLAG ='$this->FLAGZ' 
                         and GOL ='$this->GOLZ' 
						 and NO_BUKTI = '$buktix'						 
		                 and CBG = '$CBG' 
                         and PKP = '$PPN'
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from stocka 
		                 where PER ='$per' 
						 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ'    
		                 and CBG = '$CBG' 
                         and PKP = '$PPN'
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from stocka     
		             where PER ='$per' 
					 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ'  and NO_BUKTI < 
					'$buktix' and CBG = '$CBG'
                    and PKP = '$PPN'
                    ORDER BY NO_BUKTI DESC LIMIT 1" );
			

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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from stocka    
		             where PER ='$per'  
					 and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ' and NO_BUKTI > 
					 '$buktix' and CBG = '$CBG'
                         and PKP = '$PPN'
                          ORDER BY NO_BUKTI ASC LIMIT 1" );
					 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from stocka
						where PER ='$per'
						and FLAG ='$this->FLAGZ' and GOL ='$this->GOLZ'   
		                and CBG = '$CBG' 
                         and PKP = '$PPN'
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
			$kirim = Kirim::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$kirim = new Kirim;
                $kirim->TGL = Carbon::now();
                $kirim->JTEMPO = Carbon::now();
				
				
		 }

        $no_bukti = $kirim->NO_BUKTI;
        $kirimdetail = DB::table('stockad')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $kirim,
			'detail'        => $kirimdetail

        ];
 
         
         return view('otransaksi_kirim.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' =>$this->FLAGZ, 'judul' => $this->judul, 'golz' => $this->GOLZ ]);
      
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, kirim $kirim)
    {

        $this->validate(
            $request,
            [

                // ganti 19

 //               'NO_PO'       => 'required',
                'TGL'      => 'required',
                'KODES'       => 'required'


            ]
        );

        // ganti 20
        $variablell = DB::select('call kirimdel(?)', array($kirim['NO_BUKTI']));

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $kirim->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
				'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
				'NO_PO'            => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'REF'              => ($request['REF'] == null) ? "" : $request['REF'],
                'MARGIN'           => (float) str_replace(',', '', $request['MARGIN']),
                'ST_NOTA'          => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'ST_CNT'           => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'POT_PROM'         => (float) str_replace(',', '', $request['POT_PROM']),
                'KK_STS'           => ($request['KK_STS'] == null) ? "" : $request['KK_STS'],
                'BASIC'            => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'ST_PJK'           => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FORMAL'           => ($request['FORMAL'] == null) ? "" : $request['FORMAL'],
                'NOTA_KHS'         => ($request['NOTA_KHS'] == null) ? "" : $request['NOTA_KHS'],
                'FLAG'             => $FLAGZ,					
                'GOL'              => $GOLZ,					
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],				
                'BAYAR'            => (float) str_replace(',', '', $request['BAYAR']),
                'JUMLAH'           => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'              => (float) str_replace(',', '', $request['TDPP']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'NETT'             => (float) str_replace(',', '', $request['TNETT']),
				'PROM'             => (float) str_replace(',', '', $request['TPROM']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
                // 'CBG'              => $CBG,
                'CBG'            => ($request['CBG'] == null) ? "" : $request['CBG'],				
            ]
        );

		$no_buktix = $kirim->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');

        $REC        = $request->input('REC');
		$KD_BRG     = $request->input('KD_BRG');
        $BARCODE    = $request->input('BARCODE');
        $NA_BRG     = $request->input('NA_BRG');
        $JNS        = $request->input('JNS');
        $QTY        = $request->input('QTY');
        $HARGA      = $request->input('HARGA');
        $MARGIN     = $request->input('MARGIN');
        $DISKON1    = $request->input('DISKON1');
        $DISKON2    = $request->input('DISKON2');
        $DISKON3    = $request->input('DISKON3');
        $DISKON4    = $request->input('DISKON4');
        $TOTAL      = $request->input('TOTAL');		
        $HARGA_JL   = $request->input('HARGA_JL');		
        $BLT        = $request->input('BLT');	

        $query = DB::table('stockad')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = kirimdetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'JNS'        => ($JNS[$i] == null) ? "" :  $JNS[$i],						
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'MARGIN'     => (float) str_replace(',', '', $MARGIN[$i]),
                        'DISKON1'    => (float) str_replace(',', '', $DISKON1[$i]),
                        'DISKON2'    => (float) str_replace(',', '', $DISKON2[$i]),
                        'DISKON3'    => (float) str_replace(',', '', $DISKON3[$i]),
                        'DISKON4'    => (float) str_replace(',', '', $DISKON4[$i]),			
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),				
                        'HARGA_JL'   => (float) str_replace(',', '', $HARGA_JL[$i]),
                        'BLT'        => (float) str_replace(',', '', $BLT[$i]),
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = kirimdetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'JNS'        => ($JNS[$i] == null) ? "" :  $JNS[$i],						
                        'QTY'        => (float) str_replace(',', '', $QTY[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'MARGIN'     => (float) str_replace(',', '', $MARGIN[$i]),
                        'DISKON1'    => (float) str_replace(',', '', $DISKON1[$i]),
                        'DISKON2'    => (float) str_replace(',', '', $DISKON2[$i]),
                        'DISKON3'    => (float) str_replace(',', '', $DISKON3[$i]),
                        'DISKON4'    => (float) str_replace(',', '', $DISKON4[$i]),			
                        'TOTAL'      => (float) str_replace(',', '', $TOTAL[$i]),				
                        'HARGA_JL'   => (float) str_replace(',', '', $HARGA_JL[$i]),
                        'BLT'        => (float) str_replace(',', '', $BLT[$i]),
                        'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,					
                    ]
                );
            }
        }


        //  ganti 21

 		$kirim = kirim::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $kirim->NO_BUKTI;


        DB::SELECT("UPDATE stocka, sup
                    SET stocka.NAMAS = sup.NAMAS, stocka.ALAMAT = sup.ALAMAT, stocka.KOTA = sup.KOTA, stocka.PKP=sup.PKP  WHERE stocka.KODES = sup.KODES 
                    AND stocka.NO_BUKTI='$no_buktix';");
                    

        DB::SELECT("UPDATE stocka,  stockad
                    SET  stockad.ID =  stocka.NO_ID  WHERE  stocka.NO_BUKTI =  stockad.NO_BUKTI 
                    AND  stocka.NO_BUKTI='$no_bukti';");

        $variablell = DB::select('call kirimins(?)', array($kirim['NO_BUKTI']));
        
        // return redirect('/kirim/edit/?idx=' . $kirim->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul .  '&golz=' . $this->GOLZ . '');	
        return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Kirim $kirim)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('kirim')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
		
		
       $variablell = DB::select('call kirimdel(?)', array($kirim['NO_BUKTI']));//


        // ganti 23
		
        $deletekirim = Kirim::find($kirim->NO_ID);

        // ganti 24

        $deletekirim->delete();

        // ganti 

       return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ ])->with('statusHapus', 'Data '.$kirim->NO_BUKTI.' berhasil dihapus');


    }

    public function batal_post(Request $request)
    {
        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;

        // Ambil array dari checkbox
        $ids = $request->input('batal_post'); 

        // Cek apakah ada ID yang dipilih
        if (!$ids || count($ids) === 0) {
            return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)
                ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
                ->with('status', 'Tidak ada data yang dipilih.');
        }

        // Ambil data yang sesuai ID dan masih POSTED = 1
        $postedData = DB::table('stocka')
            ->whereIn('NO_ID', $ids)
            ->where('POSTED', 1)
            ->get();

        // Jika semua data belum diposting (POSTED = 0), tampilkan pesan
        if ($postedData->isEmpty()) {
            return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)
                ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
                ->with('status', 'No Bukti yang dipilih belum terposting.');
        }

        // Ambil hanya ID yang POSTED = 1 untuk update
        $idsToUpdate = $postedData->pluck('NO_ID')->toArray();

        // Update ke database
        DB::table('stocka')
            ->whereIn('NO_ID', $idsToUpdate)
            ->update(['POSTED' => 0]);

        return redirect('/kirim?flagz='.$FLAGZ.'&golz='.$GOLZ)
            ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
            ->with('status', 'Berhasil batal posting.');
    }

    
    
    public function cetak(Kirim $kirim)
    {
        $no_kirim = $kirim->NO_BUKTI;

        $file     = 'kirimc';

        $flagz1 = $kirim->FLAG;
        $judul ='';
        
        if ( $flagz1 =='BL')
        {
                $judul ='Order Pemkiriman';
        
        }
        
        if ( $flagz1 =='RB')
        {
                $judul ='Retur Pemkiriman';    
        }
        
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT stocka.NO_BUKTI, stocka.TGL, stocka.KODES, stocka.NAMAS, stocka.TOTAL_QTY, stocka.NOTES, stocka.ALAMAT, 
                                    stocka.KOTA, stockad.KD_BRG, stockad.NA_BRG, stockad.SATUAN, stockad.QTY2 AS QTY, stockad.DISK,
                                    stockad.HARGA, stockad.TOTAL, stockad.KET, stocka.TPPN, stocka.NETT,
                                    stocka.NO_PO, stocka.USRNM, stockad.KALI, stocka.TDISK, stocka.TDPP, stockad.PPN, stockad.DPP
                            FROM stocka, stockad 
                            WHERE stocka.NO_BUKTI='$no_kirim' AND stocka.NO_BUKTI = stockad.NO_BUKTI 
                            ;
		");

                DB::SELECT("UPDATE stocka SET POSTED = 1 WHERE NO_BUKTI='$no_kirim';");
                
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
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
                'DISK'    => $query[$key]->DISK,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET,
                'NO_PO'    => $query[$key]->NO_PO,
                'JUDUL'    => $judul,
                'USRNM'    => $query[$key]->USRNM,
                'KALI'    => $query[$key]->KALI,
                'TPPN'    => $query[$key]->TPPN,
                'TDISK'    => $query[$key]->TDISK,
                'TDPP'    => $query[$key]->TDPP,
                'PPN'    => $query[$key]->PPN,
                'DPP'    => $query[$key]->DPP
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
    }
    
    public function cetak2 (Kirim $kirim)
    {
        $no_kirim = $kirim->NO_BUKTI;

        $file     = 'spbc';

        $flagz1 = $kirim->FLAG;
        $judul ='';
        
        if ( $flagz1 =='BL')
        {
                $judul ='Surat Penerimaan Barang';
        
        }
        
        if ( $flagz1 =='RB')
        {
                $judul ='Retur Pemkiriman';    
        }
        
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT stocka.NO_BUKTI, stocka.TGL, stocka.KODES, stocka.NAMAS, stocka.TOTAL_QTY, stocka.NOTES, stocka.ALAMAT, 
                                    stocka.KOTA, stockad.KD_BRG, stockad.NA_BRG, stockad.SATUAN, stockad.QTY2 AS QTY, stockad.DISK,
                                    stockad.HARGA, stockad.TOTAL, stockad.KET, stocka.TPPN, stocka.NETT,
                                    stocka.NO_PO, stocka.USRNM, stockad.KALI, stocka.TDISK, stocka.TDPP, stockad.PPN, stockad.DPP
                            FROM stocka, stockad 
                            WHERE stocka.NO_BUKTI='$no_kirim' AND stocka.NO_BUKTI = stockad.NO_BUKTI 
                            ;
		");

                DB::SELECT("UPDATE stocka SET CETAK = 1 WHERE NO_BUKTI='$no_kirim';");
                
        $data = [];

        foreach ($query as $key => $value) {
            array_push($data, array(
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
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
                'DISK'    => $query[$key]->DISK,
                'NETT'    => $query[$key]->NETT,
                'KET'    => $query[$key]->KET,
                'NO_PO'    => $query[$key]->NO_PO,
                'JUDUL'    => $judul,
                'USRNM'    => $query[$key]->USRNM,
                'KALI'    => $query[$key]->KALI,
                'TPPN'    => $query[$key]->TPPN,
                'TDISK'    => $query[$key]->TDISK,
                'TDPP'    => $query[$key]->TDPP,
                'PPN'    => $query[$key]->PPN,
                'DPP'    => $query[$key]->DPP
            ));
        }
		
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
       
    }
	
	 public function posting(Request $request)
    {
      

    }
	
	
	public function getDetailkirim(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('stockad')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
}
