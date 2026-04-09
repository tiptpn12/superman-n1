<?php

namespace App\Http\Controllers;

use App\Ui;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Redirector;

class UiController extends Controller
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
    public function index()
    {
        $grup_ui = Ui::All();
        //$client = new Client();
       // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response=$client->request('GET',$url,[
        //     'verify' => false,
        // ]);
        //$kabag = json_decode($response->getBody());
        
        $data = array(
            'grup_ui' => $grup_ui
        );

        return view('page.grup_ui.tampilan', $data);
    }

    public function profil_kabag($id){
        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response=$client->request('GET',$url,[
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());
        $bagian = DB::table('master_bagian')->where('master_bagian.master_bagian_id','=',$id)
        ->select('master_bagian.*')->first();
        
        $kabag_now = $bagian->master_bagian_kepala_bagian;
        $ino_bagian_id = $bagian->ino_bagian_id;

        $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use($ino_bagian_id) {
            return $value->bagian_id == $ino_bagian_id;
        });

        $kepala_bagian = Arr::where($karyawan_all, function ($value, $key) use($kabag_now){
            return $value->karyawan_nama == $kabag_now;
        });

        $data = array(
            'karyawan' => $karyawan_bagian, 
            'bagian' => $bagian,
            'kepala_bagian' => $kepala_bagian
        );

        return view('page.bagian.profil_bagian', $data);
    }

    public function tambah(Request $request)
    {
        Ui::create([
            'grup_nama' => $request->grup,
            'grup_keterangan' => $request->keterangan
        ]);
 
        return redirect('/tampilan');
    }

    public function update(Request $request)
    {
        $grup_ui = Ui::find($request->id);
        $grup_ui->grup_nama = $request->grup;
        $grup_ui->grup_keterangan = $request->keterangan;
        $grup_ui->save();

        return redirect('/tampilan');
    }

    public function destroy($id, $status)
    {
        $grup_ui = Ui::find($id);
        $grup_ui->grup_status = $status==1?0:1;
        $grup_ui->save();

        return redirect('/tampilan');
    }
}
