<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SppBayar extends Model
{
   protected $table = 'spp_bayar';
   protected $primaryKey = 'spp_bayar_id';
   protected $fillable = ['sppb_kode_sap_bayar','sppb_kode_kbb_bayar','spp_bayar_nomor_bukti_kas','spp_bayar_rekening_bank','spp_bayar_bukti','spp_id'];
}
