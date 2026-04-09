<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SppbBuktiKasRepositoryInterface;
use App\Sppb_bukti_kas;

class SppbBuktiKasRepository implements SppbBuktiKasRepositoryInterface
{
    public function getBuktiKasBySppbId($sppb_id)
    {
        $data = Sppb_bukti_kas::where('sppb_bukti_kas.sppb_id', '=', $sppb_id)
            ->leftJoin('master_gl', 'sppb_bukti_kas.master_rekening_id', '=', 'master_gl.master_gl_id')
            ->select('master_gl.*', 'sppb_bukti_kas.*')->first();
        return $data;
    }
}
