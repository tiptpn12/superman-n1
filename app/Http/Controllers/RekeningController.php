<?php

namespace App\Http\Controllers;

use App\Company;
use App\Helpers\API;
use App\Imports\RekeningsImport;
use App\Rekening;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class RekeningController extends Controller
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
        $rekening = Rekening::All();
        $company = Company::All();

        $data = array(
            'rekening' => $rekening,
            'company' => $company
        );

        return view('page.rekening.rekening', $data);
    }

    public function getData()
    {

        // $data = Cache::remember('rekening', now()->addSecond(60), function () {
        //     $rekening = Rekening::leftJoin('master_company', 'master_rekening.company_id', '=', 'master_company.company_id')->select('master_rekening.*', 'master_company.company_nama')->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
        //         ->where('master_rekening_kode_sap', '<>', '')
        //         ->where('master_rekening_kode_sap', '<>', 0)->get();

        //     return $rekening;
        // });
        $data = Cache::remember('rekening', now()->addSecond(60), function () {
            $rekening = Rekening::select('master_rekening.*')->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
                ->where('master_rekening_kode_sap', '<>', '')
                ->where('master_rekening_kode_sap', '<>', 0)->get();

            return $rekening;
        });

        $datatable = datatables()->of($data)->addIndexColumn()->toJson();

        return $datatable;

        // $rekening = Rekening::All();

        // $datatable = datatables()->of($rekening)->addIndexColumn()->toJson();
        // return $datatable;
    }

    public function fetchData(Request $request)
    {
        $perPage = 50;
        $page = $request->input('page', 1); // Current page
        $search = $request->input('search');

        // Querying the Rekening model
        $query = Rekening::query();

        // Filtering based on search query if provided
        if ($search) {
            $query->where('master_rekening_keterangan', 'like', '%' . $search . '%')->orWhere('master_rekening_kode_sap', 'like', '%' . $search . '%');
        }
        $query = $query->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0);
        // Paginating the results
        $rekenings = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'items' => $rekenings->items(),
            'pagination' => [
                'more' => $rekenings->hasMorePages()
            ]
        ]);
    }


    public function store(Request $request)
    {
        Cache::flush();
        Rekening::create([
            'master_rekening_kode_kbb' => $request->kode_kbb,
            'master_rekening_kode_sap' => $request->kode_sap,
            'master_rekening_keterangan' => $request->keterangan
            // 'company_id' => $request->company
        ]);

        return redirect('/rekening');
    }

    public function update(Request $request)
    {
        Cache::flush();
        $rekening = Rekening::find($request->id);
        $rekening->master_rekening_kode_kbb = $request->kode_kbb;
        $rekening->master_rekening_kode_sap = $request->kode_sap;
        $rekening->master_rekening_keterangan = $request->keterangan;
        // $rekening->company_id = $request->company;
        $rekening->save();

        return redirect('/rekening');
    }

    public function destroy($id, $status)
    {
        Cache::flush();
        $rekening = Rekening::find($id);
        $rekening->master_rekening_status = $status == 1 ? 0 : 1;
        $rekening->save();

        return redirect('/rekening');
    }

    public function import(Request $request)
    {
        Excel::import(new RekeningsImport, $request->file('file'));
        return redirect('/rekening');
    }
}
