<?php

use App\Http\Controllers\ChartJsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;

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

Route::get('reloadcaptcha', 'UserController@reloadcaptcha')->name('reloadcaptcha');


Route::get('dashboard_kabag_kasi', [ChartJsController::class, 'index'])->name('chartjs.index');
Route::get('dashboard_admin_spp', [ChartJsController::class, 'index'])->name('chartjs.index');

Route::get('/', function () {
    return view('login');
})->name('login');
Route::get('/error', function () {
    return view('errors.404');
});

Route::get('/dashboard', function () {
    return view('main');
});

Route::get('/bukti_kas', function () {
    return view('page.cetak_bukti_kas');
});

Route::get('/profil_bagian', function () {
    return view('page.bagian.profil_bagian');
});
Route::get('policy', function () {
    return view('privacypolicy');
});


// Route::get('spp', function () {
//     return view('page.spp.spp');
// });

// Route::get('spp/tambah', function () {
//     return view('page.spp.spp_tambah');
// });





// Route::get('laporan/view', function () {
//      return view('page.laporan.laporan_export');
//  });







// Route::get('laporan', function () {
//     return view('page.laporan.laporan');
// });

Route::get('vendor', function () {
    return view('page.vendor.vendor');
});

Route::get('dash', function () {
    return view('dashboard_a');
});

Route::get('user', function () {
    return view('page.user.user');
});

//Login
Route::get('login', function () {
    // dd(session()->all());
    return view('login');
})->name('login');

// Route untuk login via token
Route::get('login/token/{token}', function ($token) {
    // Anda dapat memindahkan token ini ke file .env untuk keamanan yang lebih baik
    $staticToken = env('AUTOLOGIN_TOKEN');
 
    if ($token === $staticToken) {
        Session::put('username', 'asisten_pembayaran');
        Session::put('hak_akses', 39);
        Session::put('bagian', 134);
        Session::put('id', 198);
        Session::put('grup_ui', 4);
        Session::put('petugas_pp', 1);
        Session::put('company', 5);
        Session::put('level', null);
        Session::put('login', true);
 
        return redirect('dashboard')->with('alert-success', 'Login via token berhasil!');
    }
 
    return redirect('login')->with('alert', 'Token tidak valid.');
})->name('login.token');

// Route baru untuk login via token yang diarahkan ke Controller
Route::get('login/token/v2/{token}', 'AutoLoginController@loginByToken')->name('login.token.v2');

// Route untuk login via payload terenkripsi
Route::get('login/secure/{payload}', function ($payload) {
    $key = env('SECURE_LOGIN_KEY');
    if (!$key) {
        return redirect('login')->with('alert', 'Kunci enkripsi tidak dikonfigurasi.');
    }

    // Laravel membutuhkan kunci dengan panjang tertentu (16, 24, atau 32 bytes).
    // Kita akan hash kunci dari .env untuk memastikan panjangnya 32 bytes.
    $encryptionKey = hash('sha256', $key, true);
    $cipher = config('app.cipher');

    try {
        $encrypter = new Encrypter($encryptionKey, $cipher);
        $decryptedData = $encrypter->decrypt($payload);

        // Pastikan data yang didekripsi adalah JSON yang valid
        $userData = json_decode($decryptedData, true);

        if (json_last_error() === JSON_ERROR_NONE && isset($userData['username'])) {
            Session::put('username', $userData['username']);
            Session::put('hak_akses', $userData['hak_akses']);
            Session::put('bagian', $userData['bagian']);
            Session::put('id', $userData['id']);
            Session::put('level', $userData['level']);
            Session::put('login', true);
            
            return redirect('dashboard')->with('alert-success', 'Login via payload terenkripsi berhasil!');
        }

        return redirect('login')->with('alert', 'Payload tidak valid.');

    } catch (DecryptException $e) {
        return redirect('login')->with('alert', 'Payload tidak dapat didekripsi.');
    }
})->name('login.secure');

// Route untuk mematikan sesi yang dibuat via token atau secure link
Route::get('logout/special', function () {
    Session::flush(); // Menghapus semua data dari session
    return redirect()->route('login')->with('alert-success', 'Anda telah berhasil keluar.');
})->name('logout.special');

