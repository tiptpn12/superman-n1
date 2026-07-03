<?php

namespace App\Repositories;

use App\Repositories\Interfaces\SppdRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SppdRepository implements SppdRepositoryInterface
{
    public function getSppToDoListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $startDate = null, $endDate = null, $sppdPosisi = null)
    {
        $query = DB::table('spp')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
            ->select(
                'master_hak_akses_nama',
                'spp_no_dokumen',
                'spp_id',
                'spp.sppb_id',
                'spp.sppn_id',
                'sppd_revisi',
                'sppd_status',
                'sppd_posisi',
                'sppd_proses',
                'master_bagian_nama',
                'spp_status_proses',
                'spp_status_posisi',
                DB::raw("DATE_FORMAT(spp.spp_tanggal, '%d-%m-%Y') AS tanggal"),
                'sppb_no',
                'sppb_tanggal',
                'sppb_total',
                'sppn_no',
                'sppn_tanggal',
                'sppn_jumlah',
                'sppd_status',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2")
            );
        if ($grupId != null) {
            if ($grupId == 1 || $akses == 18) {
                $query->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppd_posisi', $akses)
                    ->whereBetween('spp.sppd_status', [0, 2])
                    ->whereIn('spp.flow_id', $flow)
                    ->addSelect('spp_kabag');
            } else if ($grupId == 2) {
                if ($petugasPP == 1) {
                    $query->where('spp.sppd_posisi', $akses)
                        ->where('spp.company_id', $company)
                        ->whereBetween('spp.sppd_status', [1, 2])
                        ->whereIn('spp.flow_id', $flow)
                        ->addSelect('spp_kabag',);
                } else {
                    $query->where('spp.master_bagian_id', '=', $bagian)
                        ->where('spp.sppd_posisi', $akses)
                        ->whereBetween('spp.sppd_status', [1, 2])
                        ->whereIn('spp.flow_id', $flow)
                        ->addSelect('spp_kabag',);
                }
            } else if ($grupId == 3) {
                $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                    ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                    ->where('spp.sppd_posisi', $akses)
                    ->where('spp.company_id', $company)
                    ->whereBetween('spp.sppd_status', [1, 2])
                    ->whereIn('spp.flow_id', $flow)
                    ->addSelect(
                        'sppb_bukti_kas.sppb_bukti_kas_id',
                        'spp_status_terima',
                        'spp_status_bayar',
                        'sppb_bukti_kas.master_rekening_id AS nomor_byr',
                        'sppn_bukti_kas.master_rekening_id AS nomor_pnr',
                        'sppb.sppb_metode_pembayaran as metode_pembayaran',
                        'sppb.sppb_no',
                        'sppn.sppn_no'
                    );
                $query->where(function ($q) {
                    $q->whereIn('sppb.sppb_metode_pembayaran', ['kas', 'bank', 'tidak_transfer'])
                        ->orWhereNotNull('sppn.sppn_no');
                });
            } else if ($grupId == 4) {
                $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                    ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                    ->where('spp.sppd_posisi', '=', $akses)
                    ->where('spp.company_id', $company)
                    ->whereBetween('spp.sppd_status', [1, 2])
                    ->whereIn('spp.flow_id', $flow)
                    ->addSelect(
                        'sppb_bukti_kas.sppb_bukti_kas_id',
                        'spp_status_terima',
                        'spp_status_bayar',
                        'sppb_bukti_kas.master_rekening_id AS nomor_byr',
                        'sppn_bukti_kas.master_rekening_id AS nomor_pnr',
                        'sppb.sppb_metode_pembayaran as metode_pembayaran',
                        'spp_bukti_kas_bank',
                    );
            } else if ($grupId == 7) {
                $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                    ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                    ->where('spp.company_id', $company)
                    ->where('spp.sppd_posisi', $akses)
                    ->whereBetween('spp.sppd_status', [1, 2])
                    ->whereIn('spp.flow_id', $flow)
                    ->addSelect(
                        'sppb_bukti_kas.sppb_bukti_kas_id',
                        'spp_status_terima',
                        'spp_status_bayar',
                        'sppb_bukti_kas.master_rekening_id AS nomor_byr',
                        'sppn_bukti_kas.master_rekening_id AS nomor_pnr',
                        'sppb.sppb_metode_pembayaran as metode_pembayaran');
            } else if ($grupId == 8) {
                $query->where('spp.sppd_status', 0)
                    ->where('spp.company_id', '5')
                    ->where('spp.master_bagian_id', '=', '133')
                    ->addSelect('spp_kabag');
                if ($startDate && $endDate) {
                    $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
                }
                if ($sppdPosisi) {
                    $query->where('spp.sppd_posisi', $sppdPosisi);
                }
            } else if ($grupId == 9) {
                $query->where('spp.sppd_status', 0)
                    ->addSelect('spp_kabag');
                if ($startDate && $endDate) {
                    $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
                }
                if ($sppdPosisi) {
                    $query->where('spp.sppd_posisi', $sppdPosisi);
                }
            }
            $query->groupBy('spp_id', 'spp.spp_tanggal')
                ->orderBy('spp_tanggal', 'desc');
        }
        $data = $query->get();
        return $data;
    }

    public function getSppRevisiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $flowDetailByAkses = null, $startDate = null, $endDate = null, $sppdPosisi = null)
    {
        $query = DB::table('spp')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
            ->select(
                'master_hak_akses_nama',
                'spp_no_dokumen',
                'spp_id',
                'spp.sppb_id',
                'spp.sppn_id',
                'sppd_revisi',
                'sppd_status',
                'sppd_posisi',
                'sppd_proses',
                'spp_kabag',
                'master_bagian_nama',
                'spp_status_proses',
                'spp_status_posisi',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb_no',
                'sppb_tanggal',
                'sppb_total',
                'sppn_no',
                'sppn_tanggal',
                'sppn_jumlah',
                'sppd_status',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2")
            );
        if ($grupId == 1 || $akses == 18) {
            $query->where('spp.master_bagian_id', '=', $bagian)
                ->where('spp.sppd_status', 3)
                ->where('spp.sppd_posisi', $akses)
                ->whereIn('spp.flow_id', $flow);
        } else if ($grupId == 2) {
            if ($petugasPP == 1) {
                $query->where('spp.company_id', $company)
                    ->where('spp.sppd_revisi', '=', $akses)
                    ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)
                    ->whereIn('spp.flow_id', $flow)
                    ->orWhere('spp.company_id', $company)
                    ->where('spp.sppd_status', '=', 3)
                    ->where('spp.sppd_posisi', '=', $akses)
                    ->whereIn('spp.flow_id', $flow);
            } else {
                $query->where('spp.sppd_revisi', '=', $akses)
                    ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)
                    ->where('spp.master_bagian_id', '=', $bagian)
                    ->whereIn('spp.flow_id', $flow)
                    ->orWhere('spp.sppd_posisi', '=', $akses)
                    ->where('spp.master_bagian_id', '=', $bagian)
                    ->where('spp.sppd_status', '=', 3)
                    ->whereIn('spp.flow_id', $flow);
            }
        } else if ($grupId == 3) {
            $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->where('spp.sppd_revisi', '=', $akses)
                ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)
                ->where('spp.company_id', $company)
                ->whereIn('spp.flow_id', $flow)
                ->orWhere('spp.sppd_posisi', '=', $akses)
                ->where('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->whereIn('spp.flow_id', $flow)
                ->addSelect('sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran',);
            $query->where(function ($q) {
                $q->whereIn('sppb.sppb_metode_pembayaran', ['kas', 'bank', 'tidak_transfer'])
                    ->orWhereNotNull('sppn.sppn_no');
            });
        } else if ($grupId == 4) {
            $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')->where('spp.sppd_revisi', '=', $akses)
                ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)
                ->where('spp.company_id', $company)
                ->whereIn('spp.flow_id', $flow)
                ->orWhere('spp.sppd_posisi', '=', $akses)
                ->where('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->whereIn('spp.flow_id', $flow)
                ->addSelect('spp_bukti_kas_bank', 'sppb_bukti_kas.sppb_bukti_kas_id', 'spp_status_terima', 'spp_status_bayar', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran');
        } else if ($grupId == 7) {
            $query->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->where('spp.sppd_revisi', '=', $akses)
                ->where('spp.sppd_proses', '<', $flowDetailByAkses[0]->flow_akses)
                ->where('spp.company_id', $company)
                ->whereIn('spp.flow_id', $flow)
                ->orWhere('spp.sppd_posisi', '=', $akses)
                ->where('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->whereIn('spp.flow_id', $flow)
                ->addSelect('spp_bukti_kas_bank', 'sppb_bukti_kas.sppb_bukti_kas_id', 'spp_status_terima', 'spp_status_bayar', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran');
        } else if ($grupId == 8) {
            $query->where('spp.sppd_status', 3)
                ->where('spp.sppd_proses', 0)
                ->where('spp.company_id', '5')
                ->where('spp.master_bagian_id', '=', '133');
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        } else if ($grupId == 9) {
            $query->where('spp.sppd_status', 3)
                ->where('spp.sppd_proses', 0);
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        }
        $query->groupBy('spp_id', 'spp.spp_tanggal')
            ->orderBy('spp_tanggal', 'desc');

        $data = $query->get();
        return $data;
    }

    public function getSppProgressListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $flowDetailByAkses = null, $startDate = null, $endDate = null, $sppdPosisi = null)
    {
        $query = DB::table('spp')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
            ->select(
                'master_hak_akses_nama',
                'spp_no_dokumen',
                'spp_id',
                'spp.sppb_id',
                'spp.sppn_id',
                'sppd_revisi',
                'sppd_status',
                'sppd_posisi',
                'sppd_proses',
                'spp_kabag',
                'master_bagian_nama',
                'spp_status_proses',
                'spp_status_posisi',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb_no',
                'sppb_tanggal',
                'sppb_total',
                'sppn_no',
                'sppn_tanggal',
                'sppn_jumlah',
                'sppd_status',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2")
            );

        if ($grupId == 1 || $akses == 18) {
            $query->where('spp.master_bagian_id', '=', $bagian)
                ->where('spp.sppd_status', '!=', 100)
                ->where('spp.sppd_posisi', '!=', $akses)
                ->whereIn('spp.flow_id', $flow);
        } else if ($grupId == 2) {
            switch ($petugasPP) {
                case 1:
                    $query->where('spp.company_id', $company)
                        ->where('spp.sppd_proses', '>=', $flowDetailByAkses[0]->flow_akses)
                        ->whereIn('spp.flow_id', $flow)
                        ->where('spp.sppd_proses', '!=', NULL)
                        ->where('spp.sppd_posisi', '!=', $akses)
                        ->where('spp.sppd_status', '!=', 100)
                        ->whereIn('spp.flow_id', $flow)
                        ->addSelect('flow_id');
                    break;
                default:
                    $query->where('spp.master_bagian_id', '=', $bagian)
                        ->where('spp.sppd_proses', '>=', $flowDetailByAkses[0]->flow_akses)
                        ->where('spp.sppd_proses', '!=', NULL)
                        ->where('spp.sppd_status', '!=', 100)
                        ->whereIn('spp.flow_id', $flow)
                        ->whereNotBetween('spp.sppd_status', [3, 100])
                        ->where('spp.sppd_posisi', '!=', $akses)
                        ->addSelect('flow_id');
                    break;
            }
        } else if ($grupId == 3) {
            $query->where('spp.sppd_proses', '>=', $flowDetailByAkses[0]->flow_akses)
                ->where('spp.sppd_proses', '!=', NULL)
                ->where('spp.sppd_posisi', '!=', $akses)
                ->where('spp.company_id', $company)
                ->whereBetween('spp.sppd_status', [1, 2])
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->addSelect('sppb_bukti_kas.sppb_bukti_kas_id', 'spp_status_terima', 'spp_status_bayar', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran');
            $query->where(function ($q) {
                $q->whereIn('sppb.sppb_metode_pembayaran', ['kas', 'bank', 'tidak_transfer'])
                    ->orWhereNotNull('sppn.sppn_no');
            });
        } else if ($grupId == 4) {
            $query->where('spp.company_id', $company)
                ->whereBetween('spp.sppd_status', [1, 2])
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->addSelect('sppb_bukti_kas.sppb_bukti_kas_id', 'spp_status_terima', 'spp_status_bayar', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran', 'spp_bukti_kas_bank');
        } else if ($grupId == 7) {
            $query->where('spp.company_id', $company)
                ->where('spp.sppd_proses', '>=', $flowDetailByAkses[0]->flow_akses)
                ->where('spp.sppd_proses', '!=', NULL)
                ->where('spp.sppd_posisi', '!=', $akses)
                ->whereBetween('spp.sppd_status', [1, 2])
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->addSelect('sppb_bukti_kas.sppb_bukti_kas_id', 'spp_status_terima', 'spp_status_bayar', 'sppb_bukti_kas.master_rekening_id AS nomor_byr', 'sppn_bukti_kas.master_rekening_id AS nomor_pnr', 'sppb.sppb_metode_pembayaran as metode_pembayaran');
        } else if ($grupId == 8) {
            $query->where('spp.sppd_status', '!=', 100)
                ->whereBetween('spp.sppd_status', [1, 2])
                ->where('spp.company_id', '5')
                ->where('spp.master_bagian_id', '=', '133');
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        } else if ($grupId == 9) {
            $query->where('spp.sppd_status', '!=', 100)
                ->whereBetween('spp.sppd_status', [1, 2]);
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        }

        switch ($grupId) {
            case 3:
                $query->groupBy('spp_id', 'spp_status_proses', 'spp_status_posisi', 'spp.spp_tanggal', 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'sppd_status')->orderBy('spp_tanggal', 'desc');
                break;
            default:
                $query->groupBy('spp_id', 'spp.spp_tanggal')
                    ->orderBy('spp_tanggal', 'desc');
                break;
        }

        $data = $query->get();
        return $data;
    }

    public function getSppSelesaiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null, $petugasPP = null, $startDate = null, $endDate = null, $sppdPosisi = null)
    {
        $query = DB::table('spp')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->select(
                'spp_no_dokumen',
                'spp_id',
                'flow_id',
                'spp.sppb_id',
                'sppd_proses',
                'sppd_posisi',
                'sppd_revisi',
                'sppd_status',
                'spp.sppn_id',
                'spp_kabag',
                'master_bagian_nama',
                'spp_status_proses',
                'spp_status_posisi',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb_no',
                'sppb_tanggal',
                'sppb_total',
                'sppn_no',
                'sppn_tanggal',
                'sppn_jumlah',
                'sppd_status',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2")
            );

        if ($grupId == 1 || $akses == 18) {
            $query->where('spp.master_bagian_id', '=', $bagian)
                ->where('spp.sppd_status', 100)
                ->whereIn('spp.flow_id', $flow);
        } else if ($grupId == 2) {
            switch ($petugasPP) {
                case 1:
                    $query->where('spp.company_id', $company)
                        ->where('spp.sppd_status', 100)
                        ->whereIn('spp.flow_id', $flow)
                        ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                        ->addSelect('master_hak_akses_nama',);
                    break;
                default:
                    $query->where('spp.master_bagian_id', '=', $bagian)
                        ->where('spp.sppd_status', 100)
                        ->whereIn('spp.flow_id', $flow)
                        ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                        ->addSelect('master_hak_akses_nama',);
                    break;
            }
        } else if ($grupId == 3) {
            $query->where('spp.company_id', $company)
                ->where('spp.sppd_status', 100)
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->addSelect('master_hak_akses_nama', 'sppb.sppb_metode_pembayaran as metode_pembayaran');
            $query->where(function ($q) {
                $q->whereIn('sppb.sppb_metode_pembayaran', ['kas', 'bank', 'tidak_transfer'])
                    ->orWhereNotNull('sppn.sppn_no');
            });
        } else if ($grupId == 4) {
            $query->where('spp.sppd_status', 100)
                ->where('spp.company_id', $company)
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->leftJoin('sppb_bukti_kas', 'sppb.sppb_id', '=', 'sppb_bukti_kas.sppb_id')
                ->leftJoin('sppn_bukti_kas', 'sppn.sppn_id', '=', 'sppn_bukti_kas.sppn_id')
                ->addSelect(
                    'master_hak_akses_nama',
                    'sppb_bukti_kas.master_rekening_id AS nomor_byr',
                    'sppn_bukti_kas.master_rekening_id AS nomor_pnr',
                    'sppb.sppb_metode_pembayaran as metode_pembayaran'
                );
        } else if ($grupId == 7) {
            $query->where('spp.company_id', $company)
                ->where('spp.sppd_status', 100)
                ->whereIn('spp.flow_id', $flow)
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->addSelect('master_hak_akses_nama');
        } else if ($grupId == 8) {
            $query->where('spp.sppd_status', 100)
                ->where('spp.company_id', '5')
                ->where('spp.master_bagian_id', '=', '133')
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->addSelect('master_hak_akses_nama');
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        } else if ($grupId == 9) {
            $query->where('spp.sppd_status', 100)
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->addSelect('master_hak_akses_nama');
            if ($startDate && $endDate) {
                $query->whereBetween('spp.spp_tanggal', [$startDate, $endDate]);
            }
            if ($sppdPosisi) {
                $query->where('spp.sppd_posisi', $sppdPosisi);
            }
        }

        $query->groupBy('spp_id', 'tanggal')
            ->orderBy('spp_tanggal', 'desc');

        $data = $query->get();
        return $data;
    }

    public function getSppBatalAdmiListByCriteria($bagian = null, $akses = null, $flow = null, $company = null, $grupId = null)
    {
        $query = DB::table('spp')
            ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')
            ->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
            ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')
            ->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
            ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
            ->select(
                'spp_no_dokumen',
                'spp_id',
                'flow_id',
                'spp.sppb_id',
                'sppd_proses',
                'sppd_posisi',
                'sppd_revisi',
                'sppd_status',
                'spp.sppn_id',
                'spp_kabag',
                'master_bagian_nama',
                'spp_status_proses',
                'spp_status_posisi',
                DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"),
                'sppb_no',
                'sppb_tanggal',
                'sppb_total',
                'sppn_no',
                'sppn_tanggal',
                'sppn_jumlah',
                'sppd_status',
                DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"),
                DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2")
            );

        if ($grupId == 1 || $akses == 18) {
            $query->where('spp.master_bagian_id', '=', $bagian)
                ->whereIn('spp.sppd_status', [4, 5])
                ->whereIn('spp.flow_id', $flow);
        } else if ($grupId == 8) {
            $query->whereIn('spp.sppd_status', [4, 5])
                ->where('spp.company_id', '5')
                ->where('spp.master_bagian_id', '=', '133');
        }
        else if ($grupId == 9) {
            $query->whereIn('spp.sppd_status', [4, 5]);
                //->where('spp.company_id', $company);
        }

        $query->groupBy('spp_id', 'tanggal')->orderBy('spp_tanggal', 'desc');

        $data = $query->get();
        return $data;
    }
   

}
