<?php

namespace App\Http\Controllers;

use App\GL;
use App\Bagian;
use App\CashFlow;
use App\Customer;
use App\Rekening;
use App\CostCenter;
use App\SumberDana;
use App\ProfitCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;



class MasterAPIController extends Controller
{

    public function getSPP(Request $request)
    {
        $bagian = $request->input('bagian');
        $akses = $request->input('akses');
        $flow = $request->input('flow'); // Pastikan ini berupa array atau set sesuai kebutuhan
        $page = $request->input('page');
        $pageSize = $request->input('pageSize');
        $keyword = $request->input('keyword');

        try {
            $baseQuery = DB::table('spp')->where('spp.master_bagian_id', '=', $bagian)
                // ->where('spp.sppd_posisi', $akses)
                ->whereBetween('spp.sppd_status', [0, 2])
                ->where('spp.flow_id', $flow)
                ->where('spp.spp_apk_bpd', 1)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('sppb_isi', 'sppb.sppb_id', '=', 'sppb_isi.sppb_id')->leftJoin('sppb_uraian', 'sppb_isi.sppb_isi_id', '=', 'sppb_uraian.sppb_isi_id')
                ->leftJoin('sppn_isi', 'sppn.sppn_id', '=', 'sppn_isi.sppn_id')->leftJoin('sppn_uraian', 'sppn_isi.sppn_isi_id', '=', 'sppn_uraian.sppn_isi_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->select('master_hak_akses_nama', 'spp_no_dokumen', 'spp_id', 'spp.sppb_id', 'spp.sppn_id', 'sppd_revisi', 'sppd_status', 'sppd_posisi', 'sppd_proses', 'spp_kabag', 'master_bagian_nama', 'spp_status_proses', 'spp_status_posisi', DB::raw("DATE_FORMAT(spp.spp_tanggal,'%d-%m-%Y') as tanggal"), 'sppb_no', 'sppb_tanggal', 'sppb_total', 'sppn_no', 'sppn_tanggal', 'sppn_jumlah', 'sppd_status', DB::raw("GROUP_CONCAT(DISTINCT sppb_uraian_uraian SEPARATOR ',') as sppb_uraian2"), DB::raw("GROUP_CONCAT(DISTINCT sppn_uraian_uraian SEPARATOR ',') as sppn_uraian2"))
                ->groupBy('spp_id', 'spp_tanggal')
                ->havingRaw("CONCAT(coalesce(spp_no_dokumen, ''), coalesce(sppb_no, ''), coalesce(tanggal, ''), coalesce(sppb_tanggal, ''), coalesce(sppb_total, ''), coalesce(sppn_tanggal, ''), coalesce(sppn_jumlah, ''), coalesce(sppb_uraian2, ''), coalesce(sppn_uraian2, '')) like '%" . $keyword . "%'");

            $total = (clone $baseQuery)->get()->count();


            $data = $baseQuery->orderBy('spp_tanggal', 'desc')
                    ->offset(($page - 1) * $pageSize)
                    ->limit($pageSize)
                    ->get();

            return response()->json(['data' => compact('total', 'data')], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function updateSppApkBpd(Request $request)
    {
        $spp_id = $request->input('spp_id');

        try {
            $updated = DB::table('spp')
                ->where('spp_id', $spp_id)
                ->update([
                    'sppd_status' => 5
                ]);

            if ($updated) {
                return response()->json(['message' => 'SPP Berhasil dibatalkan'], 200);
            } else {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat update. ' . $e->getMessage()], 500);
        }
    }

    public function getStatusSPP(Request $request)
    {
        $spp_id = $request->input('spp_id');

        try {
            $data = DB::table('spp')
                ->where('spp.spp_id', $spp_id)
                ->leftJoin('sppb', 'spp.sppb_id', '=', 'sppb.sppb_id')
                ->leftJoin('sppn', 'spp.sppn_id', '=', 'sppn.sppn_id')
                ->leftJoin('master_bagian', 'spp.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'spp.sppd_posisi')
                ->leftJoin('master_status_bayar', 'master_status_bayar.kode', '=', 'spp.spp_status_bayar')
                ->leftJoin('master_status_terima', 'master_status_terima.kode', '=', 'spp.spp_status_terima')
                ->select(
                    'spp_id',
                    'master_hak_akses_nama',
                    'sppb_no',
                    'sppn_no',
                    DB::raw("CASE WHEN sppb_no IS NOT NULL AND sppb_no <> '' THEN master_status_bayar.keterangan_bayar ELSE NULL END AS status_bayar"),
                    DB::raw("CASE WHEN sppn_no IS NOT NULL AND sppn_no <> '' THEN master_status_terima.keterangan_terima ELSE NULL END AS status_terima")
                )
                ->get();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function view_detail_bpd($id_spp, $username_bpd)
    {

        $id = base64_decode($id_spp);
        $username = base64_decode($username_bpd);

        if ($username == 'petugas-bpd-umum' || $username == 'op_bpd') {

            $idspp = DB::table('spp')->where('spp.spp_id', '=', $id)
                ->leftJoin('master_sumber_dana', 'spp.spp_jenis_sumber_dana', '=', 'master_sumber_dana.sumber_dana_id')
                ->select('spp.*', 'master_sumber_dana.*')->first();
            $doktam = DB::table('dokumen_tambahan')->where('dokumen_tambahan.spp_id', '=', $id)
                ->join('master_hak_akses', 'dokumen_tambahan.master_hak_akses_id', '=', 'master_hak_akses.master_hak_akses_level')
                ->select('dokumen_tambahan.*', 'master_hak_akses.*')->get();
            $idsppb = $idspp->sppb_id;
            $idsppn = $idspp->sppn_id;


            if ($idsppb != null) {
                $datasppb = DB::table('sppb')->where('sppb_id', '=', $idsppb)
                    ->leftJoin('master_bagian', 'sppb.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('sppb.*', 'master_bagian.*')->first();
                $sppbisi = DB::table('sppb_isi')->where('sppb_isi.sppb_id', '=', $datasppb->sppb_id)
                    ->where('sppb_isi.master_cash_flow_id', '=', 260)
                    ->leftJoin('master_rekening', 'sppb_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_customer', 'sppb_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_gl', 'sppb_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_cost_center', 'sppb_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_profit_center', 'sppb_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppb_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppb_isi.*', 'master_customer.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*')->get();
                $sppb_bayar = DB::table('sppb_bayar')->where('sppb_bayar.sppb_id', '=', $idsppb)->select('sppb_bayar.*')->first();
                $faktur_pajak_sppb = DB::table('faktur_pajak')->where('faktur_pajak.sppb_id', '=', $datasppb->sppb_id)->select('faktur_pajak_nomor')->get();
                // $nomorsap = DB::table('sap')->where('spp_id','=', $id)->select('sap.nomor_sap')->get();
                $dokpensppb = DB::table('dokumen_pendukung_sppb')->where('dokumen_pendukung_sppb.sppb_id', '=', $datasppb->sppb_id)
                    ->select('dokumen_pendukung_sppb.dokumen_pendukung_sppb_nama')->get();
                // dd($dokpensppb);
                foreach ($sppbisi as $a => $value2) {
                    $sppburaian[] = DB::table('sppb_uraian')->where('sppb_uraian.sppb_isi_id', '=', $value2->sppb_isi_id)->select('sppb_uraian.*')->get();
                }
                // dd($sppburaian);
                $isisppb = [];
                foreach ($sppbisi as $s => $val) {
                    $isisppb[] = collect($val)->push($sppburaian[$s]);
                }
                $data_sppb = [];
                $data_sppb = collect($datasppb)->push($isisppb)->push($faktur_pajak_sppb);
                // dd($data_sppb);
            } else {
                $data_sppb = [];
                $dokpensppb = [];
                $sppb_bayar = null;
            }
            if ($idsppn != null) {
                $datasppn = DB::table('sppn')
                    ->where('sppn_id', '=', $idsppn)->leftJoin('master_bagian', 'sppn.master_bagian_id', '=', 'master_bagian.master_bagian_id')
                    ->select('sppn.*', 'master_bagian.*')->first();

                $sppnisi = DB::table('sppn_isi')->where('sppn_isi.sppn_id', '=', $idsppn)
                    ->where('sppb_isi.master_cash_flow_id', '=', 260)
                    ->leftjoin('master_rekening', 'sppn_isi.master_kode_vendor_id', '=', 'master_rekening.master_rekening_id')
                    ->leftJoin('master_cost_center', 'sppn_isi.master_cost_center_id', '=', 'master_cost_center.master_cost_center_id')
                    ->leftJoin('master_customer', 'sppn_isi.master_customer_id', '=', 'master_customer.master_customer_id')
                    ->leftJoin('master_gl', 'sppn_isi.master_gl_id', '=', 'master_gl.master_gl_id')
                    ->leftJoin('master_profit_center', 'sppn_isi.master_profit_center_id', '=', 'master_profit_center.master_profit_center_id')
                    ->leftJoin('master_cash_flow', 'sppn_isi.master_cash_flow_id', '=', 'master_cash_flow.master_cash_flow_id')
                    ->select('sppn_isi.*', 'master_customer.*', 'master_rekening.*', 'master_cost_center.*', 'master_profit_center.*', 'master_cash_flow.*', 'master_gl.*')->get();
                // dd($sppnisi);
                $sppn_terima = DB::table('sppn_terima')->where('sppn_terima.sppn_id', '=', $idsppn)->select('sppn_terima.*')->first();
                $faktur_pajak_sppn = DB::table('faktur_pajak')->where('faktur_pajak.sppn_id', '=', $datasppn->sppn_id)->select('faktur_pajak_nomor')->get();

                // $nomorsap = DB::table('sap')->where('spp_id','=', $id)->select('sap.nomor_sap')->get();

                $dokpensppn = DB::table('dokumen_pendukung_sppn')->where('sppn_id', '=', $datasppn->sppn_id)
                    ->select('dokumen_pendukung_sppn.*')->get();

                foreach ($sppnisi as $a => $value1) {
                    $sppnuraian[] = DB::table('sppn_uraian')->where('sppn_uraian.sppn_isi_id', '=', $value1->sppn_isi_id)->select('sppn_uraian.*')->get();
                }

                $isisppn = [];
                foreach ($sppnisi as $s => $val) {
                    $isisppn[] = collect($val)->push($sppnuraian[$s]);
                }

                $data_sppn = [];
                $data_sppn = collect($datasppn)->push($isisppn)->push($faktur_pajak_sppn);
                // dd($data_sppb,$data_sppn);
            } else {
                $data_sppn = null;
                $dokpensppn = null;
                $sppn_terima = null;
            }
            $client = new Client();
            $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
            $response = $client->request('GET', $url, [
                'verify' => false,
            ]);
            $karyawan_all = json_decode($response->getBody());

            $form = 0;
            if (isset($data_sppb) && empty($data_sppn)) {
                $form = 1;
                if ($data_sppb['sppb_jenis'] == "karyawan") {
                    $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                    foreach ($Krywn as $k => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }
                    if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                        foreach ($karyawan_sppb as $k => $v) {
                            foreach ($v as $k1 => $v2) {
                                $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppb = null;
                    }
                    $karyawan_sppb = $Krywn;
                } else {
                    $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                    $karyawan_no_vendor_sppb = null;
                }
                $karyawan_no_vendor_sppn = null;
                $karyawan_sppn = null;
            } elseif (isset($data_sppn) && empty($data_sppb)) {
                $form = 2;
                if ($data_sppn['sppn_jenis'] == "karyawan") {
                    $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                    foreach ($Krywn as $k => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }
                    if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                        foreach ($karyawan_sppn as $k => $v) {
                            foreach ($v as $k1 => $v2) {
                                $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppn = null;
                    }
                    $karyawan_sppn = $Krywn;
                } else {
                    $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                    $karyawan_no_vendor_sppn = null;
                }
                $karyawan_no_vendor_sppb = null;
                $karyawan_sppb = null;
            } else {
                $form = 3;
                if ($data_sppb['sppb_jenis'] == "karyawan") {
                    $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                    foreach ($Krywn as $k => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppb[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }
                    if (isset($karyawan_sppb) && $karyawan_sppb[0] !== []) {
                        foreach ($karyawan_sppb as $k => $v) {
                            foreach ($v as $k1 => $v2) {
                                $karyawan_no_vendor_sppb[] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppb = null;
                    }
                    $karyawan_sppb = $Krywn;
                } else {
                    $karyawan_sppb = DB::table('nama_karyawan')->where('nama_karyawan.sppb_id', '=', $data_sppb['sppb_id'])->select('nama_karyawan.*')->get();
                    $karyawan_no_vendor_sppb = null;
                }
                if ($data_sppn['sppn_jenis'] == "karyawan") {
                    $Krywn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                    foreach ($Krywn as $k => $val) {
                        $nama = $val->karyawan_nama;
                        $karyawan_sppn[] = Arr::where($karyawan_all, function ($value, $key) use ($nama) {
                            return $value->karyawan_nama == $nama;
                        });
                    }
                    if (isset($karyawan_sppn) && $karyawan_sppn[0] !== []) {
                        foreach ($karyawan_sppn as $k => $v) {
                            foreach ($v as $k1 => $v2) {
                                $karyawan_no_vendor_sppn[] = $v2->karyawan_no_vendor;
                            }
                        }
                    } else {
                        $karyawan_no_vendor_sppn = null;
                    }
                    $karyawan_sppn = $Krywn;
                } else {
                    $karyawan_sppn = DB::table('nama_karyawan')->where('nama_karyawan.sppn_id', '=', $data_sppn['sppn_id'])->select('nama_karyawan.*')->get();
                    $karyawan_no_vendor_sppn = null;
                }
            }
            $gl = GL::All();
            $rekening = Rekening::All();

            // dd($idspp);
            $data = array(
                'spp' => $idspp,
                'sppb' => $data_sppb,
                'sppn' => $data_sppn,
                'dokpensppb' => $dokpensppb,
                'dokpensppn' => $dokpensppn,
                'formspp' => $form,
                'doktam' => $doktam,
                'dok_kabag' => $idspp->spp_kabag,
                'id' => $idspp->spp_id,
                'status' => $idspp->spp_status_proses,
                'sppb_bayar' => $sppb_bayar,
                'sppn_terima' => $sppn_terima,
                'no_vendor_sppb' => $karyawan_no_vendor_sppb,
                'no_vendor_sppn' => $karyawan_no_vendor_sppn,
                'gl' => $gl,
                'rekening' => $rekening,
                // 'nomor_sap' => $nomorsap,
            );
            return view('page.spp.spp_detail', $data);
        }
    }

    public function rekam_jejak_bpd($id_spp, $username_bpd)
    {
        $sppID = base64_decode($id_spp);
        $username = base64_decode($username_bpd);

        if ($username == 'petugas-bpd-umum' || $username == 'op_bpd') {

            $rekam_jejak = DB::table('rekam_jejak')->where('spp_id', '=', $sppID)
                ->leftjoin('master_hak_akses AS asal', 'master_user_id', '=', 'asal.master_hak_akses_level')
                ->leftjoin('master_hak_akses AS tujuan', 'master_user_id_tujuan', '=', 'tujuan.master_hak_akses_level')
                ->select('rekam_jejak.*', 'asal.master_hak_akses_keterangan as asal', 'tujuan.master_hak_akses_keterangan as tujuan')
                ->groupBy('rekam_jejak_waktu')->orderBy('rekam_jejak_waktu', 'asc')->get();
            // dd($sppID);

            return view('page.spp.spp_rekam_jejak', compact('rekam_jejak'));
        }
    }

    public function getProfitCenter()
    {
        try {
            $data = ProfitCenter::all();

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getSapCustomer()
    {
        try {
            $data = Customer::all();

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getCashFlow()
    {
        try {
            $data = CashFlow::where('master_cash_flow_key', '=', 'A0209007')->get();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getSapGL()
    {
        try {
            $data = GL::all();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getSapVendor()
    {
        try {
            $data = Rekening::all();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getSumberDana()
    {
        try {
            $data = SumberDana::all();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getCostCenter()
    {
        try {
            $data = CostCenter::all();


            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }

    public function getBagianCreateSPP()
    {
        try {
            $data = Bagian::with(['company', 'company.companyDetail'])->where('master_bagian_id', '=', '219')->get();

            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data. ' . $e->getMessage()], 500);
        }
    }
}
