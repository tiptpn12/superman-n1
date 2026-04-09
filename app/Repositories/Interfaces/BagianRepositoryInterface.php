<?php

namespace App\Repositories\Interfaces;

interface BagianRepositoryInterface
{
    public function getBagianById($id);
    public function getBagianExcludingIdsByCompany($company);
}
