<?php

namespace App\Imports;

use App\CostCenter;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CostCentersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(\Illuminate\Support\Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row['kode_cost_center'])) {
                CostCenter::create([
                    'company_id' => '',
                    'master_cost_center_kode' => $row['kode_cost_center'],
                    'master_cost_center_keterangan' => $row['keterangan'],
                    'master_cost_center_status' => 1,
                ]);
            }
        }
    }
}
