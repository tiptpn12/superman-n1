<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SppbBayar extends Model
{
    protected $table = 'sppb_bayar';
   protected $primaryKey = 'sppb_bayar_id';
   protected $fillable = ['sppb_bayar_tanggal','sppb_bayar_nomor_bukti_kas','master_rekening_id','sppb_bayar_bukti','sppb_id'];
}
