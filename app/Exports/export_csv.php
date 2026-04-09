<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use App\Sppb;
use App\Sppn;
use App\Spp;
use App\Flow;
use App\IsiSppb;
use App\Spp_revisi;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\SppStatus;
use App\DokumenPendukungSppb;
use App\DokumenPendukungSppn;
use App\ProfitCenter;
use App\Rekening;
use App\CostCenter;
use App\DokumenTambahan;
use App\SumberDana;
use App\CashFlow;
use App\Bagian;
use App\Sppb_bukti_kas;
use App\Sppn_bukti_kas;
use App\SppProses;
use App\RekamJejak;
use App\SppbBayar;
use App\SppnTerima;
use App\Vendor;
use App\NamaKaryawanModel;
use PhpParser\Node\Expr\Cast\Array_;

class export_csv implements FromView
{
    public function view() : View
    {
        $data = DB::table('spp')
        ->leftjoin('sppb_bayar','sppb_bayar.sppb_id','spp.sppb_id')
        ->leftjoin('sppb_bukti_kas','sppb_bukti_kas.sppb_id','spp.sppb_id')
        ->leftjoin('sppb','sppb.sppb_id','spp.sppb_id')
        ->select(DB::raw('DATE_FORMAT(sppb_bayar_tanggal,"%Y%m%d") as date'),'sppb_bukti_kas.master_vendor_id','sppb.sppb_total')->get();
        // dd($data);
        return view('page.laporan.laporan_csv', compact('data'));
    }
}
