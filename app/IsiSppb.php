<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiSppb extends Model
{
    protected $table = 'sppb_isi';
    protected $primaryKey = 'sppb_isi_id';
    protected $fillable = ['sppb_isi_id','sppb_isi_no','sppb_id','master_kode_kbb','master_kode_vendor_id','master_gl_id','master_cost_center_id', 'master_profit_center_id','master_cash_flow_id'];
}
