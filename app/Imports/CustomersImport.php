<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row['kode'])) {
                Customer::create([
                    'company_id' => '',
                    'master_customer_kode_sap' => $row['kode'],
                    'master_customer_nama' => $row['keterangan'],
                    'master_customer_status' => 1,
                ]);
            }
        }
    }
}