// Route untuk admin menghentikan sesi 'asisten_pembayaran'
Route::get('admin/kill-session/asisten-pembayaran', function () {
    // Menghapus semua sesi milik user dengan ID 198
    DB::table('sessions')->where('user_id', 198)->delete();

    return back()->with('alert-success', 'Semua sesi untuk asisten_pembayaran telah dihentikan.');
})->name('admin.kill.session');

Route::get('laporan', 'LaporanController@index');
Route::post('laporan_web', 'LaporanPDFController@index')->name('laporan_web');
Route::post('laporan_web_detail', 'LaporanWebDetailController@index')->name('laporan_web_detail');
Route::post('handle_error', 'LaporanWebDetailController@handle_error')->name('handle_error');
Route::post('laporan_pdf_detail', 'LaporanWebDetailController@export_pdf')->name('laporan_pdf_detail');
Route::post('laporan_pdf', 'LaporanPDFController@export_pdf')->name('laporan_pdf');
Route::post('laporan_csv', 'LaporanCSVController@')->name('laporan_csv');



Route::get('change_password', 'UserController@change_password')->name('change_password');
Route::post('user/change_password', 'UserController@change_password_store');
Route::get('forgot_password', 'UserController@forgot_password')->name('forgot_password');
Route::post('forgot_password', 'UserController@forgot_password_store')->name('forgot_password_post');
Route::get('reset_password', 'UserController@reset_password')->name('reset_password');
Route::post('reset_password', 'UserController@reset_password_post')->name('reset_password_post');

Route::post('laporan/advanced_search', 'LaporanController@advanced_search')->name('laporan_advanced_search');
// Route::post('laporan/laporan_export_detail', 'LaporanWebDetailController@export')->name('laporan_export_detail');
Route::post('laporan/laporan_export_detail', 'Laporan_excel_detail_controller@export')->name('laporan_export_detail');
Route::post('laporan/exports', 'LaporanExportController@export')->name('laporan_export');
Route::post('laporan/csv', 'laporanCSVController@isi_csv')->name('export_csv');

Route::get('dashboard', 'DashboardController@index');

Route::get('spp/rekamjejak/{id}', 'SppController@viewrekamjejak');

// Advanced semuasap sppb sppn
Route::get('advanced/index', 'AdvancedController@index');
// Advanced Search SAP SPPB
Route::get('advancedb/index', 'AdvancedSppbController@index');
Route::get('advancedn/index', 'AdvancedSppnController@index');
Route::get('advancedbn/index', 'AdvancedSppbSppnController@index');


Route::get('spp', 'SppController@index')->name('index');
Route::get('pembayaran', 'Pembayaran@index')->name('indexpembayaran');
Route::get('pembayaran/data/sudah-upload', 'Pembayaran@getDataSudahUpload')->name('pembayaran.data.sudahupload');
Route::get('pembayaran/data/belum-upload', 'Pembayaran@getDataBelumUpload')->name('pembayaran.data.belumupload');

Route::get('export_excel', 'Pembayaran@export_excel')->name('export_excel');
Route::post('export_excel_terpilih', 'Pembayaran@export_excel_terpilih')->name('export_excel_terpilih');
Route::get('export_pdf', 'Pembayaran@export_pdf')->name('export_pdf');
Route::post('export_pdf_terpilih', 'Pembayaran@export_pdf_terpilih')->name('export_pdf_terpilih');



Route::get('sppd/server', 'SppdServerSideController@index')->name('SppdServerSide');
Route::get('sppd', 'SppdController@index')->name('indexsppd');
Route::post('spp/dokumen_tambahan/store', 'DokumenTambahanController@store')->name('storedokumentambahan');
Route::get('dokumen_tambahan/hapus/{id}', 'DokumenTambahanController@destroy')->name('destroydokumentambahan');
Route::get('sppd/getSppdPosisiOptions', 'SppdController@getSppdPosisiOptions')->name('getSppdPosisiOptions');
Route::get('sppd/getRegionalOptions', 'SppdController@getRegionalOptions')->name('getRegionalOptions');

