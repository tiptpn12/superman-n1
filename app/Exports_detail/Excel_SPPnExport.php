<?php

namespace App\Exports_detail;

use App\SppbIsi;
use App\SppnIsi;
use App\SppbBayar;
use App\SppnTerima;
use App\spp;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Http\Request;

class Excel_SPPnExport implements FromView, ShouldAutoSize, WithTitle
{
    /**
     * @return \Illuminate\Support\View
     */

    protected $rentang_waktus, $status_bayar, $jenis_spp;

    function __construct($rentang_waktus, $status_bayar, $jenis_spp, $title = 'SPPn')
    {
        $this->rentang_waktu = $rentang_waktus;
        $this->status_bayar = $status_bayar;
        $this->title = $title;
        $this->jenis_spp = $jenis_spp;
        $this->company_id = session()->get('company');
        $this->bagian = session()->get('bagian');
        $this->hakakses = session()->get('hak_akses');
    }


    public function View(): view
    {
        $grup_ui = session()->get('grup_ui');

        $posisi_dinamis = [];

        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response = $client->request('GET', $url, [
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());
        $hak_akses = $this->hakakses;
        $bagian = $this->bagian;
        $karyawan_no_vendor_sppn = [];
        if (($this->rentang_waktu !== "semua")) {
            $rentang_waktu_raw = $this->rentang_waktu;
            $rentang_waktu = explode(" - ", $rentang_waktu_raw);
            $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
                return date('Y-m-d', strtotime($item));
            })->all();
            if ($this->jenis_spp == "semua") {
                $sppn = DB::table('spp')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            } else if ($this->jenis_spp == "spp_khusus") {
                $sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            } else {
                $sppn = DB::table('spp')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            }

        } else {
            if ($this->jenis_spp == "semua") {
                $sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($grup_ui != 1) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            } else if ($this->jenis_spp == "spp_khusus") {
                $sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.master_bagian_id', '=', 2)
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            } else {
                $sppn = DB::table('spp_proses')
                    ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', NULL)
                    ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
                    ->where('spp.master_bagian_id', '!=', 2)
                    ->where('spp.spp_status_ob', '!=', 2)
                    ->where('spp.sppb_id', '=', null)
                    ->select('spp.*', 'sppn.sppn_jumlah')
                    ->leftjoin('sppn', 'sppn.sppn_id', '=', 'spp.sppn_id')
                    ->groupBy('spp_tanggal')->orderBy('spp_tanggal', 'desc');
                if ($hak_akses >= 10 && $hak_akses <= 17) {
                    $sppn->where('spp.sppd_posisi', '=', $hak_akses);
                } else {
                    $sppn->where('spp.master_bagian_id', '=', $bagian);
                }
                $sppn->where('spp.company_id', '=', $this->company_id);
                $spp = $sppn->get();
                $totalNominalSppn = 0;
                foreach ($spp as $key => $value) {
                    $totalNominalSppn += $value->sppn_jumlah;
                }
                foreach ($spp as $v) {
                    $posisi_dinamis[] = DB::table('master_hak_akses')
                        ->where('master_hak_akses.master_hak_akses_id', '=', $v->sppd_posisi)
                        ->select('master_hak_akses.*')
                        ->first();
                }
            }
        }

        if ($this->status_bayar !== "semua") {
            $datanya = [];
            foreach ($spp as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)
                    ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                    ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('sppn_terima', 'sppn.sppn_id', '=', 'sppn_terima.sppn_id')
                    ->leftJoin('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_vendor', 'sppn_isi.master_kode_vendor_id', '=', 'master_vendor.master_vendor_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppd_posisi',
                        'sppn_isi.*',
                        'sppn_uraian.*',
                        'sppn_terima.*',
                        'master_cost_center.*',
                        'master_rekening.*',
                        'master_profit_center.*',
                        'master_gl.*',
                        'master_vendor.*',
                        'master_cash_flow.*',
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
                        'sppn_uraian_uraian as sppn_uraian2',
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_gl_id as gl_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                        'spp.spp_status_terima'
                    )->where('spp.company_id', '=', $this->company_id)
                    ->where('spp.spp_status_terima', '=', $this->status_bayar)->get();
            }

            $sppnisi = [];
            foreach ($datanya as $key => $val1) {
                foreach ($val1 as $d => $val) {
                    if ($val->sppn_isi_id != null) {
                        $sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                            ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                            ->leftjoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')

                            ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                            ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                            ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                    }
                }
            }

            $sppn_terima = [];
            foreach ($datanya as $key => $v1) {
                foreach ($v1 as $k => $v) {
                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();

                }
            }
        } else {
            $datanya = [];
            foreach ($spp as $s) {
                $datanya[] = DB::table('spp')->where('spp.spp_id', '=', $s->spp_id)

                    ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                    ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                    ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                    ->leftJoin('sppn_terima', 'sppn.sppn_id', '=', 'sppn_terima.sppn_id')
                    ->leftJoin('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_vendor', 'sppn_isi.master_kode_vendor_id', '=', 'master_vendor.master_vendor_id')
                    ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select(
                        'spp_id',
                        'sppd_posisi',
                        'sppn_isi.*',
                        'sppn_uraian.*',
                        'sppn_terima.*',
                        'master_cost_center.*',
                        'master_rekening.*',
                        'master_profit_center.*',
                        'master_gl.*',
                        'master_vendor.*',
                        'master_cash_flow.*',
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
                        'sppn_uraian_uraian as sppn_uraian2',
                        'sppn_isi.master_kode_vendor_id as rekening_sppn',
                        'sppn_isi.master_gl_id as gl_sppn',
                        'sppn_isi.master_cost_center_id as cost_center_sppn',
                        'sppn_isi.master_profit_center_id as profit_center_sppn',
                        'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                    )->where('spp.company_id', '=', $this->company_id)
                    ->get();
            }

            $sppnisi = [];
            foreach ($datanya as $d => $val1) {
                foreach ($val1 as $key => $val) {
                    if ($val->sppn_isi_id != null) {
                        $sppnisi[] = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $val->sppn_id)
                            ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                            ->leftjoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                            ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                            ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                            ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                            ->select('sppn_isi.*', 'master_rekening.*', 'master_cost_center.*', 'master_gl.*', 'master_profit_center.*', 'master_cash_flow.*')->first();
                        // dd($sppnisi);
                    }

                }
            }

            $sppn_terima = [];
            foreach ($datanya as $k => $v1) {
                foreach ($v1 as $key => $v) {

                    $sppn_terima[$k] = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $v->sppn_id)
                        ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                        ->select('master_rekening.*', 'sppn_terima.*')->first();

                }
            }
        }

        $data = [];
        foreach ($datanya as $d1) {
            foreach ($d1 as $d) {
                if ($d->spp_id) {
                    $data[] = $d;
                }
            }
        }

        //dd($data);
        $posisi_dinamis = [];
        foreach ($data as $d) {
            $posisi_dinamis[] = DB::table('master_hak_akses')
                ->where('master_hak_akses.master_hak_akses_id', '=', $d->sppd_posisi)
                ->select('master_hak_akses.*')
                ->first();
        }

        $rentang_waktu = $this->rentang_waktu;
        return view('page.laporan.laporan_excel_sppn', compact('totalNominalSppn', 'data', 'sppnisi', 'sppn_terima', 'rentang_waktu', 'karyawan_no_vendor_sppn', 'posisi_dinamis'));
    }

    public function title(): string
    {
        return $this->title;
    }
}
