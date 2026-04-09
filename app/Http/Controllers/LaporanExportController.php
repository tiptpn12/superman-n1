<?php

namespace App\Http\Controllers;

use App\Exports\LaporanSemuaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanExportController extends Controller
{
    public function export(Request $request)
    {
        // $data = LaporanSemuaExport::all();
        // $data = $request->rentang_waktu;
        // $data = $request->status_bayar;
        // $data = $request->jenis_spp;
        // $data = $request->c_spp;
        // $data = $request->c_sppb;
        // $data = $request->c_sppn;
        // dd("as");
        set_time_limit(1000);

        return Excel::download(new LaporanSemuaExport($request->rentang_waktu, $request->status_bayar, $request->jenis_spp, $request->c_spp, $request->c_sppb, $request->c_sppn), 'Laporan.xlsx');
    }
}
