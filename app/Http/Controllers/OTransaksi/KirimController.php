<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Kirim;
use App\Models\OTransaksi\KirimDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

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

    public $judul = '';
    public $FLAGZ = '';

    public function setFlag(Request $request)
    {
        if ($request->flagz == 'KO') {
            $this->judul = "Pelayanan Outlet";
        }

        $this->FLAGZ = $request->flagz;

    }

    public function index(Request $request)
    {

        $this->setFlag($request);
        // ganti 3
        return view('otransaksi_kirim.index')->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);

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
		                  stocka.ALAMAT, stocka.KOTA, stocka.PKP, stocka.NO_PO, stocka.GUDANG from bstocka, stockad
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
                        from bstockad a, brg b
                        where a.NO_BUKTI='" . $request->nobukti . "' AND a.KD_BRG = b.KD_BRG");

        return response()->json($kirimd);
    }

    public function browseuang(Request $request)
    {
        //	$kirim = DB::table('kirim')->select('NO_BUKTI', 'TGL', 'KODES','NAMAS', 'ALAMAT','KOTA', 'PERB','PERBB', 'SISA' )->where('PERB', '<>' ,'PERBB')->where('LNS', '<>',1)->where('GOL', 'Y')->orderBy('KODES', 'ASC')->get();
        $filterkodes = '';

        $CBG = Auth::user()->CBG;

        if ($request->KODES) {

            // $filterkodes = " WHERE SISA <> 0 AND KODES='".$request->KODES."' ";
            $filterkodes = " AND  KODES='" . $request->KODES . "' ";
        }

        $kirim = DB::SELECT("SELECT NO_BUKTI, TGL, KODES,
		            NAMAS, NETT as TOTAL, BAYAR, SISA from bstocka  WHERE stocka.CBG = '$CBG' and SISA <> 0
		            $filterkodes
                    ORDER BY NO_BUKTI ");

        return response()->json($kirim);
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

        $kirim = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS, ALMT_K AS ALAMAT, KOTA
                            FROM nwmassup");

        return response()->json($kirim);
    }

    public function browse_cnt(Request $request)
    {

        $kirim = DB::SELECT("SELECT CNT, NA_CNT AS NCNT
                            FROM cntbsn");

        return response()->json($kirim);
    }

    // public function getDataByNoPO(Request $request)
    // {
    //     $no_po = $request->no_po;


    //     $dataDetail = DB::table('nwagend')
    //         ->join('nwagendd', 'nwagend.NO_BUKTI', '=', 'nwagendd.NO_BUKTI')
    //         ->where('nwagend.NO_BUKTI', $no_po)
    //         ->select(
    //             'nwagend.NO_BUKTI',
    //             'nwagend.tgl',
    //             'nwagend.kodes',
    //             'nwagend.namas',
    //             // 'nwagend.cnt',
    //             // 'nwagend.ncnt',
    //             'nwagendd.kd_brg',
    //             'nwagendd.na_brg',
    //             'nwagendd.satuan',
    //             'nwagendd.qty',
    //             'nwagendd.harga',
    //             'nwagendd.diskon1',
    //             'nwagendd.diskon2',
    //             'nwagendd.diskon3',
    //             'nwagendd.diskon4',
    //             'nwagendd.margin',
    //             'nwagendd.barcode'
    //         )
    //         ->get();


    //     if ($dataDetail->count() > 0) {

    //         $cnt = $dataDetail->first()->kodes;

    //         $items = [];
    //         foreach ($dataDetail as $row) {
    //             $items[] = [
    //                 'kd_brg'  => $row->kd_brg,
    //                 'na_brg'  => $row->na_brg,
    //                 'satuan'  => $row->satuan,
    //                 'qty'     => (float) $row->qty,
    //                 'harga'   => (float) $row->harga,
    //                 'diskon1' => (float) $row->diskon1,
    //                 'diskon2' => (float) $row->diskon2,
    //                 'diskon3' => (float) $row->diskon3,
    //                 'diskon4' => (float) $row->diskon4,
    //                 'margin'  => (float) $row->margin,
    //                 'barcode' => $row->barcode,
    //             ];

    //         }
    //         // dd($items);

    //         $header = DB::table('nwagend')
    //             ->join('nwagendd', 'nwagend.NO_BUKTI', '=', 'nwagendd.NO_BUKTI')
    //         ->where('nwagend.NO_BUKTI', $no_po)
    //             ->select(
    //                 'nwagendd.margin',
    //                 'nwagendd.pot_prom',
    //                 'nwagend.st_pjk',
    //                 'nwagend.st_nota',
    //                 'nwagend.JT',
    //                 'nwagend.KODES',
    //                 'nwagend.NAMAS',
    //                 'nwagend.PROM',
    //                 'nwagend.PPN',
    //                 // 'cntbsn.kk_sts',
    //                 // 'cntbsn.ctk_lap',
    //                 // 'cntbsn.st_cnt',
    //                 // 'cntbsn.basic',
    //                 // 'cntbsn.cnt_khs',
    //                 // 'cntbsn.lbayar',
    //             )
    //             ->first();
    //             // dd($cnt, $items, $header);

    //         return response()->json([
    //             'success' => true,
    //             'cnt'     => $cnt,
    //             'items'   => $items,
    //             'header'  => $header,
    //         ]);

    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Request tidak valid',
    //     ]);
    // }

    
    public function getDataByNoPO(Request $request)
    {
        $no_po = $request->no_po;


        $dataDetail = DB::table('nwbudget')
            ->join('nwbudgetd', 'nwbudget.NO_BUKTI', '=', 'nwbudgetd.NO_BUKTI')
            ->where('nwbudget.NO_BUKTI', $no_po)
            ->select(
                'nwbudget.NO_BUKTI',
                'nwbudget.tgl',
                'nwbudget.kodes',
                'nwbudget.namas',
                'nwbudgetd.kd_brg',
                'nwbudgetd.na_brg',
                'nwbudgetd.satuan',
                'nwbudgetd.qty',
                'nwbudgetd.harga',
                'nwbudgetd.diskon1',
                'nwbudgetd.diskon2',
                'nwbudgetd.diskon3',
                'nwbudgetd.diskon4',
                'nwbudgetd.margin',
                'nwbudgetd.barcode'
            )
            ->get();


        if ($dataDetail->count() > 0) {

            $cnt = $dataDetail->first()->kodes;

            $items = [];
            foreach ($dataDetail as $row) {
                $items[] = [
                    'kd_brg'  => $row->kd_brg,
                    'na_brg'  => $row->na_brg,
                    'satuan'  => $row->satuan,
                    'qty'     => (float) $row->qty,
                    'harga'   => (float) $row->harga,
                    'diskon1' => (float) $row->diskon1,
                    'diskon2' => (float) $row->diskon2,
                    'diskon3' => (float) $row->diskon3,
                    'diskon4' => (float) $row->diskon4,
                    'margin'  => (float) $row->margin,
                    'barcode' => $row->barcode,
                ];

            }
            // dd($items);

            $header = DB::table('nwbudget')
                ->join('nwbudgetd', 'nwbudget.NO_BUKTI', '=', 'nwbudgetd.NO_BUKTI')
            ->where('nwbudget.NO_BUKTI', $no_po)
                ->select(
                    'nwbudgetd.margin',
                    'nwbudgetd.pot_prom',
                    'nwbudget.st_pjk',
                    'nwbudget.st_nota',
                    'nwbudget.JT',
                    'nwbudget.KODES',
                    'nwbudget.NAMAS',
                    'nwbudget.PROM',
                    'nwbudget.PPN',
                )
                ->first();
                // dd($cnt, $items, $header);

            return response()->json([
                'success' => true,
                'cnt'     => $cnt,
                'items'   => $items,
                'header'  => $header,
            ]);

        }

        return response()->json([
            'success' => false,
            'message' => 'Request tidak valid',
        ]);
    }

    public function getKirim(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $this->setFlag($request);

        $CBG    = Auth::user()->CBG;
        //outlet ini belum
        $OUTLET = $request->cbg_tujuan;

        $FLAG   = $this->FLAGZ;
        // dd($periode, $CBG, $OUTLET, $FLAG);

        $kirim = DB::SELECT("SELECT NO_ID, FLAG as flag, NO_BUKTI, TGL, KODES, NAMAS, NO_PO, total_qty, total, nett, usrnm, POSTED, OUTLET
                                    FROM BSTOCKA
                                    where PER = '$periode' AND CBG= '$CBG' AND FLAG= '$FLAG' AND OUTLET= '$OUTLET'
                            union all
                            SELECT NO_ID, FLAG as flag,NO_BUKTI, TGL, KODES, NAMAS, NO_PO, total_qty, total, nett, usrnm, POSTED, OUTLET
                                    FROM BSTOCKAZ
                                    where PER = PER = '$periode' AND CBG= '$CBG' AND FLAG= '$FLAG' AND OUTLET= '$OUTLET'
                                    order by NO_BUKTI
                          ");

        // ganti 6

        return Datatables::of($kirim)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if ((Auth::user()->divisi == "programmer") || (Auth::user()->divisi == "gudang")) {
                    //CEK POSTED di index dan edit
                    $url = "'" . url("kirim/delete/" . $row->NO_ID . "/?flagz=" . $row->flag) . "'";

                    // $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul .'"';
                    if (Auth::user()->divisi == 'gudang') {
                        // khusus gudang, cek CETAK
                        $btnEdit = ($row->CETAK == 1)
                            ? ' onclick="alert(\'LPB ini sudah dicetak, tidak bisa edit.\')" href="#" '
                            : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul . '"';
                    } else {
                        // user lain, tetap cek POSTED
                        $btnEdit = ($row->POSTED == 1)
                            ? ' onclick="alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" '
                            : ' href="kirim/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul . '"';
                    }

                    // $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="return confirm(&quot; Apakah anda yakin ingin hapus? &quot;)" href="kirim/delete/' . $row->NO_ID . '/?flagz=' . $row->flag . '" ';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege = '
                            <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i> Edit
                            </a>';

                    if (Auth::user()->divisi != 'gudang') {
                        $btnPrivilege .= '
                                <a class="dropdown-item btn btn-danger" target="_blank" href="kirim/cetak/' . $row->NO_BUKTI . '">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </a>';
                    }

                    if (Auth::user()->divisi == 'gudang') {
                        $btnPrivilege .= '
                                <a class="dropdown-item btn btn-danger" target="_blank"  href="kirim/cetak2/' . $row->NO_BUKTI . '">
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

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

        $CBG = Auth::user()->CBG;

        $CBG_KODE = DB::table('toko')
            ->where('KODE', $CBG)
            ->value('TYPE');

        $periode = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $last = DB::table('bstocka')
            ->where('PER', $periode)
            ->where('FLAG', $FLAGZ)
            ->where('CBG', $CBG)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');


        if ($last) {
            $angka = preg_replace('/[^0-9]/', '', $last);
            $urut = str_pad(substr($angka, -4) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urut = '0001';
        }

        $no_bukti = $FLAGZ . $tahun . $bulan . '-' . $urut . $CBG_KODE;

//////////////////////////////////////////////////////////////////////////

        // Insert Header

        // ganti 10

        $kirim = Kirim::create(
            [
                'NO_BUKTI' => $no_bukti,
                'PER'      => $periode,
                'CNT'      => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'     => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'POSTED'   => (float) str_replace(',', '', $request['POSTED']),
                'NO_PO'    => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
                'KODES'    => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'    => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'REF'      => ($request['REF'] == null) ? "" : $request['REF'],
                'MARGIN'   => (float) str_replace(',', '', $request['HMARGIN']),
                'ST_NOTA'  => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'ST_CNT'   => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'TGL'      => date('Y-m-d', strtotime($request['TGL'])),
                'POT_PROM' => (float) str_replace(',', '', $request['POT_PROM']),
                'KK_STS'   => ($request['KK_STS'] == null) ? "" : $request['KK_STS'],
                'BASIC'    => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'JTEMPO'   => date('Y-m-d', strtotime($request['JTEMPO'])),
                'ST_PJK'   => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FORMAL'   => ($request['FORMAL'] == null) ? "" : $request['FORMAL'],
                'NOTA_KHS' => ($request['NOTA_KHS'] == null) ? "" : $request['NOTA_KHS'],
                'flag'     => $FLAGZ,
                'NOTES'    => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'BAYAR'    => (float) str_replace(',', '', $request['BAYAR']),
                'JUMLAH'   => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'      => (float) str_replace(',', '', $request['TDPP']),
                'ppn'      => (float) str_replace(',', '', $request['TPPN']),
                'nett'     => (float) str_replace(',', '', $request['TNETT']),
                'PROM'     => (float) str_replace(',', '', $request['TPROM']),
                'usrnm'    => Auth::user()->username,
                'tg_smp'   => Carbon::now(),
                'CBG'      => $CBG,
                'OUTLET'   => ($request['OUTLET'] == null) ? "" : $request['OUTLET'],
            ]
        );

        $REC      = $request->input('REC');
        $KD_BRG   = $request->input('KD_BRG');
        $BARCODE  = $request->input('BARCODE');
        $NA_BRG   = $request->input('NA_BRG');
        $JNS      = $request->input('JNS');
        $QTY      = $request->input('QTY');
        $HARGA    = $request->input('HARGA');
        $MARGIN   = $request->input('MARGIN');
        $DISKON1  = $request->input('DISKON1');
        $DISKON2  = $request->input('DISKON2');
        $DISKON3  = $request->input('DISKON3');
        $DISKON4  = $request->input('DISKON4');
        $TOTAL    = $request->input('TOTAL');
        $HARGA_JL = $request->input('HARGA_JL');
        $BLT      = $request->input('BLT');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new KirimDetail;

                // Insert ke Database
                $detail->no_bukti = $no_bukti;
                $detail->rec      = $REC[$key];
                $detail->per      = $periode;
                $detail->flag     = $FLAGZ;
                $detail->KD_BRG   = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->BARCODE  = ($BARCODE[$key] == null) ? "" : $BARCODE[$key];
                $detail->NA_BRG   = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                $detail->JNS      = ($JNS[$key] == null) ? "" : $JNS[$key];
                $detail->qty      = (float) str_replace(',', '', $QTY[$key]);
                $detail->harga    = (float) str_replace(',', '', $HARGA[$key]);
                $detail->MARGIN   = (float) str_replace(',', '', $MARGIN[$key]);
                $detail->DISKON1  = (float) str_replace(',', '', $DISKON1[$key]);
                $detail->DISKON2  = (float) str_replace(',', '', $DISKON2[$key]);
                $detail->DISKON3  = (float) str_replace(',', '', $DISKON3[$key]);
                $detail->DISKON4  = (float) str_replace(',', '', $DISKON4[$key]);
                $detail->total    = (float) str_replace(',', '', $TOTAL[$key]);
                $detail->HARGA_JL = (float) str_replace(',', '', $HARGA_JL[$key]);
                $detail->BLT      = (float) str_replace(',', '', $BLT[$key]);
                $detail->save();
            }
        }

        //  ganti 11

        $no_buktix = $no_bukti;

        $kirim = Kirim::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE bstocka,  bstockad
                            SET  bstockad.ID = bstocka.NO_ID  WHERE  bstocka.NO_BUKTI =  bstockad.NO_BUKTI
							AND  bstocka.NO_BUKTI='$no_buktix';");
        //sementara prosedur kirimins dikomen dulu
        //$variablell = DB::select('call kirimins(?)', [$no_buktix]);

        // return redirect('/kirim/edit/?idx=' . $kirim->NO_ID . '&tipx=edit&flagz=' . $FLAGZ . '&judul=' . $this->judul . '&golz=' . $this->GOLZ . '');
        return redirect('/kirim?flagz=' . $FLAGZ)->with(['status' => 'Data Berhasil Disimpan!', 'flagz' => $FLAGZ]);

    }

    // ganti 15

    public function edit(Request $request, Kirim $kirim)
    {

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/kirim')
        // 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ]);
        // }

        $this->setFlag($request);

        $tipx = $request->tipx;

        $idx = $request->idx;

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bstocka
		                 where PER ='$per' and FLAG ='$this->FLAGZ'

						 and NO_BUKTI = '$buktix'
		                 and CBG = '$CBG'

                         ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bstocka
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ'
		                 and CBG = '$CBG'

                         ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bstocka
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'   and NO_BUKTI <
					'$buktix' and CBG = '$CBG'

                    ORDER BY NO_BUKTI DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'next') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bstocka
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'  and NO_BUKTI >
					 '$buktix' and CBG = '$CBG'

                          ORDER BY NO_BUKTI ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bstocka
						where PER ='$per'
						and FLAG ='$this->FLAGZ'
		                and CBG = '$CBG'

                         ORDER BY NO_BUKTI DESC  LIMIT 1");

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
            $kirim = Kirim::where('NO_ID', $idx)->first();
        } else {
            $kirim         = new Kirim;
            $kirim->TGL    = Carbon::now();
            $kirim->JTEMPO = Carbon::now();

        }

        $no_bukti    = $kirim->NO_BUKTI;
        $kirimdetail = DB::table('bstockad')->where('no_bukti', $no_bukti)->orderBy('rec')->get();

        $data = [
            'header' => $kirim,
            'detail' => $kirimdetail,

        ];
        // dd($data);

        return view('otransaksi_kirim.edit', $data)
            ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'judul' => $this->judul]);

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
                'TGL' => 'required',

            ]
        );

        // ganti 20
        // $variablell = DB::select('call kirimdel(?)', [$kirim['NO_BUKTI']]);

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ  = $this->GOLZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ganti 20

        $kirim->update(
            [
                'TGL'      => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'   => date('Y-m-d', strtotime($request['JTEMPO'])),
                'CNT'      => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'     => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'POSTED'   => (float) str_replace(',', '', $request['POSTED']),
                'NO_PO'    => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
                'KODES'    => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'    => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'REF'      => ($request['REF'] == null) ? "" : $request['REF'],
                'MARGIN'   => (float) str_replace(',', '', $request['MARGIN']),
                'ST_NOTA'  => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'ST_CNT'   => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'POT_PROM' => (float) str_replace(',', '', $request['POT_PROM']),
                'KK_STS'   => ($request['KK_STS'] == null) ? "" : $request['KK_STS'],
                'BASIC'    => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'ST_PJK'   => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'FORMAL'   => ($request['FORMAL'] == null) ? "" : $request['FORMAL'],
                'NOTA_KHS' => ($request['NOTA_KHS'] == null) ? "" : $request['NOTA_KHS'],
                'NOTES'    => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'BAYAR'    => (float) str_replace(',', '', $request['BAYAR']),
                'JUMLAH'   => (float) str_replace(',', '', $request['TJUMLAH']),
                'DPP'      => (float) str_replace(',', '', $request['TDPP']),
                'PPN'      => (float) str_replace(',', '', $request['TPPN']),
                'NETT'     => (float) str_replace(',', '', $request['TNETT']),
                'PROM'     => (float) str_replace(',', '', $request['TPROM']),
                'usrnm'    => Auth::user()->username,
                'tg_smp'   => Carbon::now(),
                'CBG'      => $CBG,
                'OUTLET'   => ($request['OUTLET'] == null) ? "" : $request['OUTLET'],
            ]
        );

        $no_buktix = $kirim->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC = $request->input('REC');

        $REC      = $request->input('REC');
        $KD_BRG   = $request->input('KD_BRG');
        $BARCODE  = $request->input('BARCODE');
        $NA_BRG   = $request->input('NA_BRG');
        $JNS      = $request->input('JNS');
        $QTY      = $request->input('QTY');
        $HARGA    = $request->input('HARGA');
        $MARGIN   = $request->input('MARGIN');
        $DISKON1  = $request->input('DISKON1');
        $DISKON2  = $request->input('DISKON2');
        $DISKON3  = $request->input('DISKON3');
        $DISKON4  = $request->input('DISKON4');
        $TOTAL    = $request->input('TOTAL');
        $HARGA_JL = $request->input('HARGA_JL');
        $BLT      = $request->input('BLT');

        $query = DB::table('stockad')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = kirimdetail::create(
                    [
                        'no_bukti' => $request->NO_BUKTI,
                        'rec'      => $REC[$i],
                        'per'      => $periode,
                        'flag'     => $this->FLAGZ,
                        'KD_BRG'   => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'barcode'  => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'NA_BRG'   => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'JNS'      => ($JNS[$i] == null) ? "" : $JNS[$i],
                        'qty'      => (float) str_replace(',', '', $QTY[$i]),
                        'harga'    => (float) str_replace(',', '', $HARGA[$i]),
                        'MARGIN'   => (float) str_replace(',', '', $MARGIN[$i]),
                        'DISKON1'  => (float) str_replace(',', '', $DISKON1[$i]),
                        'DISKON2'  => (float) str_replace(',', '', $DISKON2[$i]),
                        'DISKON3'  => (float) str_replace(',', '', $DISKON3[$i]),
                        'DISKON4'  => (float) str_replace(',', '', $DISKON4[$i]),
                        'total'    => (float) str_replace(',', '', $TOTAL[$i]),
                        'HARGA_JL' => (float) str_replace(',', '', $HARGA_JL[$i]),
                        'BLT'      => (float) str_replace(',', '', $BLT[$i]),
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = KirimDetail::updateOrCreate(
                    [
                        'no_bukti' => $request->NO_BUKTI,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'rec'      => $REC[$i],

                        'KD_BRG'   => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'BARCODE'  => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'NA_BRG'   => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'JNS'      => ($JNS[$i] == null) ? "" : $JNS[$i],
                        'qty'      => (float) str_replace(',', '', $QTY[$i]),
                        'harga'    => (float) str_replace(',', '', $HARGA[$i]),
                        'MARGIN'   => (float) str_replace(',', '', $MARGIN[$i]),
                        'DISKON1'  => (float) str_replace(',', '', $DISKON1[$i]),
                        'DISKON2'  => (float) str_replace(',', '', $DISKON2[$i]),
                        'DISKON3'  => (float) str_replace(',', '', $DISKON3[$i]),
                        'DISKON4'  => (float) str_replace(',', '', $DISKON4[$i]),
                        'total'    => (float) str_replace(',', '', $TOTAL[$i]),
                        'HARGA_JL' => (float) str_replace(',', '', $HARGA_JL[$i]),
                        'BLT'      => (float) str_replace(',', '', $BLT[$i]),
                    ]
                );
            }
        }

        //  ganti 21

        $kirim = kirim::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $kirim->NO_BUKTI;

        DB::SELECT("UPDATE bstocka,  bstockad
                    SET  bstockad.ID =  bstocka.NO_ID  WHERE  bstocka.NO_BUKTI =  bstockad.NO_BUKTI
                    AND  bstocka.NO_BUKTI='$no_bukti';");

        // $variablell = DB::select('call kirimins(?)', [$kirim['NO_BUKTI']]);

        // return redirect('/kirim/edit/?idx=' . $kirim->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul .  '&golz=' . $this->GOLZ . '');
        return redirect('/kirim?flagz=' . $FLAGZ)->with(['status' => 'Data berhasil Di Update', 'flagz' => $FLAGZ]);

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
        $judul = $this->judul;

        // $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('kirim')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }

        $variablell = DB::select('call kirimdel(?)', [$kirim['NO_BUKTI']]); //

        // ganti 23

        $deletekirim = Kirim::find($kirim->NO_ID);

        // ganti 24

        $deletekirim->delete();

        // ganti

        return redirect('/kirim?flagz=' . $FLAGZ)->with(['flagz' => $FLAGZ])->with('statusHapus', 'Data ' . $kirim->NO_BUKTI . ' berhasil dihapus');

    }

    public function batal_post(Request $request)
    {
        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ  = $this->GOLZ;
        $judul = $this->judul;

        // Ambil array dari checkbox
        $ids = $request->input('batal_post');

        // Cek apakah ada ID yang dipilih
        if (! $ids || count($ids) === 0) {
            return redirect('/kirim?flagz=' . $FLAGZ . '&golz=' . $GOLZ)
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
            return redirect('/kirim?flagz=' . $FLAGZ . '&golz=' . $GOLZ)
                ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
                ->with('status', 'No Bukti yang dipilih belum terposting.');
        }

        // Ambil hanya ID yang POSTED = 1 untuk update
        $idsToUpdate = $postedData->pluck('NO_ID')->toArray();

        // Update ke database
        DB::table('stocka')
            ->whereIn('NO_ID', $idsToUpdate)
            ->update(['POSTED' => 0]);

        return redirect('/kirim?flagz=' . $FLAGZ . '&golz=' . $GOLZ)
            ->with(['judul' => $judul, 'flagz' => $FLAGZ, 'golz' => $GOLZ])
            ->with('status', 'Berhasil batal posting.');
    }

    public function cetak(Request $request, $kirim)
    {
        $bukti = $kirim;
        $file = 'pelayanan_outlet';

        $judul  = '';
        $cbg = Auth::user()->CBG;

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }


        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::select("SELECT '$periode' as PER,
                                bstocka.outlet, bstocka.NO_BUKTI, bstocka.ref, bstocka.NO_po, bstocka.tgl, bstocka.JTEMPO,
                                bstocka.CNT, bstocka.NCNT, bstocka.POT_PROM, bstocka.ST_PJK,
                                bstocka.ST_NOTA, bstocka.KK_STS, bstocka.FORMAL, bstocka.St_CNT, bstocka.BASIC, bstocka.NOTA_KHS,
                                bstockad.KD_BRG, bstockad.NA_BRG, brgbsn.BARCODE,
                                brgbsn.jns, bstockad.qty, bstockad.harga,
                                bstockad.DISKON1, bstockad.DISKON2, bstockad.DISKON3, bstockad.DISKON4,
                                bstockad.total, bstockad.HARGA_JL,
                                bstocka.total as totalh, bstocka.prom as promh,
                                bstocka.dpp as dpph, bstocka.ppn as ppnh, bstocka.nett as netth,
                                bstocka.outlet, toko.na_toko
                            FROM bstocka
                            LEFT JOIN toko ON bstocka.outlet = toko.kode
                            LEFT JOIN bstockad ON bstocka.NO_BUKTI = bstockad.no_bukti
                            LEFT JOIN brgbsn ON bstockad.KD_BRG = brgbsn.KD_BRG
                            WHERE bstocka.no_bukti = ?

                            UNION ALL

                            SELECT '$periode' as PER,
                                bstockaz.outlet, bstockaz.NO_BUKTI, bstockaz.ref, bstockaz.NO_po, bstockaz.tgl, bstockaz.JTEMPO,
                                bstockaz.CNT, bstockaz.NCNT, bstockaz.POT_PROM, bstockaz.ST_PJK,
                                bstockaz.ST_NOTA, bstockaz.KK_STS, bstockaz.FORMAL, bstockaz.St_CNT, bstockaz.BASIC, bstockaz.NOTA_KHS,
                                bstockazd.KD_BRG, bstockazd.NA_BRG, brgbsn.BARCODE,
                                brgbsn.jns, bstockazd.qty, bstockazd.harga,
                                bstockazd.DISKON1, bstockazd.DISKON2, bstockazd.DISKON3, bstockazd.DISKON4,
                                bstockazd.total, bstockazd.HARGA_JL,
                                bstockaz.total as totalh, bstockaz.prom as promh,
                                bstockaz.dpp as dpph, bstockaz.ppn as ppnh, bstockaz.nett as netth,
                                bstockaz.outlet, toko.na_toko
                            FROM bstockaz
                            LEFT JOIN toko ON bstockaz.outlet = toko.kode
                            LEFT JOIN bstockazd ON bstockaz.NO_BUKTI = bstockazd.no_bukti
                            LEFT JOIN brgbsn ON bstockazd.KD_BRG = brgbsn.KD_BRG
                            WHERE bstockaz.no_bukti = ?
                        ", [$bukti, $bukti]);
        // dd($query);

        // DB::SELECT("UPDATE stocka SET POSTED = 1 WHERE NO_BUKTI='$no_kirim';");

        $data = json_decode(json_encode($query), true);
        $PHPJasperXML->arrayParameter = [
            "CBG"   => $cbg,
        ];

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }

    public function cetak2(Kirim $kirim)
    {
        $no_kirim = $kirim->NO_BUKTI;

        $file = 'spbc';

        $flagz1 = $kirim->FLAG;
        $judul  = '';

        if ($flagz1 == 'BL') {
            $judul = 'Surat Penerimaan Barang';

        }

        if ($flagz1 == 'RB') {
            $judul = 'Retur Pemkiriman';
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
            array_push($data, [
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'KODES'    => $query[$key]->KODES,
                'NAMAS'    => $query[$key]->NAMAS,
                'ALAMAT'   => $query[$key]->ALAMAT,
                'KOTA'     => $query[$key]->KOTA,
                'KG'       => $query[$key]->KG,
                'HARGA'    => $query[$key]->HARGA,
                'TOTAL'    => $query[$key]->TOTAL,
                'BAYAR'    => $query[$key]->BAYAR,
                'NOTES'    => $query[$key]->NOTES,
                'KD_BRG'   => $query[$key]->KD_BRG,
                'NA_BRG'   => $query[$key]->NA_BRG,
                'SATUAN'   => $query[$key]->SATUAN,
                'QTY'      => $query[$key]->QTY,
                'DISK'     => $query[$key]->DISK,
                'NETT'     => $query[$key]->NETT,
                'KET'      => $query[$key]->KET,
                'NO_PO'    => $query[$key]->NO_PO,
                'JUDUL'    => $judul,
                'USRNM'    => $query[$key]->USRNM,
                'KALI'     => $query[$key]->KALI,
                'TPPN'     => $query[$key]->TPPN,
                'TDISK'    => $query[$key]->TDISK,
                'TDPP'     => $query[$key]->TDPP,
                'PPN'      => $query[$key]->PPN,
                'DPP'      => $query[$key]->DPP,
            ]);
        }

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }

    public function posting(Request $request)
    {

    }

    public function getDetailkirim()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('stockad')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);
    }

}
