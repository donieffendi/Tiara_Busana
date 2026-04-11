<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1
use App\Models\OTransaksi\Bintang;
use App\Models\OTransaksi\BintangDetail;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class BintangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function index(Request $request)
    {
        // ganti 3
        return view('otransaksi_bintang.index');
    }

	public function browse_sup(Request $request)
    {
        $bintang = DB::SELECT("SELECT NO_SUPL, NAMA, BUDGET_AWL FROM nwmassup ORDER BY NO_SUPL ");

        return response()->json($bintang);
    }

    public function browse_brg(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
		$NO_SUPL = $request->NO_SUPL;
        $bintang= DB::SELECT("SELECT a.KDBAR, a.NMBAR, a.SUPP AS NO_SUPL, b.NAMA FROM nwmasbar a, nwmassup b WHERE SUPP = '$NO_SUPL' AND a.SUPP = b.NO_SUPL ORDER BY KDBAR");
        return response()->json($bintang);
    }


	public function index_posting(Request $request)
    {

        return view('otransaksi_bintang.post');
    }

	public function browse_pod(Request $request)
    {
        $sup = $request->kodes;



            $bintangd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.BARCODE, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, a.TOTAL,
                                a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.HJ, b.MARGIN, b.RAK AS JNS
                            from nwbintangd a
                            LEFT JOIN nwmasbar b
                                ON b.KDBAR = a.KD_BRG
                            where a.NO_BUKTI='".$request->nobukti."' ");



		return response()->json($bintangd);
	}

	public function browse_detail(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{

			$filterbukti = " WHERE a.NO_BUKTI='".$request->NO_PO."' AND a.KD_BHN = b.KD_BHN ";
		}
		$bintangd = DB::SELECT("SELECT a.REC, a.KD_BHN, a.NA_BHN, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI
                            from pod a, bhn b
                            $filterbukti ORDER BY NO_BUKTI ");


		return response()->json($bintangd);
	}


    public function browse_detail2(Request $request)
    {
		$filterbukti = '';
		if($request->NO_PO)
		{

			$filterbukti = " WHERE NO_BUKTI='".$request->NO_PO."' AND a.KD_BRG = b.KD_BRG ";
		}
		$bintangd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI
                            from pod a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");


		return response()->json($bintangd);
	}
    // ganti 4



    public function getBintang(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $CBG = Auth::user()->CBG;

        $bintang= DB::SELECT("
            SELECT *
            FROM nwbintang 
            WHERE PER = '$periode' AND CBG = '$CBG'
            ORDER BY NO_BUKTI
        ");

        // ganti 6

        return Datatables::of($bintang)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" )
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("bintang/delete/" . $row->NO_ID)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="bintang/edit/?idx=' . $row->NO_ID . '&tipx=edit'.'"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')"';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a hidden class="dropdown-item btn btn-danger" target="_blank" href="bintang/cetak/' . $row->NO_ID . '">
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
                'TGL'      => 'required'
            ]
        );

        $CBG = Auth::user()->CBG;

        $CBG_KODE = DB::table('toko')
            ->where('KODE', $CBG)
            ->value('TYPE');

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        // ambil NO_BUKTI terakhir (langsung string, bukan collection)
        $last = DB::table('nwbintang')
            ->where('PER', $periode)
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

        $no_bukti = 'TB' . $tahun . $bulan . '-' . $urutan . $CBG_KODE;



        $bintang= Bintang::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'PER'              => $periode,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
				'NO_SUPL'          => ($request['NO_SUPL'] == null) ? "" : $request['NO_SUPL'],
                'NAMA'             => ($request['NAMA'] == null) ? "" : $request['NAMA'],
                'BUDGET_AWL'       => (float) str_replace(',', '', $request['BUDGET_AWL']),
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'CBG'              => $CBG,
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
            ]
        );


		$REC        = $request->input('REC');
		$KDBAR      = $request->input('KDBAR');
        $NMBAR      = $request->input('NMBAR');
        $NO_SUPLD   = $request->input('NO_SUPLD');
        $NAMAD      = $request->input('NAMAD');
        $CEK        = $request->input('CEK');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new BintangDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->KDBAR       = ($KDBAR[$key] == null) ? "" :  $KDBAR[$key];
                $detail->NMBAR       = ($NMBAR[$key] == null) ? "" :  $NMBAR[$key];
                $detail->NO_SUPL     = ($NO_SUPLD[$key] == null) ? "" :  $NO_SUPLD[$key];
                $detail->NAMA        = ($NAMAD[$key] == null) ? "" :  $NAMAD[$key];
                $detail->CEK         = isset($CEK[$key]) ? 1 : 0;
                // $detail->CEK         = isset($CEK[$key]) ? (float) str_replace(',', '', $CEK[$key]) : 0;
                $detail->save();

                if (isset($CEK[$key]) && $CEK[$key] == 1) {
                    DB::update("
                        UPDATE nwmasbar 
                        SET TD_OD = '*',
                            ALASAN = 'TL/MACET',
                            TG_TD_OD = DATE(NOW())
                        WHERE KDBAR = ?
                    ", [$KDBAR[$key]]);
                }
            }
        }

		$no_buktix = $no_bukti;

		$bintang= Bintang::where('NO_BUKTI', $no_buktix )->first();

        DB::SELECT("UPDATE nwbintang,  nwbintangd
                            SET  nwbintangd.ID =  nwbintang.NO_ID  WHERE  nwbintang.NO_BUKTI =  nwbintangd.NO_BUKTI
							AND  nwbintang.NO_BUKTI='$no_buktix';");

        return redirect('/bintang')->with('statusInsert', 'Data baru berhasil ditambahkan');

    }

   public function edit( Request $request , Bintang $bintang)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];


        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/po')
		// 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbintang
		                 where PER ='$per'
                         AND CBG = '$CBG'
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


		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbintang
		                 where PER ='$per'
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbintang
		             where PER ='$per'
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbintang
		             where PER ='$per'
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

    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbintang
						where PER ='$per'
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
			$bintang= Bintang::where('NO_ID', $idx )->first();
	     }
		 else
		 {
				$bintang= new Bintang;
                $bintang->TGL = Carbon::now();


		 }

        $no_bukti = $bintang->NO_BUKTI;
        $bintangDetail = DB::table('nwbintangd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

		$data = [
            'header'        => $bintang,
			'detail'        => $bintangDetail

        ];



        return view('otransaksi_bintang.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx]);

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Bintang $bintang)
    {

        $this->validate(
            $request,
            [

                'TGL'      => 'required'
            ]
        );

        // $variablell = DB::select('call podel(?)', array($bintang['NO_BUKTI']));

        $CBG = Auth::user()->CBG;


        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];


        $bintang->update(
            [

                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
				'NO_SUPL'          => ($request['NO_SUPL'] == null) ? "" : $request['NO_SUPL'],
                'NAMA'             => ($request['NAMA'] == null) ? "" : $request['NAMA'],
                'BUDGET_AWL'       => (float) str_replace(',', '', $request['BUDGET_AWL']),
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
            ]
        );

		$no_buktix = $bintang->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC    = $request->input('REC');

        $KDBAR      = $request->input('KDBAR');
        $NMBAR      = $request->input('NMBAR');
        $NO_SUPLD   = $request->input('NO_SUPLD');
        $NAMAD      = $request->input('NAMAD');
        $CEK        = $request->input('CEK');

        $query = DB::table('nwbintangd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = BintangDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'KDBAR'      => ($KDBAR[$i] == null) ? "" :  $KDBAR[$i],
                        'NMBAR'      => ($NMBAR[$i] == null) ? "" :  $NMBAR[$i],
                        'NO_SUPL'    => ($NO_SUPLD[$i] == null) ? "" :  $NO_SUPLD[$i],
                        'NAMA'       => ($NAMAD[$i] == null) ? "" :  $NAMAD[$i],
                        'CEK'        => isset($CEK[$i]) ? 1 : 0,
                        // 'CEK'        => isset($CEK[$i]) ? (float) str_replace(',', '', $CEK[$i]) : 0,
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = BintangDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],
                        'KDBAR'      => ($KDBAR[$i] == null) ? "" :  $KDBAR[$i],
                        'NMBAR'      => ($NMBAR[$i] == null) ? "" :  $NMBAR[$i],
                        'NO_SUPL'    => ($NO_SUPLD[$i] == null) ? "" :  $NO_SUPLD[$i],
                        'NAMA'       => ($NAMAD[$i] == null) ? "" :  $NAMAD[$i],
                        'CEK'        => isset($CEK[$i]) ? 1 : 0,
                        // 'CEK'        => isset($CEK[$i]) ? (float) str_replace(',', '', $CEK[$i]) : 0,
                    ]
                );
            }

            if (!empty($KDBAR[$i])) {
                if (isset($CEK[$i])) {
                    // CEK = 1
                    DB::table('nwmasbar')
                        ->where('KDBAR', $KDBAR[$i])
                        ->update([
                            'TD_OD'   => '*',
                            'ALASAN'  => 'TL/MACET',
                            'TG_TD_OD'=> now()
                        ]);
                } else {
                    // CEK = 0 (rollback)
                    DB::table('nwmasbar')
                        ->where('KDBAR', $KDBAR[$i])
                        ->update([
                            'TD_OD'   => '',
                            'ALASAN'  => '',
                            'TG_TD_OD'=> '0000-00-00'
                        ]);
                }
            }
        }

 		$bintang= Bintang::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $bintang->NO_BUKTI;

        DB::SELECT("UPDATE nwbintang,  nwbintangd
                    SET  nwbintangd.ID =  nwbintang.NO_ID  WHERE  nwbintang.NO_BUKTI =  nwbintang.NO_BUKTI
                    AND  nwbintang.NO_BUKTI='$no_bukti';");

        // $variablell = DB::select('call poins(?)', array($bintang['NO_BUKTI']));

        return redirect('/bintang')->with('statusInsert', 'Data baru berhasil diupdate');


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Bintang $bintang)
    {

        // hapus detail dulu
        DB::table('nwbintangd')->where('ID', $bintang->NO_ID)->delete();

        // hapus header
        DB::table('nwbintang')->where('NO_ID', $bintang->NO_ID)->delete();

        return redirect('/bintang')->with('statusHapus','Data '.$bintang->NO_BUKTI.' berhasil dihapus');
    }

    public function cetak(Bintang $bintang, Request $request)
    {
        $no_bintang = $bintang->NO_BUKTI;
        
        $file     = 'poc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $PHPJasperXML->arrayParameter = [
            "TGL_CTK" => date('d/m/Y'),
        ];

        $query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.PER, po.CBG, po.KODES, po.NAMAS, po.Q_SALDO AS TOTAL_QTY, po.NOTES,
                                    pod.KD_BRG, pod.BARCODE, pod.NA_BRG, pod.SATUAN, pod.qty AS QTY,
                                    pod.HARGA, pod.TOTAL, pod.KET,
                                    po.JTEMPO, nwmassup.ALMT_K AS ALAMAT, nwmassup.KOTA, nwmassup.GOLONGAN AS PKP,
                                    nwmassup.CARA, nwmassup.TLP_R, nwmassup.NO_FAX, '$jenis' as COPY
                            FROM nwbintang as po
                            JOIN nwbintangd pod ON po.NO_BUKTI = pod.NO_BUKTI
                            LEFT JOIN nwmassup ON po.KODES = nwmassup.NO_SUPL
                            WHERE po.NO_BUKTI='$no_bintang' AND po.NO_BUKTI = pod.NO_BUKTI
                            ;
		");

        //dd($query);
        $cleanData = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");


    }

}
