<?php

namespace App\Repositories\Interfaces;

interface ProsesRepositoryInterface
{
    public function GetProsesToDo($companyId, $hakAkses, array $status);
    public function GetProsesRevisi($companyId, $hakAkses, $flowDetailByAkses, array $status);
}
