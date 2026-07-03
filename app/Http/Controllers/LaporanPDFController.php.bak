<?php

namespace App\Http\Controllers;
use DB;
use PDF;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PDF;

class LaporanPDFController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            $this->bagian = session()->get('bagian');
            $this->hakakses = session()->get('hak_akses');
            //dd($this->user);
            //return $next($request);
            if ($this->user == null) {

                return redirect('login');
            } else {
                return $next($request);
            }
        });
       
        
    }
    public function index(Request $request)
    {
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];
        $karyawan_no_vendor_sppb_sppb = [];
        $karyawan_no_vendor_sppn_sppn = [];
        $grup_ui = session()->get('grup_ui');
        $company_id = session()->get('company');

        $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());
        $karyawan_all = null;
        $jenis_spp = $request->jenis_spp;
        $status_bayar =  $request->status_bayar;
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        // $data_bagian = DB::table('spp')->where('spp.master_bagian_id','=',$bagian)->get();
        //dd($request->rentang_waktu,$jenis_spp);
        if ($request->rentang_waktu !== "semua") {
            $rentang_waktu_raw = $request->rentang_waktu;
            $rentang_waktu = explode(" - ", $rentang_waktu_raw);
            $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
            })->all();
            if ($jenis_spp == "semua") {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();

                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                // dd($spp_sppb);
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.sppb_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
                // dd($spp_sppb_sppn);
            } else if ($jenis_spp == "spp_khusus") {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();
                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
            } else {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();
                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
            }
        } else {
            if ($jenis_spp == "semua") {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();
                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppn_id', '=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '=', null)->select('spp.*')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
            } else if ($jenis_spp == "spp_khusus") {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();
                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppn_id', '=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.master_bagian_id', '=', $bagian)->where('spp.sppb_id', '=', null)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
            } else {
                $sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $company_id);
                $spp_sppb_sppn = $sppb_sppn->get();
                $sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppn_id', '=', null)->select('spp.*');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $company_id);
                $spp_sppb = $sppb->get();
                $sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.master_bagian_id', '=', $bagian)->where('spp.sppb_id', '=', null)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $company_id);
                $spp_sppn = $sppn->get();
            }
        }
        if ($request->status_bayar !== "semua") {
            $datanya_sppb = [];
            foreach ($spp_sppb as $s) {
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb.sppb_jenis',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'spp.spp_status_bayar',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'nama_karyawan.karyawan_no_rek',
                        'nama_karyawan.karyawan_nama',
                        'sppb.master_bank_id',
                        'master_vendor.master_vendor_rekening',
                        'master_vendor.master_vendor_nama'
                    )
                    ->where('spp.spp_status_bayar', '=', $status_bayar)
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }

            $sppb_sppbisi = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppb_sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')

                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_cash_flow.*')->first();
                }
            }

            $sppb_sppb_bayar = [];
            foreach ($datanya_sppb as $k => $v) {
                $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                if ($v->sppb_jenis == "karyawan") {
                    $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                    foreach ($Krywn_sppb as $a => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }

                    foreach ($karyawan_sppb as $b => $v1) {
                        foreach ($v1 as $k1 => $v2) {
                            $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                        }
                    }
                } else if ($v->sppb_jenis == "keuangan") {
                    if ($v->sppb_metode_pembayaran == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        foreach ($Krywn_sppb as $a => $val) {
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                        }

                        foreach ($karyawan_sppb as $b => $v1) {
                            foreach ($v1 as $k1 => $v2) {
                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                    }
                } else {
                    $karyawan_no_vendor_sppb_sppb[$k] = null;
                }
            }

            $datanya_sppn = [];
            foreach ($spp_sppn as $s) {
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn_isi.sppn_isi_id',
                        'sppn.sppn_jenis',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_gl_id as gl_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )
                    ->where('spp.spp_status_terima', '=', $status_bayar)
                    ->where('spp.company_id', '=', $company_id)->first();
            }
            //dd($datanya_sppn);
            $sppn_sppnisi = [];
            foreach ($datanya_sppn as $d => $val) {
                if ($val->sppn_isi_id !== null) {
                    $sppn_sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                }
            }
            $sppn_sppn_terima = [];
            foreach ($datanya_sppn as $k => $v) {
                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }
            $datanya = [];
            foreach ($spp_sppb_sppn as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb.sppb_jenis',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'spp.spp_status_bayar',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'nama_karyawan.karyawan_no_rek',
                        'nama_karyawan.karyawan_nama',
                        'sppb.master_bank_id',
                        'master_vendor.master_vendor_rekening',
                        'master_vendor.master_vendor_nama'
                    )
                    ->where('spp.spp_status_bayar', '=', $request->status_bayar)
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn_isi.sppn_isi_id',
                        'sppn.sppn_jenis',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_gl_id as gl_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )
                    ->where('spp.spp_status_terima', '=', $request->status_bayar)
                    ->where('spp.company_id', '=', $company_id)
                    ->first();

            }
            $sppbisi = [];
            $sppnisi = [];

            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_isi_id) && $val->sppb_isi_id != null) {
                    $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_cash_flow.*')->first();
                }
                


                if (isset($val->sppn_isi_id) && $val->sppn_isi_id != null) {
                    $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                    // dd($sppnisi);
                }
                
            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
                if (isset($v->sppb_id)) {
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                        ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppb_bayar.*')->first();

                    if ($v->sppb_jenis == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        foreach ($Krywn_sppb as $a => $val) {
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                        }

                        foreach ($karyawan_sppb as $b => $v1) {
                            foreach ($v1 as $k1 => $v2) {
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                
                            }
                        }
                    } else if ($v->sppb_jenis == "keuangan") {
                        if ($v->sppb_metode_pembayaran == "karyawan") {
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach ($Krywn_sppb as $a => $val) {
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }

                            foreach ($karyawan_sppb as $b => $v1) {
                                foreach ($v1 as $k1 => $v2) {
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        } else {
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                }
            }
        } else {
            $datanya_sppb = [];
            foreach ($spp_sppb as $s) {
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb.sppb_jenis',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'nama_karyawan.karyawan_no_rek',
                        'nama_karyawan.karyawan_nama',
                        'sppb.master_bank_id',
                        'master_vendor.master_vendor_rekening',
                        'master_vendor.master_vendor_nama'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }


            $sppb_sppbisi = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppb_sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_cash_flow.*')->first();
                }
                
            }

            $sppb_sppb_bayar = [];
            foreach ($datanya_sppb as $k => $v) {
                $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                if ($v->sppb_jenis == "karyawan") {
                    $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                    foreach ($Krywn_sppb as $a => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }

                    foreach ($karyawan_sppb as $b => $v1) {
                        foreach ($v1 as $k1 => $v2) {
                            $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                        }
                    }
                } else if ($v->sppb_jenis == "keuangan") {
                    if ($v->sppb_metode_pembayaran == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        foreach ($Krywn_sppb as $a => $val) {
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                        }

                        foreach ($karyawan_sppb as $b => $v1) {
                            foreach ($v1 as $k1 => $v2) {
                                $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                    }
                } else {
                    $karyawan_no_vendor_sppb_sppb[$k] = null;
                }
            }

            $datanya_sppn = [];
            foreach ($spp_sppn as $s) {
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')

                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn.sppn_jenis',
                        'sppn_isi.sppn_isi_id',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }

            $sppn_sppnisi = [];
            foreach ($datanya_sppn as $d => $val) {
                $sppn_sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                    ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                // dd($sppnisi);
            
            
                 }


            }

            $sppn_sppn_terima = [];
            foreach ($datanya_sppn as $k => $v) {

                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }

            $datanya = [];
            foreach ($spp_sppb_sppn as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'sppb.sppb_jenis',
                        'spp.sppb_id',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'nama_karyawan.karyawan_no_rek',
                        'nama_karyawan.karyawan_nama',
                        'sppb.master_bank_id',
                        'master_vendor.master_vendor_rekening',
                        'master_vendor.master_vendor_nama'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn.sppn_jenis',
                        'sppn_isi.sppn_isi_id',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }

            $sppbisi = [];
            $sppnisi = [];
            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_isi_id) && $val->sppb_isi_id != null) {
                    $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_cash_flow.*')->first();
                }
                


                if (isset($val->sppn_isi_id) && $val->sppn_isi_id != null) {
                    $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                    // dd($sppnisi);
                }
                
            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
                if (isset($v->sppb_id)) {
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                        ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppb_bayar.*')->first();
                    if ($v->sppb_jenis == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        foreach ($Krywn_sppb as $a => $val) {
                            $nama = $val->karyawan_nama;
                            $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                return $value->karyawan_nama == $nama;
                            });
                        }

                        foreach ($karyawan_sppb as $b => $v1) {
                            foreach ($v1 as $k1 => $v2) {
                                $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                
                            }
                        }
                    } else if ($v->sppb_jenis == "keuangan") {
                        if ($v->sppb_metode_pembayaran == "karyawan") {
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                            foreach ($Krywn_sppb as $a => $val) {
                                $nama = $val->karyawan_nama;
                                $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                                    return $value->karyawan_nama == $nama;
                                });
                            }

                            foreach ($karyawan_sppb as $b => $v1) {
                                foreach ($v1 as $k1 => $v2) {
                                    $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                                    
                                }
                            }
                        } else {
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                }
            }
        }
        $data = [];
        foreach ($datanya as $d) {
            if ($d->spp_id) {
                $data[] = $d;
            }
        }
        $data_sppb = [];
        foreach ($datanya_sppb as $d) {
            if ($d->spp_id) {
                $data_sppb[] = $d;
            }
        }
        $data_sppn = [];
        foreach ($datanya_sppn as $d) {
            if ($d->spp_id) {
                $data_sppn[] = $d;
            }
        }
        $rentang_waktu = $request->rentang_waktu;
        //dd($data_sppn,$sppn_sppnisi);
        return view('page.laporan.laporan_pdf', compact('data', 'data_sppb', 'karyawan_no_vendor_sppn_sppn', 'karyawan_no_vendor_sppb_sppb', 'data_sppn', 'sppb_sppbisi', 'sppn_sppnisi', 'sppb_sppb_bayar', 'sppn_sppn_terima', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn', 'rentang_waktu'));
    }

    public function export_pdf(Request $request)
    {
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];
        $karyawan_no_vendor_sppb_sppb = [];
        $karyawan_no_vendor_sppn_sppn = [];
        $grup_ui = session()->get('grup_ui');
        $company_id = session()->get('company');
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;

        $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        $karyawan_all = null;
        // $karyawan_all = json_decode($response->getBody());
        $jenis_spp = $request->jenis_spp;
        if ($request->rentang_waktu !== "semua") {
            $rentang_waktu_raw = $request->rentang_waktu;
            $rentang_waktu = explode(" - ", $rentang_waktu_raw);
            $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
            })->all();
            if ($jenis_spp == "semua") {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $spp_sppb_sppn = $spp_sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppb_sppn = $spp_sppb_sppn->where('spp.master_bagian_id', '=', $bagian)->get();
                }
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $spp_sppb = $spp_sppb->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppb = $spp_sppb->where('spp.master_bagian_id', '=', $bagian)->get();
                }
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $spp_sppn = $spp_sppn->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppn = $spp_sppn->where('spp.master_bagian_id', '=', $bagian)->get();
                }
            } else if ($jenis_spp == "spp_khusus") {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*')->get();
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*')->get();
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.sppb_id', '=', null)->where('spp.company_id', '=', $company_id)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc')->get();
            } else {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $spp_sppb_sppn = $spp_sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppb_sppn = $spp_sppb_sppn->where('spp.master_bagian_id', '=', $bagian)->get();
                }
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)->select('spp.*');
                if ($grup_ui != 1) {
                    $spp_sppb = $spp_sppb->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppb = $spp_sppb->where('spp.master_bagian_id', '=', $bagian)->get();
                }
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '=', null)->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $spp_sppn = $spp_sppn->where('spp.sppd_posisi', '=', $hak_akses)->get();
                } else {
                    $spp_sppn = $spp_sppn->where('spp.master_bagian_id', '=', $bagian)->get();
                }
            }
        } else {
            if ($jenis_spp = "semua") {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*')->get();
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->select('spp.*')->get();
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.sppb_id', '=', null)->where('spp.company_id', '=', $company_id)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc')->get();
            } else if ($jenis_spp = "spp_khusus") {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*')->get();
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->select('spp.*')->get();
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '=', 2)->where('spp.sppb_id', '=', null)->where('spp.company_id', '=', $company_id)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc')->get();
            } else {
                $spp_sppb_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)->select('spp.*')->get();
                $spp_sppb = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.company_id', '=', $company_id)
                    ->where('spp.sppn_id', '=', null)->select('spp.*')->get();
                $spp_sppn = DB::table('spp')->where('spp.spp_status_ob', '!=', 2)->where('spp.master_bagian_id', '!=', 2)->where('spp.sppb_id', '=', null)->where('spp.company_id', '=', $company_id)
                    ->select('spp.*')->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc')->get();
            }
            // dd($spp_sppn);

        }
        //dd($spp_sppb_sppn,$spp_sppb,$spp_sppn);
        if ($request->status_bayar !== "semua") {
            $datanya_sppb = [];
            foreach ($spp_sppb as $s) {
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppb.sppb_jenis',
                        'sppb.sppb_data_metpen',
                        'sppb.sppb_metode_pembayaran',
                        'spp.sppb_id',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'spp.spp_status_bayar'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.spp_status_bayar', '=', $request->status_bayar)->first();
            }

            $sppbKaryawanVendor = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_data_metpen && $val->sppb_metode_pembayaran) {
                    if ($val->sppb_metode_pembayaran == "bank") {
                        if ($val->sppb_data_metpen == 'input_data' || $val->sppb_data_metpen == 'lampirkan_data') {
                            $sppbKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                        } else if ($val->sppb_data_metpen == 'master_data') {
                            $sppbKaryawanVendor[$d] = DB::table('master_vendor')->where('master_vendor_id', $val->master_bank_id)->select('master_vendor_rekening as rekening', 'master_vendor_atas_nama as nama')->first();
                        }
                    } else if ($val->sppb_metode_pembayaran == "tidak_transfer") {
                        if ($val->sppb_data_metpen == 'input_data') {
                            $sppbKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                        }
                    }
                }
            }

            $sppb_sppbisi = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppb_sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_cash_flow.*')->first();
                }
                
            }

            $sppb_sppb_bayar = [];
            foreach ($datanya_sppb as $k => $v) {
                $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                if ($v->sppb_jenis == "karyawan") {
                    $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                    // foreach ($Krywn_sppb as $a => $val) {
                    //     $nama = $val->karyawan_nama;
                    //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //         return $value->karyawan_nama == $nama;
                    //     });
                    // }

                    // foreach ($karyawan_sppb as $b => $v1) {
                    //     foreach ($v1 as $k1 => $v2) {
                    //         $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                    //     }
                    // }
                } else if ($v->sppb_jenis == "keuangan") {
                    if ($v->sppb_metode_pembayaran == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppb as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }

                        // foreach ($karyawan_sppb as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                        //     }
                        // }
                    } else {
                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                    }
                } else {
                    $karyawan_no_vendor_sppb_sppb[$k] = null;
                }
            }

            $datanya_sppn = [];
            foreach ($spp_sppn as $s) {
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn.sppn_id',
                        'sppn_isi.sppn_isi_id',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_jenis',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.spp_status_terima', '=', $request->status_bayar)->first();
            }

            $sppn_sppnisi = [];
            foreach ($datanya_sppn as $d => $val) {
                if ($val->sppn_isi_id != null) {
                    $sppn_sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                }
            }
            $sppn_sppn_terima = [];
            foreach ($datanya_sppn as $k => $v) {

                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }
            $datanya = [];
            foreach ($spp_sppb_sppn as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb.sppb_jenis',
                        'sppb.sppb_data_metpen',
                        'sppb.sppb_metode_pembayaran',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'spp.spp_status_bayar'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.spp_status_bayar', '=', $request->status_bayar)->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn_isi.sppn_isi_id',
                        'sppn.sppn_jenis',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->where('spp.spp_status_terima', '=', $request->status_bayar)->first();
            }
            // dd($datanya);
            $sppbSppnKaryawanVendor = [];
            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_id)) {
                    if ($val->sppb_data_metpen && $val->sppb_metode_pembayaran) {
                        if ($val->sppb_metode_pembayaran == "bank") {
                            if ($val->sppb_data_metpen == 'input_data' || $val->sppb_data_metpen == 'lampirkan_data') {
                                $sppbSppnKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                            } else if ($val->sppb_data_metpen == 'master_data') {
                                $sppbSppnKaryawanVendor[$d] = DB::table('master_vendor')->where('master_vendor_id', $val->master_bank_id)->select('master_vendor_rekening as rekening', 'master_vendor_atas_nama as nama')->first();
                            }
                        }
                    }
                }
                $sppn_sppn_terima = [];
                foreach($datanya_sppn as $k => $v){
                    
                    $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id','=',$v->sppn_id)
                                        ->join('master_rekening','sppn_terima.master_rekening_id','=','master_rekening.master_rekening_id')
                                        ->select('master_rekening.*','sppn_terima.*')->first();
                                        
                }
            $datanya = [];
            foreach($spp_sppb_sppn as $s){
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppb','spp.sppb_id','=','sppb.sppb_id')
                            ->leftJoin('sppb_isi','sppb.sppb_id','=','sppb_isi.sppb_id')->leftJoin('sppb_uraian','sppb_isi.sppb_isi_id','=','sppb_uraian.sppb_isi_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','spp.sppb_id','sppb.sppb_jenis','sppb_isi.sppb_isi_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),'sppb_no','sppb_tanggal',
                            'sppb_total','spp_status_ob',DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                            'sppb_isi.master_kode_vendor_id as rekening_sppb','sppb_isi.master_gl_id as gl_sppb',
                            'sppb_isi.master_cost_center_id as cost_center_sppb',
                            'sppb_isi.master_cash_flow_id as cash_flow_sppb','spp.spp_status_bayar')
                            ->where('spp.spp_status_bayar','=',$request->status_bayar)->first();
                    $datanya[] = DB::table('spp')->where('spp.spp_id','=',$s->spp_id)->leftJoin('sppn','spp.sppn_id','=','sppn.sppn_id')
                            ->leftJoin('sppn_isi','sppn.sppn_id','=','sppn_isi.sppn_id')->leftJoin('sppn_uraian','sppn_isi.sppn_isi_id','=','sppn_uraian.sppn_isi_id')
                            ->leftJoin('master_bagian','spp.master_bagian_id','=','master_bagian.master_bagian_id')
                            ->select('spp_id','sppn_isi.sppn_isi_id','sppn.sppn_jenis','spp.sppn_id','master_bagian_nama','spp_kabag','spp_status_proses','spp_status_posisi',DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                            'sppn_no','sppn_tanggal','sppn_jumlah','spp_status_ob','sppn_isi.master_gl_id as gl_sppn',
                            DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),'sppn_isi.master_kode_vendor_id as rekening_sppn',
                            'sppn_isi.master_cost_center_id as cost_center_sppn','sppn_isi.master_profit_center_id as profit_center_sppn',
                            'sppn_isi.master_cash_flow_id as cash_flow_sppn','spp.spp_status_terima')
                            ->where('spp.spp_status_terima','=',$request->status_bayar)->first();
            }
            $sppbisi = [];
            $sppnisi = [];
            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_isi_id) && $val->sppb_isi_id != null) {
                    $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_cash_flow.*')->first();
                }
                


                if (isset($val->sppn_isi_id) && $val->sppn_isi_id != null) {
                    $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                    // dd($sppnisi);
                }
                
            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
                if (isset($v->sppb_id)) {
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                        ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppb_bayar.*')->first();
                    if ($v->sppb_jenis == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppb as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }

                        // foreach ($karyawan_sppb as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                        //     }
                        // }
                    } else if ($v->sppb_jenis == "keuangan") {
                        if ($v->sppb_metode_pembayaran == "karyawan") {
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                            // foreach ($Krywn_sppb as $a => $val) {
                            //     $nama = $val->karyawan_nama;
                            //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            //         return $value->karyawan_nama == $nama;
                            //     });
                            // }

                            // foreach ($karyawan_sppb as $b => $v1) {
                            //     foreach ($v1 as $k1 => $v2) {
                            //         $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                            //     }
                            // }
                        } else {
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                }
            }
        } else {
            $datanya_sppb = [];
            foreach ($spp_sppb as $s) {
                $datanya_sppb[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)
                    ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                    ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppb.sppb_jenis',
                        'sppb.master_bank_id',
                        'spp.sppb_id',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }
            // dd($datanya_sppb);

            $sppbKaryawanVendor = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_data_metpen && $val->sppb_metode_pembayaran) {
                    if ($val->sppb_metode_pembayaran == "bank") {
                        if ($val->sppb_data_metpen == 'input_data' || $val->sppb_data_metpen == 'lampirkan_data') {
                            $sppb_metpen[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                        } else if ($val->sppb_data_metpen == 'master_data') {
                            $sppb_metpen[$d] = DB::table('master_vendor')->where('master_vendor_id', $val->master_bank_id)->select('master_vendor_rekening as rekening', 'master_vendor_atas_nama as nama')->first();
                        }
                    } else if ($val->sppb_metode_pembayaran == "tidak_transfer") {
                        if ($val->sppb_data_metpen == 'input_data') {
                            $sppbKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                        }
                    }
                } else {
                    $sppb_metpen[$d] = null;
                }
            }

            $sppb_sppbisi = [];
            foreach ($datanya_sppb as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppb_sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_cash_flow.*')->first();
                }
                
            }

            $sppb_sppb_bayar = [];
            foreach ($datanya_sppb as $k => $v) {
                $sppb_sppb_bayar[$k] = DB::table('sppb_bayar')
                    ->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                    ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppb_bayar.*')->first();
                if ($v->sppb_jenis == "karyawan") {
                    $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                    // foreach ($Krywn_sppb as $a => $val) {
                    //     $nama = $val->karyawan_nama;
                    //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                    //         return $value->karyawan_nama == $nama;
                    //     });
                    // }

                    // foreach ($karyawan_sppb as $b => $v1) {
                    //     foreach ($v1 as $k1 => $v2) {
                    //         $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                    //     }
                    // }
                } else if ($v->sppb_jenis == "keuangan") {
                    if ($v->sppb_metode_pembayaran == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppb as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }

                        // foreach ($karyawan_sppb as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         $karyawan_no_vendor_sppb_sppb[$k][] = $v2->karyawan_no_vendor;
                        //     }
                        // }
                    } else {
                        $karyawan_no_vendor_sppb_sppb[$k] = null;
                    }
                } else {
                    $karyawan_no_vendor_sppb_sppb[$k] = null;
                }
            }

            $datanya_sppn = [];
            foreach ($spp_sppn as $s) {
                $datanya_sppn[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn.sppn_jenis',
                        'sppn_isi.sppn_isi_id',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }

            $sppn_sppnisi = [];
            foreach ($datanya_sppn as $d => $val) {
                $sppn_sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                    ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                // dd($sppnisi);
            
            
                 }


            }

            $sppn_sppn_terima = [];
            foreach ($datanya_sppn as $k => $v) {
                $sppn_sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                    ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->select('master_rekening.*', 'sppn_terima.*')->first();
            }

            $datanya = [];
            foreach ($spp_sppb_sppn as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb.sppb_jenis',
                        'sppb.sppb_data_metpen',
                        'sppb.sppb_metode_pembayaran',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_gl_id as gl_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn_isi.sppn_isi_id',
                        'sppn.sppn_jenis',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        'sppn_isi.master_gl_id as gl_sppn',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )
                    ->where('spp.company_id', '=', $company_id)
                    ->first();
            }
            $sppbSppnKaryawanVendor = [];
            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_id)) {
                    if ($val->sppb_data_metpen && $val->sppb_metode_pembayaran) {
                        if ($val->sppb_metode_pembayaran == "bank") {
                            if ($val->sppb_data_metpen == 'input_data' || $val->sppb_data_metpen == 'lampirkan_data') {
                                $sppbSppnKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                            } else if ($val->sppb_data_metpen == 'master_data') {
                                $sppbSppnKaryawanVendor[$d] = DB::table('master_vendor')->where('master_vendor_id', $val->master_bank_id)->select('master_vendor_rekening as rekening', 'master_vendor_atas_nama as nama')->first();
                            }
                        } else if ($val->sppb_metode_pembayaran == "tidak_transfer") {
                            if ($val->sppb_data_metpen == 'input_data') {
                                $sppbKaryawanVendor[$d] = DB::table('nama_karyawan')->where('sppb_id', $val->sppb_id)->select('karyawan_no_rek as rekening', 'karyawan_nama as nama')->first();
                            }
                        }
                    } else {
                        $sppbSppnKaryawanVendor[$d] = null;
                    }
                } else {
                    $sppbSppnKaryawanVendor[$d] = null;
                }
            }

            $sppbisi = [];
            $sppnisi = [];
            foreach ($datanya as $d => $val) {
                if (isset($val->sppb_isi_id) && $val->sppb_isi_id != null) {
                    $sppbisi[$d] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_cash_flow.*')->first();
                }
                


                if (isset($val->sppn_isi_id) && $val->sppn_isi_id != null) {
                    $sppnisi[$d] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                        ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                        ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                    // dd($sppnisi);
                }
                
            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
                if (isset($v->sppb_id)) {
                    $sppb_bayar[$k] = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $v->sppb_id)
                        ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppb_bayar.*')->first();
                    if ($v->sppb_jenis == "karyawan") {
                        $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppb as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }

                        // foreach ($karyawan_sppb as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                        //     }
                        // }
                    } else if ($v->sppb_jenis == "keuangan") {
                        if ($v->sppb_metode_pembayaran == "karyawan") {
                            $Krywn_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $v->sppb_id)->select('nama_karyawan.*')->get();
                            // foreach ($Krywn_sppb as $a => $val) {
                            //     $nama = $val->karyawan_nama;
                            //     $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            //         return $value->karyawan_nama == $nama;
                            //     });
                            // }

                            // foreach ($karyawan_sppb as $b => $v1) {
                            //     foreach ($v1 as $k1 => $v2) {
                            //         $karyawan_no_vendor_sppb[$k][] = $v2->karyawan_no_vendor;
                            //     }
                            // }
                        } else {
                            $karyawan_no_vendor_sppb[$k] = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb[$k] = null;
                    }
                }
                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                }
            }
        }
        $data = [];
        foreach ($datanya as $d) {
            if ($d->spp_id) {
                $data[] = $d;
            }
        }
        $data_sppb = [];
        foreach ($datanya_sppb as $d) {
            if ($d->spp_id) {
                $data_sppb[] = $d;
            }
        }
        $data_sppn = [];
        foreach ($datanya_sppn as $d) {
            if ($d->spp_id) {
                $data_sppn[] = $d;
            }
        }
        $rentang_waktu = $request->rentang_waktu;
        // dd($data_sppb,$datanya_sppb);
        // return view('page.laporan.laporan_pdf_export', compact('data','data_sppb','data_sppn','sppb_sppbisi','sppn_sppnisi','sppb_sppb_bayar','sppn_sppn_terima','sppbisi','sppnisi','sppb_bayar','sppn_terima','rentang_waktu'));
        $pdf = PDF::loadView('page.laporan.laporan_pdf_export', compact('data', 'karyawan_no_vendor_sppb_sppb', 'karyawan_no_vendor_sppn_sppn', 'data_sppb', 'data_sppn', 'sppb_sppbisi', 'sppn_sppnisi', 'sppb_sppb_bayar', 'sppn_sppn_terima', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'rentang_waktu', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn', 'sppbKaryawanVendor', 'sppbSppnKaryawanVendor'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
