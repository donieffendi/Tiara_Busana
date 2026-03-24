<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use App\Models\Master\Cbg;
use App\Models\Master\Perid;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

class RPenjualanController extends Controller
{
    /**
     * Halaman utama report - Route: /rkasirbantu
     */
    public function report()
    {
        $cbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
		session()->put('filter_cbg', '');
		$per = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
		session()->put('filter_periode', '');
		
		session()->put('filter_CNT', '');
		session()->put('filter_NA_CNT', '');
		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));

        return view('oreport_penjualan.report')->with([
            'cbg' => $cbg,
            'per' => $per,
            'hasilPenjualan' => []
        ]);
    }

    public function getPenjualanReport(Request $request)
    {
        $listCbg = DB::SELECT("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");
        $listPer = DB::select("SELECT * FROM perid WHERE PERIO LIKE CONCAT('%/', YEAR(NOW()))");
        $tab = $request->tab ?? 'detail';

        switch ($tab) {
            case 'detail':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Per Item.'
                    ]);
                }
                $hasilPenjualan = $this->getDetailPenjualan($request->cbg);
            break;
            
            case 'Summary':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Per Tanggal.'
                    ]);
                }
                $hasilPenjualan = $this->getSummaryPenjualan($request->cbg);
            break;

            case 'kasir':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Per Counter.'
                    ]);
                }
                $hasilPenjualan = $this->getKasirList($request->cbg);
            break;

			case 'rconter':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Rekap Per Counter.'
                    ]);
                }
                $hasilPenjualan = $this->getRekapCounter($request->cbg);
            break;

			case 'rjual':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Rekap Penjualan.'
                    ]);
                }
                $hasilPenjualan = $this->getRekapPenjualan($request->cbg);
            break;

			case 'rhari':
                if (empty($request->cbg)) {
                    return view('oreport_penjualan.report')->with([
                        'cbg' => $listCbg,
                        'hasilPenjualan' => [],
                        'error' => 'Cabang harus dipilih untuk tab Rekap Harian.'
                    ]);
                }
                $hasilPenjualan = $this->getRekapHarian($request->cbg);
            break;
        }

        return view('oreport_penjualan.report')->with([
            'cbg' => $listCbg,
            'hasilPenjualan' => $hasilPenjualan,
            'tab' => $tab
        ]);
    }

    public function getPenjualanReportAjax(Request $request)
	{
		$tab = $request->tab ?? 'detail';
		$cbg = $request->cbg ?? '';
		$cnt = $request->CNT;
		$tgl1 = date('Y-m-d',strtotime($request->tglDr));
		$tgl2 = date('Y-m-d',strtotime($request->tglSmp));

		$periode = $request->per;
		$bulan = substr($periode,0,2);

		$kode1 = $request->CNT1;
		$kode2 = $request->CNT2;

		switch ($tab) {
			case 'detail':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Per Item.'
					], 400);
				}
				$data = $this->getDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan);
			break;

			case 'summary':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Per Tanggal.'
					], 400);
				}
				$data = $this->getSummaryPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan);
			break;

			case 'summary_detail':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih.'
					], 400);
				}
				$data = $this->getSummaryDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode);
			break;

			case 'kasir':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Per Counter.'
					], 400);
				}
				$data = $this->getKasirList($cbg, $periode, $kode1, $kode2);
			break;

			case 'rconter':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Rekap Counter.'
					], 400);
				}
				$data = $this->getRekapCounter($cbg, $periode, $cnt);
			break;

			case 'rjual':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Rekap Counter.'
					], 400);
				}
				$data = $this->getRekapPenjualan($periode);
			break;

			case 'rhari':
				if (empty($cbg)) {
					return response()->json([
						'success' => false,
						'message' => 'Cabang harus dipilih untuk tab Rekap Counter.'
					], 400);
				}
				$data = $this->getRekapHarian($cbg, $cnt, $periode, $bulan);
			break;
		}

		return response()->json([
			'success' => true,
			'data' => $data
		]);
	}



    /**
     * Generate laporan Jasper - Route: /jasper-kasirbantu-report
     * Implementasi dari logika Delphi untuk generate report
     */
    public function jasperPenjualanDetailReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_peritem'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$cnt     = $request->cnt;
			$tgl1    = date('Y-m-d', strtotime($request->tgl1));
			$tgl2    = date('Y-m-d', strtotime($request->tgl2));
			$periode = $request->per;
			$bulan = substr($periode,0,2);

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'no_bukti' 		=> $item->no_bukti ?? '',
					'bukti2' 		=> $item->bukti2 ?? '',
					'initanggal'    => $item->initanggal ?? '',
					'cabang'   	    => $item->cabang ?? '',
					'KD_BRG'	    => $item->KD_BRG ?? '',
					'NA_BRG'        => $item->NA_BRG ?? '',
					'qty'      		=> $item->qty ?? 0,
					'harga'    		=> $item->harga ?? 0,
					'total'    		=> $item->total ?? 0,
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperPenjualanSummaryReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_pertgl'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$cnt     = $request->cnt;
			$tgl1    = date('Y-m-d', strtotime($request->tgl1));
			$tgl2    = date('Y-m-d', strtotime($request->tgl2));
			$periode = $request->per;
			$bulan = substr($periode,0,2);

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getSummaryPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'tgmin' 	=> $item->tgmin ?? '',
					'tgmax' 	=> $item->tgmax ?? '',
					'cnt'    	=> $item->cnt ?? '',
					'na_cnt'   	=> $item->na_cnt ?? '',
					'st_pjk'	=> $item->st_pjk ?? '',
					'tgl_jual'  => $item->tgl_jual ?? '',
					'qcash'     => $item->qcash ?? 0,
					'qkred'    	=> $item->qkred ?? 0,
					'qjml'    	=> $item->qjml ?? 0,
					'cash'    	=> $item->cash ?? 0,
					'kred'    	=> $item->kred ?? 0,
					'jml'    	=> $item->jml ?? 0,
					'qtyrf'    	=> $item->qtyrf ?? 0,
					'totalrf'   => $item->totalrf ?? 0
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperPenjualanSummaryDetailReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_detail'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$cnt     = $request->cnt;
			$tgl1    = date('Y-m-d', strtotime($request->tgl1));
			$tgl2    = date('Y-m-d', strtotime($request->tgl2));
			$periode = $request->per;
			$bulan = substr($periode,0,2);

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getSummaryDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'tgmin' 	=> $item->tgmin ?? '',
					'tgmax' 	=> $item->tgmax ?? '',
					'cnt'    	=> $item->cnt ?? '',
					'na_cnt'   	=> $item->na_cnt ?? '',
					'st_pjk'	=> $item->st_pjk ?? '',
					'tgl_jual'  => $item->tgl_jual ?? '',
					'qcash'     => $item->qcash ?? 0,
					'qkred'    	=> $item->qkred ?? 0,
					'qjml'    	=> $item->qjml ?? 0,
					'cash'    	=> $item->cash ?? 0,
					'kred'    	=> $item->kred ?? 0,
					'jml'    	=> $item->jml ?? 0,
					'qtyrf'    	=> $item->qtyrf ?? 0,
					'totalrf'   => $item->totalrf ?? 0
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperPenjualanReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_cnt'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$kode1   = $request->cnt1;
			$kode2   = $request->cnt2;
			$periode = $request->per;

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getKasirList($cbg, $periode, $kode1, $kode2);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'PER' 			=> $item->PER ?? '',
					'tgl_jual' 		=> $item->tgl_jual ?? '',
					'cnt' 			=> $item->cnt ?? '',
					'na_cnt'    	=> $item->na_cnt ?? '',
					'kodes'   	    => $item->kodes ?? '',
					'namas'	   		=> $item->namas ?? '',
					'qty'      		=> $item->qty ?? 0,
					'tharga'    	=> $item->tharga ?? 0,
					'dis'    		=> $item->dis ?? 0,
					'par'    		=> $item->par ?? 0,
					'ptiara'    	=> $item->ptiara ?? 0,
					'psup'    		=> $item->psup ?? 0,
					'nilai_jual'    => $item->nilai_jual ?? 0,
					'nilai_margin'  => $item->nilai_margin ?? 0,
					'nilai_nota'  	=> $item->nilai_nota ?? 0,
					'ppn'  			=> $item->ppn ?? 0,
					'nett'  		=> $item->nett ?? 0,
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperSpbsnReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_spbsn'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$kode1   = $request->cnt1;
			$kode2   = $request->cnt2;
			$periode = $request->per;

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getKasirListsp($cbg, $periode, $kode1, $kode2);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'cnt' 			=> $item->cnt ?? '',
					'na_cnt'    	=> $item->na_cnt ?? '',
					'kodes'   	    => $item->kodes ?? '',
					'namas'	   		=> $item->namas ?? '',
					'p_almt'	   	=> $item->p_almt ?? '',
					'p_kota'	   	=> $item->p_kota ?? '',
					'p_tlp'	   		=> $item->p_tlp ?? '',
					'ST_PJK'	   	=> $item->ST_PJK ?? '',
					'p_zona'	   	=> $item->p_zona ?? '',
					'p_fax'	   		=> $item->p_fax ?? '',
					'TGL1'	   		=> $item->TGL1 ?? '',
					'TGL2'	   		=> $item->TGL2 ?? '',
					'qty'      		=> $item->qty ?? 0,
					'TOTAL'    		=> $item->TOTAL ?? 0
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperCounterReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_rekap'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$periode = $request->per;
			$cnt     = $request->cnt;

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getRekapCounter($cbg, $periode, $cnt);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'PER' 			=> $item->PER ?? '',
					'NO_FORM' 		=> $item->NO_FORM ?? '',
					'tgl_jual' 		=> $item->tgl_jual ?? '',
					'cnt' 			=> $item->cnt ?? '',
					'na_cnt'    	=> $item->na_cnt ?? '',
					'kodes'   	    => $item->kodes ?? '',
					'namas'	   		=> $item->namas ?? '',
					'qty'      		=> $item->qty ?? 0,
					'tharga'    	=> $item->tharga ?? 0,
					'dis'    		=> $item->dis ?? 0,
					'par'    		=> $item->par ?? 0,
					'ptiara'    	=> $item->ptiara ?? 0,
					'psup'    		=> $item->psup ?? 0,
					'TDISKONVIP'    => $item->TDISKONVIP ?? 0,
					'nilai_margin'  => $item->nilai_margin ?? 0,
					'PPN'  			=> $item->PPN ?? 0,
					'DPP'  			=> $item->DPP ?? 0,
					'TOTAL'  		=> $item->TOTAL ?? 0,
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperJualReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_jual'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$periode = $request->per;
			$cbg = $request->cbg;

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getRekapPenjualan($periode);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'YER' 			=> $item->YER ?? '',
					'KEL_PT' 		=> $item->KEL_PT ?? '',
					'CNT' 			=> $item->CNT ?? '',
					'ST_CNT'    	=> $item->ST_CNT ?? '',
					'B01'      		=> $item->B01 ?? 0,
					'B02'    		=> $item->B02 ?? 0,
					'B03'    		=> $item->B03 ?? 0,
					'B04'    		=> $item->B04 ?? 0,
					'B05'    		=> $item->B05 ?? 0,
					'B06'    		=> $item->B06 ?? 0,
					'B07'    		=> $item->B07 ?? 0,
					'B08'  			=> $item->B08 ?? 0,
					'B09'  			=> $item->B09 ?? 0,
					'B10'  			=> $item->B10 ?? 0,
					'B11'  			=> $item->B11 ?? 0,
					'B12'  			=> $item->B12 ?? 0,
					'JUMB'  		=> $item->JUMB ?? 0,
					'P01'      		=> $item->P01 ?? 0,
					'P02'    		=> $item->P02 ?? 0,
					'P03'    		=> $item->P03 ?? 0,
					'P04'    		=> $item->P04 ?? 0,
					'P05'    		=> $item->P05 ?? 0,
					'P06'    		=> $item->P06 ?? 0,
					'P07'    		=> $item->P07 ?? 0,
					'P08'  			=> $item->P08 ?? 0,
					'P09'  			=> $item->P09 ?? 0,
					'P10'  			=> $item->P10 ?? 0,
					'P11'  			=> $item->P11 ?? 0,
					'P12'  			=> $item->P12 ?? 0,
					'JUMP'  		=> $item->JUMP ?? 0,
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	public function jasperOmzetReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_omzet'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg     = $request->cbg;
			$cnt     = $request->cnt;
			$tgl1    = date('Y-m-d', strtotime($request->tgl1));
			$tgl2    = date('Y-m-d', strtotime($request->tgl2));
			$periode = $request->per;
			$bulan = substr($periode,0,2);

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getOmzetPenjualan($tgl1, $tgl2, $periode);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'YER' 			=> $item->YER ?? '',
					'KEL_PT' 		=> $item->KEL_PT ?? '',
					'CNT' 			=> $item->CNT ?? '',
					'ST_CNT'    	=> $item->ST_CNT ?? '',
					'B01'      		=> $item->B01 ?? 0,
					'B02'    		=> $item->B02 ?? 0,
					'B03'    		=> $item->B03 ?? 0,
					'B04'    		=> $item->B04 ?? 0,
					'B05'    		=> $item->B05 ?? 0,
					'B06'    		=> $item->B06 ?? 0,
					'B07'    		=> $item->B07 ?? 0,
					'B08'  			=> $item->B08 ?? 0,
					'B09'  			=> $item->B09 ?? 0,
					'B10'  			=> $item->B10 ?? 0,
					'B11'  			=> $item->B11 ?? 0,
					'B12'  			=> $item->B12 ?? 0,
					'JUMB'  		=> $item->JUMB ?? 0,
					'P01'      		=> $item->P01 ?? 0,
					'P02'    		=> $item->P02 ?? 0,
					'P03'    		=> $item->P03 ?? 0,
					'P04'    		=> $item->P04 ?? 0,
					'P05'    		=> $item->P05 ?? 0,
					'P06'    		=> $item->P06 ?? 0,
					'P07'    		=> $item->P07 ?? 0,
					'P08'  			=> $item->P08 ?? 0,
					'P09'  			=> $item->P09 ?? 0,
					'P10'  			=> $item->P10 ?? 0,
					'P11'  			=> $item->P11 ?? 0,
					'P12'  			=> $item->P12 ?? 0,
					'JUMP'  		=> $item->JUMP ?? 0,
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}
	
	public function jasperHariReport(Request $request)
	{
		try {

			if (empty($request->cbg)) {
				return response()->json(['error' => 'Cabang harus dipilih.'], 400);
			}

			$file = 'rpenjualan_hari'; 
			$PHPJasperXML = new PHPJasperXML();
			$PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

			// ===========================
			// Ambil parameter
			// ===========================
			$cbg = $request->cbg;
			$cnt = $request->cnt;
			$periode = $request->per;
			$bulan = substr($periode,0,2);

			// ===========================
			// PAKAI METHOD YANG SUDAH ADA
			// ===========================
			$rows = $this->getRekapHarian($cbg, $cnt, $periode, $bulan);

			// ===========================
			// Mapping ke Jasper
			// ===========================
			$data = array_map(function ($item) {
				return [
					'PER' 			=> $item->per ?? '',
					'kode' 			=> $item->kode ?? '',
					'CNT' 			=> $item->CNT ?? '',
					'ST_CNT'    	=> $item->ST_CNT ?? '',
					'b01'      		=> $item->b01 ?? 0,
					'b02'    		=> $item->b02 ?? 0,
					'b03'    		=> $item->b03 ?? 0,
					'b04'    		=> $item->b04 ?? 0,
					'b05'    		=> $item->b05 ?? 0,
					'b06'    		=> $item->b06 ?? 0,
					'b07'    		=> $item->b07 ?? 0,
					'b08'  			=> $item->b08 ?? 0,
					'b09'  			=> $item->b09 ?? 0,
					'b10'  			=> $item->b10 ?? 0,
					'b11'  			=> $item->b11 ?? 0,
					'b12'  			=> $item->b12 ?? 0,
					'b13'  			=> $item->b13 ?? 0,
					'b14'      		=> $item->b14 ?? 0,
					'b15'    		=> $item->b15 ?? 0,
					'b16'    		=> $item->b16 ?? 0,
					'b17'    		=> $item->b17 ?? 0,
					'b18'    		=> $item->b18 ?? 0,
					'b19'    		=> $item->b19 ?? 0,
					'b20'    		=> $item->b20 ?? 0,
					'b21'  			=> $item->b21 ?? 0,
					'b22'  			=> $item->b22 ?? 0,
					'b23'  			=> $item->b23 ?? 0,
					'b24'  			=> $item->b24 ?? 0,
					'b25'  			=> $item->b25 ?? 0,
					'b26'  			=> $item->b26 ?? 0,
					'b27'  			=> $item->b27 ?? 0,
					'b28'  			=> $item->b28 ?? 0,
					'b29'  			=> $item->b29 ?? 0,
					'b30'  			=> $item->b30 ?? 0,
					'b31'  			=> $item->b31 ?? 0,
					'hjual' 		=> $item->hjual ?? 0,
					'total' 		=> $item->total ?? 0
				];
			}, $rows);

			$PHPJasperXML->setData($data);

			$PHPJasperXML->arrayParameter = [
				"TGL_CTK" => date('d/m/Y')
			];

			ob_end_clean();
			$PHPJasperXML->outpage("I");

		} catch (\Exception $e) {
			Log::error('Error Jasper Detail: ' . $e->getMessage());
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

    private function getDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan)
    {	
		$cabangx = strtolower($cbg);

        if($cbg != 'ALL')
		{

			$data = DB::select("
				SELECT B.*, A.tg_smp, A.KSR,
					A.tgl as initanggal,
					A.usrnm,
					A.CBG as cabang,
					1 as posted
				FROM {$cabangx}.jual{$bulan} A
				JOIN {$cabangx}.juald{$bulan} B
					ON A.no_bukti=B.no_bukti
				WHERE A.flag='JL'
				AND length(B.kd_brg)>7
				AND A.cbg=?
				AND (?='' OR left(B.kd_brg,5)=?)
				AND A.tgl BETWEEN ? AND ?
				ORDER BY A.tg_smp,B.KD_BRG,A.ksr,A.no_bukti,B.bukti2
			",[$cbg,$cnt,$cnt,$tgl1,$tgl2]);

		}
		else
		{

			$cabang = DB::table('toko')->get();
			$sql = "";

			foreach($cabang as $i=>$c)
			{

				$query = "
				SELECT juald{$bulan}.*, jual{$bulan}.tg_smp,
					jual{$bulan}.KSR,
					jual{$bulan}.tgl as initanggal,
					jual{$bulan}.usrnm,
					'ALL' as cabang,
					1 as posted
				FROM {$c->KODE}.juald{$bulan}
				JOIN {$c->KODE}.jual{$bulan}
					ON juald{$bulan}.no_bukti=jual{$bulan}.no_bukti
				WHERE jual{$bulan}.flag='JL'
				AND length(juald{$bulan}.kd_brg)>7
				AND jual{$bulan}.cbg='{$c->KODE}'
				AND jual{$bulan}.tgl BETWEEN '$tgl1' AND '$tgl2'
				";

				if($i==0)
					$sql = $query;
				else
					$sql .= " UNION ALL ".$query;

			}

			$sql .= " ORDER BY KD_BRG,ksr,no_bukti,tg_smp,bukti2";

			$data = DB::select($sql);
		}

		return $data;
    }


    private function getSummaryPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode, $bulan)
    {
        $cabangx = strtolower($cbg);

        if($cbg == 'ALL')
		{
			$data = DB::select("SELECT 'ALL' as cbg, cnt, na_cnt, '$tgl1' as tgmin, '$tgl2' as tgmax, st_pjk,
										tgl_jual,round(sum(qcash))  as qcash,0 as qkred, round(sum(qjml)) as qjml,
										round(sum(cash)) as cash,0 as kred,round(sum(jml)) as jml,
										round(sum(qtyrf)) as qtyrf,round(sum(totalrf)) as totalrf  from  (
								select brgbsn.cnt, brgbsn.ncnt as na_cnt, brgbsn.st_pjk,
								jual$bulan.tgl as tgl_jual,  ROUND(sum(juald$bulan.qty)) as qcash,0 as qkred, ROUND(sum(juald$bulan.qty)) as qjml,
								ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as cash,0 as kred,ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as jml,
								round(sum(if(juald$bulan.qty<0 and juald$bulan.type='RF' ,juald$bulan.qty,0))) as qtyrf,ROUND(sum(if(juald$bulan.total<0 and juald$bulan.type='RF',juald$bulan.QTY*juald$bulan.harga,0))) as totalrf
								from TGZ.jual$bulan ,TGZ.juald$bulan,brgbsn
								where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
								and jual$bulan.flag='JL' AND jual$bulan.tgl between '$tgl1' and '$tgl2'  AND juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.cnt,jual$bulan.tgl
								union all  select brgbsn.cnt, brgbsn.ncnt as na_cnt, brgbsn.st_pjk,
								jual$bulan.tgl as tgl_jual,  ROUND(sum(juald$bulan.qty)) as qcash,0 as qkred, ROUND(sum(juald$bulan.qty)) as qjml,
								ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as cash,0 as kred,ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as jml,
								round(sum(if(juald$bulan.qty<0 and juald$bulan.type='RF' ,juald$bulan.qty,0))) as qtyrf,ROUND(sum(if(juald$bulan.total<0 and juald$bulan.type='RF',juald$bulan.QTY*juald$bulan.harga,0))) as totalrf
								from sop.jual$bulan ,sop.juald$bulan,brgbsn
								where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
								and jual$bulan.flag='JL' AND jual$bulan.tgl between '$tgl1' and '$tgl2'  AND juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.cnt,jual$bulan.tgl
								union all  select brgbsn.cnt, brgbsn.ncnt as na_cnt, brgbsn.st_pjk,
								jual$bulan.tgl as tgl_jual,  ROUND(sum(juald$bulan.qty)) as qcash,0 as qkred, ROUND(sum(juald$bulan.qty)) as qjml,
								ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as cash,0 as kred,ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as jml,
								round(sum(if(juald$bulan.qty<0 and juald$bulan.type='RF' ,juald$bulan.qty,0))) as qtyrf,ROUND(sum(if(juald$bulan.total<0 and juald$bulan.type='RF',juald$bulan.QTY*juald$bulan.harga,0))) as totalrf
								from tmm.jual$bulan ,tmm.juald$bulan,brgbsn
								where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
								and jual$bulan.flag='JL' AND jual$bulan.tgl between '$tgl1' and '$tgl2'  AND juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.cnt,jual$bulan.tgl
								) as HH GROUP BY CNT,TGL_JUAL ORDER BY  CNT,TGL_JUAL
			");
		} else {
			$sql = "SELECT '$cbg' as cbg,brgbsn.cnt, brgbsn.ncnt as na_cnt, date('$tgl1') as tgmin,date('$tgl2') as tgmax, brgbsn.st_pjk,
							jual$bulan.tgl as tgl_jual,  ROUND(sum(juald$bulan.qty)) as qcash,0 as qkred, ROUND(sum(juald$bulan.qty)) as qjml, 
							ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as cash, 0 as kred,ROUND(sum(juald$bulan.QTY*juald$bulan.harga)) as jml,
							round(sum(if(juald$bulan.qty<0 and juald$bulan.type='RF',juald$bulan.qty,0))) as qtyrf,ROUND(sum(if(juald$bulan.total<0 and juald$bulan.type='RF',juald$bulan.QTY*juald$bulan.harga,0))) as totalrf 
					from {$cabangx}.jual$bulan,{$cabangx}.juald$bulan,brgbsn
					where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7  
					and jual$bulan.flag='JL' AND jual$bulan.tgl between '$tgl1' and '$tgl2' AND juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt'  GROUP BY brgbsn.cnt,jual$bulan.tgl ORDER BY brgbsn.cnt,jual$bulan.tgl";

			$data = DB::select($sql);
		}

		return $data;
    }

	private function getSummaryDetailPenjualan($cbg, $cnt, $tgl1, $tgl2, $periode)
	{
		$bulan   = substr($periode, 0, 2);
		$cabangx = strtolower($cbg);

		if ($cbg == 'ALL') {

			$sql = "
			SELECT 'ALL' as cbg,cnt, na_cnt,date('$tgl1') as tgmin,date('$tgl2') as tgmax, st_pjk,
				tgl_jual,
				ROUND(SUM(qcash))  as qcash,
				0 as qkred,
				ROUND(SUM(qjml))  as qjml,
				ROUND(SUM(cash))  as cash,
				0 as kred,
				ROUND(SUM(jml))   as jml,
				ROUND(SUM(qtyrf)) as qtyrf,
				ROUND(SUM(totalrf)) as totalrf
			FROM (
			
				SELECT brgbsn.cnt, brgbsn.ncnt as na_cnt, brgbsn.st_pjk,
					jual$bulan.tgl as tgl_jual,
					ROUND(SUM(juald$bulan.qty)) as qcash,
					0 as qkred,
					ROUND(SUM(juald$bulan.qty)) as qjml,
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)) as cash,
					0 as kred,
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)) as jml,
					ROUND(SUM(IF(juald$bulan.qty < 0 AND juald$bulan.type='RF', juald$bulan.qty, 0))) as qtyrf,
					ROUND(SUM(IF(juald$bulan.total < 0 AND juald$bulan.type='RF', juald$bulan.qty * juald$bulan.harga, 0))) as totalrf
				FROM TGZ.jual$bulan
				JOIN TGZ.juald$bulan ON jual$bulan.no_bukti = juald$bulan.no_bukti
				JOIN brgbsn ON juald$bulan.KD_BRG = brgbsn.KD_BRG
				WHERE LENGTH(juald$bulan.KD_BRG) > 7
					AND jual$bulan.flag = 'JL'
					AND jual$bulan.tgl BETWEEN '$tgl1' AND '$tgl2'
					AND brgbsn.CNT = '$cnt'
				GROUP BY brgbsn.cnt, jual$bulan.tgl

				UNION ALL

				SELECT brgbsn.cnt, brgbsn.ncnt, brgbsn.st_pjk,
					jual$bulan.tgl,
					ROUND(SUM(juald$bulan.qty)),
					0,
					ROUND(SUM(juald$bulan.qty)),
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)),
					0,
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)),
					ROUND(SUM(IF(juald$bulan.qty < 0 AND juald$bulan.type='RF', juald$bulan.qty, 0))),
					ROUND(SUM(IF(juald$bulan.total < 0 AND juald$bulan.type='RF', juald$bulan.qty * juald$bulan.harga, 0)))
				FROM SOP.jual$bulan
				JOIN SOP.juald$bulan ON jual$bulan.no_bukti = juald$bulan.no_bukti
				JOIN brgbsn ON juald$bulan.KD_BRG = brgbsn.KD_BRG
				WHERE LENGTH(juald$bulan.KD_BRG) > 7
					AND jual$bulan.flag = 'JL'
					AND jual$bulan.tgl BETWEEN '$tgl1' AND '$tgl2'
					AND brgbsn.CNT = '$cnt'
				GROUP BY brgbsn.cnt, jual$bulan.tgl

				UNION ALL

				SELECT brgbsn.cnt, brgbsn.ncnt, brgbsn.st_pjk,
					jual$bulan.tgl,
					ROUND(SUM(juald$bulan.qty)),
					0,
					ROUND(SUM(juald$bulan.qty)),
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)),
					0,
					ROUND(SUM(juald$bulan.qty * juald$bulan.harga)),
					ROUND(SUM(IF(juald$bulan.qty < 0 AND juald$bulan.type='RF', juald$bulan.qty, 0))),
					ROUND(SUM(IF(juald$bulan.total < 0 AND juald$bulan.type='RF', juald$bulan.qty * juald$bulan.harga, 0)))
				FROM TMM.jual$bulan
				JOIN TMM.juald$bulan ON jual$bulan.no_bukti = juald$bulan.no_bukti
				JOIN brgbsn ON juald$bulan.KD_BRG = brgbsn.KD_BRG
				WHERE LENGTH(juald$bulan.KD_BRG) > 7
					AND jual$bulan.flag = 'JL'
					AND jual$bulan.tgl BETWEEN '$tgl1' AND '$tgl2'
					AND brgbsn.CNT = '$cnt'
				GROUP BY brgbsn.cnt, jual$bulan.tgl

			) as HH
			GROUP BY CNT, TGL_JUAL
			ORDER BY CNT, TGL_JUAL
			";

		} else {

			$sql = "
			SELECT '$cbg' as cbg,
				brgbsn.cnt,
				brgbsn.ncnt as na_cnt,
				'$tgl1' as tgmin,
				'$tgl2' as tgmax,
				brgbsn.st_pjk,
				jual$bulan.tgl as tgl_jual,
				ROUND(SUM(juald$bulan.qty)) as qcash,
				0 as qkred,
				ROUND(SUM(juald$bulan.qty)) as qjml,
				ROUND(SUM(juald$bulan.qty * juald$bulan.harga)) as cash,
				0 as kred,
				ROUND(SUM(juald$bulan.qty * juald$bulan.harga)) as jml,
				ROUND(SUM(IF(juald$bulan.qty < 0 AND juald$bulan.type='RF', juald$bulan.qty, 0))) as qtyrf,
				ROUND(SUM(IF(juald$bulan.total < 0 AND juald$bulan.type='RF', juald$bulan.qty * juald$bulan.harga, 0))) as totalrf
			FROM {$cabangx}.jual$bulan
			JOIN {$cabangx}.juald$bulan ON jual$bulan.no_bukti = juald$bulan.no_bukti
			JOIN brgbsn ON juald$bulan.KD_BRG = brgbsn.KD_BRG
			WHERE LENGTH(juald$bulan.KD_BRG) > 7
				AND jual$bulan.flag = 'JL'
				AND jual$bulan.tgl BETWEEN '$tgl1' AND '$tgl2'
				AND brgbsn.CNT = '$cnt'
			GROUP BY brgbsn.cnt, jual$bulan.tgl
			ORDER BY brgbsn.cnt, jual$bulan.tgl
			";
		}

		return DB::select($sql);
	}

    public function getKasirList($cbg, $periode, $kode1, $kode2)
	{
		// dd($periode, $kode1, $kode2);
		// ambil PPN
		$ppnData = DB::select("CALL PPNPER(?)", [$periode]);
		$ppnx = $ppnData[0]->PPN ?? 0;

		// hitung periode seperti Delphi
		$tahun = substr($periode, 2, 4);
		$bulan = substr($periode, 0, 2);
		$perhit = intval($tahun . $bulan);

		// kasus khusus ppn
		if ($perhit > 202503) {
			$kasuskhusus = "ROUND((nilai_nota/(1+$ppnx))*$ppnx) AS ppn";
		} else {
			$kasuskhusus = "IF(ST_PJK='P1', ROUND((nilai_nota/(1+$ppnx))*$ppnx),0) AS ppn";
		}

		$sql = "
			SELECT *,
				IF(ST_PJK='P1', ROUND((nilai_nota/(1+$ppnx))*$ppnx),0) AS ppn,
				(nilai_nota - IF(ST_PJK='P1', ROUND((nilai_nota/(1+$ppnx))*$ppnx),0)) AS nett
			FROM (
				SELECT *,
					'TGZ-PBK1-142.2' AS NO_FORM,
					nilai_jual - nilai_margin + ptiara AS nilai_nota
				FROM (
					SELECT
						rkjdbsn.tgl_jual,
						rkjdbsn.cnt,
						rkjdbsn.ST_PJK,
						rkjdbsn.na_cnt,
						SUM(rkjdbsn.qty) AS qty,
						SUM(rkjdbsn.tharga) AS tharga,
						rkjdbsn.dis,
						rkjdbsn.par,
						SUM(rkjdbsn.ptiara) AS ptiara,
						SUM(rkjdbsn.psup) AS psup,
						SUM(nilai_jual) AS nilai_jual,

						ROUND(SUM(
							IF(cntbsn.ST_CNT='K',
								IF((rkjdbsn.par=0 AND rkjdbsn.dis=0) OR LEFT(cntbsn.NA_CNT,3)='***',
									rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100,
									rkjdbsn.THARGA*rkjdbsn.margin/100
								),
								IF(cntbsn.ST_NOTA='B',
									(rkjdbsn.MARGIN/(rkjdbsn.MARGIN+100))*rkjdbsn.NILAI_JUAL,
									(rkjdbsn.MARGIN/100)*rkjdbsn.NILAI_JUAL
								)
							)
						)) AS nilai_margin,

						rkjdbsn.margin,
						rkjdbsn.PER,
						supbsn.kodes,
						supbsn.namas

					FROM rkjdbsn
					LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
					JOIN cntbsn ON rkjdbsn.CNT = cntbsn.CNT

					WHERE rkjdbsn.per = ?
					AND rkjdbsn.cnt >= ?
					AND rkjdbsn.cnt <= ?

					GROUP BY rkjdbsn.cnt, rkjdbsn.tgl_jual
				) ss1
			) ss2
			ORDER BY cnt, tgl_jual
			";

		return DB::select($sql, [$periode, $kode1, $kode2]);
	}

	public function getKasirListsp($cbg, $periode, $kode1, $kode2)
	{
		// =========================
		// 1. Ambil PPN
		// =========================
		$ppnData = DB::select("CALL PPNPER(?)", [$periode]);
		$ppnx = $ppnData[0]->PPN ?? 0;

		// =========================
		// 2. Query utama (FULL Delphi style)
		// =========================
		$sql = "
			SELECT *,
				'TGZ-PBK1-142.2' AS NO_FORM,

				DATE(CONCAT(RIGHT(?,4),'-',LEFT(?,2),'-01')) AS TGL1,
				LAST_DAY(DATE(CONCAT(RIGHT(?,4),'-',LEFT(?,2),'-01'))) AS TGL2,

				(nilai_jual - nilai_margin + ptiara) AS nilai_nota,

				IF(ST_PJK='P1',
					ROUND((nilai_jual - nilai_margin + ptiara)/(1+?) * ?),
					0
				) AS ppn,

				( (nilai_jual - nilai_margin + ptiara) -
				IF(ST_PJK='P1',
					ROUND((nilai_jual - nilai_margin + ptiara)/(1+?) * ?),
					0
				)
				) AS TOTAL,

				qtyX AS qty

			FROM (
				SELECT 
					rkjdbsn.tgl_jual,
					rkjdbsn.cnt,
					rkjdbsn.ST_PJK,
					rkjdbsn.na_cnt,

					SUM(rkjdbsn.qty) AS qtyX,
					SUM(rkjdbsn.tharga) AS tharga,

					rkjdbsn.dis,
					rkjdbsn.par,

					SUM(rkjdbsn.ptiara) AS ptiara,
					SUM(rkjdbsn.psup) AS psup,
					SUM(rkjdbsn.nilai_jual) AS nilai_jual,

					ROUND(SUM(
						IF(cntbsn.ST_CNT='K',
							IF((rkjdbsn.par=0 AND rkjdbsn.dis=0) 
								OR LEFT(cntbsn.NA_CNT,3)='***',
								rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100,
								rkjdbsn.THARGA*rkjdbsn.margin/100
							),
							IF(cntbsn.ST_NOTA='B',
								(rkjdbsn.MARGIN/(rkjdbsn.MARGIN+100))*rkjdbsn.NILAI_JUAL,
								(rkjdbsn.MARGIN/100)*rkjdbsn.NILAI_JUAL
							)
						)
					)) AS nilai_margin,

					rkjdbsn.margin,
					rkjdbsn.PER,

					supbsn.kodes,
					supbsn.namas,
					supbsn.p_fax,
					supbsn.p_tlp,
					supbsn.p_almt,
					supbsn.zona,
					supbsn.p_kota

				FROM rkjdbsn
				LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
				JOIN cntbsn ON rkjdbsn.CNT = cntbsn.CNT

				WHERE rkjdbsn.per = ?
				AND rkjdbsn.cnt >= ?
				AND rkjdbsn.cnt <= ?

				GROUP BY rkjdbsn.cnt, rkjdbsn.tgl_jual
			) ss

			ORDER BY cnt, tgl_jual
		";

		return DB::select($sql, [
			$periode, $periode, // TGL1
			$periode, $periode, // TGL2
			$ppnx, $ppnx,       // PPN
			$ppnx, $ppnx,       // TOTAL
			$periode,
			$kode1,
			$kode2
		]);
	}

	public function getRekapCounter($cbg, $periode, $cnt)
	{
		// ambil PPN
		$ppnData = DB::select("CALL PPNPER(?)", [$periode]);
		$ppnx = $ppnData[0]->PPN ?? 0;

		$sql = "
		SELECT
			'TGZ-PBK1-142.2' AS NO_FORM,
			rkjdbsn.tgl_jual,
			rkjdbsn.cnt,
			rkjdbsn.na_cnt,
			SUM(rkjdbsn.qty) AS qty,
			SUM(rkjdbsn.tharga) AS tharga,
			rkjdbsn.dis,
			rkjdbsn.par,
			SUM(rkjdbsn.ptiara) AS ptiara,
			SUM(rkjdbsn.psup) AS psup,
			SUM(TDISKONVIP) AS TDISKONVIP,
			SUM(rkjdbsn.NILAI_JUAL) AS nilai_jual,

			ROUND(SUM(rkjdbsn.tharga) - SUM(TDISKON) - SUM(TDISKONVIP)) AS TOTAL,

			ROUND(
				(SUM(rkjdbsn.tharga) - SUM(TDISKON) - SUM(TDISKONVIP)) /
				IF(rkjdbsn.ST_PJK='P1', ?, 1)
			) AS DPP,

			IF(
				rkjdbsn.ST_PJK='P1',
				ROUND(
					(SUM(rkjdbsn.tharga) - SUM(TDISKON) - SUM(TDISKONVIP)) /
					(1 + ?) * ?
				),
				0
			) AS PPN,

			ROUND(SUM(
				IF(cntbsn.ST_CNT='K',
					IF((rkjdbsn.par=0 AND rkjdbsn.dis=0) OR LEFT(cntbsn.NA_CNT,3)='***',
						rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100,
						rkjdbsn.THARGA*rkjdbsn.margin/100
					),
					IF(cntbsn.ST_NOTA='B',
						(rkjdbsn.MARGIN/(rkjdbsn.MARGIN+100))*rkjdbsn.NILAI_JUAL,
						(rkjdbsn.MARGIN/100)*rkjdbsn.NILAI_JUAL
					)
				)
			)) AS nilai_margin,

			rkjdbsn.margin,
			rkjdbsn.PER,
			supbsn.kodes,
			supbsn.namas

		FROM rkjdbsn
		LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
		JOIN cntbsn ON rkjdbsn.CNT = cntbsn.CNT

		WHERE rkjdbsn.per = ?
		AND rkjdbsn.cnt = ?

		GROUP BY rkjdbsn.cnt, rkjdbsn.tgl_jual
		ORDER BY rkjdbsn.cnt, rkjdbsn.tgl_jual
		";

		return DB::select($sql, [$ppnx, $ppnx, $ppnx, $periode, $cnt]);
	}

	public function getRekapPenjualan($periode)
	{
		DB::beginTransaction();

		try {

			// Ambil PPN
			$ppnData = DB::select("CALL PPNPER(?)", [$periode]);
			$ppnx = $ppnData[0]->PPN ?? 0;

			$mon = substr(trim($periode), 0, 2);
			$yer = substr(trim($periode), -4);

			/*
			INSERT CNTBSND jika belum ada
			*/
			DB::statement("
				INSERT INTO CNTBSND(CNT,NA_CNT,YER)
				SELECT CNT,NA_CNT,?
				FROM cntbsn
				WHERE CONCAT(TRIM(CNT),?) NOT IN
				(
					SELECT CONCAT(TRIM(CNT),YER)
					FROM cntbsnd
					WHERE YER=?
				)
			", [$yer, $yer, $yer]);

			/*
			UPDATE DATA BULANAN
			*/
			DB::statement("
			UPDATE cntbsnd,
			(
				SELECT cnt,na_cnt,per,mon,yer,
				SUM(nett) as nett,
				SUM(ppn) as ppn
				FROM
				(
					SELECT *,
					LEFT(PER,2) AS MON,
					RIGHT(PER,4) AS YER,
					nilai_jual-nilai_margin+ptiara as nilai_nota,

					IF(ST_PJK='P1',
						ROUND((nilai_jual-nilai_margin+ptiara)/(1+?) * ?),
						0
					) as ppn,

					(nilai_jual-nilai_margin+ptiara) -
					IF(ST_PJK='P1',
						ROUND((nilai_jual-nilai_margin+ptiara)/(1+?) * ?),
						0
					) as nett

					FROM
					(
						SELECT
						rkjdbsn.tgl_jual,
						rkjdbsn.cnt,
						rkjdbsn.ST_PJK,
						rkjdbsn.na_cnt,

						SUM(rkjdbsn.qty) as qty,
						SUM(rkjdbsn.tharga) as tharga,
						rkjdbsn.dis,
						rkjdbsn.par,
						SUM(rkjdbsn.ptiara) as ptiara,
						SUM(rkjdbsn.psup) as psup,
						SUM(nilai_jual) as nilai_jual,

						ROUND(
							SUM(
								IF(cntbsn.ST_CNT='K',
									IF(
										(rkjdbsn.par=0 AND rkjdbsn.dis=0)
										OR LEFT(cntbsn.NA_CNT,3)='***',
										rkjdbsn.NILAI_JUAL*rkjdbsn.margin/100,
										rkjdbsn.THARGA*rkjdbsn.margin/100
									),
									IF(cntbsn.ST_NOTA='B',
										(rkjdbsn.MARGIN/(rkjdbsn.MARGIN+100))*rkjdbsn.NILAI_JUAL,
										(rkjdbsn.MARGIN/100)*rkjdbsn.NILAI_JUAL
									)
								)
							)
						) as nilai_margin,

						rkjdbsn.margin,
						rkjdbsn.PER

						FROM rkjdbsn
						LEFT JOIN supbsn ON rkjdbsn.SUP = supbsn.KODES
						JOIN cntbsn ON rkjdbsn.CNT=cntbsn.CNT

						WHERE rkjdbsn.per=?

						GROUP BY rkjdbsn.cnt,rkjdbsn.tgl_jual
					) ss
				) jjj
				GROUP BY cnt
			) AA

			SET cntbsnd.B{$mon}=AA.NETT,
				cntbsnd.P{$mon}=AA.PPN,
				cntbsnd.NA_CNT=AA.NA_CNT

			WHERE cntbsnd.CNT=AA.CNT
			AND cntbsnd.YER=AA.YER
			", [
				$ppnx,$ppnx,
				$ppnx,$ppnx,
				$periode
			]);

			/*
			UPDATE TOTAL TAHUN
			*/
			DB::statement("
				UPDATE cntbsnd
				SET
				jumb=b01+b02+b03+b04+b05+b06+b07+b08+b09+b10+b11+b12,
				jump=p01+p02+p03+p04+p05+p06+p07+p08+p09+p10+p11+p12
				WHERE yer=?
			", [$yer]);

			/*
			SELECT DATA UNTUK REPORT
			*/
			$data = DB::select("
				SELECT cntbsnd.*,cntbsn.st_cnt,cntbsn.kel_pt
				FROM cntbsnd
				LEFT JOIN cntbsn
				ON cntbsnd.cnt=cntbsn.cnt
				WHERE cntbsnd.yer=?
				ORDER BY cntbsn.kel_pt,cntbsnd.cnt
			", [$yer]);

			DB::commit();

			return $data;

		} catch (\Exception $e) {

			DB::rollBack();
			throw $e;

		}
	}

	public function getOmzetPenjualan($tgl1, $tgl2, $periode)
	{
		// ambil bulan (01,02,...)
		$mon = substr(trim($periode), 0, 2);

		// ambil list cabang (seperti cbgx di Delphi)
		$cabangList = DB::select("SELECT KODE FROM toko WHERE STA IN ('MA','CB') ORDER BY NO_ID ASC");

		$unionQueries = [];

		foreach ($cabangList as $cabang) {

			$cbg = strtolower($cabang->KODE);

			$unionQueries[] = "
				SELECT 
					'$cbg' as cbg,
					brgbsn.cnt,
					brgbsn.ncnt as na_cnt,
					DATE('$tgl1') as tgmin,
					DATE('$tgl2') as tgmax,
					brgbsn.st_pjk,

					ROUND(SUM(jld.qty)) as qcash,
					0 as qkred,
					ROUND(SUM(jld.qty)) as qjml,

					ROUND(SUM(jld.qty * jld.harga)) as cash,
					0 as kred,
					ROUND(SUM(jld.qty * jld.harga)) as jml,

					SUM(jld.total) as nbruto,

					SUM(
						IF(brgbsn.st_pjk='P1',
							FLOOR(jld.total / ((100 + $cbg.xx_ppn(1)) / 100)),
							jld.total
						)
					) as nnett,

					SUM(
						IF(brgbsn.st_pjk='P1',
							jld.total - FLOOR(jld.total / ((100 + $cbg.xx_ppn(1)) / 100)),
							0
						)
					) as nppn,

					ROUND(SUM(IF(jld.qty < 0 AND jld.type='RF', jld.qty, 0))) as qtyrf,
					ROUND(SUM(IF(jld.total < 0 AND jld.type='RF', jld.qty * jld.harga, 0))) as totalrf

				FROM {$cbg}.jual{$mon} jl
				JOIN {$cbg}.juald{$mon} jld ON jl.no_bukti = jld.no_bukti
				JOIN brgbsn ON jld.KD_BRG = brgbsn.KD_BRG

				WHERE LENGTH(jld.KD_BRG) > 7
				AND jl.flag = 'JL'
				AND jl.tgl BETWEEN '$tgl1' AND '$tgl2'

				GROUP BY brgbsn.cnt
			";
		}

		// gabungkan UNION ALL
		$sql = "
			SELECT * FROM (
				" . implode(" UNION ALL ", $unionQueries) . "
			) as rekap
			ORDER BY cbg, cnt
		";

		return DB::select($sql);
	}

	private function getRekapHarian($cbg, $cnt, $periode, $bulan)
    {
        $cabangx = strtolower($cbg);

        if($cbg == 'ALL')
		{
			$data = DB::select("SELECT cbg,cnt,na_cnt,kd_brg,na_brg,right(trim(kd_brg),5) as kode,per,hjual    
								 ,sum(if(day(tgl)=1,qty,0)) as b01,sum(if(day(tgl)=2,qty,0)) as b02,sum(if(day(tgl)=3,qty,0)) as b03,sum(if(day(tgl)=4,qty,0)) as b04,sum(if(day(tgl)=5,qty,0)) as b05
								 ,sum(if(day(tgl)=6,qty,0)) as b06,sum(if(day(tgl)=7,qty,0)) as b07,sum(if(day(tgl)=8,qty,0)) as b08,sum(if(day(tgl)=9,qty,0)) as b09,sum(if(day(tgl)=10,qty,0)) as b10
								 ,sum(if(day(tgl)=11,qty,0)) as b11,sum(if(day(tgl)=12,qty,0)) as b12,sum(if(day(tgl)=13,qty,0)) as b13,sum(if(day(tgl)=14,qty,0)) as b14,sum(if(day(tgl)=15,qty,0)) as b15
								, sum(if(day(tgl)=16,qty,0)) as b16,sum(if(day(tgl)=17,qty,0)) as b17,sum(if(day(tgl)=18,qty,0)) as b18,sum(if(day(tgl)=19,qty,0)) as b19,sum(if(day(tgl)=20,qty,0)) as b20
								,sum(if(day(tgl)=21,qty,0)) as b21,sum(if(day(tgl)=22,qty,0)) as b22,sum(if(day(tgl)=23,qty,0)) as b23,sum(if(day(tgl)=24,qty,0)) as b24,sum(if(day(tgl)=25,qty,0)) as b25
								,sum(if(day(tgl)=26,qty,0)) as b26,sum(if(day(tgl)=27,qty,0)) as b27,sum(if(day(tgl)=28,qty,0)) as b28,sum(if(day(tgl)=29,qty,0)) as b29,sum(if(day(tgl)=30,qty,0)) as b30
								,sum(if(day(tgl)=31,qty,0)) as b31,SUM(qty) as total
								from (select'ALL' as cbg,brgbsn.hjual ,brgbsn.cnt, brgbsn.ncnt as na_cnt,juald$bulan.KD_BRG,brgbsn.NA_BRG,
									       jual$bulan.tgl,jual$bulan.per,  ROUND(sum(juald$bulan.qty)) as qty  
									         from tgz.jual$bulan,tgz.juald$bulan,tgz.brgbsn 
									       where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
									      and jual$bulan.flag='JL' AND jual$bulan.per='$periode' and juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.KD_BRG,jual$bulan.tgl  union all
									  select'ALL' as cbg,brgbsn.hjual  ,brgbsn.cnt, brgbsn.ncnt as na_cnt,juald$bulan.KD_BRG,brgbsn.NA_BRG,
									       jual$bulan.tgl,jual$bulan.per,  ROUND(sum(juald$bulan.qty)) as qty  
									         from sop.jual$bulan,tgz.juald$bulan,tgz.brgbsn 
									       where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
									      and jual$bulan.flag='JL' AND jual$bulan.per='$periode' and juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.KD_BRG,jual$bulan.tgl  union all
									select'ALL' as cbg,brgbsn.hjual  ,brgbsn.cnt, brgbsn.ncnt as na_cnt,juald$bulan.KD_BRG,brgbsn.NA_BRG,
									       jual$bulan.tgl,jual$bulan.per,  ROUND(sum(juald$bulan.qty)) as qty  
									         from tgz.jual$bulan,tgz.juald$bulan,tgz.brgbsn 
									       where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
									      and jual$bulan.flag='JL' AND jual$bulan.per='$periode' and juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.KD_BRG,jual$bulan.tgl
									 ) as aa GROUP BY kd_brg
			");
		} else {
			$sql = "SELECT cbg,cnt,na_cnt,kd_brg,na_brg,right(trim(kd_brg),5) as kode,per,hjual   
					 ,sum(if(day(tgl)=1,qty,0)) as b01,sum(if(day(tgl)=2,qty,0)) as b02,sum(if(day(tgl)=3,qty,0)) as b03,sum(if(day(tgl)=4,qty,0)) as b04,sum(if(day(tgl)=5,qty,0)) as b05
					 ,sum(if(day(tgl)=6,qty,0)) as b06,sum(if(day(tgl)=7,qty,0)) as b07,sum(if(day(tgl)=8,qty,0)) as b08,sum(if(day(tgl)=9,qty,0)) as b09,sum(if(day(tgl)=10,qty,0)) as b10
					 ,sum(if(day(tgl)=11,qty,0)) as b11,sum(if(day(tgl)=12,qty,0)) as b12,sum(if(day(tgl)=13,qty,0)) as b13,sum(if(day(tgl)=14,qty,0)) as b14,sum(if(day(tgl)=15,qty,0)) as b15
					, sum(if(day(tgl)=16,qty,0)) as b16,sum(if(day(tgl)=17,qty,0)) as b17,sum(if(day(tgl)=18,qty,0)) as b18,sum(if(day(tgl)=19,qty,0)) as b19,sum(if(day(tgl)=20,qty,0)) as b20
					,sum(if(day(tgl)=21,qty,0)) as b21,sum(if(day(tgl)=22,qty,0)) as b22,sum(if(day(tgl)=23,qty,0)) as b23,sum(if(day(tgl)=24,qty,0)) as b24,sum(if(day(tgl)=25,qty,0)) as b25
					,sum(if(day(tgl)=26,qty,0)) as b26,sum(if(day(tgl)=27,qty,0)) as b27,sum(if(day(tgl)=28,qty,0)) as b28,sum(if(day(tgl)=29,qty,0)) as b29,sum(if(day(tgl)=30,qty,0)) as b30
					,sum(if(day(tgl)=31,qty,0)) as b31,SUM(qty) as total
					from (  select jual$bulan.cbg,brgbsn.hjual  ,brgbsn.cnt, brgbsn.ncnt as na_cnt,juald$bulan.KD_BRG,brgbsn.NA_BRG,
						       jual$bulan.tgl,jual$bulan.per,  ROUND(sum(juald$bulan.qty)) as qty  
						         from {$cabangx}.jual$bulan,{$cabangx}.juald$bulan,tgz.brgbsn
						       where jual$bulan.no_bukti=juald$bulan.no_bukti and LENGTH(juald$bulan.KD_BRG)>7
						      and jual$bulan.flag='JL' AND jual$bulan.per='$periode' and juald$bulan.KD_BRG=brgbsn.KD_BRG AND brgbsn.CNT='$cnt' GROUP BY brgbsn.KD_BRG,jual$bulan.tgl
						 ) as aa GROUP BY kd_brg";

			$data = DB::select($sql);
		}

		return $data;
    }
}