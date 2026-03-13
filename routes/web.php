<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Dashboard
// Route::get('/', 'App\Http\Controllers\DashboardController@index')->middleware(['auth']);
// Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index')->middleware(['auth']);
Route::get('/', 'App\Http\Controllers\DashboardController@index')->middleware(['auth']);
Route::get('/dashboard', 'App\Http\Controllers\DashboardController@dashboard_plain')->middleware(['auth']);

// Chart Dashboard
// Route::get('/chart', 'App\Http\Controllers\DashboardController@chart')->middleware(['auth']);

Route::get('/chart', 'App\Http\Controllers\DashboardController@chart')->middleware(['auth']);


// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

// Static Route

// Periode
Route::post('/periode', 'App\Http\Controllers\PeriodeController@index')->middleware(['auth'])->name('periode');

//User Edit

Route::get('/profile', 'App\Http\Controllers\ProfileController@index')->middleware(['auth']);
Route::post('/profile/update', 'App\Http\Controllers\ProfileController@update')->middleware(['auth']);
Route::post('/profile/setting/update', 'App\Http\Controllers\ProfileController@updateSetting')->middleware(['auth']);

////////

// Master Account
Route::get('/account', 'App\Http\Controllers\FMaster\AccountController@index')->middleware(['auth'])->name('account');

Route::get('/account/create', 'App\Http\Controllers\FMaster\AccountController@create')->middleware(['auth'])->name('account/create');
Route::get('/raccount', 'App\Http\Controllers\FReport\RAccountController@report')->middleware(['auth'])->name('raccount');
    // GET ACCOUNT
    Route::get('/get-account', 'App\Http\Controllers\FMaster\AccountController@getAccount')->middleware(['auth'])->name('get-account');
    Route::get('/account/browse', 'App\Http\Controllers\FMaster\AccountController@browse')->middleware(['auth'])->name('accoumt/browse');
    Route::get('/account/browse_nacno', 'App\Http\Controllers\FMaster\AccountController@browse_nacno')->middleware(['auth'])->name('accoumt/browse_nacno');
    
    
    Route::get('/account/browsecash', 'App\Http\Controllers\FMaster\AccountController@browsecash')->middleware(['auth'])->name('accoumt/browsecash');
    Route::get('/account/browsebank', 'App\Http\Controllers\FMaster\AccountController@browsebank')->middleware(['auth'])->name('accoumt/browsebank');
    Route::get('/account/browsecashbank', 'App\Http\Controllers\FMaster\AccountController@browsecashbank')->middleware(['auth'])->name('accoumt/browsecashbank');
    Route::get('/account/browseallacc', 'App\Http\Controllers\FMaster\AccountController@browseallacc')->middleware(['auth'])->name('accoumt/browseallacc');
    Route::get('/get-account-report', 'App\Http\Controllers\FReport\RAccountController@getAccountReport')->middleware(['auth'])->name('get-account-report');
    Route::post('/jasper-account-report', 'App\Http\Controllers\FReport\RAccountController@jasperAccountReport')->middleware(['auth']);
    Route::get('account/cekacc', 'App\Http\Controllers\FMaster\AccountController@cekacc')->middleware(['auth']);
    Route::get('account/browseKel', 'App\Http\Controllers\FMaster\AccountController@browseKel')->middleware(['auth']);
// Dynamic Account
Route::get('/account/edit', 'App\Http\Controllers\FMaster\AccountController@edit')->middleware(['auth'])->name('account.edit');
Route::post('/account/update/{account}', 'App\Http\Controllers\FMaster\AccountController@update')->middleware(['auth'])->name('account.update');
Route::get('/account/delete/{account}', 'App\Http\Controllers\FMaster\AccountController@destroy')->middleware(['auth'])->name('account.delete');

Route::get('/rrl', 'App\Http\Controllers\FReport\RRlController@report')->middleware(['auth'])->name('rrl');
Route::get('/get-rl-report', 'App\Http\Controllers\FReport\RRlController@getRlReport')->middleware(['auth'])->name('get-rl-report');
Route::post('/jasper-rl-report', 'App\Http\Controllers\FReport\RRlController@jasperRlReport')->middleware(['auth']);

Route::get('/rnera', 'App\Http\Controllers\FReport\RNeraController@report')->middleware(['auth'])->name('rnera');
Route::get('/get-nera-report', 'App\Http\Controllers\FReport\RNeraController@getNeraReport')->middleware(['auth'])->name('get-nera-report');
Route::post('/jasper-nera-report', 'App\Http\Controllers\FReport\RNeraController@jasperNeraReport')->middleware(['auth']);



// Master Jenis 
Route::get('/jenis', 'App\Http\Controllers\Master\JenisController@index')->middleware(['auth'])->name('jenis');
Route::post('/jenis/store', 'App\Http\Controllers\Master\JenisController@store')->middleware(['auth'])->name('jenis/store');

    Route::get('/get-jenis', 'App\Http\Controllers\Master\JenisController@getJenis')->middleware(['auth'])->name('get-jenis');
    Route::get('/jenis/browse', 'App\Http\Controllers\Master\JenisController@browse')->middleware(['auth'])->name('jenis/browse');

// Dynamic Merk
Route::get('/jenis/edit', 'App\Http\Controllers\Master\JenisController@edit')->middleware(['auth'])->name('jenis.edit');
Route::post('/jenis/update/{jenis}', 'App\Http\Controllers\Master\JenisController@update')->middleware(['auth'])->name('jenis.update');
Route::get('/jenis/delete/{jenis}', 'App\Http\Controllers\Master\JenisController@destroy')->middleware(['auth'])->name('jenis.delete');


//////////////////////


// Master Merk 
Route::get('/merk', 'App\Http\Controllers\Master\MerkController@index')->middleware(['auth'])->name('merk');
Route::post('/merk/store', 'App\Http\Controllers\Master\MerkController@store')->middleware(['auth'])->name('merk/store');

    Route::get('/get-merk', 'App\Http\Controllers\Master\MerkController@getMerk')->middleware(['auth'])->name('get-merk');
    Route::get('/merk/browse', 'App\Http\Controllers\Master\MerkController@browse')->middleware(['auth'])->name('merk/browse');

// Dynamic Merk
Route::get('/merk/edit', 'App\Http\Controllers\Master\MerkController@edit')->middleware(['auth'])->name('merk.edit');
Route::post('/merk/update/{merk}', 'App\Http\Controllers\Master\MerkController@update')->middleware(['auth'])->name('merk.update');
Route::get('/merk/delete/{merk}', 'App\Http\Controllers\Master\MerkController@destroy')->middleware(['auth'])->name('merk.delete');


//////////////////////


// Master Suplier 
Route::get('/sup', 'App\Http\Controllers\Master\SupController@index')->middleware(['auth'])->name('sup');
Route::post('/sup/store', 'App\Http\Controllers\Master\SupController@store')->middleware(['auth'])->name('sup/store');
Route::get('/rsup', 'App\Http\Controllers\OReport\RSupController@report')->middleware(['auth'])->name('rsup');
    // GET SUP
    Route::get('/get-sup', 'App\Http\Controllers\Master\SupController@getSup')->middleware(['auth'])->name('get-sup');
    Route::get('/sup/browse', 'App\Http\Controllers\Master\SupController@browse')->middleware(['auth'])->name('sup/browse');
    Route::get('/sup/browse_hari', 'App\Http\Controllers\Master\SupController@browse_hari')->middleware(['auth'])->name('sup/browse_hari');
    Route::get('/sup/browse_amplop', 'App\Http\Controllers\Master\SupController@browse_amplop')->middleware(['auth'])->name('sup/browse_amplop');
    Route::get('/sup/browsesupz', 'App\Http\Controllers\Master\SupController@browsesupz')->middleware(['auth'])->name('sup/browsesupz');
    
    Route::get('/get-sup-report', 'App\Http\Controllers\OReport\RSupController@getSupReport')->middleware(['auth'])->name('get-sup-report');
    Route::post('/jasper-sup-report', 'App\Http\Controllers\OReport\RSupController@jasperSupReport')->middleware(['auth'])->name('jasper-sup-report');
    Route::get('sup/ceksup', 'App\Http\Controllers\Master\SupController@ceksup')->middleware(['auth']);
	Route::get('sup/get-select-kodes', 'App\Http\Controllers\Master\SupController@getSelectKodes')->middleware(['auth']);
// Dynamic Suplier
Route::get('/sup/edit', 'App\Http\Controllers\Master\SupController@edit')->middleware(['auth'])->name('sup.edit');
Route::post('/sup/update/{sup}', 'App\Http\Controllers\Master\SupController@update')->middleware(['auth'])->name('sup.update');
Route::get('/sup/delete/{sup}', 'App\Http\Controllers\Master\SupController@destroy')->middleware(['auth'])->name('sup.delete');


//////////////////////

// Master Counter 
Route::get('/counter', 'App\Http\Controllers\Master\CounterController@index')->middleware(['auth'])->name('counter');
Route::post('/counter/store', 'App\Http\Controllers\Master\CounterController@store')->middleware(['auth'])->name('counter/store');
Route::get('/rcounter', 'App\Http\Controllers\OReport\RCounterController@report')->middleware(['auth'])->name('rcounter');
    // GET SUP
    Route::get('/get-counter', 'App\Http\Controllers\Master\CounterController@getCounter')->middleware(['auth'])->name('get-counter');
    Route::get('/counter/browse', 'App\Http\Controllers\Master\CounterController@browse')->middleware(['auth'])->name('counter/browse');
    Route::get('/counter/browse_th', 'App\Http\Controllers\Master\CounterController@browse_th')->middleware(['auth'])->name('counter/browse_th');
    Route::get('/counter/browse_hari', 'App\Http\Controllers\Master\CounterController@browse_hari')->middleware(['auth'])->name('counter/browse_hari');


    Route::get('/counter/browse', 'App\Http\Controllers\Master\CounterController@browse')->middleware(['auth'])->name('counter/browse');
    Route::get('/counter/browsecounterz', 'App\Http\Controllers\Master\CounterController@browsecounterz')->middleware(['auth'])->name('counter/browsecounterz');
  

    Route::get('/get-counter-report', 'App\Http\Controllers\OReport\RCounterController@getCounterReport')->middleware(['auth'])->name('get-counter-report');
    Route::post('/jasper-counter-report', 'App\Http\Controllers\OReport\RCounterController@jasperCounterReport')->middleware(['auth'])->name('jasper-counter-report');
    Route::get('counter/cekcounter', 'App\Http\Controllers\Master\CounterController@cekcounter')->middleware(['auth']);
	Route::get('counter/get-select-kodes', 'App\Http\Controllers\Master\CounterController@getSelectKodes')->middleware(['auth']);
// Dynamic Counter
Route::get('/counter/edit', 'App\Http\Controllers\Master\CounterController@edit')->middleware(['auth'])->name('counter.edit');
Route::post('/counter/update/{counter}', 'App\Http\Controllers\Master\CounterController@update')->middleware(['auth'])->name('counter.update');
Route::get('/counter/delete/{counter}', 'App\Http\Controllers\Master\CounterController@destroy')->middleware(['auth'])->name('counter.delete');



///////////////////////
// Master Brg 
Route::get('/brg', 'App\Http\Controllers\Master\BrgController@index')->middleware(['auth'])->name('brg');
Route::post('/brg/store', 'App\Http\Controllers\Master\BrgController@store')->middleware(['auth'])->name('brg/store');
Route::get('/rbrg', 'App\Http\Controllers\OReport\RBrgController@report')->middleware(['auth'])->name('rbrg');
    // GET brg
    Route::get('/get-brg', 'App\Http\Controllers\Master\BrgController@getBrg')->middleware(['auth'])->name('get-brg');
    Route::get('/brg/browse', 'App\Http\Controllers\Master\BrgController@browse')->middleware(['auth'])->name('brg/browse');
    Route::get('/brg/browsex', 'App\Http\Controllers\Master\BrgController@browsex')->middleware(['auth'])->name('brg/browsex');
    Route::get('/brg/browse_harga', 'App\Http\Controllers\Master\BrgController@browse_harga')->middleware(['auth'])->name('brg/browse_harga');
    Route::get('/brg/browse_beli', 'App\Http\Controllers\Master\BrgController@browse_beli')->middleware(['auth'])->name('brg/browse_beli');
    Route::get('/brg/browse_koreksi', 'App\Http\Controllers\Master\BrgController@browse_koreksi')->middleware(['auth'])->name('brg/browse_koreksi');
    Route::get('/brg/browse_sedia', 'App\Http\Controllers\Master\BrgController@browse_sedia')->middleware(['auth'])->name('brg/browse_sedia');

    Route::get('/brg/browsedz', 'App\Http\Controllers\Master\BrgController@browsedz')->middleware(['auth'])->name('brg/browsedz');
    Route::get('/get-brg-report', 'App\Http\Controllers\OReport\RBrgController@getBrgReport')->middleware(['auth'])->name('get-brg-report');
    Route::post('/jasper-brg-report', 'App\Http\Controllers\OReport\RBrgController@jasperBrgReport')->middleware(['auth'])->name('jasper-brg-report');
    Route::get('brg/cekbrg', 'App\Http\Controllers\Master\BrgController@cekbrg')->middleware(['auth']);
	Route::get('brg/get-select-kd_brg', 'App\Http\Controllers\Master\BrgController@getSelectKdbrg')->middleware(['auth']);

