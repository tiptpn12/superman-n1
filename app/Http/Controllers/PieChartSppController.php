<?php

namespace App\Http\Controllers;

use App\Company;
use App\Services\ChartService;
use App\Spp;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PieChartSppController extends Controller
{
    public function getDataSppTerbayarDanBelumTerbayar(Request $request)
    {
        $user_company = Session::get('company');
        $region_nama = '';
        $start_month = '';
        $end_month = '';

        if (!$request->start_month && !$request->end_month) {
            $start_month = Spp::where('company_id', '=', $user_company)->orderBy('spp_tanggal', 'asc')->first();
            if ($start_month) {
                $start_month = $start_month->spp_tanggal;
            } else {
                $start_month = '';
            }
            $end_month = Spp::where('company_id', '=', $user_company)->orderBy('spp_tanggal', 'desc')->first();
            if ($end_month) {
                $end_month = $end_month->spp_tanggal;
            } else {
                $end_month = '';
            }
        }

        // if ($user_company == 5) {
        //     $sppb_terbayar = Spp::where('sppb_id', '!=', null)
        //                     ->where('spp_status_bayar', '=', 1)
        //                     ->count();

        //     $sppb_belum_terbayar = Spp::where('sppb_id', '!=', null)
        //                         ->where('spp_status_bayar', '=', 0)
        //                         ->count();

        //     $sppn_terselesaikan = Spp::where('sppn_id', '!=', null)
        //                         ->where('spp_status_terima', '=', 1)
        //                         ->count();

        //     $sppn_belum_terselesaikan = Spp::where('sppn_id', '!=', null)
        //                         ->where('spp_status_terima', '=', 0)
        //                         ->count();
        // } else {
        $sppb_terbayar = Spp::where('sppb_id', '!=', null)
            ->where('spp_status_bayar', '=', 1)
            ->where('company_id', '=', $user_company);

        if ($request->start_month) {
            $sppb_terbayar->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }

        if ($request->end_month) {
            $sppb_terbayar->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }

        $sppb_terbayar = $sppb_terbayar->count();

        $sppb_belum_terbayar = Spp::where('sppb_id', '!=', null)
            ->where('spp_status_bayar', '=', 2)
            ->where('company_id', '=', $user_company);

        if ($request->start_month) {
            $sppb_belum_terbayar->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }

        if ($request->end_month) {
            $sppb_belum_terbayar->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }

        $sppb_belum_terbayar = $sppb_belum_terbayar->count();

        $sppn_terselesaikan = Spp::where('sppn_id', '!=', null)
            ->where('spp_status_terima', '=', 1)
            ->where('company_id', '=', $user_company);

        if ($request->start_month) {
            $sppn_terselesaikan->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }

        if ($request->end_month) {
            $sppn_terselesaikan->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }

        $sppn_terselesaikan = $sppn_terselesaikan->count();

        $sppn_belum_terselesaikan = Spp::where('sppn_id', '!=', null)
            ->where('spp_status_terima', '=', 2)
            ->where('company_id', '=', $user_company);

        if ($request->start_month) {
            $sppn_belum_terselesaikan->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }

        if ($request->end_month) {
            $sppn_belum_terselesaikan->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }

        $sppn_belum_terselesaikan = $sppn_belum_terselesaikan->count();

        $region_nama = Company::where('company_id', $user_company)->first()->company_nama;
        // }

        return response()->json([
            'sppb_terbayar' => $sppb_terbayar,
            'sppb_belum_terbayar' => $sppb_belum_terbayar,
            'sppn_terselesaikan' => $sppn_terselesaikan,
            'sppn_belum_terselesaikan' => $sppn_belum_terselesaikan,
            'region_nama' => explode('-', $region_nama)[1],
            'start' => $request->start_month ?? $this->convertToString($start_month),
            'end' => $request->end_month ?? $this->convertToString($end_month),
        ]);
    }

    public function convertToString(String $date)
    {
        if ($date == '') {
            return '';
        }

        $month = date('F', strtotime($date));
        $year = date('Y', strtotime($date));

        return $month . ' ' . $year;
    }

    public function convertToDateFormat(String $month, String $for)
    {
        if ($month == '') {
            return '';
        }

        if ($for == 'last') {
            return (new DateTime($month))->modify('last day of this month')->format('Y-m-d');
        } else {
            return (new DateTime($month))->format('Y-m-d');
        }
    }

    public function getStatusProses(ChartService $chartService)
    {
        $companyId = Session::get('company');
        $hakAkses = Session::get('hak_akses');

        try {
            $data = $chartService->getProses($companyId, $hakAkses);
            return response()->json([
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getPenerimaanPengeluaran(ChartService $chartService, Request $request)
    {
        $companyId = Session::get('company');
        $startMonth = $request->start_month;
        $endMonth = $request->end_month;

        try {
            $data = $chartService->getPenerimaanPengeluaran($companyId, $startMonth, $endMonth);
            return response()->json([
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }
}
