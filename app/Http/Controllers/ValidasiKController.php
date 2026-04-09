<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Sppb;
use App\Sppn;
use App\Spp;
use App\IsiSppb;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\SppStatus;
use App\DokumenPendukungSppb;
use App\DokumenPendukungSppn;
use App\ProfitCenter;
use App\Rekening;
use App\CostCenter;
use App\DokumenTambahan;
use App\CashFlow;
use App\Bagian;
use App\SppProses;
use App\RekamJejak;
use App\SppbBayar;
use App\SppnTerima;
use App\Vendor;
use App\NamaKaryawanModel;
use Carbon\Carbon;
use File;
use DB;
use PDF;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\FakturPajak;
use App\User;
use App\HakAkses;
use App\HistoryLogin;
use App\DetailLogin;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;
use Illuminate\Routing\Redirector;

class ValidasiKController extends Controller
{
    
    public function login_validasi($id){
        return view('page.login_validasi_sppk')->with(['id'=> $id]);
    }

    public function loginpost($id, Request $request){
        
        $username = $request->username;
        $password = $request->password;
        
        $data = User::whereRaw('master_user_name = ?', [$username])->first();
        $id_raw = hex2bin($id);
        $id_spp = base64_decode($id_raw);
        $spp = DB::table('spp')->where('spp.spp_id',$id_spp)->select('spp.*')->first();
        //dd($spp,$data);
        if($data){
            //dd('ab');   

            if(($data->master_hak_akses_id == 2 && $data->master_bagian_id == 2) || ($data->master_hak_akses_id == 3 && $data->master_bagian_id == 2 ) || $data->master_hak_akses_id > 3){
                //dd('a');   
                if($result= file_get_contents("https://ipinfo.io/?token=6718d4ac50f02a")){
                        $ip = json_decode($result);
                        $ipaddress = $_SERVER['REMOTE_ADDR'];
                        $agent = new Agent();
                        $browser = $agent->browser();
                        $os = $agent->platform();
                        $device = $agent->device();
            
                        $level = HakAkses::where('master_hak_akses_id',$data->master_hak_akses_id)->first();
                        $pw = decrypt($data->master_user_password);
                        if($password == $pw){
                            Session::put('username',$data->master_user_name);
                            Session::put('hak_akses',$data->master_hak_akses_id);
                            Session::put('bagian',$data->master_bagian_id);
                            Session::put('id',$data->master_user_id);
                            Session::put('level',$level->master_hak_akses_level);
            
                            $detail_login = new DetailLogin;
                            $detail_login->detail_login_ip = $ip->ip;
                            if(isset($ip->hostname)){
                                $detail_login->detail_login_hostname = $ip->hostname;
                            }
                            else{
                                $detail_login->detail_login_hostname = '-';
                            }
                            $detail_login->detail_login_city = $ip->city;
                            $detail_login->detail_login_region = $ip->region;
                            $detail_login->detail_login_country_code = $ip->country;
                            $detail_login->detail_login_loc = $ip->loc;
                            $detail_login->detail_login_country = country_name($ip->country);
                            $detail_login->detail_login_browser = $browser;
                            $detail_login->detail_login_os = $os;
                            $detail_login->save();
                            $request->request->add(['detail_login_id'=>$detail_login->detail_login_id]);
            
                            $history = new HistoryLogin;
                            $history->master_user_id = $data->master_user_id;
                            $history->history_login_status = 1;
                            $history->detail_login_id = $request->detail_login_id;
                            $history->save();
                        
                            $level = Session::get('level');
                            $bagian = Session::get('bagian');
                            
                            return redirect('spp_keuangan/validasi_sppk/'.$id)->with('alert','Berhasil Login');
                            
                        }
                        else{
                            return redirect('spp_keuangan/validasi_login/'.$id)->with('alert','Login gagal! Password Salah');
                        }
                   
                    }else{
                        return redirect('spp_keuangan/validasi_login/'.$id)->with('alert','Gagal! Anda bukan pembuat SPP');

                    }        
            }
        else{
            return redirect('spp_keuangan/validasi_login/'.$id)->with('alert','Gagal! Anda bukan pembuat SPP');
        }}
        else{
            return redirect('spp_keuangan/validasi_login/'.$id)->with('alert','Login gagal! Periksa kembali username anda');
        }

    }

