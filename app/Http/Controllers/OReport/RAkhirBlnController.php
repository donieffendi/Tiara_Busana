<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\Master\Perid;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

class RAkhirBlnController extends Controller
{
    public function report(Request $request)
    {
        $per = Perid::query()->get();
		$periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
		session()->put('filter_periode', $periode);

        return view('oreport_akhir_bulan.report')->with(['per' => $per])->with(['hasil' => []]);
    }

    public function getAkhirBulan(Request $request)
    {
        $periode = $request->perio;

        if (empty($periode)) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Periode belum dipilih!'
            ], 422);
        }

        try {
            DB::SELECT("CALL akhir_bulan_vbrg_biru(?)", [$periode]);

            session()->put('filter_periode', $periode);

            return response()->json([
                'status' => 'success',
                'message' => 'Proses akhir bulan berhasil!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proses akhir bulan gagal!',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}