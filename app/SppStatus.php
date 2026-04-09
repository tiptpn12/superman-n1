<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SppStatus extends Model
{
    protected $table = 'spp_status';
    protected $primaryKey = 'spp_status_id';
    protected $fillable = ['spp_status_nama'];
}
