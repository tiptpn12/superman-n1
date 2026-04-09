<?php

namespace App\Http\Controllers;

use App\Bagian;
use Illuminate\Http\Request;
use App\Spp;
use App\Helpers\API;
use App\Sppb;
use App\Sppn;
use App\RekamJejak;
use App\User;
use App\Flow;
use App\MasterDevices;
use App\Notifications\FirebaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Shared\Trend\Trend;
use App\Notifications\NewSppNotification;
use Illuminate\Support\Facades\Mail;
use Notification;

class ApiController extends Controller
{
    public function loginApi(Request $request){
        try {
            $username = $request->username;
            $password = $request->password;
            $data = DB::table('master_user')->whereRaw('master_user_name = ?', [$username])
            ->leftJoin('master_hak_akses','master_user.master_hak_akses_id','=','master_hak_akses.master_hak_akses_id')->first();
            // $data = User::whereRaw('master_user_name = ?', [$username])->first();
            if($data){
                $pw = decrypt($data->master_user_password);
                if($password == $pw){
                    return response()->json([
                        'success'   => true,
                        'message'   => 'Login Berhasil',
                        'token'      => $data->api_token,
                        'dataObject'    => $data
                    ]);
                }
                return response()->json([
                    'success'   => false,
                    'message'   => 'Login Gagal'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }
    }
    public function getBagian(){
        $data = Bagian::all();
        return API::createApi($data);
    }
    public function getUser(){
        $data = User::all();
        return API::createApi($data);
    }
    public function getSPPb(){
        $sppb = Sppb::all();
        return response()->json($sppb);
    }
    public function getSPPn(){
        $sppn = Sppn::all();
        return response()->json($sppn);
    }
    public function getDataLonglistSppb(Request $request){
        //dd($request->user);
        $grupID = DB::table('master_hak_akses')->where('master_hak_akses_id', '=', $request->user->master_hak_akses_id)->first();
        // var_dump($grupID);
        // die();
        $dataGrup = DB::table('master_grup_ui')->where('grup_id', '=', $grupID->grup_ui_id)->first();
        // dd($request->user->master_bagian_id);
        // var_dump($dataGrup);
        // die();
        if($grupID->grup_ui_id == 1 || $request->user->master_hak_akses_id == 18){
            $data = DB::table('spp')
            ->where('spp.master_bagian_id',$request->user->master_bagian_id)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 2){
            if ($request->user->master_bagian_id == 111) {
                $spp = Spp::All();
                foreach($spp as $s){
                    $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                        ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                }
                foreach($master_flow as $key => $g){
                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                        $spp_terlewati = $key ;
                    }
                }
            $data = DB::table('spp')
            ->where('spp.sppd_status','!=',100)
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else {
            $spp = Spp::All();
                foreach($spp as $s){
                    $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                        ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                }
                foreach($master_flow as $key => $g){
                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                        $spp_terlewati = $key ;
                    }
                }
            $data = DB::table('spp')
            ->where('spp.sppd_status','!=',100)
            ->where('spp.sppn_id','=',null)
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.master_bagian_id',$request->user->master_bagian_id)
            ->where('spp.sppd_proses','!=',NULL)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('tanggal','desc')
            ->get();
        }
    }else if($grupID->grup_ui_id == 3){
        $spp = Spp::All();
        foreach($spp as $s){
            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
            }
            foreach($master_flow as $key => $g){
                if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                    $spp_terlewati = $key ;
                }
            }
        $data = DB::table('spp')
        ->where('spp.sppd_proses','>=',$spp_terlewati)
        ->where('spp.sppd_proses','!=',NULL)
        ->where('spp.sppd_status','!=',100)
        ->where('spp.sppn_id','=',null)
        ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
        ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
        ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
        ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
        ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
        ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
        ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
        ->orderBy('spp_tanggal','desc')
        ->get();
        }else if($grupID->grup_ui_id == 4){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')->where('spp.sppd_status','!=',100)
            ->where('spp.sppd_proses','=',$spp_terlewati)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 5){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')->where('spp.master_bagian_id','=',$spp_terlewati)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 6){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')->where('spp.master_bagian_id','=',$spp_terlewati)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 7){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('spp.sppn_id','=',null)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'spp.spp_no_dokumen as nomor_dokumen_spp',
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi','spp.spp_no_dokumen'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }
        return API::createApi($data);
    }
    
    public function getDataLonglistSppn(){
        $data = DB::table('spp')->where('sppb_id',NULL)
        ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
        ->select(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppn.sppn_no','spp.sppd_posisi')
        ->get();
        return API::createApi($data);
    }

    public function getDataLonglistSpp(){
        $data = DB::table('spp')->whereNotNull('spp.sppb_id')->whereNotNull('spp.sppn_id')
        ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
        ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
        ->select(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb.sppb_no','spp.sppd_posisi')
        ->get();
        return API::createApi($data);
    }
    public function hitungDataSppb(Request $request){
        if($request->user->master_bagian_id == 111){
            $jumlahSppb = DB::table('spp')
            ->where('sppn_id',NULL)
            ->where('sppd_posisi',$request->user->master_hak_akses_id)
            ->count();
        }else if($request->user->master_bagian_id != 111){
            $jumlahSppb=DB::table('spp')
            ->where('sppn_id',NULL)
            ->where('master_bagian_id','=',$request->user->master_bagian_id)
            ->where('sppd_posisi',$request->user->master_hak_akses_id)
            ->count();
        }
        $jumlahSppn = DB::table('spp')
        ->where('sppd_posisi',$request->sppd_posisi)
        ->where('spp.master_bagian_id',$request->user->master_bagian_id)
        ->where('sppb_id',NULL)->count();
        $jumlahSpp = DB::table('spp')
        ->where('sppd_posisi',$request->sppd_posisi)
        ->where('spp.master_bagian_id',$request->user->master_bagian_id)
        ->whereNotNull('sppb_id')
        ->whereNotNull('sppn_id')->count();
        return response()->json(["hitungSppb"=>$jumlahSppb,
                                "hitungSppn"=>$jumlahSppn,
                                "hitungSpp"=>$jumlahSpp,
                                "cek" => $request->user->master_bagian_id,
                                ], 200);
    }
    public function sendSpp(Request $request){
        try {
            $sppd = Spp::where('spp_id', $request->spp_id)->select('spp_buat','sppd_proses','flow_id','sppd_posisi','sppd_revisi','sppd_status')->first();
            $flow_stop= DB::table('master_flow_detail')->where('master_flow_detail.flow_revisi_stop','=',1)->select('flow_detail_urutan')->first();
            $master_flow = DB::table('master_flow')->where('flow_detail_urutan',$sppd->sppd_posisi)->where('master_flow.flow_id', $sppd->flow_id)->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->select('master_flow_detail.*')->first();
            $id_flow_stop = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id','=',$sppd->flow_id)
            ->where('master_flow_detail.flow_revisi_stop','=',1)
            ->select('master_flow_detail.*')->first();
            $flow_detail_id = DB::table('master_flow_detail')->where('flow_detail_id',$master_flow->flow_detail_id+1)->select('master_flow_detail.*')->first();
            $master_flow_detail_all = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id','=',$sppd->flow_id)
            ->select('master_flow_detail.*')->get();
            $flow_stop = DB::table('master_flow_detail')->where('flow_id','=',$sppd->flow_id)->select('flow_detail_id')->first();
            $sppd_proses = $sppd->sppd_proses+1 ;
            $posisi_selanjutnya = $flow_detail_id->flow_detail_urutan ;
            $revisi_stop = $id_flow_stop->flow_detail_id - $flow_stop->flow_detail_id;
            $posisi_kirim_balik = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id','=',$sppd->flow_id)
            ->select('flow_detail_id')
            ->first();
            if($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $sppd->sppd_posisi == $id_flow_stop->flow_detail_urutan){
                $sppd_proses_revisi = $sppd->sppd_proses-$revisi_stop ;
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_posisi = $sppd->spp_buat;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;  
                $rekam_jejak->spp_id = $request->spp_id;
                $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
                $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            elseif($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $posisi_kirim_balik->flow_detail_id < $id_flow_stop->flow_detail_id ){
                $sppd_proses_revisi = $sppd->sppd_proses-1 ;
                $posisi_selanjutnya_revisi = $master_flow[$sppd->sppd_proses-1]->flow_detail_urutan ;
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_posisi = $posisi_selanjutnya_revisi;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak; 
                $rekam_jejak->spp_id = $request->spp_id;
                $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
                $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            elseif($sppd->sppd_proses == 0 && $sppd->sppd_status == 3){
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $posisi_selanjutnya;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $request->spp_id;
                $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
                $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            elseif($sppd->sppd_revisi > $id_flow_stop->flow_detail_urutan && $akses == $id_flow_stop->flow_detail_urutan  ){
                
                foreach($master_flow_detail_all as $key => $g){
                    if($sppd->sppd_revisi == $g->flow_detail_urutan){
                        $spp_balik_revisi = $key ;
                    }
                    
                }

                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $spp_balik_revisi;
                $sppd->sppd_posisi = $sppd->sppd_revisi;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $request->spp_id;
                $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
                $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            else{
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $posisi_selanjutnya;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $request->spp_id;
                $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
                $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            $flow_proses = DB::table('master_flow_detail')->where('flow_detail_urutan',$sppd->sppd_posisi)->select('flow_detail_id')->first();
            $flow_proses_stop = DB::table('master_flow_detail')->where('master_flow_detail.flow_revisi_stop','=',1)->select('flow_detail_id')->first();
            $sppbID = Spp::where('spp_id', $request->spp_id)->select('sppb_id')->first();
            $sppnID = Spp::where('spp_id', $request->spp_id)->select('sppn_id')->first();
            $sppb_nomor = null;
            $sppn_nomor = null;
            if($sppbID->sppb_id) {
                $getSppb = Sppb::where('sppb_id',$sppbID->sppb_id)
                                    ->select('sppb_id', 'sppb_no','sppb_jenis')
                                    ->first();
                $sppb_nomor = $getSppb->sppb_no;
                $jenis_sppb = $getSppb->sppb_jenis;
            }
            if($sppnID->sppn_id) {
                $getSppn = Sppn::where('sppn_id',$sppnID->sppn_id)
                ->select('sppn_id', 'sppn_no')
                ->first();
                $sppn_nomor = $getSppn->sppn_no;
            }

            $isSppCampuran = false;
            if($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
            $notificationData = [
                'spp_id' => $request->spp_id,
                'username' => $request->user->master_user_name,
                'message' => "Ada SPP Masuk ",
                'sppb_nomor' => $sppb_nomor,
                'sppn_nomor' => $sppn_nomor,
                'isSppCampuran' => $isSppCampuran,
                'jenis_sppb' => $jenis_sppb
            ];
    
            // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
            // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
            if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id-1){
                $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id',$sppd->master_bagian_id)->select('master_user_id')->first();
                $this->pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor ".$sppb_nomor,
                    $request->spp_id,
                    "masuk"
                );
                Notification::send($userNotifable, new NewSppNotification($notificationData));
            }else{
                $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                $this->pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor ".$sppb_nomor,
                    $request->spp_id,
                    "masuk"
                );
                Notification::send($userNotifable, new NewSppNotification($notificationData));
            }
            // return response()->json([
            //     "success"=>false, 
            //     "message"=>"yes", 
            //     'debugObject' => $userNotifable,
            //     'sppd_posisi' => $sppd->sppd_posisi,
            //     'master_bagian_id' => $sppd->master_bagian_id], 200);

            

            



            return response()->json(["success"=>true, "message"=>"Berhasil Dikirim"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success"=>false, "message"=>$th->getMessage()], 200);
        }
    }
    public function revisiSpp(Request $request){
        try {
            $spp = Spp::where('spp_id', $request->spp_id)->select('sppd_proses','flow_id','sppd_posisi','sppd_revisi','sppd_status')->first();
            $master_flow = Flow::where('master_flow.flow_id', $spp->flow_id)->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
            $master_flow_detail = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id','=',$spp->flow_id)
                ->where('master_flow_detail.flow_revisi_stop','=',1)
                ->select('master_flow_detail.*')->first();
            $master_flow_detail_all = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id','=',$spp->flow_id)
                ->select('master_flow_detail.*')->get();
            $posisi_spp = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id','=',$spp->flow_id)
                ->where('master_flow_detail.flow_detail_urutan','=',$spp->sppd_posisi)
                ->select('master_flow_detail.*')
                ->first();
            $posisi_kirim_balik = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id','=',$spp->flow_id)
                ->select('flow_detail_id')
                ->first();
            $id_flow_stop = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id','=',$spp->flow_id)
                ->where('master_flow_detail.flow_revisi_stop','=',1)
                ->select('master_flow_detail.*')->first();
            $flow_stop= DB::table('master_flow_detail')->where('master_flow_detail.flow_revisi_stop','=',1)->select('master_flow_detail.*')->first();
            $revisi_stop = $id_flow_stop->flow_detail_id - $flow_stop->flow_detail_id;
            foreach($master_flow_detail_all as $key => $g){
                if($master_flow_detail->flow_detail_urutan == $g->flow_detail_urutan){
                   $spp_revisi = $key ;
                }
            }
            $flow_proses = DB::table('master_flow_detail')->where('flow_detail_urutan',$spp->sppd_posisi)->select('flow_detail_id')->first();
            $flow_proses_stop = DB::table('master_flow_detail')->where('master_flow_detail.flow_revisi_stop','=',1)->select('flow_detail_id')->first();
            $sppbID = Spp::where('spp_id', $request->spp_id)->select('sppb_id')->first();
            $sppnID = Spp::where('spp_id', $request->spp_id)->select('sppn_id')->first();
            $sppb_nomor = null;
            $sppn_nomor = null;
            if($sppbID->sppb_id) {
                $getSppb = Sppb::where('sppb_id',$sppbID->sppb_id)
                                    ->select('sppb_id', 'sppb_no','sppb_jenis')
                                    ->first();
                $sppb_nomor = $getSppb->sppb_no;
                $jenis_sppb = $getSppb->sppb_jenis;
            }
            if($sppnID->sppn_id) {
                $getSppn = Sppn::where('sppn_id',$sppnID->sppn_id)
                ->select('sppn_id', 'sppn_no')
                ->first();
                $sppn_nomor = $getSppn->sppn_no;
            }

            $isSppCampuran = false;
            if($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                $notificationData = [
                    'spp_id' => $request->spp_id,
                    'username' => $request->user->master_user_name,
                    'message' => "Ada Revisi SPP Masuk ",
                    'sppb_nomor' => $sppb_nomor,
                    'sppn_nomor' => $sppn_nomor,
                    'isSppCampuran' => $isSppCampuran,
                    'jenis_sppb' => $jenis_sppb
                ];
            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $request->spp_id;
            $rekam_jejak->master_user_id = $request->sppd_posisi;
            $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
            $rekam_jejak->master_user_id_tujuan = $request->user->master_hak_akses_id;
            $rekam_jejak->rekam_jejak_revisi = $request->rekam_jejak_revisi;
            $rekam_jejak->rekam_jejak_status = 33;
            $rekam_jejak->save();
            if( $spp->sppd_posisi == $id_flow_stop->flow_detail_urutan){
                $sppd_proses_revisi = $spp->sppd_proses-$revisi_stop ;
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = 0;
                $sppd->sppd_posisi = $sppd->spp_buat;
                $sppd->sppd_status = 3;
                $sppd->save();
            }
            elseif($posisi_spp->flow_detail_id < $id_flow_stop->flow_detail_id ){
                $sppd_proses_revisi = $spp->sppd_proses-1 ;
                $posisi_selanjutnya_revisi = $master_flow[$spp->sppd_proses-1]->flow_detail_urutan ;
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_posisi = $posisi_selanjutnya_revisi;
                $sppd->sppd_status = 3;
                $sppd->save();
            }
            elseif($posisi_spp  ->flow_detail_id > $id_flow_stop->flow_detail_id){
                $sppd = Spp::find($request->spp_id);
                $sppd->sppd_revisi = $request->user->master_hak_akses_id;
                $sppd->sppd_posisi = $master_flow_detail->flow_detail_urutan;
                $sppd->sppd_proses = $spp_revisi;
                $sppd->sppd_status = 3;
                $sppd->save();
            }


            if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id-1){
                $userNotifable = User::where('master_hak_akses_id', $spp->sppd_posisi)->where('master_bagian_id',$sppd->master_bagian_id)->select('master_user_id')->first();
                $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor ".$sppb_nomor,
                    $request->spp_id,
                    "masuk"
                );
                Notification::send($userNotifable, new NewSppNotification($notificationData));
            }else{
                $userNotifable = User::where('master_hak_akses_id', $spp->sppd_posisi)->select('master_user_id')->first();
                $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor ".$sppb_nomor,
                    $request->spp_id,
                    "masuk"
                );
                Notification::send($userNotifable, new NewSppNotification($notificationData));
            }
            return response()->json(["success"=>true, "message"=>"Berhasil Direvisi"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success"=>false, "message"=>$th->getMessage()], 200);
        }
    }
    public function afterRevisi (Request $request){
        try {
            $spp = Spp::where('spp_id', $request->spp_id)->select('sppd_proses','flow_id','sppd_posisi','sppd_revisi','sppd_status')->first();
            $master_flow = Flow::where('master_flow.flow_id', $spp->flow_id)->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();

            $sppd = Spp::find($request->spp_id);
            $sppd->sppd_proses = $spp->sppd_proses-1;
            $sppd->sppd_posisi = $master_flow[$spp->sppd_proses-1]->flow_detail_urutan;
            $sppd->save();
            
            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $request->spp_id;
            $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
            $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
            $rekam_jejak->master_user_id_tujuan = $master_flow[$spp->sppd_proses-1]->flow_detail_urutan;
            $rekam_jejak->rekam_jejak_status = 33;
            $rekam_jejak->save();
            return response()->json(["success"=>true, "message"=>"Berhasil Dikembalikan"], 200);
        } catch (\Throwable $th) {
            return response()->json(["success"=>false, "message"=>$th->getMessage()], 200);
        }
    }
    public function rekamjejak(Request $request){
        try {
            $rekam_jejak = DB::table('rekam_jejak')->where('spp_id','=',$request->spp_id)
            ->leftjoin('master_hak_akses AS asal','master_user_id','=','asal.master_hak_akses_id')
            ->leftjoin('master_hak_akses AS tujuan','master_user_id_tujuan','=','tujuan.master_hak_akses_id')
            ->select('rekam_jejak.rekam_jejak_waktu as waktu','asal.master_hak_akses_keterangan as asal','tujuan.master_hak_akses_keterangan as tujuan',
            'rekam_jejak.rekam_jejak_status as status', 'rekam_jejak.rekam_jejak_revisi')
            ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu','asc')->get();          

            return response()->json(["success"=>true, "message"=>"data berhasil diambil", "data"=>$rekam_jejak],200);
        } catch (\Throwable $th) {
            return response()->json(["success"=>false, "message"=>$th->getMessage()],200);
        }
    }
    public function upload_no_doc(Request $request){
        try {
            $sppd = Spp::where('spp_id', $request->spp_id)->first();
            $sppd->spp_no_dokumen = $request->spp_no_dokumen;
            $sppd->save();

            return response()->json(["success" => true, "message" => "berhasil" , "cek" => $sppd],200);
        } catch (\Throwable $th) {
            return response()->json(["success"=>false, "message"=>$th->getMessage()],200);
        }
    }
    public function accept(Request $request){
        $spp = Spp::where('spp_id', $request->spp_id)->select('sppd_proses','flow_id','sppd_posisi','sppd_revisi','sppd_status')->first();
        // dd($spp->sppd_status );
        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $request->spp_id;
        $rekam_jejak->master_user_id = $request->user->master_hak_akses_id;
        $rekam_jejak->master_user_id_asal = $request->user->master_user_id;
        $rekam_jejak->rekam_jejak_status = 6;
        $rekam_jejak->master_user_id_tujuan = $request->sppd_posisi;
        $rekam_jejak->save();
            
        $sppd = Spp::find($request->spp_id);
        $sppd->sppd_status = 2;
        $sppd->save();           

        return response()->json(["success" => true, "message" => "berhasil"],200);
    }

    public function viewdetail(Request $request){

        $sppbIsiID = DB::table('sppb_isi')
                ->select('sppb_isi_id')
                ->where('sppb_isi.sppb_id',$request->sppb_id)
                ->first();
        //dd($sppbIsiID);
        $dataUraianSppb = DB::table('sppb_uraian')
                ->where('sppb_uraian.sppb_isi_id', '=', $sppbIsiID->sppb_isi_id)    
                ->leftJoin('sppb_isi','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('master_rekening','master_rekening.master_rekening_id','=','sppb_isi.master_kode_vendor_id')
                ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                ->select(
                    'sppb_isi.sppb_id',
                    'sppb_uraian.sppb_isi_id',
                    'master_rekening.master_rekening_kode_sap as sap_isi',
                    'master_gl.master_gl_kode as kode_gl',
                    'master_cost_center.master_cost_center_kode as isi_cost_center',
                    'sppb_uraian.sppb_uraian_uraian as isi_uraian',
                    'sppb_uraian.sppb_uraian_nominal as isi_jumlah'
                )->get();
        return response()->json(["success" => true, "message" => "berhasil","data" => $dataUraianSppb],200);
    }

    public function searchnomorspp(Request $request){
        $grupID = DB::table('master_hak_akses')->where('master_hak_akses_id', '=', $request->user->master_hak_akses_id)->first();
        // var_dump($grupID);
        // die();
        $dataGrup = DB::table('master_grup_ui')->where('grup_id', '=', $grupID->grup_ui_id)->first();
        // var_dump($dataGrup);
        // die();
        if($grupID->grup_ui_id == 1 || $request->user->master_hak_akses_id == 18){
            $data = DB::table('spp')
            ->where('spp.master_bagian_id','=',$request->user->master_bagian_id)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 2){
            if ($request->user->master_bagian_id == 111) {
                $spp = Spp::All();
                foreach($spp as $s){
                    $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                        ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                }
                foreach($master_flow as $key => $g){
                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                        $spp_terlewati = $key ;
                    }
                }
            $data = DB::table('spp')
            ->where('spp.sppd_status','!=',100)
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else {
            $spp = Spp::All();
                foreach($spp as $s){
                    $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                        ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                }
                foreach($master_flow as $key => $g){
                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                        $spp_terlewati = $key ;
                    }
                }
            $data = DB::table('spp')
            ->where('spp.sppd_status','!=',100)
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }
    }else if($grupID->grup_ui_id == 3){
        $spp = Spp::All();
        foreach($spp as $s){
            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
            }
            foreach($master_flow as $key => $g){
                if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                    $spp_terlewati = $key ;
                }
            }
        $data = DB::table('spp')
        ->where('spp.sppd_proses','>=',$spp_terlewati)
        ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
        ->where('spp.sppd_proses','!=',NULL)
        ->where('spp.sppd_status','!=',100)
        ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
        ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
        ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
        ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
        ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
        ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
        ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
        ->orderBy('spp_tanggal','desc')
        ->get();
        }else if($grupID->grup_ui_id == 4){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')->where('spp.sppd_status','!=',100)
            ->where('spp.sppd_proses','=',$spp_terlewati)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 5){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')
            ->where('spp.master_bagian_id','=',$spp_terlewati)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 6){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')
            ->where('spp.master_bagian_id','=',$spp_terlewati)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }else if($grupID->grup_ui_id == 7){
            $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    // dd($master_flow);

                    foreach($master_flow as $key => $g){

                                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                    $spp_terlewati = $key ;
                                    }

                            }
            $data = DB::table('spp')
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('sppb_no','LIKE', '%'.$request->sppb_nomor.'%')
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                'spp_id','sppb.sppb_id as sppb_id',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb.sppb_no',
                'spp.sppd_posisi', 
                'master_hak_akses.master_hak_akses_nama AS status_posisi',
                'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                'sppd_status','sppd_posisi','spp.spp_no_dokumen'
            )
            ->orderBy('spp_tanggal','desc')
            ->get();
        }
        return response()->json(["success" => true, "message" => "berhasil","data" => $data],200);
    }

    public function getNotif(Request $request){
        try {
            $dataNotif = User::find($request->user->master_user_id)->notifications;
            return response()->json([
                "success" => true, 
                "message" => "Berhasil ambil data",
                "notifikasi" => $dataNotif],200); 
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }

        
    }

    public function getCountNotification(Request $request)
    {
        try {
            $dataNotif = User::find($request->user->master_user_id)->unreadNotifications;
            $countNotif = count($dataNotif);
            return response()->json([
                "success" => true, 
                "message" => "Berhasil ambil data",
                "countNotif" => $countNotif],200); 
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }
    }

    public function dataNotifikasi(Request $request){
        try {
            $grupID = DB::table('master_hak_akses')->where('master_hak_akses_id', '=', $request->user->master_hak_akses_id)->first();
            // var_dump($grupID);
            // die();
            $dataGrup = DB::table('master_grup_ui')->where('grup_id', '=', $grupID->grup_ui_id)->first();
            // var_dump($dataGrup);
            // die();
            if($grupID->grup_ui_id == 1 || $request->user->master_hak_akses_id == 18){
                $data = DB::table('spp')
                ->where('spp.master_bagian_id','=',$request->user->master_bagian_id)
                ->where('spp.spp_id',$request->spp_id)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }else if($grupID->grup_ui_id == 2){
                if ($request->user->master_bagian_id == 111) {
                    $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    foreach($master_flow as $key => $g){
                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                            $spp_terlewati = $key ;
                        }
                    }
                $data = DB::table('spp')
                ->where('spp.sppd_status','!=',100)
                ->where('spp.sppd_proses','>=',$spp_terlewati)
                ->where('spp.spp_id',$request->spp_id)
                ->where('spp.sppd_proses','!=',NULL)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }else {
                $spp = Spp::All();
                    foreach($spp as $s){
                        $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                    }
                    foreach($master_flow as $key => $g){
                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                            $spp_terlewati = $key ;
                        }
                    }
                $data = DB::table('spp')
                ->where('spp.sppd_status','!=',100)
                ->where('spp.sppd_proses','>=',$spp_terlewati)
                ->where('spp.spp_id',$request->spp_id)
                ->where('spp.sppd_proses','!=',NULL)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }
        }else if($grupID->grup_ui_id == 3){
            $spp = Spp::All();
            foreach($spp as $s){
                $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                    ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                }
                foreach($master_flow as $key => $g){
                    if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                        $spp_terlewati = $key ;
                    }
                }
            $data = DB::table('spp')
            ->where('spp.sppd_proses','>=',$spp_terlewati)
            ->where('spp.spp_id',$request->spp_id)
            ->where('spp.sppd_proses','!=',NULL)
            ->where('spp.sppd_status','!=',100)
            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
            ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
            ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
            ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
            ->orderBy('spp_tanggal','desc')
            ->first();
            }else if($grupID->grup_ui_id == 4){
                $spp = Spp::All();
                        foreach($spp as $s){
                            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                        }
                        // dd($master_flow);

                        foreach($master_flow as $key => $g){

                                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                        $spp_terlewati = $key ;
                                        }

                                }
                $data = DB::table('spp')->where('spp.sppd_status','!=',100)
                ->where('spp.spp_id',$request->spp_id)
                ->where('spp.sppd_proses','=',$spp_terlewati)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }else if($grupID->grup_ui_id == 5){
                $spp = Spp::All();
                        foreach($spp as $s){
                            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                        }
                        // dd($master_flow);

                        foreach($master_flow as $key => $g){

                                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                        $spp_terlewati = $key ;
                                        }

                                }
                $data = DB::table('spp')->where('spp.master_bagian_id','=',$spp_terlewati)
                ->where('spp.spp_id',$request->spp_id)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }else if($grupID->grup_ui_id == 6){
                $spp = Spp::All();
                        foreach($spp as $s){
                            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                        }
                        // dd($master_flow);

                        foreach($master_flow as $key => $g){

                                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                        $spp_terlewati = $key ;
                                        }

                                }
                $data = DB::table('spp')->where('spp.master_bagian_id','=',$spp_terlewati)
                ->where('spp.spp_id',$request->spp_id)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }else if($grupID->grup_ui_id == 7){
                $spp = Spp::All();
                        foreach($spp as $s){
                            $master_flow = Flow::where('master_flow.flow_id', $s->flow_id)
                                ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
                        }
                        // dd($master_flow);

                        foreach($master_flow as $key => $g){

                                        if($request->user->master_hak_akses_id == $g->flow_detail_urutan){
                                        $spp_terlewati = $key ;
                                        }

                                }
                $data = DB::table('spp')
                ->where('spp.sppd_proses','>=',$spp_terlewati)
                ->where('spp.spp_id',$request->spp_id)
                ->where('spp.sppd_proses','!=',NULL)
                ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->leftJoin('master_sumber_dana','spp.spp_jenis_sumber_dana','=','master_sumber_dana.sumber_dana_id')
                ->leftJoin('master_hak_akses','master_hak_akses.master_hak_akses_id','=','spp.sppd_posisi')
                ->leftJoin('dokumen_pendukung_sppb','dokumen_pendukung_sppb.sppb_id','=','spp.sppb_id')
                ->select(
                    'spp_id','sppb.sppb_id as sppb_id',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    'sppb.sppb_no',
                    'spp.sppd_posisi', 
                    'master_hak_akses.master_hak_akses_nama AS status_posisi',
                    'sppb.sppb_jenis','spp.spp_kabag as spp_kabag','master_sumber_dana.nama_sumber_dana AS sumber_dana',
                    'sppb_kwitansi','sppb_berita_acara','sppb.sppb_kontrak_perjanjian as sppb_kontrak_perjanjian','sppb.sppb_invoice as sppb_invoice',
                    'sppb.sppb_faktur_pajak','sppb.sppb_referensi',
                    'sppb_efaktur','master_bagian.master_bagian_nama AS bagian','sppb.sppb_catatan',
                    'dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama',
                    'sppd_status','sppd_posisi','spp.spp_no_dokumen'
                )
                ->orderBy('spp_tanggal','desc')
                ->first();
            }
            return response()->json([  
                "success" => true, 
                "message" => "berhasil",
                "dataDetail" => $data],200);
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }
    }
    

    public function registerFirebaseDeviceToken(Request $request)
    {
        try {
            $userID = $request->user->master_user_id;
            $deviceToken = $request->device_token;
            if  ($userID || $deviceToken){
                $isRegistered = MasterDevices::where('device_token', '=', $deviceToken)
                                    ->where('master_user_id', '=', $userID)->exists();
                
                if ($isRegistered){
                    return response()->json([
                        "success" => false, 
                        "message" => "device already registered"],200);
                }

                
                $masterDevice = new MasterDevices();
                $masterDevice->master_user_id = $userID;
                $masterDevice->device_token = $deviceToken;
                $masterDevice->save();

                return response()->json([  
                    "success" => true, 
                    "message" => "Device Registered"],200);
            } else {
                return response()->json([
                    "success" => false, 
                    "message" => "Token or User ID not Can't be Empty"],200);
            }

        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }
    }

    // 
    // 
    /**
     * userIdTarget = master_user_id user yang akan dikirim notifikasi (posisi selanjutnya)
     * judulnotifikasi = judul untuk notiifkasi device
     * bodyNotifikasi = isi pesan notifikasi
     * dataID - bisa berupa id spp, sppb, sppn
     * notifikasiTipe = tipe notifikasi, kirim, revisi, dsb
     * 
     */ 
    public static function pushNotificationsToDevice($userIdTarget, $judulNotifikasi, $bodyNotifikasi, $dataID, $notifikasiTipe)
    {
        try {
            $deviceTokenCollection = MasterDevices::select('device_token')
                                ->where('master_user_id', '=', $userIdTarget)
                                ->get();
                                // dd($deviceTokenCollection);
            // $deviceTokenTarget = $deviceTokenCollection->device_token;

            // variabel tes
            $deviceTokenTarget = $deviceTokenCollection->pluck('device_token')->all();
            // $userIdTarget = 137;
            // $dataID = 1;
            // $notifikasiTipe = "masuk";
            // $judulNotifikasi = "Spp Running";
            // $bodyNotifikasi = "Superman running in background";

            $sendFirebase = FirebaseNotification::sendTo(
                $deviceTokenTarget,
                [
                    'title' => $judulNotifikasi,
                    'body' => $bodyNotifikasi,
                ],
                [
                    "userID" => $userIdTarget,
                    "dataID" => $dataID,
                    "dataTipe" => $notifikasiTipe
                ],
                true
            );
            //$sendResult = json_decode($sendFirebase);
            // var_dump($sendFirebase);
            // die();
            if ($sendFirebase->success != 0){
                return response()->json([  
                    "success" => true, 
                    "message" => "Notifikasi Terkirim"],200);
            } else {
                return response()->json([
                    "success" => false, 
                    "message" => "Gagal Kirim Notifikasi ke firebase",
                    "firebaseResult" => $sendFirebase],200);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                "success" => false, 
                "message" => $th->getMessage()],200);
        }
    }

}
