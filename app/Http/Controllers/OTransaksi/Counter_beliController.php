<?php

namespace App\Http\Controllers\Otransaksi;

use App\Http\Controllers\Controller;
use App\Models\Master\Counter;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

class Counter_beliController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('otransaksi_counter_beli.index');
    }


    public function browse(Request $request)
    {


    	if (!empty(request('q'))) {


                 $counter_beli = DB::SELECT("SELECT NO_ID, CNT, NA_CNT, SUP, NAMAS
                            FROM cntbsn
                            WHERE  NA_CNT LIKE ('%$request->q%')
                            ORDER BY NA_CNT ");


        } else {
			$counter_beli = DB::SELECT("SELECT NO_ID, CNT, NA_CNT, SUP, NAMAS
                            FROM cntbsn
                            ORDER BY NA_CNT ");
		}

        return response()->json($counter_beli);
    }

    public function browse_th(Request $request)
    {
        $counter_beli = DB::SELECT("SELECT CNT, NA_CNT, SUP, NAMAS FROM cntbsn ORDER BY CNT ");

        return response()->json($counter_beli);
    }


    public function getCounter_beli( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $counter_beli = DB::SELECT("SELECT NO_ID, CNT, NA_CNT, SUP, NAMAS, SC_CNT, IF(ST_CNT='K','KONSINYASI','PUTUS') AS ST_CNT, IF(ST_NOTA='J','JUAL','BELI') ST_NOTA,
                            ST_PJK, ST_ORD, IF(CBAYAR='C','CEK',IF(CBAYAR='G','GIRO',IF(CBAYAR='T','TRANSFER',IF(CBAYAR='A','A,AMBIL','')))) AS CBAYAR,
                            LBAYAR, MARGIN, POT_PROM, `BASIC`, KEL_PT
                        from cntbsn
                        ORDER BY CNT ");

        return Datatables::of($counter_beli)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan")
                {
                    // url untuk delete di index
                    $url = "'".url("counter_beli/delete/" . $row->NO_ID )."'";
                    // batas

                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a class="dropdown-item" href="counter_beli/edit/?idx=' . $row->NO_ID . '&tipx=edit";                                <i class="fas fa-edit"></i>
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

        $query = DB::table('cntbsn')->select('CNT')->orderByDesc('CNT')->limit(1)->get();


        $counter_beli = Counter::create(
            [
                'CNT'     => ($request['CNT'] == null) ? "" : $request['CNT'],
                'NA_CNT'     => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'SUP'      => ($request['SUP'] == null) ? "" : $request['SUP'],
                'NAMAS'      => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'LS_CNT'   => ($request['LS_CNT'] == null) ? "" : $request['LS_CNT'],
                'DIS_CUST'    => ($request['DIS_CUST'] == null) ? "" : $request['DIS_CUST'],
                'JN_CNT'     => ($request['JN_CNT'] == null) ? "" : $request['JN_CNT'],
                'DIS_TGLM'     => date('Y-m-d', strtotime($request['DIS_TGLMP'])),
                'DIS_TGLS'   => date('Y-m-d', strtotime($request['DIS_TGLSP'])),
                'ST_CNT'    => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'DIS_KHS'    => ($request['DIS_KHS'] == null) ? "" : $request['DIS_KHS'],
                'DIS_STS'    => ($request['DIS_STS'] == null) ? "" : $request['DIS_STS'],
                'SC_CNT'     => ($request['SC_CNT'] == null) ? "" : $request['SC_CNT'],
                'MARGIN'     => ($request['MARGIN'] == null) ? "" : $request['MARGIN'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'ST_NOTA'     => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'POT_PROM'     => ($request['POT_PROM'] == null) ? "" : $request['POT_PROM'],
                'ST_ORD'     => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'KEL_PT'     => ($request['KEL_PT'] == null) ? "" : $request['KEL_PT'],
                'ST_PJK'     => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'PER_NON'     => ($request['PER_NON'] == null) ? "" : $request['PER_NON'],
                'CBAYAR'     => ($request['CBAYAR'] == null) ? "" : $request['CBAYAR'],
                // 'AKTIF'     => ($request['AKTIF'] == null) ? "" : $request['AKTIF'],
                // 'BUKAC'   => date('Y-m-d', strtotime($request['BUKAC'])),
                // 'TUTUPC'     => date('Y-m-d', strtotime($request['TUTUPC'])),
                'LBAYAR'   => ($request['LBAYAR'] == null) ? "" : $request['LBAYAR'],
                // 'BLOKIR'    => ($request['BLOKIR'] == null) ? "" : $request['BLOKIR'],
                'KW_RET'      => ($request['KW_RET'] == null) ? "" : $request['KW_RET'],
                // 'BLOKIRB'     => date('Y-m-d', strtotime($request['BLOKIRB'])),
                'KW_LBL'    => ($request['KW_LBL'] == null) ? "" : $request['KW_LBL'],
                'ST_CNT'    => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'ST_ORD'    => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'ST_PJK'    => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                // 'PER_MIN'    => ($request['PER_MIN'] == null) ? "" : $request['PER_MIN'],
                // 'B_MIN'   => ($request['B_MIN'] == null) ? "" : $request['B_MIN'],
                // 'KALIB'    => ($request['KALIB'] == null) ? "" : $request['KALIB'],
                'QBU'     => (float) str_replace(',', '', $request['QBU']),
                'NBU'     => (float) str_replace(',', '', $request['NBU']),
                'QOL'     => (float) str_replace(',', '', $request['QOL']),
                'NOL'     => (float) str_replace(',', '', $request['NOL']),
                'QSB'     => (float) str_replace(',', '', $request['QSB']),
                'NSB'     => (float) str_replace(',', '', $request['NSB']),
                'QST'     => (float) str_replace(',', '', $request['QST']),
                'NST'     => (float) str_replace(',', '', $request['NST']),
                'QBL'     => (float) str_replace(',', '', $request['QBL']),
                'NBL'     => (float) str_replace(',', '', $request['NBL']),

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['CNT'];

		$counter_beli = Counter::where('CNT', $kodesx )->first();

        //return redirect('/counter_beli/edit/?idx=' . $counter_beli->NO_ID . '&tipx=edit')->with('statusInsert', 'Data baru berhasil ditambahkan');
		return redirect('/counter_beli')->with('statusInsert', 'Data baru berhasil ditambahkan');


    }



    public function edit(Request $request ,  Counter $counter_beli)
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

		   $bingco = DB::SELECT("SELECT NO_ID, CNT from cntbsn
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

		   $bingco = DB::SELECT("SELECT NO_ID, CNT from cntbsn
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

		   $bingco = DB::SELECT("SELECT NO_ID, CNT from cntbsn
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

		   $bingco = DB::SELECT("SELECT NO_ID, CNT from cntbsn
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

    		$bingco = DB::SELECT("SELECT NO_ID, CNT from cntbsn
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
			$counter_beli = Counter::where('NO_ID', $idx )->first();
	     }
		 else
		 {
             $counter_beli = new Counter;
		 }

		 $data = [
						'header' => $counter_beli,
			    ];
		return view('otransaksi_counter_beli.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ])->with(['pilihbank' => $pilihbank]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Counter $counter_beli)
    {

        $this->validate(
            $request,
            [
                'CNT'       => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;

        $counter_beli->update(
            [

                'NA_CNT'     => ($request['NA_CNT'] == null) ? "" : $request['NA_CNT'],
                'SUP'      => ($request['SUP'] == null) ? "" : $request['SUP'],
                'NAMAS'      => ($request['NAMAS'] == null) ? "" : $request['NAMAS'],
                'LS_CNT'   => ($request['LS_CNT'] == null) ? "" : $request['LS_CNT'],
                'DIS_CUST'    => ($request['DIS_CUST'] == null) ? "" : $request['DIS_CUST'],
                'JN_CNT'     => ($request['JN_CNT'] == null) ? "" : $request['JN_CNT'],
                'DIS_TGLM'     => date('Y-m-d', strtotime($request['DIS_TGLMP'])),
                'DIS_TGLS'   => date('Y-m-d', strtotime($request['DIS_TGLSP'])),
                'ST_CNT'    => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'DIS_KHS'    => ($request['DIS_KHS'] == null) ? "" : $request['DIS_KHS'],
                'DIS_STS'    => ($request['DIS_STS'] == null) ? "" : $request['DIS_STS'],
                'SC_CNT'     => ($request['SC_CNT'] == null) ? "" : $request['SC_CNT'],
                'MARGIN'     => ($request['MARGIN'] == null) ? "" : $request['MARGIN'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'ST_NOTA'     => ($request['ST_NOTA'] == null) ? "" : $request['ST_NOTA'],
                'POT_PROM'     => ($request['POT_PROM'] == null) ? "" : $request['POT_PROM'],
                'ST_ORD'     => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'KEL_PT'     => ($request['KEL_PT'] == null) ? "" : $request['KEL_PT'],
                'ST_PJK'     => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                'PER_NON'     => ($request['PER_NON'] == null) ? "" : $request['PER_NON'],
                'CBAYAR'     => ($request['CBAYAR'] == null) ? "" : $request['CBAYAR'],
                // 'AKTIF'     => ($request['AKTIF'] == null) ? "" : $request['AKTIF'],
                // 'BUKAC'   => date('Y-m-d', strtotime($request['BUKAC'])),
                // 'TUTUPC'     => date('Y-m-d', strtotime($request['TUTUPC'])),
                'LBAYAR'   => ($request['LBAYAR'] == null) ? "" : $request['LBAYAR'],
                // 'BLOKIR'    => ($request['BLOKIR'] == null) ? "" : $request['BLOKIR'],
                'KW_RET'      => ($request['KW_RET'] == null) ? "" : $request['KW_RET'],
                // 'BLOKIRB'     => date('Y-m-d', strtotime($request['BLOKIRB'])),
                'KW_LBL'    => ($request['KW_LBL'] == null) ? "" : $request['KW_LBL'],
                'ST_CNT'    => ($request['ST_CNT'] == null) ? "" : $request['ST_CNT'],
                'ST_ORD'    => ($request['ST_ORD'] == null) ? "" : $request['ST_ORD'],
                'ST_PJK'    => ($request['ST_PJK'] == null) ? "" : $request['ST_PJK'],
                // 'PER_MIN'    => ($request['PER_MIN'] == null) ? "" : $request['PER_MIN'],
                // 'B_MIN'   => ($request['B_MIN'] == null) ? "" : $request['B_MIN'],
                // 'KALIB'    => ($request['KALIB'] == null) ? "" : $request['KALIB'],
                'QBU'     => (float) str_replace(',', '', $request['QBU']),
                'NBU'     => (float) str_replace(',', '', $request['NBU']),
                'QOL'     => (float) str_replace(',', '', $request['QOL']),
                'NOL'     => (float) str_replace(',', '', $request['NOL']),
                'QSB'     => (float) str_replace(',', '', $request['QSB']),
                'NSB'     => (float) str_replace(',', '', $request['NSB']),
                'QST'     => (float) str_replace(',', '', $request['QST']),
                'NST'     => (float) str_replace(',', '', $request['NST']),
                'QBL'     => (float) str_replace(',', '', $request['QBL']),
                'NBL'     => (float) str_replace(',', '', $request['NBL']),

                'USRNM'     => Auth::user()->username,
                'TG_SMP'    => Carbon::now()
            ]
        );


        //return redirect('/counter_beli/edit/?idx=' . $counter_beli->NO_ID . '&tipx=edit');
		return redirect('/counter_beli')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, Counter $counter_beli)
    {
        $deleteCounter_beli = Counter::find($counter_beli->NO_ID);
        $deleteCounter_beli->delete();

        return redirect('/counter_beli')->with('status', 'Data berhasil dihapus');
    }

    public function cekcounter_beli(Request $request)
    {
        $getItem = DB::SELECT('select count(*) as ADA from cntbsn where CNT ="' . $request->CNT . '"');

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

        $hasil = DB::SELECT("SELECT CNT, NAMAS from cntbsn WHERE (CNT LIKE '%$search%' or NAMAS LIKE '%$search%') ORDER BY CNT LIMIT $xa,$perPage ");
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
