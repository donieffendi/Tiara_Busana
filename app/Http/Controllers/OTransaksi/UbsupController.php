<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Ubsup;
use App\Models\OTransaksi\UbsupDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class UbsupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $view;
    protected $judul;
    protected $FLAGZ;
    protected $GOLZ;

    public function setFlag(Request $request)
    {
        $this->FLAGZ = $request->flagz;
        $this->GOLZ  = $request->golz;

        // Use switch-case to determine the appropriate view and title
        switch (true) {
            case ($request->flagz == 'HB' && $request->golz == '1'):
                $this->judul = "Usulan Rubah Penghapusan Barang";
                $this->view  = 'otransaksi_ubsup.index';
                break;
            case ($request->flagz == 'HS' && $request->golz == '0'):
                $this->judul = "Usulan Hapus Suplier";
                $this->view  = 'otransaksi_ubsup.index';
                break;
            case ($request->flagz == 'PU' && $request->golz == 'PU'):
                $this->judul = "Posting Usulan Hapus Suplier";
                $this->view  = 'otransaksi_ubsup.index_posting';
                break;
            case ($request->flagz == 'PE' && $request->golz == 'PE'):
                $this->judul = "Posting Usulan Ubah Email Suplier";
                $this->view  = 'otransaksi_ubsup.index_posting_email';
                break;
            default:
                $this->judul = "Usulan Rubah Email Suplier";
                $this->view  = 'otransaksi_ubsup.index';
break;
        }
    }

    public function index(Request $request)
    {
        // Call setFlag to set the view, judul, FLAGZ, and GOLZ
        $this->setFlag($request);
        
        // Return the view with the necessary data
        return view($this->view)->with([
            'judul' => $this->judul, 
            'flagz' => $this->FLAGZ, 
            'golz'  => $this->GOLZ
        ]);
    }

    public function index_post(Request $request)
    {

        return view('otransaksi_ubsup.post');

    }

    public function browse(Request $request)
    {
        $CBG = Auth::user()->CBG;

        $filterbukti = '';
        if ($request->NO_BUKTI) {
            $filterbukti = " AND ubsup.NO_BUKTI='" . $request->NO_BUKTI . "' ";
        }
        $ubsup = DB::SELECT("SELECT * from bretur, breturd where ubsup.NO_BUKTI=breturd.no_bukti $filterbukti ");

        return response()->json($ubsup);
    }

    public function browse_brg(Request $request)
    {   
        $beli = DB::SELECT("SELECT CONCAT(SUB,KDBAR) AS KD_BRG, NMBAR AS NA_BRG, BARCODE, HJ AS HARGA_JL, HB AS HARGA, RAK AS JNS, MARGIN 
                            FROM nwmasbar");
        return response()->json($beli);
    }

    public function getUbsup(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ  = $this->GOLZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        //$ubsup = DB::SELECT("SELECT *, POSTED as cek1, POSTED1 as cek2 from bretur  WHERE PER='$periode' AND CBG = '$CBG' AND FLAG = '$FLAGZ' ORDER BY NO_BUKTI ");
		$ubsup = DB::select("
						SELECT *
						FROM bretur
                        WHERE PER = '$periode' AND CBG = '$CBG' AND flag = '$FLAGZ'
						ORDER BY NO_BUKTI
					");


        // ganti 6

        return Datatables::of($ubsup)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "non") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("ubsup/delete/" . $row->NO_ID . "/?flagz=" . $row->flag . "&golz=" . $row->GOL) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="ubsup/edit/?idx=' . $row->NO_ID . '&tipx=edit&flagz=' . $row->flag . '&judul=' . $this->judul . '&golz=' . $row->GOL . '"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="ubsup/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Print
                                </a>
                                <hr></hr>
                                <a class="dropdown-item btn btn-danger" ' . $btnDelete . '>

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

//////////////////////////////////////////////////////////////////////////////////

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
            // GANTI 9

            [
                'TGL' => 'required',

            ]
        );

        //////     nomer otomatis
        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ  = $this->GOLZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        // Ambil NO_BUKTI terakhir
        $query = DB::table('bretur')
            ->where('NO_BUKTI', 'like', $FLAGZ . $CBG . $tahun . $bulan . '-%')
            ->orderBy('NO_BUKTI', 'desc')
            ->first();

        if (!empty($query)) {
        // Ambil 4 digit terakhir (increment number)
            $lastNumber = (int) substr($query->NO_BUKTI, -4);
            $newNumber  = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        // Format NO_BUKTI
        $no_bukti = $FLAGZ . $CBG . $tahun . $bulan . '-' . $newNumber;

        $ubsup = Ubsup::create([
            'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,
                'flag'             => $FLAGZ,			
                'notes'            => ($request['NOTES']==null) ? "" : $request['NOTES'],	
                'usrnm'            => Auth::user()->username,
                'tg_smp'           => Carbon::now(),
				'CBG'              => $CBG,
        ]);

        $REC       = $request->input('REC');
        $KD_BRG    = $request->input('KD_BRG');
        $NA_BRG    = $request->input('NA_BRG');
        $KET       = $request->input('KET');
        $HPS       = $request->input('HPS');


        $FLAG = $FLAGZ;
        $PER  = $periode;
        $GOL  = $GOLZ;

        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new ReturDetail;

                // Insert ke Database
                $detail->no_bukti    = $no_bukti;
                $detail->rec         = $REC[$key];
                $detail->per         = $periode;
                $detail->flag        = $FLAGZ;	
				$detail->KD_BRG	     = ($KD_BRG[$key]==null) ? "" :  $KD_BRG[$key];
				$detail->NA_BRG	     = ($NA_BRG[$key]==null) ? "" :  $NA_BRG[$key];
				$detail->ket	     = ($KET[$key]==null) ? "" :  $KET[$key];						
				$detail->HPS         = isset($HPS[$key]) ? 1 : 0;
                $detail->save();
            }
        }
        // dd($detail);
        $no_buktix = $no_bukti;

        $ubsup = Ubsup::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE ubsup,  breturd
                            SET  breturd.ID =  ubsup.NO_ID  WHERE  ubsup.NO_BUKTI =  breturd.no_bukti
							AND  ubsup.NO_BUKTI='$no_buktix';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/ubsup?flagz=' . $FLAGZ . '&golz=' . $GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ]);

    }

    public function edit(Request $request, Ubsup $ubsup)
    {

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/pp')
        // 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }

        $this->setFlag($request);

        $tipx = $request->tipx;

        $idx = $request->idx;

        $CBG = Auth::user()->CBG;

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bretur
		                 where PER ='$per' and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ'
                         AND CBG = '$CBG'
						 and NO_BUKTI = '$buktix'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bretur
		                 where PER ='$per'
						 and FLAG ='$this->FLAGZ'
                         and GOL ='$this->GOLZ'
                         AND CBG = '$CBG'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bretur
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'
                     and GOL ='$this->GOLZ'
                     AND CBG = '$CBG'
                     and NO_BUKTI <
					 '$buktix' ORDER BY NO_BUKTI DESC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'next') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bretur
		             where PER ='$per'
					 and FLAG ='$this->FLAGZ'
                     and GOL ='$this->GOLZ'
                     AND CBG = '$CBG'
                     and NO_BUKTI >
					 '$buktix' ORDER BY NO_BUKTI ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from bretur
						where PER ='$per'
						and FLAG ='$this->FLAGZ'
                        and GOL ='$this->GOLZ'
                        AND CBG = '$CBG'
		                ORDER BY NO_BUKTI DESC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'undo' || $tipx == 'search') {

            $tipx = 'edit';

        }

        if ($idx != 0) {
            $ubsup = Ubsup::where('NO_ID', $idx)->first();
        } else {
            $ubsup      = new Ubsup;
            $ubsup->TGL = Carbon::now();

        }

        $no_bukti   = $ubsup->NO_BUKTI;
        $ubsupDetail = DB::table('breturd')->where('NO_BUKTI', $no_bukti)->get();

        $data = [
            'header' => $ubsup,
            'detail' => $ubsupDetail,

        ];

        return view('otransaksi_ubsup.edit', $data)
            ->with(['tipx' => $tipx, 'idx' => $idx, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ, 'judul' => $this->judul]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Ubsup $ubsup)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );

        $this->setFlag($request);
        $GOLZ  = $this->GOLZ;
        $FLAGZ = $this->FLAGZ;
        $judul = $this->judul;

        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $ubsup->update([
            'tgl'              => date('Y-m-d', strtotime($request['TGL'])),			
            'notes'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
            'usrnm'            => Auth::user()->username,
            'tg_smp'           => Carbon::now()
        ]);

        $no_buktix = $ubsup->NO_BUKTI;
        $NO_ID     = $request->input('NO_ID');
        $REC       = $request->input('REC');
        $KD_BRG    = $request->input('KD_BRG');
        $NA_BRG    = $request->input('NA_BRG');
        $KET       = $request->input('KET');
        $HPS       = $request->input('HPS');
        $FLAG      = $FLAGZ;
        $PER       = $periode;
        $GOL       = $GOLZ;

        // Hapus data yang tidak ada di request
        DB::table('breturd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = ReturDetail::create(
                    [
                        'no_bukti'   => $request->NO_BUKTI,
                        'rec'        => $REC[$i],
                        'per'        => $periode,
                        'flag'       => $this->FLAGZ,
                        'KD_BRG'     => ($KD_BRG[$i]==null) ? "" :  $KD_BRG[$i],
                        'NA_BRG'     => ($NA_BRG[$i]==null) ? "" : $NA_BRG[$i],	
						'ket'     	 => ($KET[$i]==null) ? "" : $KET[$i],
						'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                        
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = ReturDetail::updateOrCreate(
                    [
                        'no_bukti'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'rec'        => $REC[$i],

                        'KD_BRG'     => ($KD_BRG[$i]==null) ? "" :  $KD_BRG[$i],	
                        'NA_BRG'     => ($NA_BRG[$i]==null) ? "" : $NA_BRG[$i],	
						'ket'     	 => ($KET[$i]==null) ? "" : $KET[$i],				
						'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                    ]
                );
            }
        }

        $ubsup = Ubsup::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $ubsup->NO_BUKTI;

        DB::SELECT("UPDATE ubsup,  breturd
                    SET  breturd.NO_ID =  ubsup.NO_ID  WHERE  ubsup.NO_BUKTI =  breturd.no_bukti
                    AND  ubsup.NO_BUKTI='$no_bukti';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/ubsup?flagz=' . $FLAGZ . '&golz=' . $GOLZ)->with(['judul' => $judul, 'golz' => $GOLZ, 'flagz' => $FLAGZ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Ubsup $ubsup)
    {

        $this->setFlag($request);
        $FLAGZ = $this->FLAGZ;
        $GOLZ  = $this->GOLZ;
        $judul = $this->judul;

        // ini dr mana $this->GOLZ?
        $GOLZ  = $_GET['golz'];
        $FLAGZ = $_GET['flagz'];

        $per      = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        if ($cekperid[0]->POSTED == 1) {
            return redirect()->route('ubsup')
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        }

        $deleteUbsup = Ubsup::find($ubsup->NO_ID);

        $deleteUbsup->delete();
        // return redirect('/pp?flagz=' . $FLAGZ . '&golz=J')
        return redirect('/ubsup?flagz=' . $FLAGZ . '&golz=' . $GOLZ)
            ->with(['judul' => $judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ])
            ->with('statusHapus', 'Data ' . $ubsup->NO_BUKTI . ' berhasil dihapus');

    }

    public function cetak(Ubsup $ubsup)
    {
        $no_pp = $ubsup->NO_BUKTI;

        $file         = 'ubsup';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("SELECT *
                            FROM ubsup 
                            WHERE ubsup.NO_BUKTI='$no_pp' 
                            ;
		"); 
        $query2 = DB::SELECT("SELECT *
                            FROM breturd 
                            WHERE breturd.no_bukti='$no_pp' 
                            ;
		");

        $data = [];

        foreach ($query2 as $key => $value) {
            array_push($data, [
                'NO_BUKTI' => $query[$key]->NO_BUKTI,
                'TGL'      => $query[$key]->TGL,
                'TGL_NOW' => now()->format('d F Y'),
                'NAMAS'      => $query2[$key]->NAMAS,
                'KODES'      => $query2[$key]->KODES,
                'EMAIL'      => $query2[$key]->EMAIL,
                'E_BARU'      => $query2[$key]->E_BARU,
            ]);
        }

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
        DB::SELECT("UPDATE ubsup SET POSTED = 1 WHERE ubsup.NO_BUKTI='$no_pp';");
        if($query[0]->FLAG == 'UE'){
            foreach($query2 as $sup){

                DB::SELECT("UPDATE zsup SET EMAIL = '" . $sup->E_BARU . "' WHERE zsup.KODES='" . $sup->KODES . "';");
            }
        }
        if($query[0]->FLAG == 'HS'){
            foreach($query2 as $sup){
                DB::SELECT("DELETE FROM zsup WHERE zsup.KODES='" . $sup->KODES . "';");
            }
        }
    }

    // public function posting(Request $request)
    // {

    //     $CEK      = $request->input('cek');
    //     $NO_BUKTI = $request->input('NO_BUKTI');

    //     // $usrnmx = Auth::user()->username;

    //     $hasil = "";

    //     if ($CEK) {
    //         foreach ($CEK as $key => $value) {

    //             //$STA = $request->input('STA');

    //             $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
    //             $bulan   = session()->get('periode')['bulan'];
    //             $tahun   = substr(session()->get('periode')['tahun'], -2);

    //             $NO_BUKTIXZ = $NO_BUKTI[$key];

    //             DB::SELECT("UPDATE ubsup SET POSTED = 1 WHERE PO.NO_BUKTI='$NO_BUKTIXZ'");

    //         }
    //     } else {
    //         $hasil = $hasil . "Tidak ada Usulan yang dipilih! ; ";
    //     }

    //     if ($hasil != '') {
    //         return redirect('/ubsup/index-posting')->with('status', 'Proses Approvement Usulan ..')->with('gagal', $hasil);
    //     } else {
    //         return redirect('/ubsup/index-posting')->with('status', 'Approvement Usulan  selesai..');
    //     }

    // }

    public function getDetailUbsup()
    {

        $no_bukti = $_GET['no_bukti'];
        $result   = DB::table('breturd')->where('NO_BUKTI', $no_bukti)->get();

        return response()->json($result);
    }

    public function posting_hapus_sup(Request $request)
    {
        if (! $request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        $data = $request->input('posted');

        if (! $data) {
            return response()->json(['error' => 'Tidak ada data yang dikirim'], 400);
        }

        foreach ($data as $id => $posted) {
            DB::table('ubsup')->where('NO_ID', $id)->update(['POSTED' => $posted]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }

    public function posting_usul_emsup(Request $request)
    {
        if (! $request->isMethod('post')) {
            return response()->json(['error' => 'Method Not Allowed'], 405);
        }

        $data = $request->input('posted');

        if (! $data) {
            return response()->json(['error' => 'Tidak ada data yang dikirim'], 400);
        }

        foreach ($data as $id => $posted) {
            DB::table('ubsup')->where('NO_ID', $id)->update(['POSTED' => $posted]);
        }

        return response()->json(['message' => 'Status berhasil diperbarui']);
    }
}
