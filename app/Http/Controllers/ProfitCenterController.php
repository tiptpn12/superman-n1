<?php

namespace App\Http\Controllers;

use App\Imports\ProfitCentersImport;
use App\ProfitCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ProfitCenterController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            //dd($this->user);
            //return $next($request);
            if ($this->user == null) {

                return redirect('login');
            } else {
                return $next($request);
            }
        });
    }
    public function index()
    {
        $profit_center = ProfitCenter::All();

        $data = array(
            'profit_center' => $profit_center,
        );

        return view('page.profit_center.profit_center', $data);
    }

    public function store(Request $request)
    {
        ProfitCenter::create([
            'master_profit_center_kode' => $request->kode,
            'master_profit_unit' => $request->keterangan
        ]);

        return redirect('/profit_center');
    }

    public function update(Request $request)
    {
        $profit_center = ProfitCenter::find($request->id);
        $profit_center->master_profit_center_kode = $request->kode;
        $profit_center->master_profit_unit = $request->keterangan;
        $profit_center->save();

        return redirect('/profit_center');
    }

    public function destroy($id, $status)
    {
        $profit_center = ProfitCenter::find($id);
        $profit_center->master_profit_center_status = $status == 1 ? 0 : 1;
        $profit_center->save();

        return redirect('/profit_center');
    }

    public function import(Request $request)
    {
        Excel::import(new ProfitCentersImport, $request->file('file'));
        return redirect('/profit_center');
    }
}
