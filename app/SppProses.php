<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SppProses extends Model
{
    protected $table = 'spp_proses';
    protected $primaryKey = 'spp_proses_id';
    protected $fillable = ['spp_id','spp_proses_operator_bagian','spp_proses_kepala_bagian','spp_proses_petugas_penerima',
                            'spp_prses_petugas_pajak','spp_proses_petugas_verifikasi','spp_proses_petugas_sap_miro',
                            'spp_proses_petugas_kas_dan_bank','spp_proses_petugas_pembayaran'];
}
