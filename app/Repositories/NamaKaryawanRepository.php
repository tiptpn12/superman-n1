<?php

namespace App\Repositories;

use App\NamaKaryawanModel;
use App\Repositories\Interfaces\NamaKaryawanRepositoryInterface;

class NamaKaryawanRepository implements NamaKaryawanRepositoryInterface
{
    public function getNamaKaryawanBySppbId($sppb_id)
    {
        return NamaKaryawanModel::where('nama_karyawan.sppb_id', '=', $sppb_id)->first();
    }

    public function getNamaKaryawanBySppnId($sppn_id)
    {
        return NamaKaryawanModel::where('nama_karyawan.sppb_id', '=', $sppn_id)->first();
    }
}
