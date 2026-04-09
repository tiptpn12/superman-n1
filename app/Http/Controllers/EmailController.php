<?php

namespace App\Http\Controllers;

use App\Mail\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Spp;
use App\Sppb;
use App\Sppn;
use App\User;


class EmailController extends Controller
{
    function __construct()
    {
        $this->id = session()->get('id');
        $this->username =session()->get('username');
    }

    function index($id)
    {   
        $bagian = session()->get('bagian');
        $nama_bagian = DB::table('master_bagian')->where('master_bagian_id',$bagian)->select('master_bagian.*')->first();
        $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
        $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
        $sppb_nomor = null;
        $sppn_nomor = null; 
        if($sppbID->sppb_id) {
            $getSppb = Sppb::where('sppb_id',$sppbID->sppb_id)
                                ->select('sppb_id', 'sppb_no')
                                ->first();
            $sppb_nomor = $getSppb->sppb_no;
        }
        if($sppnID->sppn_id) {
            $getSppn = Sppn::where('sppn_id',$sppnID->sppn_id)
            ->select('sppn_id', 'sppn_no')
            ->first();
            $sppn_nomor = $getSppn->sppn_no;
        }

        return view('emails.email', compact('sppbID','sppnID','sppb_nomor','sppn_nomor','idsppn','bagian','nama_bagian'));
    }
}
