<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $table = 'master_bank';
    protected $primaryKey = 'master_bank_id';
    protected $fillable = ['master_bank_no_rekening', 'master_bank_atas_nama', 'master_bank_nama', 'master_bank_status'];
}