// Dynamic Brg

Route::get('/brg/edit', 'App\Http\Controllers\Master\BrgController@edit')->middleware(['auth'])->name('brg.edit');
Route::post('/brg/update/{brg}', 'App\Http\Controllers\Master\BrgController@update')->middleware(['auth'])->name('brg.update');
Route::get('/brg/delete/{brg}', 'App\Http\Controllers\Master\BrgController@destroy')->middleware(['auth'])->name('brg.delete');

// master cust

Route::get('/cust', 'App\Http\Controllers\Master\CustController@index')->middleware(['auth'])->name('cust');
Route::post('/cust/store', 'App\Http\Controllers\Master\CustController@store')->middleware(['auth'])->name('cust/store');
Route::get('/rcust', 'App\Http\Controllers\OReport\RCustController@report')->middleware(['auth'])->name('rcust');

    // GET cust
    Route::get('/get-cust', 'App\Http\Controllers\Master\CustController@getcust')->middleware(['auth'])->name('get-cust');
    Route::get('/cust/browse', 'App\Http\Controllers\Master\CustController@browse')->middleware(['auth'])->name('cust/browse');
    Route::get('/cust/browse_hari', 'App\Http\Controllers\Master\CustController@browse_hari')->middleware(['auth'])->name('cust/browse_hari');
    
    Route::get('/get-cust-report', 'App\Http\Controllers\OReport\RcustController@getcustReport')->middleware(['auth'])->name('get-cust-report');
    Route::post('/jasper-cust-report', 'App\Http\Controllers\OReport\RCustController@jaspercustReport')->middleware(['auth'])->name('jasper-cust-report');
    Route::get('cust/cekcust', 'App\Http\Controllers\Master\CustController@cekcust')->middleware(['auth']);
	Route::get('cust/get-select-kodec', 'App\Http\Controllers\Master\CustController@getSelectKodes')->middleware(['auth']);

// Dynamic cust
Route::get('/cust/edit', 'App\Http\Controllers\Master\CustController@edit')->middleware(['auth'])->name('cust.edit');
Route::post('/cust/update/{cust}', 'App\Http\Controllers\Master\CustController@update')->middleware(['auth'])->name('cust.update');
Route::get('/cust/delete/{cust}', 'App\Http\Controllers\Master\CustController@destroy')->middleware(['auth'])->name('cust.delete');

// Manage User
Route::get('/user/manage', 'App\Http\Controllers\UserController@index')->name('user/manage');
//Route::get('/user/add', 'App\Http\Controllers\UserController@create')->middleware(['auth', 'role:user|superadmin'])->name('user/add');
Route::get('/get-user', 'App\Http\Controllers\UserController@getUser')->middleware(['auth', 'role:user|superadmin'])->name('get-user');
Route::post('/user/add', 'App\Http\Controllers\UserController@store')->middleware(['auth', 'role:user|superadmin'])->name('user/add');


// Operational PO

Route::get('/po', 'App\Http\Controllers\OTransaksi\PoController@index')->middleware(['auth'])->name('po');
Route::post('/po/store', 'App\Http\Controllers\OTransaksi\PoController@store')->middleware(['auth'])->name('po/store');
Route::get('/rpo', 'App\Http\Controllers\OReport\RPoController@report')->middleware(['auth'])->name('rpo');
    // GET BELI
    Route::get('/po/browse', 'App\Http\Controllers\OTransaksi\PoController@browse')->middleware(['auth'])->name('po/browse');
    Route::get('/po/browse_brg', 'App\Http\Controllers\OTransaksi\PoController@browse_brg')->middleware(['auth'])->name('po/browse_brg');
    Route::get('/po/browse_sup', 'App\Http\Controllers\OTransaksi\PoController@browse_sup')->middleware(['auth'])->name('po/browse_sup');
    Route::get('/po/browse_detail', 'App\Http\Controllers\OTransaksi\PoController@browse_detail')->middleware(['auth'])->name('po/browse_detail');
    Route::get('/po/browse_detail2', 'App\Http\Controllers\OTransaksi\PoController@browse_detail2')->middleware(['auth'])->name('po/browse_detail2');
    Route::get('/po/browse_pod', 'App\Http\Controllers\OTransaksi\PoController@browse_pod')->middleware(['auth'])->name('po/browse_pod');
    Route::get('/po/browseuang', 'App\Http\Controllers\OTransaksi\PoController@browseuang')->middleware(['auth'])->name('po/browseuang');
   
    Route::get('/get-po', 'App\Http\Controllers\OTransaksi\PoController@getPo')->middleware(['auth'])->name('get-po');
	
    Route::get('/get-po-post', 'App\Http\Controllers\OTransaksi\PoController@getPo_posting')->middleware(['auth'])->name('get-po-post');
	
    Route::get('/get-po-report', 'App\Http\Controllers\OReport\RPoController@getPoReport')->middleware(['auth'])->name('get-po-report');
    // Route::get('/cetak/{po:NO_ID}', 'App\Http\Controllers\OTransaksi\PoController@cetak')->middleware(['auth']);
	Route::get('/po/cetak/{po:NO_ID}','App\Http\Controllers\OTransaksi\PoController@cetak')->middleware(['auth']);
    
    Route::post('jasper-po-report', 'App\Http\Controllers\OReport\RPoController@jasperPoReport')->middleware(['auth']);

    Route::get('/po/browse_pod', 'App\Http\Controllers\OTransaksi\PoController@browse_pod')->middleware(['auth'])->name('po/browse_pod');
	Route::get('/po/jtempo', 'App\Http\Controllers\OTransaksi\PoController@jtempo')->middleware(['auth'])->name('po/jtempo');
	
// Dynamic Po
Route::get('/po/edit', 'App\Http\Controllers\OTransaksi\PoController@edit')->middleware(['auth'])->name('po.edit');
Route::post('/po/update/{po}', 'App\Http\Controllers\OTransaksi\PoController@update')->middleware(['auth'])->name('po.update');
Route::get('/po/delete/{po}', 'App\Http\Controllers\OTransaksi\PoController@destroy')->middleware(['auth'])->name('po.delete');
Route::get('/po/repost/{po}', 'App\Http\Controllers\OTransaksi\PoController@repost')->middleware(['auth'])->name('po.repost');

Route::post('po/posting', 'App\Http\Controllers\OTransaksi\PoController@posting')->middleware(['auth']);
Route::get('po/index-posting', 'App\Http\Controllers\OTransaksi\PoController@index_posting')->middleware(['auth']);
Route::get('/get-detail-po', 'App\Http\Controllers\OTransaksi\PoController@getDetailPo')->middleware(['auth'])->name('get-detail-po');

// Posting
Route::get('/posting/index', 'App\Http\Controllers\PostingController@index')->middleware(['auth']);
Route::post('/posting/proses', 'App\Http\Controllers\PostingController@posting')->middleware(['auth']);



// Operational Transaksi Orderk
Route::get('/orderk', 'App\Http\Controllers\OTransaksi\OrderkController@index')->middleware(['auth'])->name('orderk');
Route::post('/orderk/store', 'App\Http\Controllers\OTransaksi\OrderkController@store')->middleware(['auth'])->name('orderk/store');
Route::get('/orderk/create', 'App\Http\Controllers\OTransaksi\OrderkController@create')->middleware(['auth'])->name('orderk/create');
Route::get('/get-orderk', 'App\Http\Controllers\OTransaksi\OrderkController@getOrderk')->middleware(['auth'])->name('get-orderk');
Route::get('/rorderk', 'App\Http\Controllers\OReport\ROrderkController@report')->middleware(['auth'])->name('rorderk');
Route::get('/get-orderk-report', 'App\Http\Controllers\OReport\ROderkController@getOrderkReport')->middleware(['auth'])->name('get-orderk-report');

Route::get('/orderk/show/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@show')->name('orderkid');
Route::get('/orderk/edit', 'App\Http\Controllers\OTransaksi\OrderkController@edit')->name('orderk.edit');
Route::post('/orderk/update/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@update')->name('orderk.update');
Route::get('/orderk/delete/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@destroy')->name('orderk.delete');

Route::post('/jasper-orderk-report', 'App\Http\Controllers\OReport\ROrderkController@jasperOrderkReport')->middleware(['auth']);
Route::get('/jsorderkc/{orderk:NO_ID}', 'App\Http\Controllers\OTransaksi\OrderkController@jsorderkc')->middleware(['auth']);


// Operational Transaksi Pakai
Route::get('/pakai', 'App\Http\Controllers\OTransaksi\PakaiController@index')->middleware(['auth'])->name('pakai');
Route::post('/pakai/store', 'App\Http\Controllers\OTransaksi\PakaiController@store')->middleware(['auth'])->name('pakai/store');
Route::get('/pakai/create', 'App\Http\Controllers\OTransaksi\PakaiController@create')->middleware(['auth'])->name('pakai/create');
Route::get('/get-pakai', 'App\Http\Controllers\OTransaksi\PakaiController@getPakai')->middleware(['auth'])->name('get-pakai');
Route::get('/rpakai', 'App\Http\Controllers\OReport\RPakaiController@report')->middleware(['auth'])->name('rpakai');
Route::get('/get-pakai-report', 'App\Http\Controllers\OReport\RPakaiController@getPakaiReport')->middleware(['auth'])->name('get-pakai-report');

Route::get('/pakai/show/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@show')->name('pakaiid');
Route::get('/pakai/edit', 'App\Http\Controllers\OTransaksi\PakaiController@edit')->name('pakai.edit');
Route::post('/pakai/update/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@update')->name('pakai.update');
Route::get('/pakai/delete/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@destroy')->name('pakai.delete');

Route::get('/pakai/browseOk', 'App\Http\Controllers\OTransaksi\PakaiController@browseOk')->middleware(['auth']);
Route::get('/pakai/browsePrs', 'App\Http\Controllers\OTransaksi\PakaiController@browsePrs')->middleware(['auth']);
Route::get('/pakai/browseBhn', 'App\Http\Controllers\OTransaksi\PakaiController@browseBhn')->middleware(['auth']);
Route::get('/pakai/browseXd', 'App\Http\Controllers\OTransaksi\PakaiController@browseXd')->middleware(['auth']);
Route::get('/pakai/cekOrderkWIP', 'App\Http\Controllers\OTransaksi\PakaiController@cekOrderkWIP')->middleware(['auth']);

Route::post('/jasper-pakai-report', 'App\Http\Controllers\OReport\RPakaiController@jasperPakaiReport')->middleware(['auth']);
Route::get('/jspakaic/{pakai:NO_ID}', 'App\Http\Controllers\OTransaksi\PakaiController@jspakaic')->middleware(['auth']);


// Operational Transaksi Kirim
Route::get('/kirim', 'App\Http\Controllers\OTransaksi\KirimController@index')->middleware(['auth'])->name('kirim');
Route::post('/kirim/store', 'App\Http\Controllers\OTransaksi\KirimController@store')->middleware(['auth'])->name('kirim/store');
Route::get('/kirim/create', 'App\Http\Controllers\OTransaksi\KirimController@create')->middleware(['auth'])->name('kirim/create');
Route::get('/get-kirim', 'App\Http\Controllers\OTransaksi\KirimController@getKirim')->middleware(['auth'])->name('get-kirim');
Route::get('/rkirim', 'App\Http\Controllers\OReport\RKirimController@report')->middleware(['auth'])->name('rkirim');
Route::get('/get-kirim-report', 'App\Http\Controllers\OReport\RKirimController@getKirimReport')->middleware(['auth'])->name('get-kirim-report');

