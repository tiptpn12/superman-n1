<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\RoutesNotifications;

class User extends Model
{
    use Notifiable;

    protected $table = 'master_user';
    protected $primaryKey = 'master_user_id';
    protected $fillable = ['api_token','nomor_handphone','user_emails','master_bagian_id', 'master_hak_akses_id','master_cost_center_id', 'master_user_name', 'master_user_password', 'company_id',  'master_user_status','master_hak_akses_nama'];


    // public function hak_akses()
    // {
    //     return $this->belongsTo(HakAkses::class, 'foreign_key', 'master_hak_akses_id');
    // }

    // public function bagian()
    // {
    //     return $this->belongsTo(Bagian::class, 'foreign_key', 'master_bagian_id');
    // }
}
