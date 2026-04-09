<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sppn extends Model
{
    protected $table = 'sppn';
    protected $primaryKey = 'sppn_id';
    protected $fillable = ['master_user_id','master_bagian_id','master_bank_id','sppn_jenis','sppn_no','sppn_kwitansi',
    'sppn_referensi','sppn_ba_au_53','sppn_faktur_pajak','sppn_tanggal','sppn_metode_pembayaran','sppn_catatan','sppn_sp_opl'
    ,'sppn_status','sppn_jumlah','sppn_urutan','sppn_bulan','sppn_tahun','sppn_no_rek','sppn_atas_nama','sppn_nama_bank','sppn_karyawan_id','alasan_tidak_tf'];
    
}