Route::get('/kirim/show/{kirim}', 'App\Http\Controllers\OTransaksi\KirimController@show')->name('kirimid');
Route::get('/kirim/edit', 'App\Http\Controllers\OTransaksi\KirimController@edit')->name('kirim.edit');
Route::post('/kirim/update/{kirim}', 'App\Http\Controllers\OTransaksi\KirimController@update')->name('kirim.update');
Route::get('/kirim/delete/{kirim}', 'App\Http\Controllers\OTransaksi\KirimController@destroy')->name('kirim.delete');

Route::post('/jasper-kirim-report', 'App\Http\Controllers\OReport\RKirimController@jasperKirimReport')->middleware(['auth']);
Route::get('/jskirimc/{kirim:NO_ID}', 'App\Http\Controllers\OTransaksi\KirimController@jskirimc')->middleware(['auth']);


// Operational Terima
Route::get('/terima', 'App\Http\Controllers\OTransaksi\TerimaController@index')->middleware(['auth'])->name('terima');
Route::post('/terima/store', 'App\Http\Controllers\OTransaksi\TerimaController@store')->middleware(['auth'])->name('terima/store');
Route::get('/terima/create', 'App\Http\Controllers\OTransaksi\TerimaController@create')->middleware(['auth'])->name('terima/create');
Route::get('/get-terima', 'App\Http\Controllers\OTransaksi\TerimaController@getTerima')->middleware(['auth'])->name('get-terima');
Route::get('/rterima', 'App\Http\Controllers\OReport\RTerimaController@report')->middleware(['auth'])->name('rterima');
Route::get('/get-terima-report', 'App\Http\Controllers\OReport\RTerimaController@getTerimaReport')->middleware(['auth'])->name('get-terima-report');

Route::get('/terima/show/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@show')->name('terimaid');
Route::get('/terima/edit', 'App\Http\Controllers\OTransaksi\TerimaController@edit')->name('terima.edit');
Route::post('/terima/update/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@update')->name('terima.update');
Route::get('/terima/delete/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@destroy')->name('terima.delete');

Route::post('/jasper-terima-report', 'App\Http\Controllers\OReport\RTerimaController@jasperTerimaReport')->middleware(['auth']);
Route::get('/jsterimac/{terima:NO_ID}', 'App\Http\Controllers\OTransaksi\TerimaController@jsterimac')->middleware(['auth']);

// Operational Retur
Route::get('/retur', 'App\Http\Controllers\OTransaksi\ReturController@index')->middleware(['auth'])->name('retur');
Route::post('/retur/store', 'App\Http\Controllers\OTransaksi\ReturController@store')->middleware(['auth'])->name('retur/store');
Route::get('/retur/create', 'App\Http\Controllers\OTransaksi\ReturController@create')->middleware(['auth'])->name('retur/create');
Route::get('/get-retur', 'App\Http\Controllers\OTransaksi\ReturController@getRetur')->middleware(['auth'])->name('get-retur');
Route::get('/rretur', 'App\Http\Controllers\OReport\RReturController@report')->middleware(['auth'])->name('rretur');
Route::get('/get-retur-report', 'App\Http\Controllers\OReport\RReturController@getReturReport')->middleware(['auth'])->name('get-retur-report');

Route::get('/retur/show/{retur}', 'App\Http\Controllers\OTransaksi\ReturController@show')->name('returid');
Route::get('/retur/edit', 'App\Http\Controllers\OTransaksi\ReturController@edit')->name('retur.edit');
Route::post('/retur/update/{retur}', 'App\Http\Controllers\OTransaksi\ReturController@update')->name('retur.update');
Route::get('/retur/delete/{retur}', 'App\Http\Controllers\OTransaksi\ReturController@destroy')->name('retur.delete');

Route::post('/jasper-retur-report', 'App\Http\Controllers\OReport\RReturController@jasperReturReport')->middleware(['auth']);
Route::get('/jsreturc/{retur:NO_ID}', 'App\Http\Controllers\OTransaksi\ReturController@jsreturc')->middleware(['auth']);


// Operational Transaksi Diskon
Route::get('/diskon', 'App\Http\Controllers\OTransaksi\DiskonController@index')->middleware(['auth'])->name('diskon');
Route::post('/diskon/store', 'App\Http\Controllers\OTransaksi\DiskonController@store')->middleware(['auth'])->name('diskon/store');
Route::get('/diskon/create', 'App\Http\Controllers\OTransaksi\DiskonController@create')->middleware(['auth'])->name('diskon/create');
Route::get('/get-diskon', 'App\Http\Controllers\OTransaksi\DiskonController@getDiskon')->middleware(['auth'])->name('get-diskon');
Route::get('/rdiskon', 'App\Http\Controllers\OReport\RDiskonController@report')->middleware(['auth'])->name('rdiskon');
Route::get('/get-diskon-report', 'App\Http\Controllers\OReport\RDiskonController@getDiskonReport')->middleware(['auth'])->name('get-diskon-report');

Route::get('/diskon/show/{diskon}', 'App\Http\Controllers\OTransaksi\DiskonController@show')->name('diskonid');
Route::get('/diskon/edit', 'App\Http\Controllers\OTransaksi\DiskonController@edit')->name('diskon.edit');
Route::post('/diskon/update/{diskon}', 'App\Http\Controllers\OTransaksi\DiskonController@update')->name('diskon.update');
Route::get('/diskon/delete/{diskon}', 'App\Http\Controllers\OTransaksi\DiskonController@destroy')->name('diskon.delete');

Route::post('/jasper-diskon-report', 'App\Http\Controllers\OReport\RDiskonController@jasperDiskonReport')->middleware(['auth']);
Route::get('/jsdiskonc/{diskon:NO_ID}', 'App\Http\Controllers\OTransaksi\DiskonController@jsdiskonc')->middleware(['auth']);


// Operational Transaksi Tagi
Route::get('/tagi', 'App\Http\Controllers\OTransaksi\TagiController@index')->middleware(['auth'])->name('tagi');
Route::post('/tagi/store', 'App\Http\Controllers\OTransaksi\TagiController@store')->middleware(['auth'])->name('tagi/store');
Route::get('/tagi/create', 'App\Http\Controllers\OTransaksi\TagiController@create')->middleware(['auth'])->name('tagi/create');
Route::get('/get-tagi', 'App\Http\Controllers\OTransaksi\TagiController@getTagi')->middleware(['auth'])->name('get-tagi');
Route::get('/rtagi', 'App\Http\Controllers\OReport\RTagiController@report')->middleware(['auth'])->name('rtagi');
Route::get('/get-tagi-report', 'App\Http\Controllers\OReport\RTagiController@getTagiReport')->middleware(['auth'])->name('get-tagi-report');

Route::get('/tagi/show/{tagi}', 'App\Http\Controllers\OTransaksi\TagiController@show')->name('tagiid');
Route::get('/tagi/edit', 'App\Http\Controllers\OTransaksi\TagiController@edit')->name('tagi.edit');
Route::post('/tagi/update/{tagi}', 'App\Http\Controllers\OTransaksi\TagiController@update')->name('tagi.update');
Route::get('/tagi/delete/{tagi}', 'App\Http\Controllers\OTransaksi\TagiController@destroy')->name('tagi.delete');

Route::post('/jasper-tagi-report', 'App\Http\Controllers\OReport\RTagiController@jasperTagiReport')->middleware(['auth']);
Route::get('/jstagic/{tagi:NO_ID}', 'App\Http\Controllers\OTransaksi\TagiController@jstagic')->middleware(['auth']);

// Operational Transaksi Harga
Route::get('/harga', 'App\Http\Controllers\OTransaksi\HargaController@index')->middleware(['auth'])->name('harga');
Route::post('/harga/store', 'App\Http\Controllers\OTransaksi\HargaController@store')->middleware(['auth'])->name('harga/store');
Route::get('/harga/create', 'App\Http\Controllers\OTransaksi\HargaController@create')->middleware(['auth'])->name('harga/create');
Route::get('/get-harga', 'App\Http\Controllers\OTransaksi\HargaController@getHarga')->middleware(['auth'])->name('get-harga');
Route::get('/rharga', 'App\Http\Controllers\OReport\RHargaController@report')->middleware(['auth'])->name('rharga');
Route::get('/get-harga-report', 'App\Http\Controllers\OReport\RHargaController@getHargaReport')->middleware(['auth'])->name('get-harga-report');

Route::get('/harga/show/{harga}', 'App\Http\Controllers\OTransaksi\HargaController@show')->name('hargaid');
Route::get('/harga/edit', 'App\Http\Controllers\OTransaksi\HargaController@edit')->name('harga.edit');
Route::post('/harga/update/{harga}', 'App\Http\Controllers\OTransaksi\HargaController@update')->name('harga.update');
Route::get('/harga/delete/{harga}', 'App\Http\Controllers\OTransaksi\HargaController@destroy')->name('harga.delete');

Route::post('/jasper-harga-report', 'App\Http\Controllers\OReport\RHargaController@jasperHargaReport')->middleware(['auth']);
Route::get('/jshargac/{harga:NO_ID}', 'App\Http\Controllers\OTransaksi\HargaController@jshargac')->middleware(['auth']);

// Operational Usulan Turun Harga

Route::get('/rusulanth', 'App\Http\Controllers\OReport\RUsulanthController@report')->middleware(['auth'])->name('rusulanth');
Route::get('/get-usulanth-report', 'App\Http\Controllers\OReport\RUsulanthController@getUsulanthReport')->middleware(['auth'])->name('get-usulanth-report');
Route::post('jasper-usulanth-report', 'App\Http\Controllers\OReport\RUsulanthController@jasperUsulanthReport')->middleware(['auth']);

// Operational Lain
Route::get('/lain', 'App\Http\Controllers\OTransaksi\LainController@index')->middleware(['auth'])->name('lain');
Route::post('/lain/store', 'App\Http\Controllers\OTransaksi\LainController@store')->middleware(['auth'])->name('lain/store');
Route::get('/lain/create', 'App\Http\Controllers\OTransaksi\LainController@create')->middleware(['auth'])->name('lain/create');
Route::get('/get-lain', 'App\Http\Controllers\OTransaksi\LainController@getLain')->middleware(['auth'])->name('get-lain');
Route::get('/rlain', 'App\Http\Controllers\OReport\RLainController@report')->middleware(['auth'])->name('rlain');
Route::get('/get-lain-report', 'App\Http\Controllers\OReport\RLainController@getLainReport')->middleware(['auth'])->name('get-lain-report');

Route::get('/lain/show/{lain}', 'App\Http\Controllers\OTransaksi\LainController@show')->name('lainid');
Route::get('/lain/edit', 'App\Http\Controllers\OTransaksi\LainController@edit')->name('lain.edit');
Route::post('/lain/update/{lain}', 'App\Http\Controllers\OTransaksi\LainController@update')->name('lain.update');
Route::get('/lain/delete/{lain}', 'App\Http\Controllers\OTransaksi\LainController@destroy')->name('lain.delete');

Route::post('/jasper-lain-report', 'App\Http\Controllers\OReport\RLainController@jasperLainReport')->middleware(['auth']);
Route::get('/jslainc/{lain:NO_ID}', 'App\Http\Controllers\OTransaksi\LainController@jslainc')->middleware(['auth']);

// Operational Budget
Route::get('/budget', 'App\Http\Controllers\OTransaksi\BudgetController@index')->middleware(['auth'])->name('budget');
Route::post('/budget/store', 'App\Http\Controllers\OTransaksi\BudgetController@store')->middleware(['auth'])->name('budget/store');
Route::get('/budget/create', 'App\Http\Controllers\OTransaksi\BudgetController@create')->middleware(['auth'])->name('budget/create');
Route::get('/get-budget', 'App\Http\Controllers\OTransaksi\BudgetController@getBudget')->middleware(['auth'])->name('get-budget');
Route::get('/rbudget', 'App\Http\Controllers\OReport\RBudgetController@report')->middleware(['auth'])->name('rbudget');
Route::get('/get-budget-report', 'App\Http\Controllers\OReport\RBudgetController@getBudgetReport')->middleware(['auth'])->name('get-budget-report');