// SPP Khusus
Route::get('spp_keuangan', 'SppKController@index')->name('indexsppk');
Route::get('spp_keuangan/detail/{id}', 'SppKController@viewdetail')->name('detailsppk');
Route::get('spp_keuangan/tambah', 'SppKeuanganController@index');
Route::post('spp_keuangan/store', 'SppKeuanganController@store')->name('storesppk');
Route::get('spp_keuangan/edit/{id}', 'SppKController@viewupdate')->name('viewupdatesppk');
Route::post('spp_keuangan/update/store/{id}', 'SppKController@update')->name('updatesppk');
Route::get('spp_keuangan/cetak/{id}', 'SppKController@cetak')->name('cetaksppk');
Route::post('spp_keuangan/upload/{id}', 'SppKController@upload')->name('uploadspp');
Route::post('spp_keuangan/advanced_search', 'SppKController@advanced_search')->name('advanced_search');
Route::get('spp_keuangan/cetak_bukti_kas/{id}', 'SppKController@cetakbuktikas')->name('cetakbuktikasspp');
Route::post('spp_keuangan/update_bayar/{id}', 'SppKController@update_bayar');

//Action
Route::get('spp_keuangan/send/{id}', 'SppKController@kirim')->name('sendsppk');
Route::get('spp_keuangan/accept/{id}', 'SppKController@accept')->name('acceptsppk');
Route::post('spp_keuangan/revisi/{id}', 'SppKController@revisi')->name('revisisppk');
Route::post('spp_keuangan/bayar/{id}', 'SppKController@bayar')->name('bayarsppk');
Route::get('spp_keuangan/selesai/{id}', 'SppKController@selesai')->name('selesaisppk');
Route::post('spp_keuangan/bukti_kas/{id}', 'SppKController@bukti_kas')->name('bukti_kas');
Route::get('spp_keuangan/batal/{id}', 'SppKController@batal');
Route::get('pembayaran/showrekamjejak', 'Pembayaran@showRekamJejak')->name('showRekamJejak');


//SPPb SPPn
Route::get('spp/tambah', 'SppbController@index')->name('index_spp');
Route::post('spp/realisasi', 'SppbController@realisasi')->name('realisasi');
Route::post('spp/realisasisppn', 'SppbController@realisasisppn')->name('realisasisppn');
Route::post('spp/store', 'SppbController@store')->name('storespp');
Route::post('spp/check-urutan-anomaly', 'SppbController@checkUrutanAnomaly')->name('checkUrutanAnomaly');
Route::get('spp/detail/{id}', 'SppController@viewdetail')->name('detailspp');
Route::get('spp/validasi/{id}', 'ValidasiController@login_validasi')->name('loginvalidasispp');
Route::get('spp/validasi_spp/{id}', 'ValidasiController@index_spp')->name('validasispp');

Route::get('spp/edit/{id}', 'SppController@viewupdate')->name('viewupdatespp');
Route::get('spp/debug/edit/{id}', 'SppController@debugViewUpdate')->name('debug.viewupdatespp'); // Route untuk debugging
Route::get('sppd/edit/{id}', 'SppController@viewupdate')->name('viewupdatesppd');
Route::get('sppd/edit2/{id}', 'SppController@viewupdate2')->name('viewupdatesppd2');
Route::post('spp/update/store/{id}', 'SppController@update')->name('updatespp');
Route::post('sppd/update/store/{id}', 'SppdController@update')->name('updatesppd');
Route::get('spp/cetak/{id}', 'SppController@cetak')->name('cetakspp');
Route::get('spp/cetak2/{id}', 'SppController@cetak2')->name('cetakspp2');
Route::get('spp/cetak_bukti_kas/{id}', 'SppController@cetakbuktikas')->name('cetakbuktikasspp');
Route::get('sppd/cetak_bukti_kas/{id}', 'SppdController@cetakbuktikas')->name('cetakbuktikassppd');
Route::post('spp/update_bayar/{id}', 'SppController@update_bayar');
Route::post('sppd', 'SppdController@advanced_search')->name('advanced_search_sppd');
Route::post('spp/advanced_search', 'SppController@advanced_search')->name('advanced_search_spp');
Route::get('sppd/rekam_jejak/{id}', 'SppdController@rekam_jejak')->name('rekamjejak');

