<?php

use App\Http\Controllers\Api\ChartController;
use App\Http\Controllers\Api\PenerimaanPengeluaranController;
use App\Http\Controllers\Api\ProsesController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ApiCostCenterController;
use App\Http\Controllers\Api\KirimSIController; // <-- Tambahkan ini
use App\Http\Controllers\APIPushSPPController;
use App\Http\Controllers\MasterAPIController;
// use Illuminate\Routing\Route;
use App\Http\Controllers\SppdController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [ApiController::class, 'loginApi']);
Route::group(['middleware' => 'authapi'], function () {
    Route::get('getDataLonglistSppb', [ApiController::class, 'getDataLonglistSppb']);
    Route::get('getDataLonglistSppn', [ApiController::class, 'getDataLonglistSppn']);
    Route::get('getDataLonglistSpp', [ApiController::class, 'getDataLonglistSpp']);
    Route::get('getSPPb', [ApiController::class, 'getSPPb']);
    // Route::get('getSPPn',[ApiController::class,'getSPPn']);
    Route::get('getUser', [ApiController::class, 'getUser']);
    Route::get('hitungDataSppb', [ApiController::class, 'hitungDataSppb']);
    Route::post('kirimSpp', [ApiController::class, 'sendSpp']);
    Route::post('revisiSpp', [ApiController::class, 'revisiSpp']);
    Route::post('rekamjejak', [ApiController::class, 'rekamjejak']);
    Route::post('nomordokumen', [ApiController::class, 'upload_no_doc']);
    Route::post('accept', [ApiController::class, 'accept']);
    Route::post('viewdetail', [ApiController::class, 'viewdetail']);
    Route::post('searchnomorspp', [ApiController::class, 'searchnomorspp']);
    Route::post('getNotif', [ApiController::class, 'getNotif']);
    Route::post('dataNotifikasi', [ApiController::class, 'dataNotifikasi']);
    Route::post('afterRevisi', [ApiController::class, 'afterRevisi']);
    Route::get('getCountNotification', [ApiController::class, 'getCountNotification']);


    // Firebase Notification Controller
    Route::post('registerFirebaseDeviceToken', [ApiController::class, 'registerFirebaseDeviceToken']);
    Route::post('pushNotificationsToDevice', [ApiController::class, 'pushNotificationsToDevice']);
});

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();

// });

Route::post('/cost-center', [ApiCostCenterController::class, 'createCostCenter']);
Route::put('/cost-center/{id}', [ApiCostCenterController::class, 'editCostCenter']);
Route::delete('/cost-center/{id}', [ApiCostCenterController::class, 'deleteCostCenter']);

// Master Cost Profit Center
Route::get('/getCostCenter', [ApiCostCenterController::class, 'getCostCenter']);
Route::get('/getProfitCenter', [MasterAPIController::class, 'getProfitCenter']);

// Master SAP
Route::get('/getSapCustomer', [MasterAPIController::class, 'getSapCustomer']);
Route::get('/getSapGL', [MasterAPIController::class, 'getSapGL']);
Route::get('/getSapVendor', [MasterAPIController::class, 'getSapVendor']);

// Master Cashflow
Route::get('/getCashFlow', [MasterAPIController::class, 'getCashFlow']);

// Master Sumber Dana
Route::get('/getSumberDana', [MasterAPIController::class, 'getSumberDana']);

// Get SPP karyawan BPD Superman
Route::post('/getSPP', [MasterAPIController::class, 'getSPP']);
Route::post('/getStatusSPP', [MasterAPIController::class, 'getStatusSPP']);
Route::post('/updateSppApkBpd', [MasterAPIController::class, 'updateSppApkBpd']);

// Get Nomor SPP


// PUSH SPP

Route::get('/getBagianCreateSPP', [MasterAPIController::class, 'getBagianCreateSPP']);
Route::get('/getNomorUrutSPP', [APIPushSPPController::class, 'getNomorUrutSPP']);
Route::post('/createSppb', [APIPushSPPController::class, 'createSppb']);
Route::post('/createSpp', [APIPushSPPController::class, 'createSpp']);
Route::post('/createSppbIsi', [APIPushSPPController::class, 'createSppbIsi']);
Route::post('/createUraian', [APIPushSPPController::class, 'createUraian']);
Route::post('/insertRekeningKaryawan', [APIPushSPPController::class, 'insertRekeningKaryawan']);
Route::post('/insertRekamJejak', [APIPushSPPController::class, 'insertRekamJejak']);
Route::post('/createSPPnSPPB', [APIPushSPPController::class,    'createSPPnSPPB']);

// Chart
Route::get('charts/terima-keluar', [ChartController::class, 'terimaKeluar']);
Route::get('charts/sppb-sppn-terbayar-regional', [ChartController::class, 'getSppbdanSppnTerbayarPerRegional']);
Route::get('charts/companies/sppb', [ChartController::class, 'getSppbAllCompany']);
Route::get('charts/companies/sppn', [ChartController::class, 'getSppnAllCompany']);
Route::get('charts/companies/sppn-sppb', [ChartController::class, 'getSppbSppnAllCompany']);
Route::get('charts/companies/spp-terbayar-blm-terbayar', [ChartController::class, 'getDataSppTerbayarDanBelumTerbayar']);
Route::get('charts/divisi/penerimaan-pengeluaran', [PenerimaanPengeluaranController::class, 'index']);
Route::get('charts/proses', [ProsesController::class, 'index']);

// Route untuk MENERIMA data dari skrip Python
// Dilindungi oleh API Key
Route::get('/spp-amco', [KirimSIController::class, 'fetchAll'])->middleware('apikey');
