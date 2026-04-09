<?php

namespace App\Http\Controllers;

use App\CostCenter;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class ApiCostCenterController extends Controller
{
    function __construct()
    {



    }

    public function getCostCenter()
    {
        try {
            $data = CostCenter::all();

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function createCostCenter(Request $request)
    {
        try {
            $data = $request->validate([
                'master_cost_center_kode' => 'required|string|max:255',
                'master_cost_center_keterangan' => 'required|string|max:255',
                'master_cost_center_status' => 'required|integer',
                'master_cost_budget' => 'required|string',

            ]);

            $costCenter = CostCenter::create($data);

            return response()->json(['data' => $costCenter], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat membuat cost center. ' . $e->getMessage()], 500);
        }
    }

    public function editCostCenter(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'master_cost_center_kode' => 'string|max:255',
                'master_cost_center_keterangan' => 'string|max:255',
                'master_cost_center_status' => 'integer',
                'master_cost_budget' => 'string',
                // Anda mungkin perlu validasi khusus untuk format uang
            ]);

            $costCenter = CostCenter::find($id);

            if (!$costCenter) {
                return response()->json(['error' => 'Data cost center tidak ditemukan.'], 404);
            }

            $costCenter->update($data);

            return response()->json(['data' => $costCenter], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengedit cost center. ' . $e->getMessage()], 500);
        }
    }

    public function deleteCostCenter($id)
    {
        try {
            $costCenter = CostCenter::find($id);

            if (!$costCenter) {
                return response()->json(['error' => 'Data cost center tidak ditemukan.'], 404);
            }

            $costCenter->delete();

            return response()->json(['message' => 'Data cost center berhasil dihapus.'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat menghapus cost center. ' . $e->getMessage()], 500);
        }
    }




    // APi


}