Route::get('/budget/show/{budget}', 'App\Http\Controllers\OTransaksi\BudgetController@show')->name('budgetid');
Route::get('/budget/edit', 'App\Http\Controllers\OTransaksi\BudgetController@edit')->name('budget.edit');
Route::post('/budget/update/{budget}', 'App\Http\Controllers\OTransaksi\BudgetController@update')->name('budget.update');
Route::get('/budget/delete/{budget}', 'App\Http\Controllers\OTransaksi\BudgetController@destroy')->name('budget.delete');

Route::post('/jasper-budget-report', 'App\Http\Controllers\OReport\RBudgetController@jasperBudgetReport')->middleware(['auth']);
Route::get('/jsbudgetc/{budget:NO_ID}', 'App\Http\Controllers\OTransaksi\BudgetController@jsbudgetc')->middleware(['auth']);



// Operational Counter
Route::get('/tcounter', 'App\Http\Controllers\OTransaksi\CounterController@index')->middleware(['auth'])->name('tcounter');
Route::post('/tcounter/store', 'App\Http\Controllers\OTransaksi\CounterController@store')->middleware(['auth'])->name('tcounter/store');
Route::get('/tcounter/create', 'App\Http\Controllers\OTransaksi\CounterController@create')->middleware(['auth'])->name('tcounter/create');
Route::get('/get-tcounter', 'App\Http\Controllers\OTransaksi\CounterController@getCounter')->middleware(['auth'])->name('get-tcounter');
Route::get('/rtcounter', 'App\Http\Controllers\OReport\RCounterController@report')->middleware(['auth'])->name('rtcounter');
Route::get('/get-tcounter-report', 'App\Http\Controllers\OReport\RCounterController@getCounterReport')->middleware(['auth'])->name('get-tcounter-report');

Route::get('/tcounter/show/{tcounter}', 'App\Http\Controllers\OTransaksi\CounterController@show')->name('tcounterid');
Route::get('/tcounter/edit', 'App\Http\Controllers\OTransaksi\CounterController@edit')->name('tcounter.edit');
Route::post('/tcounter/update/{tcounter}', 'App\Http\Controllers\OTransaksi\CounterController@update')->name('tcounter.update');
Route::get('/tcounter/delete/{tcounter}', 'App\Http\Controllers\OTransaksi\CounterController@destroy')->name('tcounter.delete');

Route::post('/jasper-tcounter-report', 'App\Http\Controllers\OReport\RCounterController@jasperCounterReport')->middleware(['auth']);
Route::get('/jstcounterc/{tcounter:NO_ID}', 'App\Http\Controllers\OTransaksi\CounterController@jstcounterc')->middleware(['auth']);


// Operational Buat Faktur Pajak

Route::get('/rfakturpj', 'App\Http\Controllers\OReport\RFakturpjController@report')->middleware(['auth'])->name('rfakturpj');
Route::get('/get-fakturpj-report', 'App\Http\Controllers\OReport\RFakturpjController@getFakturpjReport')->middleware(['auth'])->name('get-fakturpj-report');
Route::post('jasper-fakturpj-report', 'App\Http\Controllers\OReport\RFakturpjController@jasperFakturpjReport')->middleware(['auth']);

// Posting
Route::get('/posting/index', 'App\Http\Controllers\PostingController@index')->middleware(['auth']);
Route::post('/posting/proses', 'App\Http\Controllers\PostingController@posting')->middleware(['auth']);



// Operational Stockb

Route::get('/stockb', 'App\Http\Controllers\OTransaksi\StockbController@index')->middleware(['auth'])->name('stockb');
Route::post('/stockb/store', 'App\Http\Controllers\OTransaksi\StockbController@store')->middleware(['auth'])->name('stockb/store');
Route::get('/rstockb', 'App\Http\Controllers\OReport\RStockbController@report')->middleware(['auth'])->name('rstockb');
    
// GET Stockb
    Route::get('/stockb/browse', 'App\Http\Controllers\OTransaksi\StockbController@browse')->middleware(['auth'])->name('stockb/browse');
    Route::get('/stockb/browse_detail', 'App\Http\Controllers\OTransaksi\StockbController@browse_detail')->middleware(['auth'])->name('stockb/browse_detail');
    Route::get('/stockb/browseuang', 'App\Http\Controllers\OTransaksi\StockbController@browseuang')->middleware(['auth'])->name('stockb/browseuang');
   
    Route::get('/get-stockb', 'App\Http\Controllers\OTransaksi\StockbController@getStockb')->middleware(['auth'])->name('get-stockb');
	
    Route::get('/get-stockb-post', 'App\Http\Controllers\OTransaksi\StockbController@getStockb_posting')->middleware(['auth'])->name('get-stockb-post');
	
    Route::get('/get-stockb-report', 'App\Http\Controllers\OReport\RStockbController@getStockbReport')->middleware(['auth'])->name('get-stockb-report');
    Route::get('/jspoc/{stockb:NO_ID}', 'App\Http\Controllers\OTransaksi\StockbController@jspoc')->middleware(['auth']);
    Route::post('jasper-stockb-report', 'App\Http\Controllers\OReport\RStockbController@jasperStockbReport')->middleware(['auth']);

    Route::get('/stockb/browse_pod', 'App\Http\Controllers\OTransaksi\StockbController@browse_stockbd')->middleware(['auth'])->name('stockb/browse_stockbd');
	
// Dynamic Stockb
Route::get('/stockb/edit', 'App\Http\Controllers\OTransaksi\StockbController@edit')->middleware(['auth'])->name('stockb.edit');
Route::post('/stockb/update/{stockb}', 'App\Http\Controllers\OTransaksi\StockbController@update')->middleware(['auth'])->name('stockb.update');
Route::get('/stockb/delete/{stockb}', 'App\Http\Controllers\OTransaksi\StockbController@destroy')->middleware(['auth'])->name('stockb.delete');
Route::get('/stockb/repost/{stockb}', 'App\Http\Controllers\OTransaksi\StockbController@repost')->middleware(['auth'])->name('stockb.repost');

Route::post('stockb/posting', 'App\Http\Controllers\OTransaksi\StockbController@posting')->middleware(['auth']);
Route::get('stockb/index-posting', 'App\Http\Controllers\OTransaksi\StockbController@index_posting')->middleware(['auth']);

// Posting
Route::get('/posting/index', 'App\Http\Controllers\PostingController@index')->middleware(['auth']);
Route::post('/posting/proses', 'App\Http\Controllers\PostingController@posting')->middleware(['auth']);



// Operational Surat
Route::get('/surats', 'App\Http\Controllers\OTransaksi\SuratsController@index')->middleware(['auth'])->name('surats');
Route::post('/surats/store', 'App\Http\Controllers\OTransaksi\SuratsController@store')->middleware(['auth'])->name('surats/store');
Route::get('/surats/create', 'App\Http\Controllers\OTransaksi\SuratsController@create')->middleware(['auth'])->name('surats/create');
Route::get('/get-surats', 'App\Http\Controllers\OTransaksi\SuratsController@getSurats')->middleware(['auth'])->name('get-surats');
Route::get('/rsurats', 'App\Http\Controllers\OReport\RSuratsController@report')->middleware(['auth'])->name('rsurats');
Route::get('/surats/browse', 'App\Http\Controllers\OTransaksi\SuratsController@browse')->middleware(['auth'])->name('surats/browse');

Route::get('/surats/show/{surats}', 'App\Http\Controllers\OTransaksi\SuratsController@show')->name('suratsid');
Route::get('/surats/edit', 'App\Http\Controllers\OTransaksi\SuratsController@edit')->name('surats.edit');
Route::post('/surats/update/{surats}', 'App\Http\Controllers\OTransaksi\SuratsController@update')->name('surats.update');
Route::get('/surats/delete/{surats}', 'App\Http\Controllers\OTransaksi\SuratsController@destroy')->name('surats.delete');

Route::post('surats/batal_post', 'App\Http\Controllers\OTransaksi\SuratsController@batal_post')->middleware(['auth']);

Route::get('/surats/browseCust', 'App\Http\Controllers\OTransaksi\SuratsController@browseCust')->middleware(['auth']);
Route::get('/surats/browseSo', 'App\Http\Controllers\OTransaksi\SuratsController@browseSo')->middleware(['auth']);
Route::get('/surats/browseDo', 'App\Http\Controllers\OTransaksi\SuratsController@browseDo')->middleware(['auth']);
Route::get('/surats/browseDo_Cust', 'App\Http\Controllers\OTransaksi\SuratsController@browseDo_Cust')->middleware(['auth']);
Route::get('/surats/browse_detail', 'App\Http\Controllers\OTransaksi\SuratsController@browse_detail')->middleware(['auth']);
Route::get('/surats/do_detail', 'App\Http\Controllers\OTransaksi\SuratsController@do_detail')->middleware(['auth']);
Route::get('/surats/browse_suratsd', 'App\Http\Controllers\OTransaksi\SuratsController@browse_suratsd')->middleware(['auth']);

Route::post('/jasper-surats-report', 'App\Http\Controllers\OReport\RSuratsController@jasperSuratsReport')->middleware(['auth']);
Route::get('/jssuratsc/{surats:NO_ID}', 'App\Http\Controllers\OTransaksi\SuratsController@jssuratsc')->middleware(['auth']);

Route::get('/get-surats-report', 'App\Http\Controllers\OReport\RSuratsController@getSuratsReport')->middleware(['auth'])->name('get-surats-report');
Route::get('/jssuratsc/{surats:NO_ID}', 'App\Http\Controllers\OTransaksi\SuratsController@jssuratsc')->middleware(['auth']);
Route::post('jasper-surats-report', 'App\Http\Controllers\OReport\RSuratsController@jasperSuratsReport')->middleware(['auth']);
Route::get('/surats/cetak/{surats:NO_ID}','App\Http\Controllers\OTransaksi\SuratsController@cetak')->middleware(['auth']);
Route::get('/surats/cetak_kirim/{surats:NO_ID}','App\Http\Controllers\OTransaksi\SuratsController@cetak_kirim')->middleware(['auth']);



// Operational Do
Route::get('/deli', 'App\Http\Controllers\OTransaksi\DeliController@index')->middleware(['auth'])->name('deli');
Route::post('/deli/store', 'App\Http\Controllers\OTransaksi\DeliController@store')->middleware(['auth'])->name('deli/store');
Route::get('/deli/create', 'App\Http\Controllers\OTransaksi\DeliController@create')->middleware(['auth'])->name('deli/create');
Route::get('/get-deli', 'App\Http\Controllers\OTransaksi\DeliController@getDeli')->middleware(['auth'])->name('get-deli');
Route::get('/rdeli', 'App\Http\Controllers\OReport\RDeliController@report')->middleware(['auth'])->name('rdeli');
Route::get('/deli/browse', 'App\Http\Controllers\OTransaksi\DeliController@browse')->middleware(['auth'])->name('deli/browse');

Route::get('/deli/show/{deli}', 'App\Http\Controllers\OTransaksi\DeliController@show')->name('deliid');
Route::get('/deli/edit', 'App\Http\Controllers\OTransaksi\DeliController@edit')->name('deli.edit');
Route::post('/deli/update/{deli}', 'App\Http\Controllers\OTransaksi\DeliController@update')->name('deli.update');
Route::get('/deli/delete/{deli}', 'App\Http\Controllers\OTransaksi\DeliController@destroy')->name('deli.delete');

Route::get('/deli/browseCust', 'App\Http\Controllers\OTransaksi\DeliController@browseCust')->middleware(['auth']);
Route::get('/deli/browseSo', 'App\Http\Controllers\OTransaksi\DeliController@browseSo')->middleware(['auth']);
Route::get('/deli/browseDo', 'App\Http\Controllers\OTransaksi\DeliController@browseDo')->middleware(['auth']);
Route::get('/deli/browse_detail', 'App\Http\Controllers\OTransaksi\DeliController@browse_detail')->middleware(['auth']);
Route::get('/deli/do_detail', 'App\Http\Controllers\OTransaksi\DeliController@do_detail')->middleware(['auth']);
Route::get('/deli/browse_delid', 'App\Http\Controllers\OTransaksi\DeliController@browse_delid')->middleware(['auth']);

