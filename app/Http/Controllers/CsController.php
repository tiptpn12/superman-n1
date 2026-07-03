<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Imports\CustomersImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class CsController extends Controller
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
        return view('page.customer.customer');
    }

    public function getDataTableAll()
    {
        $data = Customer::query();
        $datatable = datatables()->of($data)->addIndexColumn()->toJson();

        return $datatable;
    }

    public function store(Request $request)
    {
        Customer::create([
            'master_customer_kode_sap' => $request->kode,
            'master_customer_nama' => $request->keterangan,
            'master_customer_status' => 1

        ]);

        return redirect('/customer');
    }

    public function update(Request $request)
    {
        $customer = Customer::find($request->id);
        $customer->master_customer_kode_sap = $request->kode;
        $customer->master_customer_nama = $request->keterangan;
        $customer->save();

        return redirect('/customer');
    }

    public function destroy($id, $status)
    {
        $customer = Customer::find($id);
        $customer->master_customer_status = $status == 1 ? 0 : 1;
        $customer->save();

        return redirect('/customer');
    }

    public function import(Request $request)
    {
        Excel::import(new CustomersImport, $request->file('file'));
        return redirect('/customer');
    }
}
