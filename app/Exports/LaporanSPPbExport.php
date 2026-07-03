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
        $grup_ui = session()->get('grup_ui');
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        $company_id = $this->company_id;

        $query = DB::table('spp')
            ->join('spp_proses', 'spp.spp_id', '=', 'spp_proses.spp_id')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppb_bayar', 'sppb.sppb_id', '=', 'sppb_bayar.sppb_id')
            ->leftJoin('master_rekening as rek_bayar', 'sppb_bayar.master_rekening_id', '=', 'rek_bayar.master_rekening_id')
            ->leftJoin('master_rekening as rek_isi', 'sppb_isi.master_kode_vendor_id', '=', 'rek_isi.master_rekening_id')
            ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
            ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
            ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
            ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
            ->leftJoin('master_vendor', 'sppb.master_bank_id', '=', 'master_vendor.master_vendor_id')
            ->where('spp.spp_status_ob', '!=', 2)
            ->whereNull('spp.sppn_id')
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
            $query->where('spp.spp_status_bayar', '=', $this->status_bayar);
        }

        // Access Control
        if ($grup_ui != 1) {
            if ($hak_akses >= 10 && $hak_akses <= 17) {
                $query->where('spp.sppd_posisi', '=', $hak_akses);
            } else {
                $query->where('spp.master_bagian_id', '=', $bagian);
            }
        } else {
            // Some original logic had $bagian filter even for grup_ui 1 in some cases, 
            // but let's follow the most common pattern in the file.
            $query->where('spp.master_bagian_id', '=', $bagian);
        }

        $results = $query->select(
            'spp.spp_id',
            'spp.sppb_id',
            'sppb.sppb_no',
            'sppb.sppb_tanggal',
            'sppb.sppb_total',
            'sppb.sppb_jenis',
            'sppb.sppb_metode_pembayaran',
            'sppb.sppb_data_metpen',
            'master_bagian.master_bagian_nama',
            'spp.spp_kabag',
            'spp_proses.spp_proses_id',
            'spp.spp_status_proses',
            'spp.spp_status_posisi',
            'spp.spp_status_ob',
            'spp.spp_status_bayar',
            DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
            DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian.sppb_uraian_uraian SEPARATOR ', ') as sppb_uraian2"),
            DB::raw("MAX(master_cash_flow.master_cash_flow_kode) as master_cash_flow_kode"),
            DB::raw("MAX(sppb_bayar.sppb_bayar_nomor_bukti_kas) as sppb_bayar_nomor_bukti_kas"),
            DB::raw("MAX(rek_isi.master_rekening_kode_sap) as master_rekening_kode_sap"),
            DB::raw("MAX(sppb_isi.master_kode_kbb) as master_kode_kbb"),
            DB::raw("MAX(master_gl.master_gl_kode) as master_gl_kode"),
            DB::raw("MAX(master_gl.master_gl_id) as master_gl_id"),
            DB::raw("MAX(master_cost_center.master_cost_center_kode) as master_cost_center_kode"),
            DB::raw("MAX(nama_karyawan.karyawan_no_rek) as karyawan_no_rek"),
            DB::raw("MAX(nama_karyawan.karyawan_nama) as karyawan_nama"),
            DB::raw("MAX(master_vendor.master_vendor_rekening) as master_bank_rekening"),
            DB::raw("MAX(master_vendor.master_vendor_nama) as master_bank_nama")
        )
        ->groupBy('spp.spp_id')
        ->orderBy('spp.spp_tanggal', 'desc')
        ->get();

        $data = [];
        $sppbisi = [];
        $sppb_bayar = [];
        $totalNominalSppb = 0;
        $karyawan_no_vendor_sppb = [];

        foreach ($results as $index => $row) {
            $data[] = $row;
            
            // Map to the existing expected structure for the view
            $sppbisi[$index] = (object)[
                'master_cash_flow_kode' => $row->master_cash_flow_kode,
                'master_kode_kbb' => $row->master_kode_kbb,
                'master_gl_id' => $row->master_gl_id,
                'master_gl_kode' => $row->master_gl_kode,
                'master_rekening_kode_sap' => $row->master_rekening_kode_sap,
                'master_cost_center_kode' => $row->master_cost_center_kode
            ];
            
            $sppb_bayar[$index] = (object)[
                'sppb_bayar_nomor_bukti_kas' => $row->sppb_bayar_nomor_bukti_kas
            ];
            
            $totalNominalSppb += $row->sppb_total;
            $karyawan_no_vendor_sppb[$index] = null; // API is disabled in original anyway
        }

        $rentang_waktu = $this->rentang_waktu;

        return view('page.laporan.laporan_sppb_export', compact('totalNominalSppb', 'data', 'sppbisi', 'sppb_bayar', 'rentang_waktu', 'karyawan_no_vendor_sppb'));
    }
    public function title(): string
    {
        return $this->title;
    }
}
