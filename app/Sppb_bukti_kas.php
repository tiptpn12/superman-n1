<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sppb_bukti_kas extends Model
{
    protected $table = 'sppb_bukti_kas';
   protected $primaryKey = 'sppb_bukti_kas_id';
   protected $fillable = ['sppb_metode_pembayaran','sppb_urutan_bukti_kas','cek_giro','master_vendor_id','master_rekening_id','sppb_id'];
}
