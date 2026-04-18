<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
// ganti 1

use App\Models\OTransaksi\Tandaretur;
use App\Models\OTransaksi\TandareturDetail;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

include_once base_path() . "/vendor/simitgroup/phpjasperxml/version/1.1/PHPJasperXML.inc.php";
use PHPJasperXML;

// ganti 2
class TandareturController extends Controller
{
    public function index(Request $request)
    {
        // ganti 3
        return view('otransaksi_tandaretur.index');
    }

    public function index_post(Request $request)
    {

        return view('otransaksi_tandaretur.post');
    }

    public function browse_brg(Request $request)
    {
        $tandaretur = DB::SELECT("SELECT KDBAR, NMBAR, RETUR, KET_UK, KET_KEM FROM nwmasbar ORDER BY KDBAR ");

        return response()->json($tandaretur);
    }

    public function getTandaretur(Request $request)
    {
        // ganti 5

        if ($request->session()->has('periode')) {
            $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];
        } else {
            $periode = '';
        }

        $cabang = session()->get('periode')['cabang'];

		$tandaretur = DB::select("
						SELECT a.NO_ID, a.NO_BUKTI, a.TGL, a.USRNM, a.TG_SMP, a.POSTED, a.NOTES, b.KDBAR, b.KET_UK, b.KET_KEM, b.RETUR_B 
                        FROM nwtandaretur a, nwtandareturd b
                        WHERE a.PER = '$periode' AND a.CBG = '$cabang' AND a.NO_BUKTI = b.NO_BUKTI
					");


        // ganti 6

        return Datatables::of($tandaretur)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                if (Auth::user()->divisi == "programmer" || Auth::user()->divisi == "non") {
                    //CEK POSTED di index dan edit

                    // url untuk delete di index
                    $url = "'" . url("tandaretur/delete/" . $row->NO_ID) . "'";
                    // batas

                    $btnEdit   = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="tandaretur/edit/?idx=' . $row->NO_ID . '&tipx=edit"';
                    $btnDelete = ($row->POSTED == 1) ? ' onclick= "alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' onclick="deleteRow(' . $url . ')"';
                    $btnPosting = ($row->POSTED == 1) ? ' onclick="alert(\'Transaksi ' . $row->NO_BUKTI . ' sudah diposting!\')" href="#" ' : ' href="#" onclick="postingData(\'' . $row->NO_ID . '\')" ';

                    $btnPrivilege =
                    '
                                <a class="dropdown-item" ' . $btnEdit . '>
                                <i class="fas fa-edit"></i>
                                    Edit
                                </a>
                                <a class="dropdown-item btn btn-success" ' . $btnPosting . '>
                                    <i class="fa fa-check"></i> Posting
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

        $last = DB::table('nwtandaretur')
            ->where('PER', $periode)
            ->orderByDesc('NO_BUKTI')
            ->value('NO_BUKTI');

        if ($last) {
            $urut = str_pad(substr($last, -5, 4) + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $urut = '0001';
        }

        $no_bukti = 'TR'.$tahun.$bulan.'-'.$urut;

        $cabang = session()->get('periode')['cabang'];

        $tandaretur = Tandaretur::create([
                'NO_BUKTI'         => $no_bukti,
                'TGL'              => date('Y-m-d', strtotime($request['TGL'])),
                'PER'              => $periode,		
                'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
                'CBG'              => $cabang,
                'USRNM'            => Auth::user()->username,
                'TG_SMP'           => Carbon::now(),
        ]);

        $REC     = $request->input('REC');
        $KDBAR   = $request->input('KDBAR');
        $NMBAR   = $request->input('NMBAR');
        $KET_UK  = $request->input('KET_UK');
        $KET_KEM = $request->input('KET_KEM');
        $RETUR   = $request->input('RETUR');
        $RETUR_B = $request->input('RETUR_B');

        if ($REC) {
            foreach ($REC as $key => $value) {
                // Declare new data di Model
                $detail    = new TandareturDetail;

                // Insert ke Database
                $detail->NO_BUKTI    = $no_bukti;
                $detail->REC         = $REC[$key];
                $detail->PER         = $periode;
                $detail->CBG         = $cabang;
				$detail->KDBAR	     = ($KDBAR[$key]==null) ? "" :  $KDBAR[$key];
				$detail->NMBAR	     = ($NMBAR[$key]==null) ? "" :  $NMBAR[$key];
				$detail->KET_UK	     = ($KET_UK[$key]==null) ? "" :  $KET_UK[$key];						
				$detail->KET_KEM	 = ($KET_KEM[$key]==null) ? "" :  $KET_KEM[$key];						
				$detail->RETUR	     = ($RETUR[$key]==null) ? "" :  $RETUR[$key];
				$detail->RETUR_B	 = ($RETUR_B[$key]==null) ? "" :  $RETUR_B[$key];
                $detail->USRNM       = Auth::user()->username;
                $detail->TG_SMP      = Carbon::now();
                $detail->save();
            }
        }
        // dd($detail);
        $no_buktix = $no_bukti;

        $tandaretur = Tandaretur::where('NO_BUKTI', $no_buktix)->first();

        DB::SELECT("UPDATE nwtandaretur, nwtandareturd
                            SET  nwtandareturd.ID =  nwtandaretur.NO_ID  WHERE  nwtandaretur.NO_BUKTI =  nwtandareturd.NO_BUKTI
							AND  nwtandaretur.NO_BUKTI='$no_buktix';");

        // return redirect('/pp/edit/?idx=' . $pp->NO_ID . '&tipx=edit&flagz=' . $this->FLAGZ . '&golz=' . $this->GOLZ . '&judul=' . $this->judul . '');
        return redirect('/tandaretur')->with('statusInsert', 'Data baru berhasil ditambahkan');

    }

    public function edit(Request $request, Tandaretur $tandaretur)
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

        $cabang = session()->get('periode')['cabang'];

        if ($idx == '0' && $tipx == 'undo') {
            $tipx = 'top';

        }

        if ($tipx == 'search') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		                 where PER ='$per'
                         and CBG = '$cabang'
						 and NO_BUKTI = '$buktix'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'top') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		                 where PER ='$per'
                         and CBG = '$cabang'
		                 ORDER BY NO_BUKTI ASC  LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = 0;
            }

        }