Route::post('/jasper-deli-report', 'App\Http\Controllers\OReport\RDeliController@jasperDeliReport')->middleware(['auth']);
Route::get('/jsdelic/{deli:NO_ID}', 'App\Http\Controllers\OTransaksi\DeliController@jsdelic')->middleware(['auth']);

Route::get('/get-deli-report', 'App\Http\Controllers\OReport\RDeliController@getDeliReport')->middleware(['auth'])->name('get-deli-report');
Route::get('/jsdelic/{deli:NO_ID}', 'App\Http\Controllers\OTransaksi\DeliController@jsdelic')->middleware(['auth']);
Route::post('jasper-deli-report', 'App\Http\Controllers\OReport\RDeliController@jasperDeliReport')->middleware(['auth']);
Route::get('/deli/cetak/{deli:NO_ID}','App\Http\Controllers\OTransaksi\DeliController@cetak')->middleware(['auth']);
Route::get('/deli/cetak2/{deli:NO_ID}','App\Http\Controllers\OTransaksi\DeliController@cetak2')->middleware(['auth']);
Route::get('/deli/cetak3/{deli:NO_ID}','App\Http\Controllers\OTransaksi\DeliController@cetak3')->middleware(['auth']);


// Operational Penjualan
Route::get('/jual', 'App\Http\Controllers\OTransaksi\JualController@index')->middleware(['auth'])->name('jual');
Route::post('/jual/store', 'App\Http\Controllers\OTransaksi\JualController@store')->middleware(['auth'])->name('jual/store');
Route::get('/jual/create', 'App\Http\Controllers\OTransaksi\JualController@create')->middleware(['auth'])->name('jual/create');
Route::get('/get-jual', 'App\Http\Controllers\OTransaksi\JualController@getJual')->middleware(['auth'])->name('get-jual');
Route::get('/rjual', 'App\Http\Controllers\OReport\RJualController@report')->middleware(['auth'])->name('rjual');
Route::get('/get-jual-report', 'App\Http\Controllers\OReport\RJualController@getJualReport')->middleware(['auth'])->name('get-jual-report');

Route::get('/jual/show/{jual}', 'App\Http\Controllers\OTransaksi\JualController@show')->name('jualid');
//Route::get('/jual/edit/{jual}', 'App\Http\Controllers\OTransaksi\JualController@edit')->name('jual.edit');
Route::post('/jual/update/{jual}', 'App\Http\Controllers\OTransaksi\JualController@update')->name('jual.update');
Route::get('/jual/delete/{jual}', 'App\Http\Controllers\OTransaksi\JualController@destroy')->name('jual.delete');

Route::get('/jual/browseSj', 'App\Http\Controllers\OTransaksi\JualController@browseSj')->middleware(['auth']);
Route::get('/jual/browseSuratsd', 'App\Http\Controllers\OTransaksi\JualController@browseSuratsd')->middleware(['auth']);
Route::get('/jual/browseSo', 'App\Http\Controllers\OTransaksi\JualController@browseSo')->middleware(['auth']);
Route::get('/jual/browse', 'App\Http\Controllers\OTransaksi\JualController@browse')->middleware(['auth'])->name('jual/browse');
Route::get('/jual/browse_juald', 'App\Http\Controllers\OTransaksi\JualController@browse_juald')->middleware(['auth'])->name('jual/browse_juald');

Route::post('/jasper-jual-report', 'App\Http\Controllers\OReport\RJualController@jasperJualReport')->middleware(['auth']);
Route::get('/jsjualc/{jual:NO_ID}', 'App\Http\Controllers\OTransaksi\JualController@jsjualc')->middleware(['auth']);

// Report Tagihan

Route::get('/rtagih', 'App\Http\Controllers\OReport\RTagihController@report')->middleware(['auth'])->name('rtagih');
Route::get('/get-tagih-report', 'App\Http\Controllers\OReport\RTagihController@getTagihReport')->middleware(['auth'])->name('get-tagih-report');
Route::post('jasper-tagih-report', 'App\Http\Controllers\OReport\RTagihController@jasperTagihReport')->middleware(['auth']);

// Report Komisi

Route::get('/rkomisi', 'App\Http\Controllers\OReport\RKomisiController@report')->middleware(['auth'])->name('rkomisi');
Route::get('/get-komisi-report', 'App\Http\Controllers\OReport\RKomisiController@getKomisiReport')->middleware(['auth'])->name('get-komisi-report');
Route::post('jasper-komisi-report', 'App\Http\Controllers\OReport\RKomisiController@jasperKomisiReport')->middleware(['auth']);

// Report Pemantauan Barang Busana Turun Harga

Route::get('/rpantau', 'App\Http\Controllers\OReport\RPantaubsnController@report')->middleware(['auth'])->name('rpantau');
Route::get('/get-pantau-report', 'App\Http\Controllers\OReport\RPantaubsnController@getPantaubsnReport')->middleware(['auth'])->name('get-pantau-report');
Route::post('jasper-pantau-report', 'App\Http\Controllers\OReport\RPantaubsnController@jasperPantaubsnReport')->middleware(['auth']);

// Report Penjualan
Route::get('/rpenjualan', 'App\Http\Controllers\OReport\RPenjualanController@report')->middleware(['auth'])->name('rpenjualan');
Route::get('/get-penjualan-report', 'App\Http\Controllers\OReport\RPenjualanController@getPenjualanReport')->middleware(['auth'])->name('get-penjualan-report');
Route::post('/jasper-penjualandetail-report', 'App\Http\Controllers\OReport\RPenjualanController@jasperPenjualanDetailReport')->middleware(['auth'])->name('jasper-penjualandetail-report');
Route::post('/jasper-penjualansummary-report', 'App\Http\Controllers\OReport\RPenjualanController@jasperPenjualanSummaryReport')->middleware(['auth'])->name('jasper-penjualansummary-report');
Route::post('/jasper-penjualan-report', 'App\Http\Controllers\OReport\RPenjualanController@jasperPenjualanReport')->middleware(['auth'])->name('jasper-penjualan-report');
Route::get('/get-penjualan-report-ajax', 'App\Http\Controllers\OReport\RPenjualanController@getPenjualanReportAjax')->name('get-penjualan-report-ajax');
      
    
// Operational Beli
Route::get('/beli', 'App\Http\Controllers\OTransaksi\BeliController@index')->middleware(['auth'])->name('beli');
Route::post('/beli/store', 'App\Http\Controllers\OTransaksi\BeliController@store')->middleware(['auth'])->name('beli/store');
Route::get('/rbeli', 'App\Http\Controllers\OReport\RBeliController@report')->middleware(['auth'])->name('rbeli');
Route::get('/rbeli_gdg', 'App\Http\Controllers\OReport\RBeli_gdgController@report')->middleware(['auth'])->name('rbeli_gdg');
    // GET BELI
    Route::get('/beli/browse', 'App\Http\Controllers\OTransaksi\BeliController@browse')->middleware(['auth'])->name('beli/browse');
    Route::get('/beli/browse_detail', 'App\Http\Controllers\OTransaksi\BeliController@browse_detail')->middleware(['auth'])->name('beli/browse_detail');
    Route::get('/beli/browse_detail2', 'App\Http\Controllers\OTransaksi\BeliController@browse_detail2')->middleware(['auth'])->name('beli/browse_detail2');
   
    Route::get('/beli/browseuang', 'App\Http\Controllers\OTransaksi\BeliController@browseuang')->middleware(['auth'])->name('beli/browseuang');
    Route::get('/get-beli', 'App\Http\Controllers\OTransaksi\BeliController@getBeli')->middleware(['auth'])->name('get-beli');
	
    Route::get('/get-beli-post', 'App\Http\Controllers\OTransaksi\BeliController@getBeli_posting')->middleware(['auth'])->name('get-beli-post');
	
    Route::get('/get-beli-report', 'App\Http\Controllers\OReport\RBeliController@getBeliReport')->middleware(['auth'])->name('get-beli-report');
    Route::get('/get-beli_gdg-report', 'App\Http\Controllers\OReport\RBeli_gdgController@getBeli_gdgReport')->middleware(['auth'])->name('get-beli_gdg-report');
	Route::get('/beli/cetak/{beli:NO_ID}','App\Http\Controllers\OTransaksi\BeliController@cetak')->middleware(['auth']);
	Route::get('/beli/cetak2/{beli:NO_ID}','App\Http\Controllers\OTransaksi\BeliController@cetak2')->middleware(['auth']);
    Route::post('jasper-beli-report', 'App\Http\Controllers\OReport\RBeliController@jasperBeliReport')->middleware(['auth']);
    Route::post('jasper-beli_gdg-report', 'App\Http\Controllers\OReport\RBeli_gdgController@jasperBeli_gdgReport')->middleware(['auth']);

    Route::get('/beli/browse_belid', 'App\Http\Controllers\OTransaksi\BeliController@browse_belid')->middleware(['auth'])->name('beli/browse_belid');
	
// Dynamic Beli
Route::get('/beli/edit', 'App\Http\Controllers\OTransaksi\BeliController@edit')->middleware(['auth'])->name('beli.edit');
Route::post('/beli/update/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@update')->middleware(['auth'])->name('beli.update');
Route::get('/beli/delete/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@destroy')->middleware(['auth'])->name('beli.delete');
Route::get('/beli/repost/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@repost')->middleware(['auth'])->name('beli.repost');

Route::post('beli/posting', 'App\Http\Controllers\OTransaksi\BeliController@posting')->middleware(['auth']);
Route::post('beli/batal_post', 'App\Http\Controllers\OTransaksi\BeliController@batal_post')->middleware(['auth']);
Route::get('beli/index-posting', 'App\Http\Controllers\OTransaksi\BeliController@index_posting')->middleware(['auth']);
Route::get('/get-detail-beli', 'App\Http\Controllers\OTransaksi\BeliController@getDetailBeli')->middleware(['auth'])->name('get-detail-beli');
Route::get('/get-detail-hut', 'App\Http\Controllers\OTransaksi\HutController@getDetailHut')->middleware(['auth'])->name('get-detail-hut');
Route::get('/get-detail-so', 'App\Http\Controllers\OTransaksi\SoController@getDetailSo')->middleware(['auth'])->name('get-detail-so');
Route::get('/get-detail-deli', 'App\Http\Controllers\OTransaksi\DeliController@getDetailDeli')->middleware(['auth'])->name('get-detail-deli');
Route::get('/get-detail-surats', 'App\Http\Controllers\OTransaksi\SuratsController@getDetailSurats')->middleware(['auth'])->name('get-detail-surats');
Route::get('/get-detail-jual', 'App\Http\Controllers\OTransaksi\JualController@getDetailJual')->middleware(['auth'])->name('get-detail-jual');
Route::get('/get-detail-piu', 'App\Http\Controllers\OTransaksi\PiuController@getDetailPiu')->middleware(['auth'])->name('get-detail-piu');
Route::get('/get-detail-stockb', 'App\Http\Controllers\OTransaksi\StockbController@getDetailStockb')->middleware(['auth'])->name('get-detail-stockb');
Route::get('/get-detail-kirim', 'App\Http\Controllers\OTransaksi\KirimController@getDetailKirim')->middleware(['auth'])->name('get-detail-kirim');
Route::get('/get-detail-retur', 'App\Http\Controllers\OTransaksi\ReturController@getDetailRetur')->middleware(['auth'])->name('get-detail-retur');
Route::get('/get-detail-terima', 'App\Http\Controllers\OTransaksi\TerimaController@getDetailTerima')->middleware(['auth'])->name('get-detail-terima');
Route::get('/get-detail-diskon', 'App\Http\Controllers\OTransaksi\DiskonController@getDetailDiskon')->middleware(['auth'])->name('get-detail-diskon');
Route::get('/get-detail-tagi', 'App\Http\Controllers\OTransaksi\TagiController@getDetailTagi')->middleware(['auth'])->name('get-detail-tagi');
Route::get('/get-detail-lain', 'App\Http\Controllers\OTransaksi\LainController@getDetailLain')->middleware(['auth'])->name('get-detail-lain');
Route::get('/get-detail-budget', 'App\Http\Controllers\OTransaksi\BudgetController@getDetailBudget')->middleware(['auth'])->name('get-detail-budget');
Route::get('/get-detail-harga', 'App\Http\Controllers\OTransaksi\HargaController@getDetailHarga')->middleware(['auth'])->name('get-detail-harga');


