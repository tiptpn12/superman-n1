<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GL extends Model
{
    protected $table = 'master_gl';
    protected $primaryKey = 'master_gl_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = ['master_gl_kode', 'master_gl_keterangan', 'master_gl_status', 'master_gl_budget', 'company_id'];
}
