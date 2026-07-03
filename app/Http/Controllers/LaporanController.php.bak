<?php

namespace App\Http\Controllers;
use App\Spp;
use App\Sppb;
use App\Sppn;
use App\SppbBayar;
use App\SppnTerima;
use App\SppProses;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LaporanController extends Controller
{
    function __construct(){
        $this->middleware(function ($request,$next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
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
    public function index(){
        $client = new Client();
            // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
            // $response=$client->request('GET',$url,[
            //     'verify' => false,
            // ]);
            // $karyawan_all = json_decode($response->getBody());
            $karyawan_all = null;
       
        $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.spp_status_ob','!=',2)
                ->select('spp.*')->get();
                
        $count_sppb = []; 
        $count_sppn = [];  
        $count_spp = [];  
        foreach($spp as $s){
            if($s->sppb_id !== null && $s->sppn_id == null){
                $count_sppb[] = $s;  
            }
            if($s->sppb_id == null && $s->sppn_id == !null){
                $count_sppn[] = $s;      

            }
            if($s->sppb_id !== null && $s->sppn_id !== null){
                $count_spp[] = $s;    
            }
        }
        $c_sppb = count($count_sppb);
        $c_sppn = count($count_sppn);
        $c_spp = count($count_spp);

        
        $data = [];
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];

        foreach($spp as $s){
            if(isset($s->sppb_id)){
                $data[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->select('spp_id','spp.spp_status_bayar','sppb.sppb_metode_pembayaran','spp.sppb_id','sppb.sppb_jenis','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                'sppb_total','spp_status_ob',DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                'sppb_isi.master_cost_center_id as cost_center_sppb',
                'sppb_isi.master_cash_flow_id as cash_flow_sppb','spp_tanggal')
                ->first();
            }
            if(isset($s->sppn_id)){
                $data[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                ->select('spp_id','spp.spp_status_terima','sppn.sppn_metode_pembayaran','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),'sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp_tanggal')
                ->first();
            }
            
        }
        $datas = [];
        $data = collect($data)->sortByDesc('spp_tanggal')->reverse()->toArray();
        foreach($data as $d){
            $datas[]=$d;
        }
        //dd($s);
        $sppbisi = [];
        $sppnisi = [];
        foreach($data as $d => $val){
            if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
            }


            if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                // dd($sppnisi);
            }
            
        }

        $sppb_bayar =[];
        $sppn_terima = [];
        foreach($datas as $k => $v){
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
                    if(isset($karyawan_sppb)){
                    
                        foreach($karyawan_sppb as $b => $v1){
                            foreach($v1 as $k1 => $v2){
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                
                            }
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
                        if(isset($karyawan_sppb)){
                            foreach($karyawan_sppb as $b => $v1){
                                foreach($v1 as $k1 => $v2){
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
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
        $data = $datas;
        // dd($sppnisi);
        //dd($data,$sppb_bayar,$karyawan_sppb,$karyawan_no_vendor_sppb);
        //dd($data,$karyawan_no_vendor_sppb,$karyawan_no_vendor_sppn,$data,$sppbisi,$sppnisi);
        Log::info('Data akhir', [$data]);
        return view ('page.laporan.laporan', compact ('data','sppbisi','sppnisi','sppb_bayar','sppn_terima','karyawan_no_vendor_sppb','karyawan_no_vendor_sppn','c_sppb','c_sppn','c_spp'));
    }

    public function advanced_search(Request $request){
        $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response=$client->request('GET',$url,[
        //     'verify' => false,
        // ]);
        //$karyawan_all = json_decode($response->getBody());
        $karyawan_all = null;
   
        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ",$rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
        })->all();
        $jenis_spp = $request->jenis_spp;
        $jenis_report = $request->jenis_report;
        $export_tipe = $request->export_tipe;
        //dd($export_tipe);
        if($jenis_spp == "semua"){
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }
        else if($jenis_spp == "spp_biasa"){
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.master_bagian_id','!=',2)->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }
        else{
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.master_bagian_id','=',2)->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }


        if($jenis_report == "detail"){
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }
        else if($jenis_report == "simple"){
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.master_bagian_id','!=',2)->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }
        else{
            if($request->rentang_waktu !== null){
                $spp=DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank','=',1)->join('spp','spp_proses.spp_id','=','spp.spp_id')->where('spp.master_bagian_id','=',2)->where('spp.spp_status_ob','!=',2)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"),$rentang_waktu)
                ->select('spp.*')->get();
            }
        }

        //$spp = [];
        $count_sppb = [];
        $count_sppn = [];
        $count_spp = [];
        foreach($spp as $s){
            if($s->sppb_id !== null && $s->sppn_id == null){
                $count_sppb[] = $s;
            }
            if($s->sppb_id == null && $s->sppn_id == !null){
                $count_sppn[] = $s;

            }
            if($s->sppb_id !== null && $s->sppn_id !== null){
                $count_spp[] = $s;
            }
        }
        $c_sppb = count($count_sppb);
        $c_sppn = count($count_sppn);
        $c_spp = count($count_spp);
        $status_bayar = $request->status_bayar;
       // $datanya = [];
       $karyawan_no_vendor_sppb = [];
       $karyawan_no_vendor_sppn = [];

        if($request->status_bayar !== "semua"){
             foreach($spp as $s){
                if(isset($s->sppb_id)){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                    ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                    ->select('spp_id','sppb.sppb_jenis','spp.sppb_id','spp.spp_status_lunas','spp.spp_status_bayar','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),'sppb_no','sppb_tanggal',
                    'sppb_total','spp_status_ob',DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                    'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                    'sppb_isi.master_cost_center_id as cost_center_sppb',
                    'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                    ->where('spp.spp_status_bayar','=',$status_bayar)->first();
                }
                if(isset($s->sppn_id)){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                    ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                    ->select('spp_id','spp.spp_status_lunas','sppn.sppn_jenis','spp.spp_status_terima','sppn_isi.sppn_isi_id','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),
                    'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                    DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),'sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                    'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                    'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                    ->where('spp.spp_status_terima','=',$status_bayar)->first();
                }
             }
        }

        else{
            foreach($spp as $s){
                if(isset ($s->sppb_id)){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                    ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                    ->select('spp_id','spp.sppb_id','sppb.sppb_jenis','spp.spp_status_lunas','spp.spp_status_bayar','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),'sppb_no','sppb_tanggal',
                    'sppb_total','spp_status_ob',DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                    'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                    'sppb_isi.master_cost_center_id as cost_center_sppb',
                    'sppb_isi.master_cash_flow_id as cash_flow_sppb')
                    ->first();
                }
                if(isset($s->sppn_id)){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                    ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                    ->select('spp_id','spp.spp_status_lunas','sppn.sppn_jenis','spp.spp_status_terima','sppn_isi.sppn_isi_id','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),
                    'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',
                    DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),'sppn_isi.master_kode_vendor_id as rekening_sppn','sppn_isi.master_gl_id as gl_sppn',
                    'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                    'sppn_isi.master_cash_flow_id as cash_flow_sppn')
                    ->first();
                }
            }
        }
        if(isset($datanya)){
            $data = [];
            foreach($datanya as $d){
                if($d->spp_id){
                    $data[] = $d;
                        }
            }
        }
       
       
        //dd($datanya);
        // dd($data, $request->status_bayar);
        if(isset($data)){
            $sppbisi = [];
            $sppnisi = [];
            foreach($data as $d => $val){
                if(isset($val->sppb_isi_id) && $val->sppb_isi_id != null){
                    $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id','=',$val->sppb_isi_id)
                    ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                    ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
                    ->leftJoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                }


                if(isset($val->sppn_isi_id) && $val->sppn_isi_id != null){
                    $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$val->sppn_id)
                    ->leftJoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
                    ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
                    ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->first();
                    // dd($sppnisi);
                }

            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach($data as $k => $v){
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
                        if(isset($karyawan_sppb)){

                        foreach($karyawan_sppb as $b => $v1){
                            foreach($v1 as $k1 => $v2){
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;

                            }
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
                            if(isset($karyawan_sppb)){

                                foreach($karyawan_sppb as $b => $v1){
                                    foreach($v1 as $k1 => $v2){
                                        $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;

                                    }
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
        else{
            $data = [];
            $sppbisi = [];
            $sppnisi = [];
            $sppb_bayar = [];
            $sppn_terima = [];
        }
       //dd($karyawan_no_vendor_sppb);
        // dd($data,$rentang_waktu,$request->status_bayar,$request->jenis_report);
    //    dd($status_bayar);
        return view ('page.laporan.laporan', compact ('data','sppbisi','sppnisi','sppb_bayar','sppn_terima','status_bayar','rentang_waktu_raw','karyawan_no_vendor_sppb','karyawan_no_vendor_sppn','jenis_spp','jenis_report','export_tipe','c_spp','c_sppb','c_sppn'));

    }

}
