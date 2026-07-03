<?php

namespace App\Http\Controllers;
use App\Spp;
use App\Sppb;
use App\Sppn;
use App\SppbBayar;
use App\SppnTerima;
use App\SppProses;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class LaporanController extends Controller
{
    function __construct(){
        $this->middleware(function ($request,$next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            //dd($this->user);
            //return $next($request);
            if($this->user == null){

                return redirect('login');

            }
            else{
                return $next($request);
            }
        });


    }
    public function index()
    {
        $karyawan_all = null;

        // Fetch basic SPP records that have passed through the kas_dan_bank process
        $spp_base = DB::table('spp_proses')
            ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', 1)
            ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
            ->where('spp.spp_status_ob', '!=', 2)
            ->select('spp.*')
            ->get();

        $c_sppb = $spp_base->whereNotNull('sppb_id')->whereNull('sppn_id')->count();
        $c_sppn = $spp_base->whereNull('sppb_id')->whereNotNull('sppn_id')->count();
        $c_spp = $spp_base->whereNotNull('sppb_id')->whereNotNull('sppn_id')->count();

        $spp_ids = $spp_base->pluck('spp_id')->toArray();
        $sppb_ids = $spp_base->whereNotNull('sppb_id')->pluck('sppb_id')->unique()->toArray();
        $sppn_ids = $spp_base->whereNotNull('sppn_id')->pluck('sppn_id')->unique()->toArray();

        // Bulk fetch SPPb data with uraian
        $sppb_data_map = [];
        if (!empty($sppb_ids)) {
            $sppb_details = DB::table('spp')
                ->whereIn('spp.spp_id', $spp_ids)
                ->whereNotNull('spp.sppb_id')
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select(
                    'spp.spp_id',
                    'spp.sppb_id',
                    'spp.spp_status_bayar',
                    'sppb.sppb_no',
                    'sppb.sppb_tanggal',
                    'sppb.sppb_total',
                    'sppb.sppb_jenis',
                    'sppb.sppb_metode_pembayaran',
                    'master_bagian.master_bagian_nama',
                    'spp.spp_kabag',
                    'spp.spp_status_proses',
                    'spp.spp_status_posisi',
                    'spp.spp_status_ob',
                    'sppb_isi.sppb_isi_id',
                    'sppb_isi.master_kode_vendor_id as rekening_sppb',
                    'sppb_isi.master_gl_id as gl_sppb',
                    'sppb_isi.master_cost_center_id as cost_center_sppb',
                    'sppb_isi.master_cash_flow_id as cash_flow_sppb',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                    'spp.spp_tanggal'
                )
                ->groupBy('spp.spp_id')
                ->get()
                ->keyBy('spp_id');
            $sppb_data_map = $sppb_details->toArray();
        }

        // Bulk fetch SPPn data with uraian
        $sppn_data_map = [];
        if (!empty($sppn_ids)) {
            $sppn_details = DB::table('spp')
                ->whereIn('spp.spp_id', $spp_ids)
                ->whereNotNull('spp.sppn_id')
                ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select(
                    'spp.spp_id',
                    'spp.sppn_id',
                    'spp.spp_status_terima',
                    'sppn.sppn_no',
                    'sppn.sppn_tanggal',
                    'sppn.sppn_jumlah',
                    'sppn.sppn_jenis',
                    'sppn.sppn_metode_pembayaran',
                    'master_bagian.master_bagian_nama',
                    'spp.spp_kabag',
                    'spp.spp_status_proses',
                    'spp.spp_status_posisi',
                    'spp.spp_status_ob',
                    'sppn_isi.sppn_isi_id',
                    'sppn_isi.master_kode_vendor_id as rekening_sppn',
                    'sppn_isi.master_gl_id as gl_sppn',
                    'sppn_isi.master_cost_center_id as cost_center_sppn',
                    'sppn_isi.master_profit_center_id as profit_center_sppn',
                    'sppn_isi.master_cash_flow_id as cash_flow_sppn',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                    DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                    'spp.spp_tanggal'
                )
                ->groupBy('spp.spp_id')
                ->get()
                ->keyBy('spp_id');
            $sppn_data_map = $sppn_details->toArray();
        }

        // Combine and Sort data
        $data = [];
        foreach ($spp_base as $s) {
            if (isset($sppb_data_map[$s->spp_id])) {
                $data[] = $sppb_data_map[$s->spp_id];
            }
            if (isset($sppn_data_map[$s->spp_id])) {
                $data[] = $sppn_data_map[$s->spp_id];
            }
        }
        $data = collect($data)->sortByDesc('spp_tanggal')->values()->toArray();

        // Bulk fetch related master data for SPPb Isi and SPPn Isi
        $sppb_isi_ids = collect($data)->pluck('sppb_isi_id')->filter()->unique()->toArray();
        $sppn_isi_ids = collect($data)->pluck('sppn_isi_id')->filter()->unique()->toArray();

        $sppbisi_details = [];
        if (!empty($sppb_isi_ids)) {
            $sppbisi_details = DB::table('sppb_isi')
                ->whereIn('sppb_isi_id', $sppb_isi_ids)
                ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')
                ->get()
                ->keyBy('sppb_isi_id');
        }

        $sppnisi_details = [];
        if (!empty($sppn_isi_ids)) {
            $sppn_ids_from_isi = collect($data)->whereNotNull('sppn_isi_id')->pluck('sppn_id')->unique()->toArray();
            $sppnisi_details = DB::table('sppn_isi')
                ->whereIn('sppn_id', $sppn_ids_from_isi)
                ->leftJoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')
                ->get()
                ->keyBy('sppn_id');
        }

        // Bulk fetch Bayar/Terima info
        $sppb_bayar_details = [];
        if (!empty($sppb_ids)) {
            $sppb_bayar_details = DB::table('sppb_bayar')
                ->whereIn('sppb_id', $sppb_ids)
                ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('master_rekening.*', 'sppb_bayar.*')
                ->get()
                ->keyBy('sppb_id');
        }

        $sppn_terima_details = [];
        if (!empty($sppn_ids)) {
            $sppn_terima_details = DB::table('sppn_terima')
                ->whereIn('sppn_id', $sppn_ids)
                ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('master_rekening.*', 'sppn_terima.*')
                ->get()
                ->keyBy('sppn_id');
        }

        // Final mapping
        $sppbisi = [];
        $sppnisi = [];
        $sppb_bayar = [];
        $sppn_terima = [];
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];

        foreach ($data as $k => $v) {
            $v_obj = (object)$v;
            if (isset($v_obj->sppb_isi_id) && isset($sppbisi_details[$v_obj->sppb_isi_id])) {
                $sppbisi[$k] = $sppbisi_details[$v_obj->sppb_isi_id];
            }
            if (isset($v_obj->sppn_id) && isset($sppnisi_details[$v_obj->sppn_id])) {
                $sppnisi[$k] = $sppnisi_details[$v_obj->sppn_id];
            }
            if (isset($v_obj->sppb_id) && isset($sppb_bayar_details[$v_obj->sppb_id])) {
                $sppb_bayar[$k] = $sppb_bayar_details[$v_obj->sppb_id];
            }
            if (isset($v_obj->sppn_id) && isset($sppn_terima_details[$v_obj->sppn_id])) {
                $sppn_terima[$k] = $sppn_terima_details[$v_obj->sppn_id];
            }
            $karyawan_no_vendor_sppb[$k] = null;
            $karyawan_no_vendor_sppn[$k] = null;
        }

        return view('page.laporan.laporan', compact('data', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn', 'c_sppb', 'c_sppn', 'c_spp'));
    }


    public function advanced_search(Request $request)
    {
        $karyawan_all = null;

        $rentang_waktu_raw = $request->rentang_waktu;
        $rentang_waktu = explode(" - ", $rentang_waktu_raw);
        $rentang_waktu = collect($rentang_waktu)->map(function ($item, $key) {
            return date('Y-m-d', strtotime($item));
        })->all();
        $jenis_spp = $request->jenis_spp;
        $jenis_report = $request->jenis_report;
        $export_tipe = $request->export_tipe;

        // Base Query
        $spp_query = DB::table('spp_proses')
            ->where('spp_proses.spp_proses_petugas_kas_dan_bank', '=', 1)
            ->join('spp', 'spp_proses.spp_id', '=', 'spp.spp_id')
            ->where('spp.spp_status_ob', '!=', 2);

        if ($request->rentang_waktu !== null) {
            $spp_query->whereBetween(DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d')"), $rentang_waktu);
        }

        if ($jenis_spp == "spp_biasa") {
            $spp_query->where('spp.master_bagian_id', '!=', 2);
        } else if ($jenis_spp == "spp_khusus") {
            $spp_query->where('spp.master_bagian_id', '=', 2);
        }

        // Apply Report Type Filter
        if ($jenis_report == "simple") {
             $spp_query->where('spp.master_bagian_id', '!=', 2);
        } else if ($jenis_report == "khusus") {
             $spp_query->where('spp.master_bagian_id', '=', 2);
        }

        $spp_base = $spp_query->select('spp.*')->get();

        $c_sppb = $spp_query->whereNotNull('sppb_id')->whereNull('sppn_id')->count();
        $c_sppn = $spp_query->whereNull('sppb_id')->whereNotNull('sppn_id')->count();
        $c_spp = $spp_query->whereNotNull('sppb_id')->whereNotNull('sppn_id')->count();

        $status_bayar = $request->status_bayar;
        $spp_ids = $spp_base->pluck('spp_id')->toArray();

        // Details mapping
        $sppb_details = [];
        if (!empty($spp_ids)) {
            $sppb_query_details = DB::table('spp')
                ->whereIn('spp.spp_id', $spp_ids)
                ->whereNotNull('spp.sppb_id')
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
                ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select(
                    'spp.spp_id',
                    'spp.sppb_id',
                    'sppb.sppb_jenis',
                    'spp.spp_status_lunas',
                    'spp.spp_status_bayar',
                    'sppb_isi.sppb_isi_id',
                    'master_bagian_nama',
                    'spp_kabag',
                    'spp_status_proses',
                    'spp_status_posisi',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),
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
                ->groupBy('spp.spp_id');
            if ($status_bayar !== "semua") {
                $sppb_query_details->where('spp.spp_status_bayar', '=', $status_bayar);
            }
            $sppb_details = $sppb_query_details->get()->keyBy('spp_id')->toArray();
        }

        $sppn_details = [];
        if (!empty($spp_ids)) {
            $sppn_query_details = DB::table('spp')
                ->whereIn('spp.spp_id', $spp_ids)
                ->whereNotNull('spp.sppn_id')
                ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
                ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->select(
                    'spp_id',
                    'spp.spp_status_lunas',
                    'sppn.sppn_jenis',
                    'spp.spp_status_terima',
                    'sppn_isi.sppn_isi_id',
                    'spp.sppn_id',
                    'master_bagian_nama',
                    'spp_kabag',
                    'spp_status_proses',
                    'spp_status_posisi',
                    DB::raw("DATE_FORMAT(spp.spp_tanggal,'%Y-%m-%d') as tanggal"),
                    'sppn_no',
                    'sppn_tanggal',
                    'sppn_jumlah',
                    'spp_status_ob',
                    DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"),
                    'sppn_isi.master_kode_vendor_id as rekening_sppn',
                    'sppn_isi.master_gl_id as gl_sppn',
                    'sppn_isi.master_cost_center_id as cost_center_sppn',
                    'sppn_isi.master_profit_center_id as profit_center_sppn',
                    'sppn_isi.master_cash_flow_id as cash_flow_sppn'
                )
                ->groupBy('spp.spp_id');
            if ($status_bayar !== "semua") {
                $sppn_query_details->where('spp.spp_status_terima', '=', $status_bayar);
            }
            $sppn_details = $sppn_query_details->get()->keyBy('spp_id')->toArray();
        }

        $data = [];
        foreach ($spp_base as $s) {
            if (isset($sppb_details[$s->spp_id])) {
                $data[] = $sppb_details[$s->spp_id];
            }
            if (isset($sppn_details[$s->spp_id])) {
                $data[] = $sppn_details[$s->spp_id];
            }
        }

        $sppb_ids = collect($data)->pluck('sppb_id')->filter()->unique()->toArray();
        $sppn_ids = collect($data)->pluck('sppn_id')->filter()->unique()->toArray();
        $sppb_isi_ids = collect($data)->pluck('sppb_isi_id')->filter()->unique()->toArray();
        $sppn_isi_ids = collect($data)->pluck('sppn_isi_id')->filter()->unique()->toArray();

        // Related Master Data
        $sppbisi_details = [];
        if (!empty($sppb_isi_ids)) {
            $sppbisi_details = DB::table('sppb_isi')
                ->whereIn('sppb_isi_id', $sppb_isi_ids)
                ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppb_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')
                ->get()
                ->keyBy('sppb_isi_id');
        }

        $sppnisi_details = [];
        if (!empty($sppn_ids)) {
            $sppnisi_details = DB::table('sppn_isi')
                ->whereIn('sppn_id', $sppn_ids)
                ->leftJoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                ->select('sppn_isi.*', 'master_rekening.*', 'master_gl.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*')
                ->get()
                ->keyBy('sppn_id');
        }

        $sppb_bayar_details = [];
        if (!empty($sppb_ids)) {
            $sppb_bayar_details = DB::table('sppb_bayar')
                ->whereIn('sppb_id', $sppb_ids)
                ->join('master_rekening', 'sppb_bayar.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('master_rekening.*', 'sppb_bayar.*')
                ->get()
                ->keyBy('sppb_id');
        }

        $sppn_terima_details = [];
        if (!empty($sppn_ids)) {
            $sppn_terima_details = DB::table('sppn_terima')
                ->whereIn('sppn_id', $sppn_ids)
                ->join('master_rekening', 'sppn_terima.master_rekening_id', '=', 'master_rekening.master_rekening_id')
                ->select('master_rekening.*', 'sppn_terima.*')
                ->get()
                ->keyBy('sppn_id');
        }

        $sppbisi = [];
        $sppnisi = [];
        $sppb_bayar = [];
        $sppn_terima = [];
        $karyawan_no_vendor_sppb = [];
        $karyawan_no_vendor_sppn = [];

        foreach ($data as $k => $v) {
            $v_obj = (object)$v;
            if (isset($v_obj->sppb_isi_id) && isset($sppbisi_details[$v_obj->sppb_isi_id])) {
                $sppbisi[$k] = $sppbisi_details[$v_obj->sppb_isi_id];
            }
            if (isset($v_obj->sppn_id) && isset($sppnisi_details[$v_obj->sppn_id])) {
                $sppnisi[$k] = $sppnisi_details[$v_obj->sppn_id];
            }
            if (isset($v_obj->sppb_id) && isset($sppb_bayar_details[$v_obj->sppb_id])) {
                $sppb_bayar[$k] = $sppb_bayar_details[$v_obj->sppb_id];
            }
            if (isset($v_obj->sppn_id) && isset($sppn_terima_details[$v_obj->sppn_id])) {
                $sppn_terima[$k] = $sppn_terima_details[$v_obj->sppn_id];
            }
            $karyawan_no_vendor_sppb[$k] = null;
            $karyawan_no_vendor_sppn[$k] = null;
        }

        return view('page.laporan.laporan', compact('data', 'sppbisi', 'sppnisi', 'sppb_bayar', 'sppn_terima', 'status_bayar', 'rentang_waktu_raw', 'karyawan_no_vendor_sppb', 'karyawan_no_vendor_sppn', 'jenis_spp', 'jenis_report', 'export_tipe', 'c_spp', 'c_sppb', 'c_sppn'));
    }


}
