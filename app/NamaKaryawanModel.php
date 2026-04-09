<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NamaKaryawanModel extends Model
{
    protected $table = 'nama_karyawan';
    protected $primaryKey = 'karyawan_id';
    protected $fillable = ['sppb_id', 'sppn_id', 'karyawan_nama', 'karyawan_nama_bank','karyawan_no_rek','karyawan_alamat'];

}
