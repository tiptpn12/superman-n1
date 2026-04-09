<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumenPendukungSppn extends Model
{
    protected $table = 'dokumen_pendukung_sppn';
    protected $primaryKey = 'dokumen_pendukung_sppn_id';
    protected $fillable = ['sppn_id', 'dokumen_pendukung_sppn_nama'];
}
