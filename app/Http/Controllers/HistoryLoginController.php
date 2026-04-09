<?php

namespace App\Http\Controllers;

use App\HistoryLogin;
use App\User;
use App\DetailLogin;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class HistoryLoginController extends Controller
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
        $user = User::all();
        $history_login = DB::table('history_login')->leftJoin('master_user', 'master_user.master_user_id', '=', 'history_login.master_user_id')
            ->leftJoin('master_bagian', 'master_bagian.master_bagian_id', '=', 'master_user.master_bagian_id')
            ->leftJoin('detail_login','detail_login.detail_login_id','=','history_login.detail_login_id')
            ->groupBy('history_login_waktu')->orderBy('history_login_waktu','desc')->get();

        $data = array(
            'history_login' => $history_login,
            'user' => $user
        );
        return view('page.history_login.history_login', $data);
    }

    public function store($user)
    {
        HistoryLogin::create([
            'master_user_id' => $user,
            'history_login_waktu' => DATE("Y-m-d H:i:s")
        ]);
 
        return response()->json(array("status"=>"oke"));
    }

    public function search(Request $request){
        
        $usernamereq = $request->username;
        
        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ",$rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
            })->all();
        
       
        $history_login = DB::table('history_login')->leftJoin('master_user', 'master_user.master_user_id', '=', 'history_login.master_user_id')
        ->leftJoin('master_bagian', 'master_bagian.master_bagian_id', '=', 'master_user.master_bagian_id')
        ->leftJoin('detail_login','detail_login.detail_login_id','=','history_login.detail_login_id')
        ->groupBy('history_login_waktu')->orderBy('history_login_waktu','desc')->get();
        // dd($history_login);
        
        if($usernamereq[0] != "semua"){
            $history_login = $history_login->whereIn('master_user_id',$usernamereq);
        }
        if($request->rentang_waktu){
            $history_login = $history_login->whereBetween('history_login_waktu',[$rentang_waktu[0].' 00:00:00',$rentang_waktu[1].' 23:59:59']);
        }
        $history_login = $history_login->values();
        $user = User::all();
        $data = array(
            'history_login' => $history_login,
            'user' => $user 
        );
        return view('page.history_login.history_login',$data);
    }
}
