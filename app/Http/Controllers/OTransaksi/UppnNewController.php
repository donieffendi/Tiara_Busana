<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Ubhppnj;
use App\Models\OTransaksi\UbhppnjDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class UppnNewController extends Controller
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
        if ($request->flagz == 'U') {
            $this->judul = "Usulan Tanda PPN";
        } else if ($request->flagz == 'P') {
            $this->judul = "Pengesahan Tanda PPN";
        }

        $this->FLAGZ = $request->flagz;

    }

    public function index(Request $request)
    {

        $this->setFlag($request);
        if ($this->FLAGZ == 'TE') {
            $view = "otransaksi_uppn.index";
        } elseif ($this->FLAGZ == 'MT') {
            $view = "otransaksi_uppn.index";
        } elseif ($this->FLAGZ == 'PT') {
            $view = "otransaksi_uppn.index_posting";
        }
        return view($view)->with([
            'judul' => $this->judul,
            'flagz' => $this->FLAGZ,
        ]);

    }
    public function browse(Request $request)
    {
        if(!empty($request->po)) {
            $po = DB::select("SELECT *
                        FROM KIRIM t
                        WHERE t.NO_BUKTI = ? AND t.POSTED=1
						AND NOT EXISTS (
							  SELECT 1
							  FROM terima tr
							  WHERE tr.NO_SCAN = t.NO_BUKTI
						  )

                    ", [$request->no_po]);
        } else {
            $po = DB::select("SELECT *
                        FROM KIRIM t
                        WHERE t.POSTED=1
						AND NOT EXISTS (
							  SELECT 1
							  FROM terima tr
							  WHERE tr.NO_SCAN = t.NO_BUKTI
						  )");
        }

        return response()->json($po);
    }

    public function browse_spd(Request $request)
    {
        $kodes    = $request->kodes;
        $no_bukti = $request->nobukti;
        // $xppn = DB::select("CALL xppn()");
        // $PN   = $xppn[0]->PN ?? 0;

        //   $pod = DB::select("SELECT td.*
        //                 FROM KMKIRIM td
        //                 WHERE td.NO_BUKTI = ?
        //                 -- AND NOT EXISTS (
        //                 --     SELECT 1
        //                 --     FROM KIRIM t
        //                 --     WHERE t.NO_SCAN = td.NO_BUKTI
        //                 -- )
        //             ", [$no_bukti]);

        $pod = DB::select("
            SELECT
                k.KD_BRG,
                k.NA_BRG,
                k.QTY,
                k.HARGA,
                k.TOTAL,
                k.CBG,
                c.NAMAS AS NAMA,
                c.ALMT_K AS ALAMAT,
                c.KOTA AS KOTA
            FROM KIRIMD k
            JOIN KIRIM t
                ON t.NO_BUKTI = k.NO_BUKTI
            LEFT JOIN zsup c
                ON c.KODES = k.CBG
            WHERE k.NO_BUKTI = ?
            AND NOT EXISTS (
                SELECT 1
                FROM terima tr
                WHERE tr.NO_SCAN = t.NO_BUKTI
            )
        ", [$no_bukti]);

        return response()->json($pod);
    }
    public function getUppnNew(Request $request)
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

        $uppn = DB::SELECT("SELECT * from terima
                            WHERE PER='$periode' and FLAG ='TE' AND NO_BUKTI LIKE 'BL%' AND CBG = '$CBG'
                            ORDER BY TGL DESC, NO_BUKTI DESC");
        // ganti 6

        return Datatables::of($uppn)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "outlet") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("uppn-new/delete/" . $row->NO_ID . "/?flagz=" . $row->FLAG) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="uppn-new/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')" ';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="uppn-new/print/' . $row->NO_BUKTI . '">
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

        // Ambil BL_TYPE dari request
        $BL_TYPE = $request->bl_type;

        //////     nomer otomatis
        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $last = DB::table('terima')
            ->select('NO_BUKTI')
            ->where('PER', $periode)
            ->where('FLAG', 'TE')
            ->where('CBG', $CBG)
            ->where('NO_BUKTI', 'like', 'BL%')
            ->lockForUpdate()
            ->orderByDesc('NO_BUKTI')
            ->first();

        if ($last) {
            $pecah = explode('-', $last->NO_BUKTI);
            $angka = substr($pecah[1], 0, 4);
            $next  = str_pad(((int) $angka) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $next = '0001';
        }

        $no_bukti = 'BL' . $tahun . $bulan . '-' . $next . substr($CBG, -1);

        $beli = Ubhppnj::create(
            [
                'NO_BUKTI'  => $no_bukti,
                'TGL'       => date('Y-m-d', strtotime($request['TGL'])),
                // 'JTEMPO'    => date('Y-m-d', strtotime($request['JTEMPO'])),
                'PER'       => $periode,
                'FLAG'      => 'TE',
                'notes'     => ($request['notes'] == null) ? "" : $request['notes'],
                'NO_SCAN'   => ($request['NO_PO'] == null) ? "" : $request['NO_PO'],
                'TYPE'      => ($request['TYPE'] == null) ? "" : $request['TYPE'],
                'NO_PJK'    => ($request['NO_PJK'] == null) ? "" : $request['NO_PJK'],
                'TOTAL_QTY' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'usrnm'     => Auth::user()->username,
                'tg_smp'    => Carbon::now(),
                'CBG'       => $CBG,
				'CBG_DARI'    => ($request['CBG_DARI'] == null) ? "" : $request['CBG_DARI'],
                'KODES'     => '510D',
                'NAMAS'     => 'ADIKARYA PANGAN FRESHINDO',
                'alamat'    => ($request['alamat'] == null) ? "" : $request['alamat'],
                'kota'      => ($request['kota'] == null) ? "" : $request['kota'],
                'GOLONGAN'  => ($request['GOLONGAN'] == null) ? "" : $request['GOLONGAN'],
                'HARI'      => (float) str_replace(',', '', $request['HARI']),
                'total_qty' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'DPP'       => (float) str_replace(',', '', $request['DPP']),
                'ppn'       => (float) str_replace(',', '', $request['TPPN']),
                'nett'      => (float) str_replace(',', '', $request['NETT']),
                'PROM'      => (float) str_replace(',', '', $request['PROM']),
                'BPROM'     => (float) str_replace(',', '', $request['disc_ps']),
                'total'     => (float) str_replace(',', '', $request['BRUTO']),
            ]
        );

        $REC     = $request->input('REC');
        $KD_BRG  = $request->input('KD_BRG');
        $NA_BRG  = $request->input('NA_BRG');
        $SISA    = $request->input('SISA');
        $qtyk    = $request->input('qtyk');
        $kemasan = $request->input('kemasan');
        $hargak  = $request->input('hargak');
        $DISKON1 = $request->input('DISKON1');
        $DISKON2 = $request->input('DISKON2');
        $DISKON3 = $request->input('DISKON3');
        $DISKON4 = $request->input('DISKON4');
        $total   = $request->input('total');
        $ket     = $request->input('ket');
        $harga   = $request->input('harga');
        $qty     = $request->input('qty');
        $kdlaku  = $request->input('kdlaku');
        $TGL_EXP = $request->input('TGL_EXP');

        // Check jika value detail ada/tidak
        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new UbhppnjDetail;

                // Insert ke Database
                $detail->NO_BUKTI = $no_bukti;
                $detail->REC      = $REC[$key];
                $detail->PER      = $periode;
                $detail->CBG      = $CBG;
                $detail->FLAG     = $this->FLAGZ;
                $detail->KD_BRG   = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->NA_BRG   = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                // $detail->QTY_PO   = ($SISA[$key] == null) ? "" : $SISA[$key];
                $detail->QTYC = (float) str_replace(',', '', $qtyk[$key] ?? 0);
                //$detail->kemasan  = (float) str_replace(',', '', $kemasan[$key]);
                $detail->HARGA = (float) str_replace(',', '', $hargak[$key]);
                // $detail->DISK  = (float) str_replace(',', '', $DISKON1[$key]);
                // $detail->DISK2 = (float) str_replace(',', '', $DISKON2[$key]);
                // $detail->DISK3 = (float) str_replace(',', '', $DISKON3[$key]);
                // $detail->DISK4 = (float) str_replace(',', '', $DISKON4[$key]);
                $detail->TOTAL = (float) str_replace(',', '', $total[$key]);
                $detail->KET   = ($ket[$key] == null) ? "" : $ket[$key];
                $detail->HARGA = (float) str_replace(',', '', $harga[$key]);
                $detail->QTY   = (float) str_replace(',', '', $qty[$key]);
                //$detail->kdlaku   = ($kdlaku[$key] == null) ? "" : $kdlaku[$key];
                // $detail->TGL_EXP     = date('Y-m-d', strtotime($TGL_EXP[$key]));
                //$detail->TGL_EXP = (! empty($TGL_EXP[$key]) && ! is_array($TGL_EXP[$key]))
                //? date('Y-m-d', strtotime($TGL_EXP[$key]))
                //: null;
                $detail->save();
            }
        }

        // $variablell = DB::select('call beliins(?)', array($no_bukti));
        // $variablell1 = DB::select('call beli_brgins(?)', array($no_bukti));

        $no_buktix = $no_bukti;

        $uppn = Ubhppnj::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE terima,  terimad
                            SET  terimad.ID =  terima.NO_ID  WHERE  terima.NO_BUKTI =  terimad.NO_BUKTI
							AND  terima.NO_BUKTI='$no_buktix';");

        // return redirect('/uppn/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        // return redirect('/terima?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
        return redirect('/uppn-new/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');

    }

    public function edit(Request $request, Ubhppnj $uppn)
    {

        $pilihcbg = DB::table('compan')->select('EXT')->orderBy('EXT', 'ASC')->get();

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/uppn')
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from terima
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from terima
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from terima
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from terima
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from terima
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
            $uppn = Ubhppnj::where('NO_ID', $idx)->first();
        } else {
            $uppn      = new Ubhppnj;
            $uppn->TGL = Carbon::now();

        }

        $no_bukti     = $uppn->NO_BUKTI;
        $uppnDetail = DB::table('terimad')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

        $data = [
            'header' => $uppn,
            'detail' => $uppnDetail,

        ];

        $sup = DB::SELECT("SELECT KODES, CONCAT(NAMAS,'-',KOTA) AS NAMAS FROM SUP
		                 ORDER BY NAMAS ASC");

        return view('otransaksi_uppn_new.edit', $data)->with(['sup' => $sup])
            ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'judul' => $this->judul])->with(['pilihcbg' => $pilihcbg]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 18

    public function update(Request $request, Ubhppnj $uppn)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );

        $BL_TYPE = $request->bl_type;

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        // $variablell = DB::select('call belidel(?)', array($beli['NO_BUKTI']));
        // $variablell1 = DB::select('call beli_brgdel(?)', array($beli['NO_BUKTI']));

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $uppn->update(
            [
                'notes'     => ($request['notes'] == null) ? "" : $request['notes'],
                'NO_PJK'    => ($request['NO_PJK'] == null) ? "" : $request['NO_PJK'],
                'total_qty' => (float) str_replace(',', '', $request['TTOTAL_QTY']),
                'usrnm'     => Auth::user()->username,
                'tg_smp'    => Carbon::now(),
                'total'     => (float) str_replace(',', '', $request['BRUTO']),
                'DPP'       => (float) str_replace(',', '', $request['DPP']),
                'ppn'       => (float) str_replace(',', '', $request['TPPN']),
                'nett'      => (float) str_replace(',', '', $request['NETT']),
                'PROM'      => (float) str_replace(',', '', $request['PROM']),
                'BPROM'     => (float) str_replace(',', '', $request['disc_ps']),
            ]
        );

        $no_buktix = $request->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC = $request->input('REC');

        $qty     = $request->input('qty');
        $qtyk    = $request->input('qtyk');
        $harga   = $request->input('harga');
        $hargak  = $request->input('hargak');
        $kemasan = $request->input('kemasan');
        $DISKON1 = $request->input('DISKON1');
        $DISKON2 = $request->input('DISKON2');
        $DISKON3 = $request->input('DISKON3');
        $DISKON4 = $request->input('DISKON4');
        $total   = $request->input('total');
        $ket     = $request->input('ket');
        $TGL_EXP = $request->input('TGL_EXP');

        $query = DB::table('terimad')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = UbhppnjDetail::create(
                    [
                        'NO_BUKTI' => $request->NO_BUKTI,
                        'REC'      => $REC[$i],
                        // 'QTY_PO'   => $qty[$i],
                        'QTY'      => $qtyk[$i],
                        'HARGA'    => $harga[$i],
                        'HARGAX'   => $hargak[$i],
                        // 'kemasan'  => $kemasan[$i],
                        // 'DISK'     => $DISKON1[$i],
                        // 'DISK2'    => $DISKON2[$i],
                        // 'DISK3'    => $DISKON3[$i],
                        // 'DISK4'    => $DISKON4[$i],
                        'TOTAL'    => $total[$i],
                        'KET'      => $ket[$i] ?? '',
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = UbhppnjDetail::updateOrCreate(
                    [
                        'NO_BUKTI' => $request->NO_BUKTI,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'REC'    => $REC[$i],

                        // 'QTY_PO' => (float) str_replace(',', '', $qty[$i]),
                        'QTY'    => (float) str_replace(',', '', $qtyk[$i]),
                        'HARGA'  => (float) str_replace(',', '', $harga[$i]),
                        'HARGAX' => (float) str_replace(',', '', $hargak[$i]),
                        // 'DISK'   => (float) str_replace(',', '', $DISKON1[$i]),
                        // 'DISK2'  => (float) str_replace(',', '', $DISKON2[$i]),
                        // 'DISK3'  => (float) str_replace(',', '', $DISKON3[$i]),
                        // 'DISK4'  => (float) str_replace(',', '', $DISKON4[$i]),
                        'TOTAL'  => (float) str_replace(',', '', $total[$i]),
                        'KET'    => $ket[$i] ?? '',
                    ]
                );
            }
        }

        $uppn = Ubhppnj::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $uppn->NO_BUKTI;

        // $variablell = DB::select('call terimains(?)', array($uppn['NO_BUKTI']));
        // $variablell1 = DB::select('call terima_brgins(?)', array($hdh['NO_BUKTI']));
        // $variablell1 = DB::select('call uppn_brgins(?)', array($uppn['NO_BUKTI']));

        DB::SELECT("UPDATE terima,  terimad
                    SET  terimad.ID =  terima.NO_ID  WHERE  terima.NO_BUKTI =  terimad.NO_BUKTI
                    AND  terima.NO_BUKTI='$no_bukti';");

        // return redirect('/uppn/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');
        // return redirect('/uppn?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ]);
        return redirect('/uppn-new/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Resbelinse
     */

    // ganti 22

    public function destroy(Request $request, Ubhppnj $uppn)
    {

        // Ambil BL_TYPE dari request
        $BL_TYPE = $request->bl_type;

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect()->route('beli')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ]);
        // }

        // $variablell = DB::select('call belidel(?)', array($beli['NO_BUKTI']));
        // $variablell = DB::select('call beli_brgdel(?)', array($beli['NO_BUKTI']));

        $deleteTerimaDetail = UbhppnjDetail::where('NO_BUKTI', $uppn->NO_BUKTI)->delete();

        $deleteTerima = Ubhppnj::find($uppn->NO_ID);
        $deleteTerima->delete();

        //    return redirect('/beli?flagz='.$FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ ])->with('statusHapus', 'Data '.$beli->NO_BUKTI.' berhasil dihapus');
        return redirect('/uppn?flagz=' . $FLAGZ)->with(['judul' => $judul, 'flagz' => $FLAGZ])->with('statusHapus', 'Data ' . $uppn->NO_BUKTI . ' berhasil dihapus');
    }
    public function print($uppn)
    {
        $no_uppn = $uppn;
        $JAM       = Carbon::now('Asia/Jakarta')
            ->addHour()
            ->format('H:i:s');
        $TGL = Carbon::now('Asia/Jakarta')
            ->addHour()
            ->format('d-m-Y');
        $file         = 'uppn-new';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::select("SELECT
                                b.NO_BUKTI,
                                b.NO_SCAN,
								b.CBG,
								comp.NAMA AS COMPAN,
                                bd.KD_BRG,
                                bd.NA_BRG,
                                bd.QTY,
                                bd.HARGA AS HARGA,
                                bd.TOTAL,
								v.HJUAL AS HARGA_VBRG,
								u.N_POINT,
                                c.KODES,
                                c.NAMAS,
                                c.ALMT_K as ALAMAT,
                                c.KOTA
                            FROM TERIMA b
                            JOIN TERIMAD bd
                                ON b.NO_BUKTI = bd.NO_BUKTI

							LEFT JOIN compan comp
								ON comp.KODE = b.CBG
							LEFT JOIN vbrg v
								ON v.KD_BRG = bd.KD_BRG
							LEFT JOIN ubhnd u
								ON u.KD_BRG = bd.KD_BRG
                            LEFT JOIN zsup c
                                ON LOWER(c.CBG) = LOWER(
                                    SUBSTRING(
                                        SUBSTRING_INDEX(b.NO_SCAN, '-', 1),
                                        3,
                                        3
                                    )
                                )
                            WHERE b.NO_BUKTI = ?;
                        ", [$no_uppn]);
                        // dd($query);

        $POSTED = DB::table("terima")->where('NO_BUKTI', $no_uppn)->value('POSTED');
        if ($POSTED == 0) {
            DB::select('call terimains(?)', [$no_uppn]);
        }
        DB::update(
            "UPDATE TERIMA SET POSTED = 1 WHERE NO_BUKTI = ?",
            [$no_uppn]
        );

        $cleanData                    = json_decode(json_encode($query), true);
        $PHPJasperXML->arrayParameter = [
            "JAM" => $JAM,
            "TGL" => $TGL,
        ];

        $PHPJasperXML->setData($cleanData);
        // dd($cleanData);

        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }
    private function updateQTY($kd_brg, $cbg, $qty)
    {
        try {

            $response = Http::asForm()->post('https://modisyst.com/tiaraapkpoin/public/api/poin/update-produk', [
                'kode'        => $kd_brg,
                'compan_code' => $cbg,
                'quantity'    => $qty,
            ]);
            $result = $response->json();
            return [
                'error'    => $response->failed(),
                'message'  => $result['message'] ?? 'Tidak ada pesan',
                'response' => $result,
                'status'   => $response->status(),
            ];
        } catch (\Illuminate\Validation\ValidationException $e) {
            return [
                'error'   => true,
                'message' => $e->errors(),
                'status'  => 422,
            ];
        } catch (\Exception $e) {
            return [
                'error'   => true,
                'message' => 'Gagal mengirim ke server tujuan: ' . $e->getMessage(),
                'status'  => 500,
            ];
        }
    }

    public function posting(Request $request)
    {

    }

    public function getDetailUppn()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('terimad')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);
    }

    public function posting_stock_terima(Request $request)
    {
        if (! $request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        $data = $request->input('posted');

        if (! $data) {
            return response()->json(['error' => 'Tidak ada data yang dikirim'], 400);
        }

        foreach ($data as $id => $posted) {
            DB::table('terima')->where('NO_ID', $id)->update(['POSTED' => $posted]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

}
