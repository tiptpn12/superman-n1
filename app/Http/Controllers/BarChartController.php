<?php

namespace App\Http\Controllers;

use App\Bagian;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BarChartController extends Controller
{
    function getSppbdanSppnTerbayarPerDivisi(Request $request)
    {
        $tanggal_awal = $request->get('tanggal_awal', '2024-08');
        $tanggal_akhir = $request->get('tanggal_akhir', '2024-08');
        $user_company = Session::get("company", 5);

        Log::info("User company", [$user_company]);

        // Get all divisi
        $all_divisi = DB::table('master_bagian as mb')
            ->join('master_company as mc', 'mb.company_id', '=', 'mc.company_id')
            ->select('mb.master_bagian_id', 'mb.master_bagian_nama', 'mb.master_bagian_kode', 'mc.company_nama')
            ->where('mc.company_id', '=', $user_company);
        // ->where('mb.master_bagian_nama', 'LIKE', 'Divisi%');

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

        $sppb_terbayar->whereRaw("DATE_FORMAT(s.spp_tanggal, '%Y-%m') BETWEEN ? AND ?", [$tanggal_awal, $tanggal_akhir]);
        $sppn_terbayar->whereRaw("DATE_FORMAT(s.spp_tanggal, '%Y-%m') BETWEEN ? AND ?", [$tanggal_awal, $tanggal_akhir]);

        $results = DB::table(DB::raw('(' . $all_divisi->toSql() . ') as ad'))
            ->mergeBindings($all_divisi)
            ->leftJoin(DB::raw('(' . $sppb_terbayar->toSql() . ') as asp'), 'ad.master_bagian_id', '=', 'asp.master_bagian_id')
            ->mergeBindings($sppb_terbayar)
            ->leftJoin(DB::raw('(' . $sppn_terbayar->toSql() . ') as asn'), 'ad.master_bagian_id', '=', 'asn.master_bagian_id')
            ->mergeBindings($sppn_terbayar)
            ->select(
                'ad.master_bagian_nama',
                'ad.master_bagian_kode',
                DB::raw('COALESCE(SUM(DISTINCT asp.sppb_total), 0) as total_sppb'),
                DB::raw('COALESCE(SUM(DISTINCT asn.sppn_jumlah), 0) as total_sppn')
            )
            ->groupBy('ad.master_bagian_kode')
            ->get();

        return response()->json([
            'results' => $results,
            'tanggal_awal' => $tanggal_awal ?? null,
            'tanggal_akhir' => $tanggal_akhir ?? null,
        ]);
    }

    function getSppbdanSppnTerbayarPerRegional(Request $request)
    {
        $tanggal_awal = $request->tanggal_awal;
        $tanggal_akhir = $request->tanggal_akhir;
        $user_company = Session::get('company');
        $hak_akses = Session::get('hak_akses');

        Log::info('Ambil tanggal awal dan akhir', [$tanggal_awal, $tanggal_akhir]);

        $allRegional = DB::table('master_company')
            ->select('company_id', 'company_nama')
            ->where('company_nama', 'LIKE', '%Regional%');
        // ->orWhere('company_id','=', 5);

        if (!in_array($hak_akses, [1, 46])) {
            $allRegional->where('company_id', '=', $user_company);
        }
        Log::info('Ambil data regional', [$allRegional->get()]);

        $all_spp = DB::table('spp')
            ->select('spp_id', 'company_id', 'spp_tanggal')
            ->where('spp_status_bayar', 1)
            ->where('spp.sppd_posisi', 39)
            ->where('spp.sppd_status', 1);

        Log::debug('Ambil data spp');


        Log::debug("Cek tanggal awal dan akhir");

        $allSppb = DB::table('spp')
            ->join('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->select('spp.master_bagian_id', 'spp.company_id', 'sppb.sppb_total')
            ->whereIn('spp.sppd_posisi', [38, 39])
            ->whereIn('spp.sppd_status', [1, 2, 100]);

        $allSppn = DB::table('spp')
            ->join('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->select('spp.master_bagian_id', 'spp.company_id', 'sppn.sppn_jumlah')
            ->whereIn('spp.sppd_posisi', [38, 39])
            ->whereIn('spp.sppd_status', [1, 2, 100]);


        if ($tanggal_awal && $tanggal_awal) {
            $all_spp->whereRaw("DATE_FORMAT(spp_tanggal, '%Y-%m') BETWEEN ? AND ?", [$tanggal_awal, $tanggal_akhir]);
            $allSppb->whereRaw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') BETWEEN ? AND ?", [$tanggal_awal, $tanggal_akhir]);
            $allSppn->whereRaw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') BETWEEN ? AND ?", [$tanggal_awal, $tanggal_akhir]);

            Log::debug('Ambil spp berdasarkan filter');
        }

        $regionalData = DB::table(DB::raw('(' . $allRegional->toSql() . ') as ar'))
            ->leftJoin(DB::raw('(' . $allSppb->toSql() . ') as asp'), 'ar.company_id', '=', 'asp.company_id')
            ->leftJoin(DB::raw('(' . $allSppn->toSql() . ') as asn'), 'ar.company_id', '=', 'asn.company_id')
            ->mergeBindings($allRegional)
            ->mergeBindings($allSppb)
            ->mergeBindings($allSppn)
            ->select(
                'ar.company_nama as company',
                DB::raw('COALESCE(SUM(DISTINCT asp.sppb_total), 0) AS total_sppb'),
                DB::raw('COALESCE(SUM(DISTINCT asn.sppn_jumlah), 0) AS total_sppn')
            )
            ->groupBy('ar.company_nama');

        if (in_array($hak_akses, [1, 46])) {
            $hoData = DB::table(DB::raw('(' . $allSppb->toSql() . ') as asp'))
                ->mergeBindings($allSppb)
                ->select(
                    DB::raw("'HO' AS company"),
                    DB::raw('COALESCE(SUM(asp.sppb_total), 0) AS total_sppb'),
                    DB::raw('(SELECT COALESCE(SUM(asn2.sppn_jumlah), 0)
                          FROM (' . $allSppn->toSql() . ') as asn2
                          WHERE asn2.company_id = asp.company_id) AS total_sppn')
                )
                ->mergeBindings($allSppn)
                ->where('asp.company_id', 5);
            // $hoData = DB::table(DB::raw('(' . $allSppb->toSql() . ') as asp'))
            //     ->leftJoin(DB::raw('(' . $allSppn->toSql() . ') as asn'), 'asp.company_id', '=', 'asn.company_id')
            //     ->mergeBindings($allSppb)
            //     ->mergeBindings($allSppn)
            //     ->select(
            //         DB::raw("'HO' AS company"),
            //         DB::raw('COALESCE(SUM(asp.sppb_total), 0) AS total_sppb'),
            //         DB::raw('COALESCE(SUM(asn.sppn_jumlah), 0) AS total_sppn')
            //     )
            //     ->where('asp.company_id', 5);

            Log::debug("Get data dari database", [$hoData->get()]);

            // Combine both queries using `unionAll`.
            $finalQuery = $hoData->unionAll($regionalData)->get();
            $results = $finalQuery;
        } else {
            $results = $regionalData->get();
        }

        return response()->json([
            'results' => $results,
            'tanggal_awal' => $tanggal_awal ?? null,
            'tanggal_akhir' => $tanggal_akhir ?? null,
        ]);
    }
}
