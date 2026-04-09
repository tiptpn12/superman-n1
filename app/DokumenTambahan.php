<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumenTambahan extends Model
{
    protected $table = 'dokumen_tambahan';
    protected $primaryKey = 'dokumen_tambahan_id';
    protected $fillable = ['spp_id', 'dokumen_tambahan_nama','master_hak_akses_id','master_user_id'];
}
