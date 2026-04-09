<?php

namespace App\Http\Controllers;
use App\Exports_detail\Core_export;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class Laporan_excel_detail_controller extends Controller
{
    public function export(Request $request){
        return Excel::download(new Core_export($request->rentang_waktu, $request->status_bayar, $request->jenis_spp,$request->c_spp,$request->c_sppb,$request->c_sppn), 'Laporan.xlsx');
    }
}
