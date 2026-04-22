<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use DataTables;
use Auth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

// ganti 2
class VbrgDwController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */



    public function browse(Request $request){
        $KD_BRG = $request->KD_BRG;
        $vbrgdw = DB::SELECT("SELECT * from nwkomponen WHERE KD_BRG = ? ORDER BY KD_BRG ", [$KD_BRG]);

        return response()->json($vbrgdw);
    }
    public function index()
    {

        return view('master_vbrgdw.index');
    }


    public function getVbrgDw()
    {
        // ganti 5

        $vbrg = DB::SELECT("SELECT * from nwkomponen ORDER BY KD_BRG  ");

        return Datatables::of($vbrg)
            ->addIndexColumn()
            
            ->rawColumns(['action'])
            ->make(true);
			
			
    }
    public function store(Request $request)
    {
        $KD_BRG = $request->KD_BRG;
        $NA_BRG = $request->NA_BRG;
        $NO = $request->NO;
        $KODES = $request->KODES;
        $NAMAS = $request->NAMAS;
        $HARGAAWAL = $request->HARGAAWAL;
        $HARGA = $request->HARGA;
        $DISCAWAL = $request->DISCAWAL;
        $DISC = $request->DISC;        
        $DISCAWAL2 = $request->DISCAWAL2;
        $DISC2 = $request->DISC2;       
        $DISCAWAL3 = $request->DISCAWAL3;
        $DISC3 = $request->DISC3;        
        $DISCAWAL4 = $request->DISCAWAL4;
        $DISC4 = $request->DISC4;      
        $PPNAWAL = $request->PPNAWAL;
        $PPN = $request->PPN;
        $STATUS = $request->STATUS;
        foreach ($NO as $i => $value) {

            $harga = (float) str_replace(',', '', $HARGA[$i]);
            $disc = (float) str_replace(',', '', $DISC[$i]);
            $disc2 = (float) str_replace(',', '', $DISC2[$i]);
            $disc3 = (float) str_replace(',', '', $DISC3[$i]);
            $disc4 = (float) str_replace(',', '', $DISC4[$i]);
            $ppn = (float) str_replace(',', '', $PPN[$i]);
            if (isset($STATUS[$i])) {
                $hargaAwal = (float) str_replace(',', '', $HARGAAWAL[$i]);
                $discAwal = (float) str_replace(',', '', $DISCAWAL[$i]);
                $discAwal2 = (float) str_replace(',', '', $DISCAWAL2[$i]);
                $discAwal3 = (float) str_replace(',', '', $DISCAWAL3[$i]);
                $discAwal4 = (float) str_replace(',', '', $DISCAWAL4[$i]);
                $ppnawal = (float) str_replace(',', '', $PPNAWAL[$i]);

                // update jika harga atau disc berubah
                if ($harga != $hargaAwal ||  $disc != $discAwal || $disc2 != $discAwal2 || $disc3 != $discAwal3 || $disc4 != $discAwal4 || $ppn != $ppnawal) {
                    DB::table('nwkomponen')
                        ->where('KD_BRG', $KD_BRG)
                        ->where('NA_BRG', $NA_BRG)
                        ->where('KODES', $KODES[$i])
                        ->where('NAMAS', $NAMAS[$i])
                        ->update([
                            'HARGA' => $harga,
                            'DISC'  => $disc,
                            'DISC2'  => $disc2,
                            'DISC3'  => $disc3,
                            'DISC4'  => $disc4,
                            'PPN'  => $ppn,
                        ]);
                }
            } else {
                // insert jika tidak ada status
                DB::table('nwkomponen')->insert([
                    'KD_BRG' => $KD_BRG,
                    'NA_BRG' => $NA_BRG,
                    'KODES'  => $KODES[$i],
                    'NAMAS'  => $NAMAS[$i],
                    'HARGA'  => $harga,
                    'DISC'   => $disc,
                    'DISC2'   => $disc2,
                    'DISC3'   => $disc3,
                    'DISC4'   => $disc4,
                    'PPN'  => $ppn,
                ]);
            }
        }


        return redirect('/vbrgdw')->with('statusInsert', 'Data baru berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Master\Rute  $rute
     * @return \Illuminate\Http\Response
     */

    

    // ganti 15

    public function edit()
    {



        return view('master_vbrgdw.edit');
    }
}
