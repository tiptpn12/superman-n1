<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'master_customer';
    protected $primaryKey = 'master_customer_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = ['master_customer_kode_kbb', 'master_customer_kode_sap', 'master_customer_nama', 'master_customer_status', 'company_id'];
}