route::get('sppb/getCostCenter', 'SppbController@get_cost_center')->name('getCostCenter');
route::get('sppb/getProfitCenter', 'SppbController@get_profit_center')->name('getProfitCenter');
// route::get('sppb/getCashFlow', 'SppbController@get_cash_flow')->name('getCashFlow');

route::get('sppd/getTodo', 'SppdServerSideController@getTodo')->name('getTodo');
route::get('sppd/getRevisi', 'SppdServerSideController@getRevisi')->name('getRevisi');
Route::get('sppd/getProses', 'SppdServerSideController@getProses')->name('getProses');
Route::get('sppd/getSelesai', 'SppdServerSideController@getSelesai')->name('getSelesai');
Route::get('sppd/getBatal', 'SppdServerSideController@getBatal')->name('getBatal');

Route::get('get-sppb-cetak-bukti-kas/{id}', 'SppdServerSideController@getSppbCetakBuktiKas')->name('getSppbCetakBuktiKas');
Route::get('get-sppn-cetak-bukti-kas/{id}', 'SppdServerSideController@getSppnCetakBuktiKas')->name('getSppnCetakBuktiKas');
Route::get('get-penerima/{id}', 'SppdServerSideController@getPenerima')->name('getPenerima');
Route::get('get-diterima/{id}', 'SppdServerSideController@getDiterima')->name('getDiterima');
Route::get('get-sppb-bayar/{id}', 'SppdServerSideController@getSppbBayar')->name('getSppbBayar');
Route::get('get-sppn-terima/{id}', 'SppdServerSideController@getSppnTerima')->name('getSppnTerima');

//Action
Route::post('sppd/upload/', 'SppdController@upload_no_doc')->name('nomor_dokumen');
Route::post('spp/upload/{id}', 'SppController@upload')->name('uploadspp');
Route::post('sppd/upload/{id}', 'SppdController@upload')->name('uploadsppd');
Route::get('spp/send/{id}', 'SppController@kirim')->name('sendspp');
Route::get('sppd/send/{id}', 'SppdController@kirim')->name('sendsppd');
Route::get('spp/accept/{id}', 'SppController@accept')->name('acceptspp');
Route::get('sppd/accept/{id}', 'SppdController@accept')->name('acceptsppd');
Route::post('spp/revisi/{id}', 'SppController@revisi')->name('revisispp');
Route::post('sppd/revisi/{id}', 'SppdController@revisi')->name('revisisppd');
Route::post('spp/bayar/{id}', 'SppController@bayar')->name('bayarspp');
Route::post('sppd/bayar/{id}', 'SppdController@bayar')->name('bayarsppd');
Route::post('spp/bukti_kas/{id}', 'SppController@bukti_kas')->name('bukti_kas');
Route::post('sppd/bukti_kas/{id?}', 'SppdController@bukti_kas')->name('bukti_kasd');
Route::post('sppd/update_bukti_kas/{id}', 'SppdController@update_bukti_kas')->name('update_bukti_kas');
Route::get('spp/selesai/{id}', 'SppController@selesai')->name('selesaispp');
Route::get('sppd/selesai/{id}', 'SppdController@selesai')->name('selesaisppd');
Route::get('spp/batal/{id}', 'SppController@batal');
Route::post('spp/upload_bukti_kas', 'SppController@upload_bukti_kas')->name('storebuktikas');
Route::post('sppd/ubah-status', 'SppdController@updateStatus')->name('ubah_status');


// Notifikasi
Route::get('send-new-spp-notification', 'NotificationNewSppController@sendTesNotif');
Route::post('mark-as-read', 'NotificationNewSppController@markNotifAsRead')->name('markNotifAsRead');
Route::post('mark-all-as-read', 'NotificationNewSppController@markAllNotifAsRead')->name('markAllNotifAsRead');
Route::post('delete-all-notifications', 'NotificationNewSppController@deleteAllNotifications')->name('deleteAllNotifications');
Route::post('get-notification', 'NotificationNewSppController@getUserNotification')->name('getUserNotification');
Route::get('notifikasi', 'NotificationNewSppController@index')->name('homeNotifikasi');


