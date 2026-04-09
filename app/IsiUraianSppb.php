<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiUraianSppb extends Model
{
    protected $table = 'sppb_uraian';
    protected $primaryKey = 'sppb_uraian_id';
    protected $fillable = ['sppb_isi_id','sppb_uraian_uraian','sppb_uraian_nominal','sppb_nominal_pajak','sppb_nominal_akhir'];
}
