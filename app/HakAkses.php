<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HakAkses extends Model
{
    protected $table = 'master_hak_akses';
    protected $primaryKey = 'master_hak_akses_id';
    protected $fillable = ['master_hak_akses_nama', 'master_hak_akses_level','grup_ui_id', 'master_hak_akses_keterangan', 'master_hak_akses_status'];

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_hak_akses_id');
    // }
}
