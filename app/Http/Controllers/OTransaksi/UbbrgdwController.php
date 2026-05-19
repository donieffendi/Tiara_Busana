<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use App\Models\Master\Perid;
use App\Models\OTransaksi\Ubbrgdw;
use App\Models\OTransaksi\UbbrgdwDetail;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPJasperXML;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use Yajra\DataTables\Facades\DataTables;

class UbbrgdwController extends Controller
{

    public function index()
    {

        return view('otransaksi_ubbrgdw.index');
    }

    public function jasperVbrgReport(Request $request)
    {
        $file         = 'vbrgpr';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        if ($request['perio']) {
            $periode = $request['perio'];
        }

        if ($request['cbg']) {
            $cbg = $request['cbg'];
        }

        if (! empty($request->cbg)) {
            $filtercbg = " and vbrgd.CBG='" . $request->cbg . "' ";
        }

        if (! empty($request->KD_BRG)) {
            $filterkode = " and vbrg.KD_BRG='" . $request->KD_BRG . "' ";
        }

        session()->put('filter_cbg', $request->cbg);
        session()->put('filter_per', $periode);
        session()->put('filter_kode1', $request->KD_BRG);
        session()->put('filter_nama1', $request->NA_BRG);

        $bulan = substr($periode, 0, 2);
        $tahun = substr($periode, 3, 4);

        $queryakum = DB::SELECT("SET @akum:=0;");
        $query     = DB::SELECT("SELECT vbrg.KD_BRG,vbrg.NA_BRG,vbrgd.AW$bulan as AW, vbrgd.MA$bulan as MA,
		    vbrgd.KE$bulan as KE,vbrgd.LN$bulan as LN,vbrgd.AK$bulan as AK,
			vbrgd.HRT$bulan as HRT,vbrgd.NIW$bulan as NIW,vbrgd.NIM$bulan as NIM,vbrgd.NIK$bulan as NIK,
		vbrgd.NIL$bulan as NIL,vbrgd.NIR$bulan as NIR
		FROM vbrg,vbrgd
		WHERE vbrg.KD_BRG=vbrgd.KD_BRG and vbrgd.YER='$tahun'
		$filtercbg $filterkode
		group by KD_BRG
		order by KD_BRG;
		");

        if ($request->has('filter')) {
            $per = Perid::query()->get();
            $cbg = Cbg::groupBy('CBG')->get();

            return view('otransaksi_ubbrgdw.report')->with(['per' => $per])->with(['cbg' => $cbg])->with(['hasil' => $query]);
        }

        $data = [];
        foreach ($query as $key => $value) {
            array_push($data, [
                'KD_BRG' => $query[$key]->KD_BRG,
                // 'KD_BRG'    => "`".strval($query[$key]->KD_BRG),
                'NA_BRG' => $query[$key]->NA_BRG,
                'AW'     => $query[$key]->AW,
                'MA'     => $query[$key]->MA,
                'KE'     => $query[$key]->KE,
                'LN'     => $query[$key]->LN,
                'AK'     => $query[$key]->AK,
                'HRT'    => $query[$key]->HRT,
                'HRT_2'  => $query[$key]->HRT_2,
                'NIW'    => $query[$key]->NIW,
                'NIM'    => $query[$key]->NIM,
                'NIK'    => $query[$key]->NIK,
                'NIL'    => $query[$key]->NIL,
                'NIR'    => $query[$key]->NIR,
            ]);
        }
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
    public function store(Request $request)
    {
        // dd("hai");
		$this->validate(
            $request,

            [
                'TGL' => 'required',

            ]
        );

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        $CBG = Auth::user()->CBG;

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);
        if ($request->NO_BUKTI == null) {
            $query = DB::table('ubbrgdw')->select('NO_BUKTI')->where('PER', $periode)->where('FLAG', 'BL')->where('NO_BUKTI', 'like', 'DB' . $tahun . $bulan . '%')
                ->orderByDesc('NO_BUKTI')->limit(1)->get();

            if ($query != '[]') {
                $query    = substr($query[0]->NO_BUKTI, -4);
                $query    = str_pad($query + 1, 4, 0, STR_PAD_LEFT);
                $no_bukti = 'DB' . $tahun . $bulan . '-' . $query;
            } else {
                $no_bukti = 'DB' . $tahun . $bulan . '-0001';
            }
            DB::table('ubbrgdw')->where('NO_ID', $request->NO_ID)->update(['NO_BUKTI' => $no_bukti]);
        } else {
            $no_bukti = $request->input('NO_BUKTI');
            // DB::table('ubbrgdwd')->where('ID', $request->NO_ID)->where('NO_BUKTI', $no_bukti)->update(['POSTED' => 0]);
        }

        $ubbrgdw = Ubbrgdw::create(
            [
                'NO_BUKTI'   => $no_bukti,
                'TGL'        => date('Y-m-d', strtotime($request['TGL'])),
                'PER'        => $periode,
                'FLAG'       => 'BL',
                'KODES'        => $request['KODES_HEADER'],
                'NAMAS'        => $request['NAMAS_HEADER'],
                'KET'        => $request['NOTES'] ?? '',
                'NO_BELI'        => $request['NO_BELI'],
                'CBG'        => $CBG,
                'USRNM'      => Auth::user()->username,
                'TG_SMP'     => Carbon::now(),
            ]
        );

        $REC    = $request->input('REC');
        $KD_BRG = $request->input('KD_BRG');
        $NA_BRG = $request->input('NA_BRG');
        $KODES = $request->input('KODES');
        $NAMAS = $request->input('NAMAS');
        $QTY = $request->input('QTY');
        $KET    = $request->input('KET');
        $HARGA    = $request->input('HARGA');
        $HARGALAMA    = $request->input('HARGALAMA');
        $DISK    = $request->input('DISK');
        $DISKLAMA    = $request->input('DISKLAMA');
        $DISK2    = $request->input('DISK2');
        $DISKLAMA2    = $request->input('DISKLAMA2');
        $DISK3    = $request->input('DISK3');
        $DISKLAMA3    = $request->input('DISKLAMA3');
        $DISK4    = $request->input('DISK4');
        $DISKLAMA4    = $request->input('DISKLAMA4');

        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail = new UbbrgdwDetail;

                // Insert ke Database
                $detail->NO_BUKTI = $no_bukti;
                $detail->REC      = $REC[$key];
                $detail->PER      = $periode;
                $detail->FLAG     = 'BL';
                $detail->KODES   = ($KODES[$key] == null) ? "" : $KODES[$key];
                $detail->NAMAS   = ($NAMAS[$key] == null) ? "" : $NAMAS[$key];
                $detail->KD_BRG   = ($KD_BRG[$key] == null) ? "" : $KD_BRG[$key];
                $detail->NA_BRG   = ($NA_BRG[$key] == null) ? "" : $NA_BRG[$key];
                $detail->KET   = ($KET[$key] == null) ? "" : $KET[$key];
                $detail->QTY      = (float) str_replace(',', '', $QTY[$key]);
                $detail->HARGA     = (float) str_replace(',', '', $HARGA[$key]);
                $detail->HARGALAMA   = (float) str_replace(',', '', $HARGALAMA[$key]);
                $detail->DISK   = (float) str_replace(',', '', $DISK[$key]);
                $detail->DISKLAMA   = (float) str_replace(',', '', $DISKLAMA[$key]);
                $detail->DISK2   = (float) str_replace(',', '', $DISK2[$key]);
                $detail->DISKLAMA2   = (float) str_replace(',', '', $DISKLAMA2[$key]);
                $detail->DISK3   = (float) str_replace(',', '', $DISK3[$key]);
                $detail->DISKLAMA3   = (float) str_replace(',', '', $DISKLAMA3[$key]);
                $detail->DISK4   = (float) str_replace(',', '', $DISK4[$key]);
                $detail->DISKLAMA4   = (float) str_replace(',', '', $DISKLAMA4[$key]);
                $detail->save();
            }
        }

