<?php

namespace App\Http\Controllers;
use App\DokumenTambahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use File;
use Illuminate\Routing\Redirector;

class DokumenTambahanController extends Controller
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
        
    }
    public function store(Request $request){
        $id = Session::get('id');
        $level = Session::get('level');
        $akses = Session::get('hak_akses');
        $request->validate([
            'dokumen_tambahan[]' => 'mimes:pdf,jpg,png,jpeg|max:3072',
        ]);
        $dokumentambahan = $request->file('dokumen_tambahan');
        $current = date('His-dmY');
            if($dokumentambahan != null)
                {
                    foreach ($dokumentambahan as $file) {
                        $dokumentambahan_file_name = str_replace("'",'',$file->getClientOriginalName());
                        $dokumentambahans = $current.'-'.$dokumentambahan_file_name;
                        $file->move('dokumen',$dokumentambahans);
                        $doktambahan = new \App\DokumenTambahan;
                        $doktambahan -> spp_id = $request->spp_id;
                        $doktambahan -> dokumen_tambahan_nama = $dokumentambahans;
                        $doktambahan -> master_hak_akses_id = $akses;
                        $doktambahan -> master_user_id = $id;
                        $doktambahan -> save();
                    }
                }
        return redirect('spp/detail/'.$request->spp_id);
    }
    public function destroy($id){
        $doktambahan = Dokumentambahan::find($id);
        File::delete(public_path('dokumen/'.$doktambahan->dokumen_tambahan_nama));
        $doktambahan->delete();
        return redirect()->back();
    }
}
