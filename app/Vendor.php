<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'master_vendor';
    protected $primaryKey = 'master_vendor_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['master_vendor_nama', 'master_vendor_nama_bank', 'master_vendor_rekening', 'master_vendor_status', 'master_vendor_atas_nama', 'company_id', 'master_vendor_alamat'];
}
