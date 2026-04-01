<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Sup;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

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


                 $sup = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA, ALAMAT, KOTA, NOTBAY, KONTAK, AKTIF, CASE WHEN PKP = '1' THEN '(PKP)' ELSE '(NON PKP)' END AS PKP2,
                            PKP, HARI
                            from nwmassup
                            WHERE  NAMA LIKE ('%$request->q%')
                            ORDER BY NAMA ");


        } else {
			$sup = DB::SELECT("SELECT NO_ID, NO_SUPL, NAMA, ALAMAT, KOTA, NOTBAY, KONTAK, AKTIF, CASE WHEN PKP = '1' THEN '(PKP)' ELSE '(NON PKP)' END AS PKP2,
                                PKP, HARI
                            from nwmassup

                            ORDER BY NAMA ");
		}

        return response()->json($sup);
    }


    public function browse_amplop(Request $request)
    {
        // $sup = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS, ALMT_K AS ALAMAT, KOTA, TLP_K AS TELP FROM nwmassup ORDER BY NO_SUPL ");
        $sup = DB::SELECT("SELECT KODES, NAMAS, P_ALMT AS ALAMAT, P_KOTA AS KOTA, P_TLP AS TELP FROM supbsn ORDER BY KODES ");

        return response()->json($sup);
    }

    public function browse_stegur(Request $request)
    {
        $sup = DB::SELECT("SELECT NO_SUPL AS KODES, NAMA AS NAMAS, ALMT_K AS ALAMAT, KOTA, TLP_K AS TELP FROM nwmassup ORDER BY NO_SUPL ");
        // $sup = DB::SELECT("SELECT KODES, NAMAS, P_ALMT AS ALAMAT, P_KOTA AS KOTA, P_TLP AS TELP FROM supbsn ORDER BY KODES ");

        return response()->json($sup);
    }

    public function getSup( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $sup = DB::SELECT("SELECT * from nwmassup ORDER BY NO_SUPL ASC");

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
                'NO_SUPL'       => 'required',
                'NAMA'       => 'required'
            ]
        );

        // Insert Header

        $query = DB::table('nwmassup')->select('NO_SUPL')->orderByDesc('NO_SUPL')->limit(1)->get();


        $sup = Sup::create(
            [
                'NO_SUPL'     => ($request['NO_SUPL'] == null) ? "" : $request['NO_SUPL'],
                'NAMA'     => ($request['NAMA'] == null) ? "" : $request['NAMA'],
                'ALMT_K'      => ($request['ALMT_K'] == null) ? "" : $request['ALMT_K'],
                'P_TLP'    => ($request['P_TLP'] == null) ? "" : $request['P_TLP'],
                'TLP_K'     => ($request['TLP_K'] == null) ? "" : $request['TLP_K'],
                'NO_FAX'    => ($request['NO_FAX'] == null) ? "" : $request['NO_FAX'],
                'NO_TELEX'    => ($request['NO_TELEX'] == null) ? "" : $request['NO_TELEX'],
                'ALMT_GD'    => ($request['ALMT_GD'] == null) ? "" : $request['ALMT_GD'],
                'PEMILIK'    => ($request['PEMILIK'] == null) ? "" : $request['PEMILIK'],
                'ALMT_R'     => ($request['ALMT_R'] == null) ? "" : $request['ALMT_R'],
                'TLP_R'     => ($request['TLP_R'] == null) ? "" : $request['TLP_R'],
                'NO_REK'     => ($request['NO_REK'] == null) ? "" : $request['NO_REK'],
                'NAMA_B'     => ($request['NAMA_B'] == null) ? "" : $request['NAMA_B'],
                'KOTA_B'     => ($request['KOTA_B'] == null) ? "" : $request['KOTA_B'],
                'AN_B'     => ($request['AN_B'] == null) ? "" : $request['AN_B'],
                'GOL_BRG'     => ($request['GOL_BRG'] == null) ? "" : $request['GOL_BRG'],
                'JEN_BRG1'     => ($request['JEN_BRG1'] == null) ? "" : $request['JEN_BRG1'],
                'BUDGET_AWL'       => (float) str_replace(',', '', $request['BUDGET_AWL']),
                'STM_PEMBL'     => ($request['STM_PEMBL'] == null) ? "" : $request['STM_PEMBL'],
                'KD_PEMBY'     => ($request['KD_PEMBY'] == null) ? "" : $request['KD_PEMBY'],
                'CARA'     => ($request['CARA'] == null) ? "" : $request['CARA'],
                'BY'       => (float) str_replace(',', '', $request['BY']),
                'BG_PERS'     => ($request['BG_PERS'] == null) ? "" : $request['BG_PERS'],
                'DISC_PS'       => (float) str_replace(',', '', $request['DISC_PS']),
                'ORDER'     => ($request['ORDER'] == null) ? "" : $request['ORDER'],
                'STATUSNYA'     => ($request['STATUSNYA'] == null) ? "" : $request['STATUSNYA'],
                'GOLONGAN'     => ($request['GOLONGAN'] == null) ? "" : $request['GOLONGAN'],
                'KOD_MIN'     => ($request['KOD_MIN'] == null) ? "" : $request['KOD_MIN'],
                'DIS_A'       => (float) str_replace(',', '', $request['DIS_A']),
                'DIS_B'       => (float) str_replace(',', '', $request['DIS_B']),
                'DIS_C'       => (float) str_replace(',', '', $request['DIS_C']),
                'PPN'       => (float) str_replace(',', '', $request['PPN']),
                'BEBAN'       => (float) str_replace(',', '', $request['BEBAN']),
                'ACC'     => ($request['ACC'] == null) ? "" : $request['ACC'],
                'PMSR_PROD'     => ($request['PMSR_PROD'] == null) ? "" : $request['PMSR_PROD'],
                'DEPT'      => ($request['DEPT'] == null) ? "" : $request['DEPT'],
                // 'TGL_M'     => date('Y-m-d', strtotime($request['TGL_M'])),

                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
            ]
        );


	    $kodesx = $request['NO_SUPL'];

		$sup = Sup::where('NO_SUPL', $kodesx )->first();

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_SUPL from nwmassup
		                 where NO_SUPL = '$kodex'
		                 ORDER BY NO_SUPL ASC  LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_SUPL from nwmassup
		                 ORDER BY NO_SUPL ASC  LIMIT 1" );

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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_SUPL from nwmassup
		             where NO_SUPL <
					 '$kodex' ORDER BY NO_SUPL DESC LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, NO_SUPL from nwmassup
		             where NO_SUPL >
					 '$kodex' ORDER BY NO_SUPL ASC LIMIT 1" );

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

    		$bingco = DB::SELECT("SELECT NO_ID, NO_SUPL from nwmassup
		              ORDER BY NO_SUPL DESC  LIMIT 1" );

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
                'NO_SUPL'       => 'required',
                // 'NAMA'      => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;

        $sup->update(
            [

                'NAMA'     => ($request['NAMA'] == null) ? "" : $request['NAMA'],
                'ALMT_K'      => ($request['ALMT_K'] == null) ? "" : $request['ALMT_K'],
                'P_TLP'    => ($request['P_TLP'] == null) ? "" : $request['P_TLP'],
                'TLP_K'     => ($request['TLP_K'] == null) ? "" : $request['TLP_K'],
                'NO_FAX'    => ($request['NO_FAX'] == null) ? "" : $request['NO_FAX'],
                'NO_TELEX'    => ($request['NO_TELEX'] == null) ? "" : $request['NO_TELEX'],
                'ALMT_GD'    => ($request['ALMT_GD'] == null) ? "" : $request['ALMT_GD'],
                'PEMILIK'    => ($request['PEMILIK'] == null) ? "" : $request['PEMILIK'],
                'ALMT_R'     => ($request['ALMT_R'] == null) ? "" : $request['ALMT_R'],
                'TLP_R'     => ($request['TLP_R'] == null) ? "" : $request['TLP_R'],
                'NO_REK'     => ($request['NO_REK'] == null) ? "" : $request['NO_REK'],
                'NAMA_B'     => ($request['NAMA_B'] == null) ? "" : $request['NAMA_B'],
                'KOTA_B'     => ($request['KOTA_B'] == null) ? "" : $request['KOTA_B'],
                'AN_B'     => ($request['AN_B'] == null) ? "" : $request['AN_B'],
                'GOL_BRG'     => ($request['GOL_BRG'] == null) ? "" : $request['GOL_BRG'],
                'JEN_BRG1'     => ($request['JEN_BRG1'] == null) ? "" : $request['JEN_BRG1'],
                'BUDGET_AWL'       => (float) str_replace(',', '', $request['BUDGET_AWL']),
                'STM_PEMBL'     => ($request['STM_PEMBL'] == null) ? "" : $request['STM_PEMBL'],
                'KD_PEMBY'     => ($request['KD_PEMBY'] == null) ? "" : $request['KD_PEMBY'],
                'CARA'     => ($request['CARA'] == null) ? "" : $request['CARA'],
                'BY'       => (float) str_replace(',', '', $request['BY']),
                'BG_PERS'     => ($request['BG_PERS'] == null) ? "" : $request['BG_PERS'],
                'DISC_PS'       => (float) str_replace(',', '', $request['DISC_PS']),
                'ORDER'     => ($request['ORDER'] == null) ? "" : $request['ORDER'],
                'STATUSNYA'     => ($request['STATUSNYA'] == null) ? "" : $request['STATUSNYA'],
                'GOLONGAN'     => ($request['GOLONGAN'] == null) ? "" : $request['GOLONGAN'],
                'KOD_MIN'     => ($request['KOD_MIN'] == null) ? "" : $request['KOD_MIN'],
                'DIS_A'       => (float) str_replace(',', '', $request['DIS_A']),
                'DIS_B'       => (float) str_replace(',', '', $request['DIS_B']),
                'DIS_C'       => (float) str_replace(',', '', $request['DIS_C']),
                'PPN'       => (float) str_replace(',', '', $request['PPN']),
                'BEBAN'       => (float) str_replace(',', '', $request['BEBAN']),
                'ACC'     => ($request['ACC'] == null) ? "" : $request['ACC'],
                'PMSR_PROD'     => ($request['PMSR_PROD'] == null) ? "" : $request['PMSR_PROD'],
                'DEPT'      => ($request['DEPT'] == null) ? "" : $request['DEPT'],

                // 'USRNM'     => Auth::user()->username,
                // 'TG_SMP'    => Carbon::now()
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
        $getItem = DB::SELECT('select count(*) as ADA from nwmassup where NO_SUPL ="' . $request->NO_SUPL . '"');

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

        $hasil = DB::SELECT("SELECT NO_SUPL, NAMA from nwmassup WHERE (NO_SUPL LIKE '%$search%' or NAMA LIKE '%$search%') ORDER BY NO_SUPL LIMIT $xa,$perPage ");
        $selectajax = array();
        foreach ($hasil as $row => $value) {
            $selectajax[] = array(
                'id' => $hasil[$row]->NO_SUPL,
                'text' => $hasil[$row]->NO_SUPL,
                'namas' => $hasil[$row]->NAMA,
            );
        }
        $select['total_count'] =  count($selectajax);
        $select['items'] = $selectajax;
        return response()->json($select);
    }

    public function Print(Request $request)
    {
        // Ambil filter range
        $kodes1  = $request->input('kodes1');
        $kodes2  = $request->input('kodes2');

        // Nama file laporan Jasper
        $file = 'Daftar_Supplier'; // ubah sesuai nama file .jrxml kamu, misalnya 'brg_list.jrxml'
        $PHPJasperXML = new \PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        // === Query utama (sesuai dengan query DataTables kamu) ===
        $query = DB::table('nwmassup')
            ->select(
                'NO_SUPL',
                'NAMA',
                'ALMT_K'
            );

        // Filter SUB BETWEEN
        if (!empty($kodes1) && !empty($kodes2)) {
            $query->whereBetween('NO_SUPL', [$kodes1, $kodes2]);
        }

        $result = $query->orderBy('NO_SUPL')->get();

        // === Konversi hasil ke array untuk Jasper ===
        $data = [];
        
        $data = json_decode(json_encode($result), true);

        // Kirim data ke Jasper
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I"); // "I" artinya inline (tampil di browser)
    }
}
