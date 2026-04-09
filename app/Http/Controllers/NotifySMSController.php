<?php

namespace App\Http\Controllers;

use App\UserModel;
use App\TransaksiModel;
use Illuminate\Http\Request;
use App\Spp;
use App\Sppb;
use App\Sppn;
use App\User;
use DB;
// use Illuminate\Support\Str;


class NotifySMSController extends Controller
{

    public function __construct()
    {
        if (!session()->has('status') || session()->get('status') != 'loginBon') {
            redirect('/login')->send();
        };
    }
    
    public static function send($id)
    {   
        $sppd = Spp::where('spp_id', $id)->select('spp_buat','sppd_proses','flow_id','sppd_posisi','sppd_revisi','sppd_status')->first();
        $user = User::where('master_hak_akses_id','=',$sppd->sppd_posisi)->select('master_user.*')->first();
        // $nomor_hp = (string)$nomor_hp1;
        // dd((string)$user->nomor_handphone);
        $nomor_hp = preg_replace("/^0/", "62", $user->nomor_handphone);
        // dd($nomor_hp);
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
        if($sppb_nomor != NULL && $sppn_nomor != NULL){
            $message = 'Ada SPP masuk dengan nomor '.$sppb_nomor .'dan '. $sppn_nomor . ' dari bagian '. $divisi . '.';
        }elseif($sppb_nomor != NULL && $sppn_nomor == NULL)
        {
            $message = 'Ada SPP masuk dengan nomor '.$sppb_nomor .' dari bagian '. $divisi . '.';
        }else{
            $message = 'Ada SPP masuk dengan nomor '.$sppn_nomor .' dari bagian '. $divisi . '.';
        }
        $url = "http://122.50.7.30/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('7074706e32303232'),
            'hp' => $nomor_hp,
            'message' => $message
        );
        $curl = curl_init();

        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        $result = curl_exec($curl);

        if(curl_errno($curl) !== 0) {
                // error_log('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
                // $errlog  = error_log('cURL error when connecting to ' . $url . ': ' . curl_error($curl));
                // var_dump($errlog);
                // var_dump("masuk error");
                // die();
        }

        // curl_close($curl);
        // var_dump("nggak error");
        // var_dump($result);
        // die();
    }

    public function postNotifyReject($array_notify)
    {
        $getNoPengBon       = $array_notify['getNoPengBon'];
        $getNoHPUserPem     = $array_notify['getNoHPUserPem'];
        $getRejectUser      = $array_notify['getRejectUser'];
        $message = 'Si BoBa - Notifikasi. \n\nTransaksi Bon barang yang Anda buat, yaitu No Pengajuan ' . $getNoPengBon . ' telah direject oleh ' . $getRejectUser . '.' . '\n\n-Admin Si BoBa';
        $url = "http://103.16.199.187/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('313233343536373839'),
            'hp' => $getNoHPUserPem,
            'message' => $message
        );
        $curl = curl_init();
        
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_exec($curl);
        curl_close($curl);
    }

    public function postNotifyRevisi($array_notify)
    {
        $getNoPengBon       = $array_notify['getNoPengBon'];
        $getNoHPUserPem     = $array_notify['getNoHPUserPem'];
        $getRevisiUser      = $array_notify['getRevisiUser'];
        $message = 'Si BoBa - Notifikasi. \n\nTransaksi Bon barang yang Anda buat, yaitu No Pengajuan ' . $getNoPengBon . ' telah direvisi oleh ' . $getRevisiUser . '.' . '\n\n-Admin Si BoBa';
        $url = "http://103.16.199.187/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('313233343536373839'),
            'hp' => $getNoHPUserPem,
            'message' => $message
        );
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_exec($curl);
        curl_close($curl);

    }


    public function postNotifyCreateBon($dataTransaksiUser, $getUserNext)
    {
    	// check data
    	$no_pengajuan_transaksi = $dataTransaksiUser['transaksi_no_peng'];    
        $user_id_pembuat        = TransaksiModel::find($dataTransaksiUser['transaksi_id']);
        $get_nama_pembuat       = $user_id_pembuat->transaksi_pembuat;
    	$hp = $getUserNext['no_hp'];
    	// dd($get_nama_pembuat, $hp, $no_pengajuan_transaksi, $user_id_pembuat);
    	$message = 'Si BoBa - Notifikasi. \n\nTransaksi Bon barang yang dibuat oleh ' . $get_nama_pembuat . ', yaitu No Pengajuan ' . $no_pengajuan_transaksi . ', diperlukan Approve/Reject/Revisi oleh Anda. \n\n-Admin Si BoBa';

        $url = "http://103.16.199.187/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('313233343536373839'),
            'hp' => $hp,
            'message' => $message
        );
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_exec($curl);
        curl_close($curl);

    }

    public function postNotifyApprove($array_notify)
    {
        $getUserNamaPem = $array_notify['getUserNamaPem'];
        $getNoPengBon   = $array_notify['getNoPengBon'];
        $getNoHPUserApp = $array_notify['getNoHPUserApp'];

        $message = 'Si BoBa - Notifikasi. \n\nTransaksi Bon barang yang dibuat oleh ' . $getUserNamaPem . ', yaitu No Pengajuan ' . $getNoPengBon . ', diperlukan Approve/Reject/Revisi oleh Anda. \n\n-Admin Si BoBa';

        $url = "http://103.16.199.187/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('313233343536373839'),
            'hp' => $getNoHPUserApp,
            'message' => $message
        );
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_exec($curl);
        curl_close($curl);

    }

    public function postNotifyCreatorBon($array_notify)
    {

        $getUserNamaPem = $array_notify['getUserNamaPem'];
        $getNoPengBon   = $array_notify['getNoPengBon'];
        $getNoHPUserPem = $array_notify['getNoHPUserPem'];
        $getUserNameApp = $array_notify['getUserNameApp'];

        $message = 'Si BoBa - Notifikasi. \n\nTransaksi Bon barang yang Anda buat, yaitu No Pengajuan ' . $getNoPengBon . ' telah diapprove oleh ' . $getUserNameApp . '.' . '\n\n-Admin Si BoBa';

        $url = "http://103.16.199.187/masking/send_post.php";
        $rows = array (
            'username' => hex2bin('7074706e31325f736d73'),
            'password' => hex2bin('313233343536373839'),
            'hp' => $getNoHPUserPem,
            'message' => $message
        );
        $curl = curl_init();
        curl_setopt( $curl, CURLOPT_URL, $url );
        curl_setopt( $curl, CURLOPT_POST, TRUE );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query($rows) );
        curl_setopt( $curl, CURLOPT_HEADER, FALSE );
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_exec($curl);
        curl_close($curl);

    }

}
