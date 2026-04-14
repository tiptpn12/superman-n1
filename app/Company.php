<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'master_company';
    protected $primaryKey = 'company_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'company_nama',
        'company_kode',
        'domisili_company',
        'company_status',
        'created_at',
        'updated_at'
    ];

    public function cetakBuktiKas()
    {
        return $this->hasMany(CetakBuktiKas::class, 'company_id', 'company_id');
    }

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_bagian_id');
    // }

    public function companyDetail() {
        return $this->hasMany(CompanyDetail::class, 'company_id', 'company_id');
    }

    public function bagian() {
        return $this->hasMany(Bagian::class, 'company_id', 'company_id');
    }
}
