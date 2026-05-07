<?php
namespace App\Http\Controllers\OTransaksi;

use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DataTables;
use DB;
use Illuminate\Http\Request;

class PostingController extends Controller
{
    public function index(Request $request)
    {
        switch (strtoupper($request->flagz)) {
            case 'BS':
                $judul = "Posting Beli";
                break;

            case 'RO':
                $judul = "Post Retur ke TGZ";
                break;

            case 'RM':
                $judul = "Post Terima Retur Outlet";
                break;

            case 'JT':
                $judul = "Posting Penjualan";
                break;

            case 'RX':
                $judul = "Posting Retur";
                break;

            case 'KB':
                $judul = "Posting Stock Opname";
                break;

            case 'KO':
                $judul = "Posting Order Outlet";
                break;

            case 'BO':
                $judul = "Posting Terima TGZ";
                break;

            case 'HJ':
                $judul = "Posting Harga Jual";
                break;

            default:
                $judul = "Posting Transaksi";
                break;
        }

        return view('otransaksi_posting.post')->with([
            'judul' => $judul,
            'flagz' => strtoupper($request->flagz)
        ]);
    }

    public function getPosting(Request $request)
    {
        $periode = $request->session()->has('periode')
            ? $request->session()->get('periode')['bulan'] . '/' . $request->session()->get('periode')['tahun']
            : '';

        $FLAGZ = strtoupper($request->flagz);
        $query = collect();

        switch ($FLAGZ) {

            case 'BS': // Posting Beli
                $query = DB::table('nwagend')
                    ->select('NO_ID', 'NO_BUKTI', 'TGL', 'KODES', 'NAMAS',
                        'total_qty AS TOTAL_QTY', 'total AS TOTAL', 'nett AS NETT',
                        'notes AS NOTES', 'TYPE', 'POSTED')
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;


            case 'BO': // Terima TGZ
            case 'RX':  // Retur
                $query = DB::table('belibsn')
                    ->select('NO_ID', 'NO_BUKTI', 'TGL', 'KODES', 'NAMAS',
                        'total_qty AS TOTAL_QTY', 'total AS TOTAL', 'nett AS NETT',
                        'notes AS NOTES', 'TYPE', 'POSTED')
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;

            case 'RO': // Retur ke TGZ
            case 'RM': // Terima retur outlet
                $query = DB::table('bretur')
                    ->select('NO_ID', 'NO_BUKTI', 'TGL', 'KODES', 'NAMAS',
                        'total_qty AS TOTAL_QTY', 'total AS TOTAL', 'nett AS NETT',
                        'notes AS NOTES', 'TYPE', 'POSTED')
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;

            case 'KB': // Stock opname / koreksi
                $query = DB::table('bstockb')
                    ->selectRaw("NO_ID, NO_BUKTI, TGL, KODES, NAMAS, TOTAL_QTY, TOTAL, '' AS NETT, NOTES, TYPE, POSTED")
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;


            case 'HJ': // Harga jual
                $query = DB::table('bhrg')
                    ->selectRaw("NO_ID, NO_BUKTI, TGL, KODES, NAMAS, TOTAL_QTY, TOTAL, '' AS NETT, NOTES, TYPE, POSTED")
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;

            case 'KO': // Order outlet
            case 'JT':  // Penjualan
                $query = DB::table('bstocka')
                    ->select('NO_ID', 'NO_BUKTI', 'TGL',
                        DB::raw("'' AS KODES"),
                        DB::raw("'' AS NAMAS"),
                        'TOTAL_QTY', 'TOTAL',
                        DB::raw("'' AS NETT"),
                        'NOTES', 'TYPE', 'POSTED')
                    ->where('POSTED', 0)
                    // ->where('PER', $periode)
                    ->where('FLAG', $FLAGZ)
                    ->orderBy('NO_BUKTI', 'ASC')
                    ->get();
                break;

            default:
                $query = collect();
                break;
        }

        return Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('cek', function ($row) {
                $checked = $row->POSTED == 1 ? 'checked disabled' : '';
                return "
                    <div class='d-flex justify-content-center align-items-center'>
                        <input type='checkbox' name='cek[]' class='form-check-input' value='{$row->NO_ID}' {$checked}>
                    </div>
                ";
            })
            ->rawColumns(['cek'])
            ->make(true);
    }

