<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RKAP extends Model
{
    public $timestamps = false;
    protected $table = 'master_budget';
    protected $primaryKey = 'budget_id';
    protected $fillable = ['gl_id', 'bagian_id','jumlah_budget', 'budget_tahun'];

    // public function user()
    // {
    //     return $this->hasMany(User::class, 'foreign_key', 'master_hak_akses_id');
    // }
}
