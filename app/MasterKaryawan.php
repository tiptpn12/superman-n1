<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterKaryawan extends Model
{
    protected $table = 'master_karyawan';
    protected $primaryKey = 'master_karyawan_id';
    protected $fillable = ['master_karyawan_nama', 'master_karyawan_bank', 'master_karyawan_rekening', 'master_bagian_id'];
}
