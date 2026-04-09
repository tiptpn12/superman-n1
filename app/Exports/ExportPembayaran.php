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

class ExportPembayaran implements FromView
{
    protected $list_id = [];
    protected $status_upload;
    protected $flow_id;
    function __construct($list_id, $status_upload, $flow_id)
    {
        $this->list_id = $list_id;
        $this->status_upload = $status_upload;
        $this->flow_id = $flow_id;
    }
    public function view(): View
    {
        // dd($this->list_id);
        if ($this->status_upload == true) {
            $data_sudah = DB::table('spp')->where('spp.spp_bukti_kas_bank', '!=', NULL)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('sppb_bukti_kas', 'spp.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
                ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'karyawan_nama', 'sppb.sppb_metode_pembayaran', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc');
        } else {
            $data_sudah = DB::table('spp')->where('spp.spp_bukti_kas_bank', '=', NULL)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('sppb_bukti_kas', 'spp.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
                ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'karyawan_nama', 'sppb.sppb_metode_pembayaran', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc');
        }

        // $sum = $data_sudah->sum('sppb_total');
        if ($this->list_id)
            $data_sudah->whereIn('spp.spp_id', $this->list_id);
        $getdata = $data_sudah->get();
        $totalNominalSppb = 0;
        $totalNominalSppn = 0;
        foreach ($getdata as $key => $value) {
            $totalNominalSppb += $value->sppb_total;
        }
        foreach ($getdata as $key => $value) {
            $totalNominalSppn += $value->sppn_jumlah;
        }
        $sum_spp = $totalNominalSppb + $totalNominalSppn;
        // dd($totalNominalSppb);
        $master_bagian = Bagian::All();
        $sppb = Sppb::All();
        $sppn = Sppn::All();
        $sppb_uraian = IsiUraianSppb::All();
        $sppn_uraian = IsiUraianSppn::All();
        $spp = Spp::All();






        return view('page.pembayaran.pembayaran_export', compact('getdata', 'master_bagian', 'totalNominalSppb', 'totalNominalSppn'));
    }
    /**
    
     */
    // public function collection()
    // {
    // $data_sudah = DB::table('spp')->whereNotNull('spp_bukti_kas_bank')->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
    // ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
    // ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
    // ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
    // ->select('spp_id','spp.sppb_id','sppd_posisi','spp.sppn_id','sppd_revisi','sppd_status','spp_bukti_kas_bank','master_bagian_nama','spp.sppb_id','spp_status_posisi','spp_status_bayar','spp_status_terima','spp.sppn_id','spp_status_proses','spp_status_bayar',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal','sppb_total','sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob',DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
    // ->groupBy('spp_id','spp.sppb_id','spp_status_posisi','spp_status_bayar','spp_status_terima','spp.sppn_id','spp_status_proses','spp_status_bayar','tanggal','sppb_no','sppb_tanggal','sppb_total','sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob')
    // ->orderBy('spp_tanggal','desc');
    // $getdata = $data_sudah->get();
    // return $getdata;
    // }
}
