<?php

namespace App\Http\Controllers;

use App\Spp;
use App\Sppb;
use App\Bagian;
use App\FakturPajak;
use App\IsiSppb;
use App\RekamJejak;
use App\IsiUraianSppb;
use App\NamaKaryawanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class APIPushSPPController extends Controller
{


    public function createSPPnSPPB(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validasi input untuk SPPB
            $validatedSppb = $request->validate([
                'master_user_id' => 'required|integer',
                'master_bagian_id' => 'required|integer',
                'sppb_jenis' => 'required|string',
                'sppb_kwitansi' => 'nullable|string',
                'sppb_referensi' => 'nullable|string',
                'sppb_berita_acara' => 'nullable|string',
                'sppb_sp_opl' => 'nullable|string',
                'sppb_faktur_pajak' => 'nullable|string',
                'sppb_metode_pembayaran' => 'nullable|string',
                'sppb_data_metpen' => 'nullable|string',
                'sppb_catatan' => 'nullable|string',
                'sppb_total' => 'nullable|string',
            ]);

            // Validasi input untuk isi_sppb
            $validatedIsiSppb = $request->validate([
                'isi_sppb' => 'required|array',
                'isi_sppb.*.master_kode_vendor_id' => 'nullable|string',
                'isi_sppb.*.master_gl_id' => 'nullable|string',
                'isi_sppb.*.master_customer_id' => 'nullable|string',
                'isi_sppb.*.master_cost_center_id' => 'nullable|string',
                'isi_sppb.*.master_profit_center_id' => 'nullable|string',
                'isi_sppb.*.master_cash_flow_id' => 'nullable|string',
                'isi_sppb.*.sppb_uraian' => 'nullable|array', // Validasi uraian dalam setiap isi_sppb
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_uraian' => 'nullable|string', // Validasi field uraian
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_nominal' => 'nullable|integer', // Validasi field uraian
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_total' => 'nullable|integer', // Validasi field uraian

            ]);

            // Ambil data bagian berdasarkan master_bagian_id
            $kodebagiansppb = Bagian::where('master_bagian_id', $request->master_bagian_id)->select('master_bagian_kode')->first();

            if (!$kodebagiansppb) {
                return response()->json(['error' => 'Bagian tidak ditemukan.'], 404);
            }

            $kodebagian = $kodebagiansppb->master_bagian_kode;

            // Perhitungan nomor sppb dan tanggal
            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::createFromFormat('Y-m-d', $tanggal)->year;
            $month = Carbon::createFromFormat('Y-m-d', $tanggal)->month;
            $bulan = $bulanromawi[$month];

            $nomor_surat = DB::table('sppb')
                ->select(DB::raw('MAX(sppb_urutan) as maxno'))
                ->where('sppb_tahun', $tahun)
                ->where('master_bagian_id', $request->master_bagian_id)
                ->first();

            $urutansppb = $nomor_surat->maxno + 1;
            $nomor = $kodebagian . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;

            // Simpan data SPPB
            $validatedSppb['sppb_no'] = $nomor;
            $validatedSppb['sppb_urutan'] = $urutansppb;
            $validatedSppb['sppb_bulan'] = $bulan;
            $validatedSppb['sppb_tahun'] = $tahun;
            $validatedSppb['sppb_tanggal'] = $tanggal;

            $sppb = new Sppb($validatedSppb);
            $sppb->save();

            // Simpan data isi_sppb
            $sppbIsiIds = [];
            foreach ($validatedIsiSppb['isi_sppb'] as $isi) {
                $isi['sppb_id'] = $sppb->sppb_id;
                $isiSppb = IsiSppb::create($isi);
                $sppbIsiIds[] = $isiSppb->sppb_isi_id; // Simpan ID untuk digunakan nanti

                // Simpan data uraian untuk setiap isi_sppb
                if (isset($isi['sppb_uraian'])) {
                    foreach ($isi['sppb_uraian'] as $uraian) {
                        $uraian['sppb_isi_id'] = $isiSppb->sppb_isi_id;
                        $uraian['sppb_nominal_pajak'] = 0;
                        $uraian['sppb_nominal_akhir'] = $uraian['sppb_uraian_nominal'];
                        IsiUraianSppb::create($uraian);
                    }
                }
            }

            // Validasi input untuk SPP
            $validatedSpp = $request->validate([
                'master_bagian_id' => 'required|integer',
                'flow_id' => 'required|integer',
                'company_id' => 'required|integer',
                'spp_jenis_sumber_dana' => 'required|integer',
            ]);

            // Simpan data SPP dengan ID SPPB yang baru disimpan
            $validatedSpp['sppb_id'] = $sppb->sppb_id;
            $validatedSpp['spp_tanggal'] = $tanggal;
            $validatedSpp['sppd_proses'] = 0;
            $validatedSpp['sppd_posisi'] = 34;
            $validatedSpp['spp_buat'] = 34;
            $validatedSpp['spp_apk_bpd'] = 1;
            $validatedSpp['sppb_faktur_pajak'] = 0;
            $spp = new Spp($validatedSpp);
            $spp->save();

            // Simpan faktur pajak dari sppb
            $validatedFakturPajak['sppb_id'] = $sppb->sppb_id;
            $validatedFakturPajak['faktur_pajak_nomor'] = $sppb->sppb_faktur_pajak;
            $fakturPajak = new FakturPajak($validatedFakturPajak);
            $fakturPajak->save();

            // ** menyimpan rekam jejak **
            $dataRekamJejak = [
                'spp_id' => $spp->spp_id,
                'master_user_id_asal' => $request->master_user_id,
                'master_user_id' => 34,
                'master_user_id_tujuan' => 0,
                'rekam_jejak_status' => 0,
                'rekam_jejak_waktu' => Carbon::now(),
            ];

            $rekamJejak = new RekamJejak($dataRekamJejak);
            $rekamJejak->save();

            // Simpan data rekening
            $validatedRekening = $request->validate([
                'karyawan_nama' => 'nullable|string',
                'karyawan_nama_bank' => 'nullable|string',
                'karyawan_no_rek' => 'nullable|string',
                'karyawan_alamat' => 'nullable|string',
            ]);

            $validatedRekening['sppb_id'] = $sppb->sppb_id;
            $rekening = new NamaKaryawanModel($validatedRekening);
            $rekening->save();

            DB::commit();

            // Mengembalikan respons JSON jika berhasil
            return response()->json([
                'dataSpp' => $spp,
                'dataSppb' => $sppb,
                'dataRekamJejak' => $rekamJejak,
                'dataRekening' => $rekening,
                'dataIsiSppb' => $validatedIsiSppb['isi_sppb'],
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Data yang dikirimkan tidak valid.',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Terjadi kesalahan saat insert tabel.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