        $no_buktix = $no_bukti;

        DB::SELECT("UPDATE ubbrgdw,  ubbrgdwd
                            SET  ubbrgdwd.ID =  ubbrgdw.NO_ID  WHERE  ubbrgdw.NO_BUKTI =  ubbrgdwd.NO_BUKTI
							AND  ubbrgdw.NO_BUKTI='$no_buktix';");

        return redirect('ubbrgdw?flagz=KZ');

    }

    public function browse(Request $request)
    {
        $periode = $request->get('periode');

        $query = DB::table('ubbrgdw')
            ->select([
                'ubbrgdw.NO_ID',
                'ubbrgdw.NO_BELI',
                'ubbrgdw.TGL',
                'ubbrgdw.FLAG',
                'ubbrgdw.KODES',
                'ubbrgdw.NAMAS',
                'ubbrgdw.ALAMAT',
                'ubbrgdw.KOTA',
                'ubbrgdw.KET',
                'ubbrgdw.USRNM',
                'ubbrgdw.NO_BUKTI',
                'ubbrgdw.SELESAI',
            ])
            ->where('ubbrgdw.NO_BUKTI', '<>', '')
            ->where('ubbrgdw.SELESAI', '=', '0')
            ->get();

        /** @var \stdClass $row */
        foreach ($query as $row) {
            $row->POSTED = DB::table('ubbrgdwd')
                ->where('NO_BUKTI', $row->NO_BUKTI)->limit(1)
                ->value('POSTED');
        }

        // Filter by period if provided
        if ($periode) {
            $period = explode('/', $periode);
            if (count($period) == 2) {
                $month = str_pad($period[0], 2, '0', STR_PAD_LEFT);
                $year  = $period[1];
                $query->whereRaw("DATE_FORMAT(TGL, '%m/%Y') = ?", [$month . '/' . $year]);
            }
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                //CEK POSTED di index dan edit

                // url untuk delete di index
                // batas
               $url = "'" . url("ubbrgdw/delete/" . $row->NO_BUKTI . "/?flagz=" . $row->FLAG) . "'";

                // $btnEdit =   ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BELI . ' sudah diposting!\')" href="#" ' : ' href="po/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->FLAG . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
// <a class="dropdown-item" ' . $btnEdit . '>
//                                 <i class="fas fa-edit"></i>
//                                     Edit
//                                 </a>

   $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                $btnPrivilege =
                '
                                <a class="dropdown-item"' . ($row->SELESAI == true ? '  onclick= "alert(\'Usulan ' . $row->NO_BUKTI . ' Sudah diselesaikan!\')" href="#" ' : ' href="ubbrgdw/edit/?idx=' . $row->NO_ID . '&tipx=edit"') . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="' . route('ubbrgdw.cetak', ['NO_ID' => $row->NO_ID]) . '">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print Usulan
                                </a>

                                <a class="dropdown-item btn btn-danger" ' .
                    (($row->POSTED == false) ?
                    ' onclick="alert(\'Perubahan harga ' . $row->NO_BUKTI . ' Belum Diusulkan!\')" href="#" ' :
                    ' target="_blank" href="' . route('ubbrgdw.cetak', ['NO_ID' => $row->NO_ID, 'selesai' => 'true']) . '"'
                ) . '>
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print Laporan
                                </a>
                                <hr></hr>

                                <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>

                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                    Delete
                                </a>

                        ';

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

            ->editColumn('TGL', function ($row) {
                return date('d-m-Y', strtotime($row->TGL));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function browseNew(Request $request)
    {
        $periode = $request->get('periode');

        $query = DB::table('ubbrgdw')
            ->select([
                'ubbrgdw.NO_ID',
                'ubbrgdw.NO_BELI',
                'ubbrgdw.TGL',
                'ubbrgdw.FLAG',
                'ubbrgdw.KODES',
                'ubbrgdw.NAMAS',
                'ubbrgdw.ALAMAT',
                'ubbrgdw.KOTA',
                'ubbrgdw.KET',
                'ubbrgdw.USRNM',
                'ubbrgdw.NO_BUKTI',
                'ubbrgdw.SELESAI',
            ])
            ->where('ubbrgdw.NO_BUKTI', '<>', '')
            ->where('ubbrgdw.SELESAI', '=', '1')
            ->get();

        /** @var \stdClass $row */
        foreach ($query as $row) {
            $row->POSTED = DB::table('ubbrgdwd')
                ->where('NO_BUKTI', $row->NO_BUKTI)->limit(1)
                ->value('POSTED');
        }

        // Filter by period if provided
        if ($periode) {
            $period = explode('/', $periode);
            if (count($period) == 2) {
                $month = str_pad($period[0], 2, '0', STR_PAD_LEFT);
                $year  = $period[1];
                $query->whereRaw("DATE_FORMAT(TGL, '%m/%Y') = ?", [$month . '/' . $year]);
            }
        }

        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                // batas
                $url = "'" . url("ubbrgdw/delete/" . $row->NO_BUKTI . "/?flagz=" . $row->FLAG) . "'";

                $btnPrivilege =
                '
                                <a class="dropdown-item btn btn-danger" ' .
                                    (($row->POSTED == false) ?
                                        ' onclick="alert(\'Perubahan harga ' . $row->NO_BUKTI . ' Belum Diusulkan!\')" href="#" ' :
                                        ' target="_blank" href="' . route('ubbrgdw.cetak', ['NO_ID' => $row->NO_ID, 'selesai' => 'true']) . '"'
                                    ) . '>
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print Laporan
                                </a>

                        ';

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

            ->editColumn('TGL', function ($row) {
                return date('d-m-Y', strtotime($row->TGL));
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function cetak($id, Request $request)
    {
        $selesai = $request->get('selesai');

        $no_ubbrgdw = $id;

        if ($selesai) {
            $file = 'ubbrgdw-l-baru1';
        } else {
            $file = 'ubbrgdw-u-baru';

        }
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        //pp.GUDANG setelah pp.NETT dihapus
        $query = DB::SELECT("SELECT ubbrgdw.NO_BUKTI, ubbrgdw.TGL, ubbrgdw.KODES, ubbrgdw.NAMAS, ubbrgdw.NO_BELI,
                                    ubbrgdwd.KD_BRG, ubbrgdwd.NA_BRG, ubbrgdwd.KET, ubbrgdwd.QTY, ubbrgdwd.HARGA, ubbrgdwd.HARGALAMA, ubbrgdwd.DISK, ubbrgdwd.DISK2, ubbrgdwd.DISK3,ubbrgdwd.DISK4, ubbrgdwd.DISKLAMA, ubbrgdwd.DISKLAMA2, ubbrgdwd.DISKLAMA3,ubbrgdwd.DISKLAMA4,
                                    vbrg.KET_UK, vbrg.KET_KEM, VBRG.KLK, VBRG.MO1, VBRG.PPN, VBRG.HJUAL, ubbrgdwd.HJUALLAMA, ubbrgdw.SELESAI
                            FROM ubbrgdw, ubbrgdwd, vbrg
                            WHERE ubbrgdw.NO_ID='$no_ubbrgdw' AND ubbrgdw.NO_BUKTI = ubbrgdwd.NO_BUKTI AND ubbrgdwd.KD_BRG=vbrg.KD_BRG
                            ;

		");

        $data = [];
        foreach ($query as $key => $value) {
            array_push($data, [
                'NO_BUKTI'  => $query[0]->NO_BUKTI,
                'TGL'       => date('d/m/Y', strtotime($query[0]->TGL)),
                'TGL_NOW'   => now()->format('d/m/Y'),
                'KODES'     => $query[0]->KODES,
                'NAMAS'     => $query[0]->NAMAS,
                'NO_BELI'   => $query[0]->NO_BELI,
                'KD_BRG'    => $query[$key]->KD_BRG,
                'NA_BRG'    => $query[$key]->NA_BRG,
                'KET'       => $query[$key]->KET == null ? '-' : $query[$key]->KET,
                'QTY'       => $query[$key]->QTY,
                'HARGA'     => $query[$key]->HARGA,
                'HARGALAMA' => $query[$key]->HARGALAMA,
                'DISK'      => $query[$key]->DISK,
                'DISK2'     => $query[$key]->DISK2,
                'DISK3'     => $query[$key]->DISK3,
                'DISK4'     => $query[$key]->DISK4,
                'DISKLAMA'  => $query[$key]->DISKLAMA,
                'DISKLAMA2' => $query[$key]->DISKLAMA2,
                'DISKLAMA3' => $query[$key]->DISKLAMA3,
                'DISKLAMA4' => $query[$key]->DISKLAMA4,
                'KET_UK'    => $query[$key]->KET_UK,
                'KET_KEM'   => $query[$key]->KET_KEM,
                'KLK'       => $query[$key]->KLK,
                'MO1'       => $query[$key]->MO1,
                'PPN'       => $query[$key]->PPN,
                'HJUAL'       => $query[$key]->HJUAL,
                'HJUALLAMA'       => $query[$key]->HJUALLAMA,
            ]);
        }
        $selesaix = $query[0]->SELESAI;
        if ($selesaix == 0 && $selesai) {
            DB::SELECT("UPDATE ubbrgdw SET SELESAI = 1 WHERE NO_ID='$no_ubbrgdw';");

            DB::statement("
                UPDATE vbrgdw
                JOIN ubbrgdwd ON ubbrgdwd.KD_BRG = vbrgdw.KD_BRG
                JOIN ubbrgdw ON ubbrgdw.NO_BUKTI = ubbrgdwd.NO_BUKTI
                SET
                    vbrgdw.HARGA = ubbrgdwd.HARGA,
                    vbrgdw.DISC  = ubbrgdwd.DISK,
                    vbrgdw.DISC2 = ubbrgdwd.DISK2,
                    vbrgdw.DISC3 = ubbrgdwd.DISK3,
                    vbrgdw.DISC4 = ubbrgdwd.DISK4
                WHERE ubbrgdw.NO_ID = ?
            ", [$no_ubbrgdw]);

        } else {
            DB::SELECT("UPDATE ubbrgdwd SET POSTED = 1 WHERE ID='$no_ubbrgdw' AND NO_BUKTI = '" . $query[0]->NO_BUKTI . "';");
        }
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }
    public function browse_detail(Request $request)
    {
        $id       = $request->get('id');
        $no_bukti = $request->get('no_bukti');

        // Jika menggunakan no_bukti, cari ID dulu
        if ($no_bukti && ! $id) {
            $header = DB::table('ubbrgdw')->where('NO_BELI', $no_bukti)->first();
            if ($header) {
                $id = $header->NO_ID;
            }
        }

        $details = DB::table('ubbrgdwd')->where('ID', $id)->where('NO_BUKTI', '<>', '')->orderBy('REC')->get();

        return response()->json(['data' => $details]);
    }

    public function edit(Request $request)
    {
        $idx = $request->get('idx');
        // dd($idx);

        $tipx = $request->get('tipx');

        if ($tipx == 'new') {
            // Create empty object for new record
            $header = (object) [
                'NO_ID'     => 0,
                'NO_BUKTI'  => '',
                'NO_BELI'   => '',
                'TGL'       => date('Y-m-d'),
                'KODES'     => '',
                'NAMAS'     => '',
                'KODEC'     => '',
                'NAMAC'     => '',
                'PKP'       => 0,
                'NOTES'     => '',
                'TOTAL_QTY' => 0,
                'KET'       => '',
                'USRNM'     => auth()->user()->username ?? '',
                'POSTED'    => 0,
            ];
            $detail = collect(); // Empty collection
        } else {
            $header = DB::table('ubbrgdw')->where('NO_ID', $idx)->first();
            $detail = DB::table('ubbrgdwd')->where('NO_BUKTI', $header->NO_BUKTI)->where('NO_BUKTI', '<>', '')->orderBy('REC')->get();
        }

        return view('otransaksi_ubbrgdw.edit', compact('header', 'detail', 'tipx'));
    }

    public function update(Request $request, Ubbrgdw $ubbrgdw)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );
        // dd($request->TGL);

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $ubbrgdw->update(
            [
                'TGL' => $request->TGL
                ? Carbon::createFromFormat('d-m-Y', $request->TGL)->format('Y-m-d')
                : null,
                'USRNM' => Auth::user()->username,
                'PER'   => $periode,
                'CBG'   => Auth::user()->CBG,
            ]
        );

        $no_buktix = $request->NO_BUKTI;

        // Update Detail
        $length = sizeof($request->input('REC'));
        $NO_ID  = $request->input('NO_ID');

        $REC   = $request->input('REC');
        $HARGA = $request->input('HARGA');
        $DISK  = $request->input('DISK');
        $DISK2 = $request->input('DISK2');
        $DISK3 = $request->input('DISK3');
        $DISK4 = $request->input('DISK4');
        $KET = $request->input('KET');

        $query = DB::table('ubbrgdwd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        // Update / Insert
        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = UbbrgdwDetail::create(
                    [
                        'NO_BUKTI' => $request->NO_BUKTI,
                        'REC'      => $REC[$i],
                        'HARGA'    => (float) str_replace(',', '', $HARGA[$i]),
                        'DISK'     => (float) str_replace(',', '', $DISK[$i]),
                        'DISK2'    => (float) str_replace(',', '', $DISK2[$i]),
                        'DISK3'    => (float) str_replace(',', '', $DISK3[$i]),
                        'DISK4'    => (float) str_replace(',', '', $DISK4[$i]),
                        'KET'      => $ket[$i] ?? '',
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = UbbrgdwDetail::updateOrCreate(
                    [
                        'NO_BUKTI' => $request->NO_BUKTI,
                        'NO_ID'    => (int) str_replace(',', '', $NO_ID[$i]),
                    ],

                    [
                        'HARGA' => (float) str_replace(',', '', $HARGA[$i]),
                        'DISK'  => (float) str_replace(',', '', $DISK[$i]),
                        'DISK2' => (float) str_replace(',', '', $DISK2[$i]),
                        'DISK3' => (float) str_replace(',', '', $DISK3[$i]),
                        'DISK4' => (float) str_replace(',', '', $DISK4[$i]),
                        'KET'   => $ket[$i] ?? '',
                    ]
                );
            }
        }

        $ubbrgdw = Ubbrgdw::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $ubbrgdw->NO_BUKTI;

        DB::update("UPDATE ubbrgdw, ubbrgdwd
                        SET ubbrgdwd.ID = ubbrgdw.NO_ID
                        WHERE ubbrgdw.NO_BUKTI = ubbrgdwd.NO_BUKTI
                        AND ubbrgdw.NO_BUKTI = ?
                    ", [$no_bukti]);

        return redirect('/ubbrgdw/edit/?idx=' . $ubbrgdw->NO_ID . '&tipx=edit');

    }

    public function browse_nobeli(Request $request)
    {
        $no_bukti = $request->NO_BUKTI ?? '';

        $sql = "
            SELECT DISTINCT
                nwagend.NO_BUKTI AS NO_BELI
            FROM nwagend
            INNER JOIN nwagendd
                ON nwagend.NO_BUKTI = nwagendd.NO_BUKTI
            LEFT JOIN vbrgdw
                ON nwagendd.KD_BRG = vbrgdw.KD_BRG
            WHERE nwagend.NO_BUKTI LIKE '%BS%'
        ";

        if (! empty($no_bukti)) {
            $sql .= " AND nwagend.NO_BUKTI LIKE '%$no_bukti%'";
        }

        $sql .= "
            AND (
                nwagendd.HARGA <> vbrgdw.HARGA
                OR nwagendd.DISKON1  <> vbrgdw.DISCLAMA
                OR nwagendd.DISKON2 <> vbrgdw.DISCLAMA2
                OR nwagendd.DISKON3 <> vbrgdw.DISCLAMA3
                OR nwagendd.DISKON4 <> vbrgdw.DISCLAMA4
            )
            ORDER BY nwagend.NO_BUKTI DESC
        ";

        $results = DB::select($sql);

        return response()->json($results);
    }

    public function get_detail_by_nobeli(Request $request)
    {
        $no_beli = $request->get('no_beli');
        //
        $header = DB::select("SELECT
                                nwagend.NO_BUKTI,
                                nwagend.TGL,
                                nwagend.TG_SMP,
                                nwagend.KODES,
                                nwagend.NAMAS,
                                nwmassup.TLP_K,

                                nwagendd.KD_BRG,
                                nwagendd.NA_BRG,
                                nwagendd.HARGA,
                                nwagendd.DISKON1,
                                nwagendd.DISKON2,
                                nwagendd.DISKON3,
                                nwagendd.DISKON4,


								vbrgdw.HARGA as HARGALAMA,

                                vbrgdw.DISCLAMA      AS DISKLAMA,
                                vbrgdw.DISCLAMA2     AS DISKLAMA2,
                                vbrgdw.DISCLAMA3     AS DISKLAMA3,
                                vbrgdw.DISCLAMA4     AS DISKLAMA4,

                                nwagendd.QTY,
                                nwagendd.PPN,
                                nwmasbar.KET_UK

                            FROM nwagend
                            INNER JOIN nwagendd
                                ON nwagend.NO_BUKTI = nwagendd.NO_BUKTI

                            LEFT JOIN nwmasbar
                                ON nwagendd.KD_BRG = nwmasbar.KDBAR

                            LEFT JOIN vbrgdw
                                ON nwagendd.KD_BRG = vbrgdw.KD_BRG

                            LEFT JOIN nwmassup
                                ON nwagend.KODES = nwmassup.NO_SUPL

                            WHERE nwagend.NO_BUKTI = ?
							AND nwagend.NO_BUKTI LIKE '%BS%'
                            AND (
                                    nwagendd.HARGA <> vbrgdw.HARGA
                                OR nwagendd.DISKON1  <> vbrgdw.DISCLAMA
                                OR nwagendd.DISKON2 <> vbrgdw.DISCLAMA2
                                OR nwagendd.DISKON3 <> vbrgdw.DISCLAMA3
                                OR nwagendd.DISKON4 <> vbrgdw.DISCLAMA4
                            )

                                 ", [$no_beli]);
        
        if (! $header) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        
        $details = DB::select("SELECT
                                nwagend.NO_BUKTI,
                                nwagend.TGL,
                                nwagend.TG_SMP,
                                nwagend.KODES,
                                nwagend.NAMAS,
                                nwmassup.TLP_K,

                                nwagendd.KD_BRG,
                                nwagendd.NA_BRG,
                                nwagendd.HARGA,
                                nwagendd.DISKON1,
                                nwagendd.DISKON2,
                                nwagendd.DISKON3,
                                nwagendd.DISKON4,


								vbrgdw.HARGA as HARGALAMA,

                                vbrgdw.DISCLAMA      AS DISKLAMA,
                                vbrgdw.DISCLAMA2     AS DISKLAMA2,
                                vbrgdw.DISCLAMA3     AS DISKLAMA3,
                                vbrgdw.DISCLAMA4     AS DISKLAMA4,

                                nwagendd.QTY,
                                nwagendd.PPN,
                                nwmasbar.KET_UK

                            FROM nwagend
                            INNER JOIN nwagendd
                                ON nwagend.NO_BUKTI = nwagendd.NO_BUKTI

                            LEFT JOIN nwmasbar
                                ON nwagendd.KD_BRG = nwmasbar.KDBAR

                            LEFT JOIN vbrgdw
                                ON nwagendd.KD_BRG = vbrgdw.KD_BRG

                            LEFT JOIN nwmassup
                                ON nwagend.KODES = nwmassup.NO_SUPL

                            WHERE nwagend.NO_BUKTI = ?
							AND nwagend.NO_BUKTI LIKE '%BS%'
                            AND (
                                    nwagendd.HARGA <> vbrgdw.HARGA
                                OR nwagendd.DISKON1  <> vbrgdw.DISCLAMA
                                OR nwagendd.DISKON2 <> vbrgdw.DISCLAMA2
                                OR nwagendd.DISKON3 <> vbrgdw.DISCLAMA3
                                OR nwagendd.DISKON4 <> vbrgdw.DISCLAMA4
                            )

                                 ", [$no_beli]);

        return response()->json([
            'header'  => $header,
            'details' => $details,
        ]);
    }

   public function delete($id, Request $request)
{

    $flagz = $request->flagz;

        try {
            DB::beginTransaction();

            // Delete detail records first
            DB::table('ubbrgdwd')->where('NO_BUKTI', $id)->delete();

            // Delete header record
            DB::table('ubbrgdw')->where('NO_BUKTI', $id)->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage(),
            ]);
        }
    }

    public function ubbrgdwOtomatis()
    {
        $bulan     = str_pad(session()->get('periode')['bulan'], 2, '0', STR_PAD_LEFT);
        $tahunFull = session()->get('periode')['tahun'];
        $tahun     = substr($tahunFull, -2);
        $periode   = $bulan . '/' . $tahunFull;

        $CBG       = 'DC1';
        $total_qty = 0;

        $periodeAwal = $bulan < 7 ? true : false;

        if ($periodeAwal) {
            // $query = "SELECT belid.NO_BUKTI, KD_BRG, SUM(QTY) AS QTY, HARGA,HARGALAMA, DISK, DISK2, DISK3, DISK4, DISKLAMA, DISKLAMA2,
            //       DISKLAMA3, DISKLAMA4
            //       FROM belid, beli, VBRG, VBRGD
            //       WHERE belid.no_bukti = beli.no_bukti
            //       VBRG.KD_BRG=belid.KD_BRG
            //        VBRGDW.KD_BRG=belid.KD_BRG
            //       AND beli.POSTED = 1
            //       AND beli.PER >= '07/" . ($tahunFull - 1) . "'
            //       AND beli.PER <= '12/" . ($tahunFull - 1) . "'
            //       AND beli.FLAG = 'BL' AND  COALESCE(belid.PROSES_UBBRGDW,0) = 0
            //       GROUP BY KD_BRG";

            $query = "
                    SELECT
                        belid.NO_BUKTI,
                        belid.KD_BRG,
                        SUM(belid.QTY) AS QTY,
                        vbrg.HARGA AS HARGA,
                        vbrgdw.HARGA as HARGALAMA,
                        belid.DISK,
                        belid.DISK2,
                        belid.DISK3,
                        belid.DISK4,
                        belid.DISKLAMA,
                        belid.DISKLAMA2,
                        belid.DISKLAMA3,
                        belid.DISKLAMA4,
                        beli.KODES,
                        beli.NAMAS
                    FROM belid
                    JOIN beli ON belid.NO_BUKTI = beli.NO_BUKTI
                    JOIN VBRG ON VBRG.KD_BRG = belid.KD_BRG
                    JOIN VBRGDW ON VBRGDW.KD_BRG = belid.KD_BRG
                    WHERE
                        beli.POSTED = 1
                        AND beli.PER >= '07/" . ($tahunFull - 1) . "'
                        AND beli.PER <= '12/" . ($tahunFull - 1) . "'
                        AND beli.FLAG = 'BL'
                        AND vbrg.HARGA <> vbrgdw.HARGA
                        AND COALESCE(belid.PROSES_UBBRGDW, 0) = 0
                    GROUP BY
                        KD_BRG
                    ORDER BY NO_BUKTI
                    ";
        } else {
            $query = "SELECT belid.NO_BUKTI, belid.KD_BRG, SUM(belid.QTY) AS QTY,  vbrg.HARGA AS HARGA,
                        vbrgdw.HARGA as HARGALAMA,  belid.DISK, belid.DISK2, belid.DISK3, belid.DISK4,
                        belid.DISKLAMA, belid.DISKLAMA2,
                  belid.DISKLAMA3, belid.DISKLAMA4, beli.KODES,
                        beli.NAMAS
                  FROM belid
                    JOIN beli ON belid.NO_BUKTI = beli.NO_BUKTI
                    JOIN VBRG ON VBRG.KD_BRG = belid.KD_BRG
                    JOIN VBRGDW ON VBRGDW.KD_BRG = belid.KD_BRG
                  AND beli.POSTED = 1
                  AND beli.PER >= '01/$tahunFull'
                  AND beli.PER <= '06/$tahunFull'
                  AND vbrg.HARGA <> vbrgdw.HARGA
                  AND beli.FLAG = 'BL' AND COALESCE(belid.PROSES_UBBRGDW,0) = 0
                  GROUP BY KD_BRG  ORDER BY NO_BUKTI";
        }

        $data = collect(DB::select($query));

        // $header = DB::SELECT("SELECT * FROM ubbrgdw");
        // $detail = DB::SELECT("SELECT * FROM ubbrgdwd");

        if ($data->count() == 0) {
            return back()->with('error', 'Data kosong');
        }

        $lastNumber = DB::table('ubbrgdw')
            ->where('NO_BUKTI', 'LIKE', 'DE' . $CBG . $tahun . $bulan . '-%')
            ->select(DB::raw('MAX(RIGHT(NO_BUKTI,4)) as last'))
            ->value('last');

        $nextNumber = $lastNumber ? ((int) $lastNumber + 1) : 1;

        // Pecah per 85 detail
        // dd($data);
        foreach ($data->chunk(85) as $chunk) {

            $no_bukti = 'DE' . $CBG . $tahun . $bulan . '-' .
            str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            $total_qty = $chunk->sum('QTY');

            $header = Ubbrgdw::create([
                'NO_BUKTI'  => $no_bukti,
                'TGL'       => now(),
                'PER'       => $periode,
                'FLAG'      => 'BL',
                'CBG'       => $CBG,
                'KET'       => 'OTOMATIS',
                'TOTAL_QTY' => $total_qty,
                'USRNM'     => Auth::user()->username,
            ]);

            $REC = 1;



            foreach ($chunk as $item) {


                if ($item->QTY > 0) {


                    $barang = DB::table('vbrg')
                        ->where('KD_BRG', $item->KD_BRG)
                        ->first();

                    if (! $barang) {
                        continue;
                    }

                    $namas = DB::table('zsup')
                        ->where('KODES', $barang->KODES)
                        ->value('NAMAS');

                    UbbrgdwDetail::create([
                        'NO_BUKTI'  => $no_bukti,
                        'ID'        => $header->NO_ID,
                        'REC'       => $REC,
                        'PER'       => $periode,
                        'FLAG'      => 'BL',
                        'KD_BRG'    => $item->KD_BRG,
                        'NA_BRG'    => $barang->NA_BRG,
                        'SATUAN'    => $barang->SATUAN,
                        'QTY'       => $item->QTY,
                        'HARGA'     => $item->HARGA,
                        'HARGALAMA' => $item->HARGALAMA,
                        'DISK'      => $item->DISK,
                        'DISK2'     => $item->DISK2,
                        'DISK3'     => $item->DISK3,
                        'DISK4'     => $item->DISK4,
                        'DISKLAMA'  => $item->DISKLAMA,
                        'DISKLAMA2' => $item->DISKLAMA2,
                        'DISKLAMA3' => $item->DISKLAMA3,
                        'DISKLAMA4' => $item->DISKLAMA4,
                        'KODES'     => $item->KODES,
                        'NAMAS'     => $item->NAMAS,
                    ]);
                       $REC++;

                    DB::table('belid')
                        ->where('NO_BUKTI', $item->NO_BUKTI)
                        ->where('KD_BRG', $item->KD_BRG)
                        ->update([
                            'PROSES_UBBRGDW' => 1,
                        ]);

                }

            }

            $nextNumber++;
        }
        return redirect()->to("ubbrgdw/edit?idx={$header->NO_ID}&tipx=edit");
    }

}
