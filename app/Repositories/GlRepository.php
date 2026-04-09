<?php

namespace App\Repositories;

use App\GL;
use App\Repositories\Interfaces\GlRepositoryInterface;

class GlRepository implements GlRepositoryInterface
{

    public function getAll()
    {
        return GL::all();
    }
    public function getDistinctGlCodesByCompany($company)
    {
        return GL::where('company_id', '=', $company)
            ->groupBy('master_gl_kode')->whereNotNull('master_gl_kode')
            ->where('master_gl_kode', '<>', '')
            ->where('master_gl_kode', '<>', 0)
            ->get();
    }
}
