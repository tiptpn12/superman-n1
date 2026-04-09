<?php

namespace App\Http\Controllers;

use App\HakAkses;
use App\Ui;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class HakAksesController extends Controller
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
        $ui = Ui::All();
        $hak_akses = HakAkses::leftJoin('master_grup_ui', 'master_grup_ui.grup_id', '=', 'master_hak_akses.grup_ui_id')
            ->get();
        $data = array(
            'hak_akses' => $hak_akses, 
            'ui' => $ui,
        );

        return view('page.hak_akses.hak_akses', $data);
    }

    public function store(Request $request)
    {
        HakAkses::create([
            'master_hak_akses_nama' => $request->nama,
            'master_hak_akses_level' => $request->level,
            'master_hak_akses_keterangan' => $request->keterangan,
            'grup_ui_id' => $request->ui
        ]);
 
        return redirect('/hak_akses');
    }

    public function update(Request $request)
    {
        $hak_akses = HakAkses::find($request->id);
        $hak_akses->master_hak_akses_nama = $request->nama;
        $hak_akses->master_hak_akses_level = $request->level;
        $hak_akses->master_hak_akses_keterangan = $request->keterangan;
        $hak_akses->grup_ui_id = $request->ui;
        $hak_akses->save();

        return redirect('/hak_akses');
    }

    public function destroy($id, $status)
    {
        $hak_akses = HakAkses::find($id);
        $hak_akses->master_hak_akses_status = $status==1?0:1;
        $hak_akses->save();

        return redirect('/hak_akses');
    }
}
