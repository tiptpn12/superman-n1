<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChartController extends Controller
{
    public function terimaKeluar()
    {
        try {
            $totalPembayaran = DB::table('spp')
                ->join('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->whereNotNull('spp.sppb_id')
                ->where('spp.company_id', 5)
                ->whereIn('spp.sppd_posisi', [38, 39])
                ->whereIn('spp.sppd_status', [1, 2, 100])
                ->sum('sppb.sppb_total');


            $totalPenerimaan = DB::table('spp')
                ->join('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->whereNotNull('spp.sppn_id')
                ->where('spp.company_id', 5)
                ->whereIn('spp.sppd_posisi', [38, 39])
                ->whereIn('spp.sppd_status', [1, 2, 100])
                ->sum('sppn.sppn_jumlah');

            return response()->json([
                'status' => 'success',
                'message' => 'Success get data penerimaan pengeluaran',
                'data' => [
                    'nama_region' => 'Head Office',
                    'total_pembayaran' => $totalPembayaran,
                    'total_penerimaan' => $totalPenerimaan
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => [
                        $th->getMessage()
                    ],
                ],
            ], 500);
        }
    }

    function getSppbdanSppnTerbayarPerRegional()
    {
        try {
            $user_company = 5;
            $hak_akses = 1;

            $allRegional = DB::table('master_company')
                ->where('company_nama', 'LIKE', '%Regional%')
                ->select('company_id', 'company_kode');
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

            $regionalData = DB::table(DB::raw('(' . $allRegional->toSql() . ') as ar'))
                ->leftJoin(DB::raw('(' . $allSppb->toSql() . ') as asp'), 'ar.company_id', '=', 'asp.company_id')
                ->leftJoin(DB::raw('(' . $allSppn->toSql() . ') as asn'), 'ar.company_id', '=', 'asn.company_id')
                ->mergeBindings($allRegional)
                ->mergeBindings($allSppb)
                ->mergeBindings($allSppn)
                ->select(
                    'ar.company_kode as company',
                    DB::raw('COALESCE(SUM(DISTINCT asp.sppb_total), 0) AS total_sppb'),
                    DB::raw('COALESCE(SUM(DISTINCT asn.sppn_jumlah), 0) AS total_sppn')
                )
                ->groupBy('ar.company_kode');

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

            $transformedResults = [];

            foreach ($results as $result) {
                $transformedResults[] = [
                    "company" => $result->company,
                    "jenis" => "Cash Out",
                    "total" => $result->total_sppb
                ];
                $transformedResults[] = [
                    "company" => $result->company,
                    "jenis" => "Cash In",
                    "total" => $result->total_sppn
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success get data Sppb dan Sppn Terbayar Per Regional',
                'data' => $transformedResults,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => [
                        $th->getMessage()
                    ],
                ],
            ], 500);
        }
    }

    public function getSppbAllCompany()
    {
        $currentYear = date('Y');
        $sppb = DB::table('spp')
            ->join('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->join('master_company as company', 'spp.company_id', '=', 'company.company_id')
            ->select(
                'company.company_kode',
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') as bulan"),
                DB::raw('SUM(sppb.sppb_total) as sppb_total')
            )
            ->whereYear('spp.spp_tanggal', $currentYear)
            ->groupBy('company.company_kode', 'bulan')
            ->orderBy('company.company_kode')
            ->orderBy('bulan')
            ->get();

        $bulanIndonesia = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $companyData = [];
        foreach ($sppb->groupBy('company_kode') as $companyKode => $dataPerCompany) {
            $companyData[$companyKode] = [];
            foreach ($bulanIndonesia as $monthNumber => $monthName) {
                // Cek apakah bulan ini ada dalam data
                $dataBulan = $dataPerCompany->firstWhere('bulan', "$currentYear-$monthNumber");

                $companyData[$companyKode][] = [
                    'company_kode' => $companyKode,
                    'bulan' => $monthName,
                    'sppb_total' => $dataBulan ? $dataBulan->sppb_total : 0,
                ];
            }
        }

        // Menggabungkan data menjadi satu array
        $flattenedData = [];
        foreach ($companyData as $companyRows) {
            foreach ($companyRows as $row) {
                $flattenedData[] = $row;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success get data sppb all company',
            'data' => $flattenedData,
        ]);
    }
    public function getSppnAllCompany()
    {
        $currentYear = Date('Y');
        $sppn = DB::table('spp')
            ->join('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->join('master_company as company', 'spp.company_id', '=', 'company.company_id')
            ->select(
                'company.company_kode',
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') as bulan"),
                DB::raw('SUM(sppn.sppn_jumlah) as sppn_jumlah')
            )
            ->whereYear('spp.spp_tanggal', $currentYear)
            ->groupBy('company.company_kode', 'bulan')
            ->orderBy('company.company_kode')
            ->orderBy('bulan')
            ->get();

        $bulanIndonesia = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $companyData = [];
        foreach ($sppn->groupBy('company_kode') as $companyKode => $dataPerCompany) {
            $companyData[$companyKode] = [];
            foreach ($bulanIndonesia as $monthNumber => $monthName) {
                // Cek apakah bulan ini ada dalam data
                $dataBulan = $dataPerCompany->firstWhere('bulan', "$currentYear-$monthNumber");

                $companyData[$companyKode][] = [
                    'company_kode' => $companyKode,
                    'bulan' => $monthName,
                    'sppn_jumlah' => $dataBulan ? $dataBulan->sppn_jumlah : 0,
                ];
            }
        }

        // Menggabungkan data menjadi satu array
        $flattenedData = [];
        foreach ($companyData as $companyRows) {
            foreach ($companyRows as $row) {
                $flattenedData[] = $row;
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success get data sppn all company',
            'data' => $flattenedData,
        ]);
    }

    public function getSppbSppnAllCompany()
    {
        $startYear = "2024";
        $currentYear = date('Y');

        // Query untuk sppb (cash_out)
        $sppb = DB::table('spp')
            ->join('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->join('master_company as company', 'spp.company_id', '=', 'company.company_id')
            ->select(
                'company.company_kode',
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y') as tahun"),
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') as bulan"),
                DB::raw('SUM(sppb.sppb_total) as cash_out')
            )
            ->whereYear('spp.spp_tanggal', ">=", $startYear)
            ->whereYear('spp.spp_tanggal', "<=", $currentYear)
            ->whereIn('spp.sppd_posisi', [38, 39])
            ->whereIn('spp.sppd_status', [1, 2, 100]) // Kondisi untuk sppd_status 1, 2, atau 100
            ->groupBy('company.company_kode', 'bulan')
            ->orderBy('company.company_kode')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy('company_kode'); // Mengelompokkan hasil berdasarkan company_kode

        // Query untuk sppn (cash_in)
        $sppn = DB::table('spp')
            ->join('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->join('master_company as company', 'spp.company_id', '=', 'company.company_id')
            ->select(
                'company.company_kode',
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y') as tahun"),
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%Y-%m') as bulan"),
                DB::raw('SUM(sppn.sppn_jumlah) as cash_in')
            )
            ->whereYear('spp.spp_tanggal', ">=", $startYear)
            ->whereYear('spp.spp_tanggal', "<=", $currentYear)
            ->whereIn('spp.sppd_posisi', [38, 39])
            ->whereIn('spp.sppd_status', [1, 2, 100]) // Kondisi untuk sppd_status 1, 2, atau 100
            ->groupBy('company.company_kode', 'bulan')
            ->orderBy('company.company_kode')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy('company_kode'); // Mengelompokkan hasil berdasarkan company_kode

        // dd($sppb);
        // List bulan dalam bahasa Indonesia
        $bulanIndonesia = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        // dd($startYear < $currentYear);
        // Gabungkan data sppb dan sppn berdasarkan company_kode dan bulan
        $companyData = [];
        foreach ($sppb as $companyKode => $dataPerCompany) {
            $companyData[$companyKode] = [];

            for ($i = $startYear; $i <= $currentYear; $i++) {
                foreach ($bulanIndonesia as $monthNumber => $monthName) {
                    $bulanFormat = "$i-$monthNumber";
                    // Dapatkan nilai cash_out dari sppb
                    $dataCashOut = $dataPerCompany->firstWhere('bulan', $bulanFormat);
                    $cashOut = $dataCashOut ? $dataCashOut->cash_out : 0;

                    // Dapatkan nilai cash_in dari sppn untuk bulan dan company yang sama
                    $dataCashIn = $sppn->get($companyKode, collect())->firstWhere('bulan', $bulanFormat);
                    $cashIn = $dataCashIn ? $dataCashIn->cash_in : 0;

                    $companyData[$companyKode][] = [
                        'company_kode' => $companyKode,
                        'bulan' => $monthNumber,
                        'tahun' => $i,
                        'cash_out' => $cashOut,
                        'cash_in' => $cashIn,
                    ];
                }
            }
        }


        // Menggabungkan data menjadi satu array
        $flattenedData = [];
        foreach ($companyData as $companyRows) {
            foreach ($companyRows as $row) {
                $flattenedData[] = $row;
            }
        }

        $data = [];
        foreach ($flattenedData as $row) {
            $data[] = [
                'company_kode' => $row['company_kode'],
                'tahun' => $row['tahun'],
                'bulan' => $row['bulan'],
                'jenis' => 'cash out',
                'total' => $row['cash_out'],
            ];

            $data[] = [
                'company_kode' => $row['company_kode'],
                'tahun' => $row['tahun'],
                'bulan' => $row['bulan'],
                'jenis' => 'cash in',
                'total' => $row['cash_in'],
            ];
        }

        // Kembalikan response JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Success get data sppb and sppn by company and month for current year',
            'data' => $data,
        ]);
    }

    public function getDataSppTerbayarDanBelumTerbayar(Request $request)
    {
        $start_month = '';
        $end_month = '';

        if (!$request->start_month && !$request->end_month) {
            $start_month = DB::table('spp')->orderBy('spp_tanggal', 'asc')->first();
            if ($start_month) {
                $start_month = $start_month->spp_tanggal;
            } else {
                $start_month = '';
            }
            $end_month = DB::table('spp')->orderBy('spp_tanggal', 'desc')->first();
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
        $sppb_terbayar = DB::table('spp')
            ->join('master_company', 'spp.company_id', '=', 'master_company.company_id')
            ->where('sppb_id', '!=', null)
            ->where('spp_status_bayar', '=', 1)
            ->select(
                DB::raw('COUNT(spp.spp_id) as total'),
                DB::raw("'sppb' as jenis"),
                'master_company.company_kode',
                'master_company.company_id'
            );
        // ->where('company_id', '=', $user_company);
        if ($request->start_month) {
            $sppb_terbayar->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }
        if ($request->end_month) {
            $sppb_terbayar->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }
        $sppb_terbayar = $sppb_terbayar->groupBy('company_id')->get();
        $sppb_belum_terbayar = DB::table('spp')
            ->join('master_company', 'spp.company_id', '=', 'master_company.company_id')
            ->where('sppb_id', '!=', null)
            ->where('spp_status_bayar', '=', 2)
            ->select(
                DB::raw('COUNT(spp.spp_id) as total'),
                DB::raw("'sppb' as jenis"),
                'master_company.company_kode',
                'master_company.company_id'
            );
        // ->where('company_id', '=', $user_company);
        if ($request->start_month) {
            $sppb_belum_terbayar->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }
        if ($request->end_month) {
            $sppb_belum_terbayar->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }
        $sppb_belum_terbayar = $sppb_belum_terbayar->groupBy('company_id')->get();
        $sppn_terselesaikan = DB::table('spp')
            ->join('master_company', 'spp.company_id', '=', 'master_company.company_id')
            ->where('sppn_id', '!=', null)
            ->where('spp_status_terima', '=', 1)
            ->select(
                DB::raw('COUNT(spp.spp_id) as total'),
                DB::raw("'sppn' as jenis"),
                'master_company.company_kode',
                'master_company.company_id'
            );
        // ->where('company_id', '=', $user_company);
        if ($request->start_month) {
            $sppn_terselesaikan->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }
        if ($request->end_month) {
            $sppn_terselesaikan->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }
        $sppn_terselesaikan = $sppn_terselesaikan->groupBy('company_id')->get();
        $sppn_belum_terselesaikan = DB::table('spp')
            ->join('master_company', 'spp.company_id', '=', 'master_company.company_id')
            ->where('sppn_id', '!=', null)
            ->where('spp_status_terima', '=', 2)
            ->select(
                DB::raw('COUNT(spp.spp_id) as total'),
                DB::raw("'sppn' as jenis"),
                'master_company.company_kode',
                'master_company.company_id'
            );
        // ->where('company_id', '=', $user_company);
        if ($request->start_month) {
            $sppn_belum_terselesaikan->where('spp_tanggal', '>=', $this->convertToDateFormat($request->start_month, 'first'));
        }
        if ($request->end_month) {
            $sppn_belum_terselesaikan->where('spp_tanggal', '<=', $this->convertToDateFormat($request->end_month, 'last'));
        }
        $sppn_belum_terselesaikan = $sppn_belum_terselesaikan->groupBy('company_id')->get();

        $final_result = [];
        foreach ($sppb_terbayar as $item) {
            $company_id = $item->company_id;
            $jenis = $item->jenis;
            $final_result[$company_id][$jenis]['company_kode'] = $item->company_kode;
            $final_result[$company_id][$jenis]['jumlah_bayar'] = $item->total;
            $final_result[$company_id][$jenis]['jenis'] = $item->jenis;
        }
        foreach ($sppb_belum_terbayar as $item) {
            $company_id = $item->company_id;
            $jenis = $item->jenis;
            $final_result[$company_id][$jenis]['company_kode'] = $item->company_kode;
            $final_result[$company_id][$jenis]['jumlah_belum_bayar'] = $item->total;
            $final_result[$company_id][$jenis]['jenis'] = $item->jenis;
        }
        foreach ($sppn_terselesaikan as $item) {
            $company_id = $item->company_id;
            $jenis = $item->jenis;
            $final_result[$company_id][$jenis]['company_kode'] = $item->company_kode;
            $final_result[$company_id][$jenis]['jumlah_bayar'] = $item->total;
            $final_result[$company_id][$jenis]['jenis'] = $item->jenis;
        }
        foreach ($sppn_belum_terselesaikan as $item) {
            $company_id = $item->company_id;
            $jenis = $item->jenis;
            $final_result[$company_id][$jenis]['company_kode'] = $item->company_kode;
            $final_result[$company_id][$jenis]['jumlah_belum_bayar'] = $item->total;
            $final_result[$company_id][$jenis]['jenis'] = $item->jenis;
        }
        // Format hasil akhir
        $formatted_result = [];
        foreach ($final_result as $company_id => $data_per_company) {
            foreach ($data_per_company as $jenis => $data) {
                $formatted_result[] = [
                    'company' => $data['company_kode'],
                    'jenis' => $data['jenis'],
                    'status_bayar' => 'sudah',
                    'total' => $data['jumlah_bayar'] ?? 0,
                ];
                $formatted_result[] = [
                    'company' => $data['company_kode'],
                    'jenis' => $data['jenis'],
                    'status_bayar' => 'belum',
                    'total' => $data['jumlah_belum_bayar'] ?? 0,
                ];
            }
        }
        // Hasil dalam bentuk array terformat
        $response = $formatted_result;
        return response()->json([
            'data' => $response
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
}
