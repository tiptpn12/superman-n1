<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $table = 'master_cash_flow';
    protected $primaryKey = 'master_cash_flow_id';
    protected $fillable = ['master_cash_flow_kode', 'master_cash_flow_keterangan', 'master_cash_flow_status'];
}
