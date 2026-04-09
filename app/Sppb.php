<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sppb extends Model
{
    protected $table = 'sppb';
    protected $primaryKey = 'sppb_id';
    protected $fillable = ['sppb_id','master_user_id','master_bagian_id','master_bank_id','sppb_jenis','sppb_no','sppb_kwitansi',
    'sppb_referensi','sppb_au_53','sppb_berita_acara','sppb_tanggal','sppb_metode_pembayaran','sppb_catatan','sppb_kontrak_perjanjian'
    ,'sppb_invoice','sppb_efaktur','sppb_status','sppb_berita_acara_file','sppb_total','sppb_sp_opl','sppb_faktur_pajak','sppb_urutan','sppb_bulan',
    'sppb_tahun','sppb_data_metpen','sppb_karyawan_id','sppb_atas_nama','sppb_nama_bank','sppb_no_rek','sppb_tidak_transfer','alasan_tidak_tf'];
    
}
