<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SppnTerima extends Model
{
    protected $table = 'sppn_terima';
    protected $primaryKey = 'sppn_terima_id';
    protected $fillable = ['sppn_terima_tanggal','sppn_terima_nomor_bukti_kas','master_rekening_id','sppn_terima_bukti','sppn_id'];
}
