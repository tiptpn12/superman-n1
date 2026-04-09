<?php

namespace App\Exports_detail;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
class Core_export implements WithMultipleSheets
{
    use Exportable;
    protected $sheets;
    protected $rentang_waktus, $status_bayar, $jenis_spp,$c_spp,$c_sppb,$c_sppn;

    function __construct($rentang_waktus, $status_bayar, $jenis_spp,$c_spp,$c_sppb,$c_sppn) {
                $this->rentang_waktu = $rentang_waktus;
                $this->status_bayar = $status_bayar;
                $this->jenis_spp = $jenis_spp;
                $this->c_spp = $c_spp;
                $this->c_sppb = $c_sppb;
                $this->c_sppn = $c_sppn;


        }
   
    public function sheets(): array
    {
        $sheets= [];
        // dd($this->c_spp);
        if($this->c_sppb >= 0){
            $sheets[] = new Excel_SPPbExport($this->rentang_waktu,$this->status_bayar,$this->jenis_spp);
        }
        if($this->c_sppn >= 0){
            $sheets[] = new Excel_SPPnExport($this->rentang_waktu,$this->status_bayar,$this->jenis_spp);
        }
        if($this->c_spp >= 0){
            $sheets[] = new Excel_sppb_sppn($this->rentang_waktu,$this->status_bayar,$this->jenis_spp);
        }
       return $sheets;
    }

    
}
