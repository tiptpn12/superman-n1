<?php

namespace App\Http\Controllers;

use App\Bagian;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;

class BagianController extends Controller
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
    private function getManagedCompanyIds()
    {
        $role = session()->get('hak_akses');
        $currentCompany = session()->get('company');
        $username = session()->get('username');

        // Admin Pusat
        if ($role == "1" || $role == "44") {
            return Company::where('company_status', '!=', '0')->pluck('company_id')->toArray();
        }

        $managedIds = [$currentCompany];

        // Admin Regional (Role 45)
        if ($role == "45" && preg_match('/admin_reg(\d+)/', $username, $matches)) {
            $regionalId = $matches[1];
            $regionalIds = Company::where('company_nama', 'LIKE', '%Regional ' . $regionalId . ' %')
                                  ->where('company_status', '!=', '0')
                                  ->pluck('company_id')->toArray();
            $managedIds = array_unique(array_merge($managedIds, $regionalIds));
        }

        return $managedIds;
    }

    public function index()
    {
        $companyId = session()->get('company');
        $hakakses = session()->get('hak_akses');
        $currentBagian = session()->get('bagian');

        $managedCompanyIds = $this->getManagedCompanyIds();

        $bagian_query = DB::table('master_bagian')
            ->leftJoin('master_company', 'master_company.company_id', '=', 'master_bagian.company_id');

        if ($hakakses != 1 && $hakakses != 44) {
            $bagian_query->whereIn('master_company.company_id', $managedCompanyIds);
        }

        $bagian_all = $bagian_query->get();

        $bagian_regional = [];
        $bagian_unit = [];

        if ($hakakses == 45) {
            foreach($bagian_all as $b) {
                if ($b->company_id == $companyId) {
                    $bagian_regional[] = $b;
                } else {
                    $bagian_unit[] = $b;
                }
            }
        }

        if ($hakakses == 45) {
            $data_company = DB::table('master_company')
                ->whereIn('company_id', $managedCompanyIds)
                ->where('company_status', '!=', '0')
                ->select('master_company.*')
                ->get();
        } else {
            $data_company = DB::table('master_company')
                ->where('company_status', '!=', '0')
                ->select('master_company.*')
                ->get();
        }

        $data = array(
            'bagian' => $bagian_all,
            'bagian_regional' => $bagian_regional,
            'bagian_unit' => $bagian_unit,
            'data_company' => $data_company,
            'companyId' => $companyId,
            'hakakses' => $hakakses
        );
        return view('page.bagian.bagian', $data);
    }

    public function profil_kabag($id)
    {
        // $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());
        $bagian = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', $id)
            ->select('master_bagian.*')->first();

        // $kabag_now = $bagian->master_bagian_kepala_bagian;
        // $ino_bagian_id = $bagian->ino_bagian_id;

        // $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use ($ino_bagian_id) {
        //     return $value->bagian_id == $ino_bagian_id;
        // });

        // $kepala_bagian = Arr::where($karyawan_all, function ($value, $key) use ($kabag_now) {
        //     return $value->karyawan_nama == $kabag_now;
        // });

        $data = array(
            'karyawan' => [], // $karyawan_bagian,
            'bagian' => $bagian,
            'kepala_bagian' => [] // $kepala_bagian
        );

        return view('page.bagian.profil_bagian', $data);
    }

    public function store(Request $request)
    {
        Bagian::create([
            'master_bagian_nama' => $request->nama,
            'master_bagian_kode' => $request->kode,
            'master_bagian_kepala_bagian' => $request->kepala_bagian,
            'master_bagian_jabatan' => $request->jabatan,
            'master_bagian_keterangan' => $request->keterangan,
            'company_id' => $request->company

        ]);

        return redirect('/bagian');
    }

    public function update(Request $request)
    {
        $bagian = Bagian::find($request->id);
        $bagian->master_bagian_nama = $request->nama;
        $bagian->master_bagian_kode = $request->kode;
        $bagian->master_bagian_kepala_bagian = $request->kepala_bagian;
        $bagian->master_bagian_jabatan = $request->jabatan;
        $bagian->master_bagian_keterangan = $request->keterangan;
        $bagian->company_id = $request->company;
        $bagian->save();

        return redirect('/bagian');
    }

    public function update_kabag(Request $request)
    {
        $bagian = Bagian::find($request->id);
        $bagian->master_bagian_kepala_bagian = $request->kepala_bagian;
        $bagian->save();

        return redirect()->back();
    }

    public function destroy($id, $status)
    {
        $bagian = Bagian::find($id);
        $bagian->master_bagian_status = $status == 1 ? 0 : 1;
        $bagian->save();

        return redirect('/bagian');
    }
}