// Bagian
Route::get('bagian', 'BagianController@index');
Route::get('profil_bagian/{id}', 'BagianController@profil_kabag');
Route::post('bagian/store', 'BagianController@store');
Route::post('bagian/update', 'BagianController@update');
Route::post('bagian/update_kabag', 'BagianController@update_kabag');
Route::get('bagian/destroy/{id}/{status}', 'BagianController@destroy');

// Tampilan/UI
Route::get('tampilan', 'UiController@index');
Route::post('tampilan/tambah', 'UiController@tambah');
Route::post('tampilan/update', 'UiController@update');
Route::get('tampilan/destroy/{id}/{status}', 'UiController@destroy');

// Flow
Route::get('flow', 'FlowController@index');
Route::post('flow/tambah', 'FlowController@tambah');
Route::post('flow/update', 'FlowController@update');
Route::get('flow/destroy/{id}/{status}', 'FlowController@destroy');


// Company
Route::get('company', 'CompanyController@index');
Route::post('company/tambah', 'CompanyController@tambah');
Route::post('company/update', 'CompanyController@update');
Route::get('company/destroy/{id}/{status}', 'CompanyController@destroy');

// Bank
Route::get('bank', 'BankController@index');
Route::post('bank/store', 'BankController@store');
Route::post('bank/update', 'BankController@update');
Route::get('bank/destroy/{id}/{status}', 'BankController@destroy');

// Profit Center
Route::get('cash_flow', 'CashFlowController@index');
Route::post('cash_flow/store', 'CashFlowController@store');
Route::post('cash_flow/update', 'CashFlowController@update');
Route::get('cash_flow/destroy/{id}/{status}', 'CashFlowController@destroy');

// Cost Center
Route::get('cost_center', 'CostCenterController@index');
Route::post('cost_center/store', 'CostCenterController@store');
Route::post('cost_center/update', 'CostCenterController@update');
Route::get('cost_center/destroy/{id}/{status}', 'CostCenterController@destroy');
Route::post('cost_center/import', 'CostCenterController@import')->name('costcenter.import');

// GL
Route::get('gl', 'GLController@index');
Route::get('gl/getdata', 'GLController@getData')->name('getGL');
Route::post('gl/store', 'GLController@store');
Route::post('gl/update', 'GLController@update');
Route::get('gl/destroy/{id}/{status}', 'GLController@destroy');
Route::post('gl/import', 'GLController@import')->name('gl.import');

// GL Detail
Route::get('gl_detail', 'GLDetailController@index');
Route::post('gl_detail/store', 'GLDetailController@store');
Route::post('gl_detail/update', 'GLDetailController@update');
Route::get('gl_detail/destroy/{id}/{status}', 'GLDetailController@destroy');

// Customer
Route::get('customer', 'CsController@index');
Route::post('customer/store', 'CsController@store');
Route::post('customer/update', 'CsController@update');
Route::get('customer/destroy/{id}/{status}', 'CsController@destroy');
Route::get('customer/getdatatableall', 'CsController@getDataTableAll')->name('getCustomerDataTableAll');
Route::post('customer/import', 'CsController@import')->name('customer.import');

// Bahan & Jasa
Route::get('bahan_jasa', 'BJController@index');
Route::post('bahan_jasa/store', 'BJController@store');
Route::post('bahan_jasa/update', 'BJController@update');
Route::get('bahan_jasa/destroy/{id}/{status}', 'BJController@destroy');

// Hak Akses
Route::get('hak_akses', 'HakAksesController@index');
Route::post('hak_akses/store', 'HakAksesController@store');
Route::post('hak_akses/update', 'HakAksesController@update');
Route::get('hak_akses/destroy/{id}/{status}', 'HakAksesController@destroy');

// master budget RKAP
Route::get('rkap', 'RKAPController@index');
Route::get('rkap/getdata', 'RKAPController@getData');
Route::post('rkap/store', 'RKAPController@store');
Route::post('rkap/update', 'RKAPController@update');
Route::get('rkap/destroy/{id}/{status}', 'RKAPController@destroy');

