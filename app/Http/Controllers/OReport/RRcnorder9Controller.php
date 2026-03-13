<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

class RRcnorder9Controller extends Controller
{
    public function report()
    {
        $cbg = Cbg::groupBy('CBG')->get();
        $subList = DB::table('brg')->select('SUB')->distinct()->orderBy('SUB')->pluck('SUB');

        // Initialize session variables
        session()->put('filter_cbg', '');
        session()->put('filter_sub', '');
        session()->put('filter_ulang', '');
        session()->put('filter_nobukti', '');

        return view('oreport_rcnorder9.report')->with([
            'cbg'       => $cbg,
            'rcnorder9' => [],
            'subList' => $subList
        ]);
    }

    public function getRcnorder9Report(Request $request)
    {
        $listCBG = Cbg::groupBy('CBG')->get(); // ⬅ hanya untuk dropdown list
        $subList = DB::table('brg')->select('SUB')->distinct()->orderBy('SUB')->pluck('SUB');
        $cbg     = $request->cbg;
        $sub     = $request->sub;
        $ulang   = $request->ulang;
        $nobukti = $request->nobukti;

        // Set filter values to session
        session()->put('filter_cbg', $request->cbg);
        session()->put('filter_sub', $request->sub);
        session()->put('filter_ulang', $request->ulang);
        session()->put('filter_nobukti', $request->nobukti);

        $rcnorder9 = [];
        // dd($request->all());
        if (! empty($request->cbg)) {
            // Validate kode tidak boleh kosong
            if (empty($request->sub)) {
                return redirect()->back()->withErrors(['sub' => 'Sub Tidak Boleh Kosong']);
            }

            if ($ulang == '1') {
                if (empty($request->nobukti)) {
                    return redirect()->back()->withErrors(['nobukti' => 'No Bukti Tidak Boleh Kosong']);
                }
            }
            // Get data barang macet
            $rcnorder9 = $this->getRcnorder9($request->cbg, $request->sub, $request->ulang, $request->nobukti);
        }

        return view('oreport_rcnorder9.report')->with([
            'cbg'       => $listCBG,
            'rcnorder9' => $rcnorder9,
            'subList' => $subList
        ]);
    }

    /**
     * Get data barang macet berdasarkan jenis yang dipilih
     * Equivalent dengan logic dalam procedure Tampil
     */
    private function getRcnorder9($cbg, $sub, $ulang, $nobukti)
    {
        try {

            if ($ulang == 0) {
                $result = DB::select('CALL gd_koreksi_tgl_produksi (?, ?, ?, ?, ?)', ['REPORT_STOK_NOL_KD8', '', $cbg, $sub, '']);
            } else {
                $result = DB::select('CALL gd_koreksi_tgl_produksi (?, ?, ?, ?, ?)', ['REPORT_STOK_NOL_KD8', $nobukti, $cbg, $sub, '']);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Error in getRcnorder9: ' . $e->getMessage());
            return [];
        }
    }

    public function print(Request $request)
    {
        $noBukti = $request->input('no_bukti');
        $sub = $request->input('sub');
        $ulang = $request->input('ulang');
        $cbg     = Auth::user()->CBG;
        $TGL     = Carbon::now()->format('d/m/Y');
        $JAM = Carbon::now('Asia/Jakarta')->addHour()->toTimeString();

        $toko = DB::table('toko')
            ->where('KODE', $cbg)
            ->value('NA_TOKO');

        if ($ulang == 1) {
            $result = DB::select('CALL gd_koreksi_tgl_produksi (?, ?, ?, ?, ?)', ['REPORT_STOK_NOL_KD8', '', $cbg, $sub, '']);
        } else {
            $result = DB::select('CALL gd_koreksi_tgl_produksi (?, ?, ?, ?, ?)', ['REPORT_STOK_NOL_KD8', $noBukti, $cbg, $sub, '']);
        }

        $file = 'rencana_kode9';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path("/app/reportc01/phpjasperxml/{$file}.jrxml"));

        // $PHPJasperXML->setData($data);
        $cleanData                    = json_decode(json_encode($result), true);
        $PHPJasperXML->arrayParameter = [
            "TGL"     => $TGL,
            "JAM"     => $JAM,
        ];
        //dd($cleanData);

        $PHPJasperXML->setData($cleanData);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
}