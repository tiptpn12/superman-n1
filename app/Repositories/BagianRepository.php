<?php

namespace App\Repositories;

use App\Bagian;
use App\Repositories\Interfaces\BagianRepositoryInterface;

class BagianRepository implements BagianRepositoryInterface
{

    public function getBagianById($id)
    {
        return Bagian::where('master_bagian_id', $id)->first();
    }

    public function getBagianExcludingIdsByCompany($company)
    {
        return Bagian::where([
            ['master_bagian_id', '!=', 10],
            ['master_bagian_id', '!=', 2]
        ])->where('company_id', '=', $company)->get();
    }
}
