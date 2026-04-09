<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    protected $table = 'master_cost_center';
    protected $primaryKey = 'master_cost_center_id';
    protected $fillable = ['company_id','master_cost_center_kode', 'master_cost_center_keterangan', 'master_cost_center_status'];
}