// Route::post('/batal_post', 'App\Http\Controllers\BeliController::class', 'batalPost')->name('beli.batal_post');

////////////////////////////////////////////////////////

// Operational PP
Route::get('/pp', 'App\Http\Controllers\OTransaksi\PpController@index')->middleware(['auth'])->name('pp');
Route::post('/pp/store', 'App\Http\Controllers\OTransaksi\PpController@store')->middleware(['auth'])->name('pp/store');
Route::get('/rpp', 'App\Http\Controllers\OReport\RPpController@report')->middleware(['auth'])->name('rpp');
    
// GET PP
    Route::get('/pp/browse', 'App\Http\Controllers\OTransaksi\PpController@browse')->middleware(['auth'])->name('pp/browse');
    Route::get('/pp/browse_detail', 'App\Http\Controllers\OTransaksi\PpController@browse_detail')->middleware(['auth'])->name('pp/browse_detail');
    Route::get('/pp/browse_detail2', 'App\Http\Controllers\OTransaksi\PpController@browse_detail2')->middleware(['auth'])->name('pp/browse_detail2');
   
    Route::get('/pp/browseuang', 'App\Http\Controllers\OTransaksi\PpController@browseuang')->middleware(['auth'])->name('pp/browseuang');
    Route::get('/get-pp', 'App\Http\Controllers\OTransaksi\PpController@getPp')->middleware(['auth'])->name('get-pp');
	
    Route::get('/get-pp-post', 'App\Http\Controllers\OTransaksi\PpController@getPp_posting')->middleware(['auth'])->name('get-pp-post');
	
    Route::get('/get-pp-report', 'App\Http\Controllers\OReport\RPpController@getPpReport')->middleware(['auth'])->name('get-pp-report');
	Route::get('/pp/cetak/{pp:NO_ID}','App\Http\Controllers\OTransaksi\PpController@cetak')->middleware(['auth']);
    Route::post('jasper-pp-report', 'App\Http\Controllers\OReport\RPpController@jasperPpReport')->middleware(['auth']);

    Route::get('/pp/browse_ppd', 'App\Http\Controllers\OTransaksi\PpController@browse_ppd')->middleware(['auth'])->name('pp/browse_ppd');
	
// Dynamic PP
Route::get('/pp/edit', 'App\Http\Controllers\OTransaksi\PpController@edit')->middleware(['auth'])->name('pp.edit');
Route::post('/pp/update/{pp}', 'App\Http\Controllers\OTransaksi\PpController@update')->middleware(['auth'])->name('pp.update');
Route::get('/pp/delete/{pp}', 'App\Http\Controllers\OTransaksi\PpController@destroy')->middleware(['auth'])->name('pp.delete');
Route::get('/pp/repost/{pp}', 'App\Http\Controllers\OTransaksi\PpController@repost')->middleware(['auth'])->name('pp.repost');

Route::post('pp/posting', 'App\Http\Controllers\OTransaksi\PpController@posting')->middleware(['auth']);
Route::get('pp/index-posting', 'App\Http\Controllers\OTransaksi\PpController@index_posting')->middleware(['auth']);

////////////////////////////////////////////////////////

/////////////////////////////////////////////////////



// Operational Utbeli
Route::get('/utbeli', 'App\Http\Controllers\OTransaksi\UtbeliController@index')->middleware(['auth'])->name('utbeli');
Route::post('/utbeli/store', 'App\Http\Controllers\OTransaksi\UtbeliController@store')->middleware(['auth'])->name('utbeli/store');
Route::get('/rutbeli', 'App\Http\Controllers\OReport\RUtbeliController@report')->middleware(['auth'])->name('rutbeli');
    // GET Utbeli
    Route::get('/utbeli/browse', 'App\Http\Controllers\OTransaksi\UtbeliController@browse')->middleware(['auth'])->name('utbeli/browse');
    Route::get('/utbeli/browseuang', 'App\Http\Controllers\OTransaksi\UtbeliController@browseuang')->middleware(['auth'])->name('utbeli/browseuang');
    Route::get('/get-utbeli', 'App\Http\Controllers\OTransaksi\UtbeliController@getUtbeli')->middleware(['auth'])->name('get-utbeli');
    Route::get('/get-utbeli-report', 'App\Http\Controllers\OReport\RUtbeliController@getUtbeliReport')->middleware(['auth'])->name('get-utbeli-report');
    Route::post('jasper-utbeli-report', 'App\Http\Controllers\OReport\RUtbeliController@jasperUtbeliReport')->middleware(['auth']);
// Dynamic Utbeli
Route::get('/utbeli/edit', 'App\Http\Controllers\OTransaksi\UtbeliController@edit')->middleware(['auth'])->name('utbeli.edit');
Route::post('/utbeli/update/{utbeli}', 'App\Http\Controllers\OTransaksi\UtbeliController@update')->middleware(['auth'])->name('utbeli.update');
Route::get('/utbeli/delete/{utbeli}', 'App\Http\Controllers\OTransaksi\UtbeliController@destroy')->middleware(['auth'])->name('utbeli.delete');
Route::get('/utbeli/repost/{utbeli}', 'App\Http\Controllers\OTransaksi\UtbeliController@repost')->middleware(['auth'])->name('utbeli.repost');
Route::get('/jsutbelic/{utbeli:NO_ID}', 'App\Http\Controllers\OTransaksi\UtbeliController@jsutbelic')->middleware(['auth']);
    

Route::get('/rum', 'App\Http\Controllers\OReport\RUmController@report')->middleware(['auth'])->name('rum');
Route::post('jasper-um-report', 'App\Http\Controllers\OReport\RUmController@jasperUmReport')->middleware(['auth']);



// Operational Jual

Route::get('/jual', 'App\Http\Controllers\OTransaksi\JualController@index')->middleware(['auth'])->name('jual');
Route::post('/jual/store', 'App\Http\Controllers\OTransaksi\JualController@store')->middleware(['auth'])->name('jual/store');
Route::get('/rjual', 'App\Http\Controllers\OReport\RJualController@report')->middleware(['auth'])->name('rjual');
    // GET BELI
    Route::get('/jual/browse', 'App\Http\Controllers\OTransaksi\JualController@browse')->middleware(['auth'])->name('jual/browse');
    Route::get('/jual/browseuang', 'App\Http\Controllers\OTransaksi\JualController@browseuang')->middleware(['auth'])->name('jual/browseuang');
    Route::get('/get-jual', 'App\Http\Controllers\OTransaksi\JualController@getJual')->middleware(['auth'])->name('get-jual');
	
    Route::get('/get-jual-post', 'App\Http\Controllers\OTransaksi\JualController@getJual_posting')->middleware(['auth'])->name('get-jual-post');
	
    Route::get('/get-jual-report', 'App\Http\Controllers\OReport\RJualController@getJualReport')->middleware(['auth'])->name('get-jual-report');
    Route::get('/jsjualc/{jual:NO_ID}', 'App\Http\Controllers\OTransaksi\JualController@jsjualc')->middleware(['auth']);
    Route::post('jasper-jual-report', 'App\Http\Controllers\OReport\RJualController@jasperJualReport')->middleware(['auth']);

    Route::get('/jual/browse_juald', 'App\Http\Controllers\OTransaksi\JualController@browse_juald')->middleware(['auth'])->name('jual/browse_juald');
	
// Dynamic Jual
Route::get('/jual/edit', 'App\Http\Controllers\OTransaksi\JualController@edit')->middleware(['auth'])->name('jual.edit');
Route::post('/jual/update/{jual}', 'App\Http\Controllers\OTransaksi\JualController@update')->middleware(['auth'])->name('jual.update');
Route::get('/jual/delete/{jual}', 'App\Http\Controllers\OTransaksi\JualController@destroy')->middleware(['auth'])->name('jual.delete');
Route::get('/jual/repost/{jual}', 'App\Http\Controllers\OTransaksi\JualController@repost')->middleware(['auth'])->name('jual.repost');

Route::post('jual/posting', 'App\Http\Controllers\OTransaksi\JualController@posting')->middleware(['auth']);
Route::get('jual/index-posting', 'App\Http\Controllers\OTransaksi\JualController@index_posting')->middleware(['auth']);


//////////////////////////////////////////////////////////


// Operational Utjual
Route::get('/utjual', 'App\Http\Controllers\OTransaksi\UtjualController@index')->middleware(['auth'])->name('utjual');
Route::post('/utjual/store', 'App\Http\Controllers\OTransaksi\UtjualController@store')->middleware(['auth'])->name('utjual/store');
Route::get('/rutjual', 'App\Http\Controllers\OReport\RUtjualController@report')->middleware(['auth'])->name('rutjual');
    // GET Utjual
    Route::get('/utjual/browse', 'App\Http\Controllers\OTransaksi\UtjualController@browse')->middleware(['auth'])->name('utjual/browse');
    Route::get('/utjual/browseuang', 'App\Http\Controllers\OTransaksi\UtjualController@browseuang')->middleware(['auth'])->name('utjual/browseuang');
    Route::get('/get-utjual', 'App\Http\Controllers\OTransaksi\UtjualController@getUtjual')->middleware(['auth'])->name('get-utjual');
    Route::get('/get-utjual-report', 'App\Http\Controllers\OReport\RUtjualController@getUtjualReport')->middleware(['auth'])->name('get-utjual-report');
    Route::post('jasper-utjual-report', 'App\Http\Controllers\OReport\RUtjualController@jasperUtjualReport')->middleware(['auth']);
// Dynamic Utjual
Route::get('/utjual/edit', 'App\Http\Controllers\OTransaksi\UtjualController@edit')->middleware(['auth'])->name('utjual.edit');
Route::post('/utjual/update/{utjual}', 'App\Http\Controllers\OTransaksi\UtjualController@update')->middleware(['auth'])->name('utjual.update');
Route::get('/utjual/delete/{utjual}', 'App\Http\Controllers\OTransaksi\UtjualController@destroy')->middleware(['auth'])->name('utjual.delete');
Route::get('/utjual/repost/{utjual}', 'App\Http\Controllers\OTransaksi\UtjualController@repost')->middleware(['auth'])->name('utjual.repost');
Route::get('/jsutjualc/{utjual:NO_ID}', 'App\Http\Controllers\OTransaksi\UtjualController@jsutjualc')->middleware(['auth']);
    


/// HUT

Route::get('/hut', 'App\Http\Controllers\OTransaksi\HutController@index')->middleware(['auth'])->name('hut');
Route::post('/hut/store', 'App\Http\Controllers\OTransaksi\HutController@store')->middleware(['auth'])->name('hut/store');

Route::get('/rhut', 'App\Http\Controllers\OReport\RHutController@report')->middleware(['auth'])->name('rhut');
    // GET HUT
    Route::get('/get-hut', 'App\Http\Controllers\OTransaksi\HutController@getHut')->middleware(['auth'])->name('get-hut');
		
    Route::get('/get-hut-post', 'App\Http\Controllers\OTransaksi\HutController@getHut_posting')->middleware(['auth'])->name('get-hut-post');
		
    Route::get('/hut/print/{hut:NO_ID}', 'App\Http\Controllers\OTransaksi\HutController@cetak')->middleware(['auth']);
    Route::get('/get-hut-report', 'App\Http\Controllers\OReport\RHutController@getHutReport')->middleware(['auth'])->name('get-hut-report');
    Route::post('/jasper-hut-report', 'App\Http\Controllers\OReport\RHutController@jasperHutReport')->middleware(['auth']);
// Dynamic Hut
Route::get('/hut/edit', 'App\Http\Controllers\OTransaksi\HutController@edit')->middleware(['auth'])->name('hut.edit');
Route::post('/hut/update/{hut}', 'App\Http\Controllers\OTransaksi\HutController@update')->middleware(['auth'])->name('hut.update');
Route::get('/hut/delete/{hut}', 'App\Http\Controllers\OTransaksi\HutController@destroy')->middleware(['auth'])->name('hut.delete');

Route::post('hut/posting', 'App\Http\Controllers\OTransaksi\HutController@posting')->middleware(['auth']);
Route::get('hut/index-posting', 'App\Http\Controllers\OTransaksi\HutController@index_posting')->middleware(['auth']);
Route::get('/hut/browse_hutd', 'App\Http\Controllers\OTransaksi\HutController@browse_hutd')->middleware(['auth'])->name('hut/browse_hutd');
Route::get('/hut/cetak/{hut:NO_ID}','App\Http\Controllers\OTransaksi\HutController@cetak')->middleware(['auth']);




