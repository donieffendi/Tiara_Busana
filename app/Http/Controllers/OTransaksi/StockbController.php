<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Stockb;
use App\Models\OTransaksi\StockbDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class StockbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resbelinse
     */
    public $judul = '';
    public $FLAGZ = '';

    public function setFlag(Request $request)
    {
        if ($request->flagz == 'KB') {
            $this->judul = "Stock Opname";
        } else if ($request->flagz == 'MT') {
            $this->judul = "Koreksi Stock Mutasi";
        } else if ($request->flagz == 'POU') {
            $this->judul = "Posting Order Outlet";
        }

        $this->FLAGZ = $request->flagz;

    }

    public function index(Request $request)
    {
        $this->setFlag($request);

        if (in_array($this->FLAGZ, ['POU'])) {
            return view('otransaksi_stockb.post')->with([
                'judul' => $this->judul,
                'flagz' => $this->FLAGZ,
            ]);
        }

        return view('otransaksi_stockb.index')->with([
            'judul' => $this->judul,
            'flagz' => $this->FLAGZ,
        ]);
    }

    public function browse_posting(Request $request)
    {

        $FLAGZ = $request->FLAGZ;
        $CBG   = Auth::user()->CBG;

        $cari = $request->CARI;

        if ($cari == '') {

            $posting = DB::SELECT("SELECT NO_ID,  NO_BUKTI,
                                            TGL, NOTES, TOTAL_QTY, POSTED
                                        FROM bstocka
                                        WHERE CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");

        } else if ($cari != '') {

            $posting = DB::SELECT("SELECT NO_ID,  NO_BUKTI,
                                            TGL, NOTES, TOTAL_QTY, POSTED
                                        FROM bstocka
                                        WHERE NO_BUKTI = '$cari' AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
        }

        return response()->json($posting);
    }

    public function browse(Request $request)
    {
        $golz = $request->GOL;

        $CBG = Auth::user()->CBG;

        $stockb = DB::SELECT("SELECT distinct PO.no_bukti , PO.KODES, PO.NAMAS,
		                  PO.ALAMAT, PO.KOTA from lapbsn, lapbsnd
                          WHERE PO.no_bukti = POD.no_bukti AND PO.GOL ='$golz' AND CBG = '$CBG'
                          AND POD.SISA > 0	");
        return resstockbnse()->json($stockb);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;

        $stockb = DB::SELECT("SELECT no_bukti,TGL,  KODES, NAMAS, TOTAL,  BAYAR,
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from lapbsn
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY no_bukti; ");

        return response()->json($stockb);
    }

    public function post(Request $request)
    {

        return view('otransaksi_stockb.post');
    }

    //SHELVI

    public function browse_detail(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE a.no_bukti='" . $request->NO_PO . "' AND a.KD_BRG = b.KD_BRG ";
        }
        $lapbsnd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from lapbsnd a, brg b
                            $filterbukti ORDER BY no_bukti ");

        return response()->json($lapbsnd);
    }

    public function browse_detail2(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE no_bukti='" . $request->NO_PO . "' AND a.KD_BRG = b.KD_BRG ";
        }
        $lapbsnd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from lapbsnd a, brg b
                            $filterbukti ORDER BY no_bukti ");

        return response()->json($lapbsnd);
    }

    public function browse_brg(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
        $SUPP = $request->KODES;
        $beli = DB::SELECT("SELECT CONCAT(SUB,KDBAR) AS KD_BRG, NMBAR AS NA_BRG, BARCODE, HJ AS HARGA_JL, HB AS HARGA, RAK AS JNS, MARGIN
                            FROM nwmasbar
                            WHERE SUPP = '$SUPP'");
        return response()->json($beli);
    }

       public function browse_sup(Request $request)
    {

        $kirim = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS, ALMT_K AS ALAMAT, KOTA, GOLONGAN, NAMA_B, NO_REK
                            FROM nwmassup");

        return response()->json($kirim);
    }

    public function browse_cnt(Request $request)
    {

        $kirim = DB::SELECT("SELECT CNT, NA_CNT AS NCNT
                            FROM cntbsn");

        return response()->json($kirim);
    }

    public function browse_brgd(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
        $SUPP = $request->KODES;
        $beli = DB::SELECT("SELECT CONCAT(SUB,KDBAR) AS KD_BRG, NMBAR AS NA_BRG
                            FROM nwmasbar
                            WHERE SUPP = '$SUPP'");
        return response()->json($beli);
    }
    // ganti 4

    public function getStockb(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $stockb = DB::SELECT("SELECT no_bukti,tgl,total_qty,notes,type,posted
                                    FROM bstockb
                                    where per='$periode' and flag='$FLAGZ' and cbg='$CBG' union all
                            SELECT no_bukti,tgl,total_qty,notes,type,posted
                                    FROM bstockbz
                                    where per='$periode' and flag='$FLAGZ' and cbg='$CBG'
                            order by no_bukti");

        // ganti 6

        return Datatables::of($stockb)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("stockb/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->no_bukti . ' sudah diposting!\')" href="#" ' : ' href="stockb/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->no_bukti . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')" ';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" href="cetak/' . $row->NO_ID . '">
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
                return;
                '
                    <input type="checkbox" name="cek[]" class="form-control cek" ' . (($row->POSTED == 1) ? "checked" : "") . '  value="' . $row->NO_ID . '" ' . (($row->POSTED == 2) ? "disabled" : "") . '></input>
                    ';

            })

            ->rawColumns(['action', 'cek'])
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
                'TGL' => 'required',

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

        $query = DB::table('lapbsn')->select('no_bukti')->where('per', $periode)->where('flag', 'KB')->where('cbg', $CBG)
            ->orderByDesc('no_bukti')->limit(1)->get();

        if ($query != '[]') {
            $query    = substr($query[0]->no_bukti, -4);
            $query    = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = 'KB' . $tahun . $bulan . '-' . $query . $CBG_KODE;
        } else {
            $no_bukti = 'KB' . $tahun . $bulan . '-0001' . $CBG_KODE;
        }

        $stockb = Stockb::create(
            [
                'no_bukti' => $no_bukti,
                'TGL'      => date('Y-m-d', strtotime($request['TGL'])),
                'per'      => $periode,
                'flag'     => 'KB',
                'notes'    => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'cnt'      => ($request['CNT'] == null) ? "" : $request['CNT'],
                'ncnt'     => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'kodes'    => ($request['KODES'] == null) ? "" : $request['KODES'],
                'namas'    => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'usrnm'    => Auth::user()->username,
                'tg_smp'   => Carbon::now(),
                'CBG'      => $CBG,
            ]
        );

        $REC    = $request->input('REC');
        $KD_BRG = $request->input('KD_BRG');
        $NA_BRG = $request->input('NA_BRG');
        $SALDO  = $request->input('SALDO');
        $RIIL   = $request->input('RIIL');
        $QTY    = $request->input('QTY');
        $HARGA  = $request->input('HARGA');
        $TOTAL  = $request->input('TOTAL');
        $KET    = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new StockbDetail;

                // Insert ke Database
                $detail->no_bukti = $no_bukti;
                $detail->rec      = $REC[$key];
                $detail->per      = $periode;
                $detail->flag     = $FLAGZ;
                $detail->kd_brg   = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->na_brg   = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                $detail->saldo    = (float) str_replace(',', '', $SALDO[$key]);
                $detail->qty      = (float) str_replace(',', '', $QTY[$key]);
                $detail->riil     = (float) str_replace(',', '', $RIIL[$key]);
                $detail->KET      = ($KET[$key] == null) ? "" : $KET[$key];
                $detail->save();
            }
        }

        $no_buktix = $no_bukti;

        $stockb = Stockb::where('no_bukti', $no_buktix)->first();

        DB::SELECT("UPDATE lapbsn,  lapbsnd
                            SET  lapbsnd.ID =  lapbsn.NO_ID  WHERE  lapbsn.no_bukti =  lapbsnd.no_bukti
							AND  lapbsn.no_bukti='$no_buktix';");

        // return redirect('/stockb/edit/?idx=' . $stockb->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/stockb?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ]);

    }

    public function edit(Request $request, Stockb $stockb)
    {

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/stockb')
        // 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }

        $this->setFlag($request);

        $tipx = $request->tipx;

        $idx = $request->idx;

        $CBG = Auth::user()->CBG;

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, no_bukti from lapbsn
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
						 and no_bukti = '$buktix' AND CBG = '$CBG'
		                 ORDER BY no_bukti ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, no_bukti from lapbsn
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
		                 ORDER BY no_bukti ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, no_bukti from lapbsn
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and no_bukti <
					 '$buktix' ORDER BY no_bukti DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'next') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, no_bukti from lapbsn
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and no_bukti >
					 '$buktix' ORDER BY no_bukti ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, no_bukti from lapbsn
						where PER ='$per'
						and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
		              ORDER BY no_bukti DESC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'undo' || $tipx == 'search') {

            $tipx = 'edit';

        }

        if ($idx != 0) {
            $stockb = Stockb::where('NO_ID', $idx)->first();
        } else {
            $stockb      = new Stockb;
            $stockb->tgl = Carbon::now();

        }

        $no_bukti     = $stockb->no_bukti;
        $stockbDetail = DB::table('lapbsnd')->where('no_bukti', $no_bukti)->orderBy('rec')->get();

        $data = [
            'header' => $stockb,
            'detail' => $stockbDetail,

        ];

        return view('otransaksi_stockb.edit', $data)
            ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'judul' => $this->judul]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 18

    public function update(Request $request, Stockb $stockb)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $stockb->update(
            [
                'tgl'    => date('Y-m-d', strtotime($request['TGL'])),
                'notes'  => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'kodes'  => ($request['KODES'] == null) ? "" : $request['KODES'],
                'namas'  => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'usrnm'  => Auth::user()->username,
                'tg_smp' => Carbon::now(),
            ]
        );

        $no_buktix = $stockb->no_bukti;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC = $request->input('REC');

        $KD_BRG = $request->input('KD_BRG');
        $NA_BRG = $request->input('NA_BRG');
        $SALDO  = $request->input('SALDO');
        $QTY  = $request->input('QTY');
        $RIIL  = $request->input('RIIL');
        $KET    = $request->input('KET');

        $query = DB::table('lapbsnd')->where('no_bukti', $request->no_bukti)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = StockbDetail::create(
                    [
                        'no_bukti' => $request->no_bukti,
                        'rec'      => $REC[$i],
                        'per'      => $periode,
                        'flag'     => $this->FLAGZ,
                        'kd_brg'   => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'na_brg'   => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'saldo'    => (float) str_replace(',', '', $SALDO[$i]),
                        'qty'      => (float) str_replace(',', '', $QTY[$i]),
                        'riil'     => (float) str_replace(',', '', $RIIL[$i]),
                        'KET'      => ($KET[$i] == null) ? "" : $KET[$i],

                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = StockbDetail::updateOrCreate(
                    [
                        'no_bukti' => $request->no_bukti,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'rec'    => $REC[$i],

                        'kd_brg' => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'na_brg' => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'saldo'  => (float) str_replace(',', '', $SALDO[$i]),
                        'qty'      => (float) str_replace(',', '', $QTY[$i]),
                        'riil'     => (float) str_replace(',', '', $RIIL[$i]),
                        'ket'    => ($KET[$i] == null) ? "" : $KET[$i],
                    ]
                );
            }
        }

        $stockb = Stockb::where('no_bukti', $no_buktix)->first();

        $no_bukti = $stockb->no_bukti;

        DB::SELECT("UPDATE lapbsn,  lapbsnd
                    SET  lapbsnd.ID =  lapbsn.NO_ID  WHERE  lapbsn.no_bukti =  lapbsnd.no_bukti
                    AND  lapbsn.no_bukti='$no_bukti';");

        // return redirect('/stockb/edit/?idx=' . $stockb->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/stockb?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Stockb $stockb)
    {

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('stockb')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        // }

        $deleteStockb = Stockb::find($stockb->NO_ID);

        $deleteStockb->delete();

        return redirect('/stockb?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ])->with('statusHapus', 'Data ' . $stockb->no_bukti . ' berhasil dihapus');

    }

    public function cetak(Stockb $stockb)
    {
        $no_stockb = $stockb->no_bukti;

        $file         = 'stockbc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT no_bukti,  TGL, KODES, NAMAS, TOTAL_QTY, NOTES, TOTAL, ALAMAT, KOTA
			FROM lapbsn
			WHERE lapbsn.no_bukti='$no_stockb'
			ORDER BY no_bukti;
		");

        $xno_stockb1 = $query[0]->no_bukti;
        $xtgl1       = $query[0]->TGL;
        $xkodes1     = $query[0]->KODES;
        $xnamas1     = $query[0]->NAMAS;
        $xtotal1     = $query[0]->TOTAL_QTY;
        $xnotes1     = $query[0]->NOTES;
        $xharga1     = $query[0]->TOTAL;
        $xalamat1    = $query[0]->ALAMAT;
        $xkota1      = $query[0]->KOTA;

        $PHPJasperXML->arrayParameter = ["HARGA1" => (float) $xharga1, "TOTAL1" => (float) $xtotal1, "NO_PO1"  => (string) $xno_stockb1,
            "TGL1"                                         => (string) $xtgl1, "KODES1"  => (string) $xkodes1, "NAMAS1" => (string) $xnamas1, "NOTES1" => (string) $xnotes1, "ALAMAT1" => (string) $xalamat1, "KOTA1" => (string) $xkota1];
        $PHPJasperXML->arraysqltable = [];

        $query2 = DB::SELECT("
			SELECT no_bukti, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT, NO_PO,  IF ( FLAG='BL' , 'A','B' ) AS FLAG, AJU, BL, EMKL, KD_BRG, NA_BRG, KG, RPHARGA AS HARGA, RPTOTAL AS TOTAL, 0 AS BAYAR,  NOTES
			FROM beli
			WHERE beli.NO_PO='$no_stockb'  UNION ALL
			SELECT no_bukti, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT,  NO_PO,  'C' AS FLAG, '' AS AJU, '' AS BL, '' AS EMKL,  '' AS KD_BRG, '' AS NA_BRG, 0 AS KG,
			0 AS HARGA, 0 AS TOTAL, BAYAR, NOTES
			FROM hut
			WHERE hut.NO_PO='$no_stockb'
			ORDER BY TGL, FLAG, no_bukti;
		");

        $data = [];

        foreach ($query2 as $key => $value) {
            array_push($data, [
                'no_bukti' => $query2[$key]->no_bukti,
                'TGL'      => $query2[$key]->TGL,
                'KODES'    => $query2[$key]->KODES,
                'NAMAS'    => $query2[$key]->NAMAS,
                'ALAMAT'   => $query2[$key]->ALAMAT,
                'AJU'      => $query2[$key]->AJU,
                'BL'       => $query2[$key]->BL,
                'EMKL'     => $query2[$key]->EMKL,
                'KG'       => $query2[$key]->KG,
                'HARGA'    => $query2[$key]->HARGA,
                'TOTAL'    => $query2[$key]->TOTAL,
                'BAYAR'    => $query2[$key]->BAYAR,
                'NOTES'    => $query2[$key]->NOTES,
            ]);
        }

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }

    // function posting (Request $request, Stockb $stockb)
    // {

    //     $REC = $request->input('REC');
    // 	$CEKX = $request->input('CEKX');
    //     $NO_IDX = $request->input('NO_ID');
    //     $no_buktiX = $request->input('no_bukti');
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

    // 			// $no_buktiXZ = ($no_buktiX[$key] == null) ? "" :  $no_buktiX[$key];
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

    // 	return redirect('/stockb/post')->with('statusInsert', 'No Bukti berhasil diupdate');

    // }

    public function posting(Request $request, Stockb $stockb)
    {
        $flagz = $request->flagz;

        $cbg = Auth::user()->CBG;

        $no_bukti_arr = $request->BUKTX ?? [];

        $cek_arr      = $request->CEKX ?? [];
// dd($no_bukti_arr, $cek_arr);
        $hasil = [];

        DB::beginTransaction();

        try {

            for ($i = 0; $i < count($no_bukti_arr); $i++) {

                if (! isset($cek_arr[$i]) || $cek_arr[$i] != 1) {
                    continue;
                }

                $no_bukti = trim($no_bukti_arr[$i]);

                $details = DB::table('bstockad')
                    ->select('QTY', 'KD_BRG', 'NO_ID', 'NO_BUKTI', 'FLAG')
                    ->where('NO_BUKTI', $no_bukti)
                    ->get();

                foreach ($details as $d) {

                    if ($flagz == 'KO') {

                        DB::table('brgbsnd')
                            ->where('KD_BRG', $d->KD_BRG)
                            ->where('CBG', $cbg)
                            ->update([
                                'ln00' => DB::raw("ln00 - {$d->QTY}"),
                                'ak00' => DB::raw("aw00 + ma00 - ke00 + ln00"),
                            ]);
                    }

                }
                DB::table('bstocka')
                    ->where('NO_BUKTI', $no_bukti)
                    ->update([
                        'usrnm'  => Auth::user()->username,
                        'tg_smp' => now(),
                    ]);

                DB::statement('CALL postbstka(?)',  [$no_bukti]);
                $hasil[] = $no_bukti;
            }

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }

        if (count($hasil) == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada No Bukti yang dipilih!',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => implode(', ', $hasil) . ' berhasil diposting',
        ]);
    }

    public function getDetailStockb()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('lapbsnd')->where('no_bukti', $no_bukti)->get();

        return response()->json($result);
    }

}
