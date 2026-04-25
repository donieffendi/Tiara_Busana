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

    function setFlag(Request $request)
    {
        if ( $request->flagz == 'HG') {
            $this->judul = "Pengajuan Harga Jual (Ganti Harga)";
        } else if ( $request->flagz == 'HT') {
            $this->judul = "Pencetakan Label Harga (Turun Harga)";
        }

        $this->FLAGZ = $request->flagz;
    }

    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_harga.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);


    }


	public function post (Request $request)
    {
        return view('otransaksi_harga.post');
    }

    // public function browse_posting(Request $request)
    // {


	// 	$cari = $request->CARI;

	// 	if ($cari == ''){

    //         $posting = DB::SELECT("SELECT NO_ID, BARCODE, NA_BRG, CNT, NCNT, HJUAL, KD_BRG
    //                                     FROM bhrgd
    //                                     WHERE KD_BRG ='' AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");

    //     } else if ($cari != ''){

    //         $posting = DB::SELECT("SELECT NO_ID, BARCODE, NA_BRG, CNT, NCNT, HJUAL, KD_BRG
    //                                     FROM bhrgd
    //                                     WHERE KD_BRG = '$cari' AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
    //     }

    //     return response()->json($posting);
    // }

    public function browse_posting(Request $request)
    {
        $cari = $request->CARI;

        $CBG   = Auth::user()->CBG;

        $query = DB::table('bhrgd as d')
            ->join('bhrg as h', 'd.NO_BUKTI', '=', 'h.NO_BUKTI')
            ->select(
                'd.NO_ID',
                'd.KD_BRG',
                'd.NA_BRG',
                'd.BARCODE',
                'd.HARGA',
                'h.CNT',
                'h.NCNT'
            )
            ->where('h.CBG', $CBG)
            ->where('h.POSTED', '0');

        // filter berdasarkan NO_BUKTI
        if ($cari != '') {
            $query->where('d.NO_BUKTI', 'like', "%$cari%");
        }

        $posting = $query->get();

        return response()->json($posting);
    }

    public function browse_conter(Request $request)
    {

        $harga = DB::SELECT("SELECT CNT, NA_CNT AS NCNT
                            FROM cntbsn");

        return response()->json($harga);
    }

    public function browse_sup(Request $request)
    {

        $harga = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS
                            FROM nwmassup");

        return response()->json($harga);
    }

    public function browse_brg(Request $request)
    {

        $cnt = $request->CNT;
        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        $bulan = session()->get('periode')['bulan'];

        $harga = DB::SELECT("SELECT KD_BRG, BARCODE, NA_BRG, JNS, HJUAL AS HARGAJL, HJUAL$bulan AS HARGAKSR, (AWL + QTY_TRM - QTY_JUAL) AS SISA
                            FROM nwmasbar WHERE CNT = '$cnt'");

        return response()->json($harga);
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
        $FLAGZ = $this->FLAGZ;

		$CBG = Auth::user()->CBG;
		$PPN = Auth::user()->PPN;

        $harga = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, KODES, NAMAS, CNT, NCNT, USRNM, POSTED, FLAG
                            FROM bhrg
                            where PER = '$periode' and FLAG='$FLAGZ' ");


        // ganti 6

        return Datatables::of($harga)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ( (Auth::user()->divisi=="programmer" ) || (Auth::user()->divisi=="gudang" ))
				{
                    //CEK POSTED di index dan edit
                    $url = "'".url("harga/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";

                    // $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    if (Auth::user()->divisi == 'gudang') {
                        // khusus gudang, cek CETAK
                        $btnEdit = ($row->CETAK == 1)
                            ? ' onclick="alert(\'LPB ini sudah dicetak, tidak bisa edit.\')" href="#" '
                            : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';
                    } else {
                        // user lain, tetap cek POSTED
                        $btnEdit = ($row->POSTED == 1)
                            ? ' onclick="alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" '
                            : ' href="harga/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';
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

        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $CBG_KODE = DB::table('toko')
            ->where('KODE', $CBG)
            ->value('TYPE');

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        // ambil NO_BUKTI terakhir (langsung string, bukan collection)
        $last = DB::table('bhrg')
            ->where('PER', $periode)
            ->where('FLAG', $FLAGZ)
            ->where('CBG', $CBG)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');

        if ($last) {

            // ambil angka setelah tanda "-"
            preg_match('/-(\d+)/', $last, $matches);

            $angka = isset($matches[1]) ? (int)$matches[1] : 0;

            $angka++;

            $urutan = str_pad($angka, 4, '0', STR_PAD_LEFT);

        } else {
            $urutan = '0001';
        }

        $no_bukti = $FLAGZ . $tahun . $bulan . '-' . $urutan . $CBG_KODE;


        // Insert Header

        // ganti 10

        $harga = Harga::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'FLAG'             => $FLAGZ,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
				'NCNT'             => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
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
        $SISA       = $request->input('SISA');
        $DTH        = $request->input('DTH');
        $KET        = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new HargaDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->BARCODE     = ($BARCODE[$key] == null) ? "" :  $BARCODE[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->JNS         = ($JNS[$key] == null) ? "" :  $JNS[$key];
                $detail->HARGAJL     = isset($HARGAJL[$key]) ? (float) str_replace(',', '', $HARGAJL[$key]) : 0;
                $detail->HARGAKSR    = isset($HARGAKSR[$key]) ? (float) str_replace(',', '', $HARGAKSR[$key]) : 0;
                $detail->HARGA       = (float) str_replace(',', '', $HARGA[$key]);
                $detail->SISA        = isset($SISA[$key]) ? (float) str_replace(',', '', $SISA[$key]) : 0;
                $detail->DTH         = isset($DTH[$key]) ? (float) str_replace(',', '', $DTH[$key]) : 0;
                $detail->KET         = ($KET[$key] == null) ? "" :  $KET[$key];
                $detail->save();
            }
        }

        //  ganti 11

		$no_buktix = $no_bukti;

		$harga = Harga::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE bhrg,  bhrgd
                            SET  bhrgd.ID = bhrg.NO_ID  WHERE  bhrg.NO_BUKTI =  bhrgd.NO_BUKTI
							AND  bhrg.NO_BUKTI='$no_buktix';");



        // $variablell = DB::select('call hargains(?)', array($no_buktix));

        // return redirect('/harga/edit/?idx=' . $harga->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/harga?flagz='.$FLAGZ)->with(['judul' => $judul,'flagz' => $FLAGZ ]);


    }


    // ganti 15


   public function edit( Request $request , Harga $harga)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];


        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/harga')
		// 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }

		$this->setFlag($request);

        // dd($request->all());

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bhrg
		                 where PER ='$per' and FLAG ='$this->FLAGZ'

						 and NO_BUKTI = '$buktix'
		                 and CBG = '$CBG'

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


		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bhrg
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ'
		                 and CBG = '$CBG'

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bhrg
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' and NO_BUKTI <
					'$buktix' and CBG = '$CBG'

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bhrg
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' and NO_BUKTI >
					 '$buktix' and CBG = '$CBG'

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

    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bhrg
						where PER ='$per'
						and FLAG ='$this->FLAGZ'
		                and CBG = '$CBG'

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
        $hargadetail = DB::table('bhrgd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

		$data = [
            'header'        => $harga,
			'detail'        => $hargadetail

        ];


         return view('otransaksi_harga.edit', $data)
		 ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' =>$this->FLAGZ, 'judul' => $this->judul]);

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
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
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
        $SISA       = $request->input('SISA');
        $DTH        = $request->input('DTH');
        $KET        = $request->input('KET');

        $query = DB::table('hargad')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = HargaDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'KD_BRG'     => ($KD_BRG[$i] == null) ? "" :  $KD_BRG[$i],
                        'BARCODE'    => ($BARCODE[$i] == null) ? "" :  $BARCODE[$i],
                        'NA_BRG'     => ($NA_BRG[$i] == null) ? "" :  $NA_BRG[$i],
                        'JNS'        => ($JNS[$i] == null) ? "" :  $JNS[$i],
                        'HARGAJL'    => (float) str_replace(',', '', $HARGAJL[$i]),
                        'HARGAKSR'   => (float) str_replace(',', '', $HARGAKSR[$i]),
                        'HARGA'      => (float) str_replace(',', '', $HARGA[$i]),
                        'SISA'       => (float) str_replace(',', '', $SISA[$i]),
                        'DTH'        => (float) str_replace(',', '', $DTH[$i]),
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],

                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = HargaDetail::updateOrCreate(
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
                        'SISA'       => (float) str_replace(',', '', $SISA[$i]),
                        'DTH'        => (float) str_replace(',', '', $DTH[$i]),
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i]
                    ]
                );
            }
        }


        //  ganti 21

 		$harga = Harga::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $harga->NO_BUKTI;


        DB::SELECT("UPDATE bhrg,  bhrgd
                    SET  bhrgd.ID =  bhrg.NO_ID  WHERE  bhrg.NO_BUKTI =  bhrgd.NO_BUKTI
                    AND  bhrg.NO_BUKTI='$no_bukti';");

        // $variablell = DB::select('call hargains(?)', array($harga['NO_BUKTI']));

        // return redirect('/harga/edit/?idx=' . $harga->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul .  '&golz=' . $this->GOLZ . '');
        return redirect('/harga?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);


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

        $judul = $this->judul;

		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('harga')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }


    //    $variablell = DB::select('call hargadel(?)', array($harga['NO_BUKTI']));


        // ganti 23

        $deleteharga = Harga::find($harga->NO_ID);

        // ganti 24

        $deleteharga->delete();

        // ganti

       return redirect('/harga?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$harga->NO_BUKTI.' berhasil dihapus');


    }


    public function cetak(Harga $harga)
    {
        $no_harga = $harga->NO_BUKTI;

        $file     = 'hargac';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $params = [
            "TGL_CTK" => date('d/m/Y H:i:s')
        ];
        $PHPJasperXML->arrayParameter = $params;

        $query = DB::SELECT("SELECT bhrg.NO_BUKTI, bhrg.TGL, bhrg.KODES, bhrg.NAMAS, bhrg.CNT, bhrg.NCNT, bhrgd.KD_BRG, bhrgd.NA_BRG,
                                    bhrgd.BARCODE, bhrgd.HARGAJL, bhrgd.DTH, bhrgd.HARGAKSR, bhrgd.HARGA, bhrgd.REC
                            FROM bhrg, bhrgd
                            WHERE bhrg.NO_BUKTI='$no_harga' AND bhrg.NO_BUKTI = bhrgd.NO_BUKTI;
		");

                // DB::SELECT("UPDATE bhrg SET POSTED = 1 WHERE NO_BUKTI='$no_harga';");

        $data = [];

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }


    // function posting (Request $request, Harga $harga)
	// {

    //     $REC = $request->input('REC');
	// 	$CEKX = $request->input('CEKX');
    //     $NO_IDX = $request->input('NO_ID');
    //     $NO_BUKTIX = $request->input('NO_BUKTI');
    //     $TGLX = $request->input('TGL');
    //     $NO_SURATSX = $request->input('NO_SURATS');
    //     $NAMACX = $request->input('NAMAC');
    //     $NETTX = $request->input('NETT');
    //     $NO_FPX = $request->input('NO_FPX');
    //     $TGL_FPX = $request->input('TGL_FPX');

    //     $USRNMX = Auth::user()->USERNAME;

    //     session()->put('posttimer', time());

    //     $hasil = "";
    //     // ddd($TGL_FPX);
    //     if ($REC) {
    //         foreach ($REC as $key => $value) {

	// 				$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
	// 				$bulan    = session()->get('periode')['bulan'];
	// 				$tahun    = substr(session()->get('periode')['tahun'], -2);


	// 			// $NETTXZ = (float) str_replace(',', '', $NETTX[$key]);

	// 			$NO_IDXZ = $NO_IDX[$key];


	// 			// $HUTHGXZ = $HUTHGX[$key];


	// 			$CEK11 = $CEKX[$key];


	// 			// $NO_BUKTIXZ = ($NO_BUKTIX[$key] == null) ? "" :  $NO_BUKTIX[$key];
	// 			// $TGLXZ = ($TGLX[$key] == null) ? "" :  $TGLX[$key];

	// 			// $NO_SURATSXZ = ($NO_SURATSX[$key] == null) ? "" :  $NO_SURATSX[$key];
	// 			// $NAMACXZ = ($NAMACX[$key] == null) ? "" :  $NAMACX[$key];

	// 			$NO_FPXZ = ($NO_FPX[$key] == null) ? "" :  $NO_FPX[$key];
	// 			$TGL_FPXZ = ($TGL_FPX[$key] == null) ? "" :  date('Y-m-d', strtotime($TGL_FPX[$key]));


	// 			if ( $CEK11 == 1 )
	// 		    {

    //                 DB::SELECT("UPDATE jual
    //                             SET NO_FP = '$NO_FPXZ',
    //                                 TGL_FP = '$TGL_FPXZ'
    //                             WHERE NO_ID ='$NO_IDXZ' ");


	// 			}

	// 				// IF CEK

    //         } // FOR


    //     }
    //     else
    //     {
    //         $hasil = $hasil ."Tidak ada No Bukti yang dipilih! ; ";
    //     }


	// 	return redirect('/harga/post')->with('statusInsert', 'No Bukti berhasil diupdate');



	// }

    public function posting(Request $request)
    {
        $NO_ID = $request->input('NO_ID');

        if (empty($NO_ID)) {
            return redirect()->back()->with('error', 'Tidak ada data!');
        }

        DB::beginTransaction();

        try {

            // ambil NO_BUKTI dari detail
            $no_bukti = DB::table('bhrgd')
                ->whereIn('NO_ID', $NO_ID)
                ->pluck('NO_BUKTI')
                ->unique();

            if ($no_bukti->isEmpty()) {
                return redirect()->back()->with('error', 'Data tidak ditemukan');
            }

            // update header
            DB::table('bhrg')
                ->whereIn('NO_BUKTI', $no_bukti)
                ->update([
                    'POSTED' => 1
                ]);

            DB::commit();

            return redirect('/harga/post')->with('statusInsert', 'Data berhasil diposting');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', 'Gagal posting: '.$e->getMessage());
        }
    }


	public function getDetailharga(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('hargad')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);;
    }




}
