<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spp extends Model
{
    // use

    protected $table = 'spp';
    protected $primaryKey = 'spp_id';
    protected $fillable = ['sppb_id', 'sppn_id', 'master_bagian_id', 'flow_id', 'company_id', 'spp_tanggal', 'sppd_proses', 'sppd_posisi', 'sppd_revisi', 'sppd_status', 'spp_buat', 'spp_kabag', 'spp_jalur_pajak', 'spp_jenis_sumber_dana', 'spp_no_dokumen', 'spp_status_ob', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp_status_lunas', 'spp_bukti_kas_bank'];
}
