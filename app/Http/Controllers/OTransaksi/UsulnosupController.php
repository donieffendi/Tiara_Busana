<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Usulnosup;
use App\Models\OTransaksi\UsulnosupDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class UsulnosupController extends Controller
{
    public function index(Request $request)
    {
        // ganti 3
        return view('otransaksi_usulnosup.index');
    }

    public function index_post(Request $request)
    {

        return view('otransaksi_usulnosup.post');
    }

    public function browse_sup(Request $request)
    {
        $usulnosup = DB::SELECT("SELECT NO_SUPL, NAMA FROM nwmassup ORDER BY NO_SUPL ");

        return response()->json($usulnosup);
    }

    public function getUsulnosup(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $CBG = Auth::user()->CBG;

		$usulnosup = DB::select("
						SELECT NO_ID, NO_BUKTI, TGL, USRNM, TG_SMP, POSTED, NOTES
                        FROM nwusul_ubah_nosup
                        WHERE CBG = '$CBG' AND PER = '$periode'
					");


        // ganti 6

        return Datatables::of($usulnosup)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "non") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("usulnosup/delete/" . $row->NO_ID) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="usulnosup/edit/?idx=' . $row->NO_ID . '&tipx=edit"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="usulnosup/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Cetak Usulan
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="usulnosup/pengesahan/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Cetak Pengesahan
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
        $CBG = Auth::user()->CBG;

        $CBG_KODE = DB::table('toko')
            ->where('KODE', $CBG)
            ->value('TYPE');

        $periode = session()->get('periode')['bulan'].'/'.session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $last = DB::table('nwusul_ubah_nosup')
            ->where('PER', $periode)
            ->where('CBG', $CBG)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');

        if ($last) {
            $urut = str_pad(substr($last, -5, 4) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urut = '0001';
        }

        $no_bukti = 'UN'.$tahun.$bulan.'-'.$urut.$CBG_KODE;

        $usulnosup = Usulnosup::create([
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,		
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
				'CBG'              => $CBG,
        ]);

        $REC       = $request->input('REC');
        $NO_SUPL   = $request->input('NO_SUPL');
        $NAMA      = $request->input('NAMA');
        $NO_BARU   = $request->input('NO_BARU');

        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new UsulnosupDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
				$detail->NO_SUPL	 = ($NO_SUPL[$key]==null) ? "" :  $NO_SUPL[$key];
				$detail->NAMA	     = ($NAMA[$key]==null) ? "" :  $NAMA[$key];
				$detail->NO_BARU	 = ($NO_BARU[$key]==null) ? "" :  $NO_BARU[$key];						
				// $detail->HPS         = isset($HPS[$key]) ? 1 : 0;
                $detail->save();
            }
        }
        // dd($detail);
        $no_buktix = $no_bukti;

        $usulnosup = Usulnosup::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE nwusul_ubah_nosup,  nwusul_ubah_nosupd
                            SET  nwusul_ubah_nosupd.ID =  nwusul_ubah_nosup.NO_ID  WHERE  nwusul_ubah_nosup.NO_BUKTI =  nwusul_ubah_nosupd.NO_BUKTI
							AND  nwusul_ubah_nosup.NO_BUKTI='$no_buktix';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/usulnosup')->with('statusInsert', 'Data baru berhasil ditambahkan');

    }

    public function edit(Request $request, Usulnosup $usulnosup)
    {

        $per = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];

        // $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED==1)
        // {
        //     return redirect('/pp')
        // 	       ->with('status', 'Maaf Periode sudah ditutup!')
        //            ->with(['judul' => $judul, 'flagz' => $FLAGZ]);
        // }

        $tipx = $request->tipx;

        $idx = $request->idx;

        $CBG = Auth::user()->CBG;

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_ubah_nosup
		                 where PER ='$per'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_ubah_nosup
		                 where PER ='$per'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_ubah_nosup
		             where PER ='$per'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_ubah_nosup
		             where PER ='$per'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_ubah_nosup
						where PER ='$per'
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
            $usulnosup = Usulnosup::where('NO_ID', $idx)->first();
        } else {
            $usulnosup      = new Usulnosup;
            $usulnosup->TGL = Carbon::now();

        }

        $no_bukti   = $usulnosup->NO_BUKTI;
        $usulnosupDetail = DB::table('nwusul_ubah_nosupd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

        $data = [
            'header' => $usulnosup,
            'detail' => $usulnosupDetail,

        ];

        return view('otransaksi_usulnosup.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Usulnosup $usulnosup)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );
        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $usulnosup->update([
            'TGL'              => date('Y-m-d', strtotime($request['TGL'])),			
            'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
            'USRNM'            => Auth::user()->username,
            'TG_SMP'           => Carbon::now()
        ]);

        $no_buktix = $usulnosup->NO_BUKTI;
        $NO_ID     = $request->input('NO_ID');
        $REC       = $request->input('REC');
        $NO_SUPL   = $request->input('NO_SUPL');
        $NAMA      = $request->input('NAMA');
        $NO_BARU   = $request->input('NO_BARU');
        // $HPS       = $request->input('HPS');

        // Hapus data yang tidak ada di request
        DB::table('nwusul_ubah_nosupd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = UsulnosupDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'NO_SUPL'    => ($NO_SUPL[$i]==null) ? "" :  $NO_SUPL[$i],
                        'NAMA'       => ($NAMA[$i]==null) ? "" : $NAMA[$i],	
						'NO_BARU'    => ($NO_BARU[$i]==null) ? "" : $NO_BARU[$i],
						// 'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                        
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = UsulnosupDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'NO_SUPL'    => ($NO_SUPL[$i]==null) ? "" :  $NO_SUPL[$i],	
                        'NAMA'       => ($NAMA[$i]==null) ? "" : $NAMA[$i],	
						'NO_BARU'    => ($NO_BARU[$i]==null) ? "" : $NO_BARU[$i],				
						// 'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                    ]
                );
            }
        }

        $usulnosup = Usulnosup::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $usulnosup->NO_BUKTI;

        DB::SELECT("UPDATE nwusul_ubah_nosup,  nwusul_ubah_nosupd
                    SET  nwusul_ubah_nosupd.NO_ID =  nwusul_ubah_nosup.NO_ID WHERE nwusul_ubah_nosup.NO_BUKTI =  nwusul_ubah_nosupd.NO_BUKTI
                    AND  nwusul_ubah_nosup.NO_BUKTI='$no_bukti';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/usulnosup')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Usulnosup $usulnosup)
    {
        $per      = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED == 1) {
        //     return redirect()->route('ubsup')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }

        $deleteUsulnosup = Usulnosup::find($usulnosup->NO_ID);

        $deleteUsulnosup->delete();

        return redirect('/usulnosup')->with('status', 'Data berhasil dihapus');
    }

    public function cetak(Usulnosup $usulnosup)
    {
        $no_pp = $usulnosup->NO_BUKTI;

        $file         = 'Usulan_Ganti_Nosup';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $params = [
            "TGL_CTK" => date('d/m/Y')
        ];
        $PHPJasperXML->arrayParameter = $params;

        $query = DB::SELECT("SELECT a.NO_BUKTI, a.TGL, b.NO_SUPL, b.NAMA, b.NO_BARU, b.REC
                            FROM nwusul_ubah_nosup a, nwusul_ubah_nosupd b 
                            WHERE a.NO_BUKTI=b.NO_BUKTI AND a.NO_BUKTI='$no_pp'
                            ORDER BY REC ASC
		");

        $data = [];

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

    public function pengesahan(Usulnosup $usulnosup)
    {
        $no_pp = $usulnosup->NO_BUKTI;

        $file         = 'Pengesahan_Ganti_Nosup';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
            SELECT a.NO_BUKTI, a.TGL, b.NO_SUPL, b.NAMA, b.NO_BARU, b.REC
            FROM nwusul_ubah_nosup a, nwusul_ubah_nosupd b 
            WHERE a.NO_BUKTI=b.NO_BUKTI AND a.NO_BUKTI=?
            ORDER BY REC ASC
        ", [$no_pp]);

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

        // update header
        DB::update("
            UPDATE nwusul_ubah_nosup 
            SET POSTED = 1 
            WHERE NO_BUKTI = ?
        ", [$no_pp]);

        // 🔥 update supplier TANPA LOOP (pakai JOIN)
        DB::update("
            UPDATE nwmassup m
            JOIN nwusul_ubah_nosupd d 
                ON m.NO_SUPL = d.NO_SUPL
            SET m.NO_SUPL = d.NO_BARU
            WHERE d.NO_BUKTI = ?
        ", [$no_pp]);
    }
}
