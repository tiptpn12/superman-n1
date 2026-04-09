<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use DB;

class DashboardV2Controller extends Controller
{
    public function index(Request $request){
        $hak_akses = Session::get('hak_akses');
        $status = $request->get('status') == null ? 'todolist' : $request->get('status');
        $data =  DB::select('call usp_get_data_spp_by_status("'.$hak_akses.'","'.$status.'")');
       
        return view('dashboard-v2.index',compact('data'));
        dd($status);
       
    }
}
