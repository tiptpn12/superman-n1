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
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        $company_id = $this->company_id;

        $query = DB::table('spp')
            ->join('spp_proses', 'spp.spp_id', '=', 'spp_proses.spp_id')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            // SPPb Joins
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppb_bayar', 'sppb.sppb_id', '=', 'sppb_bayar.sppb_id')
            ->leftJoin('master_rekening as rek_bayar_sppb', 'sppb_bayar.master_rekening_id', '=', 'rek_bayar_sppb.master_rekening_id')
            ->leftJoin('master_rekening as rek_isi_sppb', 'sppb_isi.master_kode_vendor_id', '=', 'rek_isi_sppb.master_rekening_id')
            ->leftJoin('master_gl as gl_sppb', 'sppb_isi.master_gl_id', '=', 'gl_sppb.master_gl_id')
            ->leftJoin('master_cost_center as cc_sppb', 'sppb_isi.master_cost_center_id', '=', 'cc_sppb.master_cost_center_id')
            ->leftJoin('master_cash_flow as cf_sppb', 'sppb_isi.master_cash_flow_id', '=', 'cf_sppb.master_cash_flow_id')
            // SPPn Joins
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('sppn_terima', 'sppn.sppn_id', '=', 'sppn_terima.sppn_id')
            ->leftJoin('master_rekening as rek_terima_sppn', 'sppn_terima.master_rekening_id', '=', 'rek_terima_sppn.master_rekening_id')
            ->leftJoin('master_rekening as rek_isi_sppn', 'sppn_isi.master_kode_vendor_id', '=', 'rek_isi_sppn.master_rekening_id')
            ->leftJoin('master_gl as gl_sppn', 'sppn_isi.master_gl_id', '=', 'gl_sppn.master_gl_id')
            ->leftJoin('master_cost_center as cc_sppn', 'sppn_isi.master_cost_center_id', '=', 'cc_sppn.master_cost_center_id')
            ->leftJoin('master_profit_center as pc_sppn', 'sppn_isi.master_profit_center_id', '=', 'pc_sppn.master_profit_center_id')
            ->leftJoin('master_cash_flow as cf_sppn', 'sppn_isi.master_cash_flow_id', '=', 'cf_sppn.master_cash_flow_id')
            // Other Joins
            ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
            ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
            ->where('spp.spp_status_ob', '!=', 2)
            ->whereNotNull('spp.sppb_id')
            ->whereNotNull('spp.sppn_id') // Keeping the original "BOTH" logic
            ->where('spp.company_id', '=', $company_id);

        // Filters
        if ($this->rentang_waktu !== "semua") {
            $rentang = explode(" - ", $this->rentang_waktu);
            $start = date('Y-m-d', strtotime($rentang[0]));
            $end = date('Y-m-d', strtotime($rentang[1]));
            $query->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), [$start, $end]);
        } else {
            $query->whereNull('spp_proses.spp_proses_petugas_kas_dan_bank');
        }

        if ($this->jenis_spp == "spp_khusus") {
            $query->where('spp.master_bagian_id', '=', 2);
        } else if ($this->jenis_spp != "semua") {
            $query->where('spp.master_bagian_id', '!=', 2);
        }

        if ($this->status_bayar !== "semua") {
            $query->where(function($q) {
                $q->where('spp.spp_status_bayar', $this->status_bayar)
                  ->orWhere('spp.spp_status_terima', $this->status_bayar);
            });
        }

        // Access Control
        if ($grup_ui != 1) {
            if ($hak_akses >= 10 && $hak_akses <= 17) {
                $query->where('spp.sppd_posisi', '=', $hak_akses);
            } else {
                $query->where('spp.master_bagian_id', '=', $bagian);
            }
        } else {
            $query->where('spp.master_bagian_id', '=', $bagian);
        }

        $results = $query->select(
            'spp.spp_id',
            'spp.sppb_id',
            'spp.sppn_id',
            'sppb.sppb_no',
            'sppb.sppb_tanggal',
            'sppb.sppb_total',
            'sppb.sppb_jenis',
            'sppn.sppn_no',
            'sppn.sppn_tanggal',
            'sppn.sppn_jumlah',
            'sppn.sppn_jenis',
            'master_bagian.master_bagian_nama',
            'spp.spp_kabag',
            'spp.spp_status_proses',
            'spp.spp_status_posisi',
            'spp.spp_status_ob',
            DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
            DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian.sppb_uraian_uraian SEPARATOR ', ') as sppb_uraian2"),
            DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian.sppn_uraian_uraian SEPARATOR ', ') as sppn_uraian2"),
            // SPPb derived fields
            DB::raw("MAX(cf_sppb.master_cash_flow_kode) as cf_kode_sppb"),
            DB::raw("MAX(sppb_bayar.sppb_bayar_nomor_bukti_kas) as bukti_kas_sppb"),
            DB::raw("MAX(sppb_isi.master_kode_kbb) as kbb_sppb"),
            DB::raw("MAX(rek_isi_sppb.master_rekening_kode_sap) as sap_sppb"),
            DB::raw("MAX(gl_sppb.master_gl_id) as gl_id_sppb"),
            DB::raw("MAX(gl_sppb.master_gl_kode) as gl_kode_sppb"),
            DB::raw("MAX(cc_sppb.master_cost_center_kode) as cc_kode_sppb"),
            // SPPn derived fields
            DB::raw("MAX(cf_sppn.master_cash_flow_kode) as cf_kode_sppn"),
            DB::raw("MAX(sppn_terima.sppn_terima_nomor_bukti_kas) as bukti_kas_sppn"),
            DB::raw("MAX(sppn_isi.master_kode_kbb) as kbb_sppn"),
            DB::raw("MAX(rek_isi_sppn.master_rekening_kode_sap) as sap_sppn"),
            DB::raw("MAX(gl_sppn.master_gl_id) as gl_id_sppn"),
            DB::raw("MAX(gl_sppn.master_gl_kode) as gl_kode_sppn"),
            DB::raw("MAX(cc_sppn.master_cost_center_kode) as cc_kode_sppn"),
            DB::raw("MAX(pc_sppn.master_profit_center_kode) as pc_kode_sppn")
        )
        ->groupBy('spp.spp_id')
        ->orderBy('spp.spp_tanggal', 'desc')
        ->get();

        $data = [];
        $sppbisi = [];
        $sppnisi = [];
        $sppb_bayar = [];
        $sppn_terima = [];
        $sum_spp = 0;

        foreach ($results as $index => $row) {
            $data[] = $row;
            
            $sppbisi[$index] = (object)[
                'master_cash_flow_kode' => $row->cf_kode_sppb,
                'master_kode_kbb' => $row->kbb_sppb,
                'master_rekening_kode_sap' => $row->sap_sppb,
                'master_gl_id' => $row->gl_id_sppb,
                'master_gl_kode' => $row->gl_kode_sppb,
                'master_cost_center_kode' => $row->cc_kode_sppb
            ];
            
            $sppnisi[$index] = (object)[
                'master_cash_flow_kode' => $row->cf_kode_sppn,
                'master_kode_kbb' => $row->kbb_sppn,
                'master_rekening_kode_sap' => $row->sap_sppn,
                'master_gl_id' => $row->gl_id_sppn,
                'master_gl_kode' => $row->gl_kode_sppn,
                'master_cost_center_kode' => $row->cc_kode_sppn,
                'master_profit_center_kode' => $row->pc_kode_sppn
            ];
            
            $sppb_bayar[$index] = (object)[
                'sppb_bayar_nomor_bukti_kas' => $row->bukti_kas_sppb
            ];
            
            $sppn_terima[$index] = (object)[
                'sppn_terima_nomor_bukti_kas' => $row->bukti_kas_sppn
            ];
            
            $sum_spp += ($row->sppb_total + $row->sppn_jumlah);
        }

        $rentang_waktu = $this->rentang_waktu;
        $karyawan_no_vendor_sppb = []; // API disabled
        $karyawan_no_vendor_sppn = []; // API disabled

        return view('page.laporan.laporan_export', compact('sum_spp', 'data', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'rentang_waktu', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn'));
    }

    public function title(): string
    {
        return $this->title;
    }

}
