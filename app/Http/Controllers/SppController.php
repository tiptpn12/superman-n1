<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Sppb;
use App\Sppn;
use App\Spp;
use App\IsiSppb;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\SppStatus;
use App\DokumenPendukungSppb;
use App\DokumenPendukungSppn;
use App\ProfitCenter;
use App\Rekening;
use App\CostCenter;
use App\DokumenTambahan;
use App\SumberDana;
use App\CashFlow;
use App\Bagian;
use App\MasterKaryawan;
use App\Sppb_bukti_kas;
use App\Sppn_bukti_kas;
use App\SppProses;
use App\RekamJejak;
use App\SppbBayar;
use App\SppnTerima;
use App\Vendor;
use App\NamaKaryawanModel;
use Carbon\Carbon;
use File;
use PDF;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\FakturPajak;
use Illuminate\Routing\Redirector;
use App\GL;
use App\Customer;
use Illuminate\Support\Facades\DB;

class SppController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            $this->company = session()->get('company');

            //dd($this->user);
            //return $next($request);
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
        $nama_file_bukti_kas = $current . '-' . $file_bukti_kas->getClientOriginalName();
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

        if ($spp->spp_kabag !== null) {

            if ($request->upload_file == 'file_baru') {
                File::delete(public_path('dokumen/' . $request->file_lama));
                $request->validate([
                    'spp_kabag' => 'mimes:pdf,jpg,png,jpeg|max:55000',
                ]);
                $sppkabag = $request->file('spp_kabag');
                $sppkabagname = str_replace("'", '', $sppkabag->getClientOriginalName());
                $sppkabags = $current . '-' . $sppkabagname;
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

            $sppkabagname = str_replace("'", '', $sppkabag->getClientOriginalName());
            $sppkabags = $current . '-' . $sppkabagname;
            $sppkabag->move('dokumen/', $sppkabags);

            $spp = Spp::find($id);
            $spp->spp_kabag = $sppkabags;
            $spp->save();
        }

        // dd($sppkabagname);

        return redirect('spp/send/' . $id);
    }

    public function selesai($id)
    {
        $level = Session::get('level');

        $spp = Spp::find($id);
        $spp->spp_status_ob = 3;
        $spp->save();

        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $id;
        $rekam_jejak->master_user_id = $level;
        $rekam_jejak->master_user_id_asal = Session::get('id');
        $rekam_jejak->rekam_jejak_status = 4;
        $rekam_jejak->save();

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

    public function kirim($id)
    {
        // dd('haha');
        $level = Session::get('level');
        $status = [0, 1, 3, 5, 7, 9, 11];
        if ($level == 2) {
            $status_revisi = DB::table('rekam_jejak')->where('spp_id', '=', $id)->where('rekam_jejak_status', '=', 0)->where('rekam_jejak_revisi', '!=', null)->where('rekam_jejak_revisi', '!=', '')->where('master_user_id', '!=', '2')->select('master_user_id', 'rekam_jejak_waktu')->latest('rekam_jejak_waktu')->first();

            if ($status_revisi != null) {
                // dd($status_revisi);
                $status_revisi = $status_revisi->master_user_id;
                $spp = Spp::find($id);
                $spp->spp_status_proses = $status[$status_revisi - 1];
                $spp->spp_status_ob = 1;
                $spp->save();
                $tujuan = $status_revisi;
            } else {
                $spp = Spp::find($id);
                $spp->spp_status_proses++;
                $spp->spp_status_ob = 1;
                $spp->save();
                $tujuan = 3;
            }
        } else {
            $spp = Spp::find($id);
            $spp->spp_status_proses++;
            $spp->spp_status_ob = 1;
            $spp->save();
        }

        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $id;
        $rekam_jejak->master_user_id = $level;
        $rekam_jejak->master_user_id_asal = Session::get('id');
        $rekam_jejak->rekam_jejak_status = 1;
        $rekam_jejak->save();

        switch ($level) {
            case 1:
                $spp = Spp::find($id);
                $spp->spp_status_posisi = 1;
                $spp->save();
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_penerima = 1;
                $spp_proses->save();
                $tujuan = 2;
                break;
            case 2:
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_pajak = 1;
                $spp_proses->save();

                break;
            case 3:
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_sap_miro = 1;
                $spp_proses->save();
                $tujuan = 4;
                break;
            case 4:
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_verifikasi = 1;
                $spp_proses->save();
                $tujuan = 5;
                break;
            case 5:
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_kas_dan_bank = 1;
                $spp_proses->save();
                $tujuan = 6;
                break;
            case 6:
                $spp_proses = SppProses::where('spp_id', '=', $id)->first();
                $spp_proses->spp_proses_petugas_pembayaran = 1;
                $spp_proses->save();
                $tujuan = 7;
                break;
        }
        $rekam_jejak = new RekamJejak;
        $rekam_jejak->spp_id = $id;
        $rekam_jejak->master_user_id = $level;
        $rekam_jejak->master_user_id_asal = Session::get('id');
        $rekam_jejak->rekam_jejak_status = 1;
        $rekam_jejak->master_user_id_tujuan = $tujuan;
        $rekam_jejak->save();
        return redirect('spp');
        // dd($rekam_jejak);
    }

    public function accept($id)
    {
        $level = Session::get('level');
        $status_revisi = DB::table('rekam_jejak')->where('spp_id', '=', $id)->where('rekam_jejak_status', '=', 0)->where('rekam_jejak_revisi', '!=', null)->where('rekam_jejak_revisi', '!=', '')->where('master_user_id', '=', $level)->select('master_user_id', 'rekam_jejak_waktu')->latest('rekam_jejak_waktu')->first();
        $status_revisi1 = DB::table('rekam_jejak')->where('spp_id', '=', $id)->where('rekam_jejak_status', '=', 0)->where('rekam_jejak_revisi', '!=', null)->where('rekam_jejak_revisi', '!=', '')->select('master_user_id', 'rekam_jejak_waktu', 'rekam_jejak_revisi')->latest('rekam_jejak_waktu')->first();


        //dd($level,$status_revisi,$status_revisi1);
        $spp = Spp::find($id);
        if ($spp->spp_status_ob == 2) {
            $spp->spp_status_proses++;
            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $id;
            $rekam_jejak->master_user_id = $status_revisi1->master_user_id;
            $rekam_jejak->master_user_id_tujuan = $level;
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->rekam_jejak_status = 6;
            $rekam_jejak->rekam_jejak_revisi = $status_revisi1->rekam_jejak_revisi;
            $rekam_jejak->save();
        } else {
            $spp->spp_status_proses++;
            if ($status_revisi) {
                $spp->spp_status_posisi = $level;
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $level - 1;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->master_user_id_tujuan = $level;
                $rekam_jejak->rekam_jejak_status = 6;
                $rekam_jejak->save();
            } else {
                $spp->spp_status_posisi++;
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $level - 1;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->master_user_id_tujuan = $level;
                $rekam_jejak->rekam_jejak_status = 6;
                $rekam_jejak->save();
            }
        }
        $spp->save();
        return redirect('spp');
    }

    public function revisi(Request $request, $id)
    {
        $level = Session::get('level');
        $status_revisi1 = DB::table('rekam_jejak')->where('spp_id', '=', $id)->select('master_user_id', 'rekam_jejak_waktu', 'rekam_jejak_revisi')->latest('rekam_jejak_waktu')->first();
        if ($level != 2) {
            $rekam_jejak = new RekamJejak;
            $rekam_jejak->spp_id = $id;
            $rekam_jejak->master_user_id = $level;
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->rekam_jejak_status = 0;
            $rekam_jejak->rekam_jejak_revisi = $request->revisi;
            $rekam_jejak->save();

            $spp = Spp::find($id);
            $spp->spp_status_proses = 1;
            $spp->spp_status_ob = 2;
            $spp->save();
        } else {
            if ($status_revisi1->master_user_id == 1) {

                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $level;
                $rekam_jejak->master_user_id_tujuan = 1;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->rekam_jejak_revisi = $request->revisi;
                $rekam_jejak->save();

                $spp = Spp::find($id);
                $spp->spp_status_proses = 0;
                $spp->spp_status_ob = 2;
                $spp->save();
            } else {
                $rekam_jejak = new RekamJejak;
                $rekam_jejak->spp_id = $id;
                $rekam_jejak->master_user_id = $level;
                $rekam_jejak->master_user_id_asal = Session::get('id');
                $rekam_jejak->rekam_jejak_status = 0;
                $rekam_jejak->rekam_jejak_revisi = $request->revisi;
                $rekam_jejak->save();

                $spp = Spp::find($id);
                $spp->spp_status_proses = 0;
                $spp->spp_status_ob = 2;
                $spp->save();
            }
        }


        return redirect('spp');
    }
    public function bukti_kas(Request $request, $id)
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
    $idsppb = DB::table('spp')->where('spp.sppb_id', '=', $id)->select('spp_id')->first();
    $idsppn = DB::table('spp')->where('spp.sppn_id', '=', $id)->select('spp_id')->first();
        if ($form == 0) {
            $bayar = new Sppb_bukti_kas;
        $bayar->sppb_id = $id;
            $bayar->cek_giro = $request->nomor_bukti_kas_sppb;
            $bayar->master_rekening_id = $request->rekening_bank_sppb;
            $bayar->master_vendor_id = $request->penerima;
            $bayar->alamat_sppb = $request->alamat_sppb;
            $bayar->save();
        } else if ($form == 1) {
            $terima = new Sppn_bukti_kas;
        $terima->sppn_id = $id;
            $terima->cek_giro = $request->nomor_bukti_kas_sppn;
            $terima->master_rekening_id = $request->rekening_bank_sppn;
            $terima->master_vendor_id = $request->diterima_dari;
            $terima->alamat_sppn = $request->alamat_sppn;
            $terima->save();
        }
        return redirect('spp');
    }
    public function index()
    {
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $rekening = Rekening::All()->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0);
        $vendor = Vendor::All();
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
        $b = Bagian::where([['master_bagian_id', '!=', 10], ['master_bagian_id', '!=', 2]])->get();
        $sppb_bayar = [];
        $sppn_terima = [];
        $sppb_cetak_bukti_kas = [];
        $sppn_cetak_bukti_kas = [];
        $datapenerima = [];
        $data_diterima_dari = [];
        if ($level == 1) {
            $data = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_kabag', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'tanggal')
                ->orderBy('spp_tanggal', 'desc')
                ->get();

            foreach ($data as $v) {
                $sppb_bayar[] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->select('sppb_bayar.*')->first();
                $sppn_terima[] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->select('sppn_terima.*')->first();
            }
        } else if ($level == 99) {
            $data = DB::table('spp')->where('spp.master_bagian_id', '!=', '2')->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc')
                ->get();
        } else if ($level == 2) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_penerima', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $data = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'master_bagian_nama', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }
        } else if ($level == 3) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_pajak', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();


            $data = [];
            foreach ($spp as $s => $v) {
                if ($v->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $v->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }
        } else if ($level == 4) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_sap_miro', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $data = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }
        } else if ($level == 5) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_verifikasi', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();
            // var_dump($spp[1]->spp_id);


            $data = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }
        } else if ($level == 6) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $data = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                        ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                        ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                        ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_status_terima', 'spp_status_bayar', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }
            foreach ($data as $v) {
                $sppb_cetak_bukti_kas[] = DB::table('sppb_bukti_kas')->where('sppb_bukti_kas.sppb_id', '=', $v->sppb_id)
                    ->leftJoin('master_rekening', 'sppb_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bukti_kas.*')->first();
                $sppn_cetak_bukti_kas[] = DB::table('sppn_bukti_kas')->where('sppn_bukti_kas.sppn_id', '=', $v->sppn_id)
                    ->leftJoin('master_rekening', 'sppn_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_bukti_kas.*')->first();
            }
            foreach ($data as $v) {
                $datapenerima[] = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)
                    ->select('nama_karyawan.*')->first();
                $data_diterima_dari[] = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)
                    ->select('nama_karyawan.*')->first();
            }
        } else if ($level == 7) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_pembayaran', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $data = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $data[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                        ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                        ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                        ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                        ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_bukti_kas_bank', 'master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                        ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                        ->orderBy('spp_tanggal', 'desc')
                        ->first();
                }
            }


            foreach ($data as $v) {
                $sppb_bayar[] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->select('sppb_bayar.*')->first();
                $sppn_terima[] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->select('sppn_terima.*')->first();
            }
            foreach ($data as $v) {
                $sppb_cetak_bukti_kas[] = DB::table('sppb_bukti_kas')->where('sppb_bukti_kas.sppb_id', '=', $v->sppb_id)
                    ->leftJoin('master_rekening', 'sppb_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bukti_kas.*')->first();
                $sppn_cetak_bukti_kas[] = DB::table('sppn_bukti_kas')->where('sppn_bukti_kas.sppn_id', '=', $v->sppn_id)
                    ->leftJoin('master_rekening', 'sppn_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_bukti_kas.*')->first();
            }
            foreach ($data as $v) {
                $datapenerima[] = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)
                    ->select('nama_karyawan.*')->first();
                $data_diterima_dari[] = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)
                    ->select('nama_karyawan.*')->first();
            }
        } else if ($level == 88 && $bagian !== 2) {
            $data = [];
            $data = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_status_lunas', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc')
                ->get();
        } else if ($level == 88 && $bagian == 2) {
            $data = [];
            $data = DB::table('spp')->where('spp.master_bagian_id', '!=', 2)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_status_lunas', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc')
                ->get();
        }

        foreach ($data as $d => $v) {
            $status[] = DB::table('rekam_jejak')->where('rekam_jejak.spp_id', '=', $v->spp_id)->where('rekam_jejak.master_user_id', '!=', 2)->orWhere('rekam_jejak.master_user_id_tujuan', '=', 1)->select('spp_id', 'master_user_id', 'rekam_jejak_revisi', 'created_at')->latest('created_at')->first();
            $rekam_jejak[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_level')
                ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_level')
                ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
            $posisi[] = DB::table('master_hak_akses')->where('master_hak_akses_level', '=', $v->spp_status_posisi)->select('master_hak_akses_keterangan')->first();
            // $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id','=',$v->spp_id)->where('rekam_jejak_status','=',5)
            //                     ->join('master_hak_akses','master_user_id','=','master_hak_akses.master_hak_akses_level')
            //                     ->select('rekam_jejak.*','master_hak_akses.*')->first();
            $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_level')
                ->select('rekam_jejak.*', 'master_hak_akses.*')->first();
            if ($v->spp_status_ob == 3) {
                $data_selesai[] = $v;
            }
            if ($v->spp_status_ob == 4) {
                $data_batal[] = $v;
            }
            // dd($posisi_batal);

        }
        //dd($status,$data);
        // dd($posisi_batal);

        if (isset($data_batal)) {

            foreach ($data_batal as $d => $v) {
                $rekam_jejak_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_level')
                    ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_level')
                    ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                    ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
                // $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id','=',$v->spp_id)->where('rekam_jejak_status','=',5)
                //                     ->join('master_hak_akses','master_user_id','=','master_hak_akses.master_hak_akses_level')
                //                     ->select('rekam_jejak.*','master_hak_akses.*')->first();
                $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_level')
                    ->select('rekam_jejak.*', 'master_hak_akses.*')->first();
            }
            // dd($posisi_batal);
        }
        if (empty($rekam_jejak_batal)) {
            $data_batal = [];
            $rekam_jejak_batal = [];
            $posisi_batal = '';
        }
        // dd($data_selesai);
        if (isset($data_selesai)) {
            foreach ($data_selesai as $d => $v) {
                $sppb_bayar_selesai[] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                $sppn_terima_selesai[] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
                $rekam_jejak_selesai[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_level')
                    ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_level')
                    ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                    ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
            }
        }
        if (empty($rekam_jejak_selesai)) {
            $data_selesai = [];
            $sppb_bayar_selesai = [];
            $rekam_jejak_selesai = [];
            $sppn_terima_selesai = [];
        }

        if (isset($status)) {
            foreach ($status as $s) {
                // $posisi[] = DB::table('master_hak_akses')->where('master_hak_akses_level','=',$s->master_user_id)->select('master_hak_akses_keterangan')->first();

                if ($s !== null) {
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
            $sppb_cetak_bukti_kas = [];
            $sppn_cetak_bukti_kas = [];
            $data_diterima_dari = [];
            $datapenerima = [];
        }
        // $data = collect($data)->sortByDesc('tanggal')->reverse()->toArray();

        $index = 0;
        $index_cetak = Session::get('index_cetak');
        $id_cetak = Session::get('id_cetak');
        if (empty($index_cetak)) {
            $index_cetak = 0;
            $id_cetak = 0;
        }

        if ($level == 1) {
            foreach ($rekam_jejak as $k => $r) {
                foreach ($r as $l => $s) {
                    if ($s->master_user_id == 1 || $s->master_user_id == 2 && $s->master_user_id_tujuan <= 2 || $s->rekam_jejak_revisi !== null && $s->master_user_id !== 2) {

                        $rekam_jejak_ob[$k][] = $s;
                    }
                }
            }
            $a = '';
            if (isset($rekam_jejak_ob)) {
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
        } else {
            $asal = [];
        }
        //dd($rekam_jejak_ob,$rekam_jejak,$asal);
        // dd($asal);

        // dd($data_diterima_dari);
        // dd($sppb_cetak_bukti_kas);
        return view('page.spp.spp', compact('id_cetak', 'index_cetak', 'vendor', 'index', 'data_selesai', 'data_batal', 'rekam_jejak_batal', 'data_selesai', 'rekam_jejak_selesai', 'datapenerima', 'data_diterima_dari', 'sppb_bayar_selesai', 'sppb_cetak_bukti_kas', 'sppn_cetak_bukti_kas', 'sppn_terima_selesai', 'data', 'posisi', 'status', 'status_revisi', 'rekam_jejak', 'sppb_bayar', 'sppn_terima', 'rekening', 'posisi_batal', 'b', 'bagian_id', 'asal'));
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
        $faktur_pajak_spp = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $idsppn)->where('faktur_pajak.sppb_id', '=', $idsppb)->select('faktur_pajak_id')->get();

        foreach ($idisisppb as $a => $value2) {
            $iduraiansppb[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.sppb_uraian_id')->get();
        }

        foreach ($idisisppn as $b => $value) {
            $iduraiansppn[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value->sppn_isi_id)->select('sppn_uraian.sppn_uraian_id')->get();
        }

        //FORM SPPB
        if (isset($idsppb) && empty($idsppn)) {
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
                $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian->getClientOriginalName();
                $doks->sppb_kontrak_perjanjian = $kontrak_perjanjians;
                $doks->save();
                $kontrak_perjanjian->move('dokumen/kontrakperjanjian/', $kontrak_perjanjians);
            }


            $invoice = $request->file('invoice_sppb');
            if ($invoice != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/invoice/' . $dok->sppb_invoice));
                $invoices = $current . '-' . $invoice->getClientOriginalName();
                $dok->sppb_invoice = $invoices;
                $dok->save();
                $invoice->move('dokumen/invoice/', $invoices);
            }


            $efaktur = $request->file('efaktur_sppb');
            if ($efaktur != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/efaktur/' . $dok->sppb_efaktur));
                $efakturs = $current . '-' . $efaktur->getClientOriginalName();
                $dok->sppb_efaktur = $efakturs;
                $dok->save();
                $efaktur->move('dokumen/efaktur/', $efakturs);
            }


            $berita_acara_file = $request->file('berita_acara_file_sppb');
            if ($berita_acara_file != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/beritaacara/' . $dok->sppb_berita_acara_file));

                $berita_acara_files = $current . '-' . $berita_acara_file->getClientOriginalName();
                $dok->sppb_berita_acara_file = $berita_acara_files;
                $dok->save();
                $berita_acara_file->move('dokumen/beritaacara/', $berita_acara_files);
            }

            $kodebagiansppb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
            $kodebagian = $kodebagiansppb->master_bagian_kode;

            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = $request->tanggal_sppb;
            $tanggals = date('Y-m-d', strtotime($tanggal));

            $tahun = Carbon::createFromFormat('Y-m-d', $tanggals)->year;
            $month = Carbon::createFromFormat('Y-m-d', $tanggals)->month;
            $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;


            $bulan = $bulanromawi[$month];
            $urutansppb = $request->urutan_sppb;
            $nomor = $kodebagian . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;
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
            $sppb->sppb_tahun = $tahun;
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
                            $krywn->karyawan_nama = $value;
                            $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                            $krywn->save();
                        }
                    } else if ($request->pilih_data_sppb == 'master_data') {
                        $karyawan = $request->atas_nama_bank_sppb_kas;

                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $value;
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

            foreach ($idisisppb as $i => $v) {
                $isi = IsiSppb::find($v->sppb_isi_id);

                $isi->delete();
                foreach ($iduraiansppb[$i] as $u => $va) {
                    $uraian = IsiUraianSppb::find($va->sppb_uraian_id);
                    $uraian->delete();
                }
            }
            $isisppb = $request->isi_sppb;
            dd($isisppb);
            $sum1 = 0;
            $sum2 = 0;
            foreach ($isisppb as $isi => $value) {
                if ($value['jenis_center'] == 'cost_center') {
                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                        $isisppb->master_gl_id = null;
                        $isisppb->master_customer_id = null;
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                        $isisppb->master_kode_vendor_id = null;
                        $isisppb->master_customer_id = null;
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                        $isisppb->master_gl_id = null;
                        $isisppb->master_kode_vendor_id = null;
                    }
                    $isisppb->master_cost_center_id = $value['cost_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_nominal = $b;
                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                } else {
                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                        $isisppb->master_gl_id = null;
                        $isisppb->master_customer_id = null;
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                        $isisppb->master_kode_vendor_id = null;
                        $isisppb->master_customer_id = null;
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                        $isisppb->master_gl_id = null;
                        $isisppb->master_kode_vendor_id = null;
                    }
                    $isisppb->master_profit_center_id = $value['profit_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_nominal = $b;
                        $isiuraiansppb->save();
                        $sum2 += $b;
                    }
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
                    $dokumenpendukungs = $current . '-' . $file->getClientOriginalName();
                    $file->move('dokumen', $dokumenpendukungs);
                    $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                    $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                    $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                    $dokumenpendukungsppb->save();
                }
            }
            $action = $request->status_btn;
            if ($action == "0") {
                $index_cetak = 0;
                return redirect('spp')->with('index_cetak', $index_cetak);
            } else {
                $index_cetak = 1;
                $id_cetak = $id;
                return redirect('spp')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
            }
        }
        // FORM SPPN
        else if (isset($idsppn) && empty($idsppb)) {
            $request->validate([
                'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
            ]);

            $kodebagiansppn = Bagian::where('master_bagian_id', $request->bagian_sppn)->select('master_bagian_kode')->first();
            $kodebagian = $kodebagiansppn->master_bagian_kode;

            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = $request->tanggal_sppn;
            $tanggals = date('Y-m-d', strtotime($tanggal));
            $tahun = Carbon::createFromFormat('Y-m-d', $tanggals)->year;
            $month = Carbon::createFromFormat('Y-m-d', $tanggals)->month;
            $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;

            $bulan = $bulanromawi[$month];

            $urutansppn = $request->urutan_sppn;
            $nomor = $kodebagian . "/PPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;

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
                $fp = new FakturPajak;
                $fp->sppb_id = null;
                $fp->sppn_id = $request->sppn_id;
                $fp->faktur_pajak_nomor = $value['fp'];
                $fp->save();
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
            //         }
            //         else{
            //             $krywn = new NamaKaryawanModel;
            //                 $krywn -> sppb_id = null;
            //                 $krywn -> sppn_id = $request->sppn_id;
            //                 $krywn -> karyawan_nama = "TERLAMPIR";
            //                 $krywn -> save();
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
            //         }
            //         else{
            //             $krywn = new NamaKaryawanModel;
            //             $krywn -> sppb_id = null;
            //             $krywn -> sppn_id = $request->sppn_id;
            //             $krywn -> karyawan_nama = "TERLAMPIR";
            //             $krywn -> karyawan_nama_bank = "TERLAMPIR";
            //             $krywn -> karyawan_no_rek = "TERLAMPIR";
            //             $krywn -> save();
            //         }
            //     }
            // }
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
                    $dokpensppns = $current . '-' . $file->getClientOriginalName();
                    $file->move('dokumen', $dokpensppns);
                    $dokumenpendukungsppn = new \App\DokumenPendukungSppn;
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
                if ($value['jenis_center'] == 'cost_center') {
                    $isisppn = new IsiSppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }
                    $isisppn->master_cost_center_id = $value['cost_center'];
                    $isisppn->master_cash_flow_id = $value['cash_flow'];
                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppn = new IsiUraianSppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_nominal = $b;
                        $isiuraiansppn->save();
                        $sum1 += $b;
                    }
                } else {
                    $isisppn = new IsiSppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }
                    $isisppn->master_profit_center_id = $value['profit_center'];
                    $isisppn->master_cash_flow_id = $value['cash_flow'];
                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppn = new IsiUraianSppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_nominal = $b;
                        $isiuraiansppn->save();
                        $sum2 += $b;
                    }
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

            $action = $request->status_btn;
            if ($action == "0") {
                $index_cetak = 0;
                return redirect('spp')->with('index_cetak', $index_cetak);
            } else {
                $index_cetak = 1;
                $id_cetak = $id;
                return redirect('spp')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
            }
        }
        //FORM SPP CAMPURAN
        else {
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
                $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian->getClientOriginalName();
                $dok->sppb_kontrak_perjanjian = $kontrak_perjanjians;
                $dok->save();
                $kontrak_perjanjian->move('dokumen/kontrakperjanjian/', $kontrak_perjanjians);
            }


            $invoice = $request->file('invoice_sppb');
            if ($invoice != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/invoice/' . $dok->sppb_invoice));
                $invoices = $current . '-' . $invoice->getClientOriginalName();
                $dok->sppb_invoice = $invoices;
                $dok->save();
                $invoice->move('dokumen/invoice/', $invoices);
            }

            $efaktur = $request->file('efaktur_sppb');
            if ($efaktur != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/efaktur/' . $dok->sppb_efaktur));
                $efakturs = $current . '-' . $efaktur->getClientOriginalName();
                $dok->sppb_efaktur = $efakturs;
                $dok->save();
                $efaktur->move('dokumen/efaktur/', $efakturs);
            }

            $berita_acara_file = $request->file('berita_acara_file_sppb');
            if ($berita_acara_file != null) {
                $dok = SPPb::find($idsppb);
                File::delete(public_path('dokumen/beritaacara/' . $dok->sppb_berita_acara_file));
                $berita_acara_files = $current . '-' . $berita_acara_file->getClientOriginalName();
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
            $tahun = Carbon::createFromFormat('Y-m-d', $tanggals)->year;
            $month = Carbon::createFromFormat('Y-m-d', $tanggals)->month;
            $day = Carbon::createFromFormat('Y-m-d', $tanggals)->day;

            $bulan = $bulanromawi[$month];
            $urutansppb = $request->urutan_sppb;
            $urutansppn = $request->urutan_sppn;

            $nomorsppb = $kodebagiansppb . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;
            $nomorsppn = $kodebagiansppn . "/SPPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;
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
            $sppb->sppb_bulan = $bulan;
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

            foreach ($idisisppb as $i => $v) {
                $isi = IsiSppb::find($v->sppb_isi_id);
                $isi->delete();
                foreach ($iduraiansppb[$i] as $u => $va) {
                    $uraian = IsiUraianSppb::find($va->sppb_uraian_id);
                    $uraian->delete();
                }
            }

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
                    $dokumenpendukungs = $current . '-' . $file->getClientOriginalName();
                    $file->move('dokumen', $dokumenpendukungs);
                    // DokumenPendukungSppb::create([
                    //     'sppb_id' => $request->sppb_id,
                    //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                    // ]);
                    $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                    $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                    $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                    $dokumenpendukungsppb->save();
                }
            }


            $isisppb = $request->isi_sppb;
            $sum1 = 0;
            $sum2 = 0;
            foreach ($isisppb as $isi => $value) {
                if ($value['jenis_center'] == 'cost_center') {
                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                    }
                    $isisppb->master_cost_center_id = $value['cost_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_nominal = $b;
                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                } else {
                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppb->master_gl_id = $value['gl'];
                    } else {
                        $isisppb->master_customer_id = $value['customer'];
                    }
                    $isisppb->master_profit_center_id = $value['profit_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                    foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                        $isiuraiansppb->sppb_uraian_nominal = $b;
                        $isiuraiansppb->save();
                        $sum2 += $b;
                    }
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
            $sppn->sppn_bulan = $bulan;
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
            $dokumenpendukungsppn = $request->file('dokumen_pendukung_sppn');
            if ($dokumenpendukungsppn != null) {
                foreach ($dokumenpendukungsppn as $file) {
                    $dokumenpendukungsppns = $current . '-' . $file->getClientOriginalName();
                    $file->move('dokumen', $dokumenpendukungsppns);
                    // DokumenPendukungSppb::create([
                    //     'sppb_id' => $request->sppb_id,
                    //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                    // ]);
                    $dokumenpendukungsppnsppn = new \App\DokumenPendukungSppn;
                    $dokumenpendukungsppnsppn->sppn_id = $request->sppn_id;
                    $dokumenpendukungsppnsppn->dokumen_pendukung_sppn_nama = $dokumenpendukungsppns;
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
                if ($value['jenis_center'] == 'cost_center') {
                    $isisppn = new IsiSppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }
                    $isisppn->master_cost_center_id = $value['cost_center'];
                    $isisppn->master_cash_flow_id = $value['cash_flow'];
                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppn = new IsiUraianSppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_nominal = $b;
                        $isiuraiansppn->save();
                        $total1 += $b;
                    }
                } else {
                    $isisppn = new IsiSppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    $isisppn->master_kode_kbb = $value['kode_kbb'];

                    if ($value['jenis_sap'] == 'vendor') {
                        $isisppn->master_kode_vendor_id = $value['vendor'];
                    } else if ($value['jenis_sap'] == 'gl') {
                        $isisppn->master_gl_id = $value['gl'];
                    } else {
                        $isisppn->master_customer_id = $value['customer'];
                    }
                    $isisppn->master_profit_center_id = $value['profit_center'];
                    $isisppn->master_cash_flow_id = $value['cash_flow'];
                    $isisppn->save();
                    $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                    foreach ($request->uraian_sppn[$isi] as $urai => $value2) {
                        $a = $value2['jumlah'];
                        $b = str_replace(".", "", $a);
                        $isiuraiansppn = new IsiUraianSppn;
                        $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                        $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                        $isiuraiansppn->sppn_uraian_nominal = $b;
                        $isiuraiansppn->save();
                        $total2 += $b;
                    }
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

            $action = $request->status_btn;
            if ($action == "0") {
                $index_cetak = 0;
                return redirect('spp')->with('index_cetak', $index_cetak);
            } else {
                $index_cetak = 1;
                $id_cetak = $id;
                return redirect('spp')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
            }
        }
    }

    public function viewupdate($id)
    {
        $company = Session::get('company');
        $profitcenter = ProfitCenter::where('company_id', $company)->get();
        $costcenter = CostCenter::all();
        $sumberDana = SumberDana::All();
        $cashflow = CashFlow::all();
        $bagian = Session::get('bagian');
        $level = Session::get('level');
        $client = new Client();
        $company = Session::get('company');
        $error_code = Session::get('error_code');
        // dd($error_code);
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            return redirect('user/logout');
        }
        $gl = DB::table('master_budget')->where('master_budget.bagian_id', '=', $bagian)
            ->leftJoin('master_bagian', 'master_budget.bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
            ->get();

        $rekening = DB::table('master_rekening')->where('company_id', $company)
            ->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0)
            ->get();

        //dd($rekening);

        // if (in_array($bagian, [124, 126, 127])) {
        //     $customer = DB::table('master_customer')
        //         ->where('master_customer.company_id', '=', $company)
        //         ->get();
        // } else {
        //     $customer = collect();
        // }
        // $customer = DB::table('master_customer')
        //         ->where('master_customer.company_id', '=', $company)
        //         ->get();

        // dd($customer->first());

        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());
        // $bagian_karyawan = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', $bagian)
        //     ->select('master_bagian.*')->first();

        // $ino_bagian_id = $bagian_karyawan;

        // $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use ($ino_bagian_id) {
        //     return $value->bagian_id == $ino_bagian_id;
        // });
        // if ($level == 99) {
        //     $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) {
        //         return $value->bagian_id != 7;
        //     });
        // }
        $bagianall = Bagian::where('master_bagian_id', '!=', 10)->get();
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
            ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
            ->select('spp_id', 'sppb_id', 'sppn_id', 'master_sumber_dana.*')->first();
        // dd($idspp);
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
            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
            }
            // dd($idsppb);


            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
            // dd($data_sppb);
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
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        $vendor = Vendor::where('company_id', $this->company)->get();

        if ($bagian == 61) {
            $master_karyawan = DB::table('master_karyawan')
                ->where('company_id', '=', $company)->get();
        } else {
            $master_karyawan = DB::table('master_karyawan')
                ->where('master_bagian_id', '=', $bagian)
                ->where('company_id', '=', $company)->get();
        }

        $karyawan_bagian = [];

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
            'master_karyawan' => $master_karyawan,
            'karyawan_sppb' => $karyawan_sppb,
            'karyawan_sppn' => $karyawan_sppn,
            'gl' => $gl,
            // 'customer' => $customer
        );
        // dd($data);
        $bagianspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.master_bagian_id')->first();
        // dd($bagianspp);

        //dd($karyawan_sppb);
        return view('page.spp.spp_edit', $data)
            ->with('error_code', $error_code);
    }

    public function viewupdate2($id)
    {
        $company = Session::get('company');
        $profitcenter = ProfitCenter::where('company_id', $company)->get();
        $costcenter = CostCenter::all();
        $sumberDana = SumberDana::All();
        $cashflow = CashFlow::all();
        $bagian = Session::get('bagian');
        $level = Session::get('level');
        $client = new Client();
        $company = Session::get('company');
        $error_code = Session::get('error_code');
        // dd($error_code);
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            return redirect('user/logout');
        }
        $gl = DB::table('master_budget')->where('master_budget.bagian_id', '=', $bagian)
            ->leftJoin('master_bagian', 'master_budget.bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
            ->get();

        $rekening = DB::table('master_rekening')->where('company_id', $company)
            ->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0)
            ->get();

        //dd($rekening);

        // if (in_array($bagian, [124, 126, 127])) {
        //     $customer = DB::table('master_customer')
        //         ->where('master_customer.company_id', '=', $company)
        //         ->get();
        // } else {
        //     $customer = collect();
        // }
        // $customer = DB::table('master_customer')
        //         ->where('master_customer.company_id', '=', $company)
        //         ->get();

        // dd($customer->first());

        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());
        // $bagian_karyawan = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', $bagian)
        //     ->select('master_bagian.*')->first();

        // $ino_bagian_id = $bagian_karyawan;

        // $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use ($ino_bagian_id) {
        //     return $value->bagian_id == $ino_bagian_id;
        // });
        // if ($level == 99) {
        //     $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) {
        //         return $value->bagian_id != 7;
        //     });
        // }
        $bagianall = Bagian::where('master_bagian_id', '!=', 10)->get();
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
            ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
            ->select('spp_id', 'sppb_id', 'sppn_id', 'master_sumber_dana.*')->first();
        // dd($idspp);
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
            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
            }
            // dd($idsppb);


            $data_sppb = [];
            $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
            // dd($data_sppb);
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
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        $vendor = Vendor::where('company_id', $this->company)->get();

        if ($bagian == 61) {
            $master_karyawan = DB::table('master_karyawan')
                ->where('company_id', '=', $company)->get();
        } else {
            $master_karyawan = DB::table('master_karyawan')
                ->where('master_bagian_id', '=', $bagian)
                ->where('company_id', '=', $company)->get();
        }

        $karyawan_bagian = [];

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
            'master_karyawan' => $master_karyawan,
            'karyawan_sppb' => $karyawan_sppb,
            'karyawan_sppn' => $karyawan_sppn,
            'gl' => $gl,
            // 'customer' => $customer
        );
         dd($data);
        $bagianspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.master_bagian_id')->first();
        // dd($bagianspp);

        //dd($karyawan_sppb);
        return view('page.spp.spp_edit', $data)
            ->with('error_code', $error_code);
    }

    public function viewdetail($id)
    {
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
            ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
            ->select('spp.*', 'master_sumber_dana.*')->first();
        // $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
        //     ->Join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_level')
        //     ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();

        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
            ->leftJoin('master_user', 'dokumen_tambahan.master_user_id', '=', 'master_user.master_user_id')
            ->select('dokumen_tambahan.*', 'master_user_name')->get();

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
                ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
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
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());

        $form = 0;
        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                // foreach ($Krywn as $k => $val) {
                //     $nama = $val->karyawan_nama;
                //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                //         return $value->karyawan_nama == $nama;
                //     });
               // }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        $kasubdiv = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', 114)->first();
        $kadiv = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', 68)->first();
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.*')->first();
        $perusahaan = DB::table('master_company')->where('company_id', '=', $idspp->company_id)->select('master_company.*')->first();
        $kotak_cetak = DB::table('master_cetak_spp')->where('company_id', '=', $idspp->company_id)
            ->where('status', '!=', '0')->select('master_cetak_spp.*')->get();
        // dd($kotak_cetak);
        // dd($perusahaan->company_nama);
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
            ->join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_id')
            ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        //dd($idsppb);
        $idsppn = $idspp->sppn_id;
        //dd($idsppn);
        $flowid = $idspp->flow_id;
        // dd($flowid);
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
                ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
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
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());

        // SPPB SAJA
        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
        }
        //SPPN SAJA
        else if (isset($data_sppn) && empty($data_sppb)) {
            $form = 2;
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        }
        // SPP CAMPURAN SAJA
        else {
            $form = 3;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        // dd($kasubdiv);

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
            'kadiv' => $kadiv,
            'kasubdiv' => $kasubdiv,
            'company' => $perusahaan->company_nama,
            'company_jenis' => $perusahaan->company_jenis,
            'kotak_cetak' => $kotak_cetak,
            'flowid' => $flowid
        );
        // dd($data_sppb, $kotak_cetak);
        // dd($data);
        //dd($data_sppb,$data_sppn);
        //dd($karyawan_no_vendor_sppb);
        return view('page.spp.spp_cetak', $data);
    }

    public function cetak2($id)
    {
        $kasubdiv = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', 114)->first();
        $kadiv = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', 68)->first();
        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('spp.*')->first();
        $perusahaan = DB::table('master_company')->where('company_id', '=', $idspp->company_id)->select('master_company.*')->first();
        $kotak_cetak = DB::table('master_cetak_spp')->where('company_id', '=', $idspp->company_id)
            ->where('status', '!=', '0')->select('master_cetak_spp.*')->get();
        // dd($kotak_cetak);
        // dd($perusahaan->company_nama);
        $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
            ->join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_id')
            ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();
        $idsppb = $idspp->sppb_id;
        //dd($idsppb);
        $idsppn = $idspp->sppn_id;
        //dd($idsppn);
        $flowid = $idspp->flow_id;
        // dd($flowid);
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
                ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
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
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());

        // SPPB SAJA
        if (isset($data_sppb) && empty($data_sppn)) {
            $form = 1;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
        }
        //SPPN SAJA
        else if (isset($data_sppn) && empty($data_sppb)) {
            $form = 2;
            if ($data_sppn['sppn_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        }
        // SPP CAMPURAN SAJA
        else {
            $form = 3;
            if ($data_sppb['sppb_jenis'] == "karyawan") {
                $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                foreach ($Krywn as $k => $val) {
                    $nama = $val->karyawan_nama;
                    // $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                    foreach ($karyawan_sppb as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppb = null;
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
                    // $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //     return $value->karyawan_nama == $nama;
                    // });
                }
                if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                    foreach ($karyawan_sppn as $k => $v) {
                        foreach ($v as $k1 => $v2) {
                            //$karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            $karyawan_no_vendor_sppn = null;
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
        // dd($kasubdiv);

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
            'kadiv' => $kadiv,
            'kasubdiv' => $kasubdiv,
            'company' => $perusahaan->company_nama,
            'company_jenis' => $perusahaan->company_jenis,
            'kotak_cetak' => $kotak_cetak,
            'flowid' => $flowid
        );
        // dd($data_sppb, $kotak_cetak);
        // dd($data);
        //dd($data_sppb,$data_sppn);
        //dd($karyawan_no_vendor_sppb);
        return view('page.spp.cetak2', $data);
    }

    public function bayar(Request $request, $id)
    {

        $current = date('His-dmY');
        $level = Session::get('level');
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
            $bukti = $current . '-' . $file_bukti->getClientOriginalName();
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
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->spp_id = $idsppb->spp_id;
            $rekam_jejak->master_user_id = $level;
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

            $bukti = $current . '-' . $file_bukti->getClientOriginalName();
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
            $rekam_jejak->master_user_id_asal = Session::get('id');
            $rekam_jejak->master_user_id = $level;
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
        return redirect('spp');
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

            dd($file_bukti, $bayar);
            if ($file_bukti) {
                $bukti = $current . '-' . $file_bukti->getClientOriginalName();
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
                $bukti = $current . '-' . $file_bukti->getClientOriginalName();
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

    public function cetakbuktikas($id)
    {

        $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)->select('sppb_id', 'sppn_id')->first();
        $idsppb = $idspp->sppb_id;
        $idsppn = $idspp->sppn_id;
        $cekkode_sap = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $idsppb)->select('master_kode_vendor_id')->first();
        $cek_sap = $cekkode_sap;
        if ($idsppb != null) {
            $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb.*', 'master_bagian.*', 'master_vendor.*')->first();
            $databuktikassppb = DB::table('sppb_bukti_kas')->where('sppb_id', '=', $idsppb)
                ->leftJoin('master_rekening', 'sppb_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('sppb_bukti_kas.*', 'master_rekening.*')->first();
            $datafootersppb = DB::table('nama_karyawan')->where('sppb_id', '=', $idsppb)
                ->select('nama_karyawan.*')->first();
            $databayar = DB::table('sppb_bayar')->where('sppb_id', '=', $idsppb)->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('sppb_bayar.*', 'master_rekening.*')->first();
            $databayar_vendor = DB::table('sppb_bayar')->where('sppb_id', '=', $idsppb)->join('master_vendor', 'sppb_bayar.master_rekening_id', '=', 'master_vendor.master_vendor_id')
                ->select('sppb_bayar.*', 'master_vendor.*')->first();

            if ($cek_sap != NULL) {
                $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                    ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();

                // dd('a');
            } else {
                $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                    ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*', 'master_customer.*')->get();
                // dd('b');
            }

            // dd($sppbisi);
            foreach ($sppbisi as $a => $value2) {
                $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
            }

            foreach ($sppbisi as $s => $val) {
                $isisppb[] = collect($val)->push($sppburaian[$s]);
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
                ->leftJoin('master_rekening', 'sppn_bukti_kas.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('sppn_bukti_kas.*', 'master_rekening.*')->first();
            $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*')->get();

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
        $date = Carbon::today()->locale('id')->isoFormat('D MMM Y');
        $data = array(
            'sppb_tanggal_pembuatan' => $sppb_tanggal_pembuatan,
            'sppn_tanggal_pembuatan' => $sppn_tanggal_pembuatan,
            'sppb' => $data_sppb,
            'sppn' => $data_sppn,
            'bayar' => $databayar,
            'databayar_vendor' => $databayar_vendor,
            'terima' => $dataterima,
            'dataterima_vendor' => $dataterima_vendor,
            'databuktikassppb' => $databuktikassppb,
            'databuktikassppn' => $databuktikassppn,
            'datafootersppb' => $datafootersppb,
            'datafootersppn' => $datafootersppn,
            'formspp' => $form,
            'tanggalcetak' => $date
        );
        return view('page.cetak_bukti_kas', $data);
    }

    public function advanced_search(Request $request)
    {
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $rekening = Rekening::All()->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0);
        $vendor = Vendor::All();
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
        $b = Bagian::where('master_bagian_id', '!=', 10)->get();

        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ", $rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
        })->all();
        $sppb_bayar = [];
        $sppn_terima = [];

        if ($level == 1) {
            $vendor_filter = $request->vendor;
            if ($vendor_filter !== "semua") {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_kabag', 'master_bagian_nama', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                    ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->orderBy('tanggal', 'desc')
                    ->get();
            } else {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp.sppb_id', 'spp.sppn_id', 'spp_kabag', 'master_bagian_nama', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->orderBy('tanggal', 'desc')
                    ->get();
            }
            foreach ($datas as $v) {
                $sppb_bayar[] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                $sppn_terima[] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }
        } else if ($level == 99) {
            $vendor_filter = $request->vendor;
            if ($vendor_filter !== "semua") {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '!=', '2')->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'master_bagian_nama', 'spp.master_bagian_id', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->orderBy('tanggal', 'desc')
                    ->get();
            } else {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '!=', '2')->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'master_bagian_nama', 'spp.master_bagian_id', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->orderBy('tanggal', 'desc')
                    ->get();
            }
        } else if ($level == 2) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_penerima', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $datas = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'spp.master_bagian_id', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                            ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'spp.master_bagian_id', 'sppb.master_bank_id as bank_sppb_id', 'sppn.master_bank_id as bank_sppn_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp_kabag', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }
        } else if ($level == 3) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_pajak', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();


            $datas = [];
            foreach ($spp as $s => $v) {
                if ($v->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $v->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $v->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }
        } else if ($level == 4) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_sap_miro', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $datas = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }
        } else if ($level == 5) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_verifikasi', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $datas = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }
        } else if ($level == 6) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $datas = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp_status_proses', 'spp.master_bagian_id', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'master_bagian_nama', 'sppb.master_bank_id', 'spp_status_proses', 'spp.master_bagian_id', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }
        } else if ($level == 7) {
            $spp = [];
            $spp = DB::table('spp_proses')->where('spp_proses.spp_proses_petugas_pembayaran', '=', 1)->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                ->select('spp.*')->get();

            $datas = [];
            foreach ($spp as $s) {
                if ($s->master_bagian_id != 2) {
                    $vendor_filter = $request->vendor;
                    if ($vendor_filter !== "semua") {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'spp_bukti_kas_bank', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)

                            ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    } else {
                        $datas[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                            ->select('spp_id', 'spp_bukti_kas_bank', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                            ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                            ->first();
                    }
                }
            }


            foreach ($datas as $v) {
                $sppb_bayar[] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                $sppn_terima[] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }
        } else if ($level == 88 && $bagian !== 2) {
            $datas = [];
            $vendor_filter = $request->vendor;
            if ($vendor_filter !== "semua") {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp_status_lunas', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)

                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->get();
            } else {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp_status_lunas', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->get();
            }
        } else if ($level == 88 && $bagian == 2) {
            $datas = [];
            $vendor_filter = $request->vendor;
            if ($vendor_filter !== "semua") {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '!=', 2)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp_status_lunas', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->where('sppb.master_bank_id', '=', $vendor_filter)->orWhere('sppn.master_bank_id', '=', $vendor_filter)
                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->get();
            } else {
                $datas = DB::table('spp')->where('spp.master_bagian_id', '!=', 2)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('spp_id', 'spp_status_lunas', 'sppb.master_bank_id', 'master_bagian_nama', 'spp.master_bagian_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                    ->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp_status_bayar', 'tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                    ->get();
            }
        }

        $data_batal = [];
        $data_selesai = [];
        $data_cancel = [];
        $data_final = [];
        foreach ($datas as $d => $v) {
            if ($v->spp_status_ob == 3) {
                $data_final[] = $v;
            }
            if ($v->spp_status_ob == 4) {
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
            $data_batal = $data_batal->values();
        }
        if (isset($data)) {
            foreach ($data as $d => $v) {
                $status[] = DB::table('rekam_jejak')->where('rekam_jejak.spp_id', '=', $v->spp_id)->orWhere('rekam_jejak.master_user_id_tujuan', '=', 1)->select('spp_id', 'master_user_id', 'rekam_jejak_revisi', 'created_at')->latest('created_at')->first();
                $rekam_jejak[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_level')
                    ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_level')
                    ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                    ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
                $posisi[] = DB::table('master_hak_akses')->where('master_hak_akses_level', '=', $v->spp_status_posisi)->select('master_hak_akses_keterangan')->first();
            }
        }

        if (isset($data_selesai)) {
            foreach ($data_selesai as $d => $v) {
                $rekam_jejak_selesai[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_level')
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
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_level')
                    ->select('rekam_jejak.*', 'master_hak_akses.*')->get();
                $posisi_batal[] = DB::table('rekam_jejak')->where('spp_id', '=', $v->spp_id)->where('rekam_jejak_status', '=', 5)
                    ->join('master_hak_akses', 'master_user_id', '=', 'master_hak_akses.master_hak_akses_level')
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
                    if ($s->master_user_id == 1 || $s->master_user_id == 2 && $s->master_user_id_tujuan <= 2 || $s->rekam_jejak_revisi !== null && $s->master_user_id !== 2) {

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
        return view('page.spp.spp', compact('id_cetak', 'index_cetak', 'vendor', 'index', 'data', 'data_selesai', 'data_batal', 'rekam_jejak_selesai', 'rekam_jejak_batal', 'posisi', 'status', 'status_revisi', 'rekam_jejak', 'sppb_bayar', 'sppn_terima', 'rekening', 'b', 'bagian_id', 'posisi_batal', 'asal'));
    }
}
