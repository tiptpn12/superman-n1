<?php

namespace App\Services;

use App\Repositories\Interfaces\FlowDetailRepositoryInterface;
use App\Repositories\Interfaces\ProsesRepositoryInterface;

class ProsesService
{
    protected $prosesRepository;
    protected $flowDetailRepository;
    public function __construct(ProsesRepositoryInterface $prosesRepository, FlowDetailRepositoryInterface $flowDetailRepository)
    {
        $this->prosesRepository = $prosesRepository;
        $this->flowDetailRepository = $flowDetailRepository;
    }
    public function getProsesDivisi($companyId, $hakAkses)
    {
        try {
            switch ($companyId) {
                    // Proses HO
                case 5:
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
                    break;
                    // Proses Regional 1
                case 7:
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
                    break;
                    // Proses Regional 2
                case 8:
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
                    break;
                    // Proses Regional 3
                case 9:
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
                    break;
                    // Proses Regional 4
                case 10:
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
                    break;
                    // Proses Regional 5
                case 11:
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
                    break;
                    // Proses Regional 6
                case 12:
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
                    break;
                    // Proses Regional 7
                case 13:
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
                    break;
                    // Proses Regional 8
                case 14:
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
                    break;
                default:
                    throw new \Exception('Data Not Found', 404);
            }

            return [
                'proses_divisi' => $divisi,
                'proses_akuntansi' => $akuntansi,
                'proses_perpajakan' => $perpajakan,
                'proses_anggaran' => $anggaran,
                'proses_miro' => $miro,
                'proses_kas_bank' => $kasBank,
                'proses_pembayaran' => $pembayaran,
            ];
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