// History Login
Route::get('histori_login', 'HistoryLoginController@index');
Route::post('histori_login/store/{user}', 'HistoryLoginController@store');
Route::post('histori_login/search', 'HistoryLoginController@search')->name('search_history_login');

// Profit Center
Route::get('profit_center', 'ProfitCenterController@index');
Route::post('profit_center/store', 'ProfitCenterController@store');
Route::post('profit_center/update', 'ProfitCenterController@update');
Route::get('profit_center/destroy/{id}/{status}', 'ProfitCenterController@destroy');
Route::post('porfit_center/import', 'ProfitCenterController@import')->name('profitcenter.import');

// Rekenings
Route::get('rekening', 'RekeningController@index');
Route::get('rekening/getdata', 'RekeningController@getData');
Route::get('rekening/fetchdata', 'RekeningController@fetchData')->name('fetchRekening');
Route::post('rekening/store', 'RekeningController@store');
Route::post('rekening/update', 'RekeningController@update');
Route::get('rekening/destroy/{id}/{status}', 'RekeningController@destroy');
Route::post('rekening/import', 'RekeningController@import')->name('rekening.import');

// User
Route::get('user', 'UserController@index');
Route::post('user/login', 'UserController@loginpost')->name('loginpost');
Route::get('user/logout', 'UserController@logout')->name('logout');
Route::post('user/store', 'UserController@store');
Route::post('user/update', 'UserController@update');
Route::get('user/destroy/{id}/{status}', 'UserController@destroy');
Route::get('user/getBagianByCompany/{id}', 'UserController@getBagianByCompany')->name('getBagianByCompany');

// Vendor
Route::get('vendor', 'VendorController@index');
Route::post('vendor/store', 'VendorController@store');
Route::post('vendor/update', 'VendorController@update');
Route::get('vendor/destroy/{id}/{status}', 'VendorController@destroy');
Route::get('vendor/getData', 'VendorController@getData')->name('getVendor');
Route::get('vendor/getdatatableall', 'VendorController@getDataTableAll')->name('getVendorDataTableAll');
Route::post('vendor/import', 'VendorController@import')->name('vendor.import');

// Mater Data Cetak SPP
Route::get('cetak_spp', 'CetakSPPController@index');
Route::post('cetak_spp/store', 'CetakSPPController@store');
Route::post('cetak_spp/update', 'CetakSPPController@update');
Route::get('cetak_spp/destroy/{id}/{status}', 'CetakSPPController@destroy');

Route::post('user/login-validasi/{id}', 'ValidasiController@loginpost')->name('loginvalidasipost');
Route::get('user/validasi_spp/preview/{id}', 'ValidasiController@preview')->name('previewvalidasi');


Route::post('user/login-validasi-sppk/{id}', 'ValidasiKController@loginpost')->name('loginvalidasisppkpost'); //login post validasi sppk
Route::get('user/validasi_sppk/preview/{id}', 'ValidasiKController@preview')->name('previewvalidasisppk'); //preview validasi sppk
Route::get('spp_keuangan/validasi_login/{id}', 'ValidasiKController@login_validasi')->name('loginvalidasisppk'); //halaman login validasi sppk
Route::get('spp_keuangan/validasi_sppk/{id}', 'ValidasiKController@index_sppk')->name('validasisppk'); //halaman validasi sppk

Route::get('spp/master_rekening', 'SppbController@master_rek')->name('mas_rek');
Route::post('spp/master_rekening_tambah', 'SppbController@master_rek_tambah')->name('mas_rek_t');
Route::get('spp/master_rekening_tambah_v2', 'SppbController@master_rek_tambah_pagination')->name('mas_rek_t_v2');
Route::get('spp/master_gl_tambah_v2', 'SppbController@master_gl_pagination')->name('mas_gl_t_v2');
Route::get('spp/get_jumlah_budget/{id}', 'SppbController@get_budget_by_gl_code')->name('jumlah_budget');
Route::get('spp/get_cashflow', 'SppbController@master_cashflow_pagination')->name('mas_cashflow');
Route::get('spp/get_profit_center', 'SppbController@master_cost_profit')->name('mas_profitcenter');
Route::get('spp/get_cost_center', 'SppbController@get_cost_center')->name('mas_costcenter');

