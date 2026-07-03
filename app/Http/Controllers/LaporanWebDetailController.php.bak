<?php

namespace App\Http\Controllers;
use DB;
use PDF;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use App\Exports_detail\Core_export;
use Illuminate\Support\Facades\DB as FacadesDB;
use Maatwebsite\Excel\Facades\Excel;

class LaporanWebDetailController extends Controller
{
    function __construct(){
        $this->middleware(function ($request,$next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            $this->bagian = session()->get('bagian');
            $this->hakakses = session()->get('hak_akses');
            //dd($this->user);
            //return $next($request);
            if($this->user == null){
            
                return redirect('login');
                
            }
            else{
                return $next($request);
            }
        });
       
        
    }
    public function index(Request $request){
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];
        $karyawan_no_vendor_sppb_sppb = [];
        $karyawan_no_vendor_sppn_sppn = [];
        $posisi_dinamis = [];
        $grup_ui = session()->get('grup_ui');
    
        $client = new Client();
            $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
            $response=$client->request('GET',$url,[
                'verify' => false,
            ]);
            $karyawan_all = json_decode($response->getBody());
        $jenis_spp = $request->jenis_spp;
        $status_bayar =  $request->status_bayar;
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        // $data_bagian = DB::table('spp')->where('spp.master_bagian_id','=',$bagian)->get();
        //dd($request->rentang_waktu,$jenis_spp);
        if($request->rentang_waktu !== "semua"){
            $rentang_waktu_raw = $request->rentang_waktu;
                $rentang_waktu = explode(" - ",$rentang_waktu_raw);
                $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
                })->all();
            if($jenis_spp == "semua"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)
                    ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                        if($grup_ui != 1){
                            $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                        }else{
                            $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                        }
                        $spp_sppb_sppn = $sppb_sppn->get();
                        foreach($spp_sppb_sppn as $v){
                            $posisi_dinamis[] = DB::table('master_hak_akses')
                            ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                            ->select('master_hak_akses.*')
                            ->first();
                        }
                // ddd($spp_sppb_sppn);
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                // dd($spp_sppb);    
                    if($grup_ui != 1){
                        $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                    }
                    else{
                        $sppb->where('spp.master_bagian_id', '=', $bagian);
                    }
                    $spp_sppb = $sppb->get();
                    foreach($spp_sppb as $v){
                        $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                    }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                    if($grup_ui != 1){
                        $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                    }
                    else{
                        $sppn->where('spp.master_bagian_id', '=', $bagian);
                    }
                    $spp_sppn = $sppn->get();
                    foreach($spp_sppn as $v){
                        $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                    }
                //dd($spp_sppb_sppn, $spp_sppb, $spp_sppn);
            }else if($jenis_spp == "spp_khusus"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }else{
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if( $grup_ui != 1){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();   
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                } 
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            
        }   
        else{
            if($jenis_spp == "semua"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->select('spp.*')
                ->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }else if($jenis_spp == "spp_khusus"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();   
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)->where('spp.sppb_id','=',null)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            else{
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)->where('spp.sppb_id','=',null)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            
        }
        $sum_sppb = DB::table('sppb')->sum('sppb_total');
        $sum_sppn = DB::table('sppn')->sum('sppn_jumlah');
        // $sum_spp = $sum_sppb + $spp_sppn;
        // dd($sum_sppb);
        if($request->status_bayar !== "semua"){
            $datanya_sppb = [];
            foreach($spp_sppb as $s){
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                        ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                        ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                        ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                        ->leftJoin('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                        ->select('spp_id','master_vendor.*','sppb_isi.*','master_cost_center.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','sppb_bayar.*','spp.sppb_id','sppb.sppb_jenis','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                        'sppb_total','spp_status_ob','sppb_uraian_uraian as sppb_uraian2',
                        'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb','sppb_uraian_nominal  as sppb_nominal_satuan',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb','spp.spp_status_bayar')
                        ->where('spp.spp_status_bayar','=',$status_bayar)->get();
            }
            $sppb_sppbisi = [];
            foreach($datanya_sppb as $d => $val1){
                foreach($val1 as $key => $val){
                if($val->sppb_isi_id != null){
                    $sppb_sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                    ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                    ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                    
                    ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                    ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_cash_flow.*')->first();  
                }
                
                }
            }

            $sppb_sppb_bayar =[];
            foreach($datanya_sppb as $k => $v1){
                foreach($v1 as $key => $v){
                    $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppb_bayar.*')->first();
                                    if($v->sppb_jenis == "karyawan"){
                                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                        foreach($Krywn_sppb as $a => $val){
                                            $nama = $val->karyawan_nama;
                                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                return $value->karyawan_nama == $nama;
                                            });
                                        }
                                    
                                        foreach($karyawan_sppb as $b => $v1){
                                            foreach($v1 as $k1 => $v2){
                                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                
                                            }
                                        }
                                    }
                                    else if($v->sppb_jenis == "keuangan"){
                                        if($v->sppb_metode_pembayaran == "karyawan"){
                                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                            foreach($Krywn_sppb as $a => $val){
                                                $nama = $val->karyawan_nama;
                                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                    return $value->karyawan_nama == $nama;
                                                });
                                            }
                                        
                                            foreach($karyawan_sppb as $b => $v1){
                                                foreach($v1 as $k1 => $v2){
                                                    $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                    
                                                }
                                            }
                                        }
                                        else{
                                            $karyawan_no_vendor_sppb_sppb[$k] = null;
                                        }
                                    }
                                    else{
                                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                                    }
                }
            }

            $datanya_sppn = [];
                foreach($spp_sppn as $s){
                        $datanya_sppn[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                                ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                                ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                                ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                                ->leftJoin('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                                ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                                ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                                ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                                ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                                ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                                'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                                'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                                'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                                'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp.spp_status_terima')
                                ->where('spp.spp_status_terima','=',$status_bayar)->get();
                }
                // dd($datanya_sppn);
                $sppn_sppnisi = [];
                foreach($datanya_sppn as $key => $val1){
                    foreach($val1 as $d => $val){
                        if($val->sppn_isi_id !== null){
                            $sppn_sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                            ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                
                        }
                        
                    }
                }
                $sppn_sppn_terima = [];
                foreach($datanya_sppn as $key => $v1){
                    foreach($v1 as $k => $v){
                        $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                        ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                        ->select('master_rekening.*','sppn_terima.*')->first();
                                        
                    }
                }
            $datanya = [];
            foreach($spp_sppb_sppn as $s){
                $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','master_cost_center.*','sppb_uraian.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','master_vendor.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','sppb.sppb_jenis','spp.sppb_id','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_bayar.sppb_id as cek_bukti',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->where('spp.spp_status_bayar','=',$request->status_bayar)->get();
                $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                            ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                            ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                            ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                            'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                            'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                            'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn','sppn_isi.master_kode_vendor_id as rekening_sppn',
                            'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp.spp_status_terima')
                            ->where('spp.spp_status_terima','=',$request->status_bayar)->get();
            }
            $sppbisi = [];
            $sppnisi = [];
            // dd($datanya);
            foreach($datanya as $key => $val1){
                foreach($val1 as $d => $val){
                    if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                        $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*','master_rekening.*','master_cost_center.*','master_gl.*','master_cash_flow.*')->first();  
                    }
                    

                    if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                        $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
                    }
                
                }
            }
            
            $sppb_bayar =[];
            $sppn_terima = [];
            foreach($datanya as $key => $v1){
                foreach($v1 as $k => $v){
                    if(isset($v->sppb_id)){
                        $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                        ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->select('master_rekening.*','sppb_bayar.*')->first();
                        
                        if($v->sppb_jenis == "karyawan"){
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach($Krywn_sppb as $a => $val){
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }
                        
                            foreach($karyawan_sppb as $b => $v1){
                                foreach($v1 as $k1 => $v2){
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        }
                        else if($v->sppb_jenis == "keuangan"){
                            if($v->sppb_metode_pembayaran == "karyawan"){
                                $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                foreach($Krywn_sppb as $a => $val){
                                    $nama = $val->karyawan_nama;
                                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                        return $value->karyawan_nama == $nama;
                                    });
                                }
                            
                                foreach($karyawan_sppb as $b => $v1){
                                    foreach($v1 as $k1 => $v2){
                                        $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                        
                                    }
                                }
                            }
                            else{
                                $karyawan_no_vendor_sppb[$k] = null;
                            }
                        }
                        else{
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    }
                    if(isset($v->sppn_id)){
                        $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                        ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                        ->select('master_rekening.*','sppn_terima.*')->first();
                                        
                    }
                }
            }
        }
        else{
            $datanya_sppb = [];
            foreach($spp_sppb as $s){
                    $datanya_sppb[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','spp.sppb_id','master_cost_center.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','master_bagian_nama','spp_kabag','master_vendor.*','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_uraian_nominal  as sppb_nominal_satuan',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->get();
                            
            }
            // dd($datanya_sppb);
            $sppb_sppbisi = [];
            foreach($datanya_sppb as $d => $val){
                foreach ($val as $key => $val2) {
                    if($val2->sppb_isi_id != null){
                        $sppb_sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val2->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_cash_flow.*','master_vendor.*')->first();  
                    }
                }
               
                
            }
            
            $sppb_sppb_bayar =[];
            foreach($datanya_sppb as $k => $v){
                foreach ($v as $key => $val2) {
                    $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$val2->sppb_id)
                                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppb_bayar.*')->first();
                                    if($val2->sppb_jenis == "karyawan"){
                                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$val2->sppb_id)->select('nama_karyawan.*')->get();
                                        foreach($Krywn_sppb as $a => $val){
                                            $nama = $val->karyawan_nama;
                                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                return $value->karyawan_nama == $nama;
                                            });
                                        }
                                    
                                        foreach($karyawan_sppb as $b => $v1){
                                            foreach($v1 as $k1 => $v2){
                                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                
                                            }
                                        }
                                    }
                                    else if($val2->sppb_jenis == "keuangan"){
                                        if($v->sppb_metode_pembayaran == "karyawan"){
                                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$val2->sppb_id)->select('nama_karyawan.*')->get();
                                            foreach($Krywn_sppb as $a => $val){
                                                $nama = $val->karyawan_nama;
                                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                    return $value->karyawan_nama == $nama;
                                                });
                                            }
                                        
                                            foreach($karyawan_sppb as $b => $v1){
                                                foreach($v1 as $k1 => $v2){
                                                    $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                    
                                                }
                                            }
                                        }
                                        else{
                                            $karyawan_no_vendor_sppb_sppb[$k] = null;
                                        }
                                    }
                                    else{
                                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                                    }
                }
            }

            $datanya_sppn = [];
                foreach($spp_sppn as $s){
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                        
                        ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                        ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                        ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                        ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                        ->leftJoin('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                        ->select('spp_id','spp.*','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob','sppn_isi.master_gl_id as gl_sppn',
                        'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                        ->get();
                }
                // dd($datanya_sppn);
        
                $sppn_sppnisi = [];
                foreach($datanya_sppn as $d => $val1){
                    foreach($val1 as $key => $val){
                        $sppn_sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
            
            
                     }
                }

                $sppn_sppn_terima = [];
                        foreach($datanya_sppn as $k => $v1){
                            foreach($v1 as $key => $v){
            
                                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                ->select('master_rekening.*','sppn_terima.*')->first();
                                
                            }
                        }

            $datanya = [];
            foreach($spp_sppb_sppn as $s){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','master_cost_center.*','sppb_uraian.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','master_vendor.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','sppb.sppb_jenis','spp.sppb_id','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_bayar.sppb_id as cek_bukti',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->get();
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                            ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                            ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                            ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn.sppn_jenis','sppn_isi.sppn_isi_id','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                            'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob','sppn_isi.master_gl_id as gl_sppn','sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn',
                            'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn','spp.*',
                            'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                            ->get();
            }
            
                
            $sppbisi = [];
            $sppnisi = [];
            foreach($datanya as $key => $val1){
                foreach($val1 as $d => $val){
                    if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                        $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*','master_rekening.*','master_cost_center.*','master_gl.*','master_cash_flow.*')->first();  
                    }
                
                    

                    if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                        $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
                    }
                
                }
            }

            $sppb_bayar =[];
            $sppn_terima = [];
            foreach($datanya as $k => $v){
                if(isset($v->sppb_id)){
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                    ->select('master_rekening.*','sppb_bayar.*')->first();
                    if($v->sppb_jenis == "karyawan"){
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                        // dd($Krywn_sppb);
                        foreach($Krywn_sppb as $a => $val){
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                            
                        }
                        foreach($karyawan_sppb as $b => $v1){
                            foreach($v1 as $k1 => $v2){
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                
                            }
                        }
                    }
                    else if($v->sppb_jenis == "keuangan"){
                        if($v->sppb_metode_pembayaran == "karyawan"){
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach($Krywn_sppb as $a => $val){
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }   
                        
                            foreach($karyawan_sppb as $b => $v1){
                                foreach($v1 as $k1 => $v2){
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        }
                        else{
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    }
                    else{
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if(isset($v->sppn_id)){
                $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                    ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppn_terima.*')->first();
                                    
                }
            }
        }

        $data = [];
        // dd($datanya);
        foreach($datanya as $key => $d1){
            foreach($d1 as $key1 => $d){
        // dd($datanya);

                if(isset($d->spp_id)){
                    $data[] = $d;
                    }
            }
        }
        $data_sppb = [];
        foreach($datanya_sppb as $d){
            foreach($d as $val){
                if(isset($val->spp_id)){
                    $data_sppb[] = $val;
                        }
            }
        }
        $data_sppn = [];
        foreach($datanya_sppn as $d1){
            foreach($d1 as $d){
                if(isset($d->spp_id)){
                    $data_sppn[] = $d;
                }
            }
        }
        $rentang_waktu = $request->rentang_waktu; 
        // dd($datanya);
        //dd($data_sppb,$data_sppn,$data);

        $posisi_dinamis_sppb = [];
        $posisi_dinamis_sppn = [];
        $posisi_dinamis_sppb_sppn = [];
        foreach($data as $d){
            $posisi_dinamis_sppb_sppn[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }
        foreach($data_sppn as $d){
            $posisi_dinamis_sppn[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }
        foreach($data_sppb as $d){
            $posisi_dinamis_sppb[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }


        //dd($posisi_dinamis);
        return view ('page.laporan.laporan_web_detail', compact ('posisi_dinamis','posisi_dinamis_sppb','posisi_dinamis_sppn','posisi_dinamis_sppb_sppn','data','data_sppb','karyawan_no_vendor_sppn_sppn','karyawan_no_vendor_sppb_sppb','data_sppn','sppb_sppbisi','sppn_sppnisi','sppb_sppb_bayar','sppn_sppn_terima','sppbisi','sppnisi','sppb_bayar','sppn_terima','karyawan_no_vendor_sppb','karyawan_no_vendor_sppn','rentang_waktu','sum_sppb','sum_sppb'));
         
    }

    public function export_pdf(Request $request){
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];
        $karyawan_no_vendor_sppb_sppb = [];
        $karyawan_no_vendor_sppn_sppn = [];
        $posisi_dinamis = [];
        $grup_ui = session()->get('grup_ui');
    
        $client = new Client();
            $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
            $response=$client->request('GET',$url,[
                'verify' => false,
            ]);
            $karyawan_all = json_decode($response->getBody());
        $jenis_spp = $request->jenis_spp;
        $status_bayar =  $request->status_bayar;
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        // $data_bagian = DB::table('spp')->where('spp.master_bagian_id','=',$bagian)->get();
        //dd($request->rentang_waktu,$jenis_spp);
        if($request->rentang_waktu !== "semua"){
            $rentang_waktu_raw = $request->rentang_waktu;
                $rentang_waktu = explode(" - ",$rentang_waktu_raw);
                $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
                })->all();
            if($jenis_spp == "semua"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)
                    ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                        if($grup_ui != 1){
                            $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                        }else{
                            $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                        }
                        $spp_sppb_sppn = $sppb_sppn->get();
                        foreach($spp_sppb_sppn as $v){
                            $posisi_dinamis[] = DB::table('master_hak_akses')
                            ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                            ->select('master_hak_akses.*')
                            ->first();
                        }
                // ddd($spp_sppb_sppn);
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                // dd($spp_sppb);    
                    if($grup_ui != 1){
                        $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                    }
                    else{
                        $sppb->where('spp.master_bagian_id', '=', $bagian);
                    }
                    $spp_sppb = $sppb->get();
                    foreach($spp_sppb as $v){
                        $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                    }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                    if($grup_ui != 1){
                        $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                    }
                    else{
                        $sppn->where('spp.master_bagian_id', '=', $bagian);
                    }
                    $spp_sppn = $sppn->get();
                    foreach($spp_sppn as $v){
                        $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                    }
                // dd($spp_sppb_sppn);
            }else if($jenis_spp == "spp_khusus"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }else{
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();   
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                } 
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            
        }   
        else{
            if($jenis_spp == "semua"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','=',null)->select('spp.*')
                ->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }else if($jenis_spp == "spp_khusus"){
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();   
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','=',2)->where('spp.master_bagian_id','=',$bagian)->where('spp.sppb_id','=',null)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            else{
                $sppb_sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppb_id','!=',null)->where('spp.sppn_id','!=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }else{
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb_sppn = $sppb_sppn->get();
                foreach($spp_sppb_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppb=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)
                ->where('spp.sppn_id','=',null)->select('spp.*');
                if($grup_ui != 1 ){
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppb = $sppb->get();
                foreach($spp_sppb as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
                $sppn=DB::table('spp')->where('spp.spp_status_ob','!=',2)->where('spp.master_bagian_id','!=',2)->where('spp.master_bagian_id','=',$bagian)->where('spp.sppb_id','=',null)
                ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal','desc');
                if($grup_ui != 1 ){
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                }
                else{
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $spp_sppn = $sppn->get();
                foreach($spp_sppn as $v){
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                    ->where('master_hak_akses.master_hak_akses_id','=',$v->sppd_posisi)
                    ->select('master_hak_akses.*')
                    ->first();
                }
            }
            
        }
        $sum_sppb = DB::table('sppb')->sum('sppb_total');
        $sum_sppn = DB::table('sppn')->sum('sppn_jumlah');
        // $sum_spp = $sum_sppb + $spp_sppn;
        // dd($sum_sppb);
        if($request->status_bayar !== "semua"){
            $datanya_sppb = [];
            foreach($spp_sppb as $s){
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                        ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                        ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                        ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                        ->leftJoin('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                        ->select('spp_id','master_vendor.*','sppb_isi.*','master_cost_center.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','sppb_bayar.*','spp.sppb_id','sppb.sppb_jenis','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                        'sppb_total','spp_status_ob','sppb_uraian_uraian as sppb_uraian2',
                        'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb','sppb_uraian_nominal  as sppb_nominal_satuan',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb','spp.spp_status_bayar')
                        ->where('spp.spp_status_bayar','=',$status_bayar)->get();
            }
            $sppb_sppbisi = [];
            foreach($datanya_sppb as $d => $val1){
                foreach($val1 as $key => $val){
                if($val->sppb_isi_id != null){
                    $sppb_sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                    ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                    ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                    
                    ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                    ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_cash_flow.*')->first();  
                }
                
                }
            }

            $sppb_sppb_bayar =[];
            foreach($datanya_sppb as $k => $v1){
                foreach($v1 as $key => $v){
                    $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppb_bayar.*')->first();
                                    if($v->sppb_jenis == "karyawan"){
                                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                        foreach($Krywn_sppb as $a => $val){
                                            $nama = $val->karyawan_nama;
                                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                return $value->karyawan_nama == $nama;
                                            });
                                        }
                                    
                                        foreach($karyawan_sppb as $b => $v1){
                                            foreach($v1 as $k1 => $v2){
                                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                
                                            }
                                        }
                                    }
                                    else if($v->sppb_jenis == "keuangan"){
                                        if($v->sppb_metode_pembayaran == "karyawan"){
                                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                            foreach($Krywn_sppb as $a => $val){
                                                $nama = $val->karyawan_nama;
                                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                    return $value->karyawan_nama == $nama;
                                                });
                                            }
                                        
                                            foreach($karyawan_sppb as $b => $v1){
                                                foreach($v1 as $k1 => $v2){
                                                    $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                    
                                                }
                                            }
                                        }
                                        else{
                                            $karyawan_no_vendor_sppb_sppb[$k] = null;
                                        }
                                    }
                                    else{
                                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                                    }
                }
            }

            $datanya_sppn = [];
                foreach($spp_sppn as $s){
                        $datanya_sppn[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                                ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                                ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                                ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                                ->leftJoin('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                                ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                                ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                                ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                                ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                                ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                                'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                                'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                                'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                                'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp.spp_status_terima')
                                ->where('spp.spp_status_terima','=',$status_bayar)->get();
                }
                // dd($datanya_sppn);
                $sppn_sppnisi = [];
                foreach($datanya_sppn as $key => $val1){
                    foreach($val1 as $d => $val){
                        if($val->sppn_isi_id !== null){
                            $sppn_sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                            ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                
                        }
                        
                    }
                }
                $sppn_sppn_terima = [];
                foreach($datanya_sppn as $key => $v1){
                    foreach($v1 as $k => $v){
                        $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                        ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                        ->select('master_rekening.*','sppn_terima.*')->first();
                                        
                    }
                }
            $datanya = [];
            foreach($spp_sppb_sppn as $s){
                $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','master_cost_center.*','sppb_uraian.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','master_vendor.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','sppb.sppb_jenis','spp.sppb_id','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_bayar.sppb_id as cek_bukti',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->where('spp.spp_status_bayar','=',$request->status_bayar)->get();
                $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                            ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                            ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                            ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                            'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                            'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                            'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn','sppn_isi.master_kode_vendor_id as rekening_sppn',
                            'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp.spp_status_terima')
                            ->where('spp.spp_status_terima','=',$request->status_bayar)->get();
            }
            $sppbisi = [];
            $sppnisi = [];
            // dd($datanya);
            foreach($datanya as $key => $val1){
                foreach($val1 as $d => $val){
                    if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                        $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*','master_rekening.*','master_cost_center.*','master_gl.*','master_cash_flow.*')->first();  
                    }
                    

                    if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                        $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
                    }
                
                }
            }
            
            $sppb_bayar =[];
            $sppn_terima = [];
            foreach($datanya as $key => $v1){
                foreach($v1 as $k => $v){
                    if(isset($v->sppb_id)){
                        $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                        ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->select('master_rekening.*','sppb_bayar.*')->first();
                        
                        if($v->sppb_jenis == "karyawan"){
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach($Krywn_sppb as $a => $val){
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }
                        
                            foreach($karyawan_sppb as $b => $v1){
                                foreach($v1 as $k1 => $v2){
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        }
                        else if($v->sppb_jenis == "keuangan"){
                            if($v->sppb_metode_pembayaran == "karyawan"){
                                $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                                foreach($Krywn_sppb as $a => $val){
                                    $nama = $val->karyawan_nama;
                                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                        return $value->karyawan_nama == $nama;
                                    });
                                }
                            
                                foreach($karyawan_sppb as $b => $v1){
                                    foreach($v1 as $k1 => $v2){
                                        $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                        
                                    }
                                }
                            }
                            else{
                                $karyawan_no_vendor_sppb[$k] = null;
                            }
                        }
                        else{
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    }
                    if(isset($v->sppn_id)){
                        $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                        ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                        ->select('master_rekening.*','sppn_terima.*')->first();
                                        
                    }
                }
            }
        }
        else{
            $datanya_sppb = [];
            foreach($spp_sppb as $s){
                    $datanya_sppb[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','spp.sppb_id','master_cost_center.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','master_bagian_nama','spp_kabag','master_vendor.*','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_uraian_nominal  as sppb_nominal_satuan',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->get();
                            
            }
            // dd($datanya_sppb);
            $sppb_sppbisi = [];
            foreach($datanya_sppb as $d => $val){
                foreach ($val as $key => $val2) {
                    if($val2->sppb_isi_id != null){
                        $sppb_sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val2->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_cash_flow.*','master_vendor.*')->first();  
                    }
                }
               
                
            }
            
            $sppb_sppb_bayar =[];
            foreach($datanya_sppb as $k => $v){
                foreach ($v as $key => $val2) {
                    $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$val2->sppb_id)
                                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppb_bayar.*')->first();
                                    if($val2->sppb_jenis == "karyawan"){
                                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$val2->sppb_id)->select('nama_karyawan.*')->get();
                                        foreach($Krywn_sppb as $a => $val){
                                            $nama = $val->karyawan_nama;
                                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                return $value->karyawan_nama == $nama;
                                            });
                                        }
                                    
                                        foreach($karyawan_sppb as $b => $v1){
                                            foreach($v1 as $k1 => $v2){
                                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                
                                            }
                                        }
                                    }
                                    else if($val2->sppb_jenis == "keuangan"){
                                        if($v->sppb_metode_pembayaran == "karyawan"){
                                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$val2->sppb_id)->select('nama_karyawan.*')->get();
                                            foreach($Krywn_sppb as $a => $val){
                                                $nama = $val->karyawan_nama;
                                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                                    return $value->karyawan_nama == $nama;
                                                });
                                            }
                                        
                                            foreach($karyawan_sppb as $b => $v1){
                                                foreach($v1 as $k1 => $v2){
                                                    $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                                                    
                                                }
                                            }
                                        }
                                        else{
                                            $karyawan_no_vendor_sppb_sppb[$k] = null;
                                        }
                                    }
                                    else{
                                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                                    }
                }
            }

            $datanya_sppn = [];
                foreach($spp_sppn as $s){
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                        
                        ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                        ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                        ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                        ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                        ->leftJoin('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                        ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                        ->select('spp_id','spp.*','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob','sppn_isi.master_gl_id as gl_sppn',
                        'sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                        ->get();
                }
                // dd($datanya_sppn);
        
                $sppn_sppnisi = [];
                foreach($datanya_sppn as $d => $val1){
                    foreach($val1 as $key => $val){
                        $sppn_sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
            
            
                     }
                }

                $sppn_sppn_terima = [];
                        foreach($datanya_sppn as $k => $v1){
                            foreach($v1 as $key => $v){
            
                                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                ->select('master_rekening.*','sppn_terima.*')->first();
                                
                            }
                        }

            $datanya = [];
            foreach($spp_sppb_sppn as $s){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')
                            ->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppb_bayar','sppb.sppb_id','=','sppb_bayar.sppb_id')
                            ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppb_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','master_cost_center.*','sppb_uraian.*','master_profit_center.*','master_gl.*','master_rekening.*','master_cash_flow.*','master_vendor.*','sppb_bayar.*','sppb.sppb_jenis','sppb_isi.*','sppb.sppb_jenis','spp.sppb_id','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob','sppb_uraian_uraian  as sppb_uraian2','sppb_bayar.sppb_id as cek_bukti',
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb','spp.*',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                            ->get();
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)
                            ->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')
                            ->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                            ->leftJoin('sppn_terima','sppn.sppn_id','=','sppn_terima.sppn_id')
                            ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                            ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                            ->leftJoin('master_vendor','sppn_isi.master_kode_vendor_id','=','master_vendor.master_vendor_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','sppn_isi.*','sppn_uraian.*','sppn_terima.*','master_cost_center.*','master_rekening.*','master_profit_center.*','master_gl.*','master_vendor.*','master_cash_flow.*','sppn.sppn_jenis','sppn_isi.sppn_isi_id','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                            'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob','sppn_isi.master_gl_id as gl_sppn','sppn_uraian_uraian as sppn_uraian2','sppn_isi.master_kode_vendor_id as rekening_sppn',
                            'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn','spp.*',
                            'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                            ->get();
            }
            
                
            $sppbisi = [];
            $sppnisi = [];
            foreach($datanya as $key => $val1){
                foreach($val1 as $d => $val){
                    if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                        $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                        ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*','master_rekening.*','master_cost_center.*','master_gl.*','master_cash_flow.*')->first();  
                    }
                
                    

                    if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                        $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                        ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                        ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                        ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                        // dd($sppnisi);
                    }
                
                }
            }

            $sppb_bayar =[];
            $sppn_terima = [];
            foreach($datanya as $k => $v){
                if(isset($v->sppb_id)){
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$v->sppb_id)
                    ->join('master_rekening','sppb_bayar.master_rekening_id','=','master_rekening.master_rekening_id')
                    ->select('master_rekening.*','sppb_bayar.*')->first();
                    if($v->sppb_jenis == "karyawan"){
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                        // dd($Krywn_sppb);
                        foreach($Krywn_sppb as $a => $val){
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                            
                        }
                        foreach($karyawan_sppb as $b => $v1){
                            foreach($v1 as $k1 => $v2){
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                
                            }
                        }
                    }
                    else if($v->sppb_jenis == "keuangan"){
                        if($v->sppb_metode_pembayaran == "karyawan"){
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach($Krywn_sppb as $a => $val){
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }   
                        
                            foreach($karyawan_sppb as $b => $v1){
                                foreach($v1 as $k1 => $v2){
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        }
                        else{
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    }
                    else{
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if(isset($v->sppn_id)){
                $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                    ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                    ->select('master_rekening.*','sppn_terima.*')->first();
                                    
                }
            }
        }

        $data = [];
        // dd($datanya);
        foreach($datanya as $key => $d1){
            foreach($d1 as $key1 => $d){
        // dd($datanya);

                if(isset($d->spp_id)){
                    $data[] = $d;
                    }
            }
        }
        $data_sppb = [];
        foreach($datanya_sppb as $d){
            foreach($d as $val){
                if(isset($val->spp_id)){
                    $data_sppb[] = $val;
                        }
            }
        }
        $data_sppn = [];
        foreach($datanya_sppn as $d1){
            foreach($d1 as $d){
                if(isset($d->spp_id)){
                    $data_sppn[] = $d;
                }
            }
        }
        $rentang_waktu = $request->rentang_waktu; 
        // dd($datanya);
        //dd($data_sppb,$data_sppn,$data);

        $posisi_dinamis_sppb = [];
        $posisi_dinamis_sppn = [];
        $posisi_dinamis_sppb_sppn = [];
        foreach($data as $d){
            $posisi_dinamis_sppb_sppn[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }
        foreach($data_sppn as $d){
            $posisi_dinamis_sppn[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }
        foreach($data_sppb as $d){
            $posisi_dinamis_sppb[] = DB::table('master_hak_akses')
            ->where('master_hak_akses.master_hak_akses_id','=',$d->sppd_posisi)
            ->select('master_hak_akses.*')
            ->first();
        }


        if ($request->export_tipe == 2) {
            return Excel::download(new Core_export($request->rentang_waktu, $request->status_bayar, $request->jenis_spp,$request->c_spp,$request->c_sppb,$request->c_sppn), 'Laporan.xlsx');
        }else{
            //dd($data_sppb,$data_sppn,$data_sppb);
            $pdf = PDF::loadView('page.laporan.laporan_pdf_export_detail', compact('data','posisi_dinamis_sppb','posisi_dinamis_sppn','posisi_dinamis_sppb_sppn','karyawan_no_vendor_sppb_sppb','karyawan_no_vendor_sppn_sppn','data_sppb','data_sppn','sppb_sppbisi','sppn_sppnisi','sppb_sppb_bayar','sppn_sppn_terima','sppbisi','sppnisi','sppb_bayar','sppn_terima','rentang_waktu','karyawan_no_vendor_sppb','karyawan_no_vendor_sppn'))->setPaper('a4', 'landscape');
            return $pdf->download('laporan_'.date('Y-m-d_H-i-s').'.pdf');
        }
        // dd($data_sppb,$data_sppn,$data_sppb);
        // return view('page.laporan.laporan_pdf_export', compact('data','data_sppb','data_sppn','sppb_sppbisi','sppn_sppnisi','sppb_sppb_bayar','sppn_sppn_terima','sppbisi','sppnisi','sppb_bayar','sppn_terima','rentang_waktu'));
        
        
    }
    public function export(Request $request){
            
        return Excel::download(new Core_export($request->rentang_waktu, $request->status_bayar, $request->jenis_spp,$request->c_spp,$request->c_sppb,$request->c_sppn), 'Laporan.xlsx');
    }
    
    public function handle_error(){
        return view('page.laporan.handle_error');
        
    }
}