    public function proses(Request $request)
    {
        $FLAGZ = $request->flagz;
        $CBG   = Auth::user()->CBG;
        $user  = Auth::user()->username;

        $JNS = $request->jenis ?? '';

        // periode
        $periode = session('periode')
            ? session('periode')['bulan'] . '/' . session('periode')['tahun']
            : '';

        // cek periode
        $cekperid = DB::table('perid')->where('PERIO', $periode)->first();

        if ($cekperid && $cekperid->POSTED == 1) {
            return redirect('/posting/index?flagz='.$FLAGZ.'&jenis='.$JNS)
                ->with('status', 'Maaf Periode sudah ditutup!')
                ->with(['flagz' => $FLAGZ, 'jenis' => $JNS]);
        }

        $CEK = $request->input('cek');
        $hasil = [];

        if (!$CEK) {
            return redirect()->back()->with('status', 'Tidak ada data dipilih');
        }

        DB::beginTransaction();

        try {

            foreach ($CEK as $key => $value) {

                $no_bukti = $value; // FIX UTAMA

                /* =========================
                * FLAG BL
                * ========================= */
                if ($FLAGZ == 'BL') {

                    $header = DB::table('nwagend')
                        ->where('NO_BUKTI', $no_bukti)
                        ->lockForUpdate()
                        ->first();

                    if (!$header || $header->POSTED == 1) continue;

                    $details = DB::table('nwagendd')
                        ->where('NO_BUKTI', $no_bukti)
                        ->get();

                    foreach ($details as $row) {

                        DB::update("
                            UPDATE nwmasbard
                            SET MA00 = MA00 - ?,
                                AK00 = AW00 + (MA00 - ?) - KE00 + LN00
                            WHERE KD_BRG = ? AND CBG = ?
                        ", [
                            $row->QTY,
                            $row->QTY,
                            $row->KD_BRG,
                            $CBG
                        ]);
                    }

                    DB::table('nwagend')
                        ->where('NO_BUKTI', $no_bukti)
                        ->update(['POSTED' => 1]);
                }

                /* =========================
                * FLAG ROP / RO / RM
                * ========================= */
                elseif ($FLAGZ == 'ROP') {

                    $cbg = $CBG;

                    $header = DB::table('bretur')
                        ->where('no_bukti', $no_bukti)
                        ->lockForUpdate()
                        ->first();

                    if (!$header || $header->POSTED == 1) continue;

                    $details = DB::table('breturd')
                        ->where('no_bukti', $no_bukti)
                        ->get();

                    foreach ($details as $row) {

                        $qty = $row->qty;

                        // RO
                        DB::update("
                            UPDATE brgbsnd
                            SET ln00 = ln00 - ?,
                                ak00 = aw00 + ma00 - ke00 + (ln00 - ?)
                            WHERE KD_BRG = ? AND CBG = ?
                        ", [$qty, $qty, $row->KD_BRG, $cbg]);
                    }

                    DB::table('bretur')
                        ->where('no_bukti', $no_bukti)
                        ->update([
                            'POSTED' => 1,
                            'tgl_posted' => now()
                        ]);

                    $hasil[] = $no_bukti;
                }

                /* =========================
                * FLAG KB
                * ========================= */
                elseif ($FLAGZ == 'KB') {

                    $cbg = $CBG;

                    $details = DB::table('breturd')
                        ->where('no_bukti', $no_bukti)
                        ->get();

                    foreach ($details as $d) {

                        DB::update("
                            UPDATE brgbsnd
                            SET ln00 = ln00 - ?,
                                ak00 = aw00 + ma00 - ke00 + (ln00 - ?)
                            WHERE KD_BRG = ? AND CBG = ?
                        ", [$d->qty, $d->qty, $d->KD_BRG, $cbg]);
                    }

                    DB::table('bretur')
                        ->where('no_bukti', $no_bukti)
                        ->update([
                            'POSTED' => 1,
                            'tgl_posted' => now()
                        ]);

                    $hasil[] = $no_bukti;
                }

                /* =========================
                * FLAG HJ
                * ========================= */
                elseif ($FLAGZ == 'HJ') {

                    $NO_ID = $request->input('NO_ID');

                    if (!$NO_ID) continue;

                    $no_bukti_list = DB::table('bhrgd')
                        ->whereIn('NO_ID', $NO_ID)
                        ->pluck('NO_BUKTI')
                        ->unique();

                    DB::table('bhrg')
                        ->whereIn('NO_BUKTI', $no_bukti_list)
                        ->update(['POSTED' => 1]);

                    $hasil = $no_bukti_list->toArray();
                }

                // else if ($JNS == 'korpeba' && $FLAGZ == 'TS') {
                //     $no_buktix = DB::select("SELECT NO_BUKTI FROM stockb WHERE NO_ID=" . $CEK[$key]);
                //     $no_bukti  = $no_buktix[0]->NO_BUKTI;

                //     // Update KDLAKU dari brgdt
                //     DB::statement("UPDATE stockbd, stockb
                //         SET stockbd.CBG = stockb.CBG, stockbd.PER = stockb.PER
                //         WHERE stockbd.NO_BUKTI = stockb.NO_BUKTI
                //         AND stockb.NO_BUKTI = '$no_bukti';
                //     ");

                //     DB::statement("UPDATE stockbd, brgdt
                //         SET stockbd.KDLAKU = brgdt.KDLAKU
                //         WHERE stockbd.KD_BRG = brgdt.KD_BRG
                //         AND stockbd.NO_BUKTI = '$no_bukti';
                //     ");

                //     // Validasi bulan & tahun sama dengan sekarang
                //     $cekBukti = DB::select("SELECT NO_BUKTI FROM stockb
                //         WHERE NO_BUKTI = '$no_bukti'
                //         AND CBG = '$CBG'
                //         AND MONTH(TGL) = MONTH(NOW())
                //         AND YEAR(TGL) = YEAR(NOW())
                //     ");

                //     if (count($cekBukti) > 0) {
                //         $details = DB::select("SELECT stockbd.NO_ID, stockbd.KD_BRG, stockbd.QTY, stockbd.FLAG, brgdt.KDLAKU
                //             FROM stockbd
                //             JOIN brgdt ON stockbd.KD_BRG = brgdt.KD_BRG
                //             WHERE stockbd.NO_BUKTI = '$no_bukti' AND brgdt.CBG = '$CBG'
                //         ");

                //         foreach ($details as $item) {
                //             if ($item->FLAG == 'TS') {
                //                 // === Update stok toko & retur ===
                //                 DB::statement("UPDATE brgdt SET
                //                         KE00 = KE00 + $item->QTY,
                //                         AK00 = AW00 + MA00 - KE00 + LN00,
                //                         RLN00 = RLN00 + $item->QTY,
                //                         RAK00 = RAW00 + RMA00 - RKE00 + RLN00
                //                     WHERE KD_BRG = '$item->KD_BRG' AND CBG = '$CBG'
                //                 ");
                //             }
                //         }

                //         // Panggil prosedur posting akhir
                //         DB::statement("CALL poststkb('$no_bukti')");

                //     } else {
                //         $hasil .= "No Bukti $no_bukti tidak bisa diposting / terlambat posting! ; ";
                //     }
                // } else if ($JNS == 'retura' && $FLAGZ == 'RR') {
                //     $CBG  = Auth::user()->CBG;
                //     $user = Auth::user()->username;

                //     foreach ($CEK as $key => $value) {
                //         $dataRetur = DB::select("SELECT NO_BUKTI FROM retur WHERE NO_ID = " . $CEK[$key]);
                //         if (count($dataRetur) == 0) {
                //             continue;
                //         }

                //         $bukti = trim($dataRetur[0]->NO_BUKTI);

                //         // === Update cabang & periode dari retur ke returd ===
                //         DB::statement("UPDATE returd
                //             JOIN retur ON returd.NO_BUKTI = retur.NO_BUKTI
                //             SET returd.CBG = retur.CBG,
                //                 returd.PER = retur.PER
                //             WHERE retur.NO_BUKTI = '$bukti'
                //         ");

                //         // === Update KDLAKU dari brgdt ===
                //         DB::statement("UPDATE returd
                //             JOIN brgdt ON returd.KD_BRG = brgdt.KD_BRG
                //             SET returd.KDLAKU = brgdt.KDLAKU
                //             WHERE returd.NO_BUKTI = '$bukti'
                //         ");

                //         // === Validasi: hanya boleh posting bulan & tahun berjalan ===
                //         $cekBukti = DB::select("SELECT NO_BUKTI FROM retur
                //             WHERE NO_BUKTI = '$bukti'
                //             AND CBG = '$CBG'
                //             AND MONTH(TGL) = MONTH(NOW())
                //             AND YEAR(TGL) = YEAR(NOW())
                //         ");

                //         if (count($cekBukti) > 0) {
                //             DB::statement("UPDATE retur
                //             SET POSTED = 1,
                //                 TGL_POSTED = NOW()
                //             WHERE NO_BUKTI = '$bukti'
                //         ");

                //         } else {
                //             $hasil .= "$bukti tidak bisa diposting / terlambat posting! ; ";
                //         }
                //     }
                // } else if ($JNS == 'retura') {

                //     $CBG   = Auth::user()->CBG;
                //     $FLAGG = $FLAGZ;
                //     // dd($FLAGG);

                //     if (empty($CEK) || ! is_array($CEK)) {
                //         return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih untuk diproses.']);
                //     }

                //     $hasil = [];

                //     foreach ($CEK as $id) {
                //         $retur = DB::table('retur')->where('NO_ID', $id)->first();
                //         if (! $retur) {
                //             continue;
                //         }

                //         $bukti = trim($retur->NO_BUKTI);

                //         // === 1. Sinkronisasi CBG, PER, KDLAKU ===
                //         DB::statement("
                //             UPDATE returd
                //             JOIN retur ON returd.NO_BUKTI = retur.NO_BUKTI
                //             SET returd.CBG = retur.CBG,
                //                 returd.PER = retur.PER
                //             WHERE retur.NO_BUKTI = ?
                //         ", [$bukti]);

                //         DB::statement("
                //             UPDATE returd
                //             JOIN brgdt ON returd.KD_BRG = brgdt.KD_BRG
                //             SET returd.KDLAKU = brgdt.KDLAKU
                //             WHERE returd.NO_BUKTI = ?
                //         ", [$bukti]);

                //         $valid = DB::table('retur')
                //             ->where('NO_BUKTI', $bukti)
                //             ->where('CBG', $CBG)
                //         // ->whereRaw('MONTH(TGL) = MONTH(NOW()) AND YEAR(TGL) = YEAR(NOW())')
                //             ->exists();

                //         if (! $valid) {
                //             $hasil[] = "$bukti tidak bisa diposting / terlambat posting!";
                //             continue;
                //         }

                //         $details = DB::table('returd')
                //             ->join('brgdt', 'returd.KD_BRG', '=', 'brgdt.KD_BRG')
                //             ->select('returd.KD_BRG', 'returd.QTY', 'brgdt.KDLAKU')
                //             ->where('returd.NO_BUKTI', $bukti)
                //             ->get();

                //         foreach ($details as $item) {
                //             $kdbrg  = $item->KD_BRG;
                //             $qty    = (float) $item->QTY;
                //             $kdlaku = $item->KDLAKU;

                //             switch ($FLAGG) {
                //                 case 'RV':
                //                     // Tidak ada perubahan stok (posting RR)
                //                     break;

                //                 case 'RZ':
                //                     if (in_array($kdlaku, ['0', '1'])) {
                //                         // Tambah stok gudang
                //                         DB::statement("UPDATE brgd
                //                         SET ln00 = ln00 + ?, ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = 'TGZ'", [$qty, $kdbrg]);
                //                         DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?, gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = 'TGZ'", [$qty, $kdbrg]);
                //                     } else {
                //                         // Tambah stok retur
                //                         DB::statement("UPDATE brgdt
                //                         SET rln00 = rln00 + ?, rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = ? AND CBG = 'TGZ'", [$qty, $kdbrg]);
                //                     }
                //                     break;

                //                 case 'VR':
                //                     // +Toko, -Retur
                //                     DB::statement("UPDATE brgdt
                //                     SET ln00 = ln00 + ?,
                //                         ak00 = aw00 + ma00 - ke00 + ln00,
                //                         RKE00 = RKE00 + ?,
                //                         rak00 = raw00 + rma00 - rke00 + rln00
                //                     WHERE KD_BRG = ? AND CBG = ?", [$qty, $qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'OZ':
                //                 case 'OX':
                //                     if (in_array($kdlaku, ['0', '1'])) {
                //                         // Tambah gudang
                //                         DB::statement("UPDATE brgd
                //                         SET ln00 = ln00 + ?, ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                         DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?, gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     } else {
                //                         // Tambah toko
                //                         DB::statement("UPDATE brgdt
                //                         SET ln00 = ln00 + ?, ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     }
                //                     break;

                //                 case 'RG':
                //                     if (in_array($kdlaku, ['0', '1'])) {
                //                         // Gudang berkurang, retur bertambah
                //                         DB::statement("UPDATE brgd
                //                         SET KE00 = KE00 - ?, ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     } else {
                //                         // Retur berkurang
                //                         DB::statement("UPDATE brgdt
                //                         SET RKE00 = RKE00 - ?, rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     }
                //                     break;

                //                 case 'GR':
                //                     // Terima tukar guling
                //                     DB::statement("UPDATE brgdt
                //                     SET ln00 = ln00 + ?, ak00 = aw00 + ma00 - ke00 + ln00
                //                     WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     DB::statement("UPDATE brgdt
                //                     SET RKE00 = RKE00 + ?, rak00 = raw00 + rma00 - rke00 + rln00
                //                     WHERE KD_BRG = ? AND CBG = ?", [$qty, $kdbrg, $CBG]);
                //                     break;
                //             }
                //         }

                //         // Jika flag OX, update penerimaan di TGZ.stockazd
                //         if ($FLAGG === 'OX') {
                //             DB::statement("
                //         UPDATE tgz.stockazd A
                //         JOIN returd B ON A.NO_BUKTI = B.BUKTI_PO AND A.KD_BRG = B.KD_BRG
                //         SET A.BUKTI_OX = B.NO_BUKTI, A.QTY_OX = B.QTY
                //         WHERE B.NO_BUKTI = ?
                //     ", [$bukti]);
                //         }

                //         $hasil[] = "$bukti berhasil diposting.";
                //     }
                //     DB::table('retur')
                //         ->where('NO_BUKTI', $bukti)
                //         ->update([
                //             'POSTED'     => 1,
                //             'TGL_POSTED' => now(),
                //         ]);

                // } else if ($JNS == 'jual' && $FLAGZ == 'JT') {
                //     $CBG  = Auth::user()->CBG;
                //     $user = Auth::user()->username;

                //     foreach ($CEK as $key => $value) {
                //         $dataRetur = DB::select("SELECT NO_BUKTI FROM stocka WHERE NO_ID = " . $CEK[$key]);
                //         if (count($dataRetur) == 0) {
                //             continue;
                //         }

                //         $no_bukti = trim($dataRetur[0]->NO_BUKTI);

                //         // === 1. Update KDLAKU & Periode ===
                //         DB::statement("UPDATE stockad
                //             JOIN stocka ON stockad.NO_BUKTI = stocka.NO_BUKTI
                //             SET stockad.CBG = stocka.CBG, stockad.PER = stocka.PER
                //             WHERE stockad.NO_BUKTI = '$no_bukti'
                //         ");

                //         DB::statement("
                //             UPDATE stockad
                //             JOIN brgdt ON stockad.KD_BRG = brgdt.KD_BRG
                //             SET stockad.KDLAKU = brgdt.KDLAKU
                //             WHERE stockad.NO_BUKTI = '$no_bukti'
                //         ");

                //         // === 2. Cek apakah masih periode berjalan ===
                //         $cekPeriode = DB::select("SELECT NO_BUKTI FROM stocka
                //             WHERE NO_BUKTI = '$no_bukti'
                //             AND CBG = '$CBG'
                //             AND MONTH(TGL) = MONTH(NOW())
                //             AND YEAR(TGL) = YEAR(NOW())
                //         ");

                //         if (count($cekPeriode) == 0) {
                //             continue; // skip posting
                //         }

                //         // === 3. Ambil detail stok ===
                //         $details = DB::select("SELECT stockad.QTY, stockad.KD_BRG, stockad.FLAG, stockad.ABL, brgdt.KDLAKU
                //             FROM stockad
                //             JOIN brgdt ON stockad.KD_BRG = brgdt.KD_BRG
                //             WHERE stockad.NO_BUKTI = ? AND brgdt.CBG = ?
                //         ", [$no_bukti, $CBG]);

                //         foreach ($details as $d) {
                //             switch ($d->FLAG) {
                //                 case 'OD':
                //                     DB::statement("UPDATE brgdt
                //                             SET KE00 = KE00 + ?, AK00 = AW00 + MA00 - KE00 + LN00, TGL_OO = CURDATE()
                //                             WHERE KD_BRG = ? AND CBG = ?
                //                         ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     break;

                //                 case 'OT':
                //                     // Kurangi Gudang
                //                     DB::statement("UPDATE brgd
                //                         SET KE00 = KE00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                         WHERE KD_BRG = ? AND CBG = ?", [$d->QTY, $d->KD_BRG, $CBG]);

                //                     // Tambah Toko
                //                     DB::statement("UPDATE brgdt
                //                     SET MA00 = MA00 + ?, AK00 = AW00 + MA00 - KE00 + LN00,
                //                         GKE00 = GKE00 + ?, GAK00 = GAW00 + GMA00 - GKE00 + GLN00
                //                     WHERE KD_BRG = ? AND CBG = ?", [$d->QTY, $d->QTY, $d->KD_BRG, $CBG]);
                //                     break;

                //                 case 'OO':
                //                     if ($d->ABL == 'GD') {
                //                         // Ngurangi Gudang, Nambah BRGDT
                //                         DB::statement("UPDATE brgd
                //                             SET KE00 = KE00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                             WHERE KD_BRG = ? AND CBG = ?", [$d->QTY, $d->KD_BRG, $CBG]);

                //                         DB::statement("UPDATE brgdt
                //                         SET GKE00 = GKE00 + ?, GAK00 = GAW00 + GMA00 - GKE00 + GLN00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     } else {
                //                         // Ngurangi Toko
                //                         DB::statement("UPDATE brgdt
                //                         SET KE00 = KE00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     }
                //                     break;

                //                 case 'JT':
                //                     // Jual Toko → Masuk Toko/Gudang
                //                     if ($CBG == 'DCK') {
                //                         DB::statement("UPDATE brgdt
                //                             SET MA00 = MA00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                             WHERE KD_BRG = ? AND CBG = ?
                //                         ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     } elseif ($d->KDLAKU == '0' || $d->KDLAKU == '1') {
                //                         // Nambah Gudang
                //                         DB::statement("UPDATE brgd
                //                                 SET MA00 = MA00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                                 WHERE KD_BRG = ? AND CBG = ?
                //                             ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                         DB::statement("UPDATE brgdt
                //                                 SET GMA00 = GMA00 + ?, GAK00 = GAW00 + GMA00 - GKE00 + GLN00
                //                                 WHERE KD_BRG = ? AND CBG = ?
                //                             ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     } else {
                //                         DB::statement("UPDATE brgdt
                //                             SET MA00 = MA00 + ?, AK00 = AW00 + MA00 - KE00 + LN00
                //                             WHERE KD_BRG = ? AND CBG = ?
                //                         ", [$d->QTY, $d->KD_BRG, $CBG]);
                //                     }
                //                     break;

                //             }
                //         }

                //     }
                //     DB::statement("CALL poststka('$no_bukti')");
                // } else if ($JNS == 'order_toko' && $FLAGZ == 'OT') {

                //     $CBG = Auth::user()->CBG;

                //     foreach ($CEK as $id) {

                //         $header = DB::selectOne(
                //             "SELECT NO_BUKTI FROM stocka WHERE NO_ID = ?",
                //             [$id]
                //         );
                //         if (! $header) {
                //             continue;
                //         }

                //         $no_bukti = trim($header->NO_BUKTI);

                //         // Update CBG & PER
                //         DB::statement("UPDATE stockad a
                //                         JOIN stocka b ON a.NO_BUKTI = b.NO_BUKTI
                //                         SET a.CBG = b.CBG,
                //                             a.PER = b.PER
                //                         WHERE a.NO_BUKTI = ?
                //                     ", [$no_bukti]);

                //         // Update KDLAKU (WAJIB pakai CBG)
                //         DB::statement("UPDATE stockad a
                //                         JOIN brgdt b
                //                         ON a.KD_BRG = b.KD_BRG
                //                         AND b.CBG = ?
                //                         SET a.KDLAKU = b.KDLAKU
                //                         WHERE a.NO_BUKTI = ?
                //                     ", [$CBG, $no_bukti]);

                //         $valid = DB::selectOne("SELECT NO_BUKTI FROM stocka
                //                                     WHERE NO_BUKTI = ?
                //                                     AND CBG = ?
                //                                     AND MONTH(TGL) = MONTH(NOW())
                //                                     AND YEAR(TGL) = YEAR(NOW())
                //                                 ", [$no_bukti, $CBG]);

                //         if (! $valid) {
                //             continue;
                //         }

                //         $details = DB::select("SELECT a.QTY, a.KD_BRG
                //                                 FROM stockad a
                //                                 JOIN brgdt b
                //                                 ON a.KD_BRG = b.KD_BRG
                //                                 AND b.CBG = ?
                //                                 WHERE a.NO_BUKTI = ?
                //                                 AND a.FLAG = 'OT'
                //                             ", [$CBG, $no_bukti]);

                //         foreach ($details as $d) {

                //             // Kurangi Gudang (BRGD)
                //             DB::statement("UPDATE brgd
                //                             SET KE00 = KE00 + ?,
                //                                 AK00 = AW00 + MA00 - KE00 + LN00
                //                             WHERE KD_BRG = ?
                //                             AND CBG = ?
                //                         ", [$d->QTY, $d->KD_BRG, $CBG]);

                //             // Tambah Toko + Gudang (BRGDT)
                //             DB::statement("UPDATE brgdt
                //                             SET TK   = '',
                //                                 MA00 = MA00 + ?,
                //                                 AK00 = AW00 + MA00 - KE00 + LN00,
                //                                 GKE00 = GKE00 + ?,
                //                                 GAK00 = GAW00 + GMA00 - GKE00 + GLN00
                //                             WHERE KD_BRG = ?
                //                             AND CBG = ?
                //                         ", [$d->QTY, $d->QTY, $d->KD_BRG, $CBG]);

                //             // C. LOGIKA TPO
                //             if ($d->QTY > 0) {

                //                 $po = DB::selectOne("SELECT NO_PO FROM stocka WHERE NO_BUKTI = ?", [$no_bukti]);

                //                 if ($po && $po->NO_PO) {

                //                     DB::statement("DELETE FROM tpo
                //                                     WHERE NO_BUKTI = ?
                //                                     AND KD_BRG = ?
                //                                 ", [$po->NO_PO, $d->KD_BRG]);

                //                     DB::statement("UPDATE tpo SET BKTK = ''
                //                                         WHERE NO_BUKTI = ?
                //                                     ", [$po->NO_PO]);
                //                 }
                //             }
                //         }
                //     }
                //     DB::statement("UPDATE stocka
                //         SET POSTED = 1, TGL_POSTED = NOW()
                //         WHERE NO_BUKTI = '$no_bukti'
                //     ");
                // } else if ($JNS == 'musnahff' && $FLAGZ == 'MF') {
                //     $no_buktix = DB::select("SELECT NO_BUKTI, CBG FROM musnah WHERE NO_ID=" . $CEK[$key] . ";");
                //     $no_bukti  = $no_buktix[0]->NO_BUKTI;
                //     $cbg       = $no_buktix[0]->CBG;

                //     $cekPosting = DB::select("SELECT NO_BUKTI
                //                     FROM musnah
                //                     WHERE NO_BUKTI = '$no_bukti'
                //                     AND CBG = '$cbg'
                //                     -- // AND MONTH(TGL) = MONTH(NOW())
                //                     -- // AND YEAR(TGL) = YEAR(NOW())
                //                 ");

                //     if (count($cekPosting) > 0) {
                //         $detail = DB::select("SELECT d.NO_ID, d.KD_BRG, d.QTY, b.KDLAKU
                //             FROM musnahd d
                //             JOIN brgdt b ON d.KD_BRG = b.KD_BRG
                //             WHERE d.NO_BUKTI = '$no_bukti'
                //             AND b.CBG = '$cbg'
                //         ");

                //         foreach ($detail as $item) {
                //             // Update stok: rKE00 bertambah dan rak00 direkalkulasi
                //             DB::statement("UPDATE brgdt SET
                //                         rKE00 = rKE00 + $item->QTY,
                //                         rak00 = raw00 + rma00 - rke00 + rln00
                //                     WHERE KD_BRG = '$item->KD_BRG'
                //                     AND CBG = '$cbg'
                //                 ");
                //         }

                //         // Tandai musnah sudah diposting
                //         DB::statement("UPDATE musnah
                //                             SET POSTED = 1, TGL_POSTED = NOW()
                //                             WHERE NO_BUKTI = '$no_bukti'
                //                         ");
                //     }
                // } else if ($JNS == 'musnahgd' && $FLAGZ == 'MR') {
                //     $no_buktix = DB::select("SELECT NO_BUKTI, CBG FROM musnah WHERE NO_ID=" . $CEK[$key] . ";");
                //     $no_bukti  = $no_buktix[0]->NO_BUKTI;
                //     $cbg       = $no_buktix[0]->CBG;

                //     $cekPosting = DB::select("SELECT NO_BUKTI
                //                     FROM musnah
                //                     WHERE NO_BUKTI = '$no_bukti'
                //                     AND CBG = '$cbg'
                //                     -- // AND MONTH(TGL) = MONTH(NOW())
                //                     -- // AND YEAR(TGL) = YEAR(NOW())
                //                 ");

                //     if (count($cekPosting) > 0) {
                //         $detail = DB::select("SELECT d.NO_ID, d.KD_BRG, d.QTY, b.KDLAKU
                //             FROM musnahd d
                //             JOIN brgdt b ON d.KD_BRG = b.KD_BRG
                //             WHERE d.NO_BUKTI = '$no_bukti'
                //             AND b.CBG = '$cbg'
                //         ");

                //         foreach ($detail as $item) {
                //             // Update stok: rKE00 bertambah dan rak00 direkalkulasi
                //             DB::statement("UPDATE brgdt SET
                //                         rKE00 = rKE00 + $item->QTY,
                //                         rak00 = raw00 + rma00 - rke00 + rln00
                //                     WHERE KD_BRG = '$item->KD_BRG'
                //                     AND CBG = '$cbg'
                //                 ");
                //         }

                //         // Tandai musnah sudah diposting
                //         DB::statement("UPDATE musnah
                //                             SET POSTED = 1, TGL_POSTED = NOW()
                //                             WHERE NO_BUKTI = '$no_bukti'
                //                         ");
                //     }
                // } else if ($JNS == 'manual') {

                //     $CBG   = Auth::user()->CBG;
                //     $FLAGG = $FLAGZ;
                //     $hasil = [];

                //     if (empty($CEK) || ! is_array($CEK)) {
                //         return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih untuk diproses.']);
                //     }

                //     foreach ($CEK as $id) {
                //         $stockb = DB::table('stockb')->where('NO_ID', $id)->first();
                //         if (! $stockb) {
                //             continue;
                //         }

                //         $bukti = trim($stockb->NO_BUKTI);

                //         // === 1. Update KDLAKU + CBG + PER
                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN stockb ON stockbd.NO_BUKTI = stockb.NO_BUKTI
                //             SET stockbd.CBG = stockb.CBG,
                //                 stockbd.PER = stockb.PER
                //             WHERE stockb.NO_BUKTI = ?
                //         ", [$bukti]);

                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN brgdt ON stockbd.KD_BRG = brgdt.KD_BRG
                //             SET stockbd.KDLAKU = brgdt.KDLAKU
                //             WHERE stockbd.NO_BUKTI = ?
                //         ", [$bukti]);

                //         // === 2. Validasi periode bulan & tahun berjalan
                //         $valid = DB::table('stockb')
                //             ->where('NO_BUKTI', $bukti)
                //             ->where('CBG', $CBG)
                //         // ->whereRaw('MONTH(TGL) = MONTH(NOW()) AND YEAR(TGL) = YEAR(NOW())')
                //             ->exists();

                //         if (! $valid) {
                //             $hasil[] = "$bukti tidak bisa diposting / terlambat posting!";
                //             continue;
                //         }

                //         // === 3. Ambil detail barang (stockbd)
                //         $details = DB::table('stockbd')
                //             ->join('brgdt', function ($join) {
                //                 $join->on('stockbd.KD_BRG', '=', 'brgdt.KD_BRG')
                //                     ->on('brgdt.CBG', '=', 'stockbd.CBG');
                //             })
                //             ->select('stockbd.KD_BRG', 'stockbd.QTY', 'brgdt.KDLAKU')
                //             ->where('stockbd.NO_BUKTI', $bukti)
                //             ->get();

                //         foreach ($details as $item) {
                //             $kdbrg  = $item->KD_BRG;
                //             $qty    = (float) $item->QTY;
                //             $kdlaku = $item->KDLAKU;

                //             switch ($FLAGG) {
                //                 case 'GS':
                //                     // Nambah gudang
                //                     DB::statement("
                //                 UPDATE brgd
                //                 SET ln00 = ln00 + ?,
                //                     ak00 = aw00 + ma00 - ke00 + ln00
                //                 WHERE KD_BRG = ? AND CBG = ?
                //             ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'MG':
                //                     DB::statement("UPDATE brgd
                //                         SET ln00 = ln00 + ?,
                //                             ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'KR':
                //                     DB::statement("UPDATE brgdt
                //                         SET rln00 = rln00 + ?,
                //                             rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;
                //             }
                //         }

                //         DB::statement("CALL poststkb(?)", [$bukti]);

                //         $hasil[] = "$bukti berhasil diposting.";
                //     }

                // } else if ($JNS == 'korekrt') {

                //     $CBG   = Auth::user()->CBG;
                //     $FLAGG = $FLAGZ;
                //     $hasil = [];

                //     if (empty($CEK) || ! is_array($CEK)) {
                //         return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih untuk diproses.']);
                //     }

                //     foreach ($CEK as $id) {
                //         $stockb = DB::table('stockb')->where('NO_ID', $id)->first();
                //         if (! $stockb) {
                //             continue;
                //         }

                //         $bukti = trim($stockb->NO_BUKTI);

                //         // === 1. Update KDLAKU + CBG + PER
                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN stockb ON stockbd.NO_BUKTI = stockb.NO_BUKTI
                //             SET stockbd.CBG = stockb.CBG,
                //                 stockbd.PER = stockb.PER
                //             WHERE stockb.NO_BUKTI = ?
                //         ", [$bukti]);

                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN brgdt ON stockbd.KD_BRG = brgdt.KD_BRG
                //             SET stockbd.KDLAKU = brgdt.KDLAKU
                //             WHERE stockbd.NO_BUKTI = ?
                //         ", [$bukti]);

                //         // === 2. Validasi periode bulan & tahun berjalan
                //         $valid = DB::table('stockb')
                //             ->where('NO_BUKTI', $bukti)
                //             ->where('CBG', $CBG)
                //         // ->whereRaw('MONTH(TGL) = MONTH(NOW()) AND YEAR(TGL) = YEAR(NOW())')
                //             ->exists();

                //         if (! $valid) {
                //             $hasil[] = "$bukti tidak bisa diposting / terlambat posting!";
                //             continue;
                //         }

                //         // === 3. Ambil detail barang (stockbd)
                //         $details = DB::table('stockbd')
                //             ->join('brgdt', function ($join) {
                //                 $join->on('stockbd.KD_BRG', '=', 'brgdt.KD_BRG')
                //                     ->on('brgdt.CBG', '=', 'stockbd.CBG');
                //             })
                //             ->select('stockbd.KD_BRG', 'stockbd.QTY', 'brgdt.KDLAKU')
                //             ->where('stockbd.NO_BUKTI', $bukti)
                //             ->get();

                //         foreach ($details as $item) {
                //             $kdbrg  = $item->KD_BRG;
                //             $qty    = (float) $item->QTY;
                //             $kdlaku = $item->KDLAKU;

                //             switch ($FLAGG) {
                //                 case 'GS':
                //                     // Nambah gudang
                //                     DB::statement("
                //                 UPDATE brgd
                //                 SET ln00 = ln00 + ?,
                //                     ak00 = aw00 + ma00 - ke00 + ln00
                //                 WHERE KD_BRG = ? AND CBG = ?
                //             ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'MG':
                //                     DB::statement("UPDATE brgd
                //                         SET ln00 = ln00 + ?,
                //                             ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'KR':
                //                     DB::statement("UPDATE brgdt
                //                         SET rln00 = rln00 + ?,
                //                             rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;
                //             }
                //         }

                //         DB::statement("CALL poststkb(?)", [$bukti]);

                //         $hasil[] = "$bukti berhasil diposting.";
                //     }

                // } else if ($JNS == 'korekgd') {

                //     $CBG   = Auth::user()->CBG;
                //     $FLAGG = $FLAGZ;
                //     $hasil = [];

                //     if (empty($CEK) || ! is_array($CEK)) {
                //         return response()->json(['status' => 'error', 'message' => 'Tidak ada data yang dipilih untuk diproses.']);
                //     }

                //     foreach ($CEK as $id) {
                //         $stockb = DB::table('stockb')->where('NO_ID', $id)->first();
                //         if (! $stockb) {
                //             continue;
                //         }

                //         $bukti = trim($stockb->NO_BUKTI);

                //         // === 1. Update KDLAKU + CBG + PER
                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN stockb ON stockbd.NO_BUKTI = stockb.NO_BUKTI
                //             SET stockbd.CBG = stockb.CBG,
                //                 stockbd.PER = stockb.PER
                //             WHERE stockb.NO_BUKTI = ?
                //         ", [$bukti]);

                //         DB::statement("
                //             UPDATE stockbd
                //             JOIN brgdt ON stockbd.KD_BRG = brgdt.KD_BRG
                //             SET stockbd.KDLAKU = brgdt.KDLAKU
                //             WHERE stockbd.NO_BUKTI = ?
                //         ", [$bukti]);

                //         // === 2. Validasi periode bulan & tahun berjalan
                //         $valid = DB::table('stockb')
                //             ->where('NO_BUKTI', $bukti)
                //             ->where('CBG', $CBG)
                //         // ->whereRaw('MONTH(TGL) = MONTH(NOW()) AND YEAR(TGL) = YEAR(NOW())')
                //             ->exists();

                //         if (! $valid) {
                //             $hasil[] = "$bukti tidak bisa diposting / terlambat posting!";
                //             continue;
                //         }

                //         // === 3. Ambil detail barang (stockbd)
                //         $details = DB::table('stockbd')
                //             ->join('brgdt', function ($join) {
                //                 $join->on('stockbd.KD_BRG', '=', 'brgdt.KD_BRG')
                //                     ->on('brgdt.CBG', '=', 'stockbd.CBG');
                //             })
                //             ->select('stockbd.KD_BRG', 'stockbd.QTY', 'brgdt.KDLAKU')
                //             ->where('stockbd.NO_BUKTI', $bukti)
                //             ->get();

                //         foreach ($details as $item) {
                //             $kdbrg  = $item->KD_BRG;
                //             $qty    = (float) $item->QTY;
                //             $kdlaku = $item->KDLAKU;

                //             switch ($FLAGG) {
                //                 case 'GS':
                //                     // Nambah gudang
                //                     DB::statement("
                //                 UPDATE brgd
                //                 SET ln00 = ln00 + ?,
                //                     ak00 = aw00 + ma00 - ke00 + ln00
                //                 WHERE KD_BRG = ? AND CBG = ?
                //             ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'MG':
                //                     DB::statement("UPDATE brgd
                //                         SET ln00 = ln00 + ?,
                //                             ak00 = aw00 + ma00 - ke00 + ln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);

                //                     DB::statement("UPDATE brgdt
                //                         SET gln00 = gln00 + ?,
                //                             gak00 = gaw00 + gma00 - gke00 + gln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;

                //                 case 'KR':
                //                     DB::statement("UPDATE brgdt
                //                         SET rln00 = rln00 + ?,
                //                             rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = ? AND CBG = ?
                //                     ", [$qty, $kdbrg, $CBG]);
                //                     break;
                //             }
                //         }

                //         DB::statement("CALL poststkb(?)", [$bukti]);

                //         $hasil[] = "$bukti berhasil diposting.";
                //     }

                // } else if ($JNS == 'belrb' && $FLAGZ == 'RB') {
                //     $no_buktix = DB::select("SELECT NO_BUKTI, CBG, NO_PO, KODES, NAMAS, TGL
                //               FROM beli WHERE NO_ID=" . $CEK[$key] . ";");

                //     if (count($no_buktix) == 0) {
                //         throw new \Exception('Data tidak ditemukan untuk Retur Beli!');
                //     }

                //     $no_bukti = $no_buktix[0]->NO_BUKTI;
                //     $cbg      = $no_buktix[0]->CBG;
                //     $no_po    = $no_buktix[0]->NO_PO ?? '';
                //     $kodes    = $no_buktix[0]->KODES ?? '';
                //     $namas    = $no_buktix[0]->NAMAS ?? '';
                //     $tgl      = $no_buktix[0]->TGL ?? '';
                //     $username = auth()->user()->name ?? 'SYSTEM';

                //     // Cek apakah boleh posting di periode berjalan
                //     $cekPosting = DB::select("SELECT NO_BUKTI
                //                                 FROM beli
                //                                 WHERE NO_BUKTI = '$no_bukti'
                //                                 AND CBG = '$cbg'
                //                                 -- AND MONTH(TGL) = MONTH(NOW())
                //                                 -- AND YEAR(TGL) = YEAR(NOW())
                //                             ");

                //     if (count($cekPosting) == 0) {
                //         throw new \Exception("$no_bukti tidak bisa diposting / terlambat posting!");
                //     }
                //     // db tampung tidak ada di 142
                //     // DB::statement("CALL ponline_po_del('$no_bukti')");

                //     $detail = DB::select("SELECT d.NO_ID, d.KD_BRG, d.NA_BRG, d.KEMASAN, d.PPN,
                //                                 d.DISKON1, d.DISKON2, d.DISKON3, d.DISKON4,
                //                                 d.HARGA, d.QTY, d.QTYK, d.SISAPO, d.TOTAL,
                //                                 d.KDLAKU, d.BUKTI_PO
                //                             FROM belid d
                //                             WHERE d.NO_BUKTI = '$no_bukti'
                //                         ");

                //     // Update stok retur beli
                //     foreach ($detail as $item) {
                //         DB::statement("UPDATE brgdt
                //                         SET
                //                             rke00 = rke00 + $item->QTY,
                //                             rak00 = raw00 + rma00 - rke00 + rln00
                //                         WHERE KD_BRG = '$item->KD_BRG'
                //                         AND CBG = '$cbg'
                //                     ");
                //     }

                //     DB::statement("CALL postbl('$no_bukti', '$username')");

                //     DB::statement("UPDATE beli
                //         SET POSTED = 1, TGL_POSTED = NOW()
                //         WHERE NO_BUKTI = '$no_bukti'
                //     ");
                // }

            }

        DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $pesan = count($hasil)
            ? implode(', ', $hasil) . ' berhasil diposting'
            : 'Posting berhasil';

        return redirect('/posting/index?flagz='.$FLAGZ)
            ->with('status', $pesan);
    }

    public function brgbeli($kd_brg, $na_brg, $buktix, $no_po, $tgl, $kodes, $namas, $qty, $harga, $total, $kemasan, $XD, $PPN, $D1, $D2, $D3, $D4)
    {
        // 1. Cek apakah barang sudah ada di brgdbeli
        $barang = DB::table('brgdbeli')->where('KD_BRG', $kd_brg)->first();

        if (! $barang) {
            // Insert jika belum ada
            DB::table('brgdbeli')->insert([
                'kd_brg' => $kd_brg,
                'na_brg' => $na_brg,
            ]);
        }

        DB::table('brgdbeli')->where('KD_BRG', $kd_brg)->update([
            'NO_BUKTI_1' => DB::raw('NO_BUKTI_2'),
            'NO_BUKTI_2' => DB::raw('NO_BUKTI_3'),
            'NO_BUKTI_3' => DB::raw('NO_BUKTI_4'),
            'NO_BUKTI_4' => DB::raw('NO_BUKTI_5'),
            'NO_BUKTI_5' => $buktix,

            'NO_PO_1'    => DB::raw('NO_PO_2'),
            'NO_PO_2'    => DB::raw('NO_PO_3'),
            'NO_PO_3'    => DB::raw('NO_PO_4'),
            'NO_PO_4'    => DB::raw('NO_PO_5'),
            'NO_PO_5'    => $no_po,

            // 'QTY_PO1'    => DB::raw('QTY_PO2'),
            // 'QTY_PO2'    => DB::raw('QTY_PO3'),
            // 'QTY_PO3'    => DB::raw('QTY_PO4'),
            // 'QTY_PO4'    => DB::raw('QTY_PO5'),
            // 'QTY_PO5'    => $qty_po,

            'TGL_1'      => DB::raw('TGL_2'),
            'TGL_2'      => DB::raw('TGL_3'),
            'TGL_3'      => DB::raw('TGL_4'),
            'TGL_4'      => DB::raw('TGL_5'),
            'TGL_5'      => Carbon::parse($tgl)->format('Y-m-d'),

            'KODES_1'    => DB::raw('KODES_2'),
            'KODES_2'    => DB::raw('KODES_3'),
            'KODES_3'    => DB::raw('KODES_4'),
            'KODES_4'    => DB::raw('KODES_5'),
            'KODES_5'    => $kodes,

            'NAMAS_1'    => DB::raw('NAMAS_2'),
            'NAMAS_2'    => DB::raw('NAMAS_3'),
            'NAMAS_3'    => DB::raw('NAMAS_4'),
            'NAMAS_4'    => DB::raw('NAMAS_5'),
            'NAMAS_5'    => $namas,

            'QTY_1'      => DB::raw('QTY_2'),
            'QTY_2'      => DB::raw('QTY_3'),
            'QTY_3'      => DB::raw('QTY_4'),
            'QTY_4'      => DB::raw('QTY_5'),
            'QTY_5'      => $qty,

            'HARGA_1'    => DB::raw('HARGA_2'),
            'HARGA_2'    => DB::raw('HARGA_3'),
            'HARGA_3'    => DB::raw('HARGA_4'),
            'HARGA_4'    => DB::raw('HARGA_5'),
            'HARGA_5'    => $harga,

            'TOTAL_1'    => DB::raw('TOTAL_2'),
            'TOTAL_2'    => DB::raw('TOTAL_3'),
            'TOTAL_3'    => DB::raw('TOTAL_4'),
            'TOTAL_4'    => DB::raw('TOTAL_5'),
            'TOTAL_5'    => $total,

            'KEMASAN_1'  => DB::raw('KEMASAN_2'),
            'KEMASAN_2'  => DB::raw('KEMASAN_3'),
            'KEMASAN_3'  => DB::raw('KEMASAN_4'),
            'KEMASAN_4'  => DB::raw('KEMASAN_5'),
            'KEMASAN_5'  => $kemasan,

            'PPN_1'      => DB::raw('PPN_2'),
            'PPN_2'      => DB::raw('PPN_3'),
            'PPN_3'      => DB::raw('PPN_4'),
            'PPN_4'      => DB::raw('PPN_5'),
            'PPN_5'      => $PPN,

            'XD_1'       => DB::raw('XD_2'),
            'XD_2'       => DB::raw('XD_3'),
            'XD_3'       => DB::raw('XD_4'),
            'XD_4'       => DB::raw('XD_5'),
            'XD_5'       => $XD,

            'DISKON1_1'  => DB::raw('DISKON1_2'),
            'DISKON1_2'  => DB::raw('DISKON1_3'),
            'DISKON1_3'  => DB::raw('DISKON1_4'),
            'DISKON1_4'  => DB::raw('DISKON1_5'),
            'DISKON1_5'  => $D1,

            'DISKON2_1'  => DB::raw('DISKON2_2'),
            'DISKON2_2'  => DB::raw('DISKON2_3'),
            'DISKON2_3'  => DB::raw('DISKON2_4'),
            'DISKON2_4'  => DB::raw('DISKON2_5'),
            'DISKON2_5'  => $D2,

            'DISKON3_1'  => DB::raw('DISKON3_2'),
            'DISKON3_2'  => DB::raw('DISKON3_3'),
            'DISKON3_3'  => DB::raw('DISKON3_4'),
            'DISKON3_4'  => DB::raw('DISKON3_5'),
            'DISKON3_5'  => $D3,

            'DISKON4_1'  => DB::raw('DISKON4_2'),
            'DISKON4_2'  => DB::raw('DISKON4_3'),
            'DISKON4_3'  => DB::raw('DISKON4_4'),
            'DISKON4_4'  => DB::raw('DISKON4_5'),
            'DISKON4_5'  => $D4,
        ]);
    }
}
