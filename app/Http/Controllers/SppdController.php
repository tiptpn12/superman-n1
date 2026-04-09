<?php

namespace App\Http\Controllers;

use App\Bagian;
use App\CashFlow;
use App\CostCenter;
use App\DokumenPendukungSppb;
use App\DokumenPendukungSppn;
use App\DokumenTambahan;
use App\FakturPajak;
use App\Flow;
use App\GL;
use App\Helpers\API;
use App\Http\Controllers\NotifySMSController;
use App\IsiSppb;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\Mail\Email;
use App\NamaKaryawanModel;
use App\Notifications\NewSppNotification;
use App\ProfitCenter;
use App\RekamJejak;
use App\Rekening;
use App\Spp;
use App\Spp_revisi;
use App\Sppb;
use App\Sppb_bukti_kas;
use App\SppbBayar;
use App\Sppn;
use App\Sppn_bukti_kas;
use App\SppnTerima;
use App\SppProses;
use App\SppStatus;
use App\SumberDana;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use File;
use GuzzleHttp\Client;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Notification;
use PDF;
use Redirect;

class SppdController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            // $this->mail = session()->get('user_emails');
            // dd($this->mail);
            // return $next($request);
            if ($this->user == null) {

                return redirect('login');
            } else {
                return $next($request);
            }
        });
    }
    public function upload_bukti_kas(Request $request)
    {

        $current = date('His-dmY');
        $request->validate([
            'file_bukti_kas' => 'mimes:pdf,jpg,png,jpeg|max:55000',
        ]);
        $file_bukti_kas = $request->file('file_bukti_kas');
        $buktikas_file_name = str_replace("'", '', $file_bukti_kas->getClientOriginalName());
        $nama_file_bukti_kas = $current . '-' . $buktikas_file_name;
        $file_bukti_kas->move('dokumen/', $nama_file_bukti_kas);

        $id = $request->bukti_spp_id;

        $spp = Spp::find($id);
        if ($spp->spp_bukti_kas_bank == null) {
            $spp->spp_bukti_kas_bank = $nama_file_bukti_kas;
        } else {
            File::delete(public_path('dokumen/' . $spp->spp_bukti_kas_bank));
            $spp->spp_bukti_kas_bank = $nama_file_bukti_kas;
        }
        $spp->save();

        return back();
    }

    public function upload(Request $request, $id)
    {

        $current = date('His-dmY');
        $spp = DB::table('spp')->where('spp_id', $id)->select('spp_kabag')->first();
        DB::beginTransaction();
        try {
            if ($spp->spp_kabag !== null) {

                if ($request->upload_file == 'file_baru') {
                    File::delete(public_path('dokumen/' . $request->file_lama));
                    $request->validate([
                        'spp_kabag' => 'mimes:pdf,jpg,png,jpeg|max:55000',
                    ]);
                    $sppkabag = $request->file('spp_kabag');

                    $sppkabag_file_name = str_replace("'", '', $sppkabag->getClientOriginalName());
                    $sppkabags = $current . '-' . $sppkabag_file_name;
                    $sppkabag->move('dokumen/', $sppkabags);

                    $spp = Spp::find($id);
                    $spp->spp_kabag = $sppkabags;
                    $spp->save();
                } else {
                }
            } else {
                $request->validate([
                    'spp_kabag' => 'mimes:pdf,jpg,png,jpeg|max:55000',
                ]);
                $sppkabag = $request->file('spp_kabag');

                $sppkabag_file_name = str_replace("'", '', $sppkabag->getClientOriginalName());
                $sppkabags = $current . '-' . $sppkabag_file_name;
                $sppkabag->move('dokumen/', $sppkabags);

                $spp = Spp::find($id);
                $spp->spp_kabag = $sppkabags;
                $spp->save();
            }
            DB::commit();
            return redirect('sppd/send/' . $id);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect::back()
                ->with('error_code', 5);
        }
    }

    public function selesai($id)
    {
        $akses = Session::get('hak_akses');
        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $id;
        $rekam_jejak->master_user_id = $akses;
        $rekam_jejak->master_user_id_asal = Session::get('id');
        $rekam_jejak->rekam_jejak_status = 4;
        $rekam_jejak->save();

        $sppd = Spp::find($id);
        $sppd->sppd_status = 100;
        $sppd->save();
        return back();
    }

    public function batal($id)
    {
        $level = Session::get('hak_akses');
        $spp = Spp::find($id);
        $spp->sppd_status = 4;
        $spp->save();

        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $id;
        $rekam_jejak->master_user_id = $level;
        $rekam_jejak->master_user_id_asal = Session::get('id');
        $rekam_jejak->rekam_jejak_status = 5;
        $rekam_jejak->save();

        return back();
    }

    public function upload_no_doc(Request $request)
    {
        $sppd = Spp::where('spp_id', $request->no_doc_id)->first();
        $sppd->spp_no_dokumen = $request->no_doc;
        $sppd->save();

        return redirect('sppd');
    }

    public function kirim($id, Request $request)
    {
        $jenis_sppb = [];

        $akses = Session::get('hak_akses');
        // $id_user = Session::get('id');
        $sppd = DB::table("spp")->where('spp_id', $id)
            ->select('flow_id', 'spp_buat', 'sppd_proses', 'flow_id', 'sppd_posisi', 'sppd_revisi', 'sppd_status')
            ->first();
        //dd($sppd);
        $flow_stop = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_revisi_stop', '=', 1)
            ->select('flow_detail_urutan')
            ->first();
        $flow_proses = DB::table('master_flow_detail')
            ->where('flow_detail_urutan', $sppd->sppd_posisi)
            ->select('flow_detail_id')
            ->first();
        $flow_proses_stop = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_revisi_stop', '=', 1)
            ->select('flow_detail_id')
            ->first();
        // dd($flow_proses->flow_detail_id-1);
        // $email = DB::table('master_user')
        // ->where('master_user_id','=',$id_user)
        // ->select('user_emails')->first();
        // dd($email);
        $master_flow = DB::table('master_flow')
            ->where('flow_detail_urutan', $sppd->sppd_posisi)
            ->where('master_flow.flow_id', $sppd->flow_id)
            ->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')
            ->select('master_flow_detail.*')
            ->first();
        //dd($master_flow);
        $id_flow_stop = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
            ->where('master_flow_detail.flow_revisi_stop', '=', 1)
            ->select('master_flow_detail.*')->first();
        $flow_detail_id = DB::table('master_flow_detail')
            ->where('flow_detail_id', $master_flow->flow_detail_id + 1)
            ->where('flow_id', $sppd->flow_id)
            ->select('master_flow_detail.*')
            ->first();
        // dd($flow_detail_id);
        $master_flow_detail_all = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
            ->select('master_flow_detail.*')->get();
        // $revisi = RekamJejak::where('rekam_jejak.spp_id','=',$id)->select('rekam_jejak_revisi')->latest('rekam_jejak_revisi')->value('rekam_jejak_revisi');
        // $revisi = DB::table('rekam_jejak')->where('rekam_jejak.spp_id','=',$id)->select('rekam_jejak_revisi')->latest('rekam_jejak_revisi')->first();
        // dd($master_flow->flow_detail_id+1);
        $flow_stop = DB::table('master_flow_detail')
            ->where('flow_id', '=', $sppd->flow_id)
            ->select('flow_detail_id')
            ->first();
        //dd($flow_stop);
        $sppd_proses = $sppd->sppd_proses + 1;
        $posisi_selanjutnya = $flow_detail_id->flow_detail_urutan;
        $revisi_stop = $id_flow_stop->flow_detail_id - $flow_stop->flow_detail_id;
        // $posisi_alur = DB::table('master_flow_detail')
        //             ->where('master_flow_detail.flow_detail_urutan','=',$sppd->sppd_posisi)
        //             ->select('master_flow_detail.*')->first();
        $posisi_kirim_balik = DB::table('master_flow_detail')
            ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
            ->select('flow_detail_id')
            ->first();
        // dd($revisi);
        // dd($mail_kirim->user_emails);
        // dd($id_flow_stop->flow_detail_id);
        // dd($posisi_selanjutnya);
        // dd($id_flow_stop->flow_detail_urutan);
        //dd(($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $sppd->sppd_posisi == $id_flow_stop->flow_detail_urutan),($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $posisi_kirim_balik->flow_detail_id < $id_flow_stop->flow_detail_id),($sppd->sppd_proses == 0 && $sppd->sppd_status == 3),($sppd->sppd_posisi == 20 && $sppd->sppd_revisi > 11),($akses == $id_flow_stop->flow_detail_urutan && !is_null($sppd->sppd_revisi)));
        DB::beginTransaction();
        try {
            if ($sppd->sppd_status == 3 && $sppd->sppd_posisi == 38) {
                $sppd = Spp::find($id);
                $sppd->save();
                // if ($akses == 38) {
                $sppd->sppd_posisi = 39;
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_revisi = 0;
                $sppd->sppd_status = 1;
                // }
                // else {
                //     $sppd->sppd_posisi = $posisi_selanjutnya;
                //     $sppd->sppd_proses = $sppd_proses;
                //     $sppd->sppd_status = 1;
                // }

                // Simpan perubahan pada model Spp
                $sppd->save();

                // Rekam jejak
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 1;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            } elseif ($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $sppd->sppd_posisi == $id_flow_stop->flow_detail_urutan) {
                $sppd_proses_revisi = $sppd->sppd_proses - $revisi_stop;
                // dd($sppd_proses_revisi);
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_posisi = $sppd->spp_buat;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 33;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // dd("ini loooo");
                // // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    ////Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    ////Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();


            } elseif ($sppd->sppd_proses != 0 && $sppd->sppd_status == 3 && $posisi_kirim_balik->flow_detail_id < $id_flow_stop->flow_detail_id) {
                $sppd_proses_revisi = $sppd->sppd_proses - 1;
                if ($akses = 10) {
                    $posisi_selanjutnya_revisi = $sppd->spp_buat;
                } else {
                    $posisi_selanjutnya_revisi = $master_flow[$sppd->sppd_proses - 1]->flow_detail_urutan;
                }
                //dd($posisi_selanjutnya_revisi);
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_posisi = $posisi_selanjutnya_revisi;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 33;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // dd($mail_kirim);
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // dd("ini kabeh");
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();

                // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // Mail::to($sppd->sppd_posisi)->send(new Email());
            } elseif ($sppd->sppd_proses == 0 && $sppd->sppd_status == 3) {
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $posisi_selanjutnya;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->rekam_jejak_status = 1;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // dd($rekam_jejak);
                // Mail::to($sppd->sppd_posisi)->send(new Email());
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // dd($mail_kirim);
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // // dd("ini ya lagi");
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();


            } elseif ($sppd->sppd_posisi == 20 && $sppd->sppd_revisi > 11) {
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $sppd->sppd_revisi;
                $sppd->sppd_revisi = 0;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 1;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // dd($rekam_jejak);
                // Mail::to($sppd->sppd_posisi)->send(new Email());
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // dd($mail_kirim);
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // // dd("ini ya lagi");
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();



            }
            //jika yang kirim revisi dan flow stop sama lanjut ke pos berikutnya
            elseif ($akses == $id_flow_stop->flow_detail_urutan && $sppd->sppd_revisi == $akses && !is_null($sppd->sppd_revisi)) {
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $posisi_selanjutnya;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 1;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
            }
            //jika yang kirim revisi dan flow stop beda langsung kirim balik ke revisi
            elseif ($akses == $id_flow_stop->flow_detail_urutan && !is_null($sppd->sppd_revisi)) {

                foreach ($master_flow_detail_all as $key => $g) {
                    if ($sppd->sppd_revisi == $g->flow_detail_urutan) {
                        $spp_balik_revisi = $key;
                    }
                }
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $spp_balik_revisi;
                $sppd->sppd_posisi = $sppd->sppd_revisi;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->rekam_jejak_status = 1;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // Mail::to($sppd->sppd_posisi)->send(new Email());
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // dd($mail_kirim);
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // dd('1');
                // // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // // dd("ini coa");
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();


            } else {
                // dd('cek');
                $sppd = Spp::find($id);
                $sppd->sppd_proses = $sppd_proses;
                $sppd->sppd_posisi = $posisi_selanjutnya;
                $sppd->spp_status_posisi = 2;
                $sppd->sppd_status = 1;
                $sppd->save();
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 1;
                // $rekam_jejak->rekam_jejak_revisi = $revisi;
                $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
                $rekam_jejak->save();
                // Mail::to($sppd->sppd_posisi)->send(new Email());
                // $mail_kirim = User::where('master_hak_akses_id', '=', $sppd->sppd_posisi)->select('user_emails')->first();
                // // dd($mail_kirim);
                // // Mail::to($mail_kirim->user_emails)->send(new Email($id));
                // // Mail::to('dimasw347@gmail.com')->send(new Email($id));
                // // dd($master_flow->flow_detail_urutan);
                // // dd("ini ya");
                // $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
                // $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
                // $sppb_nomor = null;
                // $sppn_nomor = null;
                // if ($sppbID->sppb_id) {
                //     $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                //         ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                //         ->first();
                //     $sppb_nomor = $getSppb->sppb_no;
                //     $jenis_sppb = $getSppb->sppb_jenis;
                // }
                // if ($sppnID->sppn_id) {
                //     $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                //         ->select('sppn_id', 'sppn_no')
                //         ->first();
                //     $sppn_nomor = $getSppn->sppn_no;
                // }

                // $isSppCampuran = false;
                // if ($sppb_nomor && $sppn_nomor) $isSppCampuran = true;
                // $notificationData = [
                //     'spp_id' => $id,
                //     'username' => session()->get('username'),
                //     'message' => "Ada SPP Masuk ",
                //     'sppb_nomor' => $sppb_nomor,
                //     'sppn_nomor' => $sppn_nomor,
                //     'isSppCampuran' => $isSppCampuran,
                //     'jenis_sppb' => $jenis_sppb
                // ];

                // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
                // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
                if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                } else {
                    // $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                    // $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    //     $userNotifable->master_user_id,
                    //     "Ada SPP Masuk ",
                    //     "SPP dengan nomor " . $sppb_nomor,
                    //     $id,
                    //     "masuk"
                    // );
                    //Notification::send($userNotifable, new NewSppNotification($notificationData));
                }



                // dump($sppd->sppd_posisi);
                // dump($sppd->master_bagian_id);
                // dump($userNotifable);
                // dump($sendNotifToDevice);
                // die();


            }
            // dd($sppd->sppd_revisi == $master_flow_detail_all->flow_detail_urutan);
            // if($akses == 2){
            // $rekam_jejak = new RekamJejak;
            // $rekam_jejak->spp_id = $id;
            // $rekam_jejak->master_user_id = $akses;
            // $rekam_jejak->master_user_id_asal = Session::get('id');
            // $rekam_jejak->rekam_jejak_status = 1;
            // $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
            // $rekam_jejak->save();
            // }else{
            //     $rekam_jejak = new RekamJejak;
            //     $rekam_jejak->spp_id = $id;
            //     $rekam_jejak->master_user_id = $akses;
            //     $rekam_jejak->master_user_id_asal = Session::get('id');
            //     $rekam_jejak->rekam_jejak_status = 1;
            //     $rekam_jejak->master_user_id_tujuan = $sppd->sppd_posisi;
            //     $rekam_jejak->save();
            //     // dd($rekam_jejak);
            // }

            //dikirim = 1 , diterima = 2, direvisi = 3, setelah revisi = 4 ;
            // NotifySMSController::send($id);
            //dd($sendNotifToDevice);

            //dump("tes");
            //die();

            DB::commit();
            return redirect('sppd');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect::back()
                ->with('error_code', 5);
        }
    }

    public function accept(Request $request, $id)
    {
        $akses = Session::get('hak_akses');
        $ids = explode(',', $id);

        try {
            foreach ($ids as $id) {
                $spp = Spp::where('spp_id', $id)->select('sppd_proses', 'flow_id', 'sppd_posisi', 'sppd_revisi', 'sppd_status')->first();

                // Rekam jejak
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $akses;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 6;
                $rekam_jejak->master_user_id_tujuan = $spp->sppd_posisi;
                $rekam_jejak->save();

                if ($spp->sppd_status == 3) {
                    $sppd = Spp::find($id);
                    $sppd->sppd_status = 5;
                    $sppd->save();
                } else {
                    $sppd = Spp::find($id);
                    $sppd->sppd_status = 2;
                    $sppd->save();
                }
            }


            // $spp = Spp::where('spp_id', $id)->select('sppd_proses', 'flow_id', 'sppd_posisi', 'sppd_revisi', 'sppd_status')->first();
            // // dd($spp->sppd_status );
            // $rekam_jejak = new RekamJejak;
            // $rekam_jejak->spp_id = $id;
            // $rekam_jejak->master_user_id = $akses;
            // $rekam_jejak->master_user_id_asal = Session::get('id');
            // $rekam_jejak->rekam_jejak_status = 6;
            // $rekam_jejak->master_user_id_tujuan = $spp->sppd_posisi;
            // $rekam_jejak->save();

            // if ($spp->sppd_status == 3) {
            //     $sppd = Spp::find($id);
            //     $sppd->sppd_status = 5;
            //     $sppd->save();
            // } else {
            //     $sppd = Spp::find($id);
            //     $sppd->sppd_status = 2;
            //     $sppd->save();
            // }



            //dikirim = 1 , diterima = 2, direvisi = 3, setelah revisi = 4 ;

            // return redirect('sppd');
            return redirect('sppd')->with('success', 'PP berhasil diterima.');
        } catch (\Exception $e) {
            return redirect('sppd')->with('error', 'Terjadi kesalahan saat menerima PP.');
        }
    }

    public function revisi(Request $request, $id)
    {


        DB::beginTransaction();
        try {
            $akses = Session::get('hak_akses');
            $id_tujuan = Session::get('id');
            $jenis_sppb = [];
            // $jenis_revisi = $request->query('jenis_revisi');


            $sppd = Spp::where('spp_id', $id)->select('sppd_proses', 'flow_id', 'sppd_posisi', 'sppd_revisi', 'sppd_status', 'master_bagian_id')->first();
            // dd($sppd);
            $master_flow = Flow::where('master_flow.flow_id', $sppd->flow_id)->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->get();
            $master_flow_detail = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
                ->where('master_flow_detail.flow_revisi_stop', '=', 1)
                ->select('master_flow_detail.*')->first();
            // dd($master_flow_detail->flow_detail_urutan);
            $master_flow_detail_all = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
                ->select('master_flow_detail.*')->get();
            $posisi_spp = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
                ->where('master_flow_detail.flow_detail_urutan', '=', $sppd->sppd_posisi)
                ->select('master_flow_detail.*')
                ->first();
            $posisi_kirim_balik = DB::table('master_flow_detail')
                ->where('master_flow_detail.flow_id', '=', $sppd->flow_id)
                ->select('flow_detail_id')
                ->first();
            // dd($sppd->flow_id);
            $flow_proses = DB::table('master_flow_detail')->where('flow_detail_urutan', $sppd->sppd_posisi)->select('flow_detail_id')->first();
            $flow_proses_stop = DB::table('master_flow_detail')->where('master_flow_detail.flow_revisi_stop', '=', 1)->select('flow_detail_id')->first();
            $sppbID = Spp::where('spp_id', $id)->select('sppb_id')->first();
            $sppnID = Spp::where('spp_id', $id)->select('sppn_id')->first();
            $sppb_nomor = null;
            $sppn_nomor = null;
            if ($sppbID->sppb_id) {
                $getSppb = Sppb::where('sppb_id', $sppbID->sppb_id)
                    ->select('sppb_id', 'sppb_no', 'sppb_jenis')
                    ->first();
                $sppb_nomor = $getSppb->sppb_no;
                $jenis_sppb = $getSppb->sppb_jenis;
            }
            if ($sppnID->sppn_id) {
                $getSppn = Sppn::where('sppn_id', $sppnID->sppn_id)
                    ->select('sppn_id', 'sppn_no')
                    ->first();
                $sppn_nomor = $getSppn->sppn_no;
            }

            $isSppCampuran = false;
            if ($sppb_nomor && $sppn_nomor)
                $isSppCampuran = true;
            $notificationData = [
                'spp_id' => $id,
                'username' => session()->get('username'),
                'message' => "Ada SPP Masuk ",
                'sppb_nomor' => $sppb_nomor,
                'sppn_nomor' => $sppn_nomor,
                'isSppCampuran' => $isSppCampuran,
                'jenis_sppb' => $jenis_sppb
            ];

            foreach ($master_flow_detail_all as $key => $g) {

                if ($master_flow_detail->flow_detail_urutan == $g->flow_detail_urutan) {
                    $spp_revisi = $key;
                }
            }
            // dd($spp_revisi);
            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $id;
            $rekam_jejak->master_user_id = $sppd->sppd_posisi;
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->master_user_id_tujuan = $akses;
            $rekam_jejak->rekam_jejak_revisi = $request->revisi;
            $rekam_jejak->rekam_jejak_status = 33;
            $rekam_jejak->save();
            // dd($id_tujuan);

            $jenis_revisi = $request->input('jenis_revisi');
            #Revisi dari kadiv bagian mundur satu langkah ke kasubdiv, lalu ke operator bagian
            if ($jenis_revisi == 'kas_bank' && $sppd->sppd_proses != 0 && $sppd->sppd_status == 2) {
                $posisi = DB::table('master_flow_detail')
                    ->where('master_flow_detail.flow_detail_id', $posisi_spp->flow_detail_id - 1)
                    ->select('master_flow_detail.*')->first();
                $sppd_proses_revisi = $sppd->sppd_proses - 1;
                $sppd = Spp::find($id);
                $sppd->sppd_posisi = $posisi->flow_detail_urutan;
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_status = 3;
                $sppd->sppd_revisi = $akses;
                $sppd->save();
            }
            //  elseif ($jenis_revisi == 'spp' && $sppd->sppd_proses != 0 && $sppd->sppd_status == 2) {
            //     $posisi = DB::table('master_flow_detail')->where('master_flow_detail.flow_detail_id', $posisi_spp->flow_detail_id - 1)
            //         ->select('master_flow_detail.*')->first();
            //     $sppd_proses_revisi = $sppd->sppd_proses - 1;
            //     $sppd = Spp::find($id);
            //     $sppd->sppd_revisi = $akses;
            //     $sppd->sppd_posisi = $master_flow_detail->flow_detail_urutan;
            //     $sppd->sppd_proses = $spp_revisi;
            //     $sppd->sppd_status = 3;
            //     $sppd->save();
            //     $userNotifable = User::where('master_hak_akses_id', $master_flow_detail->flow_detail_urutan)->first();
            //     #Revisi dari kadiv bagian mundur satu langkah ke kasubdiv, lalu ke operator bagian
            // }
            elseif ($sppd->sppd_proses != 0 && $sppd->sppd_status == 2 && $posisi_spp->flow_detail_id < $master_flow_detail->flow_detail_id) {
                $posisi = DB::table('master_flow_detail')->where('master_flow_detail.flow_detail_id', $posisi_spp->flow_detail_id - 1)
                    ->select('master_flow_detail.*')->first();
                // dd($posisi);
                $sppd_proses_revisi = $sppd->sppd_proses - 1;
                $sppd = Spp::find($id);
                // $sppd->sppd_revisi = $akses;
                $sppd->sppd_posisi = $posisi->flow_detail_urutan;
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_status = 3;
                $sppd->sppd_revisi = $akses;
                $sppd->save();
                // dd('hihi');
                // dd( $master_flow_detail->flow_detail_urutan);
                // $mail_kirim = User::where('master_hak_akses_id','=',$sppd->sppd_posisi)->select('user_emails')->first();
                // dd($mail_kirim->user_emails);
                // Mail::to($mail_kirim->user_emails)->send(new Email());
            } elseif ($sppd->sppd_proses != 0 && $sppd->sppd_status == 2 && $posisi_kirim_balik->flow_detail_id > $master_flow_detail->flow_detail_id) {
                $sppd_proses_revisi = $sppd->sppd_proses - 1;
                $posisi_selanjutnya_revisi = $master_flow[$sppd->sppd_proses - 1]->flow_detail_urutan;
                $sppd = Spp::find($id);
                // $sppd->sppd_revisi = $akses;
                $sppd->sppd_posisi = $posisi_selanjutnya_revisi;
                $sppd->sppd_proses = $sppd_proses_revisi;
                $sppd->sppd_status = 3;
                $sppd->sppd_revisi = $akses;
                $sppd->save();
                // dd('haha');
                // $mail_kirim = User::where('master_hak_akses_id','=',$sppd->sppd_posisi)->select('user_emails')->first();
                // dd($mail_kirim);
                // Mail::to($mail_kirim->user_emails)->send(new Email());
            } else {
                $sppd = Spp::find($id);
                $sppd->sppd_revisi = $akses;
                $sppd->sppd_posisi = $master_flow_detail->flow_detail_urutan;
                // dd($master_flow_detail->flow_detail_urutan);
                $sppd->sppd_proses = $spp_revisi;
                $sppd->sppd_status = 3;
                $sppd->save();
                $userNotifable = User::where('master_hak_akses_id', $master_flow_detail->flow_detail_urutan)->first();            // $mail_kirim = User::where('master_hak_akses_id','=',$sppd->sppd_posisi)->select('user_emails')->first();
                // dd($mail_kirim);
                // Mail::to($mail_kirim->user_emails)->send(new Email());
            }


            // kirim notifikasi ke user dengan hak akses sesuai flow posisi selanjutnya
            // bisa dilihat di table master_flow_detail kolom flow_detail_urutan
            if ($flow_proses->flow_detail_id < $flow_proses_stop->flow_detail_id - 1) {
                $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->where('master_bagian_id', $sppd->master_bagian_id)->select('master_user_id')->first();
                $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor " . $sppb_nomor,
                    $id,
                    "masuk"
                );
                //Notification::send($userNotifable, new NewSppNotification($notificationData));
            } else {
                $userNotifable = User::where('master_hak_akses_id', $sppd->sppd_posisi)->select('master_user_id')->first();
                $sendNotifToDevice = ApiController::pushNotificationsToDevice(
                    $userNotifable->master_user_id,
                    "Ada SPP Masuk ",
                    "SPP dengan nomor " . $sppb_nomor,
                    $id,
                    "masuk"
                );
                //Notification::send($userNotifable, new NewSppNotification($notificationData));
            }
            DB::commit();
            // NotifySMSController::send($id);
            //dikirim = 1 , diterima = 2, direvisi = 3, setelah revisi = 4 ;
            // dd($rekam_jejak);
            return redirect('sppd');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect::back()
                ->with('error_code', 7);
        }
    }


    public function bukti_kas(Request $request, $id = null)
    {
        // dd($request->form_a,$request->form_a,$id,$request->nomor_bukti_kas_sppb,$request->rekening_bank_sppb,$request->penerima,$request->alamat_sppb);
        $form_a = $request->get('form_a');
        $form_b = $request->get('form_b');
        if ($form_a == null) {
            $form = $form_b;
        } else {
            $form = $form_a;
        }
        if ($id == null) {
            $id_sppb = $request->id_sppb;
            $id_sppn = $request->id_sppn;

            $bayar = new Sppb_bukti_kas;
            $bayar->sppb_id = $id_sppb ?? "";
            $bayar->cek_giro = $request->nomor_bukti_kas_sppb ?? "";
            $bayar->sppb_metode_pembayaran = $request->cetak_bukti_kas_metode_pembayaran ?? "";
            $bayar->master_rekening_id = $request->rekening_bank_sppb ?? "";
            $bayar->master_vendor_id = $request->penerima ?? "";
            $bayar->alamat_sppb = $request->alamat_sppb ?? "";
            $bayar->sppb_bukti_kas_tanggal = $request->tanggal_cetak_sppb ?? "";
            $bayar->save();

            $terima = new Sppn_bukti_kas;
            $terima->sppn_id = $id_sppn ?? "";
            $terima->cek_giro = $request->nomor_bukti_kas_sppn ?? "";
            $terima->master_rekening_id = $request->rekening_bank_sppn ?? "";
            $terima->master_vendor_id = $request->diterima_dari ?? "";
            $terima->alamat_sppn = $request->alamat_sppn ?? "";
            $terima->sppn_bukti_kas_tanggal = $request->tanggal_cetak_sppn ?? "";
            $terima->save();

            return response()->json([
                'data' => [
                    'bayar' => $bayar,
                    'terima' => $terima,
                ],
            ]);
        }
        $idsppb = DB::table('spp')->where('spp.sppb_id', '=', $id)->select('spp_id')->first();
        $idsppn = DB::table('spp')->where('spp.sppn_id', '=', $id)->select('spp_id')->first();
        // dd($form);
        if ($form == 0) {

            $bayar = new Sppb_bukti_kas;
            $bayar->sppb_id = $id;
            $bayar->cek_giro = $request->nomor_bukti_kas_sppb;
            $bayar->sppb_metode_pembayaran = $request->cetak_bukti_kas_metode_pembayaran;
            $bayar->master_rekening_id = $request->rekening_bank_sppb;
            $bayar->master_vendor_id = $request->penerima;
            $bayar->alamat_sppb = $request->alamat_sppb;
            $bayar->sppb_bukti_kas_tanggal = $request->tanggal_cetak_sppb;
            $bayar->save();

            return response()->json([
                'sppb' => $bayar
            ], 201);
        } else if ($form == 1) {
            $terima = new Sppn_bukti_kas;
            $terima->sppn_id = $id;
            $terima->cek_giro = $request->nomor_bukti_kas_sppn;
            $terima->master_rekening_id = $request->rekening_bank_sppn;
            $terima->master_vendor_id = $request->diterima_dari;
            $terima->alamat_sppn = $request->alamat_sppn;
            $terima->sppn_bukti_kas_tanggal = $request->tanggal_cetak_sppn;
            $terima->save();

            return response()->json([
                'sppn' => $terima
            ], 201);
        }

        return response()->json([
            'error' => [
                'message' => "error"
            ]
        ], 400);
    }

    public function update_bukti_kas(Request $request, $id)
    {
        $form_a = $request->form_a;
        $form_b = $request->form_b;
        if ($form_a == null) {
            $form = $form_b;
        } else {
            $form = $form_a;
        }
        if ($form = 2) {
            if (isset($request->nomor_bukti_kas_sppb)) {
                $form = 0;
            } else {
                $form = 1;
            }
        }

        $idsppb = DB::table('spp')->where('spp.spp_id', '=', $id)->select('sppb_id')->first();
        $idsppn = DB::table('spp')->where('spp.spp_id', '=', $id)->select('sppn_id')->first();

        //dd($idsppb,$idsppn,$form,$request->all());
        if ($form == 0) {
            $bayar = Sppb_bukti_kas::where('sppb_id', $idsppb->sppb_id)->first();
            $bayar->cek_giro = $request->nomor_bukti_kas_sppb;
            $bayar->sppb_metode_pembayaran = $request->cetak_bukti_kas_metode_pembayaran;
            $bayar->master_rekening_id = $request->rekening_bank_sppb;
            $bayar->master_vendor_id = $request->penerima;
            $bayar->alamat_sppb = $request->alamat_sppb;
            $bayar->sppb_bukti_kas_tanggal = $request->tanggal_cetak_sppb;

            $bayar->save();
        } else if ($form == 1) {
            $terima = Sppn_bukti_kas::where('sppn_id', $idsppn->sppn_id)->first();
            $terima->cek_giro = $request->nomor_bukti_kas_sppn;
            $terima->master_rekening_id = $request->rekening_bank_sppn;
            $terima->master_vendor_id = $request->diterima_dari;
            $terima->alamat_sppn = $request->alamat_sppn;
            $terima->sppn_bukti_kas_tanggal = $request->tanggal_cetak_sppn;

            $terima->save();
        }

        return redirect('sppd');
    }

    public function index_dashboard() {}
    public function index()
    {
        $grup_flow = DB::table('master_flow_detail')->where('flow_revisi_stop', 1)->select('flow_detail_urutan')->first();
        $stopper = $grup_flow->flow_detail_urutan;
        /////dd($stopper);
        $grup_id = Session::get('grup_ui');
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $akses = Session::get('hak_akses');
        $petugas_pp = Session::get('petugas_pp');
        $company = Session::get('company');

        // $flow = DB::table('master_flow_detail')
        //     ->where('company_id', $company)
        //     ->where('flow_detail_urutan', $akses)
        //     ->leftjoin('master_company_detail', 'master_flow_detail.flow_id', '=', 'master_company_detail.flow_id')
        //     ->select('master_flow_detail.flow_id')
        //     ->pluck('master_flow_detail.flow_id');
        $id = Session::get('id');
        $company = Session::get('company');
        // $master_flow = Flow::where('flow_id', 1)->first();
        // $vendor = Vendor::where('company_id', $company)->get();
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
        $b = Bagian::where([['master_bagian_id', '!=', 10], ['master_bagian_id', '!=', 2]])
            ->where('company_id', '=', $company)
            ->get();
        $hak_akses = DB::table('master_hak_akses')
            ->get();
        // $rekening = GL::where('company_id', '=', $company)
        //     ->groupBy('master_gl_kode')->whereNotNull('master_gl_kode')
        //     ->where('master_gl_kode', '<>', '')
        //     ->where('master_gl_kode', '<>', 0)
        //     ->get();

        // $gl = GL::All();
        $sppb_bayar = [];
        $sppn_terima = [];
        $sppb_cetak_bukti_kas = [];
        $sppn_cetak_bukti_kas = [];
        $posisi_dinamis = [];
        $datapenerima = [];
        $data_diterima_dari = [];
        $sppb_bayar_to_do_list = [];
        $sppn_terima_to_do_list = [];
        $sppb_cetak_bukti_kas_to_do_list = [];
        $sppn_cetak_bukti_kas_to_do_list = [];
        $posisi_dinamis_to_do_list = [];
        $datapenerima_to_do_list = [];
        $data_diterima_dari_to_do_list = [];
        $sppb_bayar_revisi = [];
        $sppn_terima_revisi = [];
        $sppb_cetak_bukti_kas_revisi = [];
        $sppn_cetak_bukti_kas_revisi = [];
        $posisi_dinamis_revisi = [];
        $datapenerima_revisi = [];
        $data_diterima_dari_revisi = [];
        $spp_revisi = [];
        $spp_terlewati = [];
        $to_do_list = [];
        $revisi = [];
        //$data = [];
        $revisi = [];
        $to_do_list = [];
        $posisi_batal_to_do_list = [];
        $posisi_to_do_list = [];
        $status_to_do_list = [];
        $rekam_jejak_to_do_list = [];
        $posisi_batal_revisi = [];
        $posisi_revisi = [];
        $status_revisi = [];
        $rekam_jejak_revisi = [];
        $data_batal_admi = [];

        // $user = DB::table('master_user')->where('master_user.master_user_id', $id)->select('master_user.*')->first();
        if (empty($rekam_jejak_batal)) {
            $data_batal = [];
            $rekam_jejak_batal = [];
            $posisi_batal = '';
        }
        if (empty($rekam_jejak_selesai)) {
            //$data_selesai = [];
            $sppb_bayar_selesai = [];
            $rekam_jejak_selesai = [];
            $sppn_terima_selesai = [];
        }

        if (isset($status)) {
            foreach ($status as $s) {
                // $posisi[] = DB::table('master_hak_akses')->where('master_hak_akses_id','=',$s->master_user_id)->select('master_hak_akses_keterangan')->first();

                if ($s !== null) {
                    $status_revisi[] = 'Revisi oleh';
                } else {
                    $status_revisi[] = '';
                }
            }
        } else {
            //$data = [];
            $posisi = '';
            $status = '';
            $status_revisi = [];
            $rekam_jejak = [];
            $sppb_bayar = [];
            $sppn_terima = [];
            $sppb_cetak_bukti_kas = [];
            $sppn_cetak_bukti_kas = [];
            $data_diterima_dari = [];
            $datapenerima = [];
            $posisi_dinamis = [];
            $spp_revisi = [];
        }
        // $data = collect($data)->sortByDesc('tanggal')->reverse()->toArray();
        // dd(Session::all());
        $index = 0;
        $index_cetak = Session::get('index_cetak');
        $id_cetak = Session::get('id_cetak');
        if (empty($index_cetak)) {
            $index_cetak = 0;
            $id_cetak = 0;
        }

        // if ($level == 1) {
        //     foreach ($rekam_jejak as $k => $r) {
        //         foreach ($r as $l => $s) {
        //             if ($s->master_user_id == 1 || $s->master_user_id == 2 && $s->master_user_id_tujuan < 2 || $s->rekam_jejak_revisi !== null && $s->master_user_id !== 2) {

        //                 $rekam_jejak_ob[$k][] = $s;
        //             }
        //         }
        //     }
        //     $a = '';
        //     if (isset($rekam_jejak_ob)) {
        //         foreach ($rekam_jejak_ob as $k => $r) {
        //             foreach ($r as $l => $s) {
        //                 if ($s->rekam_jejak_revisi !== null && $s->master_user_id !== 2 || $s->master_user_id_tujuan == 1) {
        //                     $a = $s->asal;
        //                 }
        //                 if ($s->rekam_jejak_revisi !== null && $s->master_user_id == 2 || $s->master_user_id_tujuan == 1) {
        //                     $asal[$k][$l] = $a;
        //                 } else {
        //                     $asal[$k][$l] = null;
        //                 }
        //             }
        //         }
        //         $rekam_jejak = $rekam_jejak_ob;
        //     } else {
        //         $asal = [];
        //     }
        // } else {
        //     $asal = [];
        // }
        //dd($rekam_jejak_ob,$rekam_jejak,$asal);
        // dd($asal);

        // dd($data_diterima_dari);
        // dd($sppb_cetak_bukti_kas);
        //dd($data_diterima_dari_to_do_list,$datapenerima_to_do_list,$to_do_list);
        //dd($data_batal_admi, $sppn_terima_revisi, $sppn_terima_to_do_list, $sppb_bayar_revisi, $sppb_bayar_to_do_list, $data_diterima_dari_revisi, $data_diterima_dari_to_do_list, $datapenerima_revisi, $datapenerima_to_do_list, $sppn_cetak_bukti_kas_to_do_list, $sppb_cetak_bukti_kas_to_do_list, $sppb_cetak_bukti_kas_revisi, $sppn_cetak_bukti_kas_revisi, $posisi_batal_to_do_list, $posisi_to_do_list, $rekam_jejak_to_do_list, $status_to_do_list, $posisi_batal_revisi, $posisi_revisi, $rekam_jejak_revisi, $status_revisi, $posisi_dinamis_to_do_list, $posisi_dinamis_revisi, $stopper, $countrevisi, $counttodolist, $to_do_list, $revisi, $id_cetak, $index_cetak, $vendor, $index, $data_selesai, $data_batal, $rekam_jejak_batal, $data_selesai, $spp_revisi, $rekam_jejak_selesai, $posisi_dinamis, $datapenerima, $data_diterima_dari, $sppb_bayar_selesai, $sppb_cetak_bukti_kas, $sppn_cetak_bukti_kas, $sppn_terima_selesai, $data, $posisi, $status, $status_revisi, $rekam_jejak, $sppb_bayar, $sppn_terima, $posisi_batal, $b, $bagian_id, $rekening, $gl);

        return view('page.spp.sppd', compact('rekam_jejak_revisi', 'stopper', 'id_cetak', 'index_cetak', 'index', 'data_batal', 'rekam_jejak_batal', 'b', 'bagian_id', 'grup_id', 'akses', 'hak_akses'));
    }
    public function getSppdPosisiOptions()
    {
        $customOrder = [
            34, // op_divisi
            35, // asisten_akuntansi_perpajakan
            36, // asisten_pajak
            37, // asisten_verifikasi
            43, // asisten_verifikasi tk
            42, // asisten anggaran
            41, // asisten miro
            38, // as kas bank
            39  // as pembayaran
        ];

        $posisiOptions = DB::table('master_hak_akses')
            ->join('spp', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
            ->select('master_hak_akses.master_hak_akses_id', 'master_hak_akses.master_hak_akses_nama')
            ->distinct()
            ->orderByRaw('FIELD(master_hak_akses.master_hak_akses_id, ' . implode(',', $customOrder) . ')')
            ->get();

        return response()->json($posisiOptions);
    }

    public function getRegionalOptions()
    {
        $regionalOptions = DB::table('master_company')
            ->join('spp', 'master_company.company_id', '=', 'spp.company_id')
            ->select('master_company.company_id', 'master_company.company_nama')
            ->distinct()
            ->orderBy('master_company.company_id')
            ->get();

        return response()->json($regionalOptions);
    }

    public function rekam_jejak($id)
    {
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            return redirect('user/logout');
        }
        $rekam_jejak = DB::table('rekam_jejak')->where('spp_id', '=', $id)
            ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_id')
            ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_id')
            ->leftjoin('master_user', 'master_user_id_asal', '=', 'master_user.master_user_id')
            ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan', 'master_user.master_user_name as user_name_asal')
            ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
        // dd($rekam_jejak);
        return view('page.spp.spp_rekam_jejak', compact('rekam_jejak'));
    }

    // public function index(Request $request)
    // {
    //     $bagian = Session::get('bagian');
    //     $level = Session::get('level');
    //     $unit_id = Session::get('unit_id');
    //     $vendor = Vendor::All();
    //     $bagianbaru = Bagian::All();
    //     $vendorbaru = Vendor::All();
    //     $rekening = Rekening::All();
    //     $gl = GL::All();

    //     $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
    //     $rekening = Rekening::All();
    //     $b = Bagian::where([['master_bagian_id','!=',10],['master_bagian_id','!=',2]])->get();

    //     if ($request->has('bagian')) {
    //         $data = Bagian::where('master_bagian_nama', 'LIKE', '%' .$request->bagian. '%');
    //     }

    //     if (request('bagian')) {
    //         $vendor->where('Bagian', 'like', '%' . request('bagian') . '%');
    //     }

    //     $revisiCounter = [];
    //     if ($level == 1) {
    //         $revisiCounter = DB::table('spp')
    //                     ->where('spp.master_bagian_id', '=', $bagian)
    //                     ->where('spp.spp_status_ob', '=', 2)
    //                     ->where('spp.spp_status_proses', 0) // hanya ketika udah berada pada operator
    //                     ->get();
    //     } elseif ($level == 2) {
    //         $revisiCounter = DB::table('spp')
    //                     ->where('spp_proses.spp_proses_petugas_penerima', '=', 1)
    //                     ->where('spp.master_unit_id', '=', $unit_id)
    //                     ->where('spp.master_bagian_id', '!=', 2)
    //                     ->where('spp.spp_status_ob', '=', 2)
    //                     ->where('spp.spp_status_proses', '!=', 0) // hanya ketika sudah ada di penerima
    //                     ->leftJoin('spp_proses', 'spp_proses.spp_id', '=', 'spp.spp_id')
    //                     ->get();
    //     }

    //     $index = 0;
    //     $index_cetak =Session::get('index_cetak');
    //     $id_cetak = Session::get('id_cetak');
    //     if (empty($index_cetak)) {
    //         $index_cetak = 0;
    //         $id_cetak = 0;
    //     }

    //     return view('page.spp.sppd', compact('b', 'rekening', 'bagian_id', 'gl', 'revisiCounter', 'vendor', 'index', 'index_cetak', 'id_cetak', 'bagianbaru', 'rekening'));
    // }

    public function advanced_search(Request $request)
    {
        $bagian_cari = $request->bagian;
        $kode_sap_sppb = $request->kode_sap_sppb;
        $kode_sap_sppn = $request->kode_sap_sppn;
        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ", $rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
        })->all();

        // dd($bagian_cari, $kode_sap_sppb,$kode_sap_sppn,$rentang_waktu );

        Session::put('bagian_cari', $bagian_cari);
        Session::put('kode_sap_sppb', $kode_sap_sppb);
        Session::put('kode_sap_sppn', $kode_sap_sppn);
        Session::put('rentang_waktu', $rentang_waktu);
        // dd($bagian_cari);
        if ($kode_sap_sppb == 'semua' && $kode_sap_sppn == 'semua') {
            return redirect('advanced/index');
        } else if ($kode_sap_sppb != 'semua' && $kode_sap_sppn == 'semua') {
            return redirect('advancedb/index');
        } else if ($kode_sap_sppb == 'semua' && $kode_sap_sppn != 'semua') {
            return redirect('advancedn/index');
        } else if ($kode_sap_sppb != 'semua' && $kode_sap_sppn != 'semua') {
            // dd($kode_sap_sppb);
            return redirect('advancedbn/index');
        }
    }

    public function update(Request $request, $id)
    {

        //dd($request->all());
        $current = date('His-dmY');
        $bagian = Session::get('bagian');
        $user = Session::get('id');
        $level = Session::get('level');
        $sumberdana = $request->sumber_dana;

        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('sppb_id', 'sppn_id')->first();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        $idisisppb = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $idsppb)->select('sppb_isi.sppb_isi_id')->get();
        $idisisppn = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)->select('sppn_isi.sppn_isi_id')->get();
        $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $idsppb)->select('karyawan_id')->get();
        $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $idsppn)->select('karyawan_id')->get();
        $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id', '=', $idsppb)->select('faktur_pajak_id')->get();
        $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $idsppn)->select('faktur_pajak_id')->get();
        $faktur_pajak_spp = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $idsppn)->orWhere('faktur_pajak.sppb_id', '=', $idsppb)->select('faktur_pajak_id')->get();

        foreach ($idisisppb as $a => $value2) {
            $iduraiansppb[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.sppb_uraian_id')->get();
        }

        foreach ($idisisppn as $b => $value) {
            $iduraiansppn[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value->sppn_isi_id)->select('sppn_uraian.sppn_uraian_id')->get();
        }

        //FORM SPPB SAJA
        if (isset($idsppb) && empty($idsppn)) {
            DB::beginTransaction();
            try {
                $request->validate([
                    'kontrak_perjanjian_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'invoice_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'efaktur_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'berita_acara_file_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);
                $kontrak_perjanjian = $request->file('kontrak_perjanjian_sppb');
                if ($kontrak_perjanjian != null) {
                    $doks = SPPb::find($idsppb);
                    $dok = $doks->sppb_kontrak_perjanjian;
                    File::delete(public_path('dokumen/kontrakperjanjian/' . $dok));
                    $kontrak_perjanjian_file_name = str_replace("'", '', $kontrak_perjanjian->getClientOriginalName());
                    $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian_file_name;
                    $doks->sppb_kontrak_perjanjian = $kontrak_perjanjians;
                    $doks->save();
                    $kontrak_perjanjian->move('dokumen/kontrakperjanjian/', $kontrak_perjanjians);
                }


                $invoice = $request->file('invoice_sppb');
                if ($invoice != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/invoice/' . $dok->sppb_invoice));
                    $invoice_file_name = str_replace("'", '', $invoice->getClientOriginalName());
                    $invoices = $current . '-' . $invoice_file_name;
                    $dok->sppb_invoice = $invoices;
                    $dok->save();
                    $invoice->move('dokumen/invoice/', $invoices);
                }


                $efaktur = $request->file('efaktur_sppb');
                if ($efaktur != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/efaktur/' . $dok->sppb_efaktur));
                    $efaktur_file_name = str_replace("'", '', $efaktur->getClientOriginalName());
                    $efakturs = $current . '-' . $efaktur_file_name;
                    $dok->sppb_efaktur = $efakturs;
                    $dok->save();
                    $efaktur->move('dokumen/efaktur/', $efakturs);
                }


                $berita_acara_file = $request->file('berita_acara_file_sppb');
                if ($berita_acara_file != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/beritaacara/' . $dok->sppb_berita_acara_file));

                    $berita_acara_file_name = str_replace("'", '', $berita_acara_file->getClientOriginalName());
                    $berita_acara_files = $current . '-' . $berita_acara_file_name;
                    $dok->sppb_berita_acara_file = $berita_acara_files;
                    $dok->save();
                    $berita_acara_file->move('dokumen/beritaacara/', $berita_acara_files);
                }


                $kodebagiansppb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
                $kodebagian = $kodebagiansppb->master_bagian_kode;

                $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $tanggal = $request->tanggal_sppb;
                $tanggals = date('Y-m-d', strtotime($tanggal));
                //$tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
                $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;

                $tahun = $request->tahun_sppb;
                $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;

                $urutansppb = $request->urutan_sppb;
                $bulan = $bulanromawi[$month];
                $nomor = $kodebagian . "/SPPb/" . $urutansppb . "/" . $bulan . "/" .$tahun;
                
                if ($request->jenis == 'vendor') {
                    $data_metpen = $request->pilih_data_sppb_vendor;
                } else {
                    $data_metpen = $request->pilih_data_sppb;
                }
                $sppb = Sppb::find($idsppb);
                $sppb->sppb_no = $nomor;
                $sppb->sppb_jenis = $request->jenis;
                $sppb->sppb_urutan = $urutansppb;
                $sppb->sppb_bulan = $bulan;
                $sppb->sppb_tahun = $request->tahun_sppb;
                $sppb->master_user_id = $user;
                $sppb->master_bagian_id = $request->bagian_sppb;
                $sppb->master_bank_id = $request->id_bank_sppb;
                $sppb->sppb_kwitansi = $request->kwitansi_sppb;
                $sppb->sppb_referensi = $request->referensi_sppb;
                $sppb->sppb_au_53 = $request->au53_sppb;
                $sppb->sppb_berita_acara = $request->berita_acara_sppb;
                $sppb->sppb_faktur_pajak = $request->faktur_pajak_sppb;
                $sppb->sppb_sp_opl = $request->sp_opl_sppb;
                $sppb->sppb_tanggal = $tanggals;
                $sppb->sppb_metode_pembayaran = $request->metode_pembayaran_sppb;
                $sppb->sppb_no_rek = 0;
                $sppb->sppb_atas_nama = 0;
                $sppb->sppb_nama_bank = 0;
                $sppb->sppb_catatan = $request->catatan_sppb;
                $sppb->sppb_status = 0;
                $sppb->sppb_total = 0;
                $sppb->sppb_data_metpen = $data_metpen;
                $sppb->sppb_tidak_transfer = $request->tidak_transfer_cat;
                $sppb->save();
              
                $sum = 0;
                $request->request->add(['sppb_id' => $sppb->sppb_id]);
                $request->request->add(['master_bagian_id' => $sppb->master_bagian_id]);
                $isisppb = $request->isi_sppb;

                foreach ($faktur_pajak_sppb as $key => $value) {
                    $fp_sppb = FakturPajak::findOrFail($value->faktur_pajak_id);
                    $fp_sppb->delete();
                }

                $faktur_pajak = $request->faktur_pajak_sppb;
                foreach ($faktur_pajak as $key => $value) {
                    $fp = new FakturPajak;
                    $fp->sppb_id = $request->sppb_id;
                    $fp->sppn_id = null;
                    $fp->faktur_pajak_nomor = $value['fp'];
                    $fp->save();
                }

                foreach ($karyawan_sppb as $key => $value) {
                    $krywn_sppb = NamaKaryawanModel::findOrFail($value->karyawan_id);
                    $krywn_sppb->delete();
                }

                if ($request->jenis == "karyawan") {
                    if ($request->metode_pembayaran_sppb == "kas") {

                        if ($request->pilih_data_sppb == 'input_data') {
                            $karyawan = $request->karyawan_kas_sppb_input;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                                $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                                $krywn->save();
                            }
                        } else if ($request->pilih_data_sppb == 'master_data') {
                            $karyawan = $request->atas_nama_bank_sppb_kas;

                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                                $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                                $krywn->save();
                            }
                        } else {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->karyawan_alamat = "TERLAMPIR";
                            $krywn->save();
                        }
                    } else if ($request->metode_pembayaran_sppb == "kas_negara") {
                        $karyawan = $request->atas_nama_bank_sppb_kas;

                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                            $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                            $krywn->save();
                        }
                    } else if ($request->metode_pembayaran_sppb == "skbdn") {
                        if ($request->pilih_data_sppb == 'input_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                            $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                            $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                            $krywn->save();
                        }
                    } else {

                        if ($request->pilih_data_sppb == 'master_data') {
                            $karyawan = $request->karyawan_sppb;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value['nama'];
                                $krywn->karyawan_nama_bank = $value['bank'];
                                $krywn->karyawan_no_rek = $value['no_rek'];
                                $krywn->save();
                            }
                        } else if ($request->pilih_data_sppb == 'input_data') {
                            $karyawan = $request->karyawan_sppb_input;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value['nama'];
                                $krywn->karyawan_nama_bank = $value['bank'];
                                $krywn->karyawan_no_rek = $value['no_rek'];
                                $krywn->save();
                            }
                        } else {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->karyawan_nama_bank = "TERLAMPIR";
                            $krywn->karyawan_no_rek = "TERLAMPIR";
                            $krywn->save();
                        }
                    }
                } else {
                    if ($request->metode_pembayaran_sppb == 'kas_negara') {
                        $karyawan = $request->atas_nama_bank_sppb_kas;

                        $karyawan = $request->atas_nama_bank_sppb_kas;
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                            $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                            $krywn->save();
                        }
                    } else {
                        if ($request->pilih_data_sppb_vendor == 'input_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                            $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                            $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                            $krywn->save();
                        }
                    }
                }

                foreach ($idisisppb as $i => $v) {
                    $isi = IsiSppb::find($v->sppb_isi_id);

                    $isi->delete();
                    foreach ($iduraiansppb[$i] as $u => $va) {
                        $uraian = IsiUraianSppb::find($va->sppb_uraian_id);
                        $uraian->delete();
                    }
                }
                // foreach($idisisppb as $i => $v){
                //     $isi=IsiSppb::find($v->sppb_isi_id);
                //     $isi->delete();
                //     foreach($iduraiansppb[$i] as $u => $va){
                //         $uraian = IsiUraianSppb::find($va->sppb_uraian_id);
                //         $uraian->delete();
                //     }
                // }
                $isisppb = $request->isi_sppb;
                // dd($isisppb);
                // dd($request->sppb_isi_id);
                $sum1 = 0;
                $sum2 = 0;
                foreach ($isisppb as $isi => $value) {

                    // if($value['jenis_center']=='cost_center'){
                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                    }
                    if ($value['jenis_center'] == 'cost_center') {
                        $isisppb->master_cost_center_id = $value['cost_center'];
                    } else {
                        $isisppb->master_profit_center_id = $value['profit_center'];
                    }
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        // $tanpapajak = substr($value2['type_pajak_sppb'], 0, 9);
                        // dd($tanpapajak);
                        $a = str_replace('.', '', $value2['jumlah']);
                        $angka_pajak = str_replace('.', '', $value2['pajak']);
                        $angka_potongan = str_replace('.', '', $value2['potongan']);
                        $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                        $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                        if (isset($value2['type_pajak_sppb'])) {
                            $tanpapajak = substr($value2['type_pajak_sppb'], 0, 9);
                            if ($tanpapajak == 'tanpa_paj') {
                                $b = $a;
                            } else if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        } else {
                            if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        }
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_pph = str_replace('.', '', $value2['pph']);
                        $isiuraiansppb->sppb_pajak_manual = $value2['manual'];
                        $isiuraiansppb->sppb_uraian_nominal = $a;
                        $isiuraiansppb->sppb_nominal_pajak = $angka_pajak;
                        if (isset($value2['type_pajak_sppb'])) {
                            if ($tanpapajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_nominal_akhir = $a;
                            } else {
                                $isiuraiansppb->sppb_nominal_akhir = $angka_akhir;
                            }
                        } else {
                            $isiuraiansppb->sppb_nominal_akhir = $a;
                        }
                        $isiuraiansppb->sppb_dpp_ppn = $angka_dpp_ppn;
                        $isiuraiansppb->sppb_potongan = $angka_potongan;

                        if (isset($value2['type_pajak_sppb'])) {
                            $jenispajak = substr($value2['type_pajak_sppb'], 0, 9);  //substring untuk mengambil type pajak
                            if ($jenispajak == 'wapu_sppb') {

                                $jeniswapu = substr($value2['pilih_wapu_sppb'], 0, 12); // substring untuk mengambil pilih wapu
                                // dd($value2);
                                if ($jeniswapu == 'wapu_pph21_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 2,5%";
                                } else if ($jeniswapu == 'wapu_pph21_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 7,5%";
                                } else if ($jeniswapu == 'wapu_pph21_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 12,5%";
                                } else if ($jeniswapu == 'wapu_pph22_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 22 1,5%";
                                } else if ($jeniswapu == 'wapu_pph23_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 2%";
                                } else if ($jeniswapu == 'wapu_pph23_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 15%";
                                } else if ($jeniswapu == 'wapu_pph23_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 0%";
                                } else if ($jeniswapu == 'wapu_pph26_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 0%";
                                } else if ($jeniswapu == 'wapu_pph26_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 10%";
                                } else if ($jeniswapu == 'wapu_pph26_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 20%";
                                } else if ($jeniswapu == 'wapu_pasal4a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "Pasal 4 Ayat 2";
                                } else if ($jeniswapu == 'wapu_normal_') {
                                    $isiuraiansppb->sppb_pajak_wapu = "wapu Normal 11%";
                                } else if ($jeniswapu == 'wapu_nilai_l') {
                                    $isiuraiansppb->sppb_pajak_wapu = "wapu 1,1%";
                                } else if ($jeniswapu == 'wapu_manual_') {
                                    $isiuraiansppb->sppb_pajak_wapu = "Manual";
                                } else {
                                    // Kondisi wapu diluar kombinasi pph
                                }
                            } else if ($jenispajak == 'waba_sppb') {
                                $jeniswaba = substr($value2['pilih_waba_sppb'], 0, 12); // substring untuk mengambil pilih waba
                                // dd($jeniswaba);
                                if ($jeniswaba == 'waba_pph21_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 2,5%";
                                } else if ($jeniswaba == 'waba_pph21_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 7,5%";
                                } else if ($jeniswaba == 'waba_pph21_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 12,5%";
                                } else if ($jeniswaba == 'waba_pph22_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 22 1,5%";
                                } else if ($jeniswaba == 'waba_pph23_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 2%";
                                } else if ($jeniswaba == 'waba_pph23_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 15%";
                                } else if ($jeniswaba == 'waba_pph23_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 0%";
                                } else if ($jeniswaba == 'waba_pph26_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 0%";
                                } else if ($jeniswaba == 'waba_pph26_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 10%";
                                } else if ($jeniswaba == 'waba_pph26_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 20%";
                                } else if ($jeniswaba == 'waba_pasal4a') {
                                    $isiuraiansppb->sppb_pajak_waba = "Pasal 4 Ayat 2";
                                } else if ($jeniswaba == 'waba_normal_') {
                                    $isiuraiansppb->sppb_pajak_waba = "Waba Normal 11%";
                                } else if ($jeniswaba == 'waba_nilai_l') {
                                    $isiuraiansppb->sppb_pajak_waba = "Waba 1,1%";
                                } else if ($jeniswaba == 'waba_manual_') {
                                    $isiuraiansppb->sppb_pajak_waba = "Manual";
                                } else {
                                    // Kondisi waba diluar kombinasi pph
                                }
                            } else if ($jenispajak == 'pph_sppb_') {
                                $jenispph = substr($value2['pilih_pph_sppb'], 0, 12); // substring untuk mengambil pilih pph

                                if ($jenispph == 'pph21_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 2,5%";
                                } else if ($jenispph == 'pph21_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 7,5%";
                                } else if ($jenispph == 'pph21_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 12,5%";
                                } else if ($jenispph == 'pph22_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 22 1,5%";
                                } else if ($jenispph == 'pph23_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 2%";
                                } else if ($jenispph == 'pph23_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 15%";
                                } else if ($jenispph == 'pph23_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 0%";
                                } else if ($jenispph == 'pph26_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 0%";
                                } else if ($jenispph == 'pph26_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 10%";
                                } else if ($jenispph == 'pph26_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 20%";
                                } else if ($jenispph == 'pphpasal4_ay') {
                                    $isiuraiansppb->sppb_pajak_pph = "Pasal 4 Ayat 2";
                                } else if ($jenispph == 'pph_manual_s') {
                                    $isiuraiansppb->sppb_pajak_pph = "Manual";
                                } else {
                                    // Kondisi pph diluar daftar
                                }
                            } else if ($jenispajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_tanpa_pajak = "Ya";
                            } else {
                                // kondisi pilihan pajak tanpa kombinasi
                            }
                        } else {
                            // Khusus nominal tanpa pilihan pajak
                        }
                        //dd($isiuraiansppb);
                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                }
                $sum = $sum1 + $sum2;

                $isisum = Sppb::find($request->sppb_id);
                $isisum->sppb_total = $sum;
                $isisum->save();

                $spp = Spp::find($id);
                $spp->master_bagian_id = $request->bagian_sppb;
                $spp->spp_jenis_sumber_dana = $sumberdana;
                $spp->spp_tanggal = $tanggals;
                $spp->save();

                $dokpensppbtersimpan = DokumenPendukungSppb::where('sppb_id', $idsppb)->select('dokumen_pendukung_sppb_id')->get();
                $dokpenlama = $request->dokpenlama_sppb;

                foreach ($dokpensppbtersimpan as $i => $v) {
                    if (empty($dokpenlama[$i])) {
                        $dok = DokumenPendukungSppb::find($v->dokumen_pendukung_sppb_id);
                        File::delete(public_path('dokumen/' . $dok->dokumen_pendukung_sppb_nama));
                        $dok->delete();
                    }
                }

                $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
                if ($dokumenpendukung != null) {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukung_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungs = $current . '-' . $dokumenpendukung_file_name;
                        $file->move('dokumen', $dokumenpendukungs);
                        $dokumenpendukungsppb = new DokumenPendukungSppb;
                        $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }
                DB::commit();
                $action = $request->status_btn;
                if ($action == "0") {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
        //FORM SPPN SAJA
        else if (isset($idsppn) && empty($idsppb)) {
            DB::beginTransaction();
            try {
                $request->validate([
                    'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);

                $kodebagiansppn = Bagian::where('master_bagian_id', $request->bagian_sppn)->select('master_bagian_kode')->first();
                $kodebagian = $kodebagiansppn->master_bagian_kode;

                $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $tanggal = $request->tanggal_sppn;
                $tanggals = date('Y-m-d', strtotime($tanggal));
                $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
                $tahun = $request->tahun_sppn;

                $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;

                $urutansppn = $request->urutan_sppn;
                $bulan = $bulanromawi[$month];
                $nomor = $kodebagian . "/SPPn/" . $urutansppn . "/" . $bulan . "/" .$tahun;

                foreach ($karyawan_sppn as $key => $value) {
                    $krywn_sppn = NamaKaryawanModel::findOrFail($value->karyawan_id);
                    $krywn_sppn->delete();
                }

                $sppn = Sppn::find($idsppn);
                $sppn->sppn_no = $nomor;
                $sppn->master_user_id = $user;
                $sppn->master_bagian_id = $request->bagian_sppn;
                $sppn->master_bank_id = $request->id_bank_sppn;
                $sppn->sppn_jenis = $request->jenis;
                $sppn->sppn_urutan = $urutansppn;
                $sppn->sppn_bulan = $bulan;
                $sppn->sppn_tahun = $tahun;
                $sppn->sppn_kwitansi = $request->kwitansi_sppn;
                $sppn->sppn_referensi = $request->referensi_sppn;
                $sppn->sppn_ba_au_53 = $request->au58_sppn;
                $sppn->sppn_faktur_pajak = $request->faktur_pajak_sppn;
                $sppn->sppn_tanggal = $tanggals;
                $sppn->sppn_no_rek = 0;
                $sppn->sppn_atas_nama = 0;
                $sppn->sppn_nama_bank = 0;
                $sppn->sppn_sp_opl = $request->sp_opl_sppn;
                $sppn->sppn_catatan = $request->catatan_sppn;
                $sppn->sppn_status = 0;
                $sppn->sppn_jumlah = 0;
                $sppn->save();


                $request->request->add(['sppn_id' => $sppn->sppn_id]);
                $request->request->add(['master_bagian_id' => $sppn->master_bagian_id]);

                $dokpensppntersimpan = DokumenPendukungSppn::where('sppn_id', $idsppn)->select('dokumen_pendukung_sppn_id')->get();
                $dokpenlamasppn = $request->dokpenlama_sppn;


                foreach ($faktur_pajak_sppn as $key => $value) {
                    $fp_sppn = FakturPajak::findOrFail($value->faktur_pajak_id);
                    $fp_sppn->delete();
                }
                $faktur_pajak = $request->faktur_pajak_sppn;
                foreach ($faktur_pajak as $key => $value) {
                    $fp = new FakturPajak();
                    $fp->sppb_id = null;
                    $fp->sppn_id = $request->sppn_id;
                    $fp->faktur_pajak_nomor = $value['fp'];
                    $fp->save();
                }

                if ($request->jenis == "karyawan") {
                    $karyawanSppn = new NamaKaryawanModel();
                    $karyawanSppn->sppb_id = null;
                    $karyawanSppn->sppn_id = $idsppn;
                    $karyawanSppn->karyawan_nama = $request->diterima_dari;
                    $karyawanSppn->karyawan_alamat = $request->alamat_sppn;
                    $karyawanSppn->save();
                    // if($request->metode_pembayaran_sppn == "kas"){
                    //     $karyawan = $request->atas_nama_bank_sppn_kas;
                    //     if($request->pilih_data_sppn == 'input_data'){
                    // foreach($karyawan as $key => $value){
                    //     $krywn = new NamaKaryawanModel;
                    //     $krywn -> sppb_id = null;
                    //     $krywn -> sppn_id = $request->sppn_id;
                    //     $krywn -> karyawan_nama = $value;
                    //     $krywn -> save();
                    // }

                    //     }
                    //     else{
                    //         $krywn = new NamaKaryawanModel;
                    //             $krywn -> sppb_id = null;
                    //             $krywn -> sppn_id = $request->sppn_id;
                    //             $krywn -> karyawan_nama = "TERLAMPIR";
                    //             $krywn -> save();
                    //     }
                    // }
                    // else{
                    //     $karyawan = $request->karyawan_sppn;
                    //     if($request->pilih_data_sppn == 'input_data'){
                    //         foreach($karyawan as $key => $value){
                    //             $krywn = new NamaKaryawanModel;
                    //             $krywn -> sppb_id = null;
                    //             $krywn -> sppn_id = $request->sppn_id;
                    //             $krywn -> karyawan_nama = $value['nama'];
                    //             $krywn -> karyawan_nama_bank = $value['bank'];
                    //             $krywn -> karyawan_no_rek = $value['no_rek'];
                    //             $krywn -> save();
                    //         }
                    //     }
                    //     else{
                    //         $krywn = new NamaKaryawanModel;
                    //         $krywn -> sppb_id = null;
                    //         $krywn -> sppn_id = $request->sppn_id;
                    //         $krywn -> karyawan_nama = "TERLAMPIR";
                    //         $krywn -> karyawan_nama_bank = "TERLAMPIR";
                    //         $krywn -> karyawan_no_rek = "TERLAMPIR";
                    //         $krywn -> save();
                    //     }
                    // }
                } else {
                    $karyawanSppn = new NamaKaryawanModel();
                    $karyawanSppn->sppb_id = null;
                    $karyawanSppn->sppn_id = $idsppn;
                    $karyawanSppn->karyawan_nama = $request->diterima_dari;
                    $karyawanSppn->karyawan_alamat = $request->alamat_sppn;
                    $karyawanSppn->save();
                }

                foreach ($dokpensppntersimpan as $i => $v) {
                    if (empty($dokpenlamasppn[$i])) {
                        $dok = DokumenPendukungSppn::find($v->dokumen_pendukung_sppn_id);
                        File::delete(public_path('dokumen/' . $dok->dokumen_pendukung_sppn_nama));
                        $dok->delete();
                    }
                }

                $dokpensppn = $request->file('dokumen_pendukung_sppn');
                if ($dokpensppn != null) {
                    foreach ($dokpensppn as $file) {
                        $dokpensppn_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokpensppns = $current . '-' . $dokpensppn_file_name;
                        $file->move('dokumen', $dokpensppns);
                        $dokumenpendukungsppn = new DokumenPendukungSppn();
                        $dokumenpendukungsppn->sppn_id = $request->sppn_id;
                        $dokumenpendukungsppn->dokumen_pendukung_sppn_nama = $dokpensppns;
                        $dokumenpendukungsppn->save();
                    }
                }

                foreach ($idisisppn as $i => $v) {
                    $isi = IsiSppn::find($v->sppn_isi_id);
                    $isi->delete();
                    foreach ($iduraiansppn[$i] as $u => $va) {
                        $uraian = IsiUraianSppn::find($va->sppn_uraian_id);
                        $uraian->delete();
                    }
                }
                $isisppn = $request->isi_sppn;
                // dd($request->isi_sppn[1]['profit_center']);
                $sum1 = 0;
                $sum2 = 0;
                foreach ($isisppn as $isi => $value) {

                    $isisppn = new isisppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    // $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }
                    if ($value['jenis_center'] == 'cost_center') {
                        $isisppn->master_cost_center_id = $value['cost_center'];
                    } else {
                        $isisppn->master_profit_center_id = $value['profit_center'];
                    }
                    $isisppn->master_cash_flow_id = $value['cash_flow'];

                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    //dd($request->all());
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        //dd($request->uraian_sppn);
                        $a = str_replace('.', '', $value2['jumlah']);
                        $angka_pajak = str_replace('.', '', $value2['pajak']);
                        $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                        $angka_potongan = str_replace('.', '', $value2['potongan']);
                        $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                        if (isset($value2['type_pajak_sppn'])) {
                            $tanpapajak = substr($value2['type_pajak_sppn'], 0, 9);
                            if ($tanpapajak == 'tanpa_paj') {
                                $b = $a;
                            } else if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        } else {
                            if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        }
                        $isiuraiansppn = new IsiUraiansppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_pph = str_replace('.', '', $value2['pph']);
                        $isiuraiansppn->sppn_pajak_manual = $value2['manual'];
                        $isiuraiansppn->sppn_uraian_nominal = $a;
                        $isiuraiansppn->sppn_nominal_pajak = $angka_pajak;
                        if (isset($value2['type_pajak_sppn'])) {
                            if ($tanpapajak == 'tanpa_paj') {
                                $isiuraiansppn->sppn_nominal_akhir = $a;
                            } else {
                                $isiuraiansppn->sppn_nominal_akhir = $angka_akhir;
                            }
                        } else {
                            $isiuraiansppn->sppn_nominal_akhir = $a;
                        }
                        $isiuraiansppn->sppn_dpp_ppn = $angka_dpp_ppn;
                        $isiuraiansppn->sppn_potongan = $angka_potongan;
                        $jenispajak = substr($value2['type_pajak_sppn'], 0, 9);  //substring untuk mengambil type pajak
                        if ($jenispajak == 'wapu_sppn') {
                            $jeniswapu = substr($value2['pilih_wapu_sppn'], 0, 12); // substring untuk mengambil pilih wapu
                            if ($jeniswapu == 'wapu_pph21_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 2,5%";
                            } else if ($jeniswapu == 'wapu_pph21_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 7,5%";
                            } else if ($jeniswapu == 'wapu_pph21_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 12,5%";
                            } else if ($jeniswapu == 'wapu_pph22_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 22 1,5%";
                            } else if ($jeniswapu == 'wapu_pph23_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 2%";
                            } else if ($jeniswapu == 'wapu_pph23_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 15%";
                            } else if ($jeniswapu == 'wapu_pph23_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 0%";
                            } else if ($jeniswapu == 'wapu_pph26_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 0%";
                            } else if ($jeniswapu == 'wapu_pph26_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 10%";
                            } else if ($jeniswapu == 'wapu_pph26_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 20%";
                            } else if ($jeniswapu == 'wapu_pasal4a') {
                                $isiuraiansppn->sppn_pajak_wapu = "Pasal 4 Ayat 2";
                            } else if ($jeniswapu == 'wapu_normal_') {
                                $isiuraiansppn->sppn_pajak_wapu = "wapu Normal 11%";
                            } else if ($jeniswapu == 'wapu_nilai_l') {
                                $isiuraiansppn->sppn_pajak_wapu = "wapu 1,1%";
                            } else if ($jeniswapu == 'wapu_manual_') {
                                $isiuraiansppn->sppn_pajak_wapu = "Manual";
                            } else {
                                // Kondisi wapu diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'waba_sppn') {
                            $jeniswaba = substr($value2['pilih_waba_sppn'], 0, 12); // substring untuk mengambil pilih waba
                            // dd($jeniswaba);
                            if ($jeniswaba == 'waba_pph21_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 2,5%";
                            } else if ($jeniswaba == 'waba_pph21_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 7,5%";
                            } else if ($jeniswaba == 'waba_pph21_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 12,5%";
                            } else if ($jeniswaba == 'waba_pph22_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 22 1,5%";
                            } else if ($jeniswaba == 'waba_pph23_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 2%";
                            } else if ($jeniswaba == 'waba_pph23_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 15%";
                            } else if ($jeniswaba == 'waba_pph23_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 0%";
                            } else if ($jeniswaba == 'waba_pph26_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 0%";
                            } else if ($jeniswaba == 'waba_pph26_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 10%";
                            } else if ($jeniswaba == 'waba_pph26_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 20%";
                            } else if ($jeniswaba == 'waba_pasal4a') {
                                $isiuraiansppn->sppn_pajak_waba = "Pasal 4 Ayat 2";
                            } else if ($jeniswaba == 'waba_normal_') {
                                $isiuraiansppn->sppn_pajak_waba = "Waba Normal 11%";
                            } else if ($jeniswaba == 'waba_nilai_l') {
                                $isiuraiansppn->sppn_pajak_waba = "Waba 1,1%";
                            } else if ($jeniswaba == 'waba_manual_') {
                                $isiuraiansppn->sppn_pajak_waba = "Manual";
                            } else {
                                // Kondisi waba diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'pph_sppn_') {
                            $jenispph = substr($value2['pilih_pph_sppn'], 0, 12); // substring untuk mengambil pilih pph
                            // dd($jenispph);

                            if ($jenispph == 'pph21_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 2,5%";
                            } else if ($jenispph == 'pph21_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 7,5%";
                            } else if ($jenispph == 'pph21_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 12,5%";
                            } else if ($jenispph == 'pph22_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 22 1,5%";
                            } else if ($jenispph == 'pph23_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 2%";
                            } else if ($jenispph == 'pph23_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 15%";
                            } else if ($jenispph == 'pph23_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 0%";
                            } else if ($jenispph == 'pph26_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 0%";
                            } else if ($jenispph == 'pph26_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 10%";
                            } else if ($jenispph == 'pph26_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 20%";
                            } else if ($jenispph == 'pphpasal4_ay') {
                                $isiuraiansppn->sppn_pajak_pph = "Pasal 4 Ayat 2";
                            } else if ($jenispph == 'pph_manual_s') {
                                $isiuraiansppn->sppn_pajak_pph = "Manual";
                            } else {
                                // Kondisi pph diluar daftar
                            }
                        } else if ($jenispajak == 'tanpa_paj') {
                            $isiuraiansppn->sppn_tanpa_pajak = "Ya";
                        } else {
                            // kondisi pilihan pajak tanpa kombinasi
                        }

                        // dd($isiuraiansppn);
                        $isiuraiansppn->save();
                        $sum1 += $b;
                    }
                }
                $sum = $sum1 + $sum2;
                // dd($sum);
                $isisum = Sppn::find($request->sppn_id);
                $isisum->sppn_jumlah = $sum;
                $isisum->save();

                $spp = Spp::find($id);
                $spp->master_bagian_id = $request->bagian_sppn;
                $spp->spp_jenis_sumber_dana = $sumberdana;
                $spp->spp_tanggal = $tanggals;
                $spp->save();
                DB::commit();
                // dd($b, $tanpapajak);
                $action = $request->status_btn;
                if ($action == "0") {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
            } catch (\Exception $e) {
                DB::rollback();
                //dd($e);
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
        //FORM SPP CAMPURAN
        else {
            DB::beginTransaction();
            try {
                $request->validate([
                    'kontrak_perjanjian_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'invoice_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'efaktur_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'berita_acara_file_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);
                $kontrak_perjanjian = $request->file('kontrak_perjanjian_sppb');
                if ($kontrak_perjanjian != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/kontrakperjanjian/' . $dok->sppb_kontrak_perjanjian));
                    $kontrak_perjanjian_file_name = str_replace("'", '', $kontrak_perjanjian->getClientOriginalName());
                    $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian_file_name;
                    $dok->sppb_kontrak_perjanjian = $kontrak_perjanjians;
                    $dok->save();
                    $kontrak_perjanjian->move('dokumen/kontrakperjanjian/', $kontrak_perjanjians);
                }


                $invoice = $request->file('invoice_sppb');
                if ($invoice != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/invoice/' . $dok->sppb_invoice));
                    $invoice_file_name = str_replace("'", '', $invoice->getClientOriginalName());
                    $invoices = $current . '-' . $invoice_file_name;
                    $dok->sppb_invoice = $invoices;
                    $dok->save();
                    $invoice->move('dokumen/invoice/', $invoices);
                }

                $efaktur = $request->file('efaktur_sppb');
                if ($efaktur != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/efaktur/' . $dok->sppb_efaktur));
                    $efaktur_file_name = str_replace("'", '', $efaktur->getClientOriginalName());
                    $efakturs = $current . '-' . $efaktur_file_name;
                    $dok->sppb_efaktur = $efakturs;
                    $dok->save();
                    $efaktur->move('dokumen/efaktur/', $efakturs);
                }

                $berita_acara_file = $request->file('berita_acara_file_sppb');
                if ($berita_acara_file != null) {
                    $dok = SPPb::find($idsppb);
                    File::delete(public_path('dokumen/beritaacara/' . $dok->sppb_berita_acara_file));
                    $berita_acara_file_name = str_replace("'", '', $berita_acara_file->getClientOriginalName());
                    $berita_acara_files = $current . '-' . $berita_acara_file_name;
                    $dok->sppb_berita_acara_file = $berita_acara_files;
                    $dok->save();
                    $berita_acara_file->move('dokumen/beritaacara/', $berita_acara_files);
                }
                $kodebagianb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
                $kodebagiann = Bagian::where('master_bagian_id', $request->bagian_sppn)->select('master_bagian_kode')->first();
                $kodebagiansppb = $kodebagianb->master_bagian_kode;
                $kodebagiansppn = $kodebagiann->master_bagian_kode;

                $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $tanggal = $request->tanggal_sppb;
                $tanggals = date('Y-m-d', strtotime($tanggal));
                $tahun = $request->tahun_sppb;
                $month = $request->bulan_sppb;
                $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;


                $urutansppb = $request->urutan_sppb;
                $urutansppn = $request->urutan_sppn;

                $nomorsppb = $kodebagiansppb . "/SPPb/" . $urutansppb . "/" . $tahun;
                $nomorsppn = $kodebagiansppn . "/SPPn/" . $urutansppn . "/" . $tahun;
                if ($request->jenis == 'vendor') {
                    $data_metpen = $request->pilih_data_sppb_vendor;
                } else {
                    $data_metpen = $request->pilih_data_sppb;
                }
                $sppb = Sppb::find($idsppb);
                $sppb->master_user_id = $user;
                $sppb->master_bagian_id = $request->bagian_sppb;
                $sppb->master_bank_id = $request->id_bank_sppb;
                $sppb->sppb_jenis = $request->jenis;
                $sppb->sppb_no = $nomorsppb;
                $sppb->sppb_urutan = $urutansppb;
                $sppb->sppb_tahun = $tahun;
                $sppb->sppb_kwitansi = $request->kwitansi_sppb;
                $sppb->sppb_referensi = $request->referensi_sppb;
                $sppb->sppb_au_53 = $request->au53_sppb;
                $sppb->sppb_berita_acara = $request->berita_acara_sppb;
                $sppb->sppb_faktur_pajak = 0;
                $sppb->sppb_sp_opl = $request->sp_opl_sppb;
                $sppb->sppb_tanggal = $tanggals;
                $sppb->sppb_metode_pembayaran = $request->metode_pembayaran_sppb;
                $sppb->sppb_no_rek = 0;
                $sppb->sppb_atas_nama = 0;
                $sppb->sppb_nama_bank = 0;
                $sppb->sppb_catatan = $request->catatan_sppb;
                $sppb->sppb_status = 0;
                $sppb->sppb_total = 0;
                $sppb->sppb_data_metpen = $data_metpen;
                $sppb->save();


                $request->request->add(['sppb_id' => $sppb->sppb_id]);
                $request->request->add(['master_bagian_id' => $sppb->master_bagian_id]);

                foreach ($faktur_pajak_spp as $key => $value) {
                    $fp_sppb = FakturPajak::findOrFail($value->faktur_pajak_id);
                    $fp_sppb->delete();
                }



                foreach ($karyawan_sppb as $key => $value) {
                    $krywn_sppb = NamaKaryawanModel::findOrFail($value->karyawan_id);
                    $krywn_sppb->delete();
                }
                if ($request->jenis == "karyawan") {
                    if ($request->metode_pembayaran_sppb == "kas") {

                        if ($request->pilih_data_sppb == 'input_data') {
                            $karyawan = $request->karyawan_kas_sppb_input;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value;
                                $krywn->save();
                            }
                        } else if ($request->pilih_data_sppb == 'master_data') {
                            $karyawan = $request->atas_nama_bank_sppb_kas;

                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value;
                                $krywn->save();
                            }
                        } else {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->save();
                        }
                    } else if ($request->metode_pembayaran_sppb == "kas_negara") {
                    } else if ($request->metode_pembayaran_sppb == "skbdn") {
                        if ($request->pilih_data_sppb == 'input_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                            $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                            $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                            $krywn->save();
                        }
                    } else {

                        if ($request->pilih_data_sppb == 'master_data') {
                            $karyawan = $request->karyawan_sppb;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value['nama'];
                                $krywn->karyawan_nama_bank = $value['bank'];
                                $krywn->karyawan_no_rek = $value['no_rek'];
                                $krywn->save();
                            }
                        } else if ($request->pilih_data_sppb == 'input_data') {
                            $karyawan = $request->karyawan_sppb_input;
                            foreach ($karyawan as $key => $value) {
                                $krywn = new NamaKaryawanModel;
                                $krywn->sppb_id = $request->sppb_id;
                                $krywn->sppn_id = null;
                                $krywn->karyawan_nama = $value['nama'];
                                $krywn->karyawan_nama_bank = $value['bank'];
                                $krywn->karyawan_no_rek = $value['no_rek'];
                                $krywn->save();
                            }
                        } else {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->karyawan_nama_bank = "TERLAMPIR";
                            $krywn->karyawan_no_rek = "TERLAMPIR";
                            $krywn->save();
                        }
                    }
                } else {
                    if ($request->metode_pembayaran_sppb == 'kas_negara') {
                    } else {
                        if ($request->pilih_data_sppb_vendor == 'input_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                            $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                            $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                            $krywn->save();
                        }
                    }
                }
                $dokpensppbtersimpan = DokumenPendukungSppb::where('sppb_id', $idsppb)->select('dokumen_pendukung_sppb_id')->get();
                $dokpenlama = $request->dokpenlama_sppb;
                //dd($dokpenlama);
                foreach ($dokpensppbtersimpan as $i => $v) {
                    if (empty($dokpenlama[$i])) {
                        $dok = DokumenPendukungSppb::find($v->dokumen_pendukung_sppb_id);
                        File::delete(public_path('dokumen/' . $dok->dokumen_pendukung_sppb_nama));
                        $dok->delete();
                    }
                }

                $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
                if ($dokumenpendukung != null) {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukung_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungs = $current . '-' . $dokumenpendukung_file_name;
                        $file->move('dokumen', $dokumenpendukungs);
                        // DokumenPendukungSppb::create([
                        //     'sppb_id' => $request->sppb_id,
                        //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                        // ]);
                        $dokumenpendukungsppb = new DokumenPendukungSppb;
                        $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }

                foreach ($idisisppb as $i => $v) {
                    $isi = IsiSppb::find($v->sppb_isi_id);
                    $isi->delete();
                    foreach ($iduraiansppb[$i] as $u => $va) {
                        $uraian = IsiUraianSppb::find($va->sppb_uraian_id);
                        $uraian->delete();
                    }
                }


                $isisppb = $request->isi_sppb;
                $sum1 = 0;
                $sum2 = 0;

                foreach ($isisppb as $isi => $value) {

                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    // $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                    }
                    if ($value['jenis_center'] == 'cost_center') {
                        $isisppb->master_cost_center_id = $value['cost_center'];
                    } else {
                        $isisppb->master_profit_center_id = $value['profit_center'];
                    }
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        //dd($request->uraian_sppb);
                        $a = str_replace('.', '', $value2['jumlah']);
                        $angka_pajak = str_replace('.', '', $value2['pajak']);
                        $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                        $angka_potongan = str_replace('.', '', $value2['potongan']);
                        $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                        if (isset($value2['type_pajak_sppb'])) {
                            $tanpapajak = substr($value2['type_pajak_sppb'], 0, 9);
                            if ($tanpapajak == 'tanpa_paj') {
                                $b = $a;
                            } else if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        } else {
                            if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        }
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_pph = str_replace('.', '', $value2['pph']);
                        $isiuraiansppb->sppb_pajak_manual = $value2['manual'];
                        $isiuraiansppb->sppb_uraian_nominal = $a;
                        $isiuraiansppb->sppb_nominal_pajak = $angka_pajak;
                        if (isset($value2['type_pajak_sppb'])) {
                            if ($tanpapajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_nominal_akhir = $a;
                            } else {
                                $isiuraiansppb->sppb_nominal_akhir = $angka_akhir;
                            }
                        } else {
                            $isiuraiansppb->sppb_nominal_akhir = $a;
                        }
                        $isiuraiansppb->sppb_dpp_ppn = $angka_dpp_ppn;
                        $isiuraiansppb->sppb_potongan = $angka_potongan;
                        if (isset($value2['type_pajak_sppb'])) {
                            $jenispajak = substr($value2['type_pajak_sppb'], 0, 9);  //substring untuk mengambil type pajak
                            if ($jenispajak == 'wapu_sppb') {
                                $jeniswapu = substr($value2['pilih_wapu_sppb'], 0, 12); // substring untuk mengambil pilih wapu
                                if ($jeniswapu == 'wapu_pph21_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 2,5%";
                                } else if ($jeniswapu == 'wapu_pph21_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 7,5%";
                                } else if ($jeniswapu == 'wapu_pph21_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 21 12,5%";
                                } else if ($jeniswapu == 'wapu_pph22_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 22 1,5%";
                                } else if ($jeniswapu == 'wapu_pph23_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 2%";
                                } else if ($jeniswapu == 'wapu_pph23_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 15%";
                                } else if ($jeniswapu == 'wapu_pph23_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 23 0%";
                                } else if ($jeniswapu == 'wapu_pph26_a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 0%";
                                } else if ($jeniswapu == 'wapu_pph26_b') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 10%";
                                } else if ($jeniswapu == 'wapu_pph26_c') {
                                    $isiuraiansppb->sppb_pajak_wapu = "PPh 26 20%";
                                } else if ($jeniswapu == 'wapu_pasal4a') {
                                    $isiuraiansppb->sppb_pajak_wapu = "Pasal 4 Ayat 2";
                                } else if ($jeniswapu == 'wapu_normal_') {
                                    $isiuraiansppb->sppb_pajak_wapu = "wapu Normal 11%";
                                } else if ($jeniswapu == 'wapu_nilai_l') {
                                    $isiuraiansppb->sppb_pajak_wapu = "wapu 1,1%";
                                } else if ($jeniswapu == 'wapu_manual_') {
                                    $isiuraiansppb->sppb_pajak_wapu = "Manual";
                                } else {
                                    //wapu_pph
                                }
                            } else if ($jenispajak == 'waba_sppb') {
                                $jeniswaba = substr($value2['pilih_waba_sppb'], 0, 12); // substring untuk mengambil pilih waba
                                if ($jeniswaba == 'waba_pph21_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 2,5%";
                                } else if ($jeniswaba == 'waba_pph21_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 7,5%";
                                } else if ($jeniswaba == 'waba_pph21_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 21 12,5%";
                                } else if ($jeniswaba == 'waba_pph22_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 22 1,5%";
                                } else if ($jeniswaba == 'waba_pph23_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 2%";
                                } else if ($jeniswaba == 'waba_pph23_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 15%";
                                } else if ($jeniswaba == 'waba_pph23_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 23 0%";
                                } else if ($jeniswaba == 'waba_pph26_a') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 0%";
                                } else if ($jeniswaba == 'waba_pph26_b') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 10%";
                                } else if ($jeniswaba == 'waba_pph26_c') {
                                    $isiuraiansppb->sppb_pajak_waba = "PPh 26 20%";
                                } else if ($jeniswaba == 'waba_pasal4a') {
                                    $isiuraiansppb->sppb_pajak_waba = "Pasal 4 Ayat 2";
                                } else if ($jeniswaba == 'waba_normal_') {
                                    $isiuraiansppb->sppb_pajak_waba = "Waba Normal 11%";
                                } else if ($jeniswaba == 'waba_nilai_l') {
                                    $isiuraiansppb->sppb_pajak_waba = "Waba 1,1%";
                                } else if ($jeniswaba == 'waba_manual_') {
                                    $isiuraiansppb->sppb_pajak_waba = "Manual";
                                } else {
                                    // Kondisi waba sppb diluar kombinasi pph
                                }
                            } else if ($jenispajak == 'pph_sppb_') {
                                $jenispph = substr($value2['pilih_pph_sppb'], 0, 12); // substring untuk mengambil pilih pph

                                if ($jenispph == 'pph21_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 2,5%";
                                } else if ($jenispph == 'pph21_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 7,5%";
                                } else if ($jenispph == 'pph21_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 21 12,5%";
                                } else if ($jenispph == 'pph22_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 22 1,5%";
                                } else if ($jenispph == 'pph23_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 2%";
                                } else if ($jenispph == 'pph23_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 15%";
                                } else if ($jenispph == 'pph23_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 23 0%";
                                } else if ($jenispph == 'pph26_a_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 0%";
                                } else if ($jenispph == 'pph26_b_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 10%";
                                } else if ($jenispph == 'pph26_c_sppb') {
                                    $isiuraiansppb->sppb_pajak_pph = "PPh 26 20%";
                                } else if ($jenispph == 'pphpasal4_ay') {
                                    $isiuraiansppb->sppb_pajak_pph = "Pasal 4 Ayat 2";
                                } else if ($jenispph == 'pph_manual_s') {
                                    $isiuraiansppb->sppb_pajak_pph = "Manual";
                                } else {
                                    // Kondisi pph diluar daftar
                                }
                            } else if ($jenispajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_tanpa_pajak = "Ya";
                            } else {
                                // kondisi pilihan pajak tanpa kombinasi
                            }
                        } else {
                            // Khusus nominal tanpa pilihan pajak
                        }

                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                }
                $sumsppb = $sum1 + $sum2;

                $isisum = Sppb::find($request->sppb_id);
                $isisum->sppb_total = $sumsppb;
                $isisum->save();


                // }
                $request->validate([
                    'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);

                $sppn = Sppn::find($idsppn);
                $sppn->master_user_id = $user;
                $sppn->master_bagian_id = $request->bagian_sppn;
                $sppn->master_bank_id = $request->id_bank_sppn;
                $sppn->sppn_jenis = $request->jenis;
                $sppn->sppn_no = $nomorsppn;
                $sppn->sppn_urutan = $urutansppn;
                $sppn->sppn_tahun = $tahun;
                $sppn->sppn_kwitansi = $request->kwitansi_sppn;
                $sppn->sppn_referensi = $request->referensi_sppn;
                $sppn->sppn_ba_au_53 = $request->au58_sppn;
                $sppn->sppn_faktur_pajak = $request->faktur_pajak_sppn;
                $sppn->sppn_tanggal = $tanggals;
                $sppn->sppn_no_rek = $request->rekening_bank_sppn;
                $sppn->sppn_atas_nama = 0;
                $sppn->sppn_nama_bank = $request->nama_bank_sppn;
                $sppn->sppn_sp_opl = $request->sp_opl_sppn;
                $sppn->sppn_catatan = $request->catatan_sppn;
                $sppn->sppn_status = 0;
                $sppn->sppn_jumlah = 0;
                $sppn->save();

                $request->request->add(['sppn_id' => $sppn->sppn_id]);

                $dokpensppntersimpan = DokumenPendukungSppn::where('sppn_id', $idsppn)->select('dokumen_pendukung_sppn_id')->get();
                $dokpenlamasppn = $request->dokpenlama_sppn;
                //dd($dokpenlama);
                foreach ($dokpensppntersimpan as $i => $v) {
                    if (empty($dokpenlamasppn[$i])) {
                        $dok = DokumenPendukungSppn::find($v->dokumen_pendukung_sppn_id);
                        File::delete(public_path('dokumen/' . $dok->dokumen_pendukung_sppn_nama));
                        $dok->delete();
                    }
                }
                $faktur_pajak = $request->faktur_pajak_spp;
                // dd($faktur_pajak);
                if ($faktur_pajak == '') {
                    $fp = new FakturPajak;
                    $fp->sppb_id = $request->sppb_id;
                    $fp->sppn_id = $request->sppn_id;
                    $fp->faktur_pajak_nomor = "-";
                    $fp->save();
                } else {
                    foreach ($faktur_pajak as $key => $value) {
                        $fp = new FakturPajak;
                        $fp->sppb_id = $request->sppb_id;
                        $fp->sppn_id = $request->sppn_id;
                        $fp->faktur_pajak_nomor = $value['fp'];
                        $fp->save();
                    }
                }

                foreach ($karyawan_sppn as $key => $value) {
                    $krywn_sppn = NamaKaryawanModel::findOrFail($value->karyawan_id);
                    $krywn_sppn->delete();
                }
                // if($request->jenis == "karyawan"){
                //     if($request->metode_pembayaran_sppn == "kas"){
                //         $karyawan = $request->atas_nama_bank_sppn_kas;
                //         if($request->pilih_data_sppn == 'input_data'){
                //             foreach($karyawan as $key => $value){
                //                 $krywn = new NamaKaryawanModel;
                //                 $krywn -> sppb_id = null;
                //                 $krywn -> sppn_id = $request->sppn_id;
                //                 $krywn -> karyawan_nama = $value;
                //                 $krywn -> save();
                //             }
                //         }else{
                //             $krywn = new NamaKaryawanModel;
                //             $krywn -> sppb_id = null;
                //             $krywn -> sppn_id = $request->sppn_id;
                //             $krywn -> karyawan_nama = "TERLAMPIR";
                //             $krywn -> save();
                //         }
                //     }
                //     else{
                //         $karyawan = $request->karyawan_sppn;
                //         if($request->pilih_data_sppn == 'input_data'){
                //             foreach($karyawan as $key => $value){
                //                 $krywn = new NamaKaryawanModel;
                //                 $krywn -> sppb_id = null;
                //                 $krywn -> sppn_id = $request->sppn_id;
                //                 $krywn -> karyawan_nama = $value['nama'];
                //                 $krywn -> karyawan_nama_bank = $value['bank'];
                //                 $krywn -> karyawan_no_rek = $value['no_rek'];
                //                 $krywn -> save();
                //             }
                //         }else{
                //             $krywn = new NamaKaryawanModel;
                //                 $krywn -> sppb_id = null;
                //                 $krywn -> sppn_id = $request->sppn_id;
                //                 $krywn -> karyawan_nama = "TERLAMPIR";
                //                 $krywn -> karyawan_nama_bank = "TERLAMPIR";
                //                 $krywn -> karyawan_no_rek = "TERLAMPIR";
                //                 $krywn -> save();
                //         }
                //     }
                // }

                if ($request->jenis == "karyawan") {
                    $karyawanSppn = new NamaKaryawanModel();
                    $karyawanSppn->sppb_id = null;
                    $karyawanSppn->sppn_id = $idsppn;
                    $karyawanSppn->karyawan_nama = $request->diterima_dari;
                    $karyawanSppn->karyawan_alamat = $request->alamat_sppn;
                    $karyawanSppn->save();
                } else {
                    $karyawanSppn = new NamaKaryawanModel();
                    $karyawanSppn->sppb_id = null;
                    $karyawanSppn->sppn_id = $idsppn;
                    $karyawanSppn->karyawan_nama = $request->diterima_dari;
                    $karyawanSppn->karyawan_alamat = $request->alamat_sppn;
                    $karyawanSppn->save();
                }
                $dokumenpendukungsppn = $request->file('dokumen_pendukung_sppn');
                if ($dokumenpendukungsppn != null) {
                    foreach ($dokumenpendukungsppn as $file) {
                        $dokumenpendukungsppn_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungsppns = $current . '-' . $dokumenpendukungsppn_file_name;
                        $file->move('dokumen', $dokumenpendukungsppns);
                        // DokumenPendukungSppb::create([
                        //     'sppb_id' => $request->sppb_id,
                        //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                        // ]);
                        $dokumenpendukungsppnsppn = new DokumenPendukungSppn;
                        $dokumenpendukungsppnsppn->sppn_id = $request->sppn_id;
                        $dokumenpendukungsppnsppn->dokumen_pendukung_sppn_nama = $dokumenpendukungs;
                        $dokumenpendukungsppnsppn->save();
                    }
                }

                foreach ($idisisppn as $i => $v) {
                    $isi = IsiSppn::find($v->sppn_isi_id);
                    $isi->delete();
                    foreach ($iduraiansppn[$i] as $u => $va) {
                        $uraian = IsiUraianSppn::find($va->sppn_uraian_id);
                        $uraian->delete();
                    }
                }
                $isisppn = $request->isi_sppn;
                $total1 = 0;
                $total2 = 0;
                foreach ($isisppn as $isi => $value) {

                    $isisppn = new isisppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    // $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }

                    if ($value['jenis_center'] == 'cost_center') {
                        $isisppn->master_cost_center_id = $value['cost_center'];
                    } else {
                        $isisppn->master_profit_center_id = $value['profit_center'];
                    }
                    $isisppn->master_cash_flow_id = $value['cash_flow'];
                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        //dd($request->uraian_sppn);
                        $a = str_replace('.', '', $value2['jumlah']);
                        $angka_pajak = str_replace('.', '', $value2['pajak']);
                        $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                        $angka_potongan = str_replace('.', '', $value2['potongan']);
                        $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                        if (isset($value2['type_pajak_sppn'])) {
                            $tanpapajak = substr($value2['type_pajak_sppn'], 0, 9);
                            if ($tanpapajak == 'tanpa_paj') {
                                $b = $a;
                            } else if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        } else {
                            if ($angka_akhir != null) {
                                $b = $angka_akhir;
                            } else {
                                $b = $a;
                            }
                        }
                        $isiuraiansppn = new IsiUraiansppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_pph = str_replace('.', '', $value2['pph']);
                        $isiuraiansppn->sppn_pajak_manual = $value2['manual'];
                        $isiuraiansppn->sppn_uraian_nominal = $a;
                        $isiuraiansppn->sppn_nominal_pajak = $angka_pajak;
                        if (isset($value2['type_pajak_sppn'])) {
                            if ($tanpapajak == 'tanpa_paj') {
                                $isiuraiansppn->sppn_nominal_akhir = $a;
                            } else {
                                $isiuraiansppn->sppn_nominal_akhir = $angka_akhir;
                            }
                        } else {
                            $isiuraiansppn->sppn_nominal_akhir = $a;
                        }
                        $isiuraiansppn->sppn_dpp_ppn = $angka_dpp_ppn;
                        $isiuraiansppn->sppn_potongan = $angka_potongan;
                        $jenispajak = substr($value2['type_pajak_sppn'], 0, 9);  //substring untuk mengambil type pajak
                        if ($jenispajak == 'wapu_sppn') {
                            $jeniswapu = substr($value2['pilih_wapu_sppn'], 0, 12); // substring untuk mengambil pilih wapu
                            if ($jeniswapu == 'wapu_pph21_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 2,5%";
                            } else if ($jeniswapu == 'wapu_pph21_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 7,5%";
                            } else if ($jeniswapu == 'wapu_pph21_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 21 12,5%";
                            } else if ($jeniswapu == 'wapu_pph22_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 22 1,5%";
                            } else if ($jeniswapu == 'wapu_pph23_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 2%";
                            } else if ($jeniswapu == 'wapu_pph23_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 15%";
                            } else if ($jeniswapu == 'wapu_pph23_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 23 0%";
                            } else if ($jeniswapu == 'wapu_pph26_a') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 0%";
                            } else if ($jeniswapu == 'wapu_pph26_b') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 10%";
                            } else if ($jeniswapu == 'wapu_pph26_c') {
                                $isiuraiansppn->sppn_pajak_wapu = "PPh 26 20%";
                            } else if ($jeniswapu == 'wapu_pasal4a') {
                                $isiuraiansppn->sppn_pajak_wapu = "Pasal 4 Ayat 2";
                            } else if ($jeniswapu == 'wapu_normal_') {
                                $isiuraiansppn->sppn_pajak_wapu = "wapu Normal 11%";
                            } else if ($jeniswapu == 'wapu_nilai_l') {
                                $isiuraiansppn->sppn_pajak_wapu = "wapu 1,1%";
                            } else if ($jeniswapu == 'wapu_manual_') {
                                $isiuraiansppn->sppn_pajak_wapu = "Manual";
                            } else {
                                // Kondisi wapu diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'waba_sppn') {
                            $jeniswaba = substr($value2['pilih_waba_sppn'], 0, 12); // substring untuk mengambil pilih waba
                            // dd($jeniswaba);
                            if ($jeniswaba == 'waba_pph21_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 2,5%";
                            } else if ($jeniswaba == 'waba_pph21_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 7,5%";
                            } else if ($jeniswaba == 'waba_pph21_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 21 12,5%";
                            } else if ($jeniswaba == 'waba_pph22_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 22 1,5%";
                            } else if ($jeniswaba == 'waba_pph23_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 2%";
                            } else if ($jeniswaba == 'waba_pph23_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 15%";
                            } else if ($jeniswaba == 'waba_pph23_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 23 0%";
                            } else if ($jeniswaba == 'waba_pph26_a') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 0%";
                            } else if ($jeniswaba == 'waba_pph26_b') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 10%";
                            } else if ($jeniswaba == 'waba_pph26_c') {
                                $isiuraiansppn->sppn_pajak_waba = "PPh 26 20%";
                            } else if ($jeniswaba == 'waba_pasal4a') {
                                $isiuraiansppn->sppn_pajak_waba = "Pasal 4 Ayat 2";
                            } else if ($jeniswaba == 'waba_normal_') {
                                $isiuraiansppn->sppn_pajak_waba = "Waba Normal 11%";
                            } else if ($jeniswaba == 'waba_nilai_l') {
                                $isiuraiansppn->sppn_pajak_waba = "Waba 1,1%";
                            } else if ($jeniswaba == 'waba_manual_') {
                                $isiuraiansppn->sppn_pajak_waba = "Manual";
                            } else {
                                // Kondisi waba diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'pph_sppn_') {
                            $jenispph = substr($value2['pilih_pph_sppn'], 0, 12); // substring untuk mengambil pilih pph
                            if ($jenispph == 'pph21_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 2,5%";
                            } else if ($jenispph == 'pph21_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 7,5%";
                            } else if ($jenispph == 'pph21_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 21 12,5%";
                            } else if ($jenispph == 'pph22_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 22 1,5%";
                            } else if ($jenispph == 'pph23_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 2%";
                            } else if ($jenispph == 'pph23_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 15%";
                            } else if ($jenispph == 'pph23_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 23 0%";
                            } else if ($jenispph == 'pph26_a_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 0%";
                            } else if ($jenispph == 'pph26_b_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 10%";
                            } else if ($jenispph == 'pph26_c_sppn') {
                                $isiuraiansppn->sppn_pajak_pph = "PPh 26 20%";
                            } else if ($jenispph == 'pphpasal4_ay') {
                                $isiuraiansppn->sppn_pajak_pph = "Pasal 4 Ayat 2";
                            } else if ($jenispph == 'pph_manual_s') {
                                $isiuraiansppn->sppn_pajak_pph = "Manual";
                            } else {
                                // Kondisi pph diluar daftar
                            }
                        } else if ($jenispajak == 'tanpa_paj') {
                            $isiuraiansppn->sppn_tanpa_pajak = "Ya";
                        } else {
                            // kondisi pilihan pajak tanpa kombinasi
                        }
                        $isiuraiansppn->save();
                        $total1 += $b;
                    }
                }
                $totals = $total1 + $total2;
                $isisumsppn = Sppn::find($request->sppn_id);
                $isisumsppn->sppn_jumlah = $totals;
                $isisumsppn->save();

                $spp = Spp::find($id);
                $spp->master_bagian_id = $request->bagian_sppb;
                $spp->spp_tanggal = $tanggals;
                $spp->spp_jenis_sumber_dana = $sumberdana;
                $spp->save();
                DB::commit();
                $action = $request->status_btn;
                if ($action == "0") {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
            } catch (\Exception $e) {
                DB::rollback();
                //dd($e);
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
    }

    public function viewupdate($id)
    {
        $company = Session::get('company');
        // dd($request->karyawan_sppb_input,$request->pilih_data_sppb,$request->penerima_kas_sppb_karyawan_master,$request->penerima_kas_sppb_karyawan);
        $rekening = Rekening::all()->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0);

        $profitcenter = ProfitCenter::where('company_id', $company)->get();

        $costcenter = CostCenter::all();
        $sumberDana = SumberDana::All();
        $cashflow = CashFlow::all();
        $bagian = Session::get('bagian');
        $level = Session::get('level');
        $client = new Client();
        $gl = GL::All();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);
        if (in_array($bagian, [124, 126, 127])) {
            $customer = DB::table('master_customer')->get();
        } else {
            $customer = collect();
        }
        $karyawan_all = json_decode($response->getBody());
        $bagian_karyawan = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', $bagian)
            ->select('master_bagian.*')->first();

        $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use ($ino_bagian_id) {
            return $value->bagian_id == $ino_bagian_id;
        });
        if ($level == 99) {
            $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) {
                return $value->bagian_id != 7;
            });
        }
        $bagianall = Bagian::where('master_bagian_id', '!=', 10)->get();
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
            ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
            ->select('spp_id', 'sppb_id', 'sppn_id', 'master_sumber_dana.*')->first();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        if ($idsppb != null) {
            $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb.*', 'master_bagian.*', 'master_vendor.*')->first();
            $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $idsppb)
                ->leftjoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                ->leftjoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftjoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_customer.*', 'master_profit_center.*', 'master_cost_center.*', 'master_cash_flow.*')->get();
            $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id', '=', $datasppb->sppb_id)->select('faktur_pajak_nomor')->get();

            $dokpensppb = DB::table('dokumen_pendukung_sppb')->where('dokumen_pendukung_sppb.sppb_id', '=', $datasppb->sppb_id)
                ->select('dokumen_pendukung_sppb.*')->get();
            // dd($dokpensppb);
            foreach ($sppbisi as $a => $value2) {
                $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
            }

            $isisppb = [];
            // dd($sppburaian);
            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
            }

            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
        } else {
            $data_sppb = [];
            $dokpensppb = [];
        }
        if ($idsppn != null) {
            $datasppn = DB::table('sppn')
                ->where('sppn_id', '=', $idsppn)->leftJoin('master_bagian', 'sppn.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppn.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppn.*', 'master_bagian.*', 'master_vendor.*')->first();

            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)
                ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftjoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_customer.*', 'master_profit_center.*', 'master_cash_flow.*')->get();
            //dd($sppnisi);
            $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $datasppn->sppn_id)->select('faktur_pajak_nomor')->get();

            $dokpensppn = DB::table('dokumen_pendukung_sppn')->where('sppn_id', '=', $datasppn->sppn_id)
                ->select('dokumen_pendukung_sppn.*')->get();

            foreach ($sppnisi as $a => $value1) {
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value1->sppn_isi_id)->select('sppn_uraian.*')->get();
            }

            $isisppn = [];
            foreach ($sppnisi as $s => $val) {
                $isisppn[] = collect($val)->push($sppnuraian[$s]);
            }

            $data_sppn = [];
            $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
            // dd($data_sppb,$data_sppn);
        } else {
            $data_sppn = null;
            $dokpensppn = null;
        }
        // dd($data_sppb,$data_sppn);

        $form = 0;
        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            $karyawan_no_vendor_sppn = null;
            $karyawan_sppn = null;
        } else if (isset($data_sppn) && empty($data_sppb)) {
            $form = 2;
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {

                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
            $karyawan_no_vendor_sppb = null;
            $karyawan_sppb = null;
        } else {
            $form = 3;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
        }
        // dd($karyawan_sppb);
        // dd($data_sppb);
        $vendor = Vendor::All();
        $data = array(
            'sppb' => $data_sppb,
            'sppn' => $data_sppn,
            'dokpensppb' => $dokpensppb,
            'dokpensppn' => $dokpensppn,
            'formspp' => $form,
            'rekening' => $rekening,
            'bagianall' => $bagianall,
            'profitcenter' => $profitcenter,
            'costcenter' => $costcenter,
            'cashflow' => $cashflow,
            'spp_id' => $idspp,
            'vendor' => $vendor,
            'sumberdana' => $sumberDana,
            'karyawan' => $karyawan_bagian,
            'karyawan_sppb' => $karyawan_sppb,
            'karyawan_sppn' => $karyawan_sppn,
            'gl' => $gl,
            'customer' => $customer
        );
        // dd($data);

        $bagianspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.master_bagian_id')->get();
        // dd($bagianspp);
        $grup_id = Session::get('grup_ui');
        return view('page.spp.spp_edit', $data);
    }

    public function viewdetail($id)
    {
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
            ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
            ->select('spp.*', 'master_sumber_dana.*')->first();
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
            ->join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_id')
            ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;


        if ($idsppb != null) {
            $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('sppb.*', 'master_bagian.*')->first();
            $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
            $sppb_bayar = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $idsppb)->select('sppb_bayar.*')->first();
            $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id', '=', $datasppb->sppb_id)->select('faktur_pajak_nomor')->get();

            $dokpensppb = DB::table('dokumen_pendukung_sppb')->where('dokumen_pendukung_sppb.sppb_id', '=', $datasppb->sppb_id)
                ->select('dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama')->get();
            // dd($dokpensppb);
            foreach ($sppbisi as $a => $value2) {
                $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
            }
            // dd($sppburaian);
            $isisppb = [];
            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
            }
            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
            // dd($data_sppb);
        } else {
            $data_sppb = [];
            $dokpensppb = [];
            $sppb_bayar = null;
        }
        if ($idsppn != null) {

            $datasppn = DB::table('sppn')
                ->where('sppn_id', '=', $idsppn)->leftJoin('master_bagian', 'sppn.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('sppn.*', 'master_bagian.*')->first();

            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_gl.*')->get();
            // dd($sppnisi);
            $sppn_terima = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $idsppn)->select('sppn_terima.*')->first();
            $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $datasppn->sppn_id)->select('faktur_pajak_nomor')->get();

            $dokpensppn = DB::table('dokumen_pendukung_sppn')->where('sppn_id', '=', $datasppn->sppn_id)
                ->select('dokumen_pendukung_sppn.*')->get();

            foreach ($sppnisi as $a => $value1) {
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value1->sppn_isi_id)->select('sppn_uraian.*')->get();
            }

            $isisppn = [];
            foreach ($sppnisi as $s => $val) {
                $isisppn[] = collect($val)->push($sppnuraian[$s]);
            }

            $data_sppn = [];
            $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
            // dd($data_sppb,$data_sppn);
        } else {
            $data_sppn = null;
            $dokpensppn = null;
            $sppn_terima = null;
        }
        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());

        $form = 0;
        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            $karyawan_no_vendor_sppn = null;
            $karyawan_sppn = null;
        } else if (isset($data_sppn) && empty($data_sppb)) {
            $form = 2;
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
            $karyawan_no_vendor_sppb = null;
            $karyawan_sppb = null;
        } else {
            $form = 3;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
        }
        // dd($idspp);
        $data = array(
            'spp' => $idspp,
            'sppb' => $data_sppb,
            'sppn' => $data_sppn,
            'dokpensppb' => $dokpensppb,
            'dokpensppn' => $dokpensppn,
            'formspp' => $form,
            'doktam' => $doktam,
            'dok_kabag' => $idspp->spp_kabag,
            'id' => $idspp->spp_id,
            'status' => $idspp->spp_status_proses,
            'sppb_bayar' => $sppb_bayar,
            'sppn_terima' => $sppn_terima,
            'no_vendor_sppb' => $karyawan_no_vendor_sppb,
            'no_vendor_sppn' => $karyawan_no_vendor_sppn,
        );
        return view('page.spp.spp_detail', $data);
    }

    public function cetak($id)
    {
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.*')->first();
        $perusahaan = DB::table('master_company')->where('company_id', '=', $idspp->company_id)->select('company_nama')->first();
        dd($perusahaan);
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
            ->join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_id')
            ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        if ($idsppb != null) {
            $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb.*', 'master_bagian.*', 'master_vendor.*')->first();
            $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')

                ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
            $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id', '=', $datasppb->sppb_id)->select('faktur_pajak_nomor')->get();

            foreach ($sppbisi as $a => $value2) {
                $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
            }

            $isisppb = [];
            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
            }
            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
        } else {
            $data_sppb = null;
        }
        if ($idsppn != null) {
            $datasppn = DB::table('sppn')
                ->where('sppn_id', '=', $idsppn)->leftJoin('master_bagian', 'sppn.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppn.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppn.*', 'master_bagian.*', 'master_vendor.*')->first();

            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)
                ->leftJoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_gl.master_customer_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
            $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $datasppn->sppn_id)->select('faktur_pajak_nomor')->get();

            foreach ($sppnisi as $a => $value1) {
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value1->sppn_isi_id)->select('sppn_uraian.*')->get();
            }

            $isisppn = [];
            foreach ($sppnisi as $s => $val) {
                $isisppn[] = collect($val)->push($sppnuraian[$s]);
            }

            $data_sppn = [];
            $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
        } else {
            $data_sppn = null;
        }
        $form = 0;

        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());


        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            $karyawan_no_vendor_sppn = null;
            $karyawan_sppn = null;
        } else if (isset($data_sppn) && empty($data_sppb)) {
            $form = 2;
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
            $karyawan_no_vendor_sppb = null;
            $karyawan_sppb = null;
        } else {
            $form = 3;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_sppb = $Krywn;
            } else {
                $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppb = null;
            }
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        return $value->karyawan_nama == $nama;
                    });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                        }
                    }
                } else {
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_sppn = $Krywn;
            } else {
                $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                $karyawan_no_vendor_sppn = null;
            }
        }
        //dd($karyawan_no_vendor_sppb);
        $id_validasi = base64_encode($id);
        $data = array(
            'sppb' => $data_sppb,
            'sppn' => $data_sppn,
            'formspp' => $form,
            'no_vendor_sppb' => $karyawan_no_vendor_sppb,
            'no_vendor_sppn' => $karyawan_no_vendor_sppn,
            'karyawan_sppb' => $karyawan_sppb,
            'karyawan_sppn' => $karyawan_sppn,
            'id' => bin2hex($id_validasi),
            'company' => $perusahaan,
        );
        //dd($data_sppb,$data_sppn);
        dd($data);
        return view('page.spp.spp_cetak', $data);
    }

    public function bayar(Request $request, $id)
    {

        $current = date('His-dmY');
        $akses = Session::get('hak_akses');
        $form_a = $request->form_a;
        $form_b = $request->form_b;
        // dd($form_a,$form_b);
        if ($form_a == null) {
            $form = $form_b;
        } else {
            $form = $form_a;
        }
        if ($form = 2) {
            if (isset($request->tanggal_bayar_sppb)) {
                $form = 0;
            } else {
                $form = 1;
            }
        }
        $idsppb = DB::table('spp')->where('spp.sppb_id', '=', $id)->select('spp_id')->first();
        $idsppn = DB::table('spp')->where('spp.sppn_id', '=', $id)->select('spp_id')->first();
        if ($form == 0) {
            $tanggal = $request->tanggal_bayar_sppb;
            $tanggals = date('Y-m-d', strtotime($tanggal));

            $file_bukti = $request->file('bukti_sppb');
            $file_bukti_file_name = str_replace("'", '', $file_bukti->getClientOriginalName());
            $bukti = $current . '-' . $file_bukti_file_name;
            $file_bukti->move('dokumen/', $bukti);

            $bayar = new SppbBayar;
            $bayar->sppb_id = $id;
            $bayar->sppb_bayar_tanggal = $tanggals;
            $bayar->sppb_bayar_nomor_bukti_kas = $request->nomor_bukti_kas_sppb;
            $bayar->master_rekening_id = $request->rekening_bank_sppb;
            $bayar->sppb_kode_kbb_bayar = $request->sppb_kode_kbb_bayar;
            $bayar->sppb_kode_sap_bayar = $request->sppb_kode_sap_bayar;
            $bayar->sppb_bayar_bukti = $bukti;
            $bayar->save();

            $spp = Spp::where('sppb_id', '=', $id)->first();
            $spp->spp_status_bayar = 1;
            $spp->save();

            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $idsppb->spp_id;
            $rekam_jejak->master_user_id = $akses;
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->rekam_jejak_status = 2;
            $rekam_jejak->save();

            $spp = Spp::find($idsppb->spp_id);
            if ($spp->sppn_id == null) {
                $spp->spp_status_lunas = 1;
                $spp->save();
            } else {
                if ($spp->spp_status_terima == null) {
                    $spp->spp_status_lunas = null;
                    $spp->save();
                } else {
                    $spp->spp_status_lunas = 1;
                    $spp->save();
                }
            }
        } else if ($form == 1) {
            $tanggal = $request->tanggal_terima_sppn;
            $tanggals = date('Y-m-d', strtotime($tanggal));
            $file_bukti = $request->file('bukti_sppn');

            $bukti_file_name = str_replace("'", '', $file_bukti->getClientOriginalName());
            $bukti = $current . '-' . $bukti_file_name;
            $file_bukti->move('dokumen/', $bukti);

            $terima = new SppnTerima;
            $terima->sppn_id = $id;
            $terima->sppn_terima_tanggal = $tanggals;
            $terima->sppn_terima_nomor_bukti_kas = $request->nomor_bukti_kas_sppn;
            $terima->master_rekening_id = $request->rekening_bank_sppn;
            $terima->sppn_kode_kbb_terima = $request->sppn_kode_kbb_terima;
            $terima->sppn_kode_sap_terima = $request->sppn_kode_sap_terima;
            $terima->sppn_terima_bukti = $bukti;
            $terima->save();

            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $idsppn->spp_id;
            $rekam_jejak->master_user_id = $akses;
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->rekam_jejak_status = 3;
            $rekam_jejak->save();

            $spp = Spp::where('sppn_id', '=', $id)->first();
            $spp->spp_status_terima = 1;
            $spp->save();

            $spp = Spp::find($idsppn->spp_id);
            if ($spp->sppb_id == null) {
                $spp->spp_status_lunas = 1;
                $spp->save();
            } else {
                if ($spp->spp_status_bayar == null) {
                    $spp->spp_status_lunas = null;
                    $spp->save();
                } else {
                    $spp->spp_status_lunas = 1;
                    $spp->save();
                }
            }
        }
        return redirect('sppd');
    }

    public function update_bayar(Request $request, $id)
    {
        $current = date('His-dmY');
        $level = Session::get('level');

        if (isset($request->nomor_bukti_kas_sppb)) {
            $tanggal = $request->tanggal_bayar_sppb;
            $tanggals = date('Y-m-d', strtotime($tanggal));

            $bayar = SppbBayar::find($id);

            $file_bukti = $request->file('bukti_sppb');
            if ($file_bukti) {
                $bukti_file_name = str_replace("'", '', $file_bukti->getClientOriginalName());
                $bukti = $current . '-' . $bukti_file_name;
                $file_bukti->move('dokumen/', $bukti);
                File::delete(public_path('dokumen/' . $bayar->sppb_bayar_bukti));
                $bayar->sppb_bayar_bukti = $bukti;
            }
            $bayar->sppb_bayar_tanggal = $tanggals;
            $bayar->sppb_bayar_nomor_bukti_kas = $request->nomor_bukti_kas_sppb;
            $bayar->master_rekening_id = $request->rekening_bank_sppb;

            $bayar->save();
        } else if (isset($request->nomor_bukti_kas_sppn)) {
            $tanggal = $request->tanggal_terima_sppn;
            $tanggals = date('Y-m-d', strtotime($tanggal));

            $terima = SppnTerima::find($id);
            $file_bukti = $request->file('bukti_sppn');
            if ($file_bukti) {
                File::delete(public_path('dokumen/' . $terima->sppn_terima_bukti));
                $bukti_file_name = str_replace("'", '', $file_bukti->getClientOriginalName());
                $bukti = $current . '-' . $bukti_file_name;
                $file_bukti->move('dokumen/', $bukti);
                $terima->sppn_terima_bukti = $bukti;
            }
            $terima->sppn_terima_tanggal = $tanggals;
            $terima->sppn_terima_nomor_bukti_kas = $request->nomor_bukti_kas_sppn;
            $terima->master_rekening_id = $request->rekening_bank_sppn;

            $terima->save();
        }
        return redirect('sppd');
    }

    public function cetakbuktikas($id, Request $request)
    {
        // dd($request->all());
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        $pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
        if ($pageWasRefreshed) {
            $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.*')->first();
            $perusahaan = DB::table('master_company')->where('company_id', '=', $idspp->company_id)->select('master_company.*')->first();
            // dd($perusahaan);
            $idsppb = $idspp->sppb_id;
            $idsppn = $idspp->sppn_id;
            $sppb = DB::table('sppb')->where('sppb_id', $idsppb)->select('sppb.*')->first();
            $sppn = DB::table('sppn')->where('sppn_id', $idsppn)->select('sppn.*')->first();
            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = isset($sppb->sppb_tanggal) ? $sppb->sppb_tanggal : $sppn->sppn_tanggal;
            $tanggals = date('d-m-Y', strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggals)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggals)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggals)->day;


            $buktikas_id = spp::where('spp_id', $id)->select('spp.*')->first();
            // dd($sppb->sppb_metode_pembayaran);
            if ($idsppb) {
                $urutansppb = DB::table('sppb_bukti_kas')->select([DB::raw('MAX(sppb_urutan_bukti_kas) as urutan')])->first();
                $urutan = $urutansppb->urutan;
                $buktikas = Sppb_bukti_kas::where('sppb_id', $buktikas_id->sppb_id)
                    ->where('sppb_id', $idsppb)
                    ->update(['sppb_urutan_bukti_kas' => $urutan]);
            } else {

                $urutansppn = DB::table('sppn_bukti_kas')->select([DB::raw('MAX(sppn_urutan_bukti_kas) as urutan')])->first();
                $urutan = $urutansppn->urutan;
                $buktikas = Sppn_bukti_kas::where('sppn_id', $buktikas_id->sppn_id)
                    ->where('sppn_id', $idsppn)
                    ->update(['sppn_urutan_bukti_kas' => $urutan]);
            }
        } else {
            $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.*')->first();
            $perusahaan = DB::table('master_company')->where('company_id', '=', $idspp->company_id)->select('master_company.*')->first();
            // dd($perusahaan);
            $idsppb = $idspp->sppb_id;
            $idsppn = $idspp->sppn_id;

            if ($idsppb != null) {
                $buktikas = DB::table('sppb_bukti_kas')->where('sppb_id', $idsppb)->select('sppb_bukti_kas.*')->first();
            } else {
                $buktikas = DB::table('sppn_bukti_kas')->where('sppn_id', $idsppn)->select('sppn_bukti_kas.*')->first();
            }

            // $buktikas = DB::table('sppb_bukti_kas')->where('sppb_id', $idsppb)->select('sppb_bukti_kas.*')->first();
            $sppb = DB::table('sppb')->where('sppb_id', $idsppb)->select('sppb.*')->first();
            $sppn = DB::table('sppn')->where('sppn_id', $idsppn)->select('sppn.*')->first();
            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

            $tanggal = isset($sppb->sppb_tanggal) ? $sppb->sppb_tanggal : $sppn->sppn_tanggal;
            $tanggals = date('d-m-Y', strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggals)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggals)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggals)->day;
            $buktikas_id = spp::where('spp_id', $id)->select('spp.*')->first();
            // $urutan_cetak = $urutan;
            // dd($buktikas);
            if ($idsppb != null) {
                //dd($buktikas->sppb_metode_pembayaran);
                if ($buktikas->sppb_urutan_bukti_kas == NULL) {
                    $urutansppb = DB::table('sppb_bukti_kas')
                        ->where('sppb_metode_pembayaran', $sppb->sppb_metode_pembayaran)
                        ->select([DB::raw('MAX(sppb_urutan_bukti_kas) as urutan')])->first();

                    $urutan = $urutansppb->urutan + 1;
                    $buktikas = Sppb_bukti_kas::where('sppb_id', $buktikas_id->sppb_id)->update(['sppb_urutan_bukti_kas' => $urutan]);
                } else {
                    $urutansppb = DB::table('sppb_bukti_kas')
                        ->where('sppb_metode_pembayaran', $sppb->sppb_metode_pembayaran)
                        ->where('sppb_id', $idsppb)->select('sppb_bukti_kas.*')->first();
                    // dd($urutansppb);
                    $urutan = $urutansppb->sppb_urutan_bukti_kas;
                    $buktikas = Sppb_bukti_kas::where('sppb_id', $buktikas_id->sppb_id)->update(['sppb_urutan_bukti_kas' => $urutan]);
                }
            } else {
                if ($buktikas->sppn_urutan_bukti_kas == NULL) {
                    $urutansppn = DB::table('sppn_bukti_kas')->select([DB::raw('MAX(sppn_urutan_bukti_kas) as urutan')])->first();
                    $urutan = $urutansppn->urutan + 1;
                    $buktikas = Sppn_bukti_kas::where('sppn_id', $buktikas_id->sppn_id)->update(['sppn_urutan_bukti_kas' => $urutan]);
                } else {
                    $urutansppn = DB::table('sppn_bukti_kas')->where('sppn_id', $idsppn)->select('sppn_bukti_kas.*')->first();
                    $urutan = $urutansppn->sppn_urutan_bukti_kas;
                    $buktikas = Sppn_bukti_kas::where('sppn_id', $buktikas_id->sppn_id)->update(['sppn_urutan_bukti_kas' => $urutan]);
                }
            }
            $tanggal_cetak_sppb = null;
            $tanggal_cetak_sppn = null;

            if ($idsppb != null) {
                $sppb_bukti_kas = DB::table('sppb_bukti_kas')->where('sppb_id', '=', $idsppb)->select('sppb_bukti_kas.*')->first();

                if ($sppb_bukti_kas) {
                    $tanggal_cetak1 = ($sppb_bukti_kas->sppb_bukti_kas_tanggal == "" || $sppb_bukti_kas->sppb_bukti_kas_tanggal == null) ? Carbon::today()->format('Y-m-d') : $sppb_bukti_kas->sppb_bukti_kas_tanggal;
                    // dd($tanggal_cetak1);
                    $tanggal_cetak_sppb = Carbon::createFromFormat('Y-m-d', $tanggal_cetak1)->format('Y-m-d');

                    if (!$sppb_bukti_kas->sppb_bukti_kas_tanggal) {
                        DB::table('sppb_bukti_kas')->where('sppb_id', '=', $idsppb)->update(['sppb_bukti_kas_tanggal' => $tanggal_cetak_sppb]);
                    }
                }
            }
            // dd($tanggal_cetak_sppb);

            if ($idsppn != null) {
                $sppn_bukti_kas = DB::table('sppn_bukti_kas')->where('sppn_id', '=', $idsppn)->select('sppn_bukti_kas.*')->first();

                if ($sppn_bukti_kas) {
                    $tanggal_cetak2 = $sppn_bukti_kas->sppn_bukti_kas_tanggal == "" ? Carbon::now()->format('Y-m-d') : $sppn_bukti_kas->sppn_bukti_kas_tanggal;
                    $tanggal_cetak_sppn = Carbon::createFromFormat('Y-m-d', $tanggal_cetak2)->format('Y-m-d');

                    if (!$sppn_bukti_kas->sppn_bukti_kas_tanggal) {
                        DB::table('sppn_bukti_kas')->where('sppn_id', '=', $idsppn)->update(['sppn_bukti_kas_tanggal' => $tanggal_cetak_sppn]);
                    }
                }
            }
            if ($idsppb != null && $idsppn != null) {
                if ($tanggal_cetak_sppb != $tanggal_cetak_sppn) {
                    // Update SPPN dengan tanggal yang sama dengan SPPB, jika SPPB memiliki tanggal
                    if ($tanggal_cetak_sppb) {
                        DB::table('sppn_bukti_kas')->where('sppn_id', '=', $idsppn)->update(['sppn_bukti_kas_tanggal' => $tanggal_cetak_sppb]);
                        $tanggal_cetak_sppn = $tanggal_cetak_sppb; // Pastikan tanggal SPPN sama dengan tanggal SPPB
                    }
                }
            }


            // dd($buktikas);
            // $buktikas->sppb_urutan_bukti_kas = $urutan;
            // $buktikas->save();
            $bulan = $bulanromawi[$month];
            // dd($sppb->sppb_metode_pembayaran);
            // $nomor = "KA" . "/" . $urutan . "/" . $bulan . "/" . $tahun;
            // if (isset($sppb->sppb_metode_pembayaran)) {
            //     if ($sppb->sppb_metode_pembayaran == 'bank') {
            //         $nomor = "BA" . "/" . $urutan . "/" . $bulan . "/" . $tahun;
            //     }
            // }
        }

        $cekkode_sap = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $idsppb)->select('master_kode_vendor_id')->first();
        $sppbisi = [];
        $sppnisi = [];
        $nama_karyawan_sppb = [];
        $nama_karyawan_sppn = [];
        $cek_sap = $cekkode_sap;
        if ($idsppb != null) {
            $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb.*', 'master_bagian.*', 'master_vendor.*')->first();
            $databuktikassppb = DB::table('sppb_bukti_kas')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_gl', 'sppb_bukti_kas.master_rekening_id', '=', 'master_gl.master_gl_id')
                ->select('sppb_bukti_kas.*', 'master_gl.*')->first();
            $datafootersppb = DB::table('nama_karyawan')->where('sppb_id', '=', $idsppb)
                ->select('nama_karyawan.*')->first();
            $databayar = DB::table('sppb_bayar')->where('sppb_id', '=', $idsppb)
                ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('sppb_bayar.*', 'master_rekening.*')->first();
            $databayar_vendor = DB::table('sppb_bayar')->where('sppb_id', '=', $idsppb)
                ->join('master_vendor', 'sppb_bayar.master_rekening_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb_bayar.*', 'master_vendor.*')->first();
            $nama_karyawan_sppb = DB::table('nama_karyawan')->where('sppb_id', $idsppb)->select('*')->get();
            if ($cek_sap != NULL) {
                $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                    ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*', 'sppb_uraian.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')
                    ->orderBy('sppb_isi.sppb_isi_id', 'asc')
                    ->get();
                // dd($sppbisi);

                //dd($datasppb);
            } else {
                $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                    ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*', 'sppb_uraian.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_gl.*')->get();
                // dd($datasppb->sppb_id);
            }

            // dd($sppbisi);
            foreach ($sppbisi as $a => $value2) {
                $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
            }

            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
                // dd($isisppb);
            }

            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb);
        } else {
            $data_sppb = null;
            $databayar = null;
            $databayar_vendor = null;
            $databuktikassppb = null;
            $datafootersppb = null;
        }
        if ($idsppn != null) {
            $datasppn = DB::table('sppn')
                ->where('sppn_id', '=', $idsppn)->leftJoin('master_bagian', 'sppn.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('sppn.*', 'master_bagian.*')->first();

            $dataterima = DB::table('sppn_terima')->where('sppn_id', '=', $idsppn)->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('sppn_terima.*', 'master_rekening.*')->first();
            $dataterima_vendor = DB::table('sppn_terima')->where('sppn_id', '=', $idsppn)->join('master_vendor', 'sppn_terima.master_rekening_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppn_terima.*', 'master_vendor.*')->first();
            $datafootersppn = DB::table('nama_karyawan')->where('sppn_id', '=', $idsppn)
                ->select('nama_karyawan.*')->first();
            $databuktikassppn = DB::table('sppn_bukti_kas')->where('sppn_id', '=', $idsppn)
                ->leftJoin('master_gl', 'sppn_bukti_kas.master_rekening_id', '=', 'master_gl.master_gl_id')
                ->select('sppn_bukti_kas.*', 'master_gl.*')->first();
            $nama_karyawan_sppn = DB::table('nama_karyawan')->where('sppn_id', $idsppn)->select('*')->get();
            if ($cek_sap != NULL) {
                $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $datasppn->sppn_id)
                    ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*', 'sppn_uraian.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();

                //dd($datasppn);
            } else {
                $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $datasppn->sppn_id)
                    ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*', 'sppn_uraian.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
                // dd($datasppn->sppb_id);
            }
            // dd($sppnisi);

            // $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
            //     ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
            //     ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
            //     ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*')->get();

            foreach ($sppnisi as $a => $value1) {
                $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value1->sppn_isi_id)->select('sppn_uraian.*')->get();
            }

            foreach ($sppnisi as $s => $val) {
                $isisppn[] = collect($val)->push($sppnuraian[$s]);
            }

            $data_sppn = [];
            $data_sppn = collect($datasppn)->push($isisppn);
        } else {
            $data_sppn = null;
            $dataterima = null;
            $dataterima_vendor = null;
            $databuktikassppn = null;
            $datafootersppn = null;
        }
        $form = 0;
        if (($databuktikassppb) == null) {
            $form = 2;
        } else if (($databuktikassppn) == null) {
            $form = 1;
        } else {
            $form = 3;
        }


        if (isset($data_sppb['sppb_tanggal'])) {
            $tangga_pembuatanl = new Carbon($data_sppb['sppb_tanggal']);
            $sppb_tanggal_pembuatan = Carbon::parse($tangga_pembuatanl)->locale('id')->isoFormat('D MMM Y');
            $sppn_tanggal_pembuatan = '';
        } else if (isset($data_sppn['sppn_tanggal'])) {
            $sppb_tanggal_pembuatan = '';
            $tangga_pembuatan2 = new Carbon($data_sppn['sppn_tanggal']);
            $sppn_tanggal_pembuatan = Carbon::parse($tangga_pembuatan2)->locale('id')->isoFormat('D MMM Y');
        } else {
            $tangga_pembuatanl = new Carbon($data_sppb['sppb_tanggal']);
            $sppb_tanggal_pembuatan = Carbon::parse($tangga_pembuatanl)->locale('id')->isoFormat('D MMM Y');
            $tangga_pembuatan2 = new Carbon($data_sppn['sppn_tanggal']);
            $sppn_tanggal_pembuatan = Carbon::parse($tangga_pembuatan2)->locale('id')->isoFormat('D MMM Y');
        }

        // dd($form,$databuktikassppn,$databuktikassppb);
        //dd($sppbisi);
        $spp = DB::table('spp')->where('spp_id', $id)->first();
        $date = Carbon::today()->locale('id')->isoFormat('D MMM Y');
        $total_jumlah_sppb = $data_sppb['sppb_total'] ?? 0;
        $total_jumlah_sppn = $data_sppn['sppn_jumlah'] ?? 0;
        // dd($data_sppb);

        $isSppbBankMethod = null;
        $isSppnBankMethod = null;
        if (isset($data_sppb)) {
            $isSppbBankMethod = $data_sppb['sppb_metode_pembayaran'] == 'bank' ? true : false;
        }
        if (isset($data_sppn)) {
            $isSppnBankMethod = (isset($data_sppb) && $data_sppb['sppb_metode_pembayaran'] == 'bank') || (!isset($data_sppb) && $data_sppn['sppn_metode_pembayaran'] == 'bank') || (!isset($data_sppb) && $data_sppn['sppn_metode_pembayaran'] == '') ? true : false;
        }
        // dd($perusahaan->company_id);

        $data_penandatangan_sppb = DB::table('master_cetak_bukti_kas as bukti_kas')
            ->where('bukti_kas.company_id', $perusahaan->company_id)
            ->where(function ($query) use ($data_sppb, $isSppbBankMethod, $total_jumlah_sppb) {
                if ($data_sppb) {
                    if ($isSppbBankMethod) {
                        $query->where('bukti_kas.is_bank', true);
                        if ($total_jumlah_sppb > 5000000000) {
                            $query->where('bukti_kas.lebih_dari_5_m', true);
                        } else {
                            $query->where('bukti_kas.lebih_dari_5_m', false);
                        }
                    } else {
                        $query->where('bukti_kas.is_bank', false);
                        if ($total_jumlah_sppb > 25000000) {
                            $query->where('bukti_kas.lebih_dari_25_jt', true);
                        } else {
                            $query->where('bukti_kas.lebih_dari_25_jt', false);
                        }
                    }
                }
            })->first();
            //dd($total_jumlah_sppb);
            //dd($data_penandatangan_sppb);

        $data_penandatangan_sppn = DB::table('master_cetak_bukti_kas as bukti_kas')
            ->where('bukti_kas.company_id', $perusahaan->company_id)
            ->where(function ($query) use ($data_sppn, $isSppnBankMethod, $total_jumlah_sppn) {
                if ($data_sppn) {
                    if ($isSppnBankMethod) {
                        $query->where('bukti_kas.is_bank', true);
                        if ($total_jumlah_sppn > 5000000000) {
                            $query->where('bukti_kas.lebih_dari_5_m', true);
                        } else {
                            $query->where('bukti_kas.lebih_dari_5_m', false);
                        }
                    } else {
                        $query->where('bukti_kas.is_bank', false);
                        if ($total_jumlah_sppn > 25000000) {
                            $query->where('bukti_kas.lebih_dari_25_jt', true);
                        } else {
                            $query->where('bukti_kas.lebih_dari_25_jt', false);
                        }
                    }
                }
            })->first();

         //dd($data_penandatangan_sppb, $data_sppb);
        $data = array(
            'spp' => $spp,
            'sppb_tanggal_pembuatan' => $sppb_tanggal_pembuatan,
            'sppn_tanggal_pembuatan' => $sppn_tanggal_pembuatan,
            'sppb' => $data_sppb,
            'sppn' => $data_sppn,
            'sppb_isi' =>   $sppbisi,
            'sppn_isi' =>   $sppnisi,
            'bayar' => $databayar,
            'databayar_vendor' => $databayar_vendor,
            'terima' => $dataterima,
            'dataterima_vendor' => $dataterima_vendor,
            'databuktikassppb' => $databuktikassppb,
            'databuktikassppn' => $databuktikassppn,
            'datafootersppb' => $datafootersppb,
            'datafootersppn' => $datafootersppn,
            'formspp' => $form,
            'tanggalcetak' => $date,
            // 'tanggal_cetak_sppb' => $tanggal_cetak_sppb,
            // 'tanggal_cetak_sppn' => $tanggal_cetak_sppn,
            'company' => $perusahaan->company_nama,
            'company_jenis' => $perusahaan->company_jenis,
            'company_id' => $perusahaan->company_id,
            'domisili' => $perusahaan->domisili_company,
            // 'nomor' => $nomor,
            'nama_karyawan_sppb' =>  $nama_karyawan_sppb,
            'nama_karyawan_sppn' =>  $nama_karyawan_sppn,
            'data_penandatangan_sppb' => $data_penandatangan_sppb,
            'data_penandatangan_sppn' => $data_penandatangan_sppn
        );
         //dd($data);

        return view('page.cetak_bukti_kas', $data);
    }

    public function advanced_search_gagal(Request $request)
    {
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $rekening = Rekening::All()->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0);
        $gl = GL::all();
        $vendor = Vendor::All();
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
        $b = Bagian::where('master_bagian_id', '!=', 10)->get();
        $kode_sap_vendor = Rekening::all();
        // dd($kode_sap);
        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ", $rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
        })->all();
        $sppb_bayar = [];

        $sppn_terima = [];
        $vendor_filter = $request->vendor;
        $datas = [];
        if ($vendor_filter !== "semua") {
            $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('sppb_isi.master_gl_id as gl_sppb', 'sppn_isi.master_gl_id as gl_sppn', 'sppb_isi.master_customer_id as customer_sppb', 'sppn_isi.master_customer_id as customer_sppn', 'sppb_isi.master_kode_vendor_id as sppb_sap', 'sppn_isi.master_kode_vendor_id as sppn_sap', 'sppd_posisi', 'sppd_status', 'spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_kabag', 'master_bagian_nama', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp.master_bagian_id', 'sppd_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('tanggal', 'desc')
                ->get();
        } else {
            $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('sppb_isi.master_gl_id as gl_sppb', 'sppn_isi.master_gl_id as gl_sppn', 'sppb_isi.master_customer_id as customer_sppb', 'sppn_isi.master_customer_id as customer_sppn', 'sppb_isi.master_kode_vendor_id as sppb_sap', 'sppn_isi.master_kode_vendor_id as sppn_sap', 'sppd_posisi', 'sppd_status', 'spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_kabag', 'master_bagian_nama', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp.master_bagian_id', 'sppd_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('tanggal', 'desc')
                ->get();
        }
        foreach ($datas as $v) {
            $posisi_dinamis[] = DB::table('master_hak_akses')->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                ->select('master_hak_akses.*')->first();
        }
        // dd($posisi_dinamis);
        $data_batal = [];
        $data_selesai = [];
        $data_cancel = [];
        $data_final = [];
        foreach ($datas as $d => $v) {
            if ($v->sppd_status == 3) {
                $data_final[] = $v;
            }
            if ($v->sppd_status == 4) {
                $data_cancel[] = $v;
            }
        }
        if ($request->index_advanced_search == "1") {
            $data_selesai = $data_final;
            $data_batal = $data_cancel;
            $dataf[] = collect($datas);
            if ($request->rentang_waktu) {
                foreach ($dataf as $key => $value) {
                    $data = $value->whereBetween('tanggal', $rentang_waktu);
                    // dd($rentang_waktu);
                }
            }
            if ($request->posisi_terkini !== 'semua') {
                $data = $data->where('spp_status_posisi', $request->posisi_terkini);
            }
            if ($request->bagian !== 'semua') {
                $data = $data->where('master_bagian_id', $request->bagian);
            }
            if ($request->status_bayar !== 'semua') {
                $data = $data->where('spp_status_lunas', $request->status_bayar);
            }
            if ($request->kode_sap_sppb !== 'semua') {
                $data = $data->where('sppb_sap', $request->kode_sap_sppb);
            }
            if ($request->kode_sap_sppn !== 'semua') {
                $data = $data->where('sppn_sap', $request->kode_sap_sppn);
            }
            if ($request->kode_gl_sppb !== 'semua') {
                $data = $data->where('gl_sppb', $request->kode_gl_sppb);
            }
            if ($request->kode_gl_sppn !== 'semua') {
                $data = $data->where('gl_sppn', $request->kode_gl_sppn);
            }
            $data = $data->values();
        } elseif ($request->index_advanced_search == "2") {
            $data = $datas;
            $data_selesai[] = collect($data_final);
            $data_batal = $data_cancel;
            if ($request->rentang_waktu) {

                foreach ($data_selesai as $key => $value) {
                    $data_selesai = $value->whereBetween('tanggal', $rentang_waktu);
                }
            }
            if ($request->bagian !== 'semua') {
                $data_selesai = $data_selesai->where('master_bagian_id', $request->bagian);
            }
            if ($request->vendor !== 'semua') {
                $data_selesai = $data_selesai->where('master_bank_id', $request->vendor);
            }
            if ($request->kode_sap_sppb !== 'semua') {
                $data = $data->where('sppb_sap', $request->kode_sap_sppb);
            }
            if ($request->kode_sap_sppn !== 'semua') {
                $data = $data->where('sppn_sap', $request->kode_sap_sppn);
            }
            if ($request->kode_gl_sppb !== 'semua') {
                $data = $data->where('gl_sppb', $request->kode_gl_sppb);
            }
            if ($request->kode_gl_sppn !== 'semua') {
                $data = $data->where('gl_sppn', $request->kode_gl_sppn);
            }
            $data_selesai = $data_selesai->values();
        } else {
            $data = $datas;
            $data_selesai = $data_final;
            $data_batal[] = collect($data_cancel);
            if ($request->rentang_waktu) {
                foreach ($data_batal as $key => $value) {
                    $data_batal = $value->whereBetween('tanggal', $rentang_waktu);
                }
            }
            if ($request->bagian !== 'semua') {
                $data = $data->where('master_bagian_id', $request->bagian);
            }
            if ($request->vendor !== 'semua') {
                $data = $data->where('master_bank_id', $request->vendor);
            }
            if ($request->kode_sap_sppb !== 'semua') {
                $data = $data->where('sppb_sap', $request->kode_sap_sppb);
            }
            if ($request->kode_sap_sppn !== 'semua') {
                $data = $data->where('sppn_sap', $request->kode_sap_sppn);
            }
            if ($request->kode_gl_sppb !== 'semua') {
                $data = $data->where('gl_sppb', $request->kode_gl_sppb);
            }
            if ($request->kode_gl_sppn !== 'semua') {
                $data = $data->where('gl_sppn', $request->kode_gl_sppn);
            }
            $data_batal = $data_batal->values();
        }
        if (isset($data)) {
            foreach ($data as $d => $v) {
                $status[] = DB::table('rekam_jejak')->where('rekam_jejak.spp_id', '=', $v->spp_id)->select('spp_id', 'master_user_id', 'rekam_jejak_revisi', 'created_at')->latest('created_at')->first();
                $rekam_jejak[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_id')
                    ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_id')
                    ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                    ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
                $posisi[] = DB::table('master_hak_akses')->where('master_hak_akses_id', '=', $v->spp_status_posisi)->select('master_hak_akses_keterangan')->first();
            }
        }

        if (isset($data_selesai)) {
            foreach ($data_selesai as $d => $v) {
                $rekam_jejak_selesai[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_id')
                    ->select('rekam_jejak.*', 'master_hak_akses.*')->get();
            }
        }
        if (empty($rekam_jejak_selesai)) {
            $data_selesai = [];
            $rekam_jejak_selesai = [];
        }

        if (!empty($data_batal)) {
            foreach ($data_batal as $d => $v) {
                $rekam_jejak_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_id')
                    ->select('rekam_jejak.*', 'master_hak_akses.*')->get();
                $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)->where('rekam_jejak_status', '=', 5)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_id')
                    ->select('rekam_jejak.*', 'master_hak_akses.*')->first();
            }
        }
        if (empty($rekam_jejak_batal)) {
            $data_batal = [];
            $rekam_jejak_batal = [];
            $posisi_batal = '';
        }

        if (isset($status)) {
            foreach ($status as $s) {

                if ($s->rekam_jejak_revisi != null) {
                    $status_revisi[] = 'Revisi oleh';
                } else {
                    $status_revisi[] = '';
                }
            }
        } else {
            $data = [];
            $posisi = '';
            $status = '';
            $status_revisi = [];
            $rekam_jejak = [];
            $sppb_bayar = [];
            $sppn_terima = [];
        }
        $index = $request->index_advanced_search;

        $index_cetak = 0;
        $id_cetak = 0;
        if ($level == 1) {
            foreach ($rekam_jejak as $k => $r) {
                foreach ($r as $l => $s) {
                    if ($s->master_user_id == 1 || $s->master_user_id == 2 && $s->master_user_id_tujuan < 2 || $s->rekam_jejak_revisi !== null && $s->master_user_id !== 2) {

                        $rekam_jejak_ob[$k][] = $s;
                    }
                }
            }
            foreach ($rekam_jejak_ob as $k => $r) {
                foreach ($r as $l => $s) {
                    if ($s->rekam_jejak_revisi !== null && $s->master_user_id !== 2 || $s->master_user_id_tujuan == 1) {
                        $a = $s->asal;
                    }
                    if ($s->rekam_jejak_revisi !== null && $s->master_user_id == 2 || $s->master_user_id_tujuan == 1) {
                        $asal[$k][$l] = $a;
                    } else {
                        $asal[$k][$l] = null;
                    }
                }
            }
            $rekam_jejak = $rekam_jejak_ob;
        } else {
            $asal = [];
        }

        return view('page.spp.sppd', compact('posisi_dinamis', 'kode_sap_vendor', 'id_cetak', 'index_cetak', 'vendor', 'index', 'data', 'data_selesai', 'data_batal', 'rekam_jejak_selesai', 'rekam_jejak_batal', 'posisi', 'status', 'status_revisi', 'rekam_jejak', 'sppb_bayar', 'sppn_terima', 'rekening', 'b', 'bagian_id', 'posisi_batal', 'asal', 'gl'));
    }

    public function updateStatus(Request $request)
    {
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'spp_status_bayar' => 'required|integer',
        ]);

        try {
            foreach ($validatedData['ids'] as $id) {
                $spp = Spp::find($id);
                if ($spp) {
                    if ($spp->sppb_id) {
                        $spp->spp_status_bayar = $validatedData['spp_status_bayar'];
                    }

                    if ($spp->sppn_id) {
                        $spp->spp_status_terima = $validatedData['spp_status_bayar'];
                    }
                    if ($spp->sppb_id && $spp->sppn_id) {
                        $spp->spp_status_bayar = $validatedData['spp_status_bayar'];
                        $spp->spp_status_terima = $validatedData['spp_status_bayar'];
                    }
                    $spp->save();
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat mengubah status.'], 500);
        }
    }
}
