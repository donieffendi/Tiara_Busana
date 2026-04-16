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
    protected $view;
    protected $judul;
    protected $FLAGZ;
    protected $GOLZ;

    public function setFlag(Request $request)
    {
        $this->FLAGZ = $request->flagz;
        $this->GOLZ  = $request->golz;

        // Use switch-case to determine the appropriate view and title
        switch (true) {
            case ($request->flagz == 'PN' && $request->golz == '1'):
                $this->judul = "Usulan Rubah Tanda PPN";
                $this->view  = 'otransaksi_uppn.index';
                break;
            case ($request->flagz == 'HS' && $request->golz == '0'):
                $this->judul = "Usulan Hapus Suplier";
                $this->view  = 'otransaksi_uppn.index';
                break;
            case ($request->flagz == 'PU' && $request->golz == 'PU'):
                $this->judul = "Posting Usulan Hapus Suplier";
                $this->view  = 'otransaksi_uppn.index_posting';
                break;
            case ($request->flagz == 'PE' && $request->golz == 'PE'):
                $this->judul = "Posting Usulan Ubah Email Suplier";
                $this->view  = 'otransaksi_uppn.index_posting_email';
                break;
            default:
                $this->judul = "Usulan Rubah Email Suplier";
                $this->view  = 'otransaksi_uppn.index';
        break;
                }
    }


    public function index(Request $request)
    {
        // Call setFlag to set the view, judul, FLAGZ, and GOLZ
        $this->setFlag($request);

        // Return the view with the necessary data
        return view($this->view)->with([
            'judul' => $this->judul,
            'flagz' => $this->FLAGZ,
            'golz'  => $this->GOLZ
        ]);
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

        $uppn = DB::select("
						SELECT *
						FROM ubhppnj
                        WHERE PER = '$periode'
						ORDER BY NO_BUKTI
					");
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
        return redirect('/uppn-new/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul . '');

    }

    public function edit(Request $request, Ubhppnj $uppn)
    {

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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from ubhppnj
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from ubhppnj
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from ubhppnj
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from ubhppnj
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from ubhppnj
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
        $uppnDetail = DB::table('ubhppnjd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

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
        $this->validate($request, [
            'TGL' => 'required',
        ]);

        $this->setFlag($request);

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        // ✅ UPDATE HEADER
        $uppn->update([
            'TGL' => $request->TGL,
        ]);

        // =========================
        // UPDATE DETAIL
        // =========================
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');
        $REC    = $request->input('REC');
        $PPN    = $request->input('PPN');
        $KD_BRG = $request->input('KD_BRG'); // ⬅️ WAJIB ADA DI FORM

        DB::table('ubhppnjd')
            ->where('NO_BUKTI', $request->NO_BUKTI)
            ->whereNotIn('NO_ID', $NO_ID)
            ->delete();

        // =========================
        // INSERT / UPDATE + UPDATE NWMASBAR
        // =========================
        for ($i = 0; $i < $length; $i++) {

            $nilaiPPN = $PPN[$i] ?? 0;
            $kd_brg   = $KD_BRG[$i] ?? null;

            // 🔹 SIMPAN DETAIL
            if ($NO_ID[$i] == 'new') {
                UbhppnjDetail::create([
                    'NO_BUKTI' => $request->NO_BUKTI,
                    'REC'      => $REC[$i],
                    'PPN'      => $nilaiPPN,
                    'KD_BRG'   => $kd_brg,
                ]);
            } else {
                UbhppnjDetail::updateOrCreate(
                    [
                        'NO_BUKTI' => $request->NO_BUKTI,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],
                    [
                        'REC'    => $REC[$i],
                        'PPN'    => $nilaiPPN,
                        'KD_BRG' => $kd_brg,
                    ]
                );
            }

            // 🔥 UPDATE NWMASBAR BERDASARKAN KD_BRG
            if ($kd_brg) {
                DB::table('nwmasbar')
                    ->where('KDBAR', $kd_brg)
                    ->update([
                        'PPN' => $nilaiPPN
                    ]);
            }
        }

        return redirect('/uppn-new/edit/?idx=' . $uppn->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&judul=' . $this->judul);
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

        $deleteUppnDetail = UbhppnjDetail::where('NO_BUKTI', $uppn->NO_BUKTI)->delete();

        $deleteUppn = Ubhppnj::find($uppn->NO_ID);
        $deleteUppn->delete();

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

        $POSTED = DB::table("ubhppnj")->where('NO_BUKTI', $no_uppn)->value('POSTED');
        if ($POSTED == 0) {
            DB::select('call ubhppnjins(?)', [$no_uppn]);
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
        $result   = DB::table('ubhppnjd')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);
    }

    public function posting_stock_ubhppnj(Request $request)
    {
        if (! $request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        $data = $request->input('posted');

        if (! $data) {
            return response()->json(['error' => 'Tidak ada data yang dikirim'], 400);
        }

        foreach ($data as $id => $posted) {
            DB::table('ubhppnj')->where('NO_ID', $id)->update(['POSTED' => $posted]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

}
