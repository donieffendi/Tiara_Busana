<?php
namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

class RStokidealController extends Controller
{
    public function report()
    {
        return view('oreport_stokideal.report');
    }

    public function proses2(Request $request)
    {
        DB::beginTransaction();

        try {

            $tglAwal  = Carbon::now()->subMonths(3)->format('Y-m-d');
            $tglAkhir = Carbon::now()->format('Y-m-d');

            // Reset dulu
            DB::table('nwmasbar')->update(['IDEAL' => 0]);

            // Update dari penjualan
            DB::statement("
				UPDATE nwmasbar m
				JOIN (
					SELECT d.KD_BRG, SUM(d.QTY) * 2 AS IDEAL
					FROM juald d
					JOIN jual j ON d.NO_BUKTI = j.NO_BUKTI
					WHERE j.TGL BETWEEN ? AND ?
					GROUP BY d.KD_BRG
				) x ON m.KDBAR = x.KD_BRG
				SET m.IDEAL = x.IDEAL
			", [$tglAwal, $tglAkhir]);

            DB::commit();

            return back()->with('success', 'Proses Stock Ideal berhasil!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Proses gagal! ' . $e->getMessage());
        }
    }

    public function proses(Request $request)
    {
        DB::beginTransaction();

        try {

            // QTY
            $kolomQty = [
                "COALESCE(JL_LL,0)",
                "COALESCE(JL_LL2,0)",
                "COALESCE(JL_LL3,0)",
            ];

            // RP
            $kolomRp = [
                "COALESCE(JLRP_LL,0)",
                "COALESCE(JLRP_LL2,0)",
                "COALESCE(JLRP_LL3,0)",
            ];

            $totalQty = implode(' + ', $kolomQty);
            $totalRp  = implode(' + ', $kolomRp);

            $jumlahBulan = count($kolomQty);

            DB::table('nwmasbar')->update([
                'IDEAL'      => 0,
                'JLRATA_QTY' => 0,
                'JLRATA_RP'  => 0,
            ]);

            DB::statement("
            UPDATE nwmasbar
            SET
                JLRATA_QTY = ($totalQty) / $jumlahBulan,
                JLRATA_RP  = ($totalRp) / $jumlahBulan,
                IDEAL      = (($totalQty) / $jumlahBulan) * 2
        ");

            DB::commit();

            return back()->with('success', 'Proses Stock Ideal & Rata-rata berhasil!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Proses gagal! ' . $e->getMessage());
        }
    }

    // public function proses(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         $bulan   = date('m');
    //         $tahun   = date('Y');
    //         $periode = $bulan . '/' . $tahun;

    //         $getKolom = function ($b) {
    //             if ($b <= 0) {
    //                 $b += 12;
    //             }
    //             return 'JLRP_LL' . ($b == 1 ? '' : $b);
    //         };
    //         $koloms = [];
    //         for ($i = 1; $i < $bulan; $i++) {
    //             $koloms[] = "COALESCE(" . $getKolom($bulan - $i) . ",0)";
    //         }

    //         $totalJl     = implode(' + ', $koloms);
    //         $jumlahBulan = count($koloms);

    //         // Reset dulu
    //         DB::table('nwmasbar')->update(['IDEAL' => 0]);

    //         if ($jumlahBulan > 0) {
    //             DB::statement("
    //             UPDATE nwmasbar
    //             SET IDEAL = (($totalJl) / $jumlahBulan) * 2
    //         ");
    //         }

    //         DB::commit();

    //         return back()->with('success', 'Proses Stock Ideal berhasil!');

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()->with('error', 'Proses gagal! ' . $e->getMessage());
    //     }
    // }
}
