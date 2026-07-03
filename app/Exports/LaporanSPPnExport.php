<?php

namespace App\Exports;

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

class LaporanSPPnExport implements FromView, ShouldAutoSize, WithTitle
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


    public function view(): View
    {
        $grup_ui = session()->get('grup_ui');
        $bagian = $this->bagian;
        $hak_akses = $this->hakakses;
        $company_id = $this->company_id;

        $query = DB::table('spp')
            ->join('spp_proses', 'spp.spp_id', '=', 'spp_proses.spp_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('sppn_terima', 'sppn.sppn_id', '=', 'sppn_terima.sppn_id')
            ->leftJoin('master_rekening as rek_terima', 'sppn_terima.master_rekening_id', '=', 'rek_terima.master_rekening_id')
            ->leftJoin('master_rekening as rek_isi', 'sppn_isi.master_kode_vendor_id', '=', 'rek_isi.master_rekening_id')
            ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
            ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
            ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
            ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
            ->where('spp.spp_status_ob', '!=', 2)
            ->whereNull('spp.sppb_id')
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
            $query->where('spp.spp_status_terima', '=', $this->status_bayar);
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
            'spp.sppn_id',
            'sppn.sppn_no',
            'sppn.sppn_tanggal',
            'sppn.sppn_jumlah',
            'sppn.sppn_jenis',
            'master_bagian.master_bagian_nama',
            'spp.spp_kabag',
            'spp.spp_status_proses',
            'spp.spp_status_posisi',
            'spp.spp_status_ob',
            'spp.spp_status_terima',
            DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
            DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian.sppn_uraian_uraian SEPARATOR ', ') as sppn_uraian2"),
            DB::raw("MAX(master_cash_flow.master_cash_flow_kode) as master_cash_flow_kode"),
            DB::raw("MAX(sppn_terima.sppn_terima_nomor_bukti_kas) as sppn_terima_nomor_bukti_kas"),
            DB::raw("MAX(rek_isi.master_rekening_kode_sap) as master_rekening_kode_sap"),
            DB::raw("MAX(sppn_isi.master_kode_kbb) as master_kode_kbb"),
            DB::raw("MAX(master_gl.master_gl_kode) as master_gl_kode"),
            DB::raw("MAX(master_gl.master_gl_id) as master_gl_id"),
            DB::raw("MAX(master_cost_center.master_cost_center_kode) as master_cost_center_kode"),
            DB::raw("MAX(master_profit_center.master_profit_center_kode) as master_profit_center_kode")
        )
        ->groupBy('spp.spp_id')
        ->orderBy('spp.spp_tanggal', 'desc')
        ->get();

        $data = [];
        $sppnisi = [];
        $sppn_terima = [];
        $totalNominalSppn = 0;
        $karyawan_no_vendor_sppn = [];

        foreach ($results as $index => $row) {
            $data[] = $row;
            
            $sppnisi[$index] = (object)[
                'master_cash_flow_kode' => $row->master_cash_flow_kode,
                'master_rekening_kode_kbb' => $row->master_kode_kbb,
                'master_gl_id' => $row->master_gl_id,
                'master_gl_kode' => $row->master_gl_kode,
                'master_rekening_kode_sap' => $row->master_rekening_kode_sap,
                'master_cost_center_kode' => $row->master_cost_center_kode,
                'master_profit_center_kode' => $row->master_profit_center_kode
            ];
            
            $sppn_terima[$index] = (object)[
                'sppn_terima_nomor_bukti_kas' => $row->sppn_terima_nomor_bukti_kas
            ];
            
            $totalNominalSppn += $row->sppn_jumlah;
            $karyawan_no_vendor_sppn[$index] = null;
        }

        $rentang_waktu = $this->rentang_waktu;

        return view('page.laporan.laporan_sppn_export', compact('totalNominalSppn', 'data', 'sppnisi', 'sppn_terima', 'rentang_waktu', 'karyawan_no_vendor_sppn'));
    }

    public function title(): string
    {
        return $this->title;
    }
}
