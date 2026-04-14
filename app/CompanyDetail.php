<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    protected $table = 'master_company_detail';
    protected $primaryKey = 'master_company_detail_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $guarded = [
        'master_company_detail_id'
    ];

    public function company() {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}
