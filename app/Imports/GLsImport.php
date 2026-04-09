<?php

namespace App\Imports;

use App\GL;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GLsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            if (!empty($row['kode_gl'])) {
                GL::create([
                    'master_gl_kode' => $row['kode_gl'],
                    'master_gl_keterangan' => $row['keterangan'],
                    'master_gl_status' => 1
                ]);
            }
        }
    }
}
