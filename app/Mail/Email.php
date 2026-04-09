<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use App\Spp;
use App\Sppb;
use App\Sppn;
use DB;


class Email extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
        // dd($this->id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $id_user = session()->get('id');
        $bagian = session()->get('bagian');
        if ($bagian == 111){
            $nama = DB::table('master_user')->where('master_user_id',$id_user)->select('master_user.*')->first();
            $divisi = $nama->master_user_name;
            // dd($divisi);
        }else{
            $nama_bagian = DB::table('master_bagian')->where('master_bagian_id',$bagian)->select('master_bagian.*')->first();
            $divisi = $nama_bagian->master_bagian_nama;
            // dd($divisi);
        }
        $sppbID = Spp::where('spp_id', $this->id)->select('sppb_id')->first();
        $sppnID = Spp::where('spp_id', $this->id)->select('sppn_id')->first();
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
        return $this->markdown('emails.email',compact('sppbID','sppnID','sppb_nomor','sppn_nomor','bagian','divisi'));
    }
}
