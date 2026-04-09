<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    protected $table = 'master_sumber_dana';
    protected $primaryKey = 'sumber_dana_id';
    protected $fillable = ['nama_sumber_dana'];
}
