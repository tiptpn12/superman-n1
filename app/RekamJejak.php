<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RekamJejak extends Model
{
    protected $table = 'rekam_jejak';
    protected $primaryKey = 'rekam_jejak_id';
    protected $fillable = ['rekam_jejak_status', 'spp_id', 'master_user_id' ,'master_user_id_asal', 'master_user_id_tujuan', 'rekam_jejak_revisi'];
}