    public function index_sppk($id){
        $id_raw = hex2bin($id);
        $id = base64_decode($id_raw);
        $idspp = DB::table('spp')->where('spp.spp_id','=',$id)->select('spp.*')->first();
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id','=',$id)
        ->join('master_hak_akses','dokumen_tambahan.master_hak_akses_id','=','master_hak_akses.master_hak_akses_level')
        ->select('dokumen_tambahan.*','master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        if($idsppb != null){
            $datasppb = DB::table('sppb')->where('sppb_id','=',$idsppb)
            ->leftJoin('master_bagian','sppb.master_bagian_id','=','master_bagian.master_bagian_id')
            ->select('sppb.*','master_bagian.*')->first();
            $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id','=',$datasppb->sppb_id)
            ->leftJoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')

            ->leftjoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
            ->leftjoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
            ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_profit_center.*','master_cost_center.*','master_cash_flow.*')->get();
            $sppb_bayar = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id','=',$idsppb)->select('sppb_bayar.*')->first();
            $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id','=',$idsppb)->select('faktur_pajak_nomor')->get();

            $dokpensppb = DB::table('dokumen_pendukung_sppb')->where('dokumen_pendukung_sppb.sppb_id','=',$datasppb->sppb_id)
            ->select('dokumen_pendukung_sppb.*')->get();
            // dd($dokpensppb);
            foreach($sppbisi as $a =>$value2){
                 $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id','=',$value2->sppb_isi_id)->select('sppb_uraian.*')->get();
             }
        
            foreach ($sppbisi as $s => $val) {
                $isisppb[]=collect($val)->push($sppburaian[$s]);

            }
            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb); 
        }
        else {
            $data_sppb = [];
            $dokpensppb=[];
            $sppb_bayar=null;
        }
        if($idsppn != null){
            $datasppn = DB::table('sppn')
            ->where('sppn_id','=',$idsppn)->leftJoin('master_bagian','sppn.master_bagian_id','=','master_bagian.master_bagian_id')
            ->select('sppn.*','master_bagian.*')->first();

            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$idsppn)
            ->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
            ->leftjoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
            ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->get();
            $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id','=',$idsppn)->select('faktur_pajak_nomor')->get();

