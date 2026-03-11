<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

class SupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_sup.index');
    }

    
    public function browse(Request $request)
    {

		
    	if (!empty(request('q'))) {


                 $sup = DB::SELECT("SELECT NO_ID, KODES, NAMAS, ALAMAT, KOTA, NOTBAY, KONTAK, AKTIF, CASE WHEN PKP = '1' THEN '(PKP)' ELSE '(NON PKP)' END AS PKP2,
                            PKP, HARI
                            from supbsn 
                            WHERE  NAMAS LIKE ('%$request->q%') 
                            ORDER BY NAMAS "); 
	
    	    
        } else {
			$sup = DB::SELECT("SELECT NO_ID, KODES, NAMAS, ALAMAT, KOTA, NOTBAY, KONTAK, AKTIF, CASE WHEN PKP = '1' THEN '(PKP)' ELSE '(NON PKP)' END AS PKP2,
                                PKP, HARI
                            from supbsn
                            
                            ORDER BY NAMAS ");			
		}
		
        return response()->json($sup);
    }


    public function browse_amplop(Request $request)
    {
        $sup = DB::SELECT("SELECT KODES, NAMAS, P_ALMT AS ALAMAT, P_KOTA AS KOTA, P_TLP AS TELP FROM SUPBSN ORDER BY KODES ");
		
        return response()->json($sup);
    }
	
    public function getSup( Request $request )
    {
		// $PPN = Auth::user()->PPN;
		
        $sup = DB::SELECT("SELECT * from supbsn ORDER BY KODES ASC");
	
        return Datatables::of($sup)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan") 
                {   
                    // url untuk delete di index
                    $url = "'".url("sup/delete/" . $row->NO_ID )."'";
                    // batas
                    
                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a class="dropdown-item" href="sup/edit/?idx=' . $row->NO_ID . '&tipx=edit";                                <i class="fas fa-edit"></i>
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
                'KODES'       => 'required',
                'NAMAS'       => 'required'
            ]
        );

        // Insert Header

        $query = DB::table('supbsn')->select('KODES')->orderByDesc('KODES')->limit(1)->get();

		
        $sup = Sup::create(
            [
                'KODES'     => ($request['KODES'] == null) ? "" : $request['KODES'],				
                'NAMAS'     => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'TYPE'      => ($request['TYPE'] == null) ? "" : $request['TYPE'],
                'GSUP'      => ($request['GSUP'] == null) ? "" : $request['GSUP'],
                'PEMILIK'   => ($request['PEMILIK'] == null) ? "" : $request['PEMILIK'],
                'P_TELP'    => ($request['P_TELP'] == null) ? "" : $request['P_TELP'],
                'EMAIL'     => ($request['EMAIL'] == null) ? "" : $request['EMAIL'],
                'P_ALMT'    => ($request['P_ALMT'] == null) ? "" : $request['P_ALMT'],
                'P_KOTA'    => ($request['P_KOTA'] == null) ? "" : $request['P_KOTA'],
                'G_ALMT'    => ($request['G_ALMT'] == null) ? "" : $request['G_ALMT'],
                'R_ALMT'    => ($request['R_ALMT'] == null) ? "" : $request['R_ALMT'],
                'P_TLP'     => ($request['P_TLP'] == null) ? "" : $request['P_TLP'],
                'P_FAX'     => ($request['P_FAX'] == null) ? "" : $request['P_FAX'],
                'R_TLP'     => ($request['R_TLP'] == null) ? "" : $request['R_TLP'],
                'P_POS'     => ($request['P_POS'] == null) ? "" : $request['P_POS'],
                'TGL_M'     => date('Y-m-d', strtotime($request['TGL_M'])),
                'UPD_TGL'   => date('Y-m-d', strtotime($request['UPD_TGL'])),
                'TGL_PNG'   => date('Y-m-d', strtotime($request['TGL_PNG'])),
                'TGL_K'     => date('Y-m-d', strtotime($request['TGL_K'])),
                'KET_PRB'   => ($request['KET_PRB'] == null) ? "" : $request['KET_PRB'],
                'B_BANK'    => ($request['B_BANK'] == null) ? "" : $request['B_BANK'],
                'NPWP'      => ($request['NPWP'] == null) ? "" : $request['NPWP'],
                'B_KOTA'    => ($request['B_KOTA'] == null) ? "" : $request['B_KOTA'],
                'NM_NPWP'   => ($request['NM_NPWP'] == null) ? "" : $request['NM_NPWP'],
                'B_NAMA'    => ($request['B_NAMA'] == null) ? "" : $request['B_NAMA'],
                'NPPKPP'    => ($request['NPPKPP'] == null) ? "" : $request['NPPKPP'],
                'B_ACC'     => ($request['B_ACC'] == null) ? "" : $request['B_ACC'],
                'AL_MPWP'   => ($request['AL_MPWP'] == null) ? "" : $request['AL_MPWP'],
                'CARA'      => ($request['CARA'] == null) ? "" : $request['CARA'],
                'KT_NPWP'   => ($request['KT_NPWP'] == null) ? "" : $request['KT_NPWP'],
                'ADA_CNTP'  => ($request['ADA_CNTP'] == null) ? "" : $request['ADA_CNTP'],
                'BAY'       => (float) str_replace(',', '', $request['BAY']),
                'BAN'       => (float) str_replace(',', '', $request['BAN']),
                'C_SP'      => ($request['C_SP'] == null) ? "" : $request['C_SP'],

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['KODES'];
		
		$sup = Sup::where('KODES', $kodesx )->first();
					       
        //return redirect('/sup/edit/?idx=' . $sup->NO_ID . '&tipx=edit')->with('statusInsert', 'Data baru berhasil ditambahkan');
		return redirect('/sup')->with('statusInsert', 'Data baru berhasil ditambahkan');		


    }

 
 
    public function edit(Request $request ,  Sup $sup)
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
		   
		   $bingco = DB::SELECT("SELECT NO_ID, KODES from supbsn 
		                 where KODES = '$kodex'						 
		                 ORDER BY KODES ASC  LIMIT 1" );
						 
			
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, KODES from supbsn      
		                 ORDER BY KODES ASC  LIMIT 1" );
					 
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
			
		   $bingco = DB::SELECT("SELECT NO_ID, KODES from supbsn     
		             where KODES < 
					 '$kodex' ORDER BY KODES DESC LIMIT 1" );
			

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
	   
		   $bingco = DB::SELECT("SELECT NO_ID, KODES from supbsn   
		             where KODES > 
					 '$kodex' ORDER BY KODES ASC LIMIT 1" );
					 
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
		  
    		$bingco = DB::SELECT("SELECT NO_ID, KODES from supbsn    
		              ORDER BY KODES DESC  LIMIT 1" );
					 
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
			$sup = Sup::where('NO_ID', $idx )->first();	
	     }
		 else
		 {
             $sup = new Sup;			 
		 }

		 $data = [
						'header' => $sup,
			        ];				
			return view('master_sup.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ])->with(['pilihbank' => $pilihbank]);
		 
	 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sup $sup)
    {

        $this->validate(
            $request,
            [
                'KODES'       => 'required',
                'NAMAS'      => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;
		
        $sup->update(
            [
				
                'NAMAS'     => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'TYPE'      => ($request['TYPE'] == null) ? "" : $request['TYPE'],
                'GSUP'      => ($request['GSUP'] == null) ? "" : $request['GSUP'],
                'PEMILIK'   => ($request['PEMILIK'] == null) ? "" : $request['PEMILIK'],
                'P_TELP'    => ($request['P_TELP'] == null) ? "" : $request['P_TELP'],
                'EMAIL'     => ($request['EMAIL'] == null) ? "" : $request['EMAIL'],
                'P_ALMT'    => ($request['P_ALMT'] == null) ? "" : $request['P_ALMT'],
                'P_KOTA'    => ($request['P_KOTA'] == null) ? "" : $request['P_KOTA'],
                'G_ALMT'    => ($request['G_ALMT'] == null) ? "" : $request['G_ALMT'],
                'R_ALMT'    => ($request['R_ALMT'] == null) ? "" : $request['R_ALMT'],
                'P_TLP'     => ($request['P_TLP'] == null) ? "" : $request['P_TLP'],
                'P_FAX'     => ($request['P_FAX'] == null) ? "" : $request['P_FAX'],
                'R_TLP'     => ($request['R_TLP'] == null) ? "" : $request['R_TLP'],
                'P_POS'     => ($request['P_POS'] == null) ? "" : $request['P_POS'],
                'TGL_M'     => date('Y-m-d', strtotime($request['TGL_M'])),
                'UPD_TGL'   => date('Y-m-d', strtotime($request['UPD_TGL'])),
                'TGL_PNG'   => date('Y-m-d', strtotime($request['TGL_PNG'])),
                'TGL_K'     => date('Y-m-d', strtotime($request['TGL_K'])),
                'KET_PRB'   => ($request['KET_PRB'] == null) ? "" : $request['KET_PRB'],
                'B_BANK'    => ($request['B_BANK'] == null) ? "" : $request['B_BANK'],
                'NPWP'      => ($request['NPWP'] == null) ? "" : $request['NPWP'],
                'B_KOTA'    => ($request['B_KOTA'] == null) ? "" : $request['B_KOTA'],
                'NM_NPWP'   => ($request['NM_NPWP'] == null) ? "" : $request['NM_NPWP'],
                'B_NAMA'    => ($request['B_NAMA'] == null) ? "" : $request['B_NAMA'],
                'NPPKPP'    => ($request['NPPKPP'] == null) ? "" : $request['NPPKPP'],
                'B_ACC'     => ($request['B_ACC'] == null) ? "" : $request['B_ACC'],
                'AL_MPWP'   => ($request['AL_MPWP'] == null) ? "" : $request['AL_MPWP'],
                'CARA'      => ($request['CARA'] == null) ? "" : $request['CARA'],
                'KT_NPWP'   => ($request['KT_NPWP'] == null) ? "" : $request['KT_NPWP'],
                'ADA_CNTP'  => ($request['ADA_CNTP'] == null) ? "" : $request['ADA_CNTP'],
                'BAY'       => (float) str_replace(',', '', $request['BAY']),
                'BAN'       => (float) str_replace(',', '', $request['BAN']),
                'C_SP'      => ($request['C_SP'] == null) ? "" : $request['C_SP'],

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


        //return redirect('/sup/edit/?idx=' . $sup->NO_ID . '&tipx=edit');
		return redirect('/sup')->with('statusInsert', 'Data baru berhasil diupdate');
				
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, Sup $sup)
    {
        $deleteSup = Sup::find($sup->NO_ID);
        $deleteSup->delete();

        return redirect('/sup')->with('status', 'Data berhasil dihapus');
    }

    public function ceksup(Request $request)
    {
        $getItem = DB::SELECT('select count(*) as ADA from supbsn where KODES ="' . $request->KODES . '"');

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
        
        $hasil = DB::SELECT("SELECT KODES, NAMAS from supbsn WHERE (KODES LIKE '%$search%' or NAMAS LIKE '%$search%') ORDER BY KODES LIMIT $xa,$perPage ");
        $selectajax = array();
        foreach ($hasil as $row => $value) {
            $selectajax[] = array(
                'id' => $hasil[$row]->KODES,
                'text' => $hasil[$row]->KODES,
                'namas' => $hasil[$row]->NAMAS,
            );
        }
        $select['total_count'] =  count($selectajax);
        $select['items'] = $selectajax;
        return response()->json($select);
    }
}
