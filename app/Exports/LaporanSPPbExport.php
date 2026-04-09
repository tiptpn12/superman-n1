<?php

namespace App\Exports;

use App\SppbIsi;
use App\SppbBayar;
use App\spp;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanSPPbExport implements FromView, ShouldAutoSize, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $rentang_waktus, $status_bayar, $jenis_spp;

    function __construct($rentang_waktus, $status_bayar, $jenis_spp, $title = 'SPPb')
    {
        $this->rentang_waktu = $rentang_waktus;
        $this->status_bayar = $status_bayar;
        $this->jenis_spp = $jenis_spp;
        $this->title = $title;
        $this->company_id = session()->get('company');
        $this->bagian = session()->get('bagian');
        $this->hakakses = session()->get('hak_akses');

    }

    public function view(): View
    {
        $client = new Client();
        // $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);

        //$karyawan_all = json_decode($response->getBody());
        $karyawan_all = null;
        $karyawan_no_vendor_sppb = [];
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;

        $grup_ui = session()->get('grup_ui');

        if ($this->rentang_waktu !== "semua") {
            $rentang_waktu_raw = $this->rentang_waktu;
            $rentang_waktu = explode(" - ", $rentang_waktu_raw);
            $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
            })->all();
            if ($this->jenis_spp == "semua") {
                $sppb = DB::table('spp_proses')
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->leftjoin('nama_karyawan', 'nama_karyawan.sppb_id', '=', 'spp.sppb_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total')
                    ->addselect('sppb.sppb_metode_pembayaran', 'sppb.sppb_data_metpen', 'nama_karyawan.karyawan_no_rek');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                $no_rek = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            } else if ($this->jenis_spp == "spp_khusus") {
                $sppb = DB::table(
                    'spp_proses'
                )
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            } else {
                $sppb = DB::table('spp')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppb.sppb_total');
                if ($grup_ui != 1) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            }
        } else {
            if ($this->jenis_spp == "semua") {
                $sppb = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->select('spp.*', 'sppb.sppb_total')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            } else if ($this->jenis_spp == "spp_khusus") {
                $sppb = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->select('spp.*', 'sppb.sppb_total')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            } else {
                $sppb = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.sppn_id', '=', null)
                    ->select('spp.*', 'sppb.sppb_total')
                    ->leftjoin('sppb', 'sppb.sppb_id', '=', 'spp.sppb_id');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppb->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppb->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppb->where('spp.company_id', '=', $this->company_id);
                $spp = $sppb->get();
                $totalNominalSppb = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppb += $value->sppb_total;
                }
            }
            // dd($spp);

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
            }

            $sppbisi = [];
            $sppnisi = [];
            foreach ($datanya as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_cash_flow.*')->first();
                }

            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
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
                    )->where('spp.company_id', '=', $this->company_id)
                    ->first();
            }
            // dd($spp);
            // dd($datanya);

            $sppbisi = [];
            $sppnisi = [];
            foreach ($datanya as $d => $val) {
                if ($val->sppb_isi_id != null) {
                    $sppbisi[] = DB::table('sppb_isi')->where('sppb_isi.sppb_isi_id', '=', $val->sppb_isi_id)
                        ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                        ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                        ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                        ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                        ->select('sppb_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_cash_flow.*')->first();
                }

            }

            $sppb_bayar = [];
            $sppn_terima = [];
            foreach ($datanya as $k => $v) {
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
        }
        $data = [];
        foreach ($datanya as $d) {
            if ($d->spp_id) {
                $data[] = $d;
            }
        }
        $rentang_waktu = $this->rentang_waktu;

        // dd($data, $datanya);
        return view('page.laporan.laporan_sppb_export', compact('totalNominalSppb', 'data', 'sppbisi', 'sppb_bayar', 'rentang_waktu', 'karyawan_no_vendor_sppb'));
    }
    public function title(): string
    {
        return $this->title;
    }
}
