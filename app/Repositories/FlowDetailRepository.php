<?php

namespace App\Repositories;

use App\Repositories\Interfaces\FlowDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FlowDetailRepository implements FlowDetailRepositoryInterface
{

    public function getFirstFlowOrderByRevisionStop()
    {
        return DB::table('master_flow_detail')
            ->where('flow_revisi_stop', 1)
            ->select('flow_detail_urutan')
            ->first();
    }

    public function getFlowIdsByCompanyAndAccess($company, $akses)
    {
        return DB::table('master_flow_detail')
            ->where('company_id', $company)
            ->where('flow_detail_urutan', $akses)
            ->leftjoin('master_company_detail', 'master_flow_detail.flow_id', '=', 'master_company_detail.flow_id')
            ->select('master_flow_detail.flow_id')
            ->pluck('master_flow_detail.flow_id');
    }

    public function getDetailFlowByHakAkses($akses)
    {
        return DB::table('master_flow_detail')->where('flow_detail_urutan', '=', $akses)->get();
    }
}
