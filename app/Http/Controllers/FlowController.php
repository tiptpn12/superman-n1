<?php

namespace App\Http\Controllers;

use App\Flow;
use App\Company;
use App\HakAkses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Redirector;

class FlowController extends Controller
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
        $flow = DB::table('master_flow')
        ->leftJoin('master_company_detail', 'master_company_detail.flow_id', '=', 'master_flow.flow_id')
        ->leftJoin('master_company', 'master_company.company_id', '=', 'master_company_detail.company_id')
        ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id') 
        ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'master_flow_detail.flow_detail_urutan')  
        ->groupBy('flow_nama', 'company_nama', 'flow_keterangan', 'flow_status')
        ->select('master_flow.flow_id', 'flow_nama', 'company_nama', 'flow_keterangan', 'flow_status','master_company_detail.company_id')
        ->get();

    foreach ($flow as $item) {
        $item->flow = DB::table('master_flow_detail')->where('flow_id', $item->flow_id)->orderBy('flow_akses', 'asc')->leftjoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'master_flow_detail.flow_detail_urutan')->select('master_hak_akses.master_hak_akses_nama','flow_detail_urutan','flow_revisi_stop')->get();
    }
    


                // $flow = Flow::select(DB::raw('flow_nama,company_nama,group_concat(master_hak_akses_nama) as flow,flow_keterangan, flow_status'))
                //     ->leftJoin('master_company_detail', 'master_company_detail.flow_id', '=', 'master_flow.flow_id')
                //     ->leftJoin('master_company', 'master_company.company_id', '=', 'master_company_detail.company_id')
                //     ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id') 
                //     ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'master_flow_detail.flow_detail_urutan')  
                //     ->groupBy('flow_nama','company_nama','flow_keterangan','flow_status')
                //     ->orderBy('master_hak_akses_nama', 'asc')
                //     ->get();
        //dd($flow);
        $company = Company::where('company_status', 1)->get();
        $hakakses = HakAkses::where('master_hak_akses_status', 1)
        ->orderBy('master_hak_akses_nama', 'asc')
        ->get();
    
        //$client = new Client();
       // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response=$client->request('GET',$url,[
        //     'verify' => false,
        // ]);
        //$kabag = json_decode($response->getBody());
        //dd($flow);
        $data = array(
            'flow' => $flow,
            'company' => $company,
            'hakakses' => $hakakses,
        );
        //var_dump($data['flow']);
        return view('page.flow.flow', $data);
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
        //dd($request->all());
         $flow = $request->tahapan;
         $company = $request->company;
         $flow_stop=$request->stop;
        //  dd($flow_stop);
        //  dd($flow);
         
         // dd($request->nama_flow,$flow,$company,$request->keterangan);
                 // $gabung=implode(",", $flow);
        // foreach($flow as $key => $value){
        //     $flow = new Flow;
        //     $flow -> flow_urutan = $value;
        //     $flow -> save();
        // }
        $flow_insert=Flow::create([
            'flow_nama' => $request->nama_flow,
            'flow_keterangan'=>$request->keterangan
        ]);
        // dd($flow_insert->flow_id);
        $i=0;
        foreach ($flow as $key => $value) {
            DB::table('master_flow_detail')->insert([
                'flow_detail_urutan' => $value,
                'flow_id'=>$flow_insert->flow_id,
                'flow_revisi_stop'=>$flow_stop[$key] ? $flow_stop[$key] : NULL,
                'flow_akses' => $i++
                // 'flow_revisi_stop'=>array_key_exists($key, $flow_stop) ? $flow_stop[$key] : NULL
            ]);
           // dd($flow_stop[$key]);
        }
        // foreach ($flow_stop as $key => $value) {
        //     DB::table('master_flow_detail')->insert([
        //         'flow_revisi_stop'=>$value
        //     ]);
        // }
        foreach ($company as $key => $value) {
            DB::table('master_company_detail')->insert([
                'company_id' => $value,
                'flow_id'=>$flow_insert->flow_id
            ]);
        }
        // dd();
 
        return redirect('/flow');
    }

    public function update(Request $request)
    {   
        DB::beginTransaction();
        try{
            $flow = Flow::find($request->id);
            $flow->flow_nama = $request->nama_flow;
            $flow->flow_keterangan = $request->keterangan;
            $flow->save();
    
            $urutan = $request->tahapan;
            //DELETE EXISTING DATA
            $flow_urutan = DB::table('master_flow_detail')->where('flow_id', $request->id)->delete();
    
            $flow_stop=$request->stop;
            //dd($flow_stop[2]);
            $i=0;
            foreach ($urutan as $key => $value) {
                DB::table('master_flow_detail')->insert([
                    'flow_detail_urutan' => $value,
                    'flow_id'=>$request->id,
                    'flow_revisi_stop'=> isset($flow_stop[$key]) ? $flow_stop[$key] : NULL,
                    'flow_akses' => $i++
                    // 'flow_revisi_stop'=>array_key_exists($key, $flow_stop) ? $flow_stop[$key] : NULL
                ]);
                $key++;
            }
        }
        catch(\Exception $e){
            DB::rollBack();
            dd($e);
        }
        DB::commit();
        return redirect('/flow');
    }

    public function destroy($id, $status)
    {
        $flow = Flow::find($id);
        $flow->flow_status = $status==1?0:1;
        $flow->save();

        return redirect('/flow');
    }
}
