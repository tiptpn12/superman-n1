<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'master_rekening';
    protected $primaryKey = 'master_rekening_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    protected $fillable = ['master_rekening_kode_kbb', 'company_id', 'master_rekening_kode_sap', 'master_rekening_keterangan', 'master_rekening_status'];
}
