<?php

namespace App\Services;

use App\Company;
use App\Repositories\Interfaces\FlowDetailRepositoryInterface;
use App\Repositories\Interfaces\ProsesRepositoryInterface;
use DateTime;
use Illuminate\Support\Facades\DB;

class ChartService
{
    protected $prosesRepository;
    protected $flowDetailRepository;
    public function __construct(ProsesRepositoryInterface $prosesRepository, FlowDetailRepositoryInterface $flowDetailRepository)
    {
        $this->prosesRepository = $prosesRepository;
        $this->flowDetailRepository = $flowDetailRepository;
    }
    public function getProses($companyId, $hakAkses)
    {
        if (in_array($companyId, [5, 7, 8, 9, 10, 11, 12, 13, 14])) {
            // Proses DIvisi
            $divisi = $this->prosesRepository->GetProsesToDo($companyId, 34, [0, 3])->count();

            // Proses Akuntansi
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(35);
            $todoAkuntansi = $this->prosesRepository->GetProsesToDo($companyId, 35, [1, 2])->count();
            $revisiAkuntansi = $this->prosesRepository->GetProsesRevisi($companyId, 35, $flowDetailByAkses, [3])->count();
            $akuntansi = $todoAkuntansi + $revisiAkuntansi;

            // Proses Perpajakan
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(36);
            $todoPerpajakan = $this->prosesRepository->GetProsesToDo($companyId, 36, [1, 2])->count();
            $revisiPerpajakan = $this->prosesRepository->GetProsesRevisi($companyId, 36, $flowDetailByAkses, [3])->count();
            $perpajakan = $todoPerpajakan + $revisiPerpajakan;

            // Proses Anggaran
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(42);
            $todoAnggaran = $this->prosesRepository->GetProsesToDo($companyId, 42, [1, 2])->count();
            $revisiAnggaran = $this->prosesRepository->GetProsesRevisi($companyId, 42, $flowDetailByAkses, [3])->count();
            $anggaran = $todoAnggaran + $revisiAnggaran;

            // Proses Miro
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(41);
            $todoMiro = $this->prosesRepository->GetProsesToDo($companyId, 41, [1, 2])->count();
            $revisiMiro = $this->prosesRepository->GetProsesRevisi($companyId, 41, $flowDetailByAkses, [3])->count();
            $miro = $todoMiro + $revisiMiro;

            // Proses Kas dan Bank
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(38);
            $todoKasBank = $this->prosesRepository->GetProsesToDo($companyId, 38, [1, 2])->count();
            $revisiKasBank = $this->prosesRepository->GetProsesRevisi($companyId, 38, $flowDetailByAkses, [3])->count();
            $kasBank = $todoKasBank + $revisiKasBank;

            // Proses Pembayaran
            $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses(39);
            $todoPembayaran = $this->prosesRepository->GetProsesToDo($companyId, 39, [1, 2])->count();
            $revisiPembayaran = $this->prosesRepository->GetProsesRevisi($companyId, 39, $flowDetailByAkses, [3])->count();
            $pembayaran = $todoPembayaran + $revisiPembayaran;
            $region_nama = explode('-', Company::where('company_id', $companyId)->first()->company_nama)[1];
        } else {
            $divisi = 0;
            $akuntansi = 0;
            $perpajakan = 0;
            $anggaran = 0;
            $miro = 0;
            $kasBank = 0;
            $pembayaran = 0;
            $region_nama = '';
        }

        return [
            'nama_region' => $region_nama,
            'proses_divisi' => $divisi,
            'proses_akuntansi' => $akuntansi,
            'proses_perpajakan' => $perpajakan,
            'proses_anggaran' => $anggaran,
            'proses_miro' => $miro,
            'proses_kas_bank' => $kasBank,
            'proses_pembayaran' => $pembayaran,
        ];
    }

    public function getPenerimaanPengeluaran($companyId, $startMonth, $endMonth)
    {
        $start = (new DateTime($startMonth))->format('Y-m-d');
        $end = (new DateTime($endMonth))->modify('last day of this month')->format('Y-m-d');

        try {
            $totalPembayaran = DB::table('spp')
                ->join('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->whereNotNull('spp.sppb_id')
                ->where('spp.company_id', 5)
                ->whereBetween('sppb.sppb_tanggal', [$start, $end])
                ->sum('sppb.sppb_total');

            $totalPenerimaan = DB::table('spp')
                ->join('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->whereNotNull('spp.sppn_id')
                ->where('spp.company_id', 5)
                ->whereBetween('sppn.sppn_tanggal', [$start, $end])
                ->sum('sppn.sppn_jumlah');

            $region_nama = explode('-', Company::where('company_id', $companyId)->first()->company_nama)[1];

            return [
                'nama_region' => $region_nama,
                'tanggal_mulai' => $start,
                'tanggal_selesai' => $end,
                'total_pembayaran' => intval($totalPembayaran),
                'total_penerimaan' => intval($totalPenerimaan),
                'start_month' => $startMonth,
                'end_month' => $endMonth
            ];
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