// PIU

Route::get('/piu', 'App\Http\Controllers\OTransaksi\PiuController@index')->middleware(['auth'])->name('piu');
Route::post('/piu/store', 'App\Http\Controllers\OTransaksi\PiuController@store')->middleware(['auth'])->name('piu/store');

Route::get('/rpiu', 'App\Http\Controllers\OReport\RPiuController@report')->middleware(['auth'])->name('rpiu');
    // GET HUT
    Route::get('/get-piu', 'App\Http\Controllers\OTransaksi\PiuController@getPiu')->middleware(['auth'])->name('get-piu');
		
    Route::get('/get-piu-post', 'App\Http\Controllers\OTransaksi\PiuController@getHut_posting')->middleware(['auth'])->name('get-piu-post');
		
    Route::get('/hut/print/{hut:NO_ID}', 'App\Http\Controllers\OTransaksi\PiuController@cetak')->middleware(['auth']);
    Route::get('/get-piu-report', 'App\Http\Controllers\OReport\RPiuController@getPiuReport')->middleware(['auth'])->name('get-piu-report');
    Route::post('/jasper-piu-report', 'App\Http\Controllers\OReport\RPiuController@jasperPiuReport')->middleware(['auth']);
// Dynamic Hut
Route::get('/piu/edit', 'App\Http\Controllers\OTransaksi\PiuController@edit')->middleware(['auth'])->name('piu.edit');
Route::post('/piu/update/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@update')->middleware(['auth'])->name('piu.update');
Route::get('/piu/delete/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@destroy')->middleware(['auth'])->name('piu.delete');

Route::post('piu/posting', 'App\Http\Controllers\OTransaksi\PiuController@posting')->middleware(['auth']);
Route::get('piu/index-posting', 'App\Http\Controllers\OTransaksi\PiuController@index_posting')->middleware(['auth']);
Route::get('/piu/browse_piud', 'App\Http\Controllers\OTransaksi\PiuController@browse_piud')->middleware(['auth'])->name('piu/browse_piud');
Route::get('/piu/cetak/{piu:NO_ID}','App\Http\Controllers\OTransaksi\PiuController@cetak')->middleware(['auth']);


// Operational Transaksi Kik
Route::get('/kik', 'App\Http\Controllers\OTransaksi\KikController@index')->middleware(['auth'])->name('kik');
Route::post('/kik/store', 'App\Http\Controllers\OTransaksi\KikController@store')->middleware(['auth'])->name('kik/store');
//Route::get('/kik/create', 'App\Http\Controllers\OTransaksi\KikController@create')->middleware(['auth'])->name('terima/create');
Route::get('/get-kik', 'App\Http\Controllers\OTransaksi\KikController@getKik')->middleware(['auth'])->name('get-kik');
Route::get('/rkik', 'App\Http\Controllers\OReport\RKikController@report')->middleware(['auth'])->name('rkik');
Route::get('/get-kik-report', 'App\Http\Controllers\OReport\RKikController@getKikReport')->middleware(['auth'])->name('get-kik-report');

Route::get('kik/index-posting', 'App\Http\Controllers\OTransaksi\KikController@index_posting')->middleware(['auth']);



// Operational Memo
Route::get('/memo', 'App\Http\Controllers\FTransaksi\MemoController@index')->middleware(['auth'])->name('memo');
Route::post('/memo/store', 'App\Http\Controllers\FTransaksi\MemoController@store')->middleware(['auth'])->name('memo/store');
Route::get('/memo/create', 'App\Http\Controllers\FTransaksi\MemoController@create')->middleware(['auth'])->name('memo/create');
Route::get('/get-memo', 'App\Http\Controllers\FTransaksi\MemoController@getMemo')->middleware(['auth'])->name('get-memo');
Route::get('/rmemo', 'App\Http\Controllers\FReport\RMemoController@report')->middleware(['auth'])->name('rmemo');
Route::get('/get-memo-report', 'App\Http\Controllers\FReport\RMemoController@getMemoReport')->middleware(['auth'])->name('get-memo-report');

// Operational Kas Masuk
Route::get('/kas', 'App\Http\Controllers\FTransaksi\KasController@index')->middleware(['auth'])->name('kas');
Route::post('/kas/store', 'App\Http\Controllers\FTransaksi\KasController@store')->middleware(['auth'])->name('kas/store');
Route::get('/kas/create', 'App\Http\Controllers\FTransaksi\KasController@create')->middleware(['auth'])->name('kas/create');
Route::get('/get-kas', 'App\Http\Controllers\FTransaksi\KasController@getKas')->middleware(['auth'])->name('get-kas');
Route::get('/rkas', 'App\Http\Controllers\FReport\RKasController@report')->middleware(['auth'])->name('rkas');
Route::get('/get-kas-report', 'App\Http\Controllers\FReport\RKasController@getKasReport')->middleware(['auth'])->name('get-kas-report');
Route::get('/kas/cetak/{kas:NO_ID}','App\Http\Controllers\FTransaksi\KasController@cetak')->middleware(['auth']);
Route::get('/get-detail-kas', 'App\Http\Controllers\FTransaksi\KasController@getDetailKas')->middleware(['auth'])->name('get-detail-kas');




// Operational Kas Keluar
Route::get('/kask', 'App\Http\Controllers\FTransaksi\KaskController@index')->middleware(['auth'])->name('kask');
Route::post('/kask/store', 'App\Http\Controllers\FTransaksi\KaskController@store')->middleware(['auth'])->name('kask/store');
Route::get('/kask/create', 'App\Http\Controllers\FTransaksi\KaskController@create')->middleware(['auth'])->name('kask/create');
Route::get('/get-kask', 'App\Http\Controllers\FTransaksi\KaskController@getKask')->middleware(['auth'])->name('get-kask');

// Operational Bank Masuk
Route::get('/bank', 'App\Http\Controllers\FTransaksi\BankController@index')->middleware(['auth'])->name('bank');
Route::post('/bank/store', 'App\Http\Controllers\FTransaksi\BankController@store')->middleware(['auth'])->name('bank/store');
Route::get('/bank/create', 'App\Http\Controllers\FTransaksi\BankController@create')->middleware(['auth'])->name('bank/create');
Route::get('/get-bank', 'App\Http\Controllers\FTransaksi\BankController@getBank')->middleware(['auth'])->name('get-bank');
Route::get('/rbank', 'App\Http\Controllers\FReport\RBankController@report')->middleware(['auth'])->name('rbank');
Route::get('/get-bank-report', 'App\Http\Controllers\FReport\RBankController@getBankReport')->middleware(['auth'])->name('get-bank-report');
Route::get('/bank/cetak/{bank:NO_ID}','App\Http\Controllers\FTransaksi\BankController@cetak')->middleware(['auth']);
Route::get('/get-detail-bank', 'App\Http\Controllers\FTransaksi\BankController@getDetailBank')->middleware(['auth'])->name('get-detail-bank');


// Operational Bank Keluar
Route::get('/bankk', 'App\Http\Controllers\FTransaksi\BankkController@index')->middleware(['auth'])->name('bankk');
Route::post('/bankk/store', 'App\Http\Controllers\FTransaksi\BankkController@store')->middleware(['auth'])->name('bankk/store');
Route::get('/bankk/create', 'App\Http\Controllers\FTransaksi\BankkController@create')->middleware(['auth'])->name('bankk/create');
Route::get('/get-bankk', 'App\Http\Controllers\FTransaksi\BankkController@getBankk')->middleware(['auth'])->name('get-bankk');











/// PEMBATAS DENGAN BAWAH














//Dynamic Route

// User
Route::get('/user/show/{user}', 'App\Http\Controllers\UserController@show')->name('userid');
Route::get('/user/edit/{user}', 'App\Http\Controllers\UserController@edit')->name('useredit');
//Route::get('/user/delete/{user}', 'App\Http\Controllers\UserController@destroy')->name('userid');
//Route::post('/user/update/{user}', 'App\Http\Controllers\UserController@update')->name('userid');


// So
Route::get('/so/show/{so}', 'App\Http\Controllers\OTransaksi\SoController@show')->name('soid');
Route::get('/so/edit/{so}', 'App\Http\Controllers\OTransaksi\SoController@edit')->name('so.edit');
Route::post('/so/update/{so}', 'App\Http\Controllers\OTransaksi\SoController@update')->name('so.update');
Route::get('/so/delete/{so}', 'App\Http\Controllers\OTransaksi\SoController@destroy')->name('so.delete');


// Orderk
Route::get('/orderk/show/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@show')->name('orderkid');
//Route::get('/orderk/edit/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@edit')->name('orderk.edit');
Route::post('/orderk/update/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@update')->name('orderk.update');
Route::get('/orderk/delete/{orderk}', 'App\Http\Controllers\OTransaksi\OrderkController@destroy')->name('orderk.delete');


// Pakai
Route::get('/pakai/show/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@show')->name('pakaiid');
//Route::get('/pakai/edit/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@edit')->name('pakai.edit');
Route::post('/pakai/update/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@update')->name('pakai.update');
Route::get('/pakai/delete/{pakai}', 'App\Http\Controllers\OTransaksi\PakaiController@destroy')->name('pakai.delete');

// Terima
Route::get('/terima/show/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@show')->name('terimaid');
//Route::get('/terima/edit/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@edit')->name('terima.edit');
Route::post('/terima/update/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@update')->name('terima.update');
Route::get('/terima/delete/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@destroy')->name('terima.delete');


// Beli
Route::get('/beli/show/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@show')->name('beliid');
//Route::get('/beli/edit/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@edit')->name('beli.edit');
Route::post('/beli/update/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@update')->name('beli.update');
Route::get('/beli/delete/{beli}', 'App\Http\Controllers\OTransaksi\BeliController@destroy')->name('beli.delete');


Route::get('/rthut', 'App\Http\Controllers\OReport\RThutController@report')->middleware(['auth'])->name('rthut');
Route::post('jasper-thut-report', 'App\Http\Controllers\OReport\RThutController@jasperThutReport')->middleware(['auth']);

Route::get('/rtpiu', 'App\Http\Controllers\OReport\RTpiuController@report')->middleware(['auth'])->name('rtpiu');
Route::post('jasper-tpiu-report', 'App\Http\Controllers\OReport\RTpiuController@jasperTpiuReport')->middleware(['auth']);

Route::get('/rum', 'App\Http\Controllers\OReport\RUmController@report')->middleware(['auth'])->name('rum');
Route::post('jasper-um-report', 'App\Http\Controllers\OReport\RUmController@jasperUmReport')->middleware(['auth']);

Route::get('/ruj', 'App\Http\Controllers\OReport\RUjController@report')->middleware(['auth'])->name('ruj');
Route::post('jasper-uj-report', 'App\Http\Controllers\OReport\RUjController@jasperUjReport')->middleware(['auth']);




// Jual
Route::get('/jual/show/{jual}', 'App\Http\Controllers\OTransaksi\JualController@show')->name('jualid');
//Route::get('/jual/edit/{jual}', 'App\Http\Controllers\OTransaksi\JualController@edit')->name('jual.edit');
Route::post('/jual/update/{jual}', 'App\Http\Controllers\OTransaksi\JualController@update')->name('jual.update');
Route::get('/jual/delete/{jual}', 'App\Http\Controllers\OTransaksi\JualController@destroy')->name('jual.delete');

// Isi Posting
Route::get('/beli/post', 'App\Http\Controllers\OTransaksi\BeliController@post')->middleware(['auth'])->name('beli.post');
Route::get('/beli/browse_posting', 'App\Http\Controllers\OTransaksi\BeliController@browse_posting')->middleware(['auth'])->name('beli.browse_posting');
Route::post('beli/posting', 'App\Http\Controllers\OTransaksi\BeliController@posting')->middleware(['auth'])->name('beli.posting');

Route::get('/retur/post', 'App\Http\Controllers\OTransaksi\ReturController@post')->middleware(['auth'])->name('retur.post');
Route::get('/retur/browse_posting', 'App\Http\Controllers\OTransaksi\ReturController@browse_posting')->middleware(['auth'])->name('retur.browse_posting');
Route::post('retur/posting', 'App\Http\Controllers\OTransaksi\ReturController@posting')->middleware(['auth'])->name('retur.posting');