        if ($tipx == 'prev') {

            $buktix = $request->buktix;

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		             where PER ='$per'
                     and CBG = '$cabang'
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

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
		             where PER ='$per'
                     and CBG = '$cabang'
                     and NO_BUKTI >
					 '$buktix' ORDER BY NO_BUKTI ASC LIMIT 1");

            if (! empty($bingco)) {
                $idx = $bingco[0]->NO_ID;
            } else {
                $idx = $idx;
            }

        }

        if ($tipx == 'bottom') {

            $bingco = DB::SELECT("SELECT NO_ID, NO_BUKTI from nwtandaretur
						where PER ='$per'
                        and CBG = '$cabang'
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
            $tandaretur = Tandaretur::where('NO_ID', $idx)->first();
        } else {
            $tandaretur      = new Tandaretur;
            $tandaretur->TGL = Carbon::now();

        }

        $no_bukti   = $tandaretur->NO_BUKTI;
        $tandareturDetail = DB::table('nwtandareturd')->where('NO_BUKTI', $no_bukti)->orderBy('REC')->get();

        $data = [
            'header' => $tandaretur,
            'detail' => $tandareturDetail,

        ];

        return view('otransaksi_tandaretur.edit', $data)->with(['tipx' => $tipx, 'idx' => $idx]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    // ganti 18

    public function update(Request $request, Tandaretur $tandaretur)
    {

        $this->validate(
            $request,
            [

                'TGL' => 'required',
            ]
        );

        $periode = $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun'];

        $cabang = session()->get('periode')['cabang'];

        $tandaretur->update([
            'TGL'              => date('Y-m-d', strtotime($request['TGL'])),			
            'NOTES'            => ($request['NOTES']==null) ? "" : $request['NOTES'],
            'USRNM'            => Auth::user()->username,
            'TG_SMP'           => Carbon::now()
        ]);

        $no_buktix = $tandaretur->NO_BUKTI;
        $NO_ID     = $request->input('NO_ID');
        $REC       = $request->input('REC');
        $KDBAR     = $request->input('KDBAR');
        $NMBAR     = $request->input('NMBAR');
        $KET_UK    = $request->input('KET_UK');
        $KET_KEM   = $request->input('KET_KEM');
        $RETUR     = $request->input('RETUR');
        $RETUR_B   = $request->input('RETUR_B');

        // Hapus data yang tidak ada di request
        DB::table('nwtandareturd')->where('NO_BUKTI', $request->NO_BUKTI)->whereNotIn('NO_ID', $NO_ID)->delete();

        for ($i = 0; $i < $length; $i++) {
            // Insert jika NO_ID baru
            if ($NO_ID[$i] == 'new') {
                $insert = TandareturDetail::create(
                    [
                        'NO_BUKTI'   => $request->NO_BUKTI,
                        'REC'        => $REC[$i],
                        'PER'        => $periode,
                        'CBG'        => $cabang,
                        'KDBAR'      => ($KDBAR[$i]==null) ? "" :  $KDBAR[$i],
                        'NMBAR'      => ($NMBAR[$i]==null) ? "" : $NMBAR[$i],	
						'KET_UK'     => ($KET_UK[$i]==null) ? "" : $KET_UK[$i],
                        'KET_KEM'    => ($KET_KEM[$i]==null) ? "" : $KET_KEM[$i],
                        'RETUR'      => ($RETUR[$i]==null) ? "" : $RETUR[$i],
                        'RETUR_B'    => ($RETUR_B[$i]==null) ? "" : $RETUR_B[$i],
                        'USRNM'      => Auth::user()->username,
                        'TG_SMP'     => Carbon::now()
                    ]
                );
            } else {
                // Update jika NO_ID sudah ada
                $upsert = TandareturDetail::updateOrCreate(
                    [
                        'NO_BUKTI'  => $request->NO_BUKTI,
                        'NO_ID'     => (int) str_replace(',', '', $NO_ID[$i])
                    ],

                    [
                        'REC'        => $REC[$i],

                        'KDBAR'      => ($KDBAR[$i]==null) ? "" :  $KDBAR[$i],	
                        'NMBAR'      => ($NMBAR[$i]==null) ? "" : $NMBAR[$i],	
						'KET_UK'     => ($KET_UK[$i]==null) ? "" : $KET_UK[$i],
                        'KET_KEM'    => ($KET_KEM[$i]==null) ? "" : $KET_KEM[$i],
                        'RETUR'      => ($RETUR[$i]==null) ? "" : $RETUR[$i],
                        'RETUR_B'    => ($RETUR_B[$i]==null) ? "" : $RETUR_B[$i],
                        'USRNM'      => Auth::user()->username,
                        'TG_SMP'     => Carbon::now()	
                    ]
                );
            }
        }

        $tandaretur = Tandaretur::where('NO_BUKTI', $no_buktix)->first();

        $no_bukti = $tandaretur->NO_BUKTI;

        DB::SELECT("UPDATE nwtandaretur,  nwtandareturd
                    SET  nwtandareturd.NO_ID =  nwtandaretur.NO_ID WHERE nwtandaretur.NO_BUKTI =  nwtandareturd.NO_BUKTI
                    AND  nwtandaretur.NO_BUKTI='$no_bukti';");

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

    public function destroy(Request $request, Tandaretur $tandaretur)
    {
        $per      = session()->get('periode')['bulan'] . '/' . session()->get('periode')['tahun'];
        $cekperid = DB::SELECT("SELECT POSTED from perid WHERE PERIO='$per'");
        // if ($cekperid[0]->POSTED == 1) {
        //     return redirect()->route('ubsup')
        //         ->with('status', 'Maaf Periode sudah ditutup!')
        //         ->with(['judul' => $this->judul, 'flagz' => $this->FLAGZ, 'golz' => $this->GOLZ]);
        // }

        $deleteTandaretur = Tandaretur::find($tandaretur->NO_ID);

        $deleteTandaretur->delete();

        return redirect('/usulhapus')->with('status', 'Data berhasil dihapus');
    }

    public function cetak(Tandaretur $tandaretur)
    {
        $no_pp = $tandaretur->NO_BUKTI;

        $file         = 'Usulan_Hapus_Brg';
        $PHPJasperXML = new PHPJasperXML();
        $PHPJasperXML->load_xml_file(base_path() . ('/app/reportc01/phpjasperxml/' . $file . '.jrxml'));

        $params = [
            "TGL_CTK" => date('d/m/Y')
        ];
        $PHPJasperXML->arrayParameter = $params;

        $query = DB::SELECT("SELECT a.NO_BUKTI, a.TGL, b.KDBAR, b.NMBAR, b.NAMA, b.KET, b.REC
                            FROM nwtandaretur a, nwtandareturd b 
                            WHERE a.NO_BUKTI=b.NO_BUKTI AND a.NO_BUKTI='$no_pp'
                            ORDER BY REC ASC
		");

        $data = [];

        $data = json_decode(json_encode($query), true);

        $PHPJasperXML->setData($data);
        ob_end_clean();
        $PHPJasperXML->outpage("I");
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
                FROM nwtandaretur
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
            $no_id = DB::table('nwtandaretur')->insertGetId([
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
                INSERT INTO nwtandareturd (ID, NO_BUKTI, PER, REC, KDBAR, NMBAR, NAMA, KET)
                SELECT 
                    ? as ID,
                    ? as NO_BUKTI,
                    ? as PER,
                    ROW_NUMBER() OVER (ORDER BY a.KDBAR) as REC,
                    a.KDBAR,
                    a.NMBAR,
                    s.NAMA,
                    'TL/MACET' as KET
                FROM nwmasbar a
                JOIN nwmasbard d ON a.KDBAR = d.KDBAR
                LEFT JOIN nwmassup s ON a.SUPP = s.NO_SUPL
                WHERE COALESCE(a.TD_OD, '') <> '*'
                GROUP BY a.KDBAR, a.NMBAR, s.NAMA
                HAVING SUM(d.$fieldAK <> 0) = 0
            ", [$no_id, $no_bukti, $periode]);

            // =========================
            // 5. VALIDASI (optional tapi penting)
            // =========================
            $count = DB::table('nwtandareturd')
                        ->where('ID', $no_id)
                        ->count();

            if ($count == 0) {
                throw new \Exception('Tidak ada data yang memenuhi syarat!');
            }

            // =========================
            // 6. UPDATE MASTER BARANG
            // =========================
            DB::update("
                UPDATE nwmasbar m
                JOIN nwtandareturd d 
                    ON m.KDBAR = d.KDBAR
                SET  
                    m.TD_OD = '*', 
                    m.ALASAN = d.KET, 
                    m.TG_TD_OD = DATE(NOW())
                WHERE d.NO_BUKTI = ?
            ", [$no_bukti]);

            DB::commit();

            return redirect()->back()->with('status', 
                'Proses otomatis berhasil! No Bukti: ' . $no_bukti
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function posting($id)
    {
        DB::beginTransaction();

        try {

            // Update RETUR berdasarkan input transaksi (T / Y)
            DB::update("
                UPDATE nwmasbar a
                JOIN nwtandareturd b ON a.KDBAR = b.KDBAR
                JOIN nwtandaretur c ON c.NO_ID = b.ID
                SET a.RETUR = b.RETUR_B
                WHERE c.NO_ID = ?
            ", [$id]);

            // Update header jadi posted
            DB::update("
                UPDATE nwtandaretur
                SET POSTED = 1
                WHERE NO_ID = ?
            ", [$id]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diposting'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
