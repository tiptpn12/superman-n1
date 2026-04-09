<?php

namespace App\Http\Controllers;

use App\CetakSPP;
use App\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CetakSPPController extends Controller
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
        $companyId = session()->get('company');
        $hakakses = session()->get('hak_akses');
        $cetak_spp = DB::table('master_cetak_spp')
            ->leftJoin('master_company', 'master_company.company_id', '=', 'master_cetak_spp.company_id')
            ->where(function ($query) use ($companyId, $hakakses) {
                if ($hakakses == 45) {
                    $query->where('master_company.company_id', '=', $companyId);
                }
            })
            ->get();

        // $data_company = DB::table('master_company')->where('company_status', '!=', '0')->select('master_company.*')->get();
        if ($hakakses == 45) {
            $data_company = DB::table('master_company')->where('company_id', '=', $companyId)->where('company_status', '!=', '0')->select('master_company.*')->get();
        } else {
            $data_company = DB::table('master_company')->where('company_status', '!=', '0')->select('master_company.*')->get();
        }

        $data = array(
            'cetak_spp' => $cetak_spp,
            'data_company' => $data_company,
            'companyId' => $companyId,
            'hakakses' => $hakakses
        );
        // dd($data);
        return view('page.cetak_spp.cetak_spp', $data);
    }

    public function store(Request $request)
    {
        CetakSPP::create([
            'company_id' => $request->company,
            'diperiksa_oleh_1' => $request->diperiksa_oleh_1,
            'diperiksa_oleh_2' => $request->diperiksa_oleh_2,
            'diperiksa_oleh_3' => $request->diperiksa_oleh_3,
            'disetujui_oleh' => $request->disetujui_oleh,
            'tujuan_kepada' => $request->tujuan_kepada,
            'tujuan_kepada_sevp' => $request->tujuan_kepada_sevp,
            'keterangan' => $request->keterangan,
            // 'diperiksa_oleh_1_nama' => $request->diperiksa_oleh_1_nama,
            // 'diperiksa_oleh_2_nama' => $request->diperiksa_oleh_2_nama,
            // 'diperiksa_oleh_3_nama' => $request->diperiksa_oleh_3_nama,
            // 'disetujui_oleh_nama' => $request->disetujui_oleh_nama,

        ]);

        return redirect('/cetak_spp')->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, CetakSPP $cetak_spp)
    {

        // Ambil data yang akan di-update berdasarkan id
        $cetak_spp = CetakSPP::find($request->id);

        // Cek apakah data ditemukan
        if (!$cetak_spp) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Update data yang diinginkan
        $cetak_spp->company_id = $request->company;
        $cetak_spp->diperiksa_oleh_1 = $request->diperiksa_oleh_1;
        $cetak_spp->diperiksa_oleh_2 = $request->diperiksa_oleh_2;
        $cetak_spp->diperiksa_oleh_3 = $request->diperiksa_oleh_3;
        $cetak_spp->disetujui_oleh = $request->disetujui_oleh;
        $cetak_spp->tujuan_kepada = $request->tujuan_kepada;
        $cetak_spp->tujuan_kepada_sevp = $request->tujuan_kepada_sevp;
        $cetak_spp->keterangan =  $request->keterangan;
        // $cetak_spp->diperiksa_oleh_1_nama = $request->diperiksa_oleh_1_nama;
        // $cetak_spp->diperiksa_oleh_2_nama = $request->diperiksa_oleh_2_nama;
        // $cetak_spp->diperiksa_oleh_3_nama =  $request->diperiksa_oleh_3_nama;
        // $cetak_spp->disetujui_oleh_nama = $request->disetujui_oleh_nama;

        // Simpan data yang telah diperbarui
        $cetak_spp->save();

        // Redirect dengan pesan sukses
        return redirect('/cetak_spp')->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy($id, $status)
    {
        $cetak_spp = CetakSPP::find($id);
        $cetak_spp->status = $status == 1 ? 0 : 1;
        $cetak_spp->save();

        return redirect('/cetak_spp');
    }
}
