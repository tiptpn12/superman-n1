<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    protected $table = 'master_bagian';
    protected $primaryKey = 'master_bagian_id';
    protected $fillable = ['company_id', 'master_bagian_nama', 'master_bagian_kode', 'master_bagian_kepala_bagian', 'master_bagian_jabatan', 'master_bagian_keterangan', 'master_bagian_status'];

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_bagian_id');
    // }
}
