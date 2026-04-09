<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailLogin extends Model
{
    protected $table = 'detail_login';
    protected $primaryKey = 'detail_login_id';
    protected $fillable = ['detail_login_ip','detail_login_hostname','detail_login_city','detail_login_region','detail_login_country','detail_login_country_code','detail_lohin_loc','detail_login_browser','detail_login_os'];
}
