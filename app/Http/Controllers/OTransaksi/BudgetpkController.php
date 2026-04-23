<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Master\Sup;
use App\Models\OTransaksi\Nwbudget;
use App\Models\OTransaksi\NwbudgetDetail;
use App\Models\OTransaksi\Po;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class BudgetpkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $per = DB::select("SELECT PERIO FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        // ganti 3
        return view('otransaksi_budgetpk.index')->with(['per' => $per]);
    }

    public function browse(Request $request)
    {
        $tanggal = date('Y-m-d');
        $CBG     = Auth::user()->CBG;
        $kodes   = $request->kodes;

        //
        $budgetpk = DB::SELECT("SELECT distinct PO.NO_SP , PO.KODES, PO.NAMAS,
	                    PO.ALAMAT, PO.KOTA, PO.JTEMPO, PO.NOTES
                        from nwbudget as po, nwbudgetd as pod
                        WHERE PO.NO_SP = POD.NO_SP AND po.KODES='$kodes'
                        AND POD.SISA > 0 AND po.POSTED = 1 AND po.JTEMPO > '$tanggal'
                        GROUP BY NO_SP ");
        return response()->json($budgetpk);
    }

    public function browse_brg(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
        $sup      = $request->sup;
        $budgetpk = DB::SELECT("SELECT KDBAR, NMBAR, BARCODE, HB AS HARGA, 1 AS STOK FROM nwmasbar WHERE SUPP = '$sup'");
        return response()->json($budgetpk);
    }

    public function browse_sup(Request $request)
    {

        if (! empty(request('q'))) {

            $budgetpk = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA
                            from nwmassup
                            WHERE  NAMA LIKE ('%$request->q%')
                            ORDER BY NAMA ");

        } else {
            $budgetpk = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA
                            from nwmassup

                            ORDER BY NAMA ");
        }

        return response()->json($budgetpk);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;

        $budgetpk = DB::SELECT("SELECT NO_SP,TGL,  KODES, NAMAS, TOTAL,  BAYAR,
                                TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from po
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_SP; ");

        return response()->json($budgetpk);
    }

    public function index_posting(Request $request)
    {

        return view('otransaksi_budgetpk.post');
    }

    public function browse_pod(Request $request)
    {
        $sup = $request->kodes;

        $budgetpkd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.BARCODE, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA, a.TOTAL,
                                a.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.HJ, b.MARGIN, b.RAK AS JNS
                            from nwbudgetd a
                            LEFT JOIN nwmasbar b
                                ON b.KDBAR = a.KD_BRG
                            where a.NO_SP='" . $request->nobukti . "' ");

        return response()->json($budgetpkd);
    }

    public function browse_detail(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE a.NO_SP='" . $request->NO_PO . "' AND a.KD_BHN = b.KD_BHN ";
        }
        $budgetpkd = DB::SELECT("SELECT a.REC, a.KD_BHN, a.NA_BHN, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI
                            from pod a, bhn b
                            $filterbukti ORDER BY NO_SP ");

        return response()->json($budgetpkd);
    }

    public function browse_detail2(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE NO_SP='" . $request->NO_PO . "' AND a.KD_BRG = b.KD_BRG ";
        }
        $budgetpkd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, b.KALI AS KALI
                            from pod a, brg b
                            $filterbukti ORDER BY NO_SP ");

        return response()->json($budgetpkd);
    }
    // ganti 4

    public function getBudgetpk(Request $request)
    {
        // ganti 5

        // $periode = $request->per;
        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $CBG = Auth::user()->CBG;
        $PPN = Auth::user()->PPN;

        $budgetpk = DB::SELECT("
            SELECT *
            FROM nwbudget
            WHERE PER= '$periode'
            ORDER BY NO_SP
        ");

        // ganti 6

        return Datatables::of($budgetpk)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("budgetpk/delete/" . $row->NO_ID) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_SP . ' sudah diposting!\')" href="#" ' : ' href="budgetpk/edit/?idx=' . $row->NO_ID . '&tipx=edit';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_SP . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="budgetpk/cetak/' . $row->NO_ID . '">
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
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            // GANTI 9

            [
                //               'NO_PO'       => 'required',
                'TGL' => 'required',

            ]
        );

        //////     nomer otomatis

        $kodesx = $request->KODES;

        $CBG = Auth::user()->CBG;

        /////////////////////////////////////////

        /////////////////////////////////////////

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $query = DB::table('nwbudget')->select('NO_SP')->where('PER', $periode)->where('CBG', $CBG)
            ->orderByDesc('NO_SP')->limit(1)->get();

        if ($query != '[]') {
            $query    = substr($query[0]->NO_SP, -4);
            $query    = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = 'PA' . $CBG . $tahun . $bulan . '-' . $query;
        } else {
            $no_bukti = 'PA' . $CBG . $tahun . $bulan . '-0001';
        }

        $budgetpk = Nwbudget::create(
            [
                'NO_SP'   => $no_bukti,
                'TGL'     => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'  => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'     => $periode,
                'CNT'     => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NA_CNT'  => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'KODES'   => ($request['KODES'] == null) ? "" : $request['KODES'],
                'NAMAS'   => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'CBG'     => ($request['CBG'] == null) ? "" : $request['CBG'],
                'NOTES'   => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'Q_SALDO' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'R_SALDO' => (float) str_replace(',', '', $request['TTOTAL']),
                'USRNM'   => Auth::user()->username,
                'TG_SMP'  => Carbon::now(),
            ]
        );

        $REC     = $request->input('REC');
        $KD_BRG  = $request->input('KD_BRG');
        $NA_BRG  = $request->input('NA_BRG');
        $BARCODE = $request->input('BARCODE');
        $QTY     = $request->input('QTY');
        $HARGA   = $request->input('HARGA');
        $TOTAL   = $request->input('TOTAL');
        $SISA    = $request->input('SISA');
        $KDLAKU  = $request->input('KDLAKU');
        $KET     = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new NwbudgetDetail;

                // Insert ke Database
                $detail->NO_SP   = $no_bukti;
                $detail->REC     = $REC[$key];
                $detail->PER     = $periode;
                $detail->CBG     = $CBG;
                $detail->KD_BRG  = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->NA_BRG  = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                $detail->BARCODE = ($BARCODE[$key] == null) ? "" : $BARCODE[$key];
                $detail->QTY     = (float) str_replace(',', '', $QTY[$key]);
                $detail->HARGA   = (float) str_replace(',', '', $HARGA[$key]);
                $detail->TOTAL   = (float) str_replace(',', '', $TOTAL[$key]);
                $detail->SISA    = (float) str_replace(',', '', $QTY[$key]);
                $detail->KDLAKU  = ($KDLAKU[$key] == null) ? "" : $KDLAKU[$key];
                $detail->KET     = ($KET[$key] == null) ? "" : $KET[$key];
                $detail->save();
            }
        }

        $no_buktix = $no_bukti;

        $budgetpk = Nwbudget::where('NO_SP', $no_buktix)->first();

        DB::SELECT("UPDATE nwbudget, nwmassup
                    SET nwbudget.NAMAS = nwmassup.NAMA  WHERE nwbudget.KODES = nwmassup.NO_SUPL
                    AND nwbudget.NO_SP='$no_buktix';");

        DB::SELECT("UPDATE nwbudget, cntbsn
                    SET nwbudget.NA_CNT = cntbsn.NA_CNT  WHERE nwbudget.CNT = cntbsn.CNT
                    AND nwbudget.NO_SP='$no_buktix';");

        DB::SELECT("UPDATE nwbudget,  nwbudgetd
                            SET  nwbudgetd.ID =  nwbudget.NO_ID  WHERE  nwbudget.NO_SP =  nwbudgetd.no_bukti
							AND  nwbudget.NO_SP='$no_buktix';");

        return redirect('/budgetpk')->with('statusInsert', 'Data baru berhasil ditambahkan');

    }

    public function edit(Request $request, Nwbudget $budgetpk)
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

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_SP from nwbudget
		                 where PER ='$per'
                         AND CBG = '$CBG'
						 and NO_SP = '$buktix'
		                 ORDER BY NO_SP ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_SP from nwbudget
		                 where PER ='$per'
                         AND CBG = '$CBG'
		                 ORDER BY NO_SP ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_SP from nwbudget
		             where PER ='$per'
                     AND CBG = '$CBG'
                     and NO_SP <
					 '$buktix' ORDER BY NO_SP DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'next') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_SP from nwbudget
		             where PER ='$per'
                     AND CBG = '$CBG'
                     and NO_SP >
					 '$buktix' ORDER BY NO_SP ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_SP from nwbudget
						where PER ='$per'
                        AND CBG = '$CBG'
		                ORDER BY NO_SP DESC  LIMIT 1");

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
            $budgetpk = Nwbudget::where('NO_ID', $idx)->first();
        } else {
            $budgetpk         = new Nwbudget;
            $budgetpk->TGL    = Carbon::now();
            $budgetpk->JTEMPO = Carbon::now();

        }

        $no_bukti = $budgetpk->NO_BUKTI;

        $budgetpkDetail = DB::table('nwbudgetd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

        $data = [
            'header' => $budgetpk,
            'detail' => $budgetpkDetail,

        ];

        return view('otransaksi_budgetpk.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Nwbudget $budgetpk)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );

        // $variablell = DB::select('call podel(?)', array($budgetpk['NO_SP']));

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $budgetpk->update(
            [

                'TGL'     => date('Y-m-d', strtotime($request['TGL'])),
                'JTEMPO'  => date('Y-m-d', strtotime($request['JTEMPO'])),
                'CNT'     => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NA_CNT'  => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'KODES'   => ($request['KODES'] == null) ? "" : $request['KODES'],
                'CBG'     => $CBG,
                'NOTES'   => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'Q_SALDO' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'R_SALDO' => (float) str_replace(',', '', $request['TTOTAL']),
                'USRNM'   => Auth::user()->username,
                'TG_SMP'  => Carbon::now(),
            ]
        );

        $no_buktix = $budgetpk->NO_SP;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC = $request->input('REC');

        $KD_BRG  = $request->input('KD_BRG');
        $NA_BRG  = $request->input('NA_BRG');
        $BARCODE = $request->input('BARCODE');
        $QTY     = $request->input('QTY');
        $HARGA   = $request->input('HARGA');
        $TOTAL   = $request->input('TOTAL');
        $SISA    = $request->input('SISA');
        $KDLAKU  = $request->input('KDLAKU');
        $KET     = $request->input('KET');

        $query = DB::table('nwbudgetd')->where('no_bukti', $request->no_bukti)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = NwbudgetDetail::create(
                    [
                        'NO_SP'   => $request->no_bukti,
                        'REC'     => $REC[$i],
                        'PER'     => $periode,
                        'CBG'     => $CBG,
                        'KD_BRG'  => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'NA_BRG'  => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'BARCODE' => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'QTY'     => (float) str_replace(',', '', $QTY[$i]),
                        'HARGA'   => (float) str_replace(',', '', $HARGA[$i]),
                        'TOTAL'   => (float) str_replace(',', '', $TOTAL[$i]),
                        'SISA'    => (float) str_replace(',', '', $SISA[$i]),
                        'KDLAKU'  => ($KDLAKU[$i] == null) ? "" : $KDLAKU[$i],
                        'KET'     => ($KET[$i] == null) ? "" : $KET[$i],
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = NwbudgetDetail::updateOrCreate(
                    [
                        'NO_SP' => $request->NO_SP,
                        'NO_ID' => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'REC'     => $REC[$i],
                        'CBG'     => $CBG,
                        'per'     => $periode,
                        'KD_BRG'  => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'NA_BRG'  => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'BARCODE' => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'qty'     => (float) str_replace(',', '', $QTY[$i]),
                        'harga'   => (float) str_replace(',', '', $HARGA[$i]),
                        'total'   => (float) str_replace(',', '', $TOTAL[$i]),
                        'SISA'    => (float) str_replace(',', '', $SISA[$i]),
                        'KDLAKU'  => ($KDLAKU[$i] == null) ? "" : $KDLAKU[$i],
                        'KET'     => ($KET[$i] == null) ? "" : $KET[$i],
                    ]
                );
            }
        }

        $budgetpk = Nwbudget::where('NO_SP', $no_buktix)->first();

        $no_bukti = $budgetpk->NO_SP;

        DB::SELECT("UPDATE nwbudget, nwmassup
                    SET nwbudget.NAMAS = nwmassup.NAMA WHERE nwbudget.KODES = nwmassup.NO_SUPL
                    AND nwbudget.NO_SP='$no_buktix';");

        DB::SELECT("UPDATE nwbudget, cntbsn
                    SET nwbudget.NA_CNT = cntbsn.NA_CNT  WHERE nwbudget.CNT = cntbsn.CNT
                    AND nwbudget.NO_SP='$no_buktix';");

        DB::SELECT("UPDATE pobsn,  nwbudgetd
                    SET  nwbudgetd.ID =  pobsn.NO_ID  WHERE  nwbudget.NO_SP =  nwbudget.NO_SP
                    AND  nwbudget.NO_SP='$no_bukti';");

        // $variablell = DB::select('call poins(?)', array($budgetpk['NO_SP']));

        return redirect('/budgetpk')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Po $budgetpk)
    {

        // hapus detail dulu
        DB::table('nwbudgetd')->where('ID', $budgetpk->NO_ID)->delete();

        // hapus header
        DB::table('nwbudget')->where('NO_ID', $budgetpk->NO_ID)->delete();

        return redirect('/po')->with('statusHapus', 'Data ' . $budgetpk->NO_SP . ' berhasil dihapus');
    }

    public function cetak(Nwbudget $budgetpk, Request $request)
    {
        $no_po = $budgetpk->NO_SP;
        $tipe  = $request->tipe;

        if ($tipe == 'lampiran') {
            $file = 'poc_l';
        } else {
            $file = 'poc';
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $data  = DB::table('nwbudget')->where('NO_SP', $no_po)->first();
        $jenis = ($data->POSTED == 0) ? 'ASLI' : 'COPY';

        if ($tipe != 'lampiran') {
            DB::update("UPDATE nwbudget SET POSTED = 1 WHERE NO_SP = ?", [$no_po]);
        }

        $query = DB::SELECT("SELECT po.NO_SP, po.TGL, po.PER, po.CBG, po.KODES, po.NAMAS, po.Q_SALDO AS TOTAL_QTY, po.NOTES,
                                    pod.KD_BRG, pod.BARCODE, pod.NA_BRG, pod.SATUAN, pod.qty AS QTY,
                                    pod.HARGA, pod.TOTAL, pod.KET,
                                    po.JTEMPO, nwmassup.ALMT_K AS ALAMAT, nwmassup.KOTA, nwmassup.GOLONGAN AS PKP,
                                    nwmassup.CARA, nwmassup.TLP_R, nwmassup.NO_FAX, '$jenis' as COPY
                            FROM nwbudget as po
                            JOIN nwbudgetd pod ON po.NO_SP = pod.NO_SP
                            LEFT JOIN nwmassup ON po.KODES = nwmassup.NO_SUPL
                            WHERE po.NO_SP='$no_po' AND po.NO_SP = pod.NO_SP
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

        $CEK   = $request->input('cek');
        $NO_SP = $request->input('NO_SP');

        $usrnmx = Auth::user()->username;

        $hasil = "";

        if ($CEK) {
            foreach ($CEK as $key => $value) {

                //$STA = $request->input('STA');

                $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
                $bulan   = session()->get('periode')['bulan'];
                $tahun   = substr(session()->get('periode')['tahun'], -2);

                $NO_SPXZ = $NO_SP[$key];

                DB::SELECT("UPDATE po SET POSTED = 1 WHERE po.NO_SP='$NO_SPXZ'");

            }
        } else {
            $hasil = $hasil . "Tidak ada PO yang dipilih! ; ";
        }

        if ($hasil != '') {
            return redirect('/budgetpkindex-posting')->with('status', 'Proses Posting PO ..')->with('gagal', $hasil);
        } else {
            return redirect('/budgetpkindex-posting')->with('status', 'Posting Posting PO selesai..');
        }

    }

    public function jtempo(Request $request)
    {
        $tgl   = $request->input('TGL');
        $hari  = substr($tgl, 0, 2);
        $bulan = substr($tgl, 3, 2);
        $tahun = substr($tgl, 6, 4);
        $harix = $request->HARI;

        $datex = Carbon::createFromDate($tahun, $bulan, $hari);

        $datex->addDays($harix);

        $datey = $datex->format('d-m-Y');
        return $datey;

    }

    public function getDetailPo()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('nwbudgetd')->where('NO_SP', $no_bukti)->get();

        return response()->json($result);
    }

    public function budgetpkOtomatis()
    {
        $bulan     = str_pad(session()->get('periode')['bulan'], 2, '0', STR_PAD_LEFT);
        $tahunFull = session()->get('periode')['tahun'];
        $tahun     = substr($tahunFull, -2);
        $periode   = $bulan . '/' . $tahunFull;

        // $CBG       = Auth::user()->CBG;
        $CBG       = "TGZ";
        $total_qty = 0;

        $bulanSebelumnya = (int) $bulan - 1;

        if ($bulanSebelumnya <= 0) {
            $bulanSebelumnya = 12;
        }

        $bulanSebelumnya = str_pad($bulanSebelumnya, 2, '0', STR_PAD_LEFT);

        $fieldAK = 'AK' . $bulanSebelumnya;

        $hitung = DB::SELECT("SELECT
                                        a.KDBAR,
                                        a.NMBAR,
                                        a.IDEAL,
                                        a.SUPP,

                                        COALESCE(b.$fieldAK, 0) AS stock_akhir,
                                        a.TOT_JL,
                                        a.HB,
                                        a.JLRATA_RP,a.JLRATA_QTY,


                                        CASE
                                            WHEN COALESCE(sp.selisih, 0) <> 0 THEN sp.selisih
                                            ELSE 0
                                        END AS on_sp,

                                        a.JLRATA_RP *(a.IDEAL - (
                                            COALESCE(b.$fieldAK, 0) +
                                            CASE
                                                WHEN COALESCE(sp.selisih, 0) <> 0 THEN sp.selisih
                                                ELSE 0
                                            END
                                        )) AS budget,
                                         (a.JLRATA_QTY * a.HB) AS nilai_sp,
                                         (
                                             a.JLRATA_RP *(a.IDEAL - (
                                            COALESCE(b.$fieldAK, 0) +
                                            CASE
                                                WHEN COALESCE(sp.selisih, 0) <> 0 THEN sp.selisih
                                                ELSE 0
                                            END
                                        ))
                                            - (a.JLRATA_QTY * a.HB)
                                        ) AS nilai_barang_baru



                                    FROM nwmasbar a
                                    JOIN nwmasbard b
                                        ON a.KDBAR = b.KDBAR

                                    LEFT JOIN (
                                        SELECT
                                            nwbudgetd.KD_BRG,
                                            SUM(nwbudget.Q_SALDO) - COALESCE(SUM(nwagendd.QTY), 0) AS selisih
                                        FROM nwbudget
                                        JOIN nwbudgetd
                                            ON nwbudget.NO_BUKTI = nwbudgetd.NO_BUKTI
                                        JOIN nwagend
                                            ON nwbudget.NO_BUKTI = nwagend.SP
                                        LEFT JOIN nwagendd
                                            ON nwagend.NO_BUKTI = nwagendd.NO_BUKTI
                                        GROUP BY nwbudgetd.KD_BRG
                                    ) sp
                                        ON a.KDBAR = sp.KD_BRG

                                    WHERE a.TD_OD NOT LIKE '%*%'
                                    -- AND a.LAKU='Y'   AND COALESCE(b.$fieldAK,0) = 0
                                    AND b.CBG = 'TGZ' AND b.KDBAR='7425633'");

        if (count($hitung) == 0) {
            return back()->with('error', 'Data kosong');
        }

        // $lastNumber = DB::table('nwbudget')
        //     ->where('NO_BUKTI', 'LIKE', 'PB' . $CBG . $tahun . $bulan . '-%')
        //     ->select(DB::raw('MAX(RIGHT(NO_BUKTI,4)) as last'))
        //     ->value('last');

        // $nextNumber = $lastNumber ? ((int) $lastNumber + 1) : 1;

        // dd($data);

        // Pecah per 85 detail
        // foreach ($data->chunk(85) as $chunk) {

        //     $no_bukti = 'PB' . $CBG . $tahun . $bulan . '-' .
        //     str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        //     $total_budget = $chunk->sum('budget');

        //     $header = Nwbudget::create([
        //         'NO_BUKTI'   => $no_bukti,
        //         'TGL'        => now(),
        //         'PER'        => $periode,
        //         'FLAG'       => 'PO',
        //         'CBG'        => $CBG,
        //         'NOTES'      => 'OTOMATIS',
        //         'USRNM'      => Auth::user()->username,
        //         'TG_SMP'     => Carbon::now(),
        //         'BUDGET'    => $total_budget,
        //         'KODES'    => $chunk[0]->SUPP,
        //         'NAMAS'    => '',

        //     ]);

        //     $REC = 1;

        //     foreach ($chunk as $item) {

        //         if ($item->JLRATA_QTY > 0) {

        //             $barang = DB::table('nwmasbar')
        //                 ->where('KDBAR', $item->KDBAR)
        //                 ->first();

        //             if (! $barang) {
        //                 continue;
        //             }
        //             // dd($barang);

        //             NwbudgetDetail::create([
        //                 'NO_BUKTI' => $no_bukti,
        //                 'ID'       => $header->NO_ID,
        //                 'REC'      => $REC,
        //                 'PER'      => $periode,
        //                 'FLAG'     => 'PP',
        //                 'GOL'      => 'J',
        //                 'KD_BRG'   => $item->KDBAR,
        //                 'NA_BRG'   => $barang->NMBAR,
        //                 'QTY'      => $item->JLRATA_QTY,
        //                 'HARGA'    => $item->HB,
        //                 'TOTAL'    =>  $item->JLRATA_QTY*$item->HB,
        //                 'BUDGET_BRG'   => $item->nilai_barang_baru

        //             ]);

        //             $REC++;
        //         }
        //     }

        //     $nextNumber++;
        // }

        $data = collect($hitung)->groupBy('SUPP');

        foreach ($data as $supp => $items) {

            $lastNumber = DB::table('nwbudget')
                ->where('NO_BUKTI', 'LIKE', 'PB' . $CBG . $tahun . $bulan . '-%')
                ->select(DB::raw('MAX(RIGHT(NO_BUKTI,4)) as last'))
                ->value('last');

            $nextNumber = $lastNumber ? ((int) $lastNumber + 1) : 1;

            foreach ($items->chunk(85) as $chunk) {

                $no_bukti = 'PB' . $CBG . $tahun . $bulan . '-' .
                str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                $total_budget = $chunk->sum('budget');

                $header = Nwbudget::create([
                    'NO_BUKTI' => $no_bukti,
                    'TGL'      => now(),
                    'PER'      => $periode,
                    'FLAG'     => 'PO',
                    'CBG'      => $CBG,
                    'NOTES'    => 'OTOMATIS',
                    'USRNM'    => Auth::user()->username,
                    'TG_SMP'   => Carbon::now(),
                    'BUDGET'   => $total_budget,
                    'KODES'    => $supp,
                    'NAMAS'    => '',
                ]);

                $REC = 1;

                foreach ($chunk as $item) {

                    if ($item->JLRATA_QTY > 0) {

                        $barang = DB::table('nwmasbar')
                            ->where('KDBAR', $item->KDBAR)
                            ->first();

                        if (! $barang) {
                            continue;
                        }

                        NwbudgetDetail::create([
                            'NO_BUKTI'   => $no_bukti,
                            'ID'         => $header->NO_ID,
                            'REC'        => $REC,
                            'PER'        => $periode,
                            'FLAG'       => 'PP',
                            'GOL'        => 'J',
                            'KD_BRG'     => $item->KDBAR,
                            'NA_BRG'     => $barang->NMBAR,
                            'QTY'        => $item->JLRATA_QTY,
                            'HARGA'      => $item->HB,
                            'TOTAL'      => $item->JLRATA_QTY * $item->HB,
                            'BUDGET_BRG' => $item->nilai_barang_baru,
                        ]);

                        $REC++;
                    }
                }

                $nextNumber++;
            }
        }
        return redirect('/budgetpk');
    }

}
