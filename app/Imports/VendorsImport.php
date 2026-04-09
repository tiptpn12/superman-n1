<?php

namespace App\Imports;

use App\Company;
use App\Vendor;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(\Illuminate\Support\Collection $rows)
    {
        $companyId = Company::pluck('company_id')->toArray();
        foreach ($rows as $row) {
            if (!empty($row['nama_vendor'])) {
                if (in_array($row['nama_company'], $companyId)) {
                    Vendor::create([
                        'master_vendor_nama' => $row['nama_vendor'],
                        'master_vendor_nama_bank' => $row['nama_bank'],
                        'master_vendor_rekening' => $row['no_rekening'],
                        'master_vendor_atas_nama' => $row['atas_nama'],
                        'master_vendor_status' => 1,
                        'company_id' => $row['nama_company'],
                    ]);
                }
            }
        }
    }
}
