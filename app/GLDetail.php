<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GLDetail extends Model
{
    protected $table = 'master_gl_detail';
    protected $primaryKey = 'id_gl_detail';
    protected $fillable = ['id_gl', 'id_bagian', 'master_gl_detail_budget','master_gl_detail_status'];
}
