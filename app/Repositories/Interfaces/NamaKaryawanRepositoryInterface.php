<?php

namespace App\Repositories\Interfaces;

interface NamaKaryawanRepositoryInterface
{
    public function getNamaKaryawanBySppbId($sppb_id);
    public function getNamaKaryawanBySppnId($sppn_id);
}
