<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BahanJasa extends Model
{
    protected $table = 'master_bahan_jasa';
    protected $primaryKey = 'master_bahan_jasa_id';
    protected $fillable = ['master_bahan_jasa_jenis', 'master_bahan_jasa_budget', 'master_bahan_jasa_status'];
}
