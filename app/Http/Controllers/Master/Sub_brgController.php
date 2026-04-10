<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Sub_brg;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

class Sub_brgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_sub_brg.index');
    }


    public function browse(Request $request)
    {


    	if (!empty(request('q'))) {


            $sub_brg = DB::SELECT("SELECT NO_ID, SUB, KELOMPOK, DEPT
                            FROM nwaotprice
                            WHERE  SUB LIKE ('%$request->q%')
                            ORDER BY SUB ");


        } else {
			$sub_brg = DB::SELECT("SELECT NO_ID, SUB, KELOMPOK, DEPT
                            FROM nwaotprice
                            ORDER BY SUB ");
		}

        return response()->json($sub_brg);
    }

    public function browse_th(Request $request)
    {
        $sub_brg = DB::SELECT("SELECT SUB, KELOMPOK, DEPT, NAMAS FROM nwaotprice ORDER BY SUB ");

        return response()->json($sub_brg);
    }


    public function getSub_brg( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $sub_brg = DB::SELECT("SELECT NO_ID, SUB, KELOMPOK, DEPT
                        from nwaotprice
                        ORDER BY SUB ");

        return Datatables::of($sub_brg)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan")
                {
                    // url untuk delete di index
                    $url = "'".url("sub_brg/delete/" . $row->NO_ID )."'";
                    // batas

                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a class="dropdown-item" href="sub_brg/edit/?idx=' . $row->NO_ID . '&tipx=edit";                                <i class="fas fa-edit"></i>
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
                'SUB'       => 'required'
            ]
        );

        // Insert Header

        $query = DB::table('nwaotprice')->select('SUB')->orderByDesc('SUB')->limit(1)->get();


        $sub_brg = Sub_brg::create(
            [
                'SUB'       => ($request['SUB'] == null) ? "" : $request['SUB'],
                'KELOMPOK'  => ($request['KELOMPOK'] == null) ? "" : $request['KELOMPOK'],
                'DEPT'      => ($request['DEPT'] == null) ? "" : $request['DEPT'],
                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['SUB'];

		$sub_brg = Sub_brg::where('SUB', $kodesx )->first();

        //return redirect('/sub_brg/edit/?idx=' . $sub_brg->NO_ID . '&tipx=edit')->with('statusInsert', 'Data baru berhasil ditambahkan');
		return redirect('/sub_brg')->with('statusInsert', 'Data baru berhasil ditambahkan');


    }



    public function edit(Request $request ,  Sub_brg $sub_brg)
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

		   $bingco = DB::SELECT("SELECT NO_ID, SUB from nwaotprice
		                 where SUB = '$kodex'
		                 ORDER BY SUB ASC  LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, SUB from nwaotprice
		                 ORDER BY SUB ASC  LIMIT 1" );

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

		   $bingco = DB::SELECT("SELECT NO_ID, SUB from nwaotprice
		             where SUB <
					 '$kodex' ORDER BY SUB DESC LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, SUB from nwaotprice
		             where SUB >
					 '$kodex' ORDER BY SUB ASC LIMIT 1" );

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

    		$bingco = DB::SELECT("SELECT NO_ID, SUB from nwaotprice
		              ORDER BY SUB DESC  LIMIT 1" );

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
			$sub_brg = Sub_brg::where('NO_ID', $idx )->first();
	     }
		 else
		 {
             $sub_brg = new Sub_brg;
		 }

		 $data = [
						'header' => $sub_brg,
			        ];
			return view('master_sub_brg.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ])->with(['pilihbank' => $pilihbank]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sub_brg $sub_brg)
    {

        $this->validate(
            $request,
            [
                'SUB'       => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;

        $sub_brg->update(
            [

                'KELOMPOK'  => ($request['KELOMPOK'] == null) ? "" : $request['KELOMPOK'],
                'DEPT'      => ($request['DEPT'] == null) ? "" : $request['DEPT'],

                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
            ]
        );


        //return redirect('/sub_brg/edit/?idx=' . $sub_brg->NO_ID . '&tipx=edit');
		return redirect('/sub_brg')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, Sub_brg $sub_brg)
    {
        $deleteCounter = Sub_brg::find($sub_brg->NO_ID);
        $deleteCounter->delete();

        return redirect('/sub_brg')->with('status', 'Data berhasil dihapus');
    }

    public function ceksub_brg(Request $request)
    {
        $getItem = DB::SELECT('select count(*) as ADA from nwaotprice where SUB ="' . $request->SUB . '"');

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

        $hasil = DB::SELECT("SELECT SUB, NAMAS from nwaotprice WHERE (SUB LIKE '%$search%' or NAMAS LIKE '%$search%') ORDER BY SUB LIMIT $xa,$perPage ");
        $selectajax = array();
        foreach ($hasil as $row => $value) {
            $selectajax[] = array(
                'id' => $hasil[$row]->SUB,
                'text' => $hasil[$row]->SUB,
                'namas' => $hasil[$row]->NAMAS,
            );
        }
        $select['total_count'] =  count($selectajax);
        $select['items'] = $selectajax;
        return response()->json($select);
    }
}
