<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IsiUraianSppn extends Model
{
    protected $table = 'sppn_uraian';
    protected $primaryKey = 'sppn_uraian_id';
    protected $fillable = ['sppn_isi_id','sppn_uraian_uraian','sppn_uraian_nominal'];
}
