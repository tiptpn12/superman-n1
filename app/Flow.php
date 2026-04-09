<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $table = 'master_flow';
    protected $primaryKey = 'flow_id';
    protected $fillable = ['flow_nama','flow_keterangan'];

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_bagian_id');
    // }
}
