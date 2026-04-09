<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterDevices extends Model
{
    //
    protected $table = 'master_device';
    protected $fillable = ['master_user_id', 'device_token'];
}
