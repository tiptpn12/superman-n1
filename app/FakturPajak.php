<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FakturPajak extends Model
{
    protected $table = 'faktur_pajak';
    protected $primaryKey = 'faktur_pajak_id';
    protected $fillable = ['sppb_id', 'sppn_id', 'faktur_pajak_nomor'];

}
