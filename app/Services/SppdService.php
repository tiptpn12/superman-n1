<?php

namespace App\Services;

use App\Repositories\Interfaces\BagianRepositoryInterface;
use App\Repositories\Interfaces\FlowDetailRepositoryInterface;
use App\Repositories\Interfaces\GlRepositoryInterface;
use App\Repositories\Interfaces\NamaKaryawanRepositoryInterface;
use App\Repositories\Interfaces\SppbBayarRepositoryInterface;
use App\Repositories\Interfaces\SppbBuktiKasRepositoryInterface;
use App\Repositories\Interfaces\SppdRepositoryInterface;
use App\Repositories\Interfaces\SppnBuktiKasRepositoryInterface;
use App\Repositories\Interfaces\SppnTerimaRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\VendorRepositoryInterface;
use App\Sppb_bukti_kas;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class SppdService
{
    protected $userRepository;
    protected $flowDetailRepository;
    protected $vendorRepository;
    protected $bagianRepository;
    protected $glRepository;
    protected $sppdRepository;
    protected $sppbBuktiKasRepository;
    protected $sppnBuktiKasRepository;
    protected $sppbBayarRepository;
    protected $sppnTerimaRepository;
    protected $namaKaryawanRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        FlowDetailRepositoryInterface $flowDetailRepository,
        VendorRepositoryInterface $vendorRepository,
        BagianRepositoryInterface $bagianRepository,
        GlRepositoryInterface $glRepository,
        SppdRepositoryInterface $sppdRepository,
        SppbBuktiKasRepositoryInterface $sppbBuktiKasRepository,
        SppnBuktiKasRepositoryInterface $sppnBuktiKasRepository,
        SppbBayarRepositoryInterface $sppbBayarRepository,
        SppnTerimaRepositoryInterface $sppnTerimaRepository,
        NamaKaryawanRepositoryInterface $namaKaryawanRepository
    ) {
        $this->userRepository = $userRepository;
        $this->flowDetailRepository = $flowDetailRepository;
        $this->vendorRepository = $vendorRepository;
        $this->bagianRepository = $bagianRepository;
        $this->glRepository = $glRepository;
        $this->sppdRepository = $sppdRepository;
        $this->sppbBuktiKasRepository = $sppbBuktiKasRepository;
        $this->sppnBuktiKasRepository = $sppnBuktiKasRepository;
        $this->sppbBayarRepository = $sppbBayarRepository;
        $this->sppnTerimaRepository = $sppnTerimaRepository;
        $this->namaKaryawanRepository = $namaKaryawanRepository;
    }
    /**
     * Get data spp to do
     *
     * @return mixed
     */
    public function getDataSppToDo()
    {
        // Get session variables
        $grupId = Session::get('grup_ui');
        $bagianId = Session::get('bagian');
        $akses = Session::get('hak_akses');
        $petugaspPp = Session::get('petugas_pp');
        $company = Session::get('company');
        $startDate = request('start_date');
        $endDate = request('end_date');
        $sppdPosisi = request('sppd_posisi');

        if (is_null($grupId) || is_null($bagianId) || is_null($akses) || is_null($petugaspPp) || is_null($company)) {
            return response()->json(['error' => 'Session variables are not set.'], 400);
        }

        // Get flow by company and access
        $flow = $this->flowDetailRepository->getFlowIdsByCompanyAndAccess($company, $akses);

        if (is_null($flow)) {
            return response()->json(['error' => 'Failed to get flow by company and access.'], 400);
        }

        // Get data to do from cache
        $toDoLists = [];
        // Get data to do from sppd repository if it is allowed
        if (in_array($grupId, [1, 2, 3, 4, 7, 8]) || $akses == 18) {
            // Get data to do from sppd repository
            try {
                $toDoLists = $this->sppdRepository->getSppToDoListByCriteria($bagianId, $akses, $flow, $company, $grupId, $petugaspPp, $startDate, $endDate, $sppdPosisi);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        }

        // Create data tables
        $dataTables = datatables()->of($toDoLists)
            ->addIndexColumn()
            ->addColumn('hashedId', function ($row) use ($grupId) {
                // Encrypt the spp_id to be used in the view
                if (is_null($row->spp_id)) {
                    return null;
                }

                return encrypt($row->spp_id);
            });
        return $dataTables->toJson();
    }

    public function getDataSppRevisi()
    {
        $grupId = Session::get('grup_ui');
        $bagianId = Session::get('bagian');
        $akses = Session::get('hak_akses');
        $petugaspPp = Session::get('petugas_pp');
        $company = Session::get('company');

        $startDate = request('start_date');
        $endDate = request('end_date');
        $sppdPosisi = request('sppd_posisi');

        $flow = $this->flowDetailRepository->getFlowIdsByCompanyAndAccess($company, $akses);

        $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses($akses);

        $revisi = [];
        if (in_array($grupId, [1, 2, 3, 4, 7, 8]) || $akses == 18) {
            $revisi = $this->sppdRepository->getSppRevisiListByCriteria($bagianId, $akses, $flow, $company, $grupId, $petugaspPp, $flowDetailByAkses, $startDate, $endDate, $sppdPosisi);
        }

        return datatables()->of($revisi)
            ->addIndexColumn()
            ->addColumn('hashedId', function ($row) use ($grupId) {
                return encrypt($row->spp_id);
            })->toJson();
    }

    /**
     * Get data spp proses
     *
     * @return mixed
     */
    public function getDataSppProses()
    {
        // Get session variables
        $grupId = Session::get('grup_ui');
        $bagianId = Session::get('bagian');
        $akses = Session::get('hak_akses');
        $petugaspPp = Session::get('petugas_pp');
        $company = Session::get('company');

        $startDate = request('start_date');
        $endDate = request('end_date');
        $sppdPosisi = request('sppd_posisi');

        // Get flow and flow detail by access
        $flow = $this->flowDetailRepository->getFlowIdsByCompanyAndAccess($company, $akses);
        $flowDetailByAkses = $this->flowDetailRepository->getDetailFlowByHakAkses($akses);

        // Get data proses from cache
        $proses = [];
        if (in_array($grupId, [1, 2, 3, 4, 7, 8]) || $akses == 18) {
            // Get data proses from sppd repository
            $proses = $this->sppdRepository->getSppProgressListByCriteria($bagianId, $akses, $flow, $company, $grupId, $petugaspPp, $flowDetailByAkses, $startDate, $endDate, $sppdPosisi);
        }
        // Create data tables
        return datatables()->of($proses)
            ->addIndexColumn()
            ->addColumn('hashedId', function ($row) use ($grupId) {
                return encrypt($row->spp_id);
            })->toJson();
    }

    /**
     * Get data spp selesai
     *
     * @return mixed
     */
    public function getDataSppSelesai()
    {
        // Get session variables
        $grupId = Session::get('grup_ui');
        $bagianId = Session::get('bagian');
        $akses = Session::get('hak_akses');
        $petugaspPp = Session::get('petugas_pp');
        $company = Session::get('company');

        $startDate = request('start_date');
        $endDate = request('end_date');
        $sppdPosisi = request('sppd_posisi');

        // Get flow by company and access
        $flow = $this->flowDetailRepository->getFlowIdsByCompanyAndAccess($company, $akses);

        // Get data selesai from cache
        $selesai = [];

        // Get data selesai from sppd repository if it is allowed
        // Check if the user has access to view the data
        if (in_array($grupId, [1, 2, 3, 4, 7, 8]) || $akses == 18) {
            // Get data selesai from sppd repository
            $selesai = $this->sppdRepository->getSppSelesaiListByCriteria($bagianId, $akses, $flow, $company, $grupId, $petugaspPp, $startDate, $endDate, $sppdPosisi);
        }

        // Create data tables
        // Use Datatables to create data tables
        $dataTables = datatables()->of($selesai)
            // Add index column
            ->addIndexColumn()
            // Add hashed id column
            ->addColumn('hashedId', function ($row) use ($grupId) {
                // Encrypt the id with the user's id
                return encrypt($row->spp_id);
            })
            // Convert the data tables to json
            ->toJson();

        return $dataTables;
    }

    /**
     * Get data spp batal
     *
     * @return mixed
     */
    public function getDataSppBatal()
    {
        $companyId = Session::get('company');
        $grupId = Session::get('grup_ui');
        $sectionId = Session::get('bagian');
        $access = Session::get('hak_akses');

        // Get the flow by company and access
        $flowIds = $this->flowDetailRepository->getFlowIdsByCompanyAndAccess($companyId, $access);

        // Get data batal from the cache
        $batalData = [];
        // Get data batal from the sppd repository if it is allowed
        if (in_array($grupId, [1, 8]) || $access === 18) {
            // Get data batal from the sppd repository
            $batalData = $this->sppdRepository->getSppBatalAdmiListByCriteria($sectionId, $access, $flowIds, $companyId, $grupId);
        }

        // Create the data tables
        $dataTables = datatables()->of($batalData)
            ->addIndexColumn()
            ->addColumn('hashedId', function ($row) {
                // Encrypt the spp_id to be used in the view
                return encrypt($row->spp_id);
            })
            ->toJson();

        return $dataTables;
    }

    public function getSppbCetakBuktiKas($id)
    {
        try {
            $data = $this->sppbBuktiKasRepository->getBuktiKasBySppbId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getSppnCetakBuktiKas($id)
    {
        try {
            $data = $this->sppnBuktiKasRepository->getBuktiKasBySppnId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getPenerima($id)
    {
        try {
            $data = $this->namaKaryawanRepository->getNamaKaryawanBySppbId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getDiterima($id)
    {
        try {
            $data = $this->namaKaryawanRepository->getNamaKaryawanBySppnId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getSppbBayar($id)
    {
        try {
            $data = $this->sppbBayarRepository->getSppbBayarBySppbId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }

    public function getSppnTerima($id)
    {
        try {
            $data = $this->sppnTerimaRepository->getSppnTerimaBySppnId($id);
            if ($data == null) {
                throw new Exception('Data not found', 404);
            }
            return response()->json([
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'errors' => [
                    'message' => $th->getMessage(),
                ],
            ], $th->getCode());
        }
    }
}
