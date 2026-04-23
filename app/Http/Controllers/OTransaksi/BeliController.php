<?php

namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Beli;
use App\Models\OTransaksi\BeliDetail;
use App\Models\OTransaksi\Nwagend;
use App\Models\OTransaksi\NwagendDetail;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class BeliController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resbelinse
     */

    var $judul = '';
    var $FLAGZ = '';

    function setFlag(Request $request)
    {
        if ( $request->flagz == 'BS') {
            $this->judul = "Pembelian";
        } else if ( $request->flagz == 'BO') {
            $this->judul = "Terima Barang TGZ";
        } else if ( $request->flagz == 'RX') {
            $this->judul = "Retur Pembelian";
        } else if ( $request->flagz == 'BK') {
            $this->judul = "Retur Pembelian Barang";
        } else if ( $request->flagz == 'LB') {
            $this->judul = "Pembelian Non";
        } else if ( $request->flagz == 'LL') {
            $this->judul = "Retur Pembelian Non";
        }

        $this->FLAGZ = $request->flagz;



    }

    public function index(Request $request)
    {


	    $this->setFlag($request);
        // ganti 3
        return view('otransaksi_beli.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);


    }


	public function post (Request $request)
    {
        $this->setFlag($request);
        return view('otransaksi_beli.post')->with(['flagz' => $this->FLAGZ]);
    }

    public function browse(Request $request)
    {
		$CBG = Auth::user()->CBG;
		$PPN = Auth::user()->PPN;

        $beli = DB::SELECT("SELECT distinct beli.NO_BUKTI , beli.KODES, beli.NAMAS,
		                  beli.ALAMAT, beli.KOTA, beli.PKP, beli.NO_PO, beli.GUDANG from belibsn, belid
                          WHERE beli.NO_BUKTI = belid.NO_BUKTI AND beli.FLAG='BL'
                          AND beli.CBG = '$CBG'
                        --   AND beli.PKP = '$PPN'
                          ");
        return response()->json($beli);
    }

    public function browse_belid(Request $request)
    {
        $golx = $request->GOL;

        $belid = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.SISA,
                            a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, a.PPN, a.DPP, a.DISK,
                            a.QTY2 AS XQTY, a.KALI
                        from belibsnd a, brg b
                        where a.NO_BUKTI='".$request->nobukti."' AND a.KD_BRG = b.KD_BRG");

		return response()->json($belid);
	}


    public function browseuang(Request $request)
    {
        //	$beli = DB::table('beli')->select('NO_BUKTI', 'TGL', 'KODES','NAMAS', 'ALAMAT','KOTA', 'PERB','PERBB', 'SISA' )->where('PERB', '<>' ,'PERBB')->where('LNS', '<>',1)->where('GOL', 'Y')->orderBy('KODES', 'ASC')->get();
        $filterkodes = '';

		$CBG = Auth::user()->CBG;

		if($request->KODES)
		{

			// $filterkodes = " WHERE SISA <> 0 AND KODES='".$request->KODES."' ";
			$filterkodes = " AND  KODES='".$request->KODES."' ";
		}

		$beli = DB::SELECT("SELECT NO_BUKTI, TGL, KODES,
		            NAMAS, NETT as TOTAL, BAYAR, SISA from belibsn  WHERE beli.CBG = '$CBG' and SISA <> 0
		            $filterkodes
                    ORDER BY NO_BUKTI ");

        return response()->json($beli);
    }

    public function browse_posting(Request $request)
    {
        $this->setFlag($request);
        $FLAGZ = $request->FLAGZ;
        $CBG = Auth::user()->CBG;

		$cari = $request->CARI;

		if ($cari == ''){

            $posting = DB::SELECT("SELECT a.NO_ID, a.NO_BUKTI, a.TGL, a.NAMAS, SUM(b.QTY) AS TOTAL_QTY, a.TOTAL, a.NETT,
                                            a.NOTES
                                        FROM NWAGEND a
                                        JOIN NWAGENDD b ON a.NO_BUKTI = b.NO_BUKTI
                                        WHERE a.CBG = '$CBG' AND a.FLAG = '$FLAGZ' AND a.POSTED = '0' ");

        } else if ($cari != ''){

            $posting = DB::SELECT("SELECT a.NO_ID, a.NO_BUKTI, a.TGL, a.NAMAS, SUM(b.QTY) AS TOTAL_QTY, a.TOTAL, a.NETT,
                                            a.NOTES
                                        FROM NWAGEND a
                                        JOIN NWAGENDD b ON a.NO_BUKTI = b.NO_BUKTI
                                        WHERE a.NO_BUKTI = '$cari' AND a.CBG = '$CBG' AND a.FLAG = '$FLAGZ' AND a.POSTED = '0' ");
        }

        return response()->json($posting);
    }

    public function browse_brg(Request $request)
    {
        $KD_BRG = $request->KD_BRG;
		$SUPP = $request->KODES;
        $beli = DB::SELECT("SELECT CONCAT(SUB,KDBAR) AS KD_BRG, NMBAR AS NA_BRG, BARCODE, HJ AS HARGA_JL, HB AS HARGA, RAK AS JNS, MARGIN
                            FROM nwmasbar
                            WHERE SUPP = '$SUPP'");

        if(!empty($KD_BRG)) {
            $beli = DB::SELECT("SELECT KDBAR AS KD_BRG, NMBAR AS NA_BRG, BARCODE, HJ AS HARGA_JL, HB AS HARGA, RAK AS JNS, MARGIN
                            FROM nwmasbar
                            WHERE KDBAR = '$KD_BRG'");
        }
        return response()->json($beli);
    }

    public function browse_sup(Request $request)
    {

    	$beli = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS, ALMT_K AS ALAMAT, KOTA, PPN, GOLONGAN, DISC_PS
                            FROM nwmassup");

        return response()->json($beli);
    }


    public function browse_cnt(Request $request)
    {

    	$beli = DB::SELECT("SELECT CNT, NA_CNT AS NCNT
                            FROM cntbsn");

        return response()->json($beli);
    }



    public function getBeli(Request $request)
    {
        // ganti 5

       if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

		$this->setFlag($request);
        $FLAG = $this->FLAGZ;
		$CBG = session()->get('periode')['cabang'];

        $beli = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, KODES, NAMAS, SP AS NO_PO, TOTAL, NETT, USRNM, POSTED, FLAG
                                    FROM nwagend
                                    where PER = '$periode' AND CBG= '$CBG' AND FLAG= '$FLAG'
                                    order by NO_BUKTI ");


        // ganti 6

        return Datatables::of($beli)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" )
				{
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'".url("beli/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG)."'";
                    // batas

                    $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="beli/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow('.$url.')" ';


                    $btnPrivilege =
                        '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a target="_blank" class="dropdown-item btn btn-danger" href="beli/cetak/?buktix=' . $row->NO_BUKTI . '&flagz=' . $row->FLAG . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a>

                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="cetakBarcode('. $row->NO_ID .')">
                                    <i class="fas fa-id-card"></i>
                                    Cetak Barcode
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
                        <a  class="btn btn-secondary dropdown-toggle btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                'TGL'      => 'required',
            ]
        );

        //////     nomer otomatis
		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;

        $CBG = session()->get('periode')['cabang'];

        $CBG_KODE = DB::table('toko')
            ->where('KODE', $CBG)
            ->value('TYPE');

        $periode = session()->get('periode')['bulan'].'/'.session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $last = DB::table('nwagend')
            ->where('PER', $periode)
            ->where('FLAG', $FLAGZ)
            ->where('CBG', $CBG)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');

        if ($last) {
            preg_match('/-(\d+)/', $last, $matches);
            $angka = isset($matches[1]) ? (int)$matches[1] : 0;

            $urut = str_pad($angka + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urut = '0001';
        }

        $no_bukti = $FLAGZ.$tahun.$bulan.'-'.$urut.$CBG_KODE;

//////////////////////////////////////////////////////////////////////////


        // Insert Header

        // ganti 10

        $beli = Nwagend::create(
            [
                'NO_BUKTI'         => $no_bukti,
                'PER'              => $periode,
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
				'SP'            => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'ST_NOTA'          => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PROM'         => (float) str_replace(',', '', $request['TPROM']),
                'JT'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'ST_PJK'           => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FLAG'             => $FLAGZ,
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TOTAL'           => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'              => (float) str_replace(',', '', $request['TDPP']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'NETT'             => (float) str_replace(',', '', $request['TNETT']),
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
                $detail    = new NwagendDetail();

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->FLAG        = $FLAGZ;
                $detail->KD_BRG      = ($KD_BRG[$key] == null) ? "" :  $KD_BRG[$key];
                $detail->BARCODE      = ($BARCODE[$key] == null) ? "" :  $BARCODE[$key];
                $detail->NA_BRG      = ($NA_BRG[$key] == null) ? "" :  $NA_BRG[$key];
                $detail->JNS      = ($JNS[$key] == null) ? "" :  $JNS[$key];
                $detail->QTY         = (float) str_replace(',', '', $QTY[$key]);
                $detail->HARGA         = (float) str_replace(',', '', $HARGA[$key]);
                $detail->MARGIN           = (float) str_replace(',', '', $MARGIN[$key]);
                $detail->DISKON1      = (float) str_replace(',', '', $DISKON1[$key]);
                $detail->DISKON2       = (float) str_replace(',', '', $DISKON2[$key]);
                $detail->DISKON3       = (float) str_replace(',', '', $DISKON3[$key]);
                $detail->DISKON4       = (float) str_replace(',', '', $DISKON4[$key]);
                $detail->TOTAL       = (float) str_replace(',', '', $TOTAL[$key]);
                $detail->HARGA_JL       = (float) str_replace(',', '', $HARGA_JL[$key]);
                $detail->BLT       = (float) str_replace(',', '', $BLT[$key]);
                $detail->save();

                // update harga terbaru ke master barang jika ada perubahan harga
                $hargaBaru = (float) str_replace(',', '', $HARGA[$key]);

                $barang = DB::table('nwmasbar')
                    ->where('KDBAR', $KD_BRG[$key])
                    ->first();

                if ($barang) {
                    $hbLama = (float) $barang->HB;

                    if ($hbLama != $hargaBaru) {
                        DB::table('nwmasbar')
                            ->where('KDBAR', $KD_BRG[$key])
                            ->update([
                                'HBLAMA' => $hbLama,
                                'HB'     => $hargaBaru
                            ]);
                    }
                }

            }
        }


		$no_buktix = $no_bukti;

		$beli = Nwagend::where('NO_BUKTI', $no_buktix )->first();

        DB::SELECT("UPDATE nwagend,  nwagendd
                            SET  nwagendd.ID = nwagend.NO_ID  WHERE  nwagend.NO_BUKTI =  nwagendd.NO_BUKTI
							AND  nwagend.NO_BUKTI='$no_buktix';");



        // $variablell = DB::select('call beliins(?)', array($no_buktix));

        // return redirect('/beli/edit/?idx=' . $beli->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/beli?flagz='.$FLAGZ)->with(['status' => 'Data berhasil disimpan!', 'flagz' => $FLAGZ ]);


    }


    // ganti 15


   public function edit( Request $request , Nwagend $beli)
    {


		$per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];


        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/beli')
		// 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ]);
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwagend
		                 where PER ='$per' and flag ='$this->FLAGZ'
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


		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwagend
		                 where PER ='$per'
						 and flag ='$this->FLAGZ'
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwagend
		             where PER ='$per'
					 and flag ='$this->FLAGZ'   and NO_BUKTI <
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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwagend
		             where PER ='$per'
					 and flag ='$this->FLAGZ'  and NO_BUKTI >
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

    		$bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwagend
						where PER ='$per'
						and flag ='$this->FLAGZ'
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
			$beli = Nwagend::where('NO_ID', $idx )->first();

	     }
		 else
		 {
				$beli = new Nwagend;
                $beli->TGL = Carbon::now();
                $beli->JTEMPO = Carbon::now();


		 }

        $no_bukti = $beli->NO_BUKTI;
        // $belidetail = DB::table('nwagendd')->where('NO_BUKTI', $no_bukti)->orderBy('rec')->get();

        $belidetail = NwagendDetail::select('nwagendd.*',
                                    'nwmasbar.HBLAMA as HARGALAMA',
                                    'nwmasbar.DIS_A as DISKLAMA1',
                                    'nwmasbar.DIS_B as DISKLAMA2',
                                    'nwmasbar.DIS_C as DISKLAMA3'
                                )
                                ->leftJoin('nwmasbar', 'nwagendd.KD_BRG', '=', 'nwmasbar.KDBAR')
                                ->where('nwagendd.NO_BUKTI', $no_bukti)
                                ->get();

		$data = [
            'header'        => $beli,
			'detail'        => $belidetail

        ];


         return view('otransaksi_beli.edit', $data)
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

    public function update(Request $request, Nwagend $beli)
    {

        $this->validate(
            $request,
            [

                // ganti 19
                'TGL'      => 'required',


            ]
        );

        // ganti 20
        // $variablell = DB::select('call belidel(?)', array($beli['NO_BUKTI']));

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $beli->update(
            [
                'PER'              => $periode,
                'POSTED'           => (float) str_replace(',', '', $request['POSTED']),
				'SP'            => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
				'KODES'            => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'            => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'ST_NOTA'          => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PROM'         => (float) str_replace(',', '', $request['TPROM']),
                'JT'           => date('Y-m-d', strtotime($request['JTEMPO'])),
                'ST_PJK'           => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FLAG'             => $FLAGZ,
                'NOTES'            => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'TOTAL'           => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'              => (float) str_replace(',', '', $request['TDPP']),
                'PPN'              => (float) str_replace(',', '', $request['TPPN']),
                'NETT'             => (float) str_replace(',', '', $request['TNETT']),
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
                'CBG'              => $CBG,
            ]
        );

		$no_buktix = $beli->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

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


        $query = DB::table('nwagendd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID',  $NO_ID)->delete();

        $updatedBarang = [];

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = NwagendDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'FLAG'       => $this->FLAGZ,
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
                $upsert = NwagendDetail::updateOrCreate(
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
            }

            $kdBrg = ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i];
            $hargaBaru = (float) str_replace(',', '', $HARGA[$i]);

            // CEK & UPDATE HB 
            if ($kdBrg != "" && !in_array($kdBrg, $updatedBarang)) {

                $barang = DB::table('nwmasbar')
                    ->where('KDBAR', $kdBrg)
                    ->first();

                if ($barang) {
                    $hbLama = (float) $barang->HB;

                    if ($hbLama != $hargaBaru) {
                        DB::table('nwmasbar')
                            ->where('KDBAR', $kdBrg)
                            ->update([
                                'HBLAMA' => $hbLama,
                                'HB'     => $hargaBaru
                            ]);
                    }
                }

                $updatedBarang[] = $kdBrg;
            }

        }

 		$beli = Nwagend::where('NO_BUKTI', $no_buktix )->first();

        $no_bukti = $beli->NO_BUKTI;

        DB::SELECT("UPDATE nwagend,  nwagendd
                            SET  nwagendd.ID = nwagend.NO_ID  WHERE  nwagend.NO_BUKTI =  nwagendd.NO_BUKTI
							AND  nwagend.NO_BUKTI='$no_buktix';");

        // $variablell = DB::select('call beliins(?)', array($beli['NO_BUKTI']));

        // return redirect('/beli/edit/?idx=' . $beli->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul .  '&golz=' . $this->GOLZ . '');
        return redirect('/beli?flagz='.$FLAGZ)->with(['flagz' => $FLAGZ ]);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Nwagend $beli)
    {

		$this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

		// $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('beli')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }


    //    $variablell = DB::select('call belidel(?)', array($beli['NO_BUKTI']));


        // ganti 23
        DB::table('nwagendd')
            ->where('NO_BUKTI', $beli->NO_BUKTI)
            ->delete();

        $deletebeli = Nwagend::find($beli->NO_ID);

        // ganti 24

        $deletebeli->delete();

        // ganti

       return redirect('/beli?flagz='.$FLAGZ)->with(['flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$beli->NO_BUKTI.' berhasil dihapus');


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
            return redirect('/beli?flagz='.$FLAGZ.'&golz='.$GOLZ)
                ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
                ->with('status', 'Tidak ada data yang dipilih.');
        }

        // Ambil data yang sesuai ID dan masih POSTED = 1
        $postedData = DB::table('beli')
            ->whereIn('NO_ID', $ids)
            ->where('POSTED', 1)
            ->get();

        // Jika semua data belum diposting (POSTED = 0), tampilkan pesan
        if ($postedData->isEmpty()) {
            return redirect('/beli?flagz='.$FLAGZ.'&golz='.$GOLZ)
                ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
                ->with('status', 'No Bukti yang dipilih belum terposting.');
        }

        // Ambil hanya ID yang POSTED = 1 untuk update
        $idsToUpdate = $postedData->pluck('NO_ID')->toArray();

        // Update ke database
        DB::table('beli')
            ->whereIn('NO_ID', $idsToUpdate)
            ->update(['POSTED' => 0]);

        return redirect('/beli?flagz='.$FLAGZ.'&golz='.$GOLZ)
            ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
            ->with('status', 'Berhasil batal posting.');
    }



    public function cetak(Request $request)
    {
        $no_beli = $request->buktix;

        $file     = 'nota-beli';

        $flagz1 = $request->flagz;
        $judul ='';

        if ( $flagz1 =='BL')
        {
                $judul ='Order Pembelian';

        }

        if ( $flagz1 =='RB')
        {
                $judul ='Retur Pembelian';
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT
                                nwagend.NO_BUKTI, nwagend.TGL, nwagend.NO_BUKTI, nwagend.CBG,
                                nwagend.ST_PJK, nwagend.ST_NOTA, nwagend.NAMAS, nwagend.JT AS JTEMPO,
                                nwagend.TOTAL AS BRUTO, nwagend.PROM, nwagend.PPN, nwagend.DPP, nwagend.NETT,
                                nwagendd.KD_BRG, nwagendd.NA_BRG, nwagendd.BARCODE, nwagendd.QTY, nwagendd.HARGA,
                                nwagendd.DISKON1, nwagendd.DISKON2, nwagendd.DISKON3, nwagendd.DISKON4, nwagendd.TOTAL, nwagendd.HARGA_JL,
                                nwmassup.DISC_PS, nwagend.POSTED
                            FROM nwagend
                            JOIN nwagendd
                                ON nwagend.NO_BUKTI = nwagendd.NO_BUKTI
                            LEFT JOIN nwmassup
                                ON nwagend.KODES = nwmassup.NO_SUPL
                            WHERE nwagend.NO_BUKTI = '$no_beli'
                        ");
        // dd($query);

        $POSTED = $query->POSTED;
        if($POSTED == 0) {
            DB::select("call belibsnins(?)", [$no_beli]);
            DB::SELECT("UPDATE nwagend SET POSTED = 1 WHERE NO_BUKTI='$no_beli';");
        }

        $cleanData = json_decode(json_encode($query), true);
        $PHPJasperXML->setData($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }

    public function cetak2 (Beli $beli)
    {
        $no_beli = $beli->NO_BUKTI;

        $file     = 'spbc';

        $flagz1 = $beli->FLAG;
        $judul ='';

        if ( $flagz1 =='BL')
        {
                $judul ='Surat Penerimaan Barang';

        }

        if ( $flagz1 =='RB')
        {
                $judul ='Retur Pembelian';
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT beli.NO_BUKTI, beli.TGL, beli.KODES, beli.NAMAS, beli.TOTAL_QTY, beli.NOTES, beli.ALAMAT,
                                    beli.KOTA, belid.KD_BRG, belid.NA_BRG, belid.SATUAN, belid.QTY2 AS QTY, belid.DISK,
                                    belid.HARGA, belid.TOTAL, belid.KET, beli.TPPN, beli.NETT,
                                    beli.NO_PO, beli.USRNM, belid.KALI, beli.TDISK, beli.TDPP, belid.PPN, belid.DPP
                            FROM beli, belid
                            WHERE beli.NO_BUKTI='$no_beli' AND beli.NO_BUKTI = belid.NO_BUKTI
                            ;
		");

                DB::SELECT("UPDATE beli SET CETAK = 1 WHERE NO_BUKTI='$no_beli';");

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

	// function posting (Request $request, Beli $beli)
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


	// 	return redirect('/beli/post')->with('statusInsert', 'No Bukti berhasil diupdate');



	// }


	public function getDetailbeli(){

        $no_bukti = $_GET['no_bukti'];
        $result = DB::table('belid')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);;
    }

	public function getPpn(Request $request)
    {
        $tgl = $request->tgl;
        $tgl = Carbon::parse($tgl)->format('Y-m-d');

        $data = DB::select("call PPNTGL(?)", [$tgl]);

        return response()->json([
            'ppn' => $data[0]->PPN ?? 0
        ]);
    }

    public function cekHarga(Request $request)
    {
        $kd_brg = $request->kd_brg;
        $hjual  = $request->hjual;

        $data = DB::table('brgbsn')
            ->select('kd_brg', 'hjual')
            ->where('kd_brg', $kd_brg)
            ->where('hjual', '>', $hjual)
            ->first();

        return response()->json($data);
    }

    // POSTING TERIMA BARANG TGZ
    public function postingTerimaTGZ(Request $request)
    {
        $request->validate([
            'cek' => 'required'
        ]);

        DB::beginTransaction();

        try {

            $flagg = $request->flagg;

            //
            $cabangs = DB::table('toko')
                ->where('kode', '!=', '')
                ->pluck('kode');

            foreach ($request->cek as $no_bukti) {

                $header = DB::table('nwagend')
                    ->where('NO_BUKTI', $no_bukti)
                    ->first();

                $CBG = $header->CBG;

                if (!$header || $header->POSTED == 1) continue;

                    //
                    $details = DB::table('nwagendd')
                        ->where('NO_BUKTI', $no_bukti)
                        ->get();

                    foreach ($details as $row) {

                        if ($flagg == 'RX') {

                            DB::update("
                                UPDATE nwmasbard
                                SET MA00 = MA00 - ?,
                                    AK00 = AW00 + MA00 - KE00 + LN00
                                WHERE KD_BRG = ? AND CBG = ?
                            ", [
                                $row->QTY,
                                $row->KD_BRG,
                                $CBG
                            ]);
                        }

                        // BS / BO
                        if (in_array($flagg, ['BS', 'BO'])) {

                            if ($flagg == 'BS') {

                                // update barang master
                                DB::update("
                                    UPDATE nwmasbar
                                    SET TOT_TRM = TOT_TRM + QTY_TRM,
                                        QTY_TRM = ?,
                                        BKT_TRM = ?,
                                        TGL_TRM = ?,
                                        TG_BELI = ?,
                                        HJ = ?
                                    WHERE KDBAR = ?
                                ", [
                                    $row->QTY,
                                    $row->NO_BUKTI,
                                    $header->TGL,
                                    $header->TGL,
                                    $row->HARGA_JL ?? 0,
                                    $row->KD_BRG
                                ]);

                                // update harga ke semua cabang
                                foreach ($cabangs as $cbg) {
                                    DB::update("
                                        UPDATE {$cbg}.masks
                                        SET hj = ?, hjgz = ?, hjmm = ?, hjsp = ?
                                        WHERE kd_brg = ?
                                    ", [
                                        $row->HARGA_JL ?? 0,
                                        $row->HARGA_JL ?? 0,
                                        $row->HARGA_JL ?? 0,
                                        $row->HARGA_JL ?? 0,
                                        $row->KD_BRG
                                    ]);
                                }
                            }

                            // update stok masuk
                            DB::update("
                                UPDATE NWMASBARD
                                SET MA00 = MA00 - ?,
                                    AK00 = AW00 + MA00 - KE00 + LN00
                                WHERE KDBAR = ? AND CBG = ?
                            ", [
                                $row->QTY,
                                $row->KD_BRG,
                                $CBG
                            ]);
                        }
                    }

                    //
                    // DB::statement("CALL postbs(?, ?)", [
                    //     $no_bukti,
                    //     $usr
                    // ]);

                    //
                    DB::table('nwagend')
                        ->where('NO_BUKTI', $no_bukti)
                        ->update([
                            'POSTED' => 1
                        ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Posting berhasil dilakukan'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function cetakBarcode(Request $request)
    {
        $no_bukti = $request->buktix;
        $file     = 'vbrg';
        $data     = DB::SELECT("SELECT belid.KD_BRG, belid.NA_BRG, belid.QTY, brg.KET_UK, brg.BARCODE, beli.TOTAL_QTY, brg.SUB, brg.SUPP
                        FROM beli, belid, brg
                        WHERE beli.NO_BUKTI = belid.NO_BUKTI
                            AND belid.KD_BRG = brg.KD_BRG
                            AND belid.NO_BUKTI = '$no_bukti'");

        // dd($data);
        $finalData = [];
        foreach ($data as $row) {

			// bagi 2 dan bulatkan ke atas
			$qty = (int) $row->QTY;
			$jumlahCetak = ceil($qty / 2);

            for ($i = 0; $i < $jumlahCetak; $i++) {
                $finalData[] = [
                    'KD_BRG'  => $row->KD_BRG,
                    'NA_BRG'  => $row->NA_BRG,
                    'KET_UK'  => $row->KET_UK,
                    'BARCODE' => $row->BARCODE,
                    'SUB'     => $row->SUB,
                    'SUPP'    => $row->SUPP,
                ];
            }
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $cleanData = json_decode(json_encode($data), true);
        $PHPJasperXML->setData($finalData);
        $PHPJasperXML->arrayPageSetting["orientation"] = "L";
        $PHPJasperXML->arrayPageSetting["pageHeight"]  = 1 * 3.7795 * 18; // 1 mm = 3.7795 pixel, 1 ( jumlah row ) x 18 mm ( tinggi row )

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

}
