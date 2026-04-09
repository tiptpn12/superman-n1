<?php

namespace App\Repositories\Interfaces;

interface SppdRepositoryInterface
{
    public function getSppToDoListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null);
    public function getSppRevisiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $flowDetailByAkses = null);
    public function getSppProgressListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $flowDetailByAkses = null);
    public function getSppSelesaiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null);
    public function getSppBatalAdmiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null);
}
