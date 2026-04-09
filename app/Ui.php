<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ui extends Model
{
    protected $table = 'master_grup_ui';
    protected $primaryKey = 'grup_id';
    protected $fillable = ['grup_nama', 'grup_keterangan'];

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_bagian_id');
    // }
}
