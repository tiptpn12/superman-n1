<?php

namespace App\Http\Controllers;

use App\Company;
use App\Helpers\API;
use App\Imports\VendorsImport;
use App\Rekening;
use App\Vendor;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class VendorController extends Controller
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
        // $vendor = Vendor::All();
        $company = Company::All();
        $data = array(
            // 'vendor' => $vendor,
            'company' => $company
        );

        return view('page.vendor.vendor', $data);
    }

    public function store(Request $request)
    {
        Cache::flush();

        //dd($request->all());

        Vendor::create([
            'master_vendor_nama' => $request->nama,
            'master_vendor_nama_bank' => $request->nama_bank,
            'master_vendor_rekening' => $request->rekening_vendor,
            'master_vendor_atas_nama' => $request->atas_nama,
            'company_id' => $request->company
        ]);




        return redirect('/vendor');
    }

    public function storeRekening(Request $request)
    {
        $vendors = Vendor::All();

        try {

            $existingmaster_rekening = Rekening::pluck('master_rekening_kode_sap')->toArray();


            foreach ($vendors as $vendor) {
                if (!in_array($vendor->master_vendor_rekening, $existingmaster_rekening)) {
                    Rekening::create([
                        'company_id' => 5,
                        'master_rekening_kode_sap' => $vendor->master_vendor_rekening,
                        'master_rekening_keterangan' => $vendor->master_vendor_nama
                    ]);
                }
            }
        } catch (Exception $e) {
            dd($e);
        }

        dd('berhasil');
    }

    public function update(Request $request)
    {
        Cache::flush();
        $vendor = Vendor::find($request->id);
        $vendor->master_vendor_nama = $request->nama;
        $vendor->master_vendor_nama_bank = $request->nama_bank;
        $vendor->master_vendor_rekening = $request->rekening_vendor;
        $vendor->master_vendor_atas_nama = $request->atas_nama;
        $vendor->company_id = $request->company;
        $vendor->save();

        return redirect('/vendor');
    }

    public function destroy($id, $status)
    {
        Cache::flush();
        $vendor = Vendor::find($id);
        $vendor->master_vendor_status = $status == 1 ? 0 : 1;
        $vendor->save();

        return redirect('/vendor');
    }

    public function getData(Request $request)
    {
        try {
            // $vendor = Cache::remember('master_vendor' . $request->input('draw'), 60 * 10, function () {
            //     return Vendor::all();
            // });
            $vendor = Vendor::query();
            // Debugbar::info($vendor);
            $response = datatables()->of($vendor)->addIndexColumn()->make(true);

            // Debugbar::info($response);
            return $response;
        } catch (Exception $e) {
            // Debugbar::addThrowable($e);
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }

    public function getDataTableAll()
    {
        // $vendor = Vendor::all();
        // $datatable =  datatables()->of($vendor)->addIndexColumn()->toJson();
        // return $datatable;


        $data = Cache::remember('vendor', now()->addSecond(60), function () {
            $vendor = Vendor::leftJoin('master_company', 'master_vendor.company_id', '=', 'master_company.company_id')->select('master_vendor.*', 'master_company.company_nama')->get();

            return $vendor;
        });

        $datatable = datatables()->of($data)->addIndexColumn()->toJson();

        return $datatable;
    }

    public function getDataAll()
    {

        $vendor = Vendor::all();

        return API::createApi($vendor);
    }

    public function import(Request $request)
    {
        Excel::import(new VendorsImport, $request->file('file'));

        return redirect('/vendor');
    }
}
