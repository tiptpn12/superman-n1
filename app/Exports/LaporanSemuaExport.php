<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use DB;

class LaporanSemuaExport implements WithMultipleSheets
{
    use Exportable;
    protected $sheets;
    protected $rentang_waktus, $status_bayar, $jenis_spp, $c_spp, $c_sppb, $c_sppn;

    function __construct($rentang_waktus, $status_bayar, $jenis_spp, $c_spp, $c_sppb, $c_sppn)
    {
        $this->rentang_waktu = $rentang_waktus;
        $this->status_bayar = $status_bayar;
        $this->jenis_spp = $jenis_spp;
        $this->c_spp = $c_spp;
        $this->c_sppb = $c_sppb;
        $this->c_sppn = $c_sppn;


    }

    public function sheets(): array
    {
        $c_spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')->where('spp.spp_status_ob', '!=', 2)->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*')->count();
        $c_sppb = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')->where('spp.spp_status_ob', '!=', 2)->where('spp.sppn_id', '=', null)->select('spp.*')->count();
        $c_sppn = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')->where('spp.spp_status_ob', '!=', 2)->where('spp.sppb_id', '=', null)->select('spp.*')->count();

        $sheets = [];

        if ($this->c_sppb >= 0) {
            $sheets[] = new LaporanSPPbExport($this->rentang_waktu, $this->status_bayar, $this->jenis_spp);
        }
        if ($this->c_sppn >= 0) {
            $sheets[] = new LaporanSPPnExport($this->rentang_waktu, $this->status_bayar, $this->jenis_spp);
        }
        if ($this->c_spp >= 0) {
            $sheets[] = new LaporanExport($this->rentang_waktu, $this->status_bayar, $this->jenis_spp);
        }
        return $sheets;
    }


}
