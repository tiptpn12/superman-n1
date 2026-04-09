<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CetakSPP extends Model
{
    protected $table = 'master_cetak_spp';
    protected $primaryKey = 'id';
    protected $fillable = [
        'company_id',
        'diperiksa_oleh_1',
        'diperiksa_oleh_3',
        'diperiksa_oleh_2',
        'disetujui_oleh',
        'tujuan_kepada',
        'tujuan_kepada_sevp',
        'keterangan',
        'status'
        // 'diperiksa_oleh_1_nama',
        // 'diperiksa_oleh_2_nama',
        // 'diperiksa_oleh_3_nama',
        // 'disetujui_oleh_nama',
    ];
}
