<?php

namespace App\Http\Controllers;
use App\Spp;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class ChartJsController extends Controller
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

        $proses = [1,2,3,4,5,6,7];
        $petugas = ['Operator Bagian','Petugas Penerima','Petugas Pajak','Petugas SAP MIRO','Petugas Verifikasi','Petugas Kas dan Bank','Petugas Pembayaran'];
        $spp = [];
        foreach ($proses as $key => $value) {
            $spp[] = Spp::where(\DB::raw('spp_status_posisi'),$value)->count();
        }

        $status_bayar = [null,1];
        $status_bayar_text = ['Belum Dibayar','Sudah Dibayar'];
        $spp_bayar = [];
        foreach ($status_bayar as $key => $value){
            $spp_bayar[] = Spp::where(\DB::raw('spp_status_proses'),'>=',1)->where(\DB::raw('spp_status_lunas'),$value)->count();
        }
        //dd($spp_bayar);
        return view('dashboard')->with('bayar_count',json_encode($spp_bayar,JSON_NUMERIC_CHECK))->with('bayar_label',json_encode($status_bayar_text))->with('petugas',json_encode($petugas))->with('spp',json_encode($spp,JSON_NUMERIC_CHECK));
      
    }
}
