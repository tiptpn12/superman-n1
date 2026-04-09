<?php

namespace App\Repositories\Interfaces;

interface GlRepositoryInterface
{
    public function getAll();
    public function getDistinctGlCodesByCompany($company);
}
