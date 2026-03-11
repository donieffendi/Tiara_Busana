<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Harga;
use App\Models\OTransaksi\HargaDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class HargaController extends Controller
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
        if ( $request->flagz == 'HG' && $request->golz == 'BS' ) {
            $this->judul = "Pengajuan Harga Jual";
        } else if ( $request->flagz == 'HG' && $request->golz == 'LB' ) {
            $this->judul = "Pencetakan Label Harga";
        } 

        $this->FLAGZ = $request->flagz;
        $this->GOLZ = $request->golz;
        


    }
		
    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_harga.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ , 'golz' => $this->GOLZ]);
	
		
    }
	

	public function post (Request $request)
    {
        return view('otransaksi_harga.post');
    }
	
    public function browse_posting(Request $request)
    {


		$cari = $request->CARI;
		
		if ($cari == ''){
			
            $posting = DB::SELECT("SELECT NO_ID, BARCODE, NA_BRG, CNT, NCNT, HJUAL, KD_BRG
                                        FROM harga
                                        WHERE KD_BRG ='' AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
							
        } else if ($cari != ''){
			
            $posting = DB::SELECT("SELECT NO_ID, BARCODE, NA_BRG, CNT, NCNT, HJUAL, KD_BRG
                                        FROM harga
                                        WHERE KD_BRG = '$cari' AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
        } 

        return response()->json($posting);
    }



    public function getHarga(Request $request)
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

        $harga = DB::SELECT("SELECT no_bukti, tgl, kodes, namas,cnt,ncnt usrnm, posted
                            FROM bhrg
                            where PER = '$periode' and flag='$FLAGZ' ");

	   
        // ganti 6

        return Datatables::of($harga)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ( (Auth::user()->divisi=="programmer" ) || (Auth::user()->divisi=="gudang" ))
				{
                    //CEK POSTED di index dan edit
                    $url = "'".url("harga/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG . "&golz=" . $row->GOL)."'";

                    // $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';					
                    if (Auth::user()->divisi == 'gudang') {
                        // khusus gudang, cek CETAK
                        $btnEdit = ($row->CETAK == 1)
                            ? ' onclick="alert(\'LPB ini sudah dicetak, tidak bisa edit.\')" href="#" '
                            : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    } else {
                        // user lain, tetap cek POSTED
                        $btnEdit = ($row->POSTED == 1)
                            ? ' onclick="alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" '
                            : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    }
                    
                    
                    // $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="return confirm(&quot; Apakah anda yakin ingin hapus? &quot;)" href="harga/delete/' . $row->NO_ID . '/?flagz=' . $row->FLAG . '&golz=' . $row->GOL .'" ';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege = '
                            <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i> Edit
                            </a>';

                        if (Auth::user()->divisi != 'gudang') {
                            $btnPrivilege .= '
                                <a class="dropdown-item btn btn-danger" href="harga/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
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
                // 'KODES'       => 'required'

            ]
        );

        //////     nomer otomatis
        
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
 

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan    = session()->get('periode')['bulan'];
        $tahun    = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('harga')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', $FLAGZ )->where('GOL', $GOLZ )->where('CBG', $CBG)
                    ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($FLAGZ=='HG') {

            if( $GOLZ =='BS' ){

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'BS'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'BS'  . $CBG . $tahun . $bulan . '-0001';
                }

            } elseif ( $GOLZ =='LB' ) {

                if ($query != '[]') {
                    $query = substr($query[0]->NO_BUKTI, -4);
                    $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                    $no_bukti = 'LB'  . $CBG . $tahun . $bulan . '-' . $query;
                } else {
                    $no_bukti = 'LB'  . $CBG . $tahun . $bulan . '-0001';
                }
                
            } 

        } 


        // Insert Header

        // ganti 10

        $harga = Harga::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
				'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'HARGAJL'          => (float) str_replace(',', '', $request['THARGAJL']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'created_by'       => Auth::user()->username,
                'CBG'              => $CBG,
            ]
        );


		$REC        = $request->input('REC');
		$KD_BRG     = $request->input('KD_BRG');
        $BARCODE    = $request->input('BARCODE');
        $NA_BRG     = $request->input('NA_BRG');
        $JNS        = $request->input('JNS');
        $HARGAJL    = $request->input('HARGAJL');
        $HARGAKSR   = $request->input('HARGAKSR');
        $HARGA      = $request->input('HARGA');
        $KET        = $request->input('KET');  

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new hargadetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;		
                $detail->GOL         = $GOLZ;		
                $detail->CBG         = $CBG;		
               
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->BARCODE     = ($BARCODE[$key] == null) ? "" :  $BARCODE[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->JNS         = ($JNS[$key] == null) ? "" :  $JNS[$key];				
                $detail->HARGAJL     = (float) str_replace(',', '', $HARGAJL[$key]);			
                $detail->HARGAKSR    = (float) str_replace(',', '', $HARGAKSR[$key]);			
                $detail->HARGA       = (float) str_replace(',', '', $HARGA[$key]);
                $detail->KET         = ($KET[$key] == null) ? "" :  $KET[$key];				
                $detail->save();
            }
        }	

        //  ganti 11

		$no_buktix = $no_bukti;
		
		$harga = Harga::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE harga,  hargad
                            SET  hargad.ID = harga.NO_ID  WHERE  harga.NO_BUKTI =  hargad.NO_BUKTI 
							AND  harga.NO_BUKTI='$no_buktix';");

		

        $variablell = DB::select('call hargains(?)', array($no_buktix));
       
        // return redirect('/harga/edit/?idx=' . $harga->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/harga?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);

					
    }


    // ganti 15

   
   public function edit( Request $request , Harga $harga)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
		
				
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect('/harga')
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from harga
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
			

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from harga 
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from harga     
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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from harga    
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from harga
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
			$harga = Harga::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
				$harga = new Harga;
                $harga->TGL = Carbon::now();
                $harga->JTEMPO = Carbon::now();
				
				
		 }

        $no_bukti = $harga->NO_BUKTI;
        $hargadetail = DB::table('hargad')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();
		
		$data = [
            'header'        => $harga,
			'detail'        => $hargadetail

        ];
 
         
         return view('otransaksi_harga.edit', $data)
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

    public function update(Request $request, harga $harga)
    {

        $this->validate(
            $request,
            [
                'TGL'      => 'required',
                'KODES'       => 'required'


            ]
        );

        // ganti 20
        $variablell = DB::select('call hargadel(?)', array($harga['NO_BUKTI']));

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
        $CBG = Auth::user()->CBG;
		
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $harga->update(
            [
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
				'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'HARGAJL'          => (float) str_replace(',', '', $request['THARGAJL']),
                'FLAG'             => $FLAGZ,					
                'GOL'              => $GOLZ,					
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'updated_by'       => Auth::user()->username,
                'CBG'              => $CBG,
            ]
        );

		$no_buktix = $harga->NO_BUKTI;
		
        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');

        $KD_BRG     = $request->input('KD_BRG');
        $BARCODE    = $request->input('BARCODE');
        $NA_BRG     = $request->input('NA_BRG');
        $JNS        = $request->input('JNS');
        $HARGAJL    = $request->input('HARGAJL');
        $HARGAKSR   = $request->input('HARGAKSR');
        $HARGA      = $request->input('HARGA');
        $KET        = $request->input('KET'); 	

        $query = DB::table('hargad')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = hargadetail::create(
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
                        'HARGAJL'    => (float) str_replace(',', '', $HARGAJL[$i]),
                        'HARGAKSR'   => (float) str_replace(',', '', $HARGAKSR[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],	
						
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = hargadetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'JNS'        => ($JNS[$i] == null) ? "" :  $JNS[$i],						
                        'HARGAJL'    => (float) str_replace(',', '', $HARGAJL[$i]),
                        'HARGAKSR'   => (float) str_replace(',', '', $HARGAKSR[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],	
						'FLAG'       => $this->FLAGZ,
                        'GOL'        => $this->GOLZ,
                        'CBG'        => $CBG,						
                    ]
                );
            }
        }


        //  ganti 21

 		$harga = harga::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $harga->NO_BUKTI;


        DB::SELECT("UPDATE harga,  hargad
                    SET  hargad.ID =  harga.NO_ID  WHERE  harga.NO_BUKTI =  hargad.NO_BUKTI 
                    AND  harga.NO_BUKTI='$no_bukti';");

        $variablell = DB::select('call hargains(?)', array($harga['NO_BUKTI']));
        
        // return redirect('/harga/edit/?idx=' . $harga->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul .  '&golz=' . $this->GOLZ . '');	
        return redirect('/harga?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);
		
	   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Harga $harga)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ = $this->GOLZ;
        $judul = $this->judul;
		
		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED==1)
        {
            return redirect()->route('harga')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }
		
		
       $variablell = DB::select('call hargadel(?)', array($harga['NO_BUKTI']));//


        // ganti 23
		
        $deleteharga = Harga::find($harga->NO_ID);

        // ganti 24

        $deleteharga->delete();

        // ganti 

       return redirect('/harga?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ ])->with('statusHapus', 'Data '.$harga->NO_BUKTI.' berhasil dihapus');


    }
    
    
    public function cetak(Harga $harga)
    {
        $no_harga = $harga->NO_BUKTI;

        $file     = 'hargac';

        $flagz1 = $harga->FLAG;
        $judul ='';
        
        if ( $flagz1 =='BL')
        {
                $judul ='Order Pemhargaan';
        
        }
        
        if ( $flagz1 =='RB')
        {
                $judul ='Retur Pemhargaan';    
        }
        
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT harga.NO_BUKTI, harga.TGL, harga.KODES, harga.NAMAS, harga.TOTAL_QTY, harga.NOTES, harga.ALAMAT, 
                                    harga.KOTA, hargad.KD_BRG, hargad.NA_BRG, hargad.SATUAN, hargad.QTY2 AS QTY, hargad.DISK,
                                    hargad.HARGA, hargad.TOTAL, hargad.KET, harga.TPPN, harga.NETT,
                                    harga.NO_PO, harga.USRNM, hargad.KALI, harga.TDISK, harga.TDPP, hargad.PPN, hargad.DPP
                            FROM harga, hargad 
                            WHERE harga.NO_BUKTI='$no_harga' AND harga.NO_BUKTI = hargad.NO_BUKTI 
                            ;
		");

                DB::SELECT("UPDATE harga SET POSTED = 1 WHERE NO_BUKTI='$no_harga';");
                
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
	

    function posting (Request $request, Harga $harga)
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

		
		return redirect('/harga/post')->with('statusInsert', 'No Bukti berhasil diupdate');		
	
		
		
	}
	
	
	public function getDetailharga(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('hargad')->where('NO_BUKTI', $no_bukti)->get();
        
        return response()->json($result);;
    }
	
	
	
	
}
