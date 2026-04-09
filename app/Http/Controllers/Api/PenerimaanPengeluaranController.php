<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanPengeluaranController extends Controller
{
    public function index()
    {
        $all_divisi = DB::table('master_bagian as mb')
            ->join('master_company as mc', 'mb.company_id', '=', 'mc.company_id')
            ->select('mb.master_bagian_id', 'mb.master_bagian_nama', 'mb.master_bagian_kode', 'mc.company_nama', 'mc.company_kode');

        //Get all sppb terbayar
        $sppb_terbayar = DB::table('sppb as sb')
            ->join('spp as s', 'sb.sppb_id', '=', 's.sppb_id')
            ->select('s.master_bagian_id', 's.company_id', 'sb.sppb_total', 's.spp_tanggal')
            ->whereIn('s.sppd_posisi', [38, 39])
            ->whereIn('s.sppd_status', [1, 2, 100]);

        //Get all sppn terbayar
        $sppn_terbayar = DB::table('sppn as sn')
            ->join('spp as s', 'sn.sppn_id', '=', 's.sppn_id')
            ->select('s.master_bagian_id', 's.company_id', 'sn.sppn_jumlah', 's.spp_tanggal')
            ->whereIn('s.sppd_posisi', [38, 39])
            ->whereIn('s.sppd_status', [1, 2, 100]);

        $results = DB::table(DB::raw('(' . $all_divisi->toSql() . ') as ad'))
            ->mergeBindings($all_divisi)
            ->leftJoin(DB::raw('(' . $sppb_terbayar->toSql() . ') as asp'), 'ad.master_bagian_id', '=', 'asp.master_bagian_id')
            ->mergeBindings($sppb_terbayar)
            ->leftJoin(DB::raw('(' . $sppn_terbayar->toSql() . ') as asn'), 'ad.master_bagian_id', '=', 'asn.master_bagian_id')
            ->mergeBindings($sppn_terbayar)
            ->select(
                'ad.company_kode',
                'ad.master_bagian_kode',
                DB::raw('COALESCE(SUM(DISTINCT asp.sppb_total), 0) as total_sppb'),
                DB::raw('COALESCE(SUM(DISTINCT asn.sppn_jumlah), 0) as total_sppn')
            )
            ->groupBy('ad.master_bagian_kode')
            ->orderBy('ad.company_kode')
            ->get();

        $data = [];
        foreach ($results as $key => $value) {
            $data[] = [
                'company_kode' => $value->company_kode,
                'master_bagian_kode' => $value->master_bagian_kode,
                'jenis' => 'sppb',
                'total' => $value->total_sppb,
            ];
            $data[] = [
                'company_kode' => $value->company_kode,
                'master_bagian_kode' => $value->master_bagian_kode,
                'jenis' => 'sppn',
                'total' => $value->total_sppn,
            ];
        }
        return response()->json([
            'data' => $data,
        ]);
    }
}
