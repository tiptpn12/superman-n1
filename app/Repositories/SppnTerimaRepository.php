<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SppnTerimaRepositoryInterface;
use App\SppnTerima;

class SppnTerimaRepository implements SppnTerimaRepositoryInterface
{
    public function getSppnTerimaBySppnId($sppn_id)
    {
        return SppnTerima::where('sppn_terima.sppn_id', '=', $sppn_id)
            ->select('sppn_terima.*')->first();
    }
}
