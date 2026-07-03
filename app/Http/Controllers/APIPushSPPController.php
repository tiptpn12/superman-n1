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
            $validatedSppRequest = $request->validate([
                'master_user_id' => 'required',
                'spp_jenis' => 'required|string',
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
                'master_bagian_id' => 'required',
                'sppn_jenis' => 'required',
                'sppn_sumber_dana' => 'required',
            ]);

            $validatedIsiSppnRequest = $request->validated([
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
                'isi_sppb.*.sppn_uraian.*.sppn_uraian_pajak' => 'nullable|integer',
                'isi_sppb.*.sppn_uraian.*.sppn_uraian_total_pajak' => 'nullable|integer',
                'isi_sppb.*.sppn_uraian.*.sppn_uraian_potongan' => 'nullable|integer',
            ]);

            DB::beginTransaction();
            $kodebagianb = Bagian::where('master_bagian_id', $validatedSppbRequest['master_bagian_id'])->select('master_bagian_kode')->first();
            $kodebagiann = Bagian::where('master_bagian_id', $validatedSppnRequest['master_bagian_id'])->select('master_bagian_kode')->first();
            $kodebagiansppb = $kodebagianb->master_bagian_kode;
            $kodebagiansppn = $kodebagiann->master_bagian_kode;

            $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
            $tanggal = $request->tanggal_sppb;
            $tanggals = date('Y-m-d', strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;

            $bulan = $bulanromawi[$month];


            $nomor_surat_sppb = DB::table('sppb')
                ->select('sppb_urutan', 'sppb_tahun', DB::raw('MAX(sppb_urutan) as maxno'))
                // ->where('sppb_tahun', $tahun)
                ->where('master_bagian_id', $request->bagian_sppb)
                ->first();
            $nomor_surat_sppn = DB::table('sppn')
                ->select('sppn_urutan', 'sppn_tahun', DB::raw('MAX(sppn_urutan) as maxnosppn'))
                // ->where('sppn_tahun', $tahun)
                ->where('master_bagian_id', $request->bagian_sppn)
                ->first();
            if ($day == 3 && $month == 1 && $tahun != $nomor_surat_sppb->sppb_tahun) {
                $urutansppb = 1;
                if ($day == 3 && $month == 1 && $tahun != $nomor_surat_sppn->sppn_tahun) {
                    $urutansppn = 1;
                } else {
                    $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
                }

            } else {
                $urutansppb = $nomor_surat_sppb->maxno + 1;
                $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
            }

            $nomorsppb = $kodebagiansppb . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;
            $nomorsppn = $kodebagiansppn . "/SPPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;

            $sppb = Sppb::create([
                'master_user_id' => $validatedSppRequest['master_user_id'],
                'master_bagian_id' => $validatedSppbRequest['master_bagian_id'],
                'sppb_jenis' => $validatedSppbRequest['sppb_jenis'],
                'sppb_no' => $nomorsppb,
                'sppb_urutan' => $urutansppb,
                'sppb_bulan' => $bulan,
                'sppb_tahun' => $tahun,
                'sppb_kwitansi' => $validatedSppbRequest['sppb_kwitansi'],
                'sppb_referensi' => $validatedSppbRequest['sppb_referensi'],
                'sppb_au_53' => $request->au53_sppb,
                'sppb_berita_acara' => $request->berita_acara_sppb,
                'sppb_faktur_pajak' => 0,
                'sppb_sp_opl' => $request->sp_opl_sppb,
                'sppb_tanggal' => $tanggals,
                'sppb_metode_pembayaran' => $request->metode_pembayaran_sppb,
                'sppb_no_rek' => $request->rekening_bank_sppb,
                'sppb_atas_nama' => 0,
                'sppb_nama_bank' => $request->nama_bank_sppb,
                'sppb_catatan' => $request->catatan_sppb,
                'sppb_status' => 0,
                'sppb_total' => 0,
                'sppb_data_metpen' => $request->data_metpen,
                'sppb_tidak_transfer' => $request->tidak_transfer_cat
            ]);

            $request->request->add(['sppb_id' => $sppb->sppb_id]);

            $isisppb = $request->isi_sppb;

            $sum1 = 0;
            $sum2 = 0;
            foreach ($isisppb as $isi => $value) {

                $isisppb = new isisppb;
                $isisppb->sppb_id = $request->sppb_id;

                if ($value['jenis_sap'] == 'vendor') {
                    $isisppb->master_kode_vendor_id = $value['vendor'];
                } else if ($value['jenis_sap'] == 'gl') {
                    $isisppb->master_gl_id = $value['gl'];
                } else {
                    $isisppb->master_customer_id = $value['customer'];
                }

                if ($value['jenis_center'] == 'cost_center') {
                    $isisppb->master_cost_center_id = $value['cost_center'];
                } else {
                    $isisppb->master_profit_center_id = $value['profit_center'];
                }
                $isisppb->master_cash_flow_id = $value['cash_flow'];
                $isisppb->save();
                $request->request->add(['sppb_isi_id' => $isisppb->sppb_isi_id]);
                foreach ($request->uraian_sppb[$isi] as $urai => $value2) {
                    $a = str_replace('.', '', $value2['jumlah']);
                    $angka_pajak = str_replace('.', '', $value2['pajak']);
                    $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                    $angka_potongan = str_replace('.', '', $value2['potongan']);
                    $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                    if (isset($value2['type_pajak_sppb'])) {
                        $tanpapajak = substr($value2['type_pajak_sppb'], 0, 9);
                        if ($tanpapajak == 'tanpa_paj') {
                            $b = $a;
                        } else if ($angka_akhir != null) {
                            $b = $angka_akhir;
                        } else {
                            $b = $a;
                        }
                    } else {
                        if ($angka_akhir != null) {
                            $b = $angka_akhir;
                        } else {
                            $b = $a;
                        }
                    }
                    $isiuraiansppb = new IsiUraiansppb;
                    $isiuraiansppb->sppb_isi_id = $request->sppb_isi_id;
                    $isiuraiansppb->sppb_uraian_uraian = $value2['ket'];
                    $isiuraiansppb->sppb_pajak_manual = $value2['manual'];
                    $isiuraiansppb->sppb_uraian_pph = str_replace('.', '', $value2['pph']);
                    $isiuraiansppb->sppb_uraian_nominal = $a;
                    $isiuraiansppb->sppb_nominal_pajak = $angka_pajak;
                    $isiuraiansppb->sppb_nominal_akhir = $b;
                    $isiuraiansppb->sppb_dpp_ppn = $angka_dpp_ppn;
                    $isiuraiansppb->sppb_potongan = $angka_potongan;
                    if (isset($value2['type_pajak_sppb'])) {
                        $jenispajak = substr($value2['type_pajak_sppb'], 0, 9);  //substring untuk mengambil type pajak
                        if ($jenispajak == 'wapu_sppb') {
                            $jeniswapu = substr($value2['pilih_wapu_sppb'], 0, 12); // substring untuk mengambil pilih wapu
                            if ($jeniswapu == 'wapu_pph21_a') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 21 2,5%";
                            } else if ($jeniswapu == 'wapu_pph21_b') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 21 7,5%";
                            } else if ($jeniswapu == 'wapu_pph21_c') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 21 12,5%";
                            } else if ($jeniswapu == 'wapu_pph22_a') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 22 1,5%";
                            } else if ($jeniswapu == 'wapu_pph23_a') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 23 2%";
                            } else if ($jeniswapu == 'wapu_pph23_b') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 23 15%";
                            } else if ($jeniswapu == 'wapu_pph23_c') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 23 0%";
                            } else if ($jeniswapu == 'wapu_pph26_a') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 26 0%";
                            } else if ($jeniswapu == 'wapu_pph26_b') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 26 10%";
                            } else if ($jeniswapu == 'wapu_pph26_c') {
                                $isiuraiansppb->sppb_pajak_wapu = "PPh 26 20%";
                            } else if ($jeniswapu == 'wapu_pasal4a') {
                                $isiuraiansppb->sppb_pajak_wapu = "Pasal 4 Ayat 2";
                            } else if ($jeniswapu == 'wapu_normal_') {
                                $isiuraiansppb->sppb_pajak_wapu = "wapu Normal 11%";
                            } else if ($jeniswapu == 'wapu_nilai_l') {
                                $isiuraiansppb->sppb_pajak_wapu = "wapu 1,1%";
                            } else if ($jeniswapu == 'wapu_manual_') {
                                $isiuraiansppb->sppb_pajak_wapu = "Manual";
                            } else {
                                //wapu_pph
                            }
                        } else if ($jenispajak == 'waba_sppb') {
                            $jeniswaba = substr($value2['pilih_waba_sppb'], 0, 12); // substring untuk mengambil pilih waba
                            if ($jeniswaba == 'waba_pph21_a') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 21 2,5%";
                            } else if ($jeniswaba == 'waba_pph21_b') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 21 7,5%";
                            } else if ($jeniswaba == 'waba_pph21_c') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 21 12,5%";
                            } else if ($jeniswaba == 'waba_pph22_a') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 22 1,5%";
                            } else if ($jeniswaba == 'waba_pph23_a') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 23 2%";
                            } else if ($jeniswaba == 'waba_pph23_b') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 23 15%";
                            } else if ($jeniswaba == 'waba_pph23_c') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 23 0%";
                            } else if ($jeniswaba == 'waba_pph26_a') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 26 0%";
                            } else if ($jeniswaba == 'waba_pph26_b') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 26 10%";
                            } else if ($jeniswaba == 'waba_pph26_c') {
                                $isiuraiansppb->sppb_pajak_waba = "PPh 26 20%";
                            } else if ($jeniswaba == 'waba_pasal4a') {
                                $isiuraiansppb->sppb_pajak_waba = "Pasal 4 Ayat 2";
                            } else if ($jeniswaba == 'waba_normal_') {
                                $isiuraiansppb->sppb_pajak_waba = "Waba Normal 11%";
                            } else if ($jeniswaba == 'waba_nilai_l') {
                                $isiuraiansppb->sppb_pajak_waba = "Waba 1,1%";
                            } else if ($jeniswaba == 'waba_manual_') {
                                $isiuraiansppb->sppb_pajak_waba = "Manual";
                            }
                        } else if ($jenispajak == 'pph_sppb_') {
                            $jenispph = substr($value2['pilih_pph_sppb'], 0, 12); // substring untuk mengambil pilih pph

                            if ($jenispph == 'pph21_a_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 21 2,5%";
                            } else if ($jenispph == 'pph21_b_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 21 7,5%";
                            } else if ($jenispph == 'pph21_c_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 21 12,5%";
                            } else if ($jenispph == 'pph22_a_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 22 1,5%";
                            } else if ($jenispph == 'pph23_a_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 23 2%";
                            } else if ($jenispph == 'pph23_b_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 23 15%";
                            } else if ($jenispph == 'pph23_c_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 23 0%";
                            } else if ($jenispph == 'pph26_a_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 26 0%";
                            } else if ($jenispph == 'pph26_b_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 26 10%";
                            } else if ($jenispph == 'pph26_c_sppb') {
                                $isiuraiansppb->sppb_pajak_pph = "PPh 26 20%";
                            } else if ($jenispph == 'pphpasal4_ay') {
                                $isiuraiansppb->sppb_pajak_pph = "Pasal 4 Ayat 2";
                            } else if ($jenispph == 'pph_manual_s') {
                                $isiuraiansppb->sppb_pajak_pph = "Manual";
                            }
                        } else if ($jenispajak == 'tanpa_paj') {
                            $isiuraiansppb->sppb_tanpa_pajak = "Ya";
                        }
                    }

                    $isiuraiansppb->save();
                    $sum1 += $b;
                }
            }
            $sum = $sum1 + $sum2;
            $isisum = Sppb::find($request->sppb_id);
            $isisum->sppb_total = $sum;
            $isisum->save();

            $sppn = Sppn::create([
                'master_user_id' => $request->user_id,
                'master_bagian_id' => $request->bagian_sppn,
                'master_bank_id' => $request->id_bank_sppn,
                'sppn_jenis' => $request->jenis,
                'sppn_sumber_dana' => $request->sumber_dana,
                'sppn_no' => $nomorsppn,
                'sppn_urutan' => $urutansppn,
                'sppn_bulan' => $bulan,
                'sppn_tahun' => $tahun,
                'sppn_kwitansi' => $request->kwitansi_sppn,
                'sppn_referensi' => $request->referensi_sppn,
                'sppn_ba_au_53' => $request->au58_sppn,
                'sppn_faktur_pajak' => 0,
                'sppn_tanggal' => $tanggals,
                'sppn_no_rek' => $request->rekening_bank_sppn,
                'sppn_atas_nama' => 0,
                'sppn_nama_bank' => $request->nama_bank_sppn,
                'sppn_sp_opl' => $request->sp_opl_sppn,
                'sppn_catatan' => $request->catatan_sppn,
                'sppn_status' => 0,
                'sppn_jumlah' => 0
            ]);

            $request->request->add(['sppn_id' => $sppn->sppn_id]);

            $isisppn = $request->isi_sppn;

            $total1 = 0;
            $total2 = 0;
            foreach ($isisppn as $isi => $value) {

                $isisppn = new IsiSppn;
                $isisppn->sppn_id = $request->sppn_id;

                if ($value['jenis_sap'] == 'vendor') {
                    $isisppn->master_kode_vendor_id = $value['vendor'];
                } else if ($value['jenis_sap'] == 'gl') {
                    $isisppn->master_gl_id = $value['gl'];
                } else {
                    $isisppn->master_customer_id = $value['customer'];
                }
                if ($value['jenis_center'] == 'cost_center') {
                    $isisppn->master_cost_center_id = $value['cost_center'];
                } else {
                    $isisppn->master_profit_center_id = $value['profit_center'];
                }
                $isisppn->master_cash_flow_id = $value['cash_flow'];
                $isisppn->save();
                $request->request->add(['sppn_isi_id' => $isisppn->sppn_isi_id]);
                foreach ($request->uraian_sppn[$isi] as $urai => $value2) {

                    $a = str_replace('.', '', $value2['jumlah']);
                    $angka_pajak = str_replace('.', '', $value2['pajak']);
                    $angka_akhir = str_replace('.', '', $value2['total_pajak']);
                    $angka_potongan = str_replace('.', '', $value2['potongan']);
                    $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                    if (isset($value2['type_pajak_sppn'])) {
                        $tanpapajak = substr($value2['type_pajak_sppn'], 0, 9);
                        if ($tanpapajak == 'tanpa_paj') {
                            $b = $a;
                        } else if ($angka_akhir != null) {
                            $b = $angka_akhir;
                        } else {
                            $b = $a;
                        }
                    } else {
                        if ($angka_akhir != null) {
                            $b = $angka_akhir;
                        } else {
                            $b = $a;
                        }
                    }
                    $isiuraiansppn = new IsiUraianSppn;
                    $isiuraiansppn->sppn_isi_id = $request->sppn_isi_id;
                    $isiuraiansppn->sppn_uraian_uraian = $value2['ket'];
                    $isiuraiansppn->sppn_pajak_manual = $value2['manual'];
                    $isiuraiansppn->sppn_uraian_pph = str_replace('.', '', $value2['pph']);
                    $isiuraiansppn->sppn_uraian_nominal = $a;
                    $isiuraiansppn->sppn_nominal_pajak = $angka_pajak;
                    $isiuraiansppn->sppn_nominal_akhir = $b;
                    $isiuraiansppn->sppn_dpp_ppn = $angka_dpp_ppn;
                    $isiuraiansppn->sppn_potongan = $angka_potongan;

                    $jenispajak = substr($value2['type_pajak_sppn'], 0, 9);  //substring untuk mengambil type pajak
                    if ($jenispajak == 'wapu_sppn') {
                        $jeniswapu = substr($value2['pilih_wapu_sppn'], 0, 12); // substring untuk mengambil pilih wapu
                        if ($jeniswapu == 'wapu_pph21_a') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 21 2,5%";
                        } else if ($jeniswapu == 'wapu_pph21_b') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 21 7,5%";
                        } else if ($jeniswapu == 'wapu_pph21_c') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 21 12,5%";
                        } else if ($jeniswapu == 'wapu_pph22_a') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 22 1,5%";
                        } else if ($jeniswapu == 'wapu_pph23_a') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 23 2%";
                        } else if ($jeniswapu == 'wapu_pph23_b') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 23 15%";
                        } else if ($jeniswapu == 'wapu_pph23_c') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 23 0%";
                        } else if ($jeniswapu == 'wapu_pph26_a') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 26 0%";
                        } else if ($jeniswapu == 'wapu_pph26_b') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 26 10%";
                        } else if ($jeniswapu == 'wapu_pph26_c') {
                            $isiuraiansppn->sppn_pajak_wapu = "PPh 26 20%";
                        } else if ($jeniswapu == 'wapu_pasal4a') {
                            $isiuraiansppn->sppn_pajak_wapu = "Pasal 4 Ayat 2";
                        } else if ($jeniswapu == 'wapu_normal_') {
                            $isiuraiansppn->sppn_pajak_wapu = "wapu Normal 11%";
                        } else if ($jeniswapu == 'wapu_nilai_l') {
                            $isiuraiansppn->sppn_pajak_wapu = "wapu 1,1%";
                        } else if ($jeniswapu == 'wapu_manual_') {
                            $isiuraiansppn->sppn_pajak_wapu = "Manual";
                        }
                    } else if ($jenispajak == 'waba_sppn') {
                        $jeniswaba = substr($value2['pilih_waba_sppn'], 0, 12); // substring untuk mengambil pilih waba

                        if ($jeniswaba == 'waba_pph21_a') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 21 2,5%";
                        } else if ($jeniswaba == 'waba_pph21_b') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 21 7,5%";
                        } else if ($jeniswaba == 'waba_pph21_c') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 21 12,5%";
                        } else if ($jeniswaba == 'waba_pph22_a') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 22 1,5%";
                        } else if ($jeniswaba == 'waba_pph23_a') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 23 2%";
                        } else if ($jeniswaba == 'waba_pph23_b') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 23 15%";
                        } else if ($jeniswaba == 'waba_pph23_c') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 23 0%";
                        } else if ($jeniswaba == 'waba_pph26_a') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 26 0%";
                        } else if ($jeniswaba == 'waba_pph26_b') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 26 10%";
                        } else if ($jeniswaba == 'waba_pph26_c') {
                            $isiuraiansppn->sppn_pajak_waba = "PPh 26 20%";
                        } else if ($jeniswaba == 'waba_pasal4a') {
                            $isiuraiansppn->sppn_pajak_waba = "Pasal 4 Ayat 2";
                        } else if ($jeniswaba == 'waba_normal_') {
                            $isiuraiansppn->sppn_pajak_waba = "Waba Normal 11%";
                        } else if ($jeniswaba == 'waba_nilai_l') {
                            $isiuraiansppn->sppn_pajak_waba = "Waba 1,1%";
                        } else if ($jeniswaba == 'waba_manual_') {
                            $isiuraiansppn->sppn_pajak_waba = "Manual";
                        }
                    } else if ($jenispajak == 'pph_sppn_') {
                        $jenispph = substr($value2['pilih_pph_sppn'], 0, 12);
                        if ($jenispph == 'pph21_a_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 21 2,5%";
                        } else if ($jenispph == 'pph21_b_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 21 7,5%";
                        } else if ($jenispph == 'pph21_c_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 21 12,5%";
                        } else if ($jenispph == 'pph22_a_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 22 1,5%";
                        } else if ($jenispph == 'pph23_a_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 23 2%";
                        } else if ($jenispph == 'pph23_b_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 23 15%";
                        } else if ($jenispph == 'pph23_c_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 23 0%";
                        } else if ($jenispph == 'pph26_a_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 26 0%";
                        } else if ($jenispph == 'pph26_b_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 26 10%";
                        } else if ($jenispph == 'pph26_c_sppn') {
                            $isiuraiansppn->sppn_pajak_pph = "PPh 26 20%";
                        } else if ($jenispph == 'pphpasal4_ay') {
                            $isiuraiansppn->sppn_pajak_pph = "Pasal 4 Ayat 2";
                        } else if ($jenispph == 'pph_manual_s') {
                            $isiuraiansppn->sppn_pajak_pph = "Manual";
                        }
                    } else if ($jenispajak == 'tanpa_paj') {
                        $isiuraiansppn->sppn_tanpa_pajak = "Ya";
                    }
                    $isiuraiansppn->save();
                    $total1 += $b;
                }
            }
            $totals = $total1 + $total2;
            $isisumsppn = Sppn::find($request->sppn_id);
            $isisumsppn->sppn_jumlah = $totals;
            $isisumsppn->save();

            $validatedSpp['spp_tanggal'] = $tanggal;
            $validatedSpp['sppd_proses'] = 0;
            $validatedSpp['sppd_posisi'] = 34;
            $validatedSpp['spp_buat'] = 34;
            $validatedSpp['spp_apk_bpd'] = 1;
            $validatedSpp['sppb_faktur_pajak'] = 0;

            $spp = Spp::create([
                'sppb_id' => $request->sppb_id,
                'sppn_id' => $request->sppn_id,
                'master_bagian_id' => $request->bagian_sppb,
                'spp_status_ob' => 0,
                'sppd_proses' => 0,
                'sppd_status' => 0,
                'flow_id' => $request->flow_id,
                'sppd_posisi' => $master_flow[0]->flow_detail_urutan,
                'spp_jenis_sumber_dana' => $sumberdana,
                'spp_status_proses' => 0,
                'spp_status_posisi' => 1,
                'spp_status_bayar' => 0,
                'spp_buat' => $level,
                'spp_status_terima' => 0,
                'spp_tanggal' => $tanggals,
                'company_id' => $perusahaan,
            ]);
            $request->request->add(['spp_id' => $spp->spp_id]);

            $faktur_pajak = $request->faktur_pajak_spp;
            foreach ($faktur_pajak as $key => $value) {
                $fp = new FakturPajak;
                $fp->sppb_id = $request->sppb_id;
                $fp->sppn_id = $request->sppn_id;
                $fp->faktur_pajak_nomor = $value['fp'];
                $fp->save();
            }
            $krywn = new NamaKaryawanModel;
            $krywn->sppb_id = null;
            $krywn->sppn_id = $request->sppn_id;
            $krywn->karyawan_nama = $request->diterima_dari;
            $krywn->karyawan_nama_bank = "-";
            $krywn->karyawan_no_rek = "-";
            $krywn->karyawan_alamat = $request->alamat_sppn;
            $krywn->save();
            if ($request->jenis == "karyawan") {
                if ($request->metode_pembayaran_sppn == "kas") {
                    $karyawan = $request->atas_nama_bank_sppn_kas;
                    if ($request->pilih_data_sppn == 'input_data') {
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = null;
                            $krywn->sppn_id = $request->sppn_id;
                            $krywn->karyawan_nama = $value;
                            $krywn->save();
                        }
                    } else {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = null;
                        $krywn->sppn_id = $request->sppn_id;
                        $krywn->karyawan_nama = "TERLAMPIR";
                        $krywn->save();
                    }
                } else {
                    $karyawan = $request->karyawan_sppn;
                    if ($request->pilih_data_sppn == 'input_data') {
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = null;
                            $krywn->sppn_id = $request->sppn_id;
                            $krywn->karyawan_nama = $value['nama'];
                            $krywn->karyawan_nama_bank = $value['bank'];
                            $krywn->karyawan_no_rek = $value['no_rek'];
                            $krywn->save();
                        }
                    } else {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = null;
                        $krywn->sppn_id = $request->sppn_id;
                        $krywn->karyawan_nama = "TERLAMPIR";
                        $krywn->karyawan_nama_bank = "TERLAMPIR";
                        $krywn->karyawan_no_rek = "TERLAMPIR";
                        $krywn->save();
                    }
                }
            } else {
            }
            if ($request->jenis == "karyawan") {
                if ($request->metode_pembayaran_sppb == "kas") {

                    if ($request->pilih_data_sppb == 'input_data') {
                        $karyawan = $request->karyawan_kas_sppb_input;
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->penerima_kas_sppb_karyawan;
                            $krywn->karyawan_alamat = $request->karyawan_alamat_sppb_input;
                            $krywn->save();
                        }
                    } else if ($request->pilih_data_sppb == 'master_data') {
                        $karyawan = $request->atas_nama_bank_sppb_kas;

                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->penerima_kas_sppb_karyawan_master;
                            $krywn->karyawan_alamat = $request->alamat_kas_sppb_karyawan_master;
                            $krywn->save();
                        }
                    } else {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = $request->sppb_id;
                        $krywn->sppn_id = null;
                        $krywn->karyawan_nama = "TERLAMPIR";
                        $krywn->karyawan_alamat = "TERLAMPIR";
                        $krywn->save();
                    }
                } else if ($request->metode_pembayaran_sppb == "kas_negara") {
                    $krywn = new NamaKaryawanModel;
                    $krywn->sppb_id = $request->sppb_id;
                    $krywn->sppn_id = null;
                    $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                    $krywn->karyawan_nama_bank = "-";
                    $krywn->karyawan_no_rek = "-";
                    $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                    $krywn->save();
                } else if ($request->metode_pembayaran_sppb == "skbdn") {
                    if ($request->pilih_data_sppb == 'input_data') {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = $request->sppb_id;
                        $krywn->sppn_id = null;
                        $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                        $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                        $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                        $krywn->karyawan_alamat = $request->karyawan_alamat_sppb_input;
                        $krywn->save();
                    }
                } else {
                    if ($request->pilih_data_sppb == 'master_data') {
                        $karyawan = $request->karyawan_sppb;
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $value['nama'];
                            $krywn->karyawan_nama_bank = $value['bank'];
                            $krywn->karyawan_no_rek = $value['no_rek'];
                            $krywn->karyawan_alamat = $request->karyawan_alamat_sppb_input;
                            $krywn->save();
                        }
                    } else if ($request->pilih_data_sppb == 'input_data') {
                        $karyawan = $request->karyawan_sppb_input;
                        foreach ($karyawan as $key => $value) {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $value['nama'];
                            $krywn->karyawan_nama_bank = $value['bank'];
                            $krywn->karyawan_no_rek = $value['no_rek'];
                            $krywn->karyawan_alamat = $request->karyawan_alamat_sppb_input;
                            $krywn->save();
                        }
                    } else {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = $request->sppb_id;
                        $krywn->sppn_id = null;
                        $krywn->sppb_tidak_transfer = $request->tidak_transfer_cat;
                        $krywn->karyawan_nama = "TERLAMPIR";
                        $krywn->karyawan_nama_bank = "TERLAMPIR";
                        $krywn->karyawan_no_rek = "TERLAMPIR";
                        $krywn->karyawan_alamat = "TERLAMPIR";
                        $krywn->save();
                    }
                }
            } else {
                if ($request->metode_pembayaran_sppb == 'kas_negara') {
                    $krywn = new NamaKaryawanModel;
                    $krywn->sppb_id = $request->sppb_id;
                    $krywn->sppn_id = null;
                    $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                    $krywn->karyawan_nama_bank = "-";
                    $krywn->karyawan_no_rek = "-";
                    $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                    $krywn->save();
                } elseif ($request->metode_pembayaran_sppb == 'kas') {
                    $krywn = new NamaKaryawanModel;
                    $krywn->sppb_id = $request->sppb_id;
                    $krywn->sppn_id = null;
                    $krywn->karyawan_nama = $request->nama_kas_negara_sppb_input;
                    $krywn->karyawan_nama_bank = "-";
                    $krywn->karyawan_no_rek = "-";
                    $krywn->karyawan_alamat = $request->alamat_kas_negara_sppb_input;
                    $krywn->save();
                } else {
                    if ($request->pilih_data_sppb_vendor == 'input_data') {
                        $krywn = new NamaKaryawanModel;
                        $krywn->sppb_id = $request->sppb_id;
                        $krywn->sppn_id = null;
                        $krywn->karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                        $krywn->karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                        $krywn->karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                        $krywn->karyawan_alamat = $request->karyawan_alamat_sppb_input;
                        $krywn->save();
                    }
                }
            }
            $rekamJejak = RekamJejak::create([
                'spp_id' => $request->spp_id,
                'master_user_id' => $level,
                'master_user_id_asal' => Session::get('id'),
                'rekam_jejak_status' => 0,
                'rekam_jejak_revisi' => null
            ]);
            $spp_proses = SppProses::create([
                'spp_id' => $request->spp_id,
                'spp_proses_operator_bagian' => 1,
                'spp_proses_kepala_bagian' => 1
            ]);


            $action = $request->status_btn;
            DB::commit();

            return response()->json([
                'dataSpp' => $spp,
                'dataSppb' => $sppb,
                'dataRekamJejak' => $rekamJejak,
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
