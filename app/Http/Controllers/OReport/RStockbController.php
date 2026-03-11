<?php

namespace App\Http\Controllers\OReport;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Master\Brg;
use App\Models\Master\Perid;
use DataTables;
use Auth;
use DB;

include_once base_path()."/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

use \koolreport\laravel\Friendship;
use \koolreport\bootstrap4\Theme;

class RStockbController extends Controller
{

    public function report()
    {
		$per = Perid::query()->get();
		session()->put('filter_periode', '');

		session()->put('filter_tglDari', date("d-m-Y"));
		session()->put('filter_tglSampai', date("d-m-Y"));
		session()->put('filter_CNT1', '');
		session()->put('filter_CNT2', '');

        return view('oreport_stockb.report')->with(['per' => $per])->with(['hasil' => []]);
    }	  
	
	public function jasperStockbReport(Request $request) 
	{
		$file 	= 'stockn';
		$PHPJasperXML = new PHPJasperXML();
		$PHPJasperXML->load_xml_file(base_path().('/app/reportc01/phpjasperxml/'.$file.'.jrxml'));
		
			// Ganti format tanggal input agar sama dengan database
			$tglDrD = date("Y-m-d", strtotime($request['tglDr']));
            $tglSmpD = date("Y-m-d", strtotime($request['tglSmp']));
			
			// Check Filter
			$semua = $request->semua;
			$per = $request->per;

			$bulan = substr($per,0,2);
			$tahun = substr($per,3,4);

			$kode1 = $request->CNT1;
			$kode2 = $request->CNT2;

			session()->put('filter_tglDari', $request->tglDr);
			session()->put('filter_tglSampai', $request->tglSmp);
			session()->put('filter_CNT', $request->CNT);
			session()->put('filter_NA_CNT', $request->NA_CNT);
			session()->put('filter_periode', $request->per);
			session()->put('filter_semua', $request->semua);
		
		if ($semua == 1){
			$query = DB::SELECT("SELECT AA.cnt, AA.ncnt,'' as kd_brg,'' as na_brg, SUM(AA.qtyx) as qtyx, round(SUM(AA.total_cash)) as total_cash, AA.dis,       
								AA.par, SUM(AA.beban_cash) as beban_cash, AA.tgl, AA.no_bukti,IF(YY.CNT IS NULL,0,1) AS KONDISI                            
								FROM                                                                   
								(SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                  
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) as total_cash,    
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis - disbsn.PAR)/100) as beban_cash                      
										from tgz.juald$bulan,brgbsn,disbsn                                            
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
										AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM<>disbsn.TGLS and  disbsn.dis<>0  AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                      
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                         
								UNION ALL                                                              
										SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                    
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,  
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								  disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis- disbsn.PAR)/100) as beban_cash                                  
										from tmm.juald$bulan,brgbsn,disbsn                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
										AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1'  AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM<>disbsn.TGLS  and disbsn.dis<>0   AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                               
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                           
								UNION ALL                                                              
										SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                    
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,     
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								   disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis- disbsn.PAR)/100) as beban_cash                                   
										from sop.juald$bulan,brgbsn,disbsn                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
								 	AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1'  AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM<>disbsn.TGLS  and disbsn.DIS<>0    AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                            
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                            
								) as AA LEFT JOIN (SELECT  CNT,TGLS AS TGL FROM disbsn where disbsn.TGLM=disbsn.tgls AND disbsn.TYPE='ALL' AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'      GROUP BY CNT,TGLM  ) AS YY ON AA.CNT=YY.CNT AND AA.TGL=YY.TGL
								  GROUP BY AA.CNT, AA.TGL, AA.NO_BUKTI  HAVING KONDISI=0   UNION ALL
								 SELECT AA.cnt, AA.ncnt,'' as kd_brg,'' as na_brg, SUM(AA.qtyx) as qtyx, round(SUM(AA.total_cash)) as total_cash, AA.dis,       
								AA.par, SUM(AA.beban_cash) as beban_cash, AA.tgl, AA.no_bukti,0 AS KONDISI                       
								FROM                                                                   
								(SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                  
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) as total_cash,    
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG, 
								disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis- disbsn.PAR)/100) as beban_cash                      
										from tgz.juald$bulan,brgbsn,disbsn                                            
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
										AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM=disbsn.TGLS and  disbsn.dis<>0   AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                         
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                         
								UNION ALL                                                              
										SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                    
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,  
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								  disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis- disbsn.PAR)/100) as beban_cash                                  
										from tmm.juald$bulan,brgbsn,disbsn                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
										AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1'  AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM=disbsn.TGLS  and disbsn.dis<>0    AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                           
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                           
								UNION ALL                                                              
										SELECT disbsn.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,                                    
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,     
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								   disbsn.dis, disbsn.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsn.dis- disbsn.PAR)/100) as beban_cash                                   
										from sop.juald$bulan,brgbsn,disbsn                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                             
								 	AND date(juald$bulan.TGL) BETWEEN disbsn.TGLM AND disbsn.TGLS                  
										AND brgbsn.CNT>='$kode1'  AND brgbsn.CNT<='$kode2' AND brgbsn.CNT=disbsn.CNT AND disbsn.TYPE='ALL' AND disbsn.TGLM=disbsn.TGLS  and disbsn.DIS<>0    AND DISBSN.TGLS>='$tglDrD' AND DISBSN.TGLM<='$tglSmpD'                         
										GROUP BY brgbsn.CNT, DATE(juald$bulan.TGL),disbsn.NO_BUKTI                            
								) as AA  GROUP BY AA.CNT, AA.TGL, AA.NO_BUKTI  ORDER BY CNT,TGL");
		} else {
			$query = DB::SELECT("SELECT AA.cnt, AA.ncnt, AA.kd_brg,AA.na_brg, SUM(AA.qtyx) as qtyx, round(SUM(AA.total_cash)) as total_cash, AA.dis,                    
								AA.par, SUM(AA.beban_cash) as beban_cash, AA.tgl, AA.no_bukti,IF(YY.KD_BRG IS NULL,0,1) AS KONDISI                                    
								FROM                                                                                
								(SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                               
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) as total_cash,                 
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis - disbsnd.PAR)/100) as beban_cash                                   
										from tgz.juald$bulan,brgbsn,disbsnd                                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
										AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG AND disbsnd.TGLM<>disbsnd.TGLS and  disbsnd.dis<>0   AND DISBSND.TGLS>='$tglDrD'   AND  DISBSND.TGLM<='$tglSmpD'                                         
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                      
								UNION ALL                                                                           
										SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                                   
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,               
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis- disbsnd.PAR)/100) as beban_cash                                               
										from tmm.juald$bulan,brgbsn,disbsnd                                                      
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
										AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG  AND disbsnd.TGLM<>disbsnd.TGLS  and disbsnd.dis<>0  AND DISBSND.TGLS>='$tglDrD'   AND  DISBSND.TGLM<='$tglSmpD'                                       
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                        
								UNION ALL                                                                           
										SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                                   
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,                  
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis- disbsnd.PAR)/100) as beban_cash                                                
										from sop.juald$bulan,brgbsn,disbsnd                                                      
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
									AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG  AND disbsnd.TGLM<>disbsnd.TGLS  and disbsnd.DIS<>0  AND DISBSND.TGLS>='$tglDrD' AND DISBSND.TGLM<='$tglSmpD'                                       
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                         
								) as AA  LEFT JOIN (SELECT  CNT,KD_BRG,TGLS AS TGL FROM disbsnd where disbsnd.TGLM=disbsnd.tgls    AND DISBSND.TGLS>='$tglDrD' AND DISBSND.TGLM<='$tglSmpD'  GROUP BY CNT,KD_BRG,TGLM   ) AS YY ON  AA.KD_BRG=YY.KD_BRG AND AA.TGL=YY.TGL   
								GROUP BY AA.CNT,AA.KD_BRG, AA.TGL, AA.NO_BUKTI    UNION ALL 
								SELECT AA.cnt, AA.ncnt, AA.kd_brg,AA.na_brg, SUM(AA.qtyx) as qtyx, round(SUM(AA.total_cash)) as total_cash, AA.dis,                    
								AA.par, SUM(AA.beban_cash) as beban_cash, AA.tgl, AA.no_bukti ,0 AS KONDISI                                   
								FROM                                                                                
								(		SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                               
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) as total_cash,                 
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis- disbsnd.PAR)/100) as beban_cash                                   
										from tgz.juald$bulan,brgbsn,disbsnd                                                         
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
										AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG AND disbsnd.TGLM=disbsnd.TGLS and  disbsnd.dis<>0  AND DISBSND.TGLS>='$tglDrD' AND DISBSND.TGLM<='$tglSmpD'                                       
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                      
								UNION ALL                                                                           
										SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                                   
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,               
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis- disbsnd.PAR)/100) as beban_cash                                               
										from tmm.juald$bulan,brgbsn,disbsnd                                                      
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
										AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG  AND disbsnd.TGLM=disbsnd.TGLS  and disbsnd.dis<>0  AND DISBSND.TGLS>='$tglDrD'   AND  DISBSND.TGLM<='$tglSmpD'                                      
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                        
								UNION ALL                                                                           
										SELECT disbsnd.NO_BUKTI,brgbsn.CNT,	brgbsn.NCNT,brgbsn.kd_brg,brgbsn.na_brg,                                                   
										sum(juald$bulan.qty) as qtyx, sum(juald$bulan.total+juald$bulan.diskon) total_cash,                  
										date(juald$bulan.TGL) as TGL, juald$bulan.CBG,
								disbsnd.dis, disbsnd.par as par, round( sum(juald$bulan.total+juald$bulan.diskon) * ( disbsnd.dis- disbsnd.PAR)/100) as beban_cash                                                
										from sop.juald$bulan,brgbsn,disbsnd                                                      
										WHERE juald$bulan.KD_BRG=brgbsn.KD_BRG                                          
									AND date(juald$bulan.TGL) BETWEEN disbsnd.TGLM AND disbsnd.TGLS                               
										AND brgbsn.CNT>='$kode1' AND brgbsn.CNT<='$kode2' AND brgbsn.KD_BRG=disbsnd.KD_BRG  AND disbsnd.TGLM=disbsnd.TGLS  and disbsnd.DIS<>0  AND DISBSND.TGLS>='$tglDrD'   AND  DISBSND.TGLM<='$tglSmpD'                                       
										GROUP BY brgbsn.CNT, brgbsn.KD_BRG, DATE(juald$bulan.TGL),disbsnd.NO_BUKTI                                         
								) as AA
								GROUP BY AA.CNT,AA.KD_BRG, AA.TGL, AA.NO_BUKTI");
		}

		if($request->has('filter'))
		{
			$per = Perid::query()->get();

			return view('oreport_stockb.report')->with(['per' => $per])->with(['hasil' => $query]);
		}

		$data=[];
		foreach ($query as $key => $value)
		{
			array_push($data, array(
				'NO_BUKTI' => $query[$key]->NO_BUKTI,
				'TGL' => $query[$key]->TGL,
				'KD_BRG' => $query[$key]->KD_BRG,
				'NA_BRG' => $query[$key]->NA_BRG,
				'KG' => $query[$key]->KG,
				'NOTES' => $query[$key]->NOTES,
			));
		}
		$PHPJasperXML->setData($data);
		ob_end_clean();
		$PHPJasperXML->outpage("I");
	}
	
}
