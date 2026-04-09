<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sppn_bukti_kas extends Model
{
    protected $table = 'sppn_bukti_kas';
   protected $primaryKey = 'sppn_bukti_kas_id';
   protected $fillable = ['cek_giro','master_vendor_id','master_rekening_id','sppn_id'];
}
