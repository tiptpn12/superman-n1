<?php

namespace App\Imports;

use App\Rekening;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RekeningsImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row['kode_kkb']) && !empty($row['kode_sap'])) {
                Rekening::create([
                    'company_id' => '',
                    'master_rekening_kode_kbb' => $row['kode_kkb'],
                    'master_rekening_kode_sap' => $row['kode_sap'],
                    'master_rekening_keterangan' => $row['keterangan'],
                    'master_rekening_status' => 1
                ]);
            }
        }
    }
}
