<?php

namespace App\Http\Controllers;

use App\Company;
use App\GL;
use App\Imports\GLsImport;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class GLController extends Controller
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
        $gl = GL::All();
        $company = Company::All();

        $data = array(
            'gl' => $gl,
            'company' => $company
        );

        return view('page.gl.gl', $data);
    }

    public function getData()
    {


        $data = Cache::remember('gl', now()->addSecond(60), function () {
            // $gl = GL::leftJoin('master_company', 'master_gl.company_id', '=', 'master_company.company_id')->select('master_gl.*', 'master_company.company_nama')->get();
            $gl = GL::select('master_gl.*')->get();

            return $gl;
        });

        $datatable = datatables()->of($data)->addIndexColumn()->toJson();

        return $datatable;
        // $gl = GL::All();

        // $datatable = datatables()->of($gl)->addIndexColumn()->toJson();
        // return $datatable;
    }
    public function store(Request $request)
    {
        Cache::flush();
        GL::create([
            'master_gl_kode' => $request->kode,
            'master_gl_keterangan' => $request->keterangan,
            'master_gl_status' => 1
            // 'company_id' => $request->company

        ]);

        return redirect('/gl');
    }

    public function update(Request $request)
    {
        Cache::flush();
        $gl = GL::find($request->id);
        $gl->master_gl_kode = $request->kode;
        $gl->master_gl_keterangan = $request->keterangan;
        // $gl->company_id = $request->company;
        $gl->save();

        return redirect('/gl');
    }

    public function destroy($id, $status)
    {
        Cache::flush();
        $gl = GL::find($id);
        $gl->master_gl_status = $status == 1 ? 0 : 1;
        $gl->save();

        return redirect('/gl');
    }

    public function byCompany()
    {
        $company = Session::get('company');

        $gl = GL::where('master_gl_company', $company)->get();
    }

    public function import(Request $request)
    {
        Excel::import(new GLsImport, $request->file('file'));
        return redirect('/gl');
    }
}
