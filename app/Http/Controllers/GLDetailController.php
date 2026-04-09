<?php

namespace App\Http\Controllers;

use App\GLDetail;
use App\Bagian;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class GLDetailController extends Controller
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
        $bagian = Bagian::All();
        $gl_detail=DB::table('master_gl_detail')->leftJoin('master_bagian','master_gl_detail.id_bagian','=','master_bagian.master_bagian_id')->get();

        $data = array(
            'gl_detail' => $gl_detail, 
            'bagian' => $bagian, 
        );
        // dd($data);
        return view('page.gl_detail.gl_detail', $data);
    }

    public function store(Request $request)
    {
        GLDetail::create([
            'id_bagian' => $request->id_bagian,
            'master_gl_detail_budget' => $request->budget,
            'master_gl_detail_status' => 1

        ]);
 
        return redirect('/gl_detail');
    }

    public function update(Request $request)
    {
        $gl = GLDetail::find($request->id);
        $gl->id_bagian = $request->id_bagian;
        $gl->master_gl_detail_budget = $request->budget;
        $gl->save();

        return redirect('/gl_detail');
    }

    public function destroy($id, $status)
    {
        $gl = GLDetail::find($id);
        $gl->master_gl_detail_status = $status==1?0:1;
        $gl->save();

        return redirect('/gl_detail');
    }
}
