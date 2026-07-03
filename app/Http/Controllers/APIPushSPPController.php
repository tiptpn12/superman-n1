<?php

namespace App\Http\Controllers;

use App\Spp;
use App\Sppb;
use App\Bagian;
use App\FakturPajak;
use App\IsiSppb;
use App\IsiSppn;
use App\RekamJejak;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\NamaKaryawanModel;
use App\Sppn;
use App\SppProses;
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

                if (count($validatedIsiSppb['isi_sppb']) <= 3) {
                    // Simpan data rekening dengan data inputan
                    $validatedRekening = [
                        'karyawan_nama' => $isi['karyawan_nama'],
                        'karyawan_nama_bank' => $isi['karyawan_nama_bank'],
                        'karyawan_no_rek' => $isi['karyawan_no_rek'],
                        'karyawan_alamat' => $isi['karyawan_alamat'],
                    ];

                    $validatedRekening['sppb_id'] = $sppb->sppb_id;
                    $rekening = new NamaKaryawanModel($validatedRekening);
                    $rekening->save();
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

            if (count($validatedIsiSppb['isi_sppb']) > 3) {
                // Simpan data rekening dengan data terlampir
                $validatedRekening = $request->validate([
                    'karyawan_nama' => 'nullable|string',
                    'karyawan_nama_bank' => 'nullable|string',
                    'karyawan_no_rek' => 'nullable|string',
                    'karyawan_alamat' => 'nullable|string',
                ]);

                $validatedRekening['sppb_id'] = $sppb->sppb_id;
                $rekening = new NamaKaryawanModel($validatedRekening);
                $rekening->save();
            }

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

    public function createSPPBnSPPN(Request $request) {
        try {
            DB::beginTransaction();

            $validatedSppRequest = $request->validate([
                'master_user_id' => 'required|integer',
                'spp_jenis' => 'required|string',
                'flow_id' => 'required|integer',
                'company_id' => 'required|integer',
                'spp_jenis_sumber_dana' => 'required|integer',
            ]);

            $validatedSppbRequest = $request->validate([
                'master_bagian_id' => 'required|integer',
                'sppb_kwitansi' => 'nullable|string',
                'sppb_referensi' => 'nullable|string',
                'sppb_berita_acara' => 'nullable|string',
                'sppb_sp_opl' => 'nullable|string',
                'sppb_faktur_pajak' => 'nullable|string',
                'sppb_metode_pembayaran' => 'nullable|string',
                'sppb_data_metpen' => 'nullable|string',
                'sppb_catatan' => 'nullable|string',
                'sppb_total' => 'nullable|string',
                'sppb_tidak_transfer' => 'nullable|string',
            ]);

            $validatedIsiSppbRequest = $request->validate([
                'isi_sppb' => 'required|array',
                'isi_sppb.*.master_kode_vendor_id' => 'nullable|string',
                'isi_sppb.*.master_gl_id' => 'nullable|string',
                'isi_sppb.*.master_customer_id' => 'nullable|string',
                'isi_sppb.*.master_cost_center_id' => 'nullable|string',
                'isi_sppb.*.master_profit_center_id' => 'nullable|string',
                'isi_sppb.*.master_cash_flow_id' => 'nullable|string',
                'isi_sppb.*.sppb_uraian' => 'nullable|array',
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_uraian' => 'nullable|string',
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_nominal' => 'nullable|integer',
                'isi_sppb.*.sppb_uraian.*.sppb_uraian_total' => 'nullable|integer',
            ]);

            $validatedSppnRequest = $request->validate([
                'master_bagian_id' => 'required|integer',
                'sppn_jenis' => 'required|string',
                'sppn_sumber_dana' => 'required|string',
                'sppn_kwitansi' => 'nullable|string',
                'sppn_referensi' => 'nullable|string',
                'sppn_ba_au_53' => 'nullable|string',
                'sppn_sp_opl' => 'nullable|string',
                'sppn_catatan' => 'nullable|string',
                'sppn_nama_bank' => 'nullable|string',
                'sppn_no_rek' => 'nullable|string',
            ]);

            $validatedIsiSppnRequest = $request->validate([
                'isi_sppn' => 'required|array',
                'isi_sppn.*.master_kode_vendor_id' => 'nullable|string',
                'isi_sppn.*.master_gl_id' => 'nullable|string',
                'isi_sppn.*.master_customer_id' => 'nullable|string',
                'isi_sppn.*.master_cost_center_id' => 'nullable|string',
                'isi_sppn.*.master_profit_center_id' => 'nullable|string',
                'isi_sppn.*.master_cash_flow_id' => 'nullable|string',
                'isi_sppn.*.sppn_uraian' => 'nullable|array',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_uraian' => 'nullable|string',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_nominal' => 'nullable|integer',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_total' => 'nullable|integer',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_pajak' => 'nullable|integer',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_total_pajak' => 'nullable|integer',
                'isi_sppn.*.sppn_uraian.*.sppn_uraian_potongan' => 'nullable|integer',
            ]);

            $validatedKaryawanRequest = $request->validate([
                'diterima_dari' => 'nullable|string',
                'alamat_sppn' => 'nullable|string',
                'pilih_data_sppb' => 'nullable|string',
                'pilih_data_sppn' => 'nullable|string',
                'metode_pembayaran_sppn' => 'nullable|string',
                'atas_nama_bank_sppn_kas' => 'nullable|array',
                'atas_nama_bank_sppn_kas.*' => 'nullable|string',
                'karyawan_sppn' => 'nullable|array',
                'karyawan_sppn.*.nama' => 'nullable|string',
                'karyawan_sppn.*.bank' => 'nullable|string',
                'karyawan_sppn.*.no_rek' => 'nullable|string',
                'karyawan_kas_sppb_input' => 'nullable|array',
                'penerima_kas_sppb_karyawan' => 'nullable|string',
                'karyawan_alamat_sppb_input' => 'nullable|string',
                'penerima_kas_sppb_karyawan_master' => 'nullable|string',
                'alamat_kas_sppb_karyawan_master' => 'nullable|string',
                'atas_nama_bank_sppb_kas' => 'nullable|array',
                'nama_kas_negara_sppb_input' => 'nullable|string',
                'alamat_kas_negara_sppb_input' => 'nullable|string',
                'atas_nama_bank_sppb_vendor' => 'nullable|string',
                'nama_bank_sppb_vendor' => 'nullable|string',
                'rekening_bank_sppb_vendor' => 'nullable|string',
                'karyawan_sppb' => 'nullable|array',
                'karyawan_sppb.*.nama' => 'nullable|string',
                'karyawan_sppb.*.bank' => 'nullable|string',
                'karyawan_sppb.*.no_rek' => 'nullable|string',
                'karyawan_sppb_input' => 'nullable|array',
                'karyawan_sppb_input.*.nama' => 'nullable|string',
                'karyawan_sppb_input.*.bank' => 'nullable|string',
                'karyawan_sppb_input.*.no_rek' => 'nullable|string',
                'pilih_data_sppb_vendor' => 'nullable|string',
            ]);

            $validatedFakturPajakRequest = $request->validate([
                'faktur_pajak_spp' => 'nullable|array',
                'faktur_pajak_spp.*.fp' => 'required|string',
            ]);

            $kodebagianb = Bagian::where('master_bagian_id', $validatedSppbRequest['master_bagian_id'])->select('master_bagian_kode')->first();
            $kodebagiann = Bagian::where('master_bagian_id', $validatedSppnRequest['master_bagian_id'])->select('master_bagian_kode')->first();

            if (!$kodebagianb || !$kodebagiann) {
                return response()->json(['error' => 'Bagian tidak ditemukan.'], 404);
            }

            $kodebagiansppb = $kodebagianb->master_bagian_kode;
            $kodebagiansppn = $kodebagiann->master_bagian_kode;

            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = Carbon::now()->format('Y-m-d');
            $tahun = Carbon::createFromFormat('Y-m-d', $tanggal)->year;
            $month = Carbon::createFromFormat('Y-m-d', $tanggal)->month;
            $bulan = $bulanromawi[$month];

            // Get urutan sppb
            $nomor_surat_sppb = DB::table('sppb')
                ->select(DB::raw('MAX(sppb_urutan) as maxno'))
                ->where('sppb_tahun', $tahun)
                ->where('master_bagian_id', $validatedSppbRequest['master_bagian_id'])
                ->first();
            $urutansppb = ($nomor_surat_sppb->maxno ?? 0) + 1;

            // Get urutan sppn
            $nomor_surat_sppn = DB::table('sppn')
                ->select(DB::raw('MAX(sppn_urutan) as maxnosppn'))
                ->where('sppn_tahun', $tahun)
                ->where('master_bagian_id', $validatedSppnRequest['master_bagian_id'])
                ->first();
            $urutansppn = ($nomor_surat_sppn->maxnosppn ?? 0) + 1;

            $nomorsppb = $kodebagiansppb . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;
            $nomorsppn = $kodebagiansppn . "/SPPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;

            $sppb = Sppb::create([
                'master_user_id' => $validatedSppRequest['master_user_id'],
                'master_bagian_id' => $validatedSppbRequest['master_bagian_id'],
                'sppb_jenis' => $validatedSppRequest['spp_jenis'],
                'sppb_no' => $nomorsppb,
                'sppb_urutan' => $urutansppb,
                'sppb_bulan' => $bulan,
                'sppb_tahun' => $tahun,
                'sppb_kwitansi' => $validatedSppbRequest['sppb_kwitansi'],
                'sppb_referensi' => $validatedSppbRequest['sppb_referensi'],
                'sppb_berita_acara' => $validatedSppbRequest['sppb_berita_acara'],
                'sppb_faktur_pajak' => 0,
                'sppb_sp_opl' => $validatedSppbRequest['sppb_sp_opl'],
                'sppb_tanggal' => $tanggal,
                'sppb_metode_pembayaran' => $validatedSppbRequest['sppb_metode_pembayaran'],
                'sppb_catatan' => $validatedSppbRequest['sppb_catatan'],
                'sppb_status' => 0,
                'sppb_total' => 0,
                'sppb_data_metpen' => $validatedSppbRequest['sppb_data_metpen'],
                'sppb_tidak_transfer' => $validatedSppbRequest['sppb_tidak_transfer'] ?? null,
            ]);

            $sum1 = 0;
            foreach ($validatedIsiSppbRequest['isi_sppb'] as $isi) {
                $isi['sppb_id'] = $sppb->sppb_id;
                $isiSppb = IsiSppb::create($isi);

                if (isset($isi['sppb_uraian'])) {
                    foreach ($isi['sppb_uraian'] as $uraian) {
                        $uraianData = [
                            'sppb_isi_id' => $isiSppb->sppb_isi_id,
                            'sppb_uraian_uraian' => $uraian['sppb_uraian_uraian'] ?? null,
                            'sppb_uraian_nominal' => $uraian['sppb_uraian_nominal'] ?? 0,
                            'sppb_nominal_pajak' => 0,
                            'sppb_nominal_akhir' => $uraian['sppb_uraian_total'] ?? $uraian['sppb_uraian_nominal'] ?? 0,
                            'sppb_tanpa_pajak' => 'Ya',
                        ];
                        IsiUraianSppb::create($uraianData);
                        $sum1 += $uraianData['sppb_nominal_akhir'];
                    }
                }
            }
            $sppb->sppb_total = $sum1;
            $sppb->save();

            $sppn = Sppn::create([
                'master_user_id' => $validatedSppRequest['master_user_id'],
                'master_bagian_id' => $validatedSppnRequest['master_bagian_id'],
                'sppn_jenis' => $validatedSppnRequest['sppn_jenis'],
                'sppn_sumber_dana' => $validatedSppnRequest['sppn_sumber_dana'],
                'sppn_no' => $nomorsppn,
                'sppn_urutan' => $urutansppn,
                'sppn_bulan' => $bulan,
                'sppn_tahun' => $tahun,
                'sppn_kwitansi' => $validatedSppnRequest['sppn_kwitansi'] ?? null,
                'sppn_referensi' => $validatedSppnRequest['sppn_referensi'] ?? null,
                'sppn_ba_au_53' => $validatedSppnRequest['sppn_ba_au_53'] ?? null,
                'sppn_faktur_pajak' => 0,
                'sppn_tanggal' => $tanggal,
                'sppn_no_rek' => $validatedSppnRequest['sppn_no_rek'] ?? null,
                'sppn_atas_nama' => 0,
                'sppn_nama_bank' => $validatedSppnRequest['sppn_nama_bank'] ?? null,
                'sppn_sp_opl' => $validatedSppnRequest['sppn_sp_opl'] ?? "-",
                'sppn_catatan' => $validatedSppnRequest['sppn_catatan'] ?? null,
                'sppn_status' => 0,
                'sppn_jumlah' => 0,
            ]);

            $total1 = 0;
            foreach ($validatedIsiSppnRequest['isi_sppn'] as $isi) {
                $isi['sppn_id'] = $sppn->sppn_id;
                $isiSppn = IsiSppn::create($isi);

                if (isset($isi['sppn_uraian'])) {
                    foreach ($isi['sppn_uraian'] as $uraian) {
                        $uraianData = [
                            'sppn_isi_id' => $isiSppn->sppn_isi_id,
                            'sppn_uraian_uraian' => $uraian['sppn_uraian_uraian'] ?? null,
                            'sppn_uraian_nominal' => $uraian['sppn_uraian_nominal'] ?? 0,
                            'sppn_nominal_pajak' => $uraian['sppn_uraian_pajak'] ?? 0,
                            'sppn_nominal_akhir' => $uraian['sppn_uraian_total_pajak'] ?? $uraian['sppn_uraian_total'] ?? $uraian['sppn_uraian_nominal'] ?? 0,
                            'sppn_potongan' => $uraian['sppn_uraian_potongan'] ?? 0,
                        ];
                        IsiUraianSppn::create($uraianData);
                        $total1 += $uraianData['sppn_nominal_akhir'];
                    }
                }
            }
            $sppn->sppn_jumlah = $total1;
            $sppn->save();

            $spp = Spp::create([
                'sppb_id' => $sppb->sppb_id,
                'sppn_id' => $sppn->sppn_id,
                'master_bagian_id' => $validatedSppbRequest['master_bagian_id'],
                'spp_status_ob' => 0,
                'sppd_proses' => 0,
                'sppd_status' => 0,
                'flow_id' => $validatedSppRequest['flow_id'],
                'sppd_posisi' => 34,
                'spp_jenis_sumber_dana' => $validatedSppRequest['spp_jenis_sumber_dana'],
                'spp_status_proses' => 0,
                'spp_status_posisi' => 1,
                'spp_status_bayar' => 0,
                'spp_buat' => 34,
                'spp_status_terima' => 0,
                'spp_tanggal' => $tanggal,
                'company_id' => $validatedSppRequest['company_id'],
                'spp_apk_bpd' => 1,
                'sppb_faktur_pajak' => 0,
            ]);

            if (isset($validatedFakturPajakRequest['faktur_pajak_spp'])) {
                foreach ($validatedFakturPajakRequest['faktur_pajak_spp'] as $value) {
                    FakturPajak::create([
                        'sppb_id' => $sppb->sppb_id,
                        'sppn_id' => $sppn->sppn_id,
                        'faktur_pajak_nomor' => $value['fp'],
                    ]);
                }
            }

            NamaKaryawanModel::create([
                'sppb_id' => null,
                'sppn_id' => $sppn->sppn_id,
                'karyawan_nama' => $validatedKaryawanRequest['diterima_dari'] ?? '-',
                'karyawan_nama_bank' => '-',
                'karyawan_no_rek' => '-',
                'karyawan_alamat' => $validatedKaryawanRequest['alamat_sppn'] ?? '-',
            ]);

            $metodeSppb = $validatedSppbRequest['sppb_metode_pembayaran'] ?? '';
            $pilihVendor = $validatedKaryawanRequest['pilih_data_sppb_vendor'] ?? '';
            if ($metodeSppb == 'kas_negara') {
                NamaKaryawanModel::create([
                    'sppb_id' => $sppb->sppb_id,
                    'sppn_id' => null,
                    'karyawan_nama' => $validatedKaryawanRequest['nama_kas_negara_sppb_input'] ?? null,
                    'karyawan_nama_bank' => "-",
                    'karyawan_no_rek' => "-",
                    'karyawan_alamat' => $validatedKaryawanRequest['alamat_kas_negara_sppb_input'] ?? null,
                ]);
            } elseif ($metodeSppb == 'kas') {
                NamaKaryawanModel::create([
                    'sppb_id' => $sppb->sppb_id,
                    'sppn_id' => null,
                    'karyawan_nama' => $validatedKaryawanRequest['nama_kas_negara_sppb_input'] ?? null,
                    'karyawan_nama_bank' => "-",
                    'karyawan_no_rek' => "-",
                    'karyawan_alamat' => $validatedKaryawanRequest['alamat_kas_negara_sppb_input'] ?? null,
                ]);
            } else {
                if ($pilihVendor == 'input_data') {
                    NamaKaryawanModel::create([
                        'sppb_id' => $sppb->sppb_id,
                        'sppn_id' => null,
                        'karyawan_nama' => $validatedKaryawanRequest['atas_nama_bank_sppb_vendor'] ?? null,
                        'karyawan_nama_bank' => $validatedKaryawanRequest['nama_bank_sppb_vendor'] ?? null,
                        'karyawan_no_rek' => $validatedKaryawanRequest['rekening_bank_sppb_vendor'] ?? null,
                        'karyawan_alamat' => $validatedKaryawanRequest['karyawan_alamat_sppb_input'] ?? null,
                    ]);
                }
            }

            $rekamJejak = RekamJejak::create([
                'spp_id' => $spp->spp_id,
                'master_user_id' => 34,
                'master_user_id_asal' => $validatedSppRequest['master_user_id'],
                'master_user_id_tujuan' => 0,
                'rekam_jejak_status' => 0,
                'rekam_jejak_waktu' => Carbon::now(),
                'rekam_jejak_revisi' => null,
            ]);

            $spp_proses = SppProses::create([
                'spp_id' => $spp->spp_id,
                'spp_proses_operator_bagian' => 1,
                'spp_proses_kepala_bagian' => 1
            ]);

            DB::commit();

            return response()->json([
                'dataSpp' => $spp,
                'dataSppb' => $sppb,
                'dataSppn' => $sppn,
                'dataRekamJejak' => $rekamJejak,
                'spp_proses' => $spp_proses,
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
