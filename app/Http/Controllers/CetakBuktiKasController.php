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
                $grupId = session()->get('grup_ui');
                if (!in_array($this->hakAksesId, [1, 44, 45]) && !in_array($grupId, [8, 9])) {
                    return redirect('dashboard');
                }
                return $next($request);
            }
        });
    }
    private function getManagedCompanyIds()
    {
        $role = session()->get('hak_akses') ?? $this->hakAksesId;
        $currentCompany = session()->get('company');
        $username = session()->get('username') ?? $this->user;

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
        $role = session()->get('hak_akses');

        if ($role == "1" || $role == "44") {
            $cetakBuktiKas = CetakBuktiKas::with(['company'])->orderBy('company_id', 'asc')->get();
            
            // Get list of company IDs that already have configurations
            $existingCompanyIds = CetakBuktiKas::pluck('company_id')->toArray();
            
            $companies = Company::where('company_status', '!=', '0')
                ->whereNotIn('company_id', $existingCompanyIds)
                ->get();
        } else {
            $managedCompanyIds = $this->getManagedCompanyIds();
            
            $cetakBuktiKas = CetakBuktiKas::with(['company'])->whereIn('company_id', $managedCompanyIds)->get();
            
            $existingCompanyIds = $cetakBuktiKas->pluck('company_id')->toArray();
            
            $companies = Company::where('company_status', '!=', '0')
                ->whereIn('company_id', $managedCompanyIds)
                ->whereNotIn('company_id', $existingCompanyIds)
                ->get();
        }

        $view_data = [
            'hakAkses' => $this->hakAksesId,
            'cetakBuktiKas' => $cetakBuktiKas,
            'companies' => $companies
        ];
        return view('page.cetak_bukti_kas.index', $view_data);
    }

    public function getData()
    {
        $role = $this->hakAksesId;

        $query = CetakBuktiKas::with(['company']);

        if (!($role == "1" || $role == "44")) {
            $managedCompanyIds = $this->getManagedCompanyIds();
            $query->whereIn('company_id', $managedCompanyIds);
        }

        $data = $query->orderBy('company_id', 'asc')->get();

        // Group by company to show 1 row per company in the main table
        $grouped = $data->groupBy('company_id')->map(function ($items) {
            $first = $items->first();
            
            $valid_count = 0;
            $combinations = [
                ['b' => 0, 'm5' => 0, 'j25' => 0],
                ['b' => 0, 'm5' => 0, 'j25' => 1],
                ['b' => 1, 'm5' => 0, 'j25' => 0],
                ['b' => 1, 'm5' => 1, 'j25' => 0],
            ];
            
            foreach ($combinations as $combo) {
                $exists = $items->where('is_bank', $combo['b'])
                                ->where('lebih_dari_5_m', $combo['m5'])
                                ->where('lebih_dari_25_jt', $combo['j25'])
                                ->isNotEmpty();
                if ($exists) {
                    $valid_count++;
                }
            }

            return [
                'company_id' => $first->company_id,
                'company_nama' => $first->company->company_nama ?? 'N/A',
                'valid_scenarios' => $valid_count,
                'total_records' => $items->count(),
                'id' => $first->id 
            ];
        })->values();

        return response()->json($grouped);
    }

    public function getDataByCompany($company_id)
    {
        try {
            $scenarios = CetakBuktiKas::where('company_id', $company_id)->get();
            $company = Company::find($company_id);

            return response()->json([
                'success' => true,
                'company' => $company,
                'data' => $scenarios
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $company_id = $request->company;

        // Check if company already has configurations
        $exists = CetakBuktiKas::where('company_id', $company_id)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Perusahaan ini sudah terdaftar. Silakan gunakan fitur Edit untuk mengubah data.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $scenarios = $request->scenarios; // Expected array of 4 scenarios

            foreach ($scenarios as $item) {
                CetakBuktiKas::create([
                    'company_id' => $company_id,
                    'is_bank' => $item['is_bank'],
                    'lebih_dari_5_m' => $item['lebih_dari_5_m'],
                    'lebih_dari_25_jt' => $item['lebih_dari_25_jt'],
                    'dibuat_sub_bagian' => $item['dibuat_sub_bagian'] ?? null,
                    'dibuat_sub_bagian_nama' => $item['dibuat_sub_bagian_nama'] ?? null,
                    'diperiksa_oleh_sub_bagian' => $item['diperiksa_oleh_sub_bagian'],
                    'diperiksa_oleh_sub_bagian_nama' => $item['diperiksa_oleh_sub_bagian_nama'] ?? null,
                    'diperiksa_oleh_bagian' => $item['diperiksa_oleh_bagian'],
                    'diperiksa_oleh_bagian_nama' => $item['diperiksa_oleh_bagian_nama'] ?? null,
                    'disetujui_oleh' => $item['disetujui_oleh'],
                    'disetujui_oleh_nama' => $item['disetujui_oleh_nama'] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Seluruh skenario berhasil ditambahkan',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $company_id)
    {
        // $company_id here is actually company_id, not record id
        DB::beginTransaction();
        try {
            $scenarios = $request->scenarios; // Expected array of 4 scenarios

            // Hapus semua record lama untuk company ini agar tidak ada data sampah/duplikat
            CetakBuktiKas::where('company_id', $company_id)->delete();

            foreach ($scenarios as $item) {
                CetakBuktiKas::create([
                    'company_id' => $company_id,
                    'is_bank' => $item['is_bank'],
                    'lebih_dari_5_m' => $item['lebih_dari_5_m'],
                    'lebih_dari_25_jt' => $item['lebih_dari_25_jt'],
                    'dibuat_sub_bagian' => $item['dibuat_sub_bagian'] ?? null,
                    'dibuat_sub_bagian_nama' => $item['dibuat_sub_bagian_nama'] ?? null,
                    'diperiksa_oleh_sub_bagian' => $item['diperiksa_oleh_sub_bagian'],
                    'diperiksa_oleh_sub_bagian_nama' => $item['diperiksa_oleh_sub_bagian_nama'] ?? null,
                    'diperiksa_oleh_bagian' => $item['diperiksa_oleh_bagian'],
                    'diperiksa_oleh_bagian_nama' => $item['diperiksa_oleh_bagian_nama'] ?? null,
                    'disetujui_oleh' => $item['disetujui_oleh'],
                    'disetujui_oleh_nama' => $item['disetujui_oleh_nama'] ?? null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Seluruh skenario berhasil diperbarui',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }

    public function destroy($company_id)
    {
        try {
            CetakBuktiKas::where('company_id', $company_id)->delete();
            return response()->json([
                'success' => true,
                'message' => 'Semua skenario untuk perusahaan ini berhasil dihapus',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi Kesalahan: ' . $th->getMessage(),
            ], 500);
        }
    }
}
