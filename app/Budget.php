<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'master_budget';
    protected $primaryKey = 'budget_id';
    protected $fillable = ['gl_id', 'bagian_id','jumlah_budget'];
}
