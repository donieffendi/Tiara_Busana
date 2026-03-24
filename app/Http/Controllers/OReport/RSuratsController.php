<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
// ganti 1
use App\Models\Master\Perid;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class RSuratsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function report()
    {
        $cbg = Cbg::groupBy('CBG')->get();
        $per = Perid::query()->get();
        session()->put('filter_cbg', '');
        session()->put('filter_sup', '');
        session()->put('filter_sup2', 'ZZZ');

        return view('oreport_surats.report')->with(['cbg' => $cbg, 'per' => $per])->with(['hasil' => []]);

    }

    public function jasperSuratsReport(Request $request)
    {
        $file         = 'suratsn';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $per   = $request->per;
        $cbg   = $request->cbg;
        $sub1  = $request->sub1;
        $sub2  = $request->sub2;
        $kode1 = $request->kode1;
        $kode2 = $request->kode2;
        $urut  = $request->urut;
        $mode  = $request->mode ?? 'periode';

        $yerini = date('Y');
        $yeritu = substr($per, -4);

        $monthNow = date('m');
        $yearNow  = date('Y');

        $mon = substr($per, 0, 2);

        if ($mon == $monthNow && $yeritu == $yearNow) {
            $mon = '00';
        }

        $whereCbg = '';
        if (! empty($cbg) && $cbg != 'ALL') {
            $whereCbg = " AND brgbsnd.cbg = '$cbg'";
        }

        $order = ($urut == 'kode_brg') ? 'brgbsn.kd_brg' : 'brgbsn.na_brg';

        if ($yerini == $yeritu) {
            $tableDetail = 'brgbsnd';
        } else {
            $tableDetail = 'brgbsnd';
            //sementara disamakan untuk test data
            //$tableDetail = 'brgbsnd' . $yeritu;
        }
        // dd( $sub1, $sub2, $kode1, $kode2,$whereCbg);
        // dd($tableDetail);
        if ($mode == 'periode') {

            $query = DB::select("
        SELECT
            brgbsn.kd_brg,
            brgbsn.cnt,
            brgbsn.ncnt,
            brgbsn.NA_brg,
            brgbsn.barcode,
            '$per' as per,
            '$cbg' as cbg,
            brgbsn.HJUAL,
            brgbsn.tgl_trm,
            brgbsnd.TGL_KSR as tgl_jual,

            SUM(brgbsnd.aw$mon) as aw,
            SUM(brgbsnd.ma$mon) as ma,
            SUM(brgbsnd.ke$mon) as ke,
            SUM(brgbsnd.ln$mon) as ln,
            SUM(brgbsnd.AK$mon) as ak,

            SUM(brgbsnd.AK$mon) * brgbsn.HBELI as tbeli,
            SUM(brgbsnd.AK$mon) * brgbsn.HJUAL as tsisa,

            brgbsn.hbeli,
            brgbsn.hbnet,
            brgbsnd.psn as statpsn,
            CONCAT(brgbsnd.td_od,'-',cat_od) as tdod

        FROM brgbsn
        JOIN $tableDetail brgbsnd ON brgbsn.kd_brg = brgbsnd.kd_brg

        WHERE
            brgbsn.cnt >= ? AND brgbsn.cnt <= ?
            AND brgbsn.kd_brg >= ? AND brgbsn.kd_brg <= ?
            $whereCbg

        GROUP BY brgbsn.kd_brg
        ORDER BY brgbsn.cnt, $order
    ", [
                $sub1, $sub2, $kode1, $kode2,
            ]);
        } elseif ($mode == 'card') {
            $query = DB::select("
        SELECT
            brgbsn.kd_brg,
            brgbsn.cnt,
            brgbsn.ncnt,
            brgbsn.NA_brg,
            brgbsn.barcode,
            '$per' as per,
            '$cbg' as cbg,
            brgbsn.HJUAL,
            brgbsn.tgl_trm,
            brgbsnd.TGL_KSR as tgl_jual,

            SUM(brgbsnd.aw$mon) as aw,
            SUM(brgbsnd.ma$mon) as ma,
            SUM(brgbsnd.ke$mon) as ke,
            SUM(brgbsnd.ln$mon) as ln,
            SUM(brgbsnd.AK$mon) as ak,

            SUM(brgbsnd.AK$mon) * brgbsn.HBELI as tbeli,
            SUM(brgbsnd.AK$mon) * brgbsn.HJUAL as tsisa,

            brgbsn.hbeli,
            brgbsn.hbnet,
            brgbsnd.psn as statpsn,
            CONCAT(brgbsnd.td_od,'-',cat_od) as tdod

        FROM brgbsn
        JOIN $tableDetail brgbsnd ON brgbsn.kd_brg = brgbsnd.kd_brg

        WHERE
            brgbsn.cnt >= ? AND brgbsn.cnt <= ?
            AND brgbsn.kd_brg >= ? AND brgbsn.kd_brg <= ?
            $whereCbg

        GROUP BY brgbsn.kd_brg
        ORDER BY brgbsn.cnt, $order
    ", [
                $sub1, $sub2, $kode1, $kode2,
            ]);
        }
        if ($request->has('filter')) {
            $cbgList = Cbg::groupBy('CBG')->get();
            $perList = Perid::all();

            return view('oreport_surats.report', [
                'cbg'   => $cbgList,
                'per'   => $perList,
                'hasil' => $hasilArray,
            ]);
        }

        $data = [];
        foreach ($query as $row) {
            $data[] = [
                'KD_BRG' => $row->kd_brg,
                'NA_BRG' => $row->NA_brg,
                'QTY'    => $row->ak,
                'AW'     => $row->aw,
                'MA'     => $row->ma,
                'KE'     => $row->ke,
                'LN'     => $row->ln,
                'TBELI'  => $row->tbeli,
                'TSISA'  => $row->tsisa,
                'CBG'    => $row->cbg,
                'PER'    => $row->per,
            ];
        }

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

}
