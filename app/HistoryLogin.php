<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HistoryLogin extends Model
{
    protected $table = 'history_login';
    protected $primaryKey = 'history_login_id';
    protected $fillable = ['master_user_id', 'history_login_waktu', 'history_login_status','detail_login_id'];
}
