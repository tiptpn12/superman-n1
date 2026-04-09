<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SppbBayarRepositoryInterface;
use App\SppbBayar;

class SppbBayarRepository implements SppbBayarRepositoryInterface
{
    public function getSppbBayarBySppbId($sppb_id)
    {
        return SppbBayar::where('sppb_bayar.sppb_id', '=', $sppb_id)
            ->select('sppb_bayar.*')->first();
    }
}
