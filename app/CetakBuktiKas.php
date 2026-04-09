<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CetakBuktiKas extends Model
{
    protected $table = 'master_cetak_bukti_kas';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'dibuat_sub_bagian',
        'dibuat_sub_bagian_nama',
        'diperiksa_oleh_sub_bagian',
        'diperiksa_oleh_sub_bagian_nama',
        'diperiksa_oleh_bagian',
        'diperiksa_oleh_bagian_nama',
        'disetujui_oleh',
        'disetujui_oleh_nama',
        'is_bank',
        'lebih_dari_5_m',
        'lebih_dari_25_jt',
        'created_at',
        'updated_at',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
