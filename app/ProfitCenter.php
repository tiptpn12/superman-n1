<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfitCenter extends Model
{
    protected $table = 'master_profit_center';
    protected $primaryKey = 'master_profit_center_id';

    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['master_profit_center_kode', 'master_profit_unit', 'master_profit_center_status', 'company_id',];
}
