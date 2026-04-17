<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Tretur_ppn;
use App\Models\OTransaksi\Tretur_ppnDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class Tretur_ppnController extends Controller
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
        if ($request->flagz == 'TR') {
            $this->judul = "Tanda Retur Per PPN";
        }

        $this->FLAGZ = $request->flagz;

    }
    public function index(Request $request)
    {
        $this->setFlag($request);

        if (in_array($this->FLAGZ, ['ROP', 'RSP'])) {
            return view('otransaksi_retur.post')->with([
                'judul' => $this->judul,
                'flagz' => $this->FLAGZ,
            ]);
        }

        return view('otransaksi_retur.index')->with([
            'judul' => $this->judul,
            'flagz' => $this->FLAGZ,
        ]);
    }

    // public function browse(Request $request)
    // {
    //     $golz = $request->GOL;

    //     $CBG = Auth::user()->CBG;

    //     $retur = DB::SELECT("SELECT distinct PO.NO_BUKTI , PO.KODES, PO.NAMAS,
    // 	                  PO.ALAMAT, PO.KOTA from nwtandaretur, returd
    //                       WHERE PO.NO_BUKTI = POD.NO_BUKTI AND PO.GOL ='$golz' AND CBG = '$CBG'
    //                       AND POD.SISA > 0	");
    //     return resreturnse()->json($retur);
    // }

    public function browse(Request $request)
    {
        // $golz = $request->GOL;

        $CBG = Auth::user()->CBG;

        $retur = DB::SELECT("SELECT distinct NO_BUKTI , TGL from nwtandaretur, returd
                          WHERE CBG = '$CBG' ");
        return resreturnse()->json($retur);
    }

    public function browseuang(Request $request)
    {
        $CBG = Auth::user()->CBG;

        $retur = DB::SELECT("SELECT NO_BUKTI,TGL,  KODES, NAMAS, TOTAL,  BAYAR,
                        (TOTAL-BAYAR) AS SISA, ALAMAT, KOTA from nwtandaretur
		                WHERE LNS <> 1 AND CBG = '$CBG' ORDER BY NO_BUKTI; ");

        return response()->json($retur);
    }

    public function browse_posting(Request $request)
    {
        $FLAGZ = $request->FLAGZ;

        $CBG = Auth::user()->CBG;

        $cari = $request->CARI;

        if ($cari == '') {

            $posting = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NAMAS, TOTAL_QTY,
                                            NOTES
                                        FROM nwtandaretur
                                        WHERE NO_BUKTI =''AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");

        } else if ($cari != '') {

            $posting = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, NAMAS, TOTAL_QTY,
                                            NOTES
                                        FROM nwtandaretur
                                        WHERE NO_BUKTI = '$cari'AND CBG = '$CBG' AND FLAG = '$FLAGZ' AND POSTED = '0' ");
        }

        return response()->json($posting);
    }
    // public function post(Request $request)
    // {

    //     return view('otransaksi_retur.post');
    // }

    public function browse_detail(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE a.NO_BUKTI='" . $request->NO_PO . "' AND a.KD_BRG = b.KD_BRG ";
        }
        $returd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from nwtandareturd a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");

        return response()->json($returd);
    }

    public function browse_detail2(Request $request)
    {
        $filterbukti = '';
        if ($request->NO_PO) {

            $filterbukti = " WHERE NO_BUKTI='" . $request->NO_PO . "' AND a.KD_BRG = b.KD_BRG ";
        }
        $returd = DB::SELECT("SELECT a.REC, a.KD_BRG, a.NA_BRG, a.SATUAN , a.QTY, a.HARGA, a.KIRIM, a.SISA,
                                b.SATUAN AS SATUAN_PO, a.QTY AS QTY_PO, '1' AS X
                            from nwtandareturd a, brg b
                            $filterbukti ORDER BY NO_BUKTI ");

        return response()->json($returd);
    }

    public function browse_cnt(Request $request)
    {

        $retur = DB::SELECT("SELECT CNT, NA_CNT AS NCNT
                            FROM cntbsn");

        return response()->json($retur);
    }

    public function browse_brg(Request $request)
    {
        // $KD_BRG = $request->KD_BRG;
        $CNT   = $request->CNT;
        $retur = DB::SELECT("SELECT KD_BRG, NA_BRG, BARCODE, DATE_FORMAT(TG_BELI,'%d-%m-%Y') AS TGL_MULAI, BEL AS QTY, HBELI AS HARGA FROM brgbsn WHERE CNT = '$CNT'");
        return response()->json($retur);
    }
    // ganti 4

    public function getTretur_ppn(Request $request)
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

        $tretur_ppn = DB::SELECT("SELECT NO_ID, NO_BUKTI, TGL, CNT, NCNT, total_qty, notes, POSTED, flag FROM nwtandaretur where per='$periode' and flag='$FLAGZ' order by NO_BUKTI");

        // ganti 6

        $tretur_ppn = Datatables::of($tretur_ppn)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("tretur_ppn/delete/" . $row->NO_ID . "/?flagz=" . $row->flag) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="tretur_ppn/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul . '"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')" ';

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
                                <a class="dropdown-item btn btn-danger" href="cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print Ulang
                                </a>
                                <hr></hr>
                                <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>

                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    Hapus
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

        $query = DB::table('nwtandaretur')->select('NO_BUKTI')->where('PER', $periode)->where('flag', $FLAGZ)->where('CBG', $CBG)
            ->orderByDesc('NO_BUKTI')->limit(1)->get();

        if ($query != '[]') {
            $query    = substr($query[0]->NO_BUKTI, -4);
            $query    = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
            $no_bukti = $FLAGZ . $tahun . $bulan . '-' . $query . $CBG_KODE;
        } else {
            $no_bukti = $FLAGZ . $tahun . $bulan . '-0001' . $CBG_KODE;
        }

        $tretur_ppn = Tretur_ppn::create(
            [
                'NO_BUKTI'  => $no_bukti,
                'TGL'       => date('Y-m-d', strtotime($request['TGL'])),
                'PER'       => $periode,
                'flag'      => $FLAGZ,
                'CNT'       => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'      => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'notes'     => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'OUTLET'    => ($request['OUTLET'] == null) ? "" : $request['OUTLET'],
                'total_qty' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'usrnm'     => Auth::user()->username,
                'tg_smp'    => Carbon::now(),
                'CBG'       => $CBG,
            ]
        );

        $REC       = $request->input('REC');
        $KD_BRG    = $request->input('KD_BRG');
        $BARCODE   = $request->input('BARCODE');
        $NA_BRG    = $request->input('NA_BRG');
        $TGL_MULAI = $request->input('TGL_MULAI');
        $QTYK      = $request->input('QTYK');
        $HARGA     = $request->input('HARGA');
        $QTY       = $request->input('QTY');
        $KET       = $request->input('KET');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new Tretur_ppnDetail;

                // Insert ke Database
                $detail->no_bukti  = $no_bukti;
                $detail->rec       = $REC[$key];
                $detail->per       = $periode;
                $detail->flag      = $FLAGZ;
                $detail->KD_BRG    = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->BARCODE   = ($BARCODE[$key] == null) ? "" : $BARCODE[$key];
                $detail->NA_BRG    = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                $detail->tgl_mulai = date('Y-m-d', strtotime($TGL_MULAI[$key]));
                $detail->qtyk      = (float) str_replace(',', '', $QTYK[$key]);
                $detail->harga     = (float) str_replace(',', '', $HARGA[$key]);
                $detail->qty       = (float) str_replace(',', '', $QTY[$key]);
                $detail->ket       = ($KET[$key] == null) ? "" : $KET[$key];
                $detail->save();
            }
        }

        $no_buktix = $no_bukti;

        $tretur_ppn = Tretur_ppn::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE nwtandaretur,  nwtandareturd
                            SET  nwtandareturd.ID =  nwtandaretur.NO_ID  WHERE  nwtandaretur.NO_BUKTI =  nwtandareturd.no_bukti
							AND  nwtandaretur.NO_BUKTI='$no_buktix';");

        // return redirect('/tretur_ppn/edit/?idx=' . $tretur_ppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/tretur_ppn?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ]);

    }

    public function edit(Request $request, Tretur_ppn $tretur_ppn)
    {

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/tretur_ppn')
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
						 and NO_BUKTI = '$buktix' AND CBG = '$CBG'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and NO_BUKTI <
					 '$buktix' ORDER BY NO_BUKTI DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'next') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
                     and NO_BUKTI >
					 '$buktix' ORDER BY NO_BUKTI ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
						where PER ='$per'
						and FLAG ='$this->FLAGZ' AND CBG = '$CBG'
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
            $tretur_ppn = Tretur_ppn::where('NO_ID', $idx)->first();
        } else {
            $tretur_ppn      = new Tretur_ppn;
            $tretur_ppn->TGL = Carbon::now();

        }

        $no_bukti    = $tretur_ppn->NO_BUKTI;
        $returDetail = DB::table('nwtandareturd')->where('no_bukti', $no_bukti)->orderBy('rec')->get();

        $data = [
            'header' => $tretur_ppn,
            'detail' => $returDetail,

        ];

        return view('otransaksi_tretur_ppn.edit', $data)
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

    public function update(Request $request, Tretur_ppn $tretur_ppn)
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

        $tretur_ppn->update(
            [
                'tgl'       => date('Y-m-d', strtotime($request['TGL'])),
                'CNT'       => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NCNT'      => ($request['NCNT'] == null) ? "" : $request['NCNT'],
                'notes'     => ($request['NOTES'] == null) ? "" : $request['NOTES'],
                'OUTLET'    => ($request['OUTLET'] == null) ? "" : $request['OUTLET'],
                'total_qty' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'usrnm'     => Auth::user()->username,
                'tg_smp'    => Carbon::now(),
            ]
        );

        $no_buktix = $tretur_ppn->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC      = $request->input('REC');
        $KD_BRG   = $request->input('KD_BRG');
        $BARCODE  = $request->input('BARCODE');
        $NA_BRG   = $request->input('NA_BRG');
        $TGL_CAIR = $request->input('TGL_CAIR');
        $QTYK     = $request->input('QTYK');
        $HARGA    = $request->input('HARGA');
        $QTY      = $request->input('QTY');
        $KET      = $request->input('KET');

        $query = DB::table('nwtandareturd')->where('no_bukti', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = Tretur_ppnDetail::create(
                    [
                        'no_bukti'  => $request->NO_BUKTI,
                        'rec'       => $REC[$i],
                        'per'       => $periode,
                        'flag'      => $this->FLAGZ,
                        'KD_BRG'    => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'BARCODE'   => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'NA_BRG'    => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'tgl_mulai' => ($TGL_MULAI[$i] != '') ? date("Y-m-d", strtotime($TGL_MULAI[$i])) : "",
                        'qtyk'      => (float) str_replace(',', '', $QTYK[$i]),
                        'harga'     => (float) str_replace(',', '', $HARGA[$i]),
                        'qty'       => (float) str_replace(',', '', $QTY[$i]),
                        'ket'       => ($KET[$i] == null) ? "" : $KET[$i],

                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = Tretur_ppnDetail::updateOrCreate(
                    [
                        'no_bukti' => $request->NO_BUKTI,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'rec'       => $REC[$i],

                        'KD_BRG'    => ($KD_BRG[$i] == null) ? "" : $KD_BRG[$i],
                        'BARCODE'   => ($BARCODE[$i] == null) ? "" : $BARCODE[$i],
                        'NA_BRG'    => ($NA_BRG[$i] == null) ? "" : $NA_BRG[$i],
                        'tgl_mulai' => ($TGL_MULAI[$i] != '') ? date("Y-m-d", strtotime($TGL_MULAI[$i])) : "",
                        'qtyk'      => (float) str_replace(',', '', $QTYK[$i]),
                        'harga'     => (float) str_replace(',', '', $HARGA[$i]),
                        'qty'       => (float) str_replace(',', '', $QTY[$i]),
                        'ket'       => ($KET[$i] == null) ? "" : $KET[$i],
                    ]
                );
            }
        }

        $tretur_ppn = Tretur_ppn::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $tretur_ppn->NO_BUKTI;

        DB::SELECT("UPDATE bertur,  berturd
                    SET  berturd.ID =  bertur.NO_ID  WHERE  bertur.NO_BUKTI =  berturd.no_bukti
                    AND  bertur.NO_BUKTI='$no_bukti';");

        // return redirect('/tretur_ppn/edit/?idx=' . $tretur_ppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        return redirect('/tretur_ppn?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Tretur_ppn $tretur_ppn)
    {

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('retur')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        // }

        $deleteTretur_ppn = Tretur_ppn::find($tretur_ppn->NO_ID);

        $deleteTretur_ppn->delete();

        return redirect('/tretur_ppn?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ])->with('statusHapus', 'Data ' . $tretur_ppn->NO_BUKTI . ' berhasil dihapus');

    }

    public function cetak(Tretur_ppn $tretur_ppn)
    {
        $no_retur = $tretur_ppn->NO_BUKTI;

        $file         = 'returc';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
			SELECT NO_BUKTI,  TGL, KODES, NAMAS, TOTAL_QTY, NOTES, TOTAL, ALAMAT, KOTA
			FROM retur
			WHERE retur.NO_BUKTI='$no_retur'
			ORDER BY NO_BUKTI;
		");

        $xno_retur1 = $query[0]->NO_BUKTI;
        $xtgl1      = $query[0]->TGL;
        $xkodes1    = $query[0]->KODES;
        $xnamas1    = $query[0]->NAMAS;
        $xtotal1    = $query[0]->TOTAL_QTY;
        $xnotes1    = $query[0]->NOTES;
        $xharga1    = $query[0]->TOTAL;
        $xalamat1   = $query[0]->ALAMAT;
        $xkota1     = $query[0]->KOTA;

        $PHPJasperXML->arrayParameter = ["HARGA1" => (float) $xharga1, "TOTAL1" => (float) $xtotal1, "NO_PO1"  => (string) $xno_retur1,
            "TGL1"                                    => (string) $xtgl1, "KODES1"  => (string) $xkodes1, "NAMAS1" => (string) $xnamas1, "NOTES1" => (string) $xnotes1, "ALAMAT1" => (string) $xalamat1, "KOTA1" => (string) $xkota1];
        $PHPJasperXML->arraysqltable = [];

        $query2 = DB::SELECT("
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT, NO_PO,  IF ( FLAG='BL' , 'A','B' ) AS FLAG, AJU, BL, EMKL, KD_BRG, NA_BRG, KG, RPHARGA AS HARGA, RPTOTAL AS TOTAL, 0 AS BAYAR,  NOTES
			FROM beli
			WHERE beli.NO_PO='$no_retur'  UNION ALL
			SELECT NO_BUKTI, TGL, KODES, NAMAS, if(ALAMAT='','NOT-FOUND.png',ALAMAT) as ALAMAT,  NO_PO,  'C' AS FLAG, '' AS AJU, '' AS BL, '' AS EMKL,  '' AS KD_BRG, '' AS NA_BRG, 0 AS KG,
			0 AS HARGA, 0 AS TOTAL, BAYAR, NOTES
			FROM hut
			WHERE hut.NO_PO='$no_retur'
			ORDER BY TGL, FLAG, NO_BUKTI;
		");

        $data = [];

        foreach ($query2 as $key => $value) {
            array_push($data, [
                'NO_BUKTI' => $query2[$key]->NO_BUKTI,
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

    public function posting(Request $request, Tretur_ppn $tretur_ppn)
    {
        $flagz = $request->flagz;
        $flagg = ($flagz == 'ROP') ? 'RO' : $flagz;

        $cbg = Auth::user()->CBG;

        $no_bukti_arr = $request->NO_BUKTIX ?? [];
        $cek_arr      = $request->CEKX ?? [];

        $hasil = [];

        DB::beginTransaction();

        try {

            for ($i = 0; $i < count($no_bukti_arr); $i++) {

                if (! isset($cek_arr[$i]) || $cek_arr[$i] != 1) {
                    continue;
                }

                $no_bukti = trim($no_bukti_arr[$i]);

                $details = DB::table('nwtandareturd')
                    ->where('no_bukti', $no_bukti)
                    ->get();

                foreach ($details as $d) {

                    if ($flagg == 'RO') {

                        DB::table('brgbsnd')
                            ->where('KD_BRG', $d->KD_BRG)
                            ->where('CBG', $cbg)
                            ->update([
                                'ln00' => DB::raw("ln00 - {$d->qty}"),
                                'ak00' => DB::raw("aw00 + ma00 - ke00 + (ln00 - {$d->qty})"),
                            ]);
                    }

                    if ($flagg == 'RM') {

                        DB::table('brgbsnd')
                            ->where('KD_BRG', $d->KD_BRG)
                            ->where('CBG', $cbg)
                            ->update([
                                'ln00' => DB::raw("ln00 + {$d->qty}"),
                                'ak00' => DB::raw("aw00 + ma00 - ke00 + (ln00 + {$d->qty})"),
                            ]);
                    }
                }

                DB::table('nwtandaretur')
                    ->where('no_bukti', $no_bukti)
                    ->update([
                        'POSTED'     => 1,
                        'tgl_posted' => now(),
                    ]);

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
    public function getDetailTretur_ppn()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('returd')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);
    }

}
