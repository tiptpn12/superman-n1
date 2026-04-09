<?php

namespace App\Imports;

use App\ProfitCenter;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProfitCentersImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row['kode_profit_center'])) {
                ProfitCenter::create([
                    'company_id' => '',
                    'master_profit_center_kode' => $row['kode_profit_center'],
                    'master_profit_unit' => $row['keterangan_unit'],
                    'master_profit_center_status' => 1,
                ]);
            }
        }
    }
}
