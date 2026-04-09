<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sppb;
use App\Sppn;
use App\Spp;
use App\Flow;
use App\IsiSppb;
use App\Spp_revisi;
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
use DB;
use PDF;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use App\FakturPajak;
use Illuminate\Routing\Redirector;
use App\GL;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportPembayaran;
use Illuminate\Support\Facades\DB as FacadesDB;
use Yajra\DataTables\Facades\DataTables;

class Pembayaran extends Controller
{
    
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            //dd($this->user);
            //return $next($request);
            if ($this->user == null) {

                return redirect('login');
            } else {
                return $next($request);
            }
        });
    }

    public function export_pdf(Request $request)
    {
        $akses = Session::get('hak_akses');
        $flow = DB::table('master_flow_detail')->where('flow_detail_urutan', $akses)->select('flow_id')->first();
        $ids = $request->ids;
        $status = $request->status;
        if ($status == 0) {
            $data_sudah = $data = DB::table('spp')->where('spp.spp_bukti_kas_bank', '=', NULL)
                ->where('spp.flow_id', $flow->flow_id)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc');
        } else {
            $data_sudah = $data = DB::table('spp')
                ->where('spp.spp_bukti_kas_bank', '!=', NULL)
                ->where('spp.flow_id', $flow->flow_id)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
                ->orderBy('spp_tanggal', 'desc');
        }

        $getdata = $data_sudah->get();
        $totalNominalSppb = 0;
        $totalNominalSppn = 0;
        foreach ($getdata as $key => $value) {
            $totalNominalSppb += $value->sppb_total;
        }
        foreach ($getdata as $key => $value) {
            $totalNominalSppn += $value->sppn_jumlah;
        }
        $sum_spp = $totalNominalSppb + $totalNominalSppn;

        $pdf = \PDF::loadView('page.pembayaran.pembayaran_pdf', compact('ids', 'getdata', 'totalNominalSppn', 'totalNominalSppb', 'sum_spp'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('ReportPembayaran.pdf');
    }
    public function export_pdf_terpilih(Request $request)
    {
        $akses = Session::get('hak_akses');
        // $master_flow = Flow::where('flow_id', 1)->first();
        $flow = DB::table('master_flow_detail')->where('flow_detail_urutan', $akses)->select('flow_id')->first();
        $ids = $request->ids;
        $status = $request->status;
        $arrayIDs = (array_map('intval', explode(',', $ids[0])));
        $data = DB::table('spp')->whereIn('spp.spp_id', $arrayIDs)
            ->where('spp.flow_id', $flow->flow_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
            ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
            ->orderBy('spp_tanggal', 'desc');
        $getdata = $data->get();
        // dd($getdata);
        $totalNominalSppb = 0;
        $totalNominalSppn = 0;
        foreach ($getdata as $key => $value) {
            $totalNominalSppb += $value->sppb_total;
        }
        foreach ($getdata as $key => $value) {
            $totalNominalSppn += $value->sppn_jumlah;
        }
        $sum_spp = $totalNominalSppb + $totalNominalSppn;

        $pdf = \PDF::loadView('page.pembayaran.pembayaran_pdf', compact('ids', 'getdata', 'totalNominalSppn', 'totalNominalSppb'));
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('ReportPembayaran.pdf');
        // dd($getdata);
    }
    public function export_excel(Request $request)
    {
        $akses = Session::get('hak_akses');
        // $master_flow = Flow::where('flow_id', 1)->first();
        $flow = DB::table('master_flow_detail')->where('flow_detail_urutan', $akses)->select('flow_id')->first();
        $ids = $request->ids;
        $status = $request->status;
        $data_sudah = DB::table('spp')->whereNotNull('spp_bukti_kas_bank')->where('spp_status_bayar', '=', '0')->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('sppb_bukti_kas', 'spp.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
            ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
            ->select('spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'karyawan_nama', 'sppb.sppb_metode_pembayaran', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
            ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
            ->orderBy('spp_tanggal', 'desc');
        $getdata = $data_sudah->get();
        // dd($getdata);
        return Excel::download(new ExportPembayaran($ids, $status == 0 ? false : true, $flow->flow_id), 'ReportPembayaran.xlsx');
    }
    public function export_excel_terpilih(Request $request)
    {
        $akses = Session::get('hak_akses');
        // $master_flow = Flow::where('flow_id', 1)->first();
        $flow = DB::table('master_flow_detail')->where('flow_detail_urutan', $akses)->select('flow_id')->first();
        $ids = $request->ids;
        $status = $request->status;
        $arrayIDs = (array_map('intval', explode(',', $ids[0])));

        $data = DB::table('spp')->where('spp.spp_bukti_kas_bank', '!=', NULL)->where('spp.flow_id', $flow->flow_id)->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('nama_karyawan', 'sppb.sppb_id', '=', 'nama_karyawan.sppb_id')
            ->select('spp_tanggal', 'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'karyawan_nama', 'sppb.sppb_metode_pembayaran', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
            ->groupBy('spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima', 'spp.sppn_id', 'spp_status_proses', 'spp_status_bayar', 'spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob')
            ->orderBy('spp_tanggal', 'desc');
        $getdata = $data->get();
        // dd($data->get());
        return Excel::download(new ExportPembayaran($arrayIDs, $status == 0 ? false : true, $flow->flow_id), 'ReportPembyaran.xlsx');
    }

   public function index(Request $request)
    {
        return view('page.pembayaran.pembayaran', [
            'b' => Bagian::whereNotIn('master_bagian_id', [10, 2])->get(),
            'vendor' => Vendor::all(),
            'm_flows' => $this->getMasterFlows(Session::get('hak_akses')),
        ]);
    }

    private function getMasterFlows($akses)
    {
        $flow = DB::table('master_flow_detail')
            ->where('flow_detail_urutan', $akses)
            ->select('flow_id')
            ->first();

        if (!$flow) {
            return collect();
        }

        return DB::table('master_flow_detail')
            ->where('flow_id', $flow->flow_id)
            ->leftJoin('master_hak_akses', 'master_flow_detail.flow_detail_urutan', '=', 'master_hak_akses.master_hak_akses_id')
            ->get();
    }

    private function getFlowFromSession()
    {
        $flow = DB::table('master_flow_detail')
            ->where('company_id', Session::get('company'))
            ->where('flow_detail_urutan', Session::get('hak_akses'))
            ->leftJoin('master_company_detail', 'master_flow_detail.flow_id', '=', 'master_company_detail.flow_id')
            ->pluck('master_flow_detail.flow_id');

        if ($flow->isEmpty()) {
            abort(400, 'Flow ID tidak ditemukan untuk hak akses ini.');
        }

        return $flow;
    }

    private function baseQuerySpp($uploaded, $flow)
    {
        $query = DB::table('spp')
            ->whereIn('spp.flow_id', $flow)
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->select(
                'spp_id', 'spp.sppb_id', 'sppd_posisi', 'spp.sppn_id', 'sppd_revisi', 'sppd_status',
                'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama', 'spp_status_posisi', 'spp_status_bayar',
                'spp_status_terima', 'spp_status_proses', 'spp_status_ob',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb_no', 'sppb_tanggal', 'sppb_total',
                'sppn_no', 'sppn_tanggal', 'sppn_jumlah',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ', ') as sppb_uraian"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ', ') as sppn_uraian")
            )
            ->groupBy(
                'spp_id', 'spp.sppb_id', 'spp_status_posisi', 'spp_status_bayar', 'spp_status_terima',
                'spp.sppn_id', 'spp_status_proses', 'spp_tanggal', 'sppb_no', 'sppb_tanggal',
                'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'spp_status_ob', 'sppd_status', 
                'sppd_posisi', 'sppd_revisi', 'spp_bukti_kas_bank', 'master_bagian.master_bagian_nama'
            )
            ->orderBy('spp_tanggal', 'desc');

        if ($uploaded) {
            $query->whereNotNull('spp.spp_bukti_kas_bank');
        } else {
            $query->whereNull('spp.spp_bukti_kas_bank');
        }

        return $query;
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('rentang_waktu')) {
            $dates = explode(' - ', $request->rentang_waktu);
            $query->whereBetween('spp_tanggal', [Carbon::parse($dates[0]), Carbon::parse($dates[1])]);
        }
        if ($request->filled('bagian') && $request->bagian !== 'semua') {
            $query->where('spp.master_bagian_id', $request->bagian);
        }
        if ($request->filled('vendor') && $request->vendor !== 'semua') {
            $query->where(function($q) use ($request) {
                $q->where('sppb.master_bank_id', $request->vendor)
                ->orWhere('sppn.master_bank_id', $request->vendor);
            });
        }
        if ($request->filled('posisi_terkini') && $request->posisi_terkini !== 'semua') {
            if ($request->posisi_terkini == '100') {
                $query->where('sppd_status', 100);
            } else {
                $query->where('sppd_posisi', $request->posisi_terkini)
                    ->where('sppd_status', '!=', 100);
            }
        }
        if ($request->filled('status_bayar') && $request->status_bayar !== 'semua') {
            $query->where('spp_status_bayar', $request->status_bayar);
        }

        return $query;
    }

    private function getDataTableResponse($query)
    {
        $hakAksesList = DB::table('master_hak_akses')->pluck('master_hak_akses_nama', 'master_hak_akses_id');
        
        return DataTables::of($query)
            ->addColumn('posisi_dinamis', function($row) use ($hakAksesList) {
                return $hakAksesList[$row->sppd_posisi] ?? null;
            })
            ->addColumn('status_pembayaran', function($row) {
                if ($row->spp_status_bayar == 1) {
                    return '<span class="badge badge-success">Sudah Dibayar</span>';
                } elseif ($row->spp_status_bayar == 0) {
                    return '<span class="badge badge-warning">Belum Dibayar</span>';
                } else {
                    return '<span class="badge badge-secondary">Tidak Diketahui</span>';
                }
            })
            ->rawColumns(['status_pembayaran'])

            ->filterColumn('sppb_uraian', function($query, $keyword) {
                $query->where('sppb_uraian.sppb_uraian_uraian', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('sppn_uraian', function($query, $keyword) {
                $query->where('sppn_uraian.sppn_uraian_uraian', 'LIKE', "%{$keyword}%");
            })

            ->make(true);
    }

    public function getDataBelumUpload(Request $request)
    {
        $query = $this->baseQuerySpp(false, $this->getFlowFromSession());
        $query = $this->applyFilters($query, $request);
        return $this->getDataTableResponse($query);
    }

    public function getDataSudahUpload(Request $request)
    {
        $query = $this->baseQuerySpp(true, $this->getFlowFromSession());
        $query = $this->applyFilters($query, $request);
        return $this->getDataTableResponse($query);
    }
}
