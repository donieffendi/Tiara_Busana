<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
// ganti 1
use App\Models\Master\Perid;
use Carbon\Carbon;
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
        $cbg   = Cbg::groupBy('CBG')->get();
        $tahun = Carbon::now()->format('Y');

        $per = Perid::where('PERIO', 'like', "%/2026")
            ->orderBy('PERIO')
            ->get();
        session()->put('filter_cbg', '');
        session()->put('filter_sup', '');
        session()->put('filter_sup2', 'ZZZ');

        return view('oreport_surats.report')->with(['cbg' => $cbg, 'per' => $per])->with(['hasil' => [], 'hasil2' => []]);

    }

    public function jasperSuratsReport(Request $request)

    {

        $file         = 'stock_barang_1';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $per       = $request->per;
        $cbg       = $request->cbg;
        $sub1      = $request->sub1;
        $sub2      = $request->sub2;
        $kode1     = $request->kode1;
        $kode2     = $request->kode2;
        $urut      = $request->urut;
        $kode_card = $request->kode_card;
        $tgl_card  = $request->tgl_card;
        $tgl       = Carbon::now()->format('d/m/Y');
        $mode      = $request->mode ?? 'periode';

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
            brgbsn.kd_brg AS KD_BRG,
            brgbsn.cnt AS CNT,
            brgbsn.ncnt AS NCNT,
            brgbsn.NA_brg AS NA_BRG,
            brgbsn.barcode AS BARCODE,
            '$per' as PER,
            '$cbg' as CBG,
            brgbsn.HJUAL,
            brgbsn.tgl_trm AS TGL_TRM,
            brgbsnd.TGL_KSR as TGL_JUAL,

            CAST(SUM(brgbsnd.aw$mon) AS DECIMAL(15,3)) as AW,
            CAST(SUM(brgbsnd.ma$mon) AS DECIMAL(15,3)) as MA,
            CAST(SUM(brgbsnd.ke$mon) AS DECIMAL(15,3)) as KE,
            CAST(SUM(brgbsnd.ln$mon) AS DECIMAL(15,3)) as LN,
            CAST(SUM(brgbsnd.AK$mon) AS DECIMAL(15,3)) as AK,
            CAST(SUM(brgbsnd.AK$mon) AS DECIMAL(15,3)) * brgbsn.HBELI as tbeli,
            CAST(SUM(brgbsnd.AK$mon) AS DECIMAL(15,3)) * brgbsn.HJUAL as tsisa,

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
            $query2 = DB::select(" SELECT *, @AKHIR := @AKHIR + AWAL + MASUK - KELUAR + LAIN AS SALDO
                FROM (
                    SELECT 'Saldo Awal' AS no_bukti, '$tgl_card' AS tgl, kd_brg, NA_BRG,
                        aw$mon AS awal, 0 AS masuk, 0 AS keluar, 0 AS lain, 'AW' AS flag, 0 AS urt
                    FROM brgbsnd
                    WHERE yer = '$tahun' AND kd_brg = '$kode_card' AND cbg = '$cbg' AND aw$mon <> 0

                    UNION ALL

                    SELECT b.no_bukti, b.tgl, d.kd_brg, d.na_brg,
                        0, d.qty, 0, 0, b.flag, 1
                    FROM belibsnz b
                    JOIN belibsnzd d ON b.no_bukti = d.no_bukti
                    WHERE b.cbg = '$cbg' AND b.per = '$per' AND b.flag = 'BS'
                        AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT b.no_bukti, b.tgl, d.kd_brg, d.na_brg,
                        0, d.qty * -1, 0, 0, b.flag, 2
                    FROM belibsnz b
                    JOIN belibsnzd d ON b.no_bukti = d.no_bukti
                    WHERE b.cbg = '$cbg' AND b.per = '$per' AND b.flag = 'RX'
                        AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT b.no_bukti, b.tgl, d.kd_brg, d.na_brg,
                        0, d.qty, 0, 0, b.flag, 1
                    FROM belibsnz b
                    JOIN belibsnzd d ON b.no_bukti = d.no_bukti
                    WHERE b.cbg = '$cbg' AND b.per = '$per' AND b.flag = 'BO'
                        AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT s.no_bukti, s.tgl, d.kd_brg, d.na_brg,
                        0, 0, 0, d.qty * -1, s.flag, 4
                    FROM bstockaz s
                    JOIN bstockazd d ON s.no_bukti = d.no_bukti
                    WHERE s.per = '$per' AND s.cbg = '$cbg' AND s.flag = 'KO'
                        AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT s.no_bukti, s.tgl, d.kd_brg, d.na_brg,
                        0, 0, 0, d.qty, s.flag, 3
                    FROM bstockbz s
                    JOIN bstockbzd d ON s.no_bukti = d.no_bukti
                    WHERE s.per = '$per' AND s.cbg = '$cbg' AND s.flag = 'KB'
                        AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT r.no_bukti, r.tgl, d.kd_brg, d.na_brg,
                        0, 0, 0, d.qty * -1, r.flag, 5
                    FROM bretur r
                    JOIN breturd d ON r.no_bukti = d.no_bukti
                    WHERE r.cbg = '$cbg' AND r.per = '$per' AND r.flag = 'RO'
                        AND r.posted = 1 AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT r.no_bukti, r.tgl, d.kd_brg, d.na_brg,
                        0, 0, 0, d.qty, r.flag, 5
                    FROM bretur r
                    JOIN breturd d ON r.no_bukti = d.no_bukti
                    WHERE r.cbg = '$cbg' AND r.per = '$per' AND r.flag = 'RM'
                        AND r.posted = 1 AND d.kd_brg = '$kode_card' AND d.qty <> 0

                    UNION ALL

                    SELECT no_bukti, DATE(tgl), kd_brg, na_brg,
                        0, 0, qty, 0, flag, 3
                    FROM {$cbg}.juald$mon
                    WHERE cbg = '$cbg' AND per = '$per' AND flag = 'JL'
                        AND jns = 'BSN' AND kd_brg = '$kode_card'

                ) AS AA
                JOIN (SELECT @AKHIR := 0) AS BB
                ORDER BY kd_brg, tgl, urt
            ");
        }
        // dd($cbg, $per, $$kode_card, $mon);
        // dd($query2);

        session()->put('sub1', $request->sub1);
        session()->put('sub2', $request->sub2);
        session()->put('kode1', $request->kode1);
        session()->put('kode2', $request->kode2);
        session()->put('kode_card', $request->kode_card);
        session()->put('cbg', $request->cbg);
        session()->put('filter_periode', $request->per);

        if ($request->has('filter')) {

            $hasil = collect($query)->map(function ($row) {
                $row = (array) $row;

                return [
                    'CNT'      => $row['CNT'] ?? $row['cnt'] ?? null,
                    'KD_BRG'   => $row['KD_BRG'] ?? $row['kd_brg'] ?? null,
                    'BARCODE'  => $row['BARCODE'] ?? $row['barcode'] ?? null,
                    'NA_BRG'   => $row['NA_BRG'] ?? $row['na_brg'] ?? null,
                    'TGL_TRM'  => $row['TGL_TRM'] ?? $row['tgl_trm'] ?? null,
                    'TGL_JUAL' => $row['TGL_JUAL'] ?? $row['tgl_jual'] ?? null,

                    'AW'       => (float) ($row['AW'] ?? $row['aw'] ?? 0),
                    'MA'       => (float) ($row['MA'] ?? $row['ma'] ?? 0),
                    'KE'       => (float) ($row['KE'] ?? $row['ke'] ?? 0),
                    'LN'       => (float) ($row['LN'] ?? $row['ln'] ?? 0),
                    'AK'       => (float) ($row['AK'] ?? $row['ak'] ?? 0),
                ];
            });

            $hasil2 = collect($query2)->map(function ($row) {
                $row = (array) $row;

                return [
                    'kd_brg'   => $row['kd_brg'] ?? $row['kd_brg'] ?? null,
                    'NA_BRG'   => $row['NA_BRG'] ?? $row['na_brg'] ?? null,
                    'tgl'  => $row['tgl'] ?? $row['tgl'] ?? null,
                    'no_bukti' => $row['no_bukti'] ?? $row['no_bukti'] ?? null,

                    'awal'       => (float) ($row['awal'] ?? $row['awal'] ?? 0),
                    'masuk'       => (float) ($row['masuk'] ?? $row['masuk'] ?? 0),
                    'keluar'       => (float) ($row['keluar'] ?? $row['keluar'] ?? 0),
                    'lain'       => (float) ($row['lain'] ?? $row['lain'] ?? 0),
                    'SALDO'       => (float) ($row['SALDO'] ?? $row['SALDO'] ?? 0),
                ];
            });

            $cbgList = Cbg::groupBy('CBG')->get();
            $perList = Perid::all();

            return view('oreport_surats.report', [
                'cbg'    => $cbgList,
                'per'    => $perList,
                'hasil'  => $hasil,
                'hasil2' => $hasil2,
            ]);
        }

        $data = [];
        if ($mode == 'periode') {

    foreach ($query as $row) {
        $data[] = [
            'KD_BRG'   => $row->KD_BRG,
            'NA_BRG'   => $row->NA_BRG,
            'QTY'      => $row->AK,
            'AW'       => $row->AW,
            'MA'       => $row->MA,
            'KE'       => $row->KE,
            'LN'       => $row->LN,
            'TBELI'    => $row->tbeli,
            'tsisa'    => $row->tsisa,
            'CBG'      => $row->CBG,
            'PER'      => $row->PER,
            'TGL_TRM'  => $row->TGL_TRM,
            'TGL_JUAL' => $row->TGL_JUAL,
            'CNT'      => $row->CNT,
            'NCNT'     => $row->NCNT,
        ];
    }

} elseif ($mode == 'card') {

    foreach ($query2 as $row) {
        $data[] = [
            'KD_BRG'   => $row->kd_brg,
            'NA_BRG'   => $row->NA_BRG,
            'TGL'      => $row->tgl,
            'NO_BUKTI' => $row->no_bukti,
            'AWAL'     => $row->awal,
            'MASUK'    => $row->masuk,
            'KELUAR'   => $row->keluar,
            'LAIN'     => $row->lain,
            'SALDO'    => $row->SALDO,
        ];
    }
}
        $PHPJasperXML->arrayParameter = [
            "TGL" => $tgl,
        ];

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

}
