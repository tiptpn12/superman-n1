<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spp_revisi extends Model
{
    protected $table = 'spp_revisi';
    protected $primaryKey = 'spp_revisi_id';
    protected $fillable = ['spp_id','spp_revisi_keterangan'];
    
}
