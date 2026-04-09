<?php

namespace App\Http\Controllers;

use App\BahanJasa;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class BJController extends Controller
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
        $bahanjasa = BahanJasa::All();

        $data = array(
            'bahan_jasa' => $bahanjasa, 
        );
        // dd($data);
        return view('page.bahan_jasa.bahan_jasa', $data);
    }

    public function store(Request $request)
    {   
        BahanJasa::create([
            'master_bahan_jasa_jenis' => $request->jenis,
            'master_bahan_jasa_budget' => str_replace('.', '', $request->budget),
            'master_bahan_jasa_status' => 1

        ]);
 
        return redirect('/bahan_jasa');
    }

    public function update(Request $request)
    {
        $bahanjasa = BahanJasa::find($request->id);
        $bahanjasa->master_bahan_jasa_jenis = $request->jenis;
        $bahanjasa->master_bahan_jasa_budget = $request->budget;
        $bahanjasa->save();

        return redirect('/bahan_jasa');
    }

    public function destroy($id, $status)
    {
        $bahanjasa = BahanJasa::find($id);
        $bahanjasa->master_bahan_jasa_status = $status==1?0:1;
        $bahanjasa->save();

        return redirect('/bahan_jasa');
    }
}
