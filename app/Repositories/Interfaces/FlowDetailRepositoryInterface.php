<?php

namespace App\Repositories\Interfaces;

interface FlowDetailRepositoryInterface
{
    public function getFlowIdsByCompanyAndAccess($company, $akses);
    public function getFirstFlowOrderByRevisionStop();
    public function getDetailFlowByHakAkses($akses);
}
