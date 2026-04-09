<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiSppn extends Model
{
    protected $table = 'sppn_isi';
    protected $primaryKey = 'sppn_isi_id';
    protected $fillable = ['sppn_id','master_kode_kbb','master_kode_vendor_id','master_gl_id','master_cost_center_id','master_profit_center_id','master_cash_flow_id'];
}
