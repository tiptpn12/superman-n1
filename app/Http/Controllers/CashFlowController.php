<?php

namespace App\Http\Controllers;

use App\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class CashFlowController extends Controller
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
        $cash_flow = CashFlow::All();

        $data = array(
            'cash_flow' => $cash_flow, 
        );

        return view('page.cash_flow.cash_flow', $data);
    }

    public function store(Request $request)
    {
        CashFlow::create([
            'master_cash_flow_kode' => $request->kode,
            'master_cash_flow_keterangan' => $request->keterangan
        ]);
 
        return redirect('/cash_flow');
    }

    public function update(Request $request)
    {
        $cash_flow = CashFlow::find($request->id);
        $cash_flow->master_cash_flow_kode = $request->kode;
        $cash_flow->master_cash_flow_keterangan = $request->keterangan;
        $cash_flow->save();

        return redirect('/cash_flow');
    }

    public function destroy($id, $status)
    {
        $cash_flow = CashFlow::find($id);
        $cash_flow->master_cash_flow_status = $status==1?0:1;
        $cash_flow->save();

        return redirect('/cash_flow');
    }
}
