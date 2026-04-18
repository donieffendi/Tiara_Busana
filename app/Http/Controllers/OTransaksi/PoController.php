<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Po;
use App\Models\OTransaksi\PoDetail;
use App\Models\OTransaksi\Nwbudget;
use App\Models\OTransaksi\NwbudgetDetail;
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
        $tanggal = date('Y-m-d');
        $CBG     = Auth::user()->CBG;
        $kodes = $request->kodes;

        //
        // $po = DB::select("SELECT DISTINCT PO.NO_BUKTI, PO.KODES, PO.NAMAS,
        //                             PO.ALAMAT, PO.KOTA, PO.JTEMPO, PO.NOTES
        //                     FROM nwbudget AS PO
        //                     JOIN nwbudgetd AS POD ON PO.NO_BUKTI = POD.NO_BUKTI
        //                     WHERE PO.KODES = ?
        //                     AND POD.SISA > 0
        //                     AND PO.POSTED = 1
        //                     AND PO.JTEMPO > ?
        //                     AND NOT EXISTS (
        //                         SELECT 1 
        //                         FROM nwagend 
        //                         WHERE nwagend.SP = PO.NO_BUKTI
        //                     )
        //                     GROUP BY PO.NO_BUKTI, PO.KODES, PO.NAMAS,
        //                             PO.ALAMAT, PO.KOTA, PO.JTEMPO, PO.NOTES
        //                 ", [$kodes, $tanggal]);

        $po = DB::SELECT("SELECT DISTINCT 
                            PO.NO_BUKTI, PO.KODES, PO.NAMAS,
                            PO.ALAMAT, PO.KOTA, PO.JTEMPO, PO.NOTES
                        FROM nwbudget AS PO
                        JOIN nwbudgetd AS POD ON PO.NO_BUKTI = POD.NO_BUKTI
                        WHERE PO.KODES = ?
                            AND POD.SISA > 0
                            AND PO.POSTED = 1
                            AND PO.JTEMPO > ?
                            AND NOT EXISTS (
                                SELECT 1 
                                FROM nwagend 
                                WHERE nwagend.SP = PO.NO_BUKTI
                            )

                        UNION ALL
                            
                            SELECT DISTINCT 
                            BS.NO_BUKTI, BS.KODES, BS.NAMAS,
                            BS.ALAMAT, BS.KOTA, BS.JTEMPO, BS.NOTES
                        FROM bstockaz AS BS
                        JOIN bstockazd AS BSD ON BS.NO_BUKTI = BSD.NO_BUKTI
                        WHERE BS.KODES = ?
                            AND BS.SISA > 0
                            AND BS.POSTED = 1
                            AND BS.JTEMPO > ?
                            AND NOT EXISTS (
                                SELECT 1 
                                FROM nwagend 
                                WHERE nwagend.SP = BS.NO_BUKTI
                            )", [$kodes, $tanggal, $kodes, $tanggal]);

        return response()->json($po);
    }

    public function browse_brg(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
		$sup = $request->sup;
        $po = DB::SELECT("SELECT KDBAR, NMBAR, BARCODE, HB AS HARGA, 1 AS STOK FROM nwmasbar WHERE SUPP = '$sup'");
        return response()->json($po);
    }

    public function browse_sup(Request $request)
    {


    	if (!empty(request('q'))) {


                 $po = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA
                            from nwmassup
                            WHERE  NAMA LIKE ('%$request->q%')
                            ORDER BY NAMA ");


        } else {
			$po = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA
                            from nwmassup

                            ORDER BY NAMA ");
		}

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
        $sup = $request->kodes;



            $pod = DB::SELECT("SELECT a.REC, a.KD_BRG, a.BARCODE, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, a.TOTAL,
                                a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.HJ, b.MARGIN, b.RAK AS JNS
                            from nwbudgetd a
                            LEFT JOIN nwmasbar b
                                ON b.KDBAR = a.KD_BRG
                            where a.NO_BUKTI='".$request->nobukti."' ");



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
        $jenisLaporan = $request->jenis_laporan;
        $filterJenisLaporan = '';



        switch ($jenisLaporan) {
            case 'kurang_laku_senin':
                $filterJenisLaporan = "
                    AND EXISTS (
                        SELECT 1
                        FROM nwbudgetd pod
                        INNER JOIN nwmasbar mb ON mb.KDBAR = pod.KD_BRG
                        WHERE pod.NO_BUKTI = nwbudget.NO_BUKTI
                          AND DATEDIFF(CURDATE(), COALESCE(NULLIF(mb.TG_BELI1, '0000-00-00'), NULLIF(mb.TG_BELI2, '0000-00-00'))) > 60
                          AND IFNULL((mb.JL / NULLIF(mb.BL, 0)) * 100, 0) <= 40
                    )
                ";
                break;

            case 'laku_senin':
                $filterJenisLaporan = "
                    AND EXISTS (
                        SELECT 1
                        FROM nwbudgetd pod
                        INNER JOIN nwmasbar mb ON mb.KDBAR = pod.KD_BRG
                        WHERE pod.NO_BUKTI = nwbudget.NO_BUKTI
                          AND DATEDIFF(CURDATE(), COALESCE(NULLIF(mb.TG_BELI1, '0000-00-00'), NULLIF(mb.TG_BELI2, '0000-00-00'))) > 20
                          AND DATEDIFF(CURDATE(), COALESCE(NULLIF(mb.TG_BELI1, '0000-00-00'), NULLIF(mb.TG_BELI2, '0000-00-00'))) < 60
                          AND IFNULL((mb.JL / NULLIF(mb.BL, 0)) * 100, 0) > 40
                    )
                ";
                break;

            case 'tidak_laku':
                $filterJenisLaporan = "
                    AND EXISTS (
                        SELECT 1
                        FROM nwbudgetd pod
                        INNER JOIN nwmasbar mb ON mb.KDBAR = pod.KD_BRG
                        WHERE pod.NO_BUKTI = nwbudget.NO_BUKTI
                          AND IFNULL((mb.JL / NULLIF((mb.SA + mb.BL), 0)) * 100, 0) < 40
                    )
                ";
                break;

            case 'laku':
                $filterJenisLaporan = "
                    AND EXISTS (
                        SELECT 1
                        FROM nwbudgetd pod
                        INNER JOIN nwmasbar mb ON mb.KDBAR = pod.KD_BRG
                        WHERE pod.NO_BUKTI = nwbudget.NO_BUKTI
                          AND IFNULL((mb.JL / NULLIF((mb.SA + mb.BL), 0)) * 100, 0) >= 40
                    )
                ";
                break;
        }

        $po = DB::SELECT("
            SELECT *
            FROM nwbudget
            WHERE PER= '$periode'
              AND FLAG= '$this->FLAGZ'
              AND GOL= '$this->GOLZ'
              $filterJenisLaporan
            ORDER BY NO_BUKTI
        ");


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
                                <a class="dropdown-item btn btn-danger" target="_blank" href="po/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="po/cetak/' . $row->NO_ID . '?tipe=lampiran">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Lampiran
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

        $query = DB::table('nwbudget')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'PO')->where('CBG', 'DC1')
                ->where('GOL', $this->GOLZ )->orderByDesc('NO_BUKTI')->limit(1)->get();


        if ($query != '[]') {
            $query = substr($query[0]->NO_BUKTI, -4);
            $query = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = $GOLZ  . 'DC1' . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = $GOLZ  . 'DC1' . $tahun . $bulan . '-0001';
        }



        $po = Nwbudget::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'              => $periode,
				'CNT'              => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NA_CNT'           => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'FLAG'             => 'PO',
                'GOL'              => $GOLZ,
                'CBG'              => ($request['CBG'] == null) ? "" : $request['CBG'],
                'NOTES'              => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'Q_SALDO'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'R_SALDO'            => (float) str_replace(',', '', $request['TTOTAL']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
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
        $KDLAKU     = $request->input('KDLAKU');
        $KET        = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new NwbudgetDetail;

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
                $detail->KDLAKU      = ($KDLAKU[$key] == null) ? "" :  $KDLAKU[$key];
                $detail->KET         = ($KET[$key] == null) ? "" :  $KET[$key];
                $detail->save();
            }
        }

		$no_buktix = $no_bukti;

		$po = Po::where('NO_BUKTI', $no_buktix )->first();


        DB::SELECT("UPDATE nwbudget, nwmassup
                    SET nwbudget.NAMAS = nwmassup.NAMA  WHERE nwbudget.KODES = nwmassup.NO_SUPL
                    AND nwbudget.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE nwbudget, cntbsn
                    SET nwbudget.NA_CNT = cntbsn.NA_CNT  WHERE nwbudget.CNT = cntbsn.CNT
                    AND nwbudget.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE nwbudget,  nwbudgetd
                            SET  nwbudgetd.ID =  nwbudget.NO_ID  WHERE  nwbudget.NO_BUKTI =  nwbudgetd.no_bukti
							AND  nwbudget.NO_BUKTI='$no_buktix';");

        return redirect('/po?flagz='.$FLAGZ.'&golz='.$GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ ]);

    }

   public function edit( Request $request , Nwbudget $po)
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbudget
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ'
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


		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbudget
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ'
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbudget
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbudget
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'
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

    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwbudget
						where PER ='$per'
						and FLAG ='$this->FLAGZ'
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
			$po = Nwbudget::where('NO_ID', $idx )->first();
	     }
		 else
		 {
				$po = new Nwbudget;
                $po->TGL = Carbon::now();
                $po->JTEMPO = Carbon::now();


		 }

        $no_bukti = $po->NO_BUKTI;
        $poDetail = DB::table('nwbudgetd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

		$data = [
            'header'        => $po,
			'detail'        => $poDetail

        ];



        return view('otransaksi_po.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ, 'judul'=> $this->judul ]);

    }

  /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Nwbudget $po)
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
                'NA_CNT'           => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'FLAG'             => 'PO',
                'GOL'              => $GOLZ,
                'CBG'              => $CBG,
                'NOTES'              => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'Q_SALDO'        => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'R_SALDO'            => (float) str_replace(',', '', $request['TTOTAL']),
				'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
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
        $KDLAKU     = $request->input('KDLAKU');
        $KET        = $request->input('KET');

        $query = DB::table('nwbudgetd')->where('no_bukti', $request->no_bukti)->whereNotIn('NO_ID',  $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = NwbudgetDetail::create(
                    [
                        'NO_BUKTI'   => $request->no_bukti,
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
                        'KDLAKU'     => ($KDLAKU[$i] == null) ? "" :  $KDLAKU[$i],
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = NwbudgetDetail::updateOrCreate(
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
                        'KDLAKU'     => ($KDLAKU[$i] == null) ? "" :  $KDLAKU[$i],
                        'KET'        => ($KET[$i] == null) ? "" :  $KET[$i],
                    ]
                );
            }
        }

 		$po = Nwbudget::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $po->NO_BUKTI;

        DB::SELECT("UPDATE nwbudget, nwmassup
                    SET nwbudget.NAMAS = nwmassup.NAMA WHERE nwbudget.KODES = nwmassup.NO_SUPL
                    AND nwbudget.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE nwbudget, cntbsn
                    SET nwbudget.NA_CNT = cntbsn.NA_CNT  WHERE nwbudget.CNT = cntbsn.CNT
                    AND nwbudget.NO_BUKTI='$no_buktix';");

        DB::SELECT("UPDATE nwbudget,  nwbudgetd
                    SET  nwbudgetd.ID =  nwbudget.NO_ID  WHERE  nwbudget.NO_BUKTI =  nwbudget.NO_BUKTI
                    AND  nwbudget.NO_BUKTI='$no_bukti';");

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

    public function destroy(Request $request, Nwbudget $po)
    {
        $this->setFlag($request);
        $FLAGZ = $_GET['flagz'];
        $GOLZ = $_GET['golz'];
        $judul = $this->judul;

        // hapus detail dulu
        DB::table('nwbudgetd')->where('ID', $po->NO_ID)->delete();

        // hapus header
        DB::table('nwbudget')->where('NO_ID', $po->NO_ID)->delete();

        return redirect('/po?flagz='.$FLAGZ.'&golz='.$GOLZ)
            ->with(['judul'=>$judul,'flagz'=>$this->FLAGZ,'golz'=>$this->GOLZ])
            ->with('statusHapus','Data '.$po->NO_BUKTI.' berhasil dihapus');
    }

    public function cetak(Nwbudget $po, Request $request)
    {
        $no_po = $po->NO_BUKTI;
        $tipe = $request->tipe;

        if ($tipe == 'lampiran') {
            $file     = 'poc_l';
        } else {
            $file     = 'poc';
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $data = DB::table('nwbudget')->where('NO_BUKTI', $no_po)->first();
        $jenis = ($data->POSTED == 0) ? 'ASLI' : 'COPY';

        if($tipe != 'lampiran') {
            DB::update("UPDATE nwbudget SET POSTED = 1 WHERE NO_BUKTI = ?", [$no_po]);
        }

        $query = DB::SELECT("SELECT po.NO_BUKTI, po.TGL, po.PER, po.CBG, po.KODES, po.NAMAS, po.Q_SALDO AS TOTAL_QTY, po.NOTES,
                                    pod.KD_BRG, pod.BARCODE, pod.NA_BRG, pod.SATUAN, pod.qty AS QTY,
                                    pod.HARGA, pod.TOTAL, pod.KET,
                                    po.JTEMPO, nwmassup.ALMT_K AS ALAMAT, nwmassup.KOTA, nwmassup.GOLONGAN AS PKP,
                                    nwmassup.CARA, nwmassup.TLP_R, nwmassup.NO_FAX, '$jenis' as COPY
                            FROM nwbudget as po
                            JOIN nwbudgetd pod ON po.NO_BUKTI = pod.NO_BUKTI
                            LEFT JOIN nwmassup ON po.KODES = nwmassup.NO_SUPL
                            WHERE po.NO_BUKTI='$no_po' AND po.NO_BUKTI = pod.NO_BUKTI
                            ;
		");

        //dd($query);
        $cleanData = json_decode(json_encode($query), true);
        $PHPJasperXML->setData($cleanData);
        $PHPJasperXML->arrayParameter = [
            "TGL_CTK" => date('d/m/Y'),
        ];
        ob_end_clean();
        $PHPJasperXML->outpage("I");


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
        $result = DB::table('nwbudgetd')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);;
    }

}
