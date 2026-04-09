<?php

namespace App\Http\Controllers;

use App\CetakBuktiKas;
use App\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCetakBuktiKasRequest;
use App\Http\Requests\UpdateCetakBuktiKasRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakBuktiKasController extends Controller
{
    public $user;
    public $hakAksesId;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            // return $next($request);
            if ($this->user == null) {
                return redirect('login');
            } else {
                $this->hakAksesId = session()->get('hak_akses');
                if (!in_array($this->hakAksesId, [44, 45])) {
                    return redirect('dashboard');
                }
                return $next($request);
            }
        });
    }
    public function index()
    {
        $currentCompany = session()->get('company');

        $cetakBuktiKas = CetakBuktiKas::with(['company'])->where('company_id', $currentCompany)->get();

        $companies = Company::where('company_status', '!=', '0')
            ->where('company_id', $currentCompany)
            ->get();

        $view_data = [
            'hakAkses' => $this->hakAksesId,
            'cetakBuktiKas' => $cetakBuktiKas,
            'companies' => $companies
        ];
        return view('page.cetak_bukti_kas.index', $view_data);
    }

    public function getData()
    {
        $currentCompany = session()->get('company');
        $role=$this->hakAksesId;
        if($role == "1" || $role == "44"){
            
            $cetakBuktiKas = CetakBuktiKas::with(['company'])->orderBy('company_id', 'asc')->get();
            //dd($cetakBuktiKas);
        }
        else{
            $cetakBuktiKas = CetakBuktiKas::with(['company'])
            ->where('company_id', $currentCompany)
            ->get();
        }
        
        return response()->json($cetakBuktiKas);
    }

    public function getDataById($id)
    {
        try {
            $cetakBuktiKas = CetakBuktiKas::with(['company'])->find($id);
            return response()->json([
                'success' => true,
                'data' => $cetakBuktiKas
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], $th->getCode());
        }
    }

    public function store(StoreCetakBuktiKasRequest $request)
    {
        try {
            $cetakBuktiKas = new CetakBuktiKas();
            $cetakBuktiKas->company_id = $request->company;
            $cetakBuktiKas->dibuat_sub_bagian = $request->sub_bagian_pembuat;
            $cetakBuktiKas->dibuat_sub_bagian_nama = $request->nama_pembuat ?? null;
            $cetakBuktiKas->diperiksa_oleh_sub_bagian = $request->sub_bagian_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_sub_bagian_nama = $request->nama_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_bagian = $request->bagian_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_bagian_nama = $request->nama_bagian_pemeriksa;
            $cetakBuktiKas->disetujui_oleh = $request->yang_menyetujui;
            $cetakBuktiKas->disetujui_oleh_nama = $request->nama_yang_menyetujui;
            $cetakBuktiKas->is_bank = $request->is_bank;
            $cetakBuktiKas->lebih_dari_5_m = $request->lebih_dari_5_m;
            $cetakBuktiKas->lebih_dari_25_jt = $request->lebih_dari_25_jt;
            $cetakBuktiKas->created_at = Carbon::now()->format('Y-m-d H:i:s');
            $cetakBuktiKas->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $cetakBuktiKas->save();

            return response()->json([
                'success' => true,
                'message' => 'data berhasil disimpan',
                'data' => $cetakBuktiKas,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateCetakBuktiKasRequest $request, $id)
    {
        try {
            $cetakBuktiKas = CetakBuktiKas::find($id);
            if (!$cetakBuktiKas) {
                return response()->json([
                    'success' => false,
                    'message' => 'data tidak ditemukan'
                ], 404);
            }

            $cetakBuktiKas->company_id = $request->ubah_company_id;
            $cetakBuktiKas->dibuat_sub_bagian = $request->ubah_sub_bagian_pembuat;
            $cetakBuktiKas->dibuat_sub_bagian_nama = $request->ubah_nama_pembuat ?? null;
            $cetakBuktiKas->diperiksa_oleh_sub_bagian = $request->ubah_sub_bagian_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_sub_bagian_nama = $request->ubah_nama_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_bagian = $request->ubah_bagian_pemeriksa;
            $cetakBuktiKas->diperiksa_oleh_bagian_nama = $request->ubah_nama_bagian_pemeriksa;
            $cetakBuktiKas->disetujui_oleh = $request->ubah_yang_menyetujui;
            $cetakBuktiKas->disetujui_oleh_nama = $request->ubah_nama_yang_menyetujui;
            $cetakBuktiKas->is_bank = $request->ubah_is_bank;
            $cetakBuktiKas->lebih_dari_5_m = $request->ubah_lebih_dari_5_m;
            $cetakBuktiKas->lebih_dari_25_jt = $request->ubah_lebih_dari_25_jt;
            $cetakBuktiKas->updated_at = Carbon::now()->format('Y-m-d H:i:s');
            $cetakBuktiKas->save();

            return response()->json([
                'success' => true,
                'message' => 'Update data berhasil disimpan',
                'data' => $cetakBuktiKas,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cetakBuktiKas = CetakBuktiKas::find($id);
            if (!$cetakBuktiKas) {
                return response()->json([
                    'success' => false,
                    'message' => 'data tidak ditemukan'
                ], 404);
            }
            $cetakBuktiKas->delete();
            return response()->json([
                'success' => true,
                'message' => 'data berhasil di hapus',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }
}
