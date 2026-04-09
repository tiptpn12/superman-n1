<?php

namespace App\Http\Controllers;

use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Redirector;

class CompanyController extends Controller
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
        $company = Company::All();
        //$client = new Client();
       // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response=$client->request('GET',$url,[
        //     'verify' => false,
        // ]);
        //$kabag = json_decode($response->getBody());
        
        $data = array(
            'company' => $company
        );

        return view('page.company.company', $data);
    }

    // public function profil_kabag($id){
    //     $client = new Client();
    //     $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
    //     $response=$client->request('GET',$url,[
    //         'verify' => false,
    //     ]);
    //     $karyawan_all = json_decode($response->getBody());
    //     $bagian = DB::table('master_bagian')->where('master_bagian.master_bagian_id','=',$id)
    //     ->select('master_bagian.*')->first();
        
    //     $kabag_now = $bagian->master_bagian_kepala_bagian;
    //     $ino_bagian_id = $bagian->ino_bagian_id;

    //     $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use($ino_bagian_id) {
    //         return $value->bagian_id == $ino_bagian_id;
    //     });

    //     $kepala_bagian = Arr::where($karyawan_all, function ($value, $key) use($kabag_now){
    //         return $value->karyawan_nama == $kabag_now;
    //     });

    //     $data = array(
    //         'karyawan' => $karyawan_bagian, 
    //         'bagian' => $bagian,
    //         'kepala_bagian' => $kepala_bagian
    //     );

    //     return view('page.bagian.profil_bagian', $data);
    // }

    public function tambah(Request $request)
    {
        Company::create([
            'company_nama' => $request->company,
            'domisili_company' =>$request->domisili
        ]);
 
        return redirect('/company');
    }

    public function update(Request $request)
    {
        $company = Company::find($request->id);
        $company->company_nama = $request->company;
        $company->domisili_company = $request->domisili;
        $company->save();

        return redirect('/company');
    }

    public function destroy($id, $status)
    {
        $company = Company::find($id);
        // $company->company_status = $status==1?0:1;
        $company->delete();

        return redirect('/company');
    }
}