Route::get('spp/master_gl', 'SppbController@master_gl')->name('mas_gl');

// Datatable SPP Umum
Route::get('datatable/getDataSppProses', 'SppDatatableController@getDataSppProses')->name('getDataSppProses'); // table todo list
Route::get('datatable/getDataSppDalamProses', 'SppDatatableController@getDataSppDalamProses')->name('getDataSppDalamProses');
Route::get('datatable/getDataSppRevisi', 'SppDatatableController@getDataSppRevisi')->name('getDataSppRevisi');
Route::get('datatable/getDataSppBaru', 'SppDatatableController@getDataSppBaru')->name('getDataSppBaru');
Route::get('datatable/getDataSppSelesai', 'SppDatatableController@getDataSppSelesai')->name('getDataSppSelesai');
Route::get('datatable/getDataSppBatal', 'SppDatatableController@getDataSppBatal')->name('getDataSppBatal');
Route::get('datatable/advanced_search', 'SppDatatableController@advaced_search')->name('advanced_search');

// Datatable SPP Khusus
Route::get('datatable/getDataSppKhususProses', 'SppDatatableController@getDataSppKhususProses')->name('getDataSppKhususProses');
Route::get('datatable/getDataSppKhususDalamProses', 'SppDatatableController@getDataSppKhususDalamProses')->name('getDataSppKhususDalamProses');
Route::get('datatable/getDataSppKhususRevisi', 'SppDatatableController@getDataSppKhususRevisi')->name('getDataSppKhususRevisi');
Route::get('datatable/getDataSppKhususBaru', 'SppDatatableController@getDataSppKhususBaru')->name('getDataSppKhususBaru');
Route::get('datatable/getDataSppKhususSelesai', 'SppDatatableController@getDataSppKhususSelesai')->name('getDataSppKhususSelesai');
Route::get('datatable/getDataSppKhususBatal', 'SppDatatableController@getDataSppKhususBatal')->name('getDataSppKhususBatal');

//email
Route::get('email/{id}', 'EmailController@index');
// Route::get('generate','UserController@generate_token');

//create rekeening based on vendor
Route::get('vendor/createrekening', 'VendorController@storeRekening')->name('create_rek');

// bar chart
Route::post('barchart/divisi', 'BarChartController@getSppbdanSppnTerbayarPerDivisi')->name('barchart.divisi');
Route::post('barchart/regional', 'BarChartController@getSppbdanSppnTerbayarPerRegional')->name('barchart.regional');

// for piechart
Route::post('piechart/spp', 'PieChartSppController@getDataSppTerbayarDanBelumTerbayar')->name('piechart_spp');
Route::get('spp/get-proses', 'PieChartSppController@getStatusProses')->name('getTotalProses');
Route::post('spp/get-keluar-terima', 'PieChartSppController@getPenerimaanPengeluaran')->name('getKeluarTerima');

Route::get('spp/get-customer', 'SppbController@master_customer')->name('master_customer');

// for file
Route::get('doc/{doc}', 'DownloadFileController@readFile');

Route::get('cetak-bukti-kas', 'CetakBuktiKasController@index')->name('admin.cetakbuktikas.index');
Route::get('cetak-bukti-kas/data', 'CetakBuktiKasController@getData')->name('admin.cetakbuktikas.getdata');
Route::get('cetak-bukti-kas/data-by-company/{id}', 'CetakBuktiKasController@getDataByCompany')->name('admin.cetakbuktikas.getDataByCompany');
Route::post('cetak-bukti-kas/store', 'CetakBuktiKasController@store')->name('admin.cetakbuktikas.store');
Route::post('cetak-bukti-kas/update/{id}', 'CetakBuktiKasController@update')->name('admin.cetakbuktikas.update');
Route::delete('cetak-bukti-kas/destroy/{id}', 'CetakBuktiKasController@destroy')->name('admin.cetakbuktikas.destroy');