            // dd($sppnisi);
            $dokpensppn = DB::table('dokumen_pendukung_sppn')->where('sppn_id','=',$datasppn->sppn_id)
            ->select('dokumen_pendukung_sppn.*')->get();
            $sppn_terima = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$idsppn)->select('sppn_terima.*')->first();

            foreach($sppnisi as $a =>$value1){
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id','=',$value1->sppn_isi_id)->select('sppn_uraian.*')->get();
             }
    
            $isisppn=[];
            foreach ($sppnisi as $s => $val) {
                $isisppn[]=collect($val)->push($sppnuraian[$s]);
            }
    
            $data_sppn=[];
            $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
            // dd($data_sppb,$data_sppn);    
        }
        else{
            $data_sppn=null;
            $dokpensppn=null;
            $sppn_terima=null;
        }

        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response=$client->request('GET',$url,[
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());

        $form=0;
        if(isset($data_sppb) && empty($data_sppn)){
            $form=1;
            if($data_sppb['sppb_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.karyawan_nama')->get();
                foreach($Krywn as $k => $val){
                    
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                
                foreach($karyawan_sppb as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        
                    }
                }
            }
            else{
                $karyawan_no_vendor_sppb = null;
            }
            $karyawan_no_vendor_sppn = null;
        }
        else if(isset($data_sppn) && empty($data_sppb)){
            $form=2;
            if($data_sppn['sppn_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.karyawan_nama')->get();
                foreach($Krywn as $k => $val){
                    $nama=$val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                foreach($karyawan_sppn as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        $karyawan_nama_sppn[] = $v2->karyawan_nama;
                    }
                }
            }else{
                $karyawan_no_vendor_sppn = null;
            }
            $karyawan_no_vendor_sppb = null;
        }
        else{
            $form=3;
            if($data_sppb['sppb_metode_pembayaran']== "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.karyawan_nama')->get();
                foreach($Krywn as $k => $val){
                    
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                
                foreach($karyawan_sppb as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        $karyawan_nama_sppb[] = $v2->karyawan_nama;
                    }
                }
            }
            else{
                $karyawan_no_vendor_sppb = null;
            }
            if($data_sppn['sppn_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.karyawan_nama')->get();
                foreach($Krywn as $k => $val){
                    $nama=$val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                foreach($karyawan_sppn as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        $karyawan_nama_sppn[] = $v2->karyawan_nama;
                    }
                }
            }
            else{
                $karyawan_no_vendor_sppn = null;
            }
        }
        $id_validasi = base64_encode($id);
        // dd($idspp->spp_jalur_pajak);
        $data = array(
            'id_validasi' => bin2hex($id_validasi),
            'sppb' => $data_sppb, 
            'sppn' => $data_sppn,
            'dokpensppb' => $dokpensppb,
            'dokpensppn' => $dokpensppn,
            'formspp' => $form,
            'doktam' => $doktam,
            'spp' => $idspp,
            'dok_kabag' => $idspp->spp_kabag,
            'sppb_bayar' => $sppb_bayar,
            'sppn_terima' => $sppn_terima,
            'no_vendor_sppb' => $karyawan_no_vendor_sppb,
            'no_vendor_sppn' => $karyawan_no_vendor_sppn,
        );
        //dd($data_sppb);
        // return $data_sppb;
            return view ('page.spp_keuangan.spp_keuangan_validasi',$data);
    }

    public function preview($id){
        
        $id_raw = hex2bin($id);
        $id = base64_decode($id_raw);
        $idspp = DB::table('spp')->where('spp.spp_id','=',$id)->select('sppb_id','sppn_id')->first();
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id','=',$id)
        ->join('master_hak_akses','dokumen_tambahan.master_hak_akses_id','=','master_hak_akses.master_hak_akses_id')
        
        ->select('dokumen_tambahan.*','master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        if($idsppb != null){
            $datasppb = DB::table('sppb')->where('sppb_id','=',$idsppb)
            ->leftJoin('master_bagian','sppb.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_vendor','sppb.master_bank_id','=','master_vendor.master_vendor_id')
            ->select('sppb.*','master_bagian.*','master_vendor.*')->first();
            $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id','=',$datasppb->sppb_id)
            ->leftjoin('master_rekening','sppb_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
            ->leftJoin('master_gl','sppb_isi.master_gl_id','=','master_gl.master_gl_id')
            ->leftjoin('master_cost_center','sppb_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
            ->leftjoin('master_cash_flow','sppb_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
            ->leftJoin('master_profit_center','sppb_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
            ->select('sppb_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_cash_flow.*','master_profit_center.*')->get();
            $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id','=',$datasppb->sppb_id)->select('faktur_pajak_nomor')->get();
            foreach($sppbisi as $a =>$value2){
                 $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id','=',$value2->sppb_isi_id)->select('sppb_uraian.*')->get();
             }
   
            $isisppb=[];
            foreach ($sppbisi as $s => $val) {
                $isisppb[]=collect($val)->push($sppburaian[$s]);

            }
            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb); 
        }
        else {
            $data_sppb = null;
        }
        if($idsppn != null){
            $datasppn = DB::table('sppn')
            ->where('sppn_id','=',$idsppn)->leftJoin('master_bagian','sppn.master_bagian_id','=','master_bagian.master_bagian_id')
            ->leftJoin('master_vendor','sppn.master_bank_id','=','master_vendor.master_vendor_id')
            ->select('sppn.*','master_bagian.*','master_vendor.*')->first();

            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id','=',$idsppn)->leftjoin('master_rekening','sppn_isi.master_kode_vendor_id','=','master_rekening.master_rekening_id')
            ->leftJoin('master_cost_center','sppn_isi.master_cost_center_id','=','master_cost_center.master_cost_center_id')
            ->leftJoin('master_gl','sppn_isi.master_gl_id','=','master_gl.master_gl_id')
            ->leftJoin('master_profit_center','sppn_isi.master_profit_center_id','=','master_profit_center.master_profit_center_id')
            ->leftjoin('master_cash_flow','sppn_isi.master_cash_flow_id','=','master_cash_flow.master_cash_flow_id')
            ->select('sppn_isi.*','master_rekening.*','master_gl.*','master_cost_center.*','master_profit_center.*','master_cash_flow.*')->get();
           
            $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id','=',$datasppn->sppn_id)->select('faktur_pajak_nomor')->get();

            foreach($sppnisi as $a =>$value1){
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id','=',$value1->sppn_isi_id)->select('sppn_uraian.*')->get();
             }
    
            $isisppn=[];
            foreach ($sppnisi as $s => $val) {
                $isisppn[]=collect($val)->push($sppnuraian[$s]);
            }
            
            $data_sppn=[];
            $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
      
        }
        else{
            $data_sppn=null;
        }
        $form=0;
        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response=$client->request('GET',$url,[
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());

        if(isset($data_sppb) && empty($data_sppn)){
            $form=1;
            if($data_sppb['sppb_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach($Krywn as $k => $val){
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                foreach($karyawan_sppb as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                    }
                }
                $karyawan_sppb = $Krywn;
            }
            else{
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
             
            }
            $karyawan_no_vendor_sppn = null;
            $karyawan_sppn = null;

        }
        else if(isset($data_sppn) && empty($data_sppb)){
            $form=2;
            if($data_sppn['sppn_jenis'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach($Krywn as $k => $val){
                    $nama=$val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                foreach($karyawan_sppn as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                    }
                }
                $karyawan_sppn = $Krywn;
            }
            else{
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
                

            }
            $karyawan_no_vendor_sppb = null;
            $karyawan_sppb = null;

        }
        else{
            $form=3;
            if($data_sppb['sppb_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach($Krywn as $k => $val ){
                    $nama= $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                
                foreach($karyawan_sppb as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                      
                    }
                }
                $karyawan_sppb = $Krywn;
            }
            else{
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id','=',$data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
                
            }
            if($data_sppn['sppn_metode_pembayaran'] == "karyawan"){
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach($Krywn as $k => $val){
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                
                foreach($karyawan_sppn as $k => $v){
                    foreach($v as $k1 => $v2){
                        $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                   
                    }
                }
                $karyawan_sppn = $Krywn;
            }
            else{
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id','=',$data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
                
            }
        }
        $id_validasi = base64_encode($id);
        $data = array(
            'sppb' => $data_sppb, 
            'sppn' => $data_sppn,
            'formspp' => $form,
            'no_vendor_sppb' => $karyawan_no_vendor_sppb,
            'no_vendor_sppn' => $karyawan_no_vendor_sppn,
            'karyawan_sppb' => $karyawan_sppb,
            'karyawan_sppn' => $karyawan_sppn,
            'id' => bin2hex($id_validasi),
            'validasi' => 1,
        
        );
       //dd($karyawan_no_vendor_sppb);
        //return view ('page.spp_keuangan.spp_keuangan_cetak',$data);
  

        // $pdf = PDF::loadView('page.spp_keuangan.spp_keuangan_preview', $data);
    
        // return $pdf->stream();
        return view ('page.spp_keuangan.spp_keuangan_preview',$data);
    }
}
