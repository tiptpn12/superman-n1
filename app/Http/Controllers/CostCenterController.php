<?php

namespace App\Http\Controllers;

use App\CostCenter;
use App\Imports\CostCentersImport;
use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class CostCenterController extends Controller
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
        $cost_center = DB::table('master_cost_center')->leftJoin('master_company', 'master_company.company_id', '=', 'master_cost_center.company_id')->get();
        $data_company = DB::table('master_company')->where('company_status', '!=', '0')->select('master_company.*')->get();

        $data = array(
            'cost_center' => $cost_center,
            'data_company' => $data_company,
        );

        return view('page.cost_center.cost_center', $data);
    }

    public function store(Request $request)
    {
        CostCenter::create([
            'master_cost_center_kode' => $request->kode,
            'master_cost_center_keterangan' => $request->keterangan
            // 'company_id' => $request->company
        ]);

        return redirect('/cost_center');
    }

    public function update(Request $request)
    {
        $cost_center = CostCenter::find($request->id);
        $cost_center->master_cost_center_kode = $request->kode;
        $cost_center->master_cost_center_keterangan = $request->keterangan;
        // $cost_center->company_id = $request->company;
        $cost_center->save();

        return redirect('/cost_center');
    }

    public function destroy($id, $status)
    {
        $cost_center = CostCenter::find($id);
        $cost_center->master_cost_center_status = $status == 1 ? 0 : 1;
        $cost_center->save();

        return redirect('/cost_center');
    }

    public function import(Request $request)
    {
        Excel::import(new CostCentersImport, $request->file('file'));
        return redirect('/cost_center');
    }
}
