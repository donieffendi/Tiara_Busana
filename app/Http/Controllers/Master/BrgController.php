<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brg;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

class BrgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_brg.index');
    }

    
    public function getBrg( Request $request )
    {
		// $PPN = Auth::user()->PPN;
		
        $brg = DB::SELECT("SELECT NO_ID, KD_BRG,JNS,NA_BRG,HJUAL,HBELI,ST_PJK,ST_NOTA,kategori 
                        from brgbsn 
                        order by kd_brg ");

        return Datatables::of($brg)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan") 
                {   
                    // url untuk delete di index
                    $url = "'".url("brg/delete/" . $row->NO_ID )."'";
                    // batas
                    
                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a class="dropdown-item" href="brg/edit/?idx=' . $row->NO_ID . '&tipx=edit";                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <hr>
                                </hr>

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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate(
            $request,
            // GANTI 8 SESUAI NAMA KOLOM DI NAVICAT //
            [
                'CNT'       => 'required'
            ]
        );

        // Insert Header

        $query = DB::table('brgbsn')->select('CNT')->orderByDesc('CNT')->limit(1)->get();

		
        $brg = Brg::create(
            [
                'CNT'     => ($request['CNT'] == null) ? "" : $request['CNT'],				
                'NA_CNT'     => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'SUP'      => ($request['SUP'] == null) ? "" : $request['SUP'],
                'KD_BRG'      => ($request['KD_BRG'] == null) ? "" : $request['KD_BRG'],
                'NA_BRG'      => ($request['NA_BRG'] == null) ? "" : $request['NA_BRG'],
                'BARCODE'      => ($request['BARCODE'] == null) ? "" : $request['BARCODE'],
                'JNS'   => ($request['JNS'] == null) ? "" : $request['JNS'],
                'DIS_STS'    => ($request['DIS_STS'] == null) ? "" : $request['DIS_STS'],
                'DIS_KHS'     => ($request['DIS_KHS'] == null) ? "" : $request['DIS_KHS'],
                'HJUAL'       => (float) str_replace(',', '', $request['HJUAL']),
                'BELI'       => (float) str_replace(',', '', $request['BELI']),
                'HBNET'       => (float) str_replace(',', '', $request['HBNET']),
                'DIS1'       => (float) str_replace(',', '', $request['DIS1']),
                'DIS2'       => (float) str_replace(',', '', $request['DIS2']),
                'DIS3'       => (float) str_replace(',', '', $request['DIS3']),
                'DIS4'       => (float) str_replace(',', '', $request['DIS4']),
                'ST_HRG'     => ($request['ST_HRG'] == null) ? "" : $request['ST_HRG'],
                'LS_CNT'     => ($request['LS_CNT'] == null) ? "" : $request['LS_CNT'],
                'DTH'     => ($request['DTH'] == null) ? "" : $request['DTH'],
                'TTH'     => ($request['TTH'] == null) ? "" : $request['TTH'],
                'JN_CNT'     => ($request['JN_CNT'] == null) ? "" : $request['JN_CNT'],
                'DIS_PRO'       => (float) str_replace(',', '', $request['DIS_PRO']),
                'DIS_CUST'       => (float) str_replace(',', '', $request['DIS_CUST']),
                'DIS_CUSN'     => ($request['DIS_CUSN'] == null) ? "" : $request['DIS_CUSN'],
                'ST_CNT'     => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'ST_NOTA'     => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'MARGIN'       => (float) str_replace(',', '', $request['MARGIN']),
                'ST_ORD'     => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'ST_PJK'     => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'CBAYAR'     => ($request['CBAYAR'] == null) ? "" : $request['CBAYAR'],
                'KEL_PT'     => ($request['KEL_PT'] == null) ? "" : $request['KEL_PT'],
                'LBAYAR'     => ($request['LBAYAR'] == null) ? "" : $request['LBAYAR'],
                'KEL_BRG'     => ($request['KEL_BRG'] == null) ? "" : $request['KEL_BRG'],
                'KW_RET'     => ($request['KW_RET'] == null) ? "" : $request['KW_RET'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'KW_LBL'     => ($request['KW_LBL'] == null) ? "" : $request['KW_LBL'],
                'KATEGORI'     => ($request['KATEGORI'] == null) ? "" : $request['KATEGORI'],
                'DIS_TGLM'     => date('Y-m-d', strtotime($request['DIS_TGLM'])),
                'DIS_TGLS'   => date('Y-m-d', strtotime($request['DIS_TGLS'])),

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['CNT'];
		
		$brg = Brg::where('CNT', $kodesx )->first();
					       
        //return redirect('/brg/edit/?idx=' . $brg->NO_ID . '&tipx=edit')->with('statusInsert', 'Data baru berhasil ditambahkan');
		return redirect('/brg')->with('statusInsert', 'Data baru berhasil ditambahkan');		


    }

 
 
    public function edit(Request $request ,  Brg $brg)
    {

        $pilihbank = DB::table('bang')->select('KODE', 'NAMA')->orderBy('KODE', 'ASC')->get();
        // ganti 16


		$tipx = $request->tipx;

		$idx = $request->idx;
					

		
		if ( $idx =='0' && $tipx=='undo'  )
	    {
			$tipx ='top';
			
		   }
		   

		if ($tipx=='search') {
			
		   	
    	   $kodex = $request->kodex;
		   
		   $bingco = DB::SELECT("SELECT NO_ID, CNT from brgbsn 
		                 where CNT = '$kodex'						 
		                 ORDER BY CNT ASC  LIMIT 1" );
						 
			
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
		
					
		}
		   
		if ($tipx=='top') {
			
		   $bingco = DB::SELECT("SELECT NO_ID, CNT from brgbsn      
		                 ORDER BY CNT ASC  LIMIT 1" );
					 
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
			  
		}
		
		
		if ($tipx=='prev' ) {
			
    	   $kodex = $request->kodex;
			
		   $bingco = DB::SELECT("SELECT NO_ID, CNT from brgbsn     
		             where CNT < 
					 '$kodex' ORDER BY CNT DESC LIMIT 1" );
			

			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = $idx; 
			  }
			  
			  
			  
			  

		}
		if ($tipx=='next' ) {
			
				
      	   $kodex = $request->kodex;
	   
		   $bingco = DB::SELECT("SELECT NO_ID, CNT from brgbsn   
		             where CNT > 
					 '$kodex' ORDER BY CNT ASC LIMIT 1" );
					 
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = $idx; 
			  }
			  
			
		}

		if ($tipx=='bottom') {
		  
    		$bingco = DB::SELECT("SELECT NO_ID, CNT from brgbsn    
		              ORDER BY CNT DESC  LIMIT 1" );
					 
			if(!empty($bingco)) 
			{
				$idx = $bingco[0]->NO_ID;
			  }
			else
			{
				$idx = 0; 
			  }
			  
			
		}


		if ( $tipx=='undo' || $tipx=='search' )
	    {
        
			$tipx ='edit';
			
		   }
		
	
	  	if ( $idx != 0 ) 
		{
			$brg = Brg::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
             $brg = new Brg;			 
		 }

		 $data = [
						'header' => $brg,
			        ];				
			return view('master_brg.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ])->with(['pilihbank' => $pilihbank]);
		 
	 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brg $brg)
    {

        $this->validate(
            $request,
            [
                'CNT'       => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;
		
        $brg->update(
            [
                		
                'NA_CNT'      => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'SUP'         => ($request['SUP'] == null) ? "" : $request['SUP'],
                'KD_BRG'      => ($request['KD_BRG'] == null) ? "" : $request['KD_BRG'],
                'NA_BRG'      => ($request['NA_BRG'] == null) ? "" : $request['NA_BRG'],
                'BARCODE'     => ($request['BARCODE'] == null) ? "" : $request['BARCODE'],
                'JNS'         => ($request['JNS'] == null) ? "" : $request['JNS'],
                'DIS_STS'     => ($request['DIS_STS'] == null) ? "" : $request['DIS_STS'],
                'DIS_KHS'     => ($request['DIS_KHS'] == null) ? "" : $request['DIS_KHS'],
                'HJUAL'       => (float) str_replace(',', '', $request['HJUAL']),
                'BELI'        => (float) str_replace(',', '', $request['BELI']),
                'HBNET'       => (float) str_replace(',', '', $request['HBNET']),
                'DIS1'        => (float) str_replace(',', '', $request['DIS1']),
                'DIS2'        => (float) str_replace(',', '', $request['DIS2']),
                'DIS3'        => (float) str_replace(',', '', $request['DIS3']),
                'DIS4'        => (float) str_replace(',', '', $request['DIS4']),
                'ST_HRG'      => ($request['ST_HRG'] == null) ? "" : $request['ST_HRG'],
                'LS_CNT'      => ($request['LS_CNT'] == null) ? "" : $request['LS_CNT'],
                'DTH'         => ($request['DTH'] == null) ? "" : $request['DTH'],
                'TTH'     => ($request['TTH'] == null) ? "" : $request['TTH'],
                'JN_CNT'     => ($request['JN_CNT'] == null) ? "" : $request['JN_CNT'],
                'DIS_PRO'       => (float) str_replace(',', '', $request['DIS_PRO']),
                'DIS_CUST'       => (float) str_replace(',', '', $request['DIS_CUST']),
                'DIS_CUSN'     => ($request['DIS_CUSN'] == null) ? "" : $request['DIS_CUSN'],
                'ST_CNT'     => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'ST_NOTA'     => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'MARGIN'       => (float) str_replace(',', '', $request['MARGIN']),
                'ST_ORD'     => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'ST_PJK'     => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'CBAYAR'     => ($request['CBAYAR'] == null) ? "" : $request['CBAYAR'],
                'KEL_PT'     => ($request['KEL_PT'] == null) ? "" : $request['KEL_PT'],
                'LBAYAR'     => ($request['LBAYAR'] == null) ? "" : $request['LBAYAR'],
                'KEL_BRG'     => ($request['KEL_BRG'] == null) ? "" : $request['KEL_BRG'],
                'KW_RET'     => ($request['KW_RET'] == null) ? "" : $request['KW_RET'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'KW_LBL'     => ($request['KW_LBL'] == null) ? "" : $request['KW_LBL'],
                'KATEGORI'     => ($request['KATEGORI'] == null) ? "" : $request['KATEGORI'],
                'DIS_TGLM'     => date('Y-m-d', strtotime($request['DIS_TGLM'])),
                'DIS_TGLS'   => date('Y-m-d', strtotime($request['DIS_TGLS'])),

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


        //return redirect('/brg/edit/?idx=' . $brg->NO_ID . '&tipx=edit');
		return redirect('/brg')->with('statusInsert', 'Data baru berhasil diupdate');
				
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, Brg $brg)
    {
        $deleteBrg = Brg::find($brg->NO_ID);
        $deleteBrg->delete();

        return redirect('/brg')->with('status', 'Data berhasil dihapus');
    }

    public function cekbrg(Request $request)
    {
        $getItem = DB::SELECT('select count(*) as ADA from brgbsn where CNT ="' . $request->CNT . '"');

        return $getItem;
    }
	
    public function getSelectKodes(Request $request)
    {
        $search = $request->search;
        $page = $request->page;
        if ($page == 0) {
            $xa = 0;
        } else {
            $xa = ($page - 1) * 10;
        }
        $perPage = 10;
        
        $hasil = DB::SELECT("SELECT CNT, NAMAS from brgbsn WHERE (CNT LIKE '%$search%' or NAMAS LIKE '%$search%') ORDER BY CNT LIMIT $xa,$perPage ");
        $selectajax = array();
        foreach ($hasil as $row => $value) {
            $selectajax[] = array(
                'id' => $hasil[$row]->CNT,
                'text' => $hasil[$row]->CNT,
                'namas' => $hasil[$row]->NAMAS,
            );
        }
        $select['total_count'] =  count($selectajax);
        $select['items'] = $selectajax;
        return response()->json($select);
    }
}
