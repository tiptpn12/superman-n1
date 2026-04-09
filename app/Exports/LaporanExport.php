<?php

namespace App\Exports;

use App\SppbIsi;
use App\SppnIsi;
use App\SppbBayar;
use App\SppnTerima;
use App\spp;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $rentang_waktus, $status_bayar, $jenis_spp;

    function __construct($rentang_waktus, $status_bayar, $jenis_spp, $title = 'SPPb dan SPPn')
    {
        $this->rentang_waktu = $rentang_waktus;
        $this->status_bayar = $status_bayar;
        $this->title = $title;
        $this->jenis_spp = $jenis_spp;
        $this->company_id = session()->get('company');
        $this->bagian = session()->get('bagian');
        $this->hakakses = session()->get('hak_akses');
    }

    public function view(): View
    {
        $grup_ui = session()->get('grup_ui');
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];
        $hak_akses = $this->hakakses;
        $bagian = $this->bagian;
        $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        // $karyawan_all = json_decode($response->getBody());
        $karyawan_all = null;
        if ($this->rentang_waktu !== "semua") {
            $rentang_waktu_raw = $this->rentang_waktu;
            $rentang_waktu = explode(" - ", $rentang_waktu_raw);
            $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
            })->all();
            if ($this->jenis_spp == "semua") {
                $sppb_sppn = DB::table('spp')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '!=', null)
                    ->where('spp.sppn_id', '!=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;

            } else if ($this->jenis_spp == "spp_khusus") {
                $sppb_sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.sppb_id', '!=', null)
                    ->where('spp.sppn_id', '!=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;

            } else {
                $sppb_sppn = DB::table('spp')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.sppb_id', '!=', null)
                    ->where('spp.sppn_id', '!=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id');
                // ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;

            }
        } else {
            if ($this->jenis_spp == "semua") {
                $sppb_sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '!=', null)->where('spp.sppn_id', '!=', null)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;
            } else if ($this->jenis_spp == "spp_khusus") {
                $sppb_sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.sppb_id', '!=', null)
                    ->where('spp.sppn_id', '!=', null)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;
            } else {
                $sppb_sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.sppb_id', '!=', null)
                    ->where('spp.sppn_id', '!=', null)
                    ->select('spp.*', 'sppb.sppb_total', 'sppn.sppn_jumlah')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb_sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb_sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb_sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb_sppn->get();
                $totalNominalSppb = 0;
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                $sum_spp = $totalNominalSppb + $totalNominalSppn;
            }

        }
        if ($this->status_bayar !== "semua") {
            $datanya = [];
            foreach ($spp as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'sppb_jenis',
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
                    )->where('spp.company_id', '=', $this->company_id)
                    ->where('spp.spp_status_bayar', '=', $this->status_bayar)->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
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
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )->where('spp.company_id', '=', $this->company_id)
                    ->where('spp.spp_status_terima', '=', $this->status_bayar)->first();
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
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_cash_flow.*', 'master_gl.*')->first();
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
                            $karyawan_no_vendor_sppb = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb = null;
                    }
                }

                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                    if ($v->sppn_jenis == "karyawan") {
                        $Krywn_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppn as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }
                        // foreach ($karyawan_sppn as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         $karyawan_no_vendor_sppn[$k][] = $v2->karyawan_no_vendor;
                        //     }
                        // }
                    } else if ($v->sppn_jenis == "keuangan") {
                        if ($v->sppn_metode_pembayaran == "karyawan") {

                            $Krywn_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)->select('nama_karyawan.*')->get();
                            // foreach ($Krywn_sppn as $a => $val) {
                            //     $nama = $val->karyawan_nama;
                            //     $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            //         return $value->karyawan_nama == $nama;
                            //     });
                            // }
                            // foreach ($karyawan_sppn as $b => $v1) {
                            //     foreach ($v1 as $k1 => $v2) {
                            //         $karyawan_no_vendor_sppn[$k][] = $v2->karyawan_no_vendor;
                            //     }
                            // }
                        } else {
                            $karyawan_no_vendor_sppn = null;
                        }

                    } else {
                        $karyawan_no_vendor_sppn = null;

                    }
                }
            }
        } else {
            $datanya = [];
            foreach ($spp as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                    ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('master_vendor', 'master_vendor.master_vendor_id', '=', 'sppb.master_bank_id')
                    ->select(
                        'spp_id',
                        'spp.sppb_id',
                        'sppb_isi.sppb_isi_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppb_no',
                        'sppb_tanggal',
                        'sppb_jenis',
                        'sppb_total',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                        'sppb_isi.master_kode_vendor_id as rekening_sppb',
                        'sppb_isi.master_cost_center_id as cost_center_sppb',
                        'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                        'sppb.sppb_metode_pembayaran',
                        'sppb.sppb_data_metpen',
                        'nama_karyawan.karyawan_no_rek',
                        'nama_karyawan.karyawan_nama',
                        'sppb.master_bank_id',
                        'master_vendor.master_vendor_rekening',
                        'master_vendor.master_vendor_nama'
                    )->where('spp.company_id', '=', $this->company_id)
                    ->first();
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppn_isi.sppn_isi_id',
                        'spp.sppn_id',
                        'master_bagian_nama',
                        'spp_kabag',
                        'spp_status_proses',
                        'spp_status_posisi',
                        DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                        'sppn_no',
                        'sppn_jenis',
                        'sppn_tanggal',
                        'sppn_jumlah',
                        'spp_status_ob',
                        DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )->where('spp.company_id', '=', $this->company_id)
                    ->first();
            }
            //dd($datanya);
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
                            $karyawan_no_vendor_sppb = null;
                        }
                    } else {
                        $karyawan_no_vendor_sppb = null;
                    }
                }
                if (isset($v->sppn_id)) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();
                    if ($v->sppn_jenis == "karyawan") {
                        $Krywn_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)->select('nama_karyawan.*')->get();
                        // foreach ($Krywn_sppn as $a => $val) {
                        //     $nama = $val->karyawan_nama;
                        //     $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                        //         return $value->karyawan_nama == $nama;
                        //     });
                        // }
                        // // foreach ($Krywn_sppn as $b => $v1) {
                        // //     foreach ($v1 as $k1 => $v2) {
                        // //         $karyawan_no_vendor_sppn[$k][] = $v2->karyawan_no_vendor;
                        // //     }
                        // // }
                        // foreach ($Krywn_sppn as $b => $v1) {
                        //     foreach ($v1 as $k1 => $v2) {
                        //         // Untuk debugging, periksa nilai $v2
                        //         // dd($v2); // Berhenti dan dump nilai

                        //         if (is_object($v2) && isset($v2->karyawan_no_vendor)) {
                        //             $karyawan_no_vendor_sppn[$k][] = $v2->karyawan_no_vendor;
                        //         } else {
                        //             $karyawan_no_vendor_sppn[$k][] = 'No Vendor Found';
                        //         }
                        //     }
                        // }
                    } else if ($v->sppn_jenis == "keuangan") {
                        if ($v->sppn_metode_pembayaran == "karyawan") {

                            $Krywn_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $v->sppn_id)->select('nama_karyawan.*')->get();
                            // foreach ($Krywn_sppn as $a => $val) {
                            //     $nama = $val->karyawan_nama;
                            //     $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            //         return $value->karyawan_nama == $nama;
                            //     });
                            // }
                            // foreach ($Krywn_sppn as $b => $v1) {
                            //     foreach ($v1 as $k1 => $v2) {
                            //         $karyawan_no_vendor_sppn[$k][] = $v2->karyawan_no_vendor;
                            //     }
                            // }
                        } else {
                            $karyawan_no_vendor_sppn = null;
                        }

                    } else {
                        $karyawan_no_vendor_sppn = null;

                    }
                }
            }
        }
        $data = [];
        foreach ($datanya as $d) {
            if ($d->spp_id) {
                $data[] = $d;
            }
        }
        $rentang_waktu = $this->rentang_waktu;
        //dd($data);
        return view('page.laporan.laporan_export', compact('sum_spp', 'data', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'rentang_waktu', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn'));
    }

    public function title(): string
    {
        return $this->title;
    }

}
