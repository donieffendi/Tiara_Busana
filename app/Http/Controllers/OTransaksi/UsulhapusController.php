<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Usulhapus;
use App\Models\OTransaksi\UsulhapusDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class UsulhapusController extends Controller
{
    public function index(Request $request)
    {
        // ganti 3
        return view('otransaksi_usulhapus.index');
    }

    public function index_post(Request $request)
    {

        return view('otransaksi_usulhapus.post');
    }

    public function browse_brg(Request $request)
    {
        $usulhapus = DB::SELECT("SELECT a.KDBAR, a.NMBAR, b.NAMA FROM nwmasbar a, nwmassup b WHERE a.SUPP = b.NO_SUPL ORDER BY KDBAR ");

        return response()->json($usulhapus);
    }

    public function getUsulhapus(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $CBG = Auth::user()->CBG;

		$usulhapus = DB::select("
						SELECT NO_ID, NO_BUKTI, TGL, USRNM, TG_SMP, POSTED, NOTES
                        FROM nwusul_hapus_brg
                        WHERE PER = '$periode'
					");


        // ganti 6

        return Datatables::of($usulhapus)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "non") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("usulhapus/delete/" . $row->NO_ID) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="usulhapus/edit/?idx=' . $row->NO_ID . '&tipx=edit"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="usulhapus/cetak/' . $row->NO_ID . '">
                                    <i class="fa fa-print" aria-hidden="true"></i>
                                    Cetak Usulan
                                </a>
                                <a class="dropdown-item btn btn-danger" target="_blank" href="usulhapus/pengesahan/' . $row->NO_ID . '">
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
        $periode = session()->get('periode')['bulan'].'/'.session()->get('periode')['tahun'];

        $bulan = session()->get('periode')['bulan'];
        $tahun = substr(session()->get('periode')['tahun'], -2);

        $last = DB::table('nwusul_hapus_brg')
            ->where('PER', $periode)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');

        if ($last) {
            $urut = str_pad(substr($last, -5, 4) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urut = '0001';
        }

        $no_bukti = 'UH'.$tahun.$bulan.'-'.$urut;

        $usulhapus = Usulhapus::create([
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,		
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
        ]);

        $REC     = $request->input('REC');
        $KDBAR   = $request->input('KDBAR');
        $NMBAR   = $request->input('NMBAR');
        $NAMA    = $request->input('NAMA');
        $KET     = $request->input('KET');

        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new UsulhapusDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
				$detail->KDBAR	     = ($KDBAR[$key]==null) ? "" :  $KDBAR[$key];
				$detail->NMBAR	     = ($NMBAR[$key]==null) ? "" :  $NMBAR[$key];
				$detail->NAMA	     = ($NAMA[$key]==null) ? "" :  $NAMA[$key];						
				$detail->KET	     = ($KET[$key]==null) ? "" :  $KET[$key];						
				// $detail->HPS         = isset($HPS[$key]) ? 1 : 0;
                $detail->save();
            }
        }
        // dd($detail);
        $no_buktix = $no_bukti;

        $usulhapus = Usulhapus::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE nwusul_hapus_brg,  nwusul_hapus_brgd
                            SET  nwusul_hapus_brgd.ID =  nwusul_hapus_brg.NO_ID  WHERE  nwusul_hapus_brg.NO_BUKTI =  nwusul_hapus_brgd.NO_BUKTI
							AND  nwusul_hapus_brg.NO_BUKTI='$no_buktix';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/usulhapus')->with('statusInsert', 'Data baru berhasil ditambahkan');

    }

    public function edit(Request $request, Usulhapus $usulhapus)
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_hapus_brg
		                 where PER ='$per'
						 and NO_BUKTI = '$buktix'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_hapus_brg
		                 where PER ='$per'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_hapus_brg
		             where PER ='$per'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_hapus_brg
		             where PER ='$per'
                     and NO_BUKTI >
					 '$buktix' ORDER BY NO_BUKTI ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwusul_hapus_brg
						where PER ='$per'
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
            $usulhapus = Usulhapus::where('NO_ID', $idx)->first();
        } else {
            $usulhapus      = new Usulhapus;
            $usulhapus->TGL = Carbon::now();

        }

        $no_bukti   = $usulhapus->NO_BUKTI;
        $usulhapusDetail = DB::table('nwusul_hapus_brgd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

        $data = [
            'header' => $usulhapus,
            'detail' => $usulhapusDetail,

        ];

        return view('otransaksi_usulhapus.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Usulhapus $usulhapus)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );
        $CBG = Auth::user()->CBG;

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $usulhapus->update([
            'TGL'              => date('Y-m-d', strtotime($request['TGL'])),			
            'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
            'USRNM'            => Auth::user()->username,
            'TG_SMP'           => Carbon::now()
        ]);

        $no_buktix = $usulhapus->NO_BUKTI;
        $NO_ID     = $request->input('NO_ID');
        $REC       = $request->input('REC');
        $KDBAR     = $request->input('KDBAR');
        $NMBAR     = $request->input('NMBAR');
        $NAMA      = $request->input('NAMA');
        $KET      = $request->input('KET');
        // $HPS       = $request->input('HPS');

        // Hapus data yang tidak ada di request
        DB::table('nwusul_hapus_brgd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = UsulhapusDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'KDBAR'      => ($KDBAR[$i]==null) ? "" :  $KDBAR[$i],
                        'NMBAR'      => ($NMBAR[$i]==null) ? "" : $NMBAR[$i],	
						'NAMA'       => ($NAMA[$i]==null) ? "" : $NAMA[$i],
						'KET'        => ($KET[$i]==null) ? "" : $KET[$i],
						// 'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                        
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = UsulhapusDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KDBAR'      => ($KDBAR[$i]==null) ? "" :  $KDBAR[$i],	
                        'NMBAR'      => ($NMBAR[$i]==null) ? "" : $NMBAR[$i],	
						'NAMA'       => ($NAMA[$i]==null) ? "" : $NAMA[$i],				
						'KET'        => ($KET[$i]==null) ? "" : $KET[$i],				
						// 'HPS'        => isset($HPS[$i]) ? $HPS[$i] : 0,	
                    ]
                );
            }
        }

        $usulhapus = Usulhapus::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $usulhapus->NO_BUKTI;

        DB::SELECT("UPDATE nwusul_hapus_brg,  nwusul_hapus_brgd
                    SET  nwusul_hapus_brgd.NO_ID =  nwusul_hapus_brg.NO_ID WHERE nwusul_hapus_brg.NO_BUKTI =  nwusul_hapus_brgd.NO_BUKTI
                    AND  nwusul_hapus_brg.NO_BUKTI='$no_bukti';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/usulhapus')->with('statusInsert', 'Data baru berhasil diupdate');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 22

    public function destroy(Request $request, Usulhapus $usulhapus)
    {
        $per      = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED == 1) {
        //     return redirect()->route('ubsup')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }

        $deleteUsulhapus = Usulhapus::find($usulhapus->NO_ID);

        $deleteUsulhapus->delete();

        return redirect('/usulhapus')->with('status', 'Data berhasil dihapus');
    }

    public function cetak(Usulhapus $usulhapus)
    {
        $no_pp = $usulhapus->NO_BUKTI;

        $file         = 'Usulan_Hapus_Brg';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $params = [
            "TGL_CTK" => date('d/m/Y')
        ];
        $PHPJasperXML->arrayParameter = $params;

        $query = DB::SELECT("SELECT a.NO_BUKTI, a.TGL, b.KDBAR, b.NMBAR, b.NAMA, b.KET, b.REC
                            FROM nwusul_hapus_brg a, nwusul_hapus_brgd b 
                            WHERE a.NO_BUKTI=b.NO_BUKTI AND a.NO_BUKTI='$no_pp'
                            ORDER BY REC ASC
		");

        $data = [];

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
    }

    public function pengesahan(Usulhapus $usulhapus)
    {
        $no_pp = $usulhapus->NO_BUKTI;

        $file         = 'Pengesahan_Hapus_Brg';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $query = DB::SELECT("
            SELECT a.NO_BUKTI, a.TGL, b.KDBAR, b.NMBAR, b.NAMA, b.REC, b.KET
            FROM nwusul_hapus_brg a, nwusul_hapus_brgd b 
            WHERE a.NO_BUKTI=b.NO_BUKTI AND a.NO_BUKTI=?
            ORDER BY REC ASC
        ", [$no_pp]);

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");

        // update header
        DB::update("
            UPDATE nwusul_hapus_brg 
            SET POSTED = 1 
            WHERE NO_BUKTI = ?
        ", [$no_pp]);

        // 🔥 update supplier TANPA LOOP (pakai JOIN)
        DB::update("
            UPDATE nwmasbar m
            JOIN nwusul_hapus_brgd d 
                ON m.KDBAR = d.KDBAR
            SET m.TD_OD = '*'
            WHERE d.NO_BUKTI = ?
        ", [$no_pp]);
    }

    public function prosesOtomatis()
    {
        DB::beginTransaction();

        try {

            // =========================
            // 1. SET PERIODE
            // =========================
            $bulan   = date('m'); // contoh: 03
            $tahun   = date('Y'); // contoh: 2026
            $tahun2  = substr($tahun, 2, 2); // 26

            $periode = $bulan . '/' . $tahun; // 03/2026

            // =========================
            // 2. GENERATE NO_BUKTI
            // =========================
            $prefix = 'UH' . $tahun2 . $bulan; // UH2603

            $last = DB::selectOne("
                SELECT MAX(NO_BUKTI) as last_no
                FROM nwusul_hapus_brg
                WHERE NO_BUKTI LIKE ?
            ", [$prefix . '%']);

            if ($last && $last->last_no) {
                $lastNumber = (int) substr($last->last_no, -4);
                $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $nextNumber = '0001';
            }

            $no_bukti = $prefix . '-' . $nextNumber;

            // =========================
            // 3. INSERT HEADER
            // =========================
            $no_id = DB::table('nwusul_hapus_brg')->insertGetId([
                'NO_BUKTI' => $no_bukti,
                'TGL'      => DB::raw('DATE(NOW())'),
                'PER'      => $periode,
                'NOTES'    => 'PROSES OTOMATIS',
                'USRNM'    => Auth::user()->username,
                'TG_SMP'   => Carbon::now()
            ]);

            // =========================
            // 4. INSERT DETAIL
            // =========================
            $fieldAK = 'AK' . $bulan; // contoh: AK03

            DB::insert("
                INSERT INTO nwusul_hapus_brgd (ID, NO_BUKTI, PER, REC, KDBAR, NMBAR, NAMA, KET)
                SELECT 
                    ? as ID,
                    ? as NO_BUKTI,
                    ? as PER,
                    ROW_NUMBER() OVER (ORDER BY a.KDBAR) as REC,
                    a.KDBAR,
                    a.NMBAR,
                    s.NAMA,
                    'AUTO' as KET
                FROM nwmasbar a
                JOIN nwmasbard d ON a.KDBAR = d.KDBAR
                LEFT JOIN nwmassup s ON a.SUPP = s.NO_SUPL
                GROUP BY a.KDBAR, a.NMBAR, s.NAMA
                HAVING SUM(d.$fieldAK <> 0) = 0
            ", [$no_id, $no_bukti, $periode]);

            // =========================
            // 5. VALIDASI (optional tapi penting)
            // =========================
            $count = DB::table('nwusul_hapus_brgd')
                        ->where('ID', $no_id)
                        ->count();

            if ($count == 0) {
                throw new \Exception('Tidak ada data yang memenuhi syarat!');
            }

            DB::commit();

            return redirect()->back()->with('status', 
                'Proses otomatis berhasil! No Bukti: ' . $no_bukti
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
