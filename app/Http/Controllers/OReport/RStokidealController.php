<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use Carbon\Carbon;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RStokidealController extends Controller
{
    public function report()
    {	
        return view('oreport_stokideal.report');
    }

	public function proses(Request $request)
	{
		DB::beginTransaction();

		try {

			$tglAwal = Carbon::now()->subMonths(3)->format('Y-m-d');
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

			return back()->with('error', 'Proses gagal! '.$e->getMessage());
		}
	}
}
