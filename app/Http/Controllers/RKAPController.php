<?php

namespace App\Http\Controllers;

use App\RKAP;
use App\GL;
use App\Bagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RKAPController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // dd(session()->all());
            // fetch session and use it in entire class with constructor
            // Ambil data user dan company dari session
            $this->user = session()->get('username');
            $this->company = session()->get('company');
            // dd($this->company);
            // rrrreturn $next($request);

            // Redirect ke login jika user tidak ditemukan di session
            if ($this->user == null) {
                return redirect('login');
            }

            return $next($request);
        });
    }

    public function index()
    {
        // Menentukan apakah pengguna adalah admin
        $is_admin = $this->user == 'admin';
        // dd($is_admin);

        if ($is_admin) {
            $gl = GL::all();
            $bagian = Bagian::all();
        } else {
            $company_id = $this->company;
            $gl = GL::where('company_id', $company_id)->get();
            $bagian = Bagian::where('company_id', $company_id)->get();
        }

        $data = [
            'gl' => $gl,
            'bagian' => $bagian,
        ];

        return view('page.rkap.rkap', $data);
    }

    public function getData(Request $request)
    {
        // Mendapatkan informasi company dari session
        $company_id = $this->company;

        // Menentukan apakah pengguna adalah admin
        $is_admin = $this->user == 'admin';

        $data = Cache::remember('rkap', now()->addSeconds(60), function () use ($is_admin, $company_id) {
            if ($is_admin) {
                return RKAP::leftJoin('master_gl', 'master_gl.master_gl_id', '=', 'master_budget.gl_id')
                    ->leftJoin('master_bagian', 'master_bagian.master_bagian_id', '=', 'master_budget.bagian_id')
                    ->select('budget_id', 'master_gl_kode', 'master_bagian_nama', 'jumlah_budget', 'budget_tahun', 'master_gl_id', 'master_bagian_id', 'budget_status')
                    ->get();
            } else {
                return RKAP::leftJoin('master_gl', 'master_gl.master_gl_id', '=', 'master_budget.gl_id')
                    ->leftJoin('master_bagian', 'master_bagian.master_bagian_id', '=', 'master_budget.bagian_id')
                    // ->where('master_gl.company_id', $company_id)
                    ->where('master_bagian.company_id', $company_id)
                    ->select('budget_id', 'master_gl_kode', 'master_bagian_nama', 'jumlah_budget', 'budget_tahun', 'master_gl_id', 'master_bagian_id', 'budget_status')
                    ->get();
            }
        });

        return datatables()->of($data)->addIndexColumn()->toJson();
    }

    public function store(Request $request)
    {
        Cache::flush();

        if ($request->divisi == "all") {
            $bagians = Bagian::all();
            foreach ($bagians as $bagian) {
                RKAP::create([
                    'gl_id' => $request->gl,
                    'bagian_id' => $bagian->master_bagian_id,
                    'jumlah_budget' => $request->rkap,
                    'budget_tahun' => $request->tahun
                ]);
            }
        } else {
            RKAP::create([
                'gl_id' => $request->gl,
                'bagian_id' => $request->divisi,
                'jumlah_budget' => $request->rkap,
                'budget_tahun' => $request->tahun
            ]);
        }

        return redirect('/rkap');
    }

    public function update(Request $request)
    {
        Cache::flush();

        if ($request->divisi == "all") {
            $bagians = Bagian::all();
            foreach ($bagians as $bagian) {
                $rkap = RKAP::where('bagian_id', $bagian->master_bagian_id)
                    ->where('gl_id', $request->gl)
                    ->where('budget_tahun', $request->tahun)
                    ->get();

                if ($rkap->isEmpty()) {
                    RKAP::create([
                        'gl_id' => $request->gl,
                        'bagian_id' => $bagian->master_bagian_id,
                        'jumlah_budget' => $request->rkap,
                        'budget_tahun' => $request->tahun
                    ]);
                } else {
                    $rkap[0]->jumlah_budget = $request->rkap;
                    $rkap[0]->save();
                }
            }
        } else {
            $rkap = RKAP::find($request->id);
            $rkap->gl_id = $request->gl;
            $rkap->bagian_id = $request->divisi;
            $rkap->jumlah_budget = $request->rkap;
            $rkap->budget_tahun = $request->tahun;
            $rkap->save();
        }

        return redirect('/rkap');
    }

    public function destroy($id, $status)
    {
        Cache::flush();

        $rkap = RKAP::find($id);
        $rkap->budget_status = $status == '1' ? 0 : 1;
        $rkap->save();

        return redirect('/rkap');
    }
}
