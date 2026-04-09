<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesController extends Controller
{
    public function index(ChartService $chartService)
    {
        $companyId = 5;
        $hakAkses = 1;

        try {
            $company = DB::table('master_company')
                ->where('company_Status', 1)
                ->whereNotNull('company_kode')
                ->select('company_nama as nama', 'company_id as id', 'company_kode as kode')
                ->get();

            $data = [];
            foreach ($company as $key => $value) {
                $result = $chartService->getProses($value->id, $hakAkses);

                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'divisi',
                    'jumlah' => $result['proses_divisi'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'akuntansi',
                    'jumlah' => $result['proses_akuntansi'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'anggaran',
                    'jumlah' => $result['proses_anggaran'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'perpajakan',
                    'jumlah' => $result['proses_perpajakan'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'miro',
                    'jumlah' => $result['proses_miro'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'kas bank',
                    'jumlah' => $result['proses_kas_bank'],
                ];
                $data[] = [
                    'nama_company' => $result['nama_region'],
                    'nama_proses' => 'pembayaran',
                    'jumlah' => $result['proses_pembayaran'],
                ];
            }
            return response()->json([
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }
}
