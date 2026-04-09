<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SppnBuktiKasRepositoryInterface;
use App\Sppn_bukti_kas;

class SppnBuktiKasRepository implements SppnBuktiKasRepositoryInterface
{
    public function getBuktiKasBySppnId($sppn_id)
    {
        $data = Sppn_bukti_kas::where('sppn_bukti_kas.sppn_id', '=', $sppn_id)
            ->leftJoin('master_gl', 'sppn_bukti_kas.master_rekening_id', '=', 'master_gl.master_gl_id')
            ->select('master_gl.*', 'sppn_bukti_kas.*')->first();
        return $data;
    }
}
