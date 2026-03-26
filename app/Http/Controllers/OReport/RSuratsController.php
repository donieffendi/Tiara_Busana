<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
// ganti 1
use App\Models\Master\Perid;
use DB;
use Carbon\Carbon;
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

        return view('oreport_surats.report')->with(['cbg' => $cbg, 'per' => $per])->with(['hasil' => [], 'hasil2' => []]);

    }

    public function jasperSuratsReport(Request $request)
    {
        $file         = 'stock_barang_1';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $per   = $request->per;
        $cbg   = $request->cbg;
        $sub1  = $request->sub1;
        $sub2  = $request->sub2;
        $kode1 = $request->kode1;
        $kode2 = $request->kode2;
        $urut  = $request->urut;
        $kode_card = $request->kode_card;
        $tgl_card = $request->tgl_card;
        $tgl = Carbon::now();
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
              $query2 = DB::select("SELECT *,
       @AKHIR := @AKHIR + AWAL + MASUK - KELUAR + LAIN AS SALDO
FROM (

    SELECT 'Saldo Awal' AS no_bukti, NULL AS TGL, kd_brg, NA_BRG,
           aw$mon AS awal, 0 AS masuk, 0 AS keluar, 0 AS lain, 'AW' AS flag, 0 AS urt
    FROM brgbsnd
    WHERE yer='2025' AND cbg='GZ' AND aw$mon<>0

    UNION ALL

    -- BELI (BS)
    SELECT belibsnz.no_bukti, belibsnz.TGL, belibsnzd.KD_BRG, belibsnzd.NA_BRG,
           0, belibsnzd.qty, 0, 0, belibsnz.FLAG, 1
    FROM belibsnz
    JOIN belibsnzd ON belibsnz.NO_BUKTI=belibsnzd.NO_BUKTI
    WHERE belibsnz.CBG='GZ' AND belibsnz.PER='12/2025'
          AND belibsnz.flag='BS' AND belibsnzd.qty<>0

    UNION ALL

    -- RETUR BELI (RX)
    SELECT belibsnz.no_bukti, belibsnz.TGL, belibsnzd.KD_BRG, belibsnzd.NA_BRG,
           0, belibsnzd.qty * -1, 0, 0, belibsnz.FLAG, 2
    FROM belibsnz
    JOIN belibsnzd ON belibsnz.NO_BUKTI=belibsnzd.NO_BUKTI
    WHERE belibsnz.CBG='GZ' AND belibsnz.PER='12/2025'
          AND belibsnz.flag='RX' AND belibsnzd.qty<>0

    UNION ALL

    -- BONUS (BO)
    SELECT belibsnz.no_bukti, belibsnz.TGL, belibsnzd.KD_BRG, belibsnzd.NA_BRG,
           0, belibsnzd.qty, 0, 0, belibsnz.FLAG, 1
    FROM belibsnz
    JOIN belibsnzd ON belibsnz.NO_BUKTI=belibsnzd.NO_BUKTI
    WHERE belibsnz.CBG='GZ' AND belibsnz.PER='12/2025'
          AND belibsnz.flag='BO' AND belibsnzd.qty<>0

    UNION ALL

    -- KOREKSI KELUAR (KO)
    SELECT bstockaz.no_bukti, bstockaz.TGL, bstockazd.KD_BRG, bstockazd.NA_BRG,
           0, 0, 0, bstockazd.qty * -1, bstockaz.FLAG, 4
    FROM bstockaz
    JOIN bstockazd ON bstockaz.NO_BUKTI=bstockazd.NO_BUKTI
    WHERE bstockaz.PER='12/2025' AND bstockaz.cbg='GZ'
          AND bstockaz.flag='KO' AND bstockazd.qty<>0

    UNION ALL

    -- KOREKSI MASUK (KB)
    SELECT bstockbz.no_bukti, bstockbz.TGL, bstockbzd.KD_BRG, bstockbzd.NA_BRG,
           0, 0, 0, bstockbzd.qty, bstockbz.FLAG, 3
    FROM bstockbz
    JOIN bstockbzd ON bstockbz.NO_BUKTI=bstockbzd.NO_BUKTI
    WHERE bstockbz.PER='12/2025' AND bstockbz.cbg='GZ'
          AND bstockbz.flag='KB' AND bstockbzd.qty<>0

    UNION ALL

    -- RETUR OUT (RO)
    SELECT bretur.no_bukti, bretur.TGL, breturd.KD_BRG, breturd.NA_BRG,
           0, 0, 0, breturd.qty * -1, bretur.FLAG, 5
    FROM bretur
    JOIN breturd ON bretur.NO_BUKTI=breturd.NO_BUKTI
    WHERE bretur.CBG='GZ' AND bretur.PER='12/2025'
          AND bretur.flag='RO' AND bretur.posted=1
          AND breturd.qty<>0

    UNION ALL

    -- RETUR MASUK (RM)
    SELECT bretur.no_bukti, bretur.TGL, breturd.KD_BRG, breturd.NA_BRG,
           0, 0, 0, breturd.qty, bretur.FLAG, 5
    FROM bretur
    JOIN breturd ON bretur.NO_BUKTI=breturd.NO_BUKTI
    WHERE bretur.CBG='GZ' AND bretur.PER='12/2025'
          AND bretur.flag='RM' AND bretur.posted=1
          AND breturd.qty<>0

    UNION ALL

    -- PENJUALAN
    SELECT no_bukti, DATE(TGL), KD_BRG, NA_BRG,
           0, 0, QTY, 0, FLAG, 6
    FROM juald09
    WHERE cbg='GZ' AND PER='12/2025'
          AND FLAG='JL' AND JNS='BSN'

) AS AA
JOIN (SELECT @AKHIR := 0) AS BB ON 1=1
ORDER BY KD_BRG, TGL, URT");
        }

        session()->put('sub1', $request->sub1);
        session()->put('sub2', $request->sub2);
        session()->put('kode1', $request->kode1);
        session()->put('kode2', $request->kode2);
         session()->put('cbg', $request->cbg);

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
        foreach ($query as $row) {
            $data[] = [
                'KD_BRG' => $row->KD_BRG,
                'NA_BRG' => $row->NA_BRG,
                'QTY'    => $row->ak,
                'AW'     => $row->AW,
                'MA'     => $row->MA,
                'KE'     => $row->KE,
                'LN'     => $row->LN,
                'TBELI'  => $row->tbeli,
                'tsisa'  => $row->tsisa,
                'CBG'    => $row->CBG,
                'PER'    => $row->PER,
                'TGL_TRM'    => $row->TGL_TRM,
                'TGL_JUAL'    => $row->TGL_JUAL,
                'CNT'    => $row->CNT,
                'NCNT'    => $row->NCNT,
            ];
        }
        $PHPJasperXML->arrayParameter = [
            "TGL" => $tgl
        ];

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

}



