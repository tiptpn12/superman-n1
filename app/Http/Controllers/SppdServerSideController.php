<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SppdService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SppdServerSideController extends Controller
{
    protected $sppdService;

    public function __construct(SppdService $sppdService)
    {
        $this->sppdService = $sppdService;
    }

    public function index()
    {
        $grupId = Session::get('grup_ui');
        $hakAkses = Session::get('hak_akses');
        $index = 0;
        $index_cetak = Session::get('index_cetak', 0);
        $id_cetak = Session::get('id_cetak', 0);
        // dd('test');
        return view('page.spp.sppd_old_2', compact('grupId', 'hakAkses', 'index', 'index_cetak', 'id_cetak'));
    }

    public function getTodo()
    {
        try {
            $dataTables = $this->sppdService->getDataSppToDo();
            return $dataTables;
            // dd($todoList);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getRevisi()
    {
        try {
            $dataTables = $this->sppdService->getDataSppRevisi();

            return $dataTables;
        } catch (\Throwable $th) {

            dd($th);
        }
    }

    /**
     * Get data table server side proses SPPD.
     *
     * @return \Yajra\DataTables\DataTables
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function getProses()
    {
        // Try to get data table server side proses SPPD
        try {
            $dataTables = $this->sppdService->getDataSppProses();

            // Check if data table is null
            if (is_null($dataTables)) {
                // Throw an invalid argument exception
                throw new \InvalidArgumentException('DataTables is null');
            }

            // Return data table
            return $dataTables;
        } catch (\InvalidArgumentException | \Throwable $th) {
            // Return error response with 500 status code
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data table server side selesai SPPD.
     *
     * @return \Yajra\DataTables\DataTables
     *
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function getSelesai()
    {
        // Try to get data table server side Selesai SPPD
        try {
            $dataTables = $this->sppdService->getDataSppSelesai();
            // Check if data table is null
            if (is_null($dataTables)) {
                // Throw an invalid argument exception
                throw new \InvalidArgumentException('DataTables is null');
            }

            // Return data table
            return $dataTables;
        } catch (\InvalidArgumentException | \Throwable $th) {
            // Return error response with 500 status code
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Get data table server side batal SPPD.
     *
     * This method will return data table server side batal SPPD.
     *
     * @return \Yajra\DataTables\DataTables
     *
     * @throws \InvalidArgumentException
     *     If data table is null
     * @throws \Throwable
     *     If there is an error when getting data table server side batal SPPD
     */
    public function getBatal()
    {
        // Try to get data table server side batal SPPD
        try {
            $dataTables = $this->sppdService->getDataSppBatal();
            // Check if data table is null
            if (is_null($dataTables)) {
                // Throw an invalid argument exception
                throw new \InvalidArgumentException('DataTables is null');
            }

            // Return data table
            return $dataTables;
        } catch (\InvalidArgumentException | \Throwable $th) {
            // Return error response with 500 status code
            return response()->json([
                'message' => 'Terjadi kesalahan sistem',
                'errors' => $th->getMessage(),
            ], 500);
        }
    }

    public function getSppbCetakBuktiKas($id)
    {
        try {
            return $this->sppdService->getSppbCetakBuktiKas($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getSppnCetakBuktiKas($id)
    {
        try {
            return $this->sppdService->getSppnCetakBuktiKas($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getPenerima($id)
    {
        try {
            return $this->sppdService->getPenerima($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getDiterima($id)
    {
        try {
            return $this->sppdService->getDiterima($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getSppbBayar($id)
    {
        try {
            return $this->sppdService->getSppbBayar($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getSppnTerima($id)
    {
        try {
            return $this->sppdService->getSppnTerima($id);
        } catch (\Throwable $th) {
            return $th;
        }
    }
}
