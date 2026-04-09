<?php

namespace App\Repositories;

use App\Repositories\Interfaces\ProsesRepositoryInterface;
use App\Spp;

class ProsesRepository implements ProsesRepositoryInterface
{
    public function GetProsesToDo($companyId, $hakAkses, array $status)
    {
        $data = Spp::where('company_id', $companyId)->where('sppd_posisi', $hakAkses)->whereBetween('sppd_status', $status)->get();

        return $data;
    }
    public function GetProsesRevisi($companyId, $hakAkses, $flowDetailByAkses, array $status)
    {
        return Spp::where('spp.company_id', $companyId)
            ->where('spp.sppd_revisi', '=', $hakAkses)
            ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)

            ->orWhere('spp.company_id', $companyId)
            ->where('spp.sppd_status', '=', $status[0])
            ->where('spp.sppd_posisi', '=', $hakAkses)

            ->get();
    }
}
