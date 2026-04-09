<?php

namespace App\Repositories;

use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Vendor;

class VendorRepository implements VendorRepositoryInterface
{
    public function getVendorByCompanyId($companyId)
    {
        return Vendor::where('company_id', $companyId)->get();
    }
}
