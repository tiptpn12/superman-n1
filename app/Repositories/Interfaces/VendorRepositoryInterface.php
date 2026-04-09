<?php

namespace App\Repositories\Interfaces;

interface VendorRepositoryInterface
{
    public function getVendorByCompanyId($companyId);
}
