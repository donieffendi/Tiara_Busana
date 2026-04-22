<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Brg;
use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";

use PHPJasperXML;

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

    public function browse_sup(Request $request)
    {
        $brg = DB::SELECT("SELECT NO_SUPL AS SUPP, NAMA FROM nwmassup ORDER BY NO_SUPL ");

        return response()->json($brg);
    }

    public function browse_sub(Request $request)
    {
        $brg = DB::SELECT("SELECT SUB, KELOMPOK, DEPT FROM nwaotprice ORDER BY SUB ");

        return response()->json($brg);
    }

    public function get_sub(Request $request)
    {
        $SUB = $request->SUB;
        $brg = DB::SELECT("SELECT SUB, KELOMPOK, DEPT FROM nwaotprice where SUB = '$SUB' ORDER BY SUB ");

        return response()->json($brg);
    }

    public function browse_event(Request $request)
    {
        $brg = DB::SELECT("SELECT KODE, NAMA FROM hraya ORDER BY KODE ");

        return response()->json($brg);
    }

    public function browse_plu(Request $request)
    {
        $brg = DB::SELECT("SELECT KDBAR, NMBAR, SUB, SUPP, KET_UK FROM nwmasbar ORDER BY KDBAR ");

        return response()->json($brg);
    }


    public function getBrg( Request $request )
    {
		// $PPN = Auth::user()->PPN;

        $dept = session()->get('periode')['dept'];
        $cabang = session()->get('periode')['cabang'];


        $brg = DB::SELECT("SELECT nwmasbar.NO_ID, nwmasbar.SUB, nwmasbar.KDBAR, nwmasbar.NMBAR, nwmasbar.ITEM_SUP, nwmasbar.KET_UK, nwmasbar.KET_KEM, nwmasbar.SUPP, nwmasbar.QTY_BELI1, nwmasbar.HB, nwmasbar.DIS_A, nwmasbar.DIS_B, nwmasbar.DIS_C, nwmasbar.TOT_BL,
                                nwaotprice.DEPT
                        from nwmasbar
						join nwaotprice on nwmasbar.SUB = nwaotprice.SUB
						where nwaotprice.DEPT = '$dept'
                        order by nwmasbar.SUB ");

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
                                <a class="dropdown-item" href="brg/edit/?idx=' . $row->NO_ID . '&tipx=edit";>
                                <i class="fas fa-edit"></i>
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

        $sub = $request->SUB;

        $last = DB::table('nwmasbar')
                ->orderByDesc('KDBAR')
                ->first();

        if($last){
            $kdbarnum = (int)$last->KDBAR + 1;
        }else{
            $kdbarnum = 1;
        }

        $KDBAR = str_pad($kdbarnum,7,'0',STR_PAD_LEFT);

        $count = DB::table('nwmasbar')
                ->where('SUB',$sub)
                ->count() + 1;

        $urutbarcode = str_pad($count,5,'0',STR_PAD_LEFT);

        if(!empty($request->input('BARCODE'))) {
            $BARCODE = $request->BARCODE;
        } else {
            $BARCODE = $KDBAR.$urutbarcode;
        }


        $brg = Brg::create(
            [
                'RAK'       => ($request['RAK'] == null) ? "" : $request['RAK'],
                'PPN'       => ($request['PPN'] == null) ? "" : $request['PPN'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'BARCODE'   => $BARCODE,
                'STM'       => ($request['STM'] == null) ? "" : $request['STM'],
                'QTY_BELI1' => (float) str_replace(',', '', $request['QTY_BELI1']),
                'KD_EVENT'  => ($request['KD_EVENT'] == null) ? "" : $request['KD_EVENT'],
                'HB'        => (float) str_replace(',', '', $request['HB']),
                'DIS_A'     => (float) str_replace(',', '', $request['DIS_A']),
                'ITEM_SUP'  => ($request['ITEM_SUP'] == null) ? "" : $request['ITEM_SUP'],
                'DIS_B'     => (float) str_replace(',', '', $request['DIS_B']),
                'SUPP'      => ($request['SUPP'] == null) ? "" : $request['SUPP'],
                'DIS_C'     => (float) str_replace(',', '', $request['DIS_C']),
                'SUB'       => ($request['SUB'] == null) ? "" : $request['SUB'],
                'KDBAR'     => $KDBAR,
                'RETUR'     => ($request['RETUR'] == null) ? "" : $request['RETUR'],
                'NMBAR'     => ($request['NMBAR'] == null) ? "" : $request['NMBAR'],
                'KET'       => ($request['KET'] == null) ? "" : $request['KET'],
                'KET_UK'    => ($request['KET_UK'] == null) ? "" : $request['KET_UK'],
                'KET_KEM'   => ($request['KET_KEM'] == null) ? "" : $request['KET_KEM'],
                'PMSR_PROD' => ($request['PMSR_PROD'] == null) ? "" : $request['PMSR_PROD']
            ]
        );

		return redirect('/brg')->with('statusInsert', 'Data baru berhasil ditambahkan');


    }



    public function edit(Request $request ,  Brg $brg)
    {


		$tipx = $request->tipx;

		$idx = $request->idx;



		if ( $idx =='0' && $tipx=='undo'  )
	    {
			$tipx ='top';

		   }


		if ($tipx=='search') {


    	   $kodex = $request->kodex;

		   $bingco = DB::SELECT("SELECT NO_ID, KDBAR from nwmasbar
		                 where KDBAR = '$kodex'
		                 ORDER BY KDBAR ASC  LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, KDBAR from nwmasbar
		                 ORDER BY KDBAR ASC  LIMIT 1" );

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

		   $bingco = DB::SELECT("SELECT NO_ID, KDBAR from nwmasbar
		             where KDBAR <
					 '$kodex' ORDER BY KDBAR DESC LIMIT 1" );


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

		   $bingco = DB::SELECT("SELECT NO_ID, KDBAR from nwmasbar
		             where KDBAR >
					 '$kodex' ORDER BY KDBAR ASC LIMIT 1" );

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

    		$bingco = DB::SELECT("SELECT NO_ID, KDBAR from nwmasbar
		              ORDER BY KDBAR DESC  LIMIT 1" );

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
			// $brg = Brg::where('NO_ID', $idx )->first();
            $brg = DB::table('nwmasbar')
                ->leftJoin('nwmassup', 'nwmasbar.SUPP', '=', 'nwmassup.NO_SUPL')
                ->leftJoin('hraya', 'nwmasbar.KD_EVENT', '=', 'hraya.KODE')
                ->select(
                    'nwmasbar.*',
                    'nwmassup.NAMA',
                    'hraya.NAMA as NM_EVENT'
                )
                ->where('nwmasbar.NO_ID', $idx)
                ->first();
	     }
		 else
		 {
             $brg = new Brg;
		 }

		 $data = [
                    'header' => $brg,
                ];
			return view('master_brg.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx ]);
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
                'SUB'       => 'required'
            ]
        );

		$tipx = 'edit';
		$idx = $request->idx;

        $brg->update(
            [
                'RAK'       => ($request['RAK'] == null) ? "" : $request['RAK'],
                'PPN'       => ($request['PPN'] == null) ? "" : $request['PPN'],
                'BASIC'     => ($request['BASIC'] == null) ? "" : $request['BASIC'],
                'BARCODE'   => ($request['BARCODE'] == null) ? "" : $request['BARCODE'],
                'STM'       => ($request['STM'] == null) ? "" : $request['STM'],
                'QTY_BELI1' => (float) str_replace(',', '', $request['QTY_BELI1']),
                'KD_EVENT'  => ($request['KD_EVENT'] == null) ? "" : $request['KD_EVENT'],
                'HB'        => (float) str_replace(',', '', $request['HB']),
                'DIS_A'     => (float) str_replace(',', '', $request['DIS_A']),
                'ITEM_SUP'  => ($request['ITEM_SUP'] == null) ? "" : $request['ITEM_SUP'],
                'DIS_B'     => (float) str_replace(',', '', $request['DIS_B']),
                'SUPP'      => ($request['SUPP'] == null) ? "" : $request['SUPP'],
                'DIS_C'     => (float) str_replace(',', '', $request['DIS_C']),
                'SUB'       => ($request['SUB'] == null) ? "" : $request['SUB'],
                'KDBAR'     => ($request['KDBAR'] == null) ? "" : $request['KDBAR'],
                'RETUR'     => ($request['RETUR'] == null) ? "" : $request['RETUR'],
                'NMBAR'     => ($request['NMBAR'] == null) ? "" : $request['NMBAR'],
                'KET'       => ($request['KET'] == null) ? "" : $request['KET'],
                'KET_UK'    => ($request['KET_UK'] == null) ? "" : $request['KET_UK'],
                'KET_KEM'   => ($request['KET_KEM'] == null) ? "" : $request['KET_KEM'],
                'PMSR_PROD' => ($request['PMSR_PROD'] == null) ? "" : $request['PMSR_PROD']
            ]
        );

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
        $getItem = DB::SELECT('select count(*) as ADA from nwmasbar where KDBAR ="' . $request->KDBAR . '"');

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

    public function Print(Request $request)
    {
        // Ambil filter range
        $sub1  = $request->input('sub1');
        $sub2  = $request->input('sub2');
        $supp1 = $request->input('supp1');
        $supp2 = $request->input('supp2');

        // Nama file laporan Jasper
        $file = 'Daftar_Barang'; // ubah sesuai nama file .jrxml kamu, misalnya 'brg_list.jrxml'
        $PHPJasperXML = new \PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        // === Query utama (sesuai dengan query DataTables kamu) ===
        $query = DB::table('nwmasbar as a')
            ->join('nwmassup as b', 'a.SUPP', '=', 'b.NO_SUPL')
            ->select(
                'a.SUB',
                'a.KDBAR',
                'a.NMBAR',
                'a.ITEM_SUP',
                'a.SUPP',
                'b.NAMA',
                'a.KET_UK',
                'a.KET_KEM',
                'a.BARCODE'
            );

        // Filter SUB BETWEEN
        if (!empty($sub1) && !empty($sub2)) {
            $query->whereBetween('a.SUB', [$sub1, $sub2]);
        }

        // Filter SUPP BETWEEN
        if (!empty($supp1) && !empty($supp2)) {
            $query->whereBetween('a.SUPP', [$supp1, $supp2]);
        }

        $result = $query->orderBy('a.SUB')->orderBy('a.KDBAR')->get();

        // === Konversi hasil ke array untuk Jasper ===
        $data = [];

        $data = json_decode(json_encode($result), true);

        // Kirim data ke Jasper
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I"); // "I" artinya inline (tampil di browser)
    }

    public function Barcode(Request $request)
    {
        // Ambil filter range
        $sub1  = $request->input('sub1');
        $sub2  = $request->input('sub2');
        $supp1 = $request->input('supp1');
        $supp2 = $request->input('supp2');
        $qty = $request->input('qty', 1);

        // Nama file laporan Jasper
        $file = 'cetakbcd'; // ubah sesuai nama file .jrxml kamu, misalnya 'brg_list.jrxml'
        $PHPJasperXML = new \PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));
        $params = [
			"TGL_CTK" => date('d/m/Y')
		];
		$PHPJasperXML->arrayParameter = $params;


        // === Query utama (sesuai dengan query DataTables kamu) ===
        $query = DB::table('nwmasbar as a')
            ->join('nwmassup as b', 'a.SUPP', '=', 'b.NO_SUPL')
            ->select(
                'a.SUB AS SUB',
                'a.KDBAR AS KD_BRG',
                'a.NMBAR AS NA_BRG',
                'a.ITEM_SUP',
                'a.SUPP',
                'b.NAMA ',
                'a.KET_UK',
                'a.KET_KEM',
                'a.BARCODE',
                'a.HJ AS HJUAL'
            );

        // Filter SUB BETWEEN
        if (!empty($sub1) && !empty($sub2)) {
            $query->whereBetween('a.SUB', [$sub1, $sub2]);
        }

        // Filter SUPP BETWEEN
        if (!empty($supp1) && !empty($supp2)) {
            $query->whereBetween('a.SUPP', [$supp1, $supp2]);
        }

        $result = $query->orderBy('a.SUB')->orderBy('a.KDBAR')->get();

        // === Konversi hasil ke array untuk Jasper ===
        $data = [];
        $resultArray = json_decode(json_encode($result), true);

        foreach ($resultArray as $row) {
            for ($i = 0; $i < $qty; $i++) {
                $data[] = $row;
            }
        }

        // Kirim data ke Jasper
        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I"); // "I" artinya inline (tampil di browser)
    }

    public function cetak(Request $request, Brg $brg){
        $file = 'vbrg';
        $data = [];
        $qty = max((int) $request->query('qty', 1), 1);
		$jumlahCetak = ceil($qty / 2); // dibagi 2

		for ($i = 0; $i < $jumlahCetak; $i++) {
            $data[] = [
                "KD_BRG"  => $brg->KD_BRG . " ",
                "NA_BRG"  => $brg->NA_BRG . " ",
                "KET_UK"  => $brg->KET_UK . " ",
                "BARCODE" => $brg->BARCODE . " ",
                "SUB"     => $brg->SUB . " ",
                "SUPP"   => $brg->SUPP . " ",
            ];
        }
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $cleanData = json_decode(json_encode($data), true);
        $PHPJasperXML->setData($data);
        $PHPJasperXML->arrayPageSetting["orientation"] = "L";
        $PHPJasperXML->arrayPageSetting["pageHeight"]  = 1 * 3.7795 * 18; // 1 mm = 3.7795 pixel, 1 ( jumlah row ) x 18 mm ( tinggi row )

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

    public function cetakBarcode(Request $request)
    {
        $no_bukti = $request->buktix;
        $file     = 'vbrg';
        $data     = DB::SELECT("SELECT belid.KD_BRG, belid.NA_BRG, belid.QTY, brg.KET_UK, brg.BARCODE, beli.TOTAL_QTY
                        FROM beli, belid, brg
                        WHERE beli.NO_BUKTI = belid.NO_BUKTI
                            AND belid.KD_BRG = brg.KD_BRG
                            AND belid.NO_BUKTI = '$no_bukti'");

        // dd($data);
        $finalData = [];
        foreach ($data as $row) {

			// bagi 2 dan bulatkan ke atas
			$qty = (int) $row->QTY;
			$jumlahCetak = ceil($qty / 2);

            for ($i = 0; $i < $jumlahCetak; $i++) {
                $finalData[] = [
                    'KD_BRG'  => $row->KD_BRG,
                    'NA_BRG'  => $row->NA_BRG,
                    'KET_UK'  => $row->KET_UK,
                    'BARCODE' => $row->BARCODE,
                ];
            }
        }

        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $cleanData = json_decode(json_encode($data), true);
        $PHPJasperXML->setData($finalData);
        $PHPJasperXML->arrayPageSetting["orientation"] = "L";
        $PHPJasperXML->arrayPageSetting["pageHeight"]  = 1 * 3.7795 * 18; // 1 mm = 3.7795 pixel, 1 ( jumlah row ) x 18 mm ( tinggi row )

        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }
}
