<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\Master\Vbrg;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class VBrgDwHargaPemulaNewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function browse(Request $request)
    // {
    //     $KD_BRG = $request->KD_BRG;

    //     $vbrgdw = DB::SELECT("SELECT * from vbrg ORDER BY KODES ");

    //     return response()->json($vbrgdw);
    // }

    public function browse(Request $request)
    {
        $query = DB::table('vbrg')
            ->orderBy('KODES');

        if ($request->filled('KD_BRG')) {
            $query->where('KD_BRG', 'like', '%' . $request->KD_BRG . '%');
        }

        $vbrgdw = $query->get();

        return response()->json($vbrgdw);
    }

    public function index()
    {

        return view('master_vbrgdw_harga_pemula_new.index');
    }

    public function browse_kodes(Request $request)
    {
        $q = $request->q;

        $results = DB::table('zsup')
            ->select('KODES', 'NAMAS')
            ->when($q, function ($query) use ($q) {
                $query->where('KODES', 'LIKE', "%{$q}%")
                    ->orWhere('NAMAS', 'LIKE', "%{$q}%");
            })
            ->groupBy('KODES')
            ->orderBy('KODES', 'desc')
            ->get();

        return response()->json($results);
    }

    public function getVbrgdw(Request $request)
    {
        // ganti 5

        $periode = null;

        if ($request->session()->has('periode')) {
            $bulan   = $request->session()->get('periode.bulan');
            $tahun   = $request->session()->get('periode.tahun');
            $periode = $bulan . '/' . $tahun;
        }
        $wherePeriode = '';

        if ($periode) {
            $wherePeriode = "AND MONTH(TGL) = $bulan AND YEAR(TGL) = $tahun";
        }

        $vbrg = DB::select("
                            SELECT *
                            FROM (
                                SELECT *,
                                    ROW_NUMBER() OVER (PARTITION BY NO_BUKTI ORDER BY NO_ID) AS rn
                                FROM vbrgdw
                                WHERE NO_BUKTI != ''
                                $wherePeriode
                            ) x
                            WHERE rn = 1
                            ORDER BY NO_BUKTI
                        ");

        return Datatables::of($vbrg)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "owner" || Auth::user()->divisi == "sales" || Auth::user()->divisi == 'pembelian') {
                    $url = "'" . url("vbrgdw-harga-pemula-new/delete/" . $row->NO_ID) . "'";
                    // batas

                    $btnDelete = ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege =
                    '
                                    <a class="dropdown-item" href="vbrgdw-harga-pemula-new/edit/?idx=' . $row->NO_BUKTI . '&tipx=edit">
                                    <i class="fas fa-edit"></i>
                                        Edit
                                    </a>
                                     <a class="dropdown-item" target="_blank"
                                        href="' . url('vbrgdw-harga-pemula-new/print/' . $row->NO_BUKTI) . '">
                                        <i class="fas fa-print"></i> Print Usulan
                                    </a>

                                    <a class="dropdown-item" target="_blank"
                                        href="' . url('vbrgdw-harga-pemula-new/print-pengesahan/' . $row->NO_BUKTI) . '">
                                        <i class="fas fa-file-signature"></i> Print Pengesahan
                                    </a>

                                    <hr></hr>
                                    <a hidden class="dropdown-item btn btn-danger" ' . $btnDelete . '>

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
            ->rawColumns(['action'])
            ->make(true);

    }
    public function store(Request $request)
    {

        $NO_BUKTI = $request->NO_BUKTI;
        $KODES    = $request->KODES;
        $NAMAS    = $request->NAMAS;
        $TGL      = Carbon::createFromFormat('d-m-Y', $request->TGL)
            ->format('Y-m-d');

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $prefix = "PGZ{$tahun}{$bulan}-";

        if (empty($NO_BUKTI)) {
            $last = DB::table('vbrgdw')
                ->where('NO_BUKTI', 'like', $prefix . '%')
                ->orderBy('NO_BUKTI', 'desc')
                ->value('NO_BUKTI');

            $num      = $last ? ((int) substr($last, -4)) + 1 : 1;
            $NO_BUKTI = $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
        }
        // dd($NO_BUKTI);

        $existsNoBukti = DB::table('vbrgdw')
            ->where('NO_BUKTI', $NO_BUKTI)
            ->exists();

        foreach ($request->detail as $row) {

            $harga = (float) str_replace(',', '', $row['HARGA']);
            $disc  = (float) str_replace(',', '', $row['DISC']);
            $disc2 = (float) str_replace(',', '', $row['DISC2']);
            $disc3 = (float) str_replace(',', '', $row['DISC3']);
            $disc4 = (float) str_replace(',', '', $row['DISC4']);

            if ($existsNoBukti) {

                DB::table('vbrgdw')
                    ->where('KD_BRG', $row['KD_BRG'])
                    ->where('NO_BUKTI', $NO_BUKTI)
                    ->update([
                        'KD_BRG' => $row['KD_BRG'],
                        'NA_BRG' => $row['NA_BRG'],
                        'HARGA'  => $harga,
                        'DISC'   => $disc,
                        'DISC2'  => $disc2,
                        'DISC3'  => $disc3,
                        'DISC4'  => $disc4,
                    ]);

            } else {

                DB::table('vbrgdw')->insert([
                    'NO_BUKTI' => $NO_BUKTI,
                    'KODES'    => $KODES,
                    'NAMAS'    => $NAMAS,
                    'TGL'      => $TGL,
                    'KD_BRG'   => $row['KD_BRG'],
                    'NA_BRG'   => $row['NA_BRG'],
                    'HARGA'    => $harga,
                    'DISC'     => $disc,
                    'DISC2'    => $disc2,
                    'DISC3'    => $disc3,
                    'DISC4'    => $disc4,
                ]);
            }
        }

        return redirect('/vbrgdw-harga-pemula-new')
            ->with('statusInsert', 'Data berhasil disimpan. No Bukti: ' . $NO_BUKTI);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 15

    public function edit(Request $request, Vbrg $vbrg)
    {

        $pilih = DB::table('vbrgdw')->get();

        // ganti 16

        $tipx = $request->tipx;

        $idx = $request->idx;

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $kodex = $request->no_id;

            $bingco = DB::SELECT("SELECT NO_ID from vbrgdw
		                 where NO_ID = '$kodex'
		                 ORDER BY NO_ID ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {
            $bingco = DB::SELECT("SELECT NO_ID from vbrgdw
		                 ORDER BY NO_ID ASC  LIMIT 1");
            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $kodex = $request->no_id;

            $bingco = DB::SELECT("SELECT NO_ID from vbrgdw
		             where NO_ID <
					 '$kodex' ORDER BY NO_ID DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }
        if ($tipx == 'next') {
            $kodex = $request->no_id;

            $bingco = DB::SELECT("SELECT NO_ID from vbrgdw
                    where NO_ID >
                    '$kodex' ORDER BY NO_ID ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID from vbrgdw
		              ORDER BY NO_ID DESC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'undo' || $tipx == 'search') {

            $tipx = 'edit';

        }
        $header = null;
        $detail = [];

        $header = DB::table('vbrgdw')
            ->where('NO_BUKTI', $idx)
            ->first();

        $detail = DB::table('vbrgdw')
            ->where('NO_BUKTI', $idx)
            ->get()
            ->toArray();

        return view('master_vbrgdw_harga_pemula_new.edit', [
            'header' => $header,
            'detail' => $detail,
            'idx'    => $idx,
        ]);

    }

    public function printUsulan($no_bukti, Request $request)
    {
        $TGL          = Carbon::now()->format('d-m-Y');
        $file         = "usulan_harga_pemula";
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT *, DATE_FORMAT(TGL, '%d-%m-%Y') AS TGL FROM vbrgdw WHERE NO_BUKTI='$no_bukti'");

        $cleanData                    = json_decode(json_encode($query), true);
        $PHPJasperXML->arrayParameter = [
            "TGL" => $TGL,
        ];

        $PHPJasperXML->setData($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }

    public function printPengesahan($no_bukti, Request $request)
    {
        $TGL          = Carbon::now()->format('d-m-Y');
        $file         = "pengesahan_harga_pemula";
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        DB::table('vbrgdw')
            ->where('NO_BUKTI', $no_bukti)
            ->update([
                'POSTED' => 1,
            ]);

        $query = DB::SELECT("SELECT * FROM vbrgdw WHERE NO_BUKTI='$no_bukti'");

        $cleanData                    = json_decode(json_encode($query), true);
        $PHPJasperXML->arrayParameter = [
            "TGL" => $TGL,
        ];

        $PHPJasperXML->setData($cleanData);
        // dd($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

    }
}
