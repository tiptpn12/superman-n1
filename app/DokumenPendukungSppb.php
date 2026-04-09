<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumenPendukungSppb extends Model
{
    protected $table = 'dokumen_pendukung_sppb';
    protected $primaryKey = 'dokumen_pendukung_sppb_id';
    protected $fillable = ['sppb_id', 'dokumen_pendukung_sppb_nama'];
}