Route::get('/jual/post', 'App\Http\Controllers\OTransaksi\JualController@post')->middleware(['auth'])->name('jual.post');
Route::get('/jual/browse_posting', 'App\Http\Controllers\OTransaksi\JualController@browse_posting')->middleware(['auth'])->name('jual.browse_posting');
Route::post('jual/posting', 'App\Http\Controllers\OTransaksi\JualController@posting')->middleware(['auth'])->name('jual.posting');

Route::get('/stockb/post', 'App\Http\Controllers\OTransaksi\StockbController@post')->middleware(['auth'])->name('stockb.post');
Route::get('/stockb/browse_posting', 'App\Http\Controllers\OTransaksi\StockbController@browse_posting')->middleware(['auth'])->name('stockb.browse_posting');
Route::post('stockb/posting', 'App\Http\Controllers\OTransaksi\StockbController@posting')->middleware(['auth'])->name('stockb.posting');

Route::get('/harga/post', 'App\Http\Controllers\OTransaksi\HargaController@post')->middleware(['auth'])->name('harga.post');
Route::get('/harga/browse_posting', 'App\Http\Controllers\OTransaksi\HargaController@browse_posting')->middleware(['auth'])->name('harga.browse_posting');
Route::post('harga/posting', 'App\Http\Controllers\OTransaksi\HargaController@posting')->middleware(['auth'])->name('harga.posting');




// Hut
Route::get('/hut/show/{hut}', 'App\Http\Controllers\OTransaksi\HutController@show')->name('hutid');
//Route::get('/hut/edit/{hut}', 'App\Http\Controllers\OTransaksi\HutController@edit')->name('hut.edit');
Route::post('/hut/update/{hut}', 'App\Http\Controllers\OTransaksi\HutController@update')->name('hut.update');
Route::get('/hut/delete/{hut}', 'App\Http\Controllers\OTransaksi\HutController@destroy')->name('hut.delete');


// Piu
Route::get('/piu/show/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@show')->name('piuid');
//Route::get('/piu/edit/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@edit')->name('piu.edit');
Route::post('/piu/update/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@update')->name('piu.update');
Route::get('/piu/delete/{piu}', 'App\Http\Controllers\OTransaksi\PiuController@destroy')->name('piu.delete');



// Piu Non
Route::get('/piun/show/{piun}', 'App\Http\Controllers\OTransaksi\PiunController@show')->name('piunid');
Route::get('/piun/edit/{piun}', 'App\Http\Controllers\OTransaksi\PiunController@edit')->name('piun.edit');
Route::post('/piun/update/{piun}', 'App\Http\Controllers\OTransaksi\PiunController@update')->name('piun.update');
Route::get('/piun/delete/{piun}', 'App\Http\Controllers\OTransaksi\PiunController@destroy')->name('piun.delete');


// Terima
Route::get('/terima/show/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@show')->name('terimaid');
//Route::get('/terima/edit/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@edit')->name('terima.edit');
Route::post('/terima/update/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@update')->name('terima.update');
Route::get('/terima/delete/{terima}', 'App\Http\Controllers\OTransaksi\TerimaController@destroy')->name('terima.delete');


// Operational Memo
Route::get('/memo', 'App\Http\Controllers\FTransaksi\MemoController@index')->middleware(['auth'])->name('memo');
Route::post('/memo/store', 'App\Http\Controllers\FTransaksi\MemoController@store')->middleware(['auth'])->name('memo/store');
Route::get('/rmemo', 'App\Http\Controllers\FReport\RMemoController@report')->middleware(['auth'])->name('rmemo');
    // GET MEMO
    Route::get('/get-memo', 'App\Http\Controllers\FTransaksi\MemoController@getMemo')->middleware(['auth'])->name('get-memo');
    Route::get('/get-memo-report', 'App\Http\Controllers\FReport\RMemoController@getMemoReport')->middleware(['auth'])->name('get-memo-report');
    Route::post('memo/jasper-memo-report', 'App\Http\Controllers\FReport\RMemoController@jasperMemoReport')->middleware(['auth']);
// Dynamic Memo
Route::get('/memo/edit', 'App\Http\Controllers\FTransaksi\MemoController@edit')->middleware(['auth'])->name('memo.edit');
Route::post('/memo/update/{memo}', 'App\Http\Controllers\FTransaksi\MemoController@update')->middleware(['auth'])->name('memo.update');
Route::get('/memo/delete/{memo}', 'App\Http\Controllers\FTransaksi\MemoController@destroy')->middleware(['auth'])->name('memo.delete');
Route::get('/memo/cetak/{memo:NO_ID}','App\Http\Controllers\FTransaksi\MemoController@cetak')->middleware(['auth']);
Route::get('/get-detail-memo', 'App\Http\Controllers\FTransaksi\MemoController@getDetailMemo')->middleware(['auth'])->name('get-detail-memo');
    
//Report
Route::post('/rmemo/cetak', 'App\Http\Controllers\FReport\RMemoController@cetak')->middleware(['auth'])->name('rmemo.cetak');

// Operational Kas Masuk
Route::get('/kas', 'App\Http\Controllers\FTransaksi\KasController@index')->middleware(['auth'])->name('kas');
Route::post('/kas/store', 'App\Http\Controllers\FTransaksi\KasController@store')->middleware(['auth'])->name('kas/store');
Route::get('/kas_validasi', 'App\Http\Controllers\FTransaksi\KasController@create_validasi')->middleware(['auth'])->name('kas_validasi');


Route::get('/kas/browse_bukti', 'App\Http\Controllers\FTransaksi\KasController@browse_bukti')->middleware(['auth'])->name('kas/browse_bukti');
 
Route::get('/rkas', 'App\Http\Controllers\FReport\RKasController@report')->middleware(['auth'])->name('rkas');
    // GET KAS
    Route::get('/get-kas', 'App\Http\Controllers\FTransaksi\KasController@getKas')->middleware(['auth'])->name('get-kas');
    Route::get('/get-kas-report', 'App\Http\Controllers\FReport\RKasController@getKasReport')->middleware(['auth'])->name('get-kas-report');
    Route::post('rkas/jasper-kas-report', 'App\Http\Controllers\FReport\RKasController@jasperKasReport')->middleware(['auth']);
// Dynamic Kas Masuk
Route::get('/kas/edit', 'App\Http\Controllers\FTransaksi\KasController@edit')->middleware(['auth'])->name('kas.edit');
Route::post('/kas/update/{kas}', 'App\Http\Controllers\FTransaksi\KasController@update')->middleware(['auth'])->name('kas.update');
Route::get('/kas/delete/{kas}', 'App\Http\Controllers\FTransaksi\KasController@destroy')->middleware(['auth'])->name('kas.delete');

// Operational Bank Masuk
Route::get('/bank', 'App\Http\Controllers\FTransaksi\BankController@index')->middleware(['auth'])->name('bank');
Route::post('/bank/store', 'App\Http\Controllers\FTransaksi\BankController@store')->middleware(['auth'])->name('bank/store');
Route::get('/rbank', 'App\Http\Controllers\FReport\RBankController@report')->middleware(['auth'])->name('rbank');
    // GET BANK
    Route::get('/get-bank', 'App\Http\Controllers\FTransaksi\BankController@getBank')->middleware(['auth'])->name('get-bank');
    Route::get('/get-bank-report', 'App\Http\Controllers\FReport\RBankController@getBankReport')->middleware(['auth'])->name('get-bank-report');
    Route::post('rbank/jasper-bank-report', 'App\Http\Controllers\FReport\RBankController@jasperBankReport')->middleware(['auth']);
    Route::get('/jasper-bank-trans/{bank:NO_ID}', 'App\Http\Controllers\FTransaksi\BankController@jasperBankTrans')->middleware(['auth']);
// Dynamic Bank Masuk
Route::get('/bank/edit', 'App\Http\Controllers\FTransaksi\BankController@edit')->middleware(['auth'])->name('bank.edit');
Route::post('/bank/update/{bank}', 'App\Http\Controllers\FTransaksi\BankController@update')->middleware(['auth'])->name('bank.update');
Route::get('/bank/delete/{bank}', 'App\Http\Controllers\FTransaksi\BankController@destroy')->middleware(['auth'])->name('bank.delete');


//Report
Route::post('/rmemo/cetak', 'App\Http\Controllers\FReport\RMemoController@cetak')->middleware(['auth'])->name('rmemo.cetak');



// Laporan Kartu Stok
Route::get('/rkarstk', 'App\Http\Controllers\OReport\RKarstkController@kartu')->middleware(['auth']);
Route::get('/get-stok-kartu', 'App\Http\Controllers\OReport\RKarstkController@getStokKartu')->middleware(['auth']);
Route::post('jasper-stok-kartu', 'App\Http\Controllers\OReport\RKarstkController@jasperStokKartu')->middleware(['auth']);

// Laporan Kartu Hutang
Route::get('/rkartuh', 'App\Http\Controllers\OReport\RKartuhController@kartu')->middleware(['auth']);
Route::get('/get-hut-kartu', 'App\Http\Controllers\OReport\RKartuhController@getHutKartu')->middleware(['auth']);
Route::post('jasper-hut-kartu', 'App\Http\Controllers\OReport\RKartuhController@jasperHutKartu')->middleware(['auth']);

// Laporan Kartu Piutang
Route::get('/rkartup', 'App\Http\Controllers\OReport\RKartupController@kartu')->middleware(['auth']);
Route::get('/get-piu-kartu', 'App\Http\Controllers\OReport\RKartupController@getPiuKartu')->middleware(['auth']);
Route::post('jasper-piu-kartu', 'App\Http\Controllers\OReport\RKartupController@jasperPiuKartu')->middleware(['auth']);


// Laporan Sisa Hutang
Route::get('/rsisahut', 'App\Http\Controllers\OReport\RKartuhController@sisa')->middleware(['auth']);
Route::get('/get-hut-sisa', 'App\Http\Controllers\OReport\RKartuhController@getHutSisa')->middleware(['auth']);
Route::post('/jasper-hutsisa-report', 'App\Http\Controllers\OReport\RKartuhController@jasperHutSisaReport')->middleware(['auth']);

// Laporan Sisa Piutang
Route::get('/rsisapiu', 'App\Http\Controllers\OReport\RKartupController@sisa')->middleware(['auth']);
Route::get('/get-piu-sisa', 'App\Http\Controllers\OReport\RKartupController@getPiuSisa')->middleware(['auth']);
Route::post('/jasper-piusisa-report', 'App\Http\Controllers\OReport\RKartupController@jasperPiuSisaReport')->middleware(['auth']);

#import Excel

Route::get('/import_excel', 'App\Http\Controllers\OReport\RExcelController@report')->middleware(['auth'])->name('rexcel');
Route::post('/import_excel/import_excel', 'App\Http\Controllers\OReport\RExcelController@import_excel');

// Import Excel Cust

Route::post('/ImportCustProses', 'App\Http\Controllers\OReport\RImportCustController@ImportCustProses')->middleware(['auth'])->name('ImportCustomerProses');
Route::get('/rimportcust', 'App\Http\Controllers\OReport\RImportCustController@importc')->middleware(['auth'])->name('rimportcust');

// Import Excel Sup

Route::post('/ImportSupProses', 'App\Http\Controllers\OReport\RImportSupController@ImportSupProses')->middleware(['auth'])->name('ImportSuplierProses');
Route::get('/rimportsup', 'App\Http\Controllers\OReport\RImportSupController@importc')->middleware(['auth'])->name('rimportsup');

// Import Excel Brg

Route::post('/ImportBrgProses', 'App\Http\Controllers\OReport\RImportBrgController@ImportBrgProses')->middleware(['auth'])->name('ImportBarangProses');
Route::get('/rimportbrg', 'App\Http\Controllers\OReport\RImportBrgController@importc')->middleware(['auth'])->name('rimportbrg');


//coba - coba 28/01/2025
//report dengan grup
Route::get('/rbgroup', 'App\Http\Controllers\OReport\RBgroupController@report')->middleware(['auth'])->name('rbgroup');
Route::post('/jasper-bgroup-report', 'App\Http\Controllers\OReport\RBgroupController@jasperBgroupReport')->middleware(['auth'])->name('jasper-bgroup-report');



require __DIR__.'/auth.php';
