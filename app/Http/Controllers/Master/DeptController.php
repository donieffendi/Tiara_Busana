<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Dept;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

class DeptController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('master_dept.index');
    }


    public function browse(Request $request)
    {


    	if (!empty(request('q'))) {


            $dept = DB::SELECT("SELECT NO_ID, kd_dept, nama
                            FROM dept
                            WHERE  kd_dept LIKE ('%$request->q%')
                            ORDER BY kd_dept ");


        } else {
			$dept = DB::SELECT("SELECT NO_ID, kd_dept, nama
                            FROM dept
                            ORDER BY kd_dept ");
		}

        return response()->json($dept);
    }

    public function browse_th(Request $request)
    {
        $dept = DB::SELECT("SELECT kd_dept, nama FROM dept ORDER BY kd_dept ");

        return response()->json($dept);
    }


    public function getDept( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $dept = DB::SELECT("SELECT NO_ID, kd_dept, nama
                        from dept
                        ORDER BY kd_dept ");

        return Datatables::of($dept)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi=="programmer" || Auth::user()->divisi=="owner" || Auth::user()->divisi=="assistant" || Auth::user()->divisi=="accounting" || Auth::user()->divisi=="pembelian" || Auth::user()->divisi=="penjualan")
                {
                    // url untuk delete di index
                    $url = "'".url("dept/delete/" . $row->NO_ID )."'";
                    // batas

                    $btnDelete = '';
                    //' onclick="deleteRow('.$url.')"';

                    $btnPrivilege =
                        '
                                <a class="dropdown-item" href="dept/edit/?idx=' . $row->NO_ID . '&tipx=edit";                                <i class="fas fa-edit"></i>
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
                'kd_dept'       => 'required'
            ]
        );

        // Insert Header

        $query = DB::table('dept')->select('kd_dept')->orderByDesc('kd_dept')->limit(1)->get();


        $dept = Dept::create(
            [
                'kd_dept'       => ($request['kd_dept'] == null) ? "" : $request['kd_dept'],
                'nama'  => ($request['nama'] == null) ? "" : $request['nama'],
                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['kd_dept'];

		$dept = Dept::where('kd_dept', $kodesx )->first();

        //return redirect('/dept/edit/?idx=' . $dept->NO_ID . '&tipx=edit')->with('statusInsert', 'Data baru berhasil ditambahkan');
		return redirect('/dept')->with('statusInsert', 'Data baru berhasil ditambahkan');


    }



    public function edit(Request $request ,  Dept $dept)
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

		   $bingco = DB::SELECT("SELECT NO_ID, kd_dept from dept
		                 where kd_dept = '$kodex'
		                 ORDER BY kd_dept ASC  LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, kd_dept from dept
		                 ORDER BY kd_dept ASC  LIMIT 1" );

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

		   $bingco = DB::SELECT("SELECT NO_ID, kd_dept from dept
		             where kd_dept <
					 '$kodex' ORDER BY kd_dept DESC LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, kd_dept from dept
		             where kd_dept >
					 '$kodex' ORDER BY kd_dept ASC LIMIT 1" );

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

    		$bingco = DB::SELECT("SELECT NO_ID, kd_dept from dept
		              ORDER BY kd_dept DESC  LIMIT 1" );

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
			$dept = Dept::where('NO_ID', $idx )->first();
	     }
		 else
		 {
             $dept = new Dept;
		 }

		 $data = [
                    'header' => $dept,
                ];
			return view('master_dept.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ])->with(['pilihbank' => $pilihbank]);


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dept $dept)
    {

        $this->validate(
            $request,
            [
                'kd_dept'       => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;

        $dept->update(
            [

                'nama'  => ($request['nama'] == null) ? "" : $request['nama'],

                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
            ]
        );


        //return redirect('/dept/edit/?idx=' . $dept->NO_ID . '&tipx=edit');
		return redirect('/dept')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */
    public function destroy( Request $request, Dept $dept)
    {
        $deleteCounter = Dept::find($dept->NO_ID);
        $deleteCounter->delete();

        return redirect('/dept')->with('status', 'Data berhasil dihapus');
    }

    public function cekdept(Request $request)
    {
        $getItem = DB::SELECT('select count(*) as ADA from dept where kd_dept ="' . $request->kd_dept . '"');

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

        $hasil = DB::SELECT("SELECT kd_dept, NAMAS from dept WHERE (kd_dept LIKE '%$search%' or NAMAS LIKE '%$search%') ORDER BY kd_dept LIMIT $xa,$perPage ");
        $selectajax = array();
        foreach ($hasil as $row => $value) {
            $selectajax[] = array(
                'id' => $hasil[$row]->kd_dept,
                'text' => $hasil[$row]->kd_dept,
                'namas' => $hasil[$row]->NAMAS,
            );
        }
        $select['total_count'] =  count($selectajax);
        $select['items'] = $selectajax;
        return response()->json($select);
    }
}
