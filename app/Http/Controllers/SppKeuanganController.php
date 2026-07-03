<?php

namespace App\Http\Controllers;
use App\Rekening;
use App\CostCenter;
use App\ProfitCenter;

use Carbon\Carbon;
use App\Sppb;
use App\Sppn;
use App\Bagian;
use App\DokumenPendukungSpbb;
use App\DokumenPendukungSppn;
use App\SumberDana;
use App\Customer;
use App\IsiSppb;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\Spp;
use App\SppProses;
use App\RekamJejak;
use App\CashFlow;
use App\Vendor;
use App\FakturPajak;
use App\NamaKaryawanModel;
use App\GL;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use DB;
use Illuminate\Routing\Redirector;
class SppKeuanganController extends Controller
{
    function __construct(){
        $this->middleware(function ($request,$next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            //dd($this->user);
            //return $next($request);
            if($this->user == null){
            
                return redirect('login');
                
            }
            else{
                return $next($request);
            }
        });
       
        
    }

    public function index()
    {
        $rekening = Rekening::All()->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
    ->where('master_rekening_kode_sap', '<>', '')
    ->where('master_rekening_kode_sap', '<>', 0);
        $costcenter = CostCenter::All();
        $profitcenter = ProfitCenter::All();
        $sumberDana = SumberDana::All();
        $customer = Customer::All();
        $cashflow = CashFlow::All();
        $vendor = Vendor::All();
        $bagian = 2;
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first();
        // $gl = GL::All();
        $gl = DB::table('master_budget')->where('master_budget.bagian_id','=',$bagian)
        ->leftJoin('master_bagian','master_budget.bagian_id','=','master_bagian.master_bagian_id')
        ->leftJoin('master_gl','master_budget.gl_id','=','master_gl.master_gl_id')
        ->get();
        $client = new Client();
        $url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        $response=$client->request('GET',$url,[
            'verify' => false,
        ]);
        $karyawan_all = json_decode($response->getBody());
        $bagian_karyawan = DB::table('master_bagian')->where('master_bagian.master_bagian_id','=',$bagian)
        ->select('master_bagian.*')->first();
        
        $ino_bagian_id = $bagian_karyawan->ino_bagian_id;

        $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) use($ino_bagian_id) {
            return $value->bagian_id == $ino_bagian_id;
        });

        $data = array(
            'rekening' => $rekening, 
            'costcenter' => $costcenter,
            'profitcenter' => $profitcenter,
            'customer' => $customer,
            'sumberdana' => $sumberDana,
            'bagian' => $bagian_id,
            'cashflow' => $cashflow,
            'vendor' => $vendor,
            'karyawan' => $karyawan_bagian,
            'gl' => $gl
        );
        return view('page.spp_keuangan.spp_keuangan_tambah', $data);
    }
    public function store(Request $request)
    {
       // dd($request->atas_nama_bank_sppb_vendor);
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $user = Session::get('id');
        $jalur = $request->jalur_pajak;
        $sumberdana = $request->sumber_dana;
        if($request->jenis_form=='sppb'){
            $request -> validate([
                'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
            ]);
            $bulanromawi=array('','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
           
            $tanggal = $request->tanggal_sppb;
            $tanggals = date('Y-m-d',strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;

            $bulan = $bulanromawi[$month];
            $nomor_surat = DB::table('sppb')
                   ->select('sppb_urutan','sppb_tahun', DB::raw('MAX(sppb_urutan) as maxno'))
                   ->where('sppb_tahun', $tahun)->first();
            if($day == 3 && $month == 1 && $tahun != $nomor_surat->sppb_tahun){
                $urutansppb = 1;
            }
            else{
                $urutansppb = $nomor_surat->maxno + 1;
            }
            $kodebagiansppb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
            $kodebagian = $kodebagiansppb->master_bagian_kode;
            $nomor=$kodebagian."/SPPb/".$urutansppb."/".$bulan."/".$tahun;
            $data_metpen = $request->pilih_data_sppb;     

                $sppb = Sppb::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppb,
                    'master_bank_id' => $request->id_bank_sppb,
                    'sppb_jenis'=>'Keuangan',
                    'sppb_no'=> $nomor,
                    'sppb_urutan' => $urutansppb,
                    'sppb_bulan' => $bulan,
                    'sppb_tahun' => $tahun,
                    'sppb_kwitansi' => $request -> kwitansi_sppb,
                    'sppb_referensi' => $request -> referensi_sppb,
                    'sppb_au_53' => $request -> au53_sppb,
                    'sppb_berita_acara' => $request -> berita_acara_sppb,
                    'sppb_faktur_pajak' => 0,
                    'sppb_sp_opl' => $request -> sp_opl_sppb,
                    'sppb_tanggal' => $tanggals,
                    'sppb_metode_pembayaran' => $request -> metode_pembayaran_sppb,
                    'sppb_no_rek' => 0,
                    'sppb_atas_nama' => 0,
                    'sppb_nama_bank' => 0,
                    'sppb_catatan' => $request -> catatan_sppb,
                    'sppb_status' =>0,
                    'sppb_total' =>0,
                    'sppb_data_metpen' => $data_metpen,
                ]);
            
            
            $request->request->add(['sppb_id'=>$sppb->sppb_id]);
            $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
            if($dokumenpendukung != null)
                {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukungs = $file->getClientOriginalName();
                        $file->move('dokumen',$dokumenpendukungs);
                        $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                        $dokumenpendukungsppb -> sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb -> dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }
            $isisppb=$request->isi_sppb;
            $sum1=0;
                $sum2=0;
                foreach( $isisppb as $isi =>$value){
                    if($value['jenis_center']=='cost_center'){
                        $isisppb = new isisppb;
                        $isisppb->sppb_id=$request->sppb_id;
                        $isisppb->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppb->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppb->master_customer_id = $value['customer'];
                        }else{
                            $isisppb->master_gl_id = $value['gl'];
                        }                         
                        $isisppb->master_cost_center_id = $value['cost_center'];
                        $isisppb->master_cash_flow_id = $value['cash_flow'];
                        $isisppb->save();
                        $request->request->add(['sppb_isi_id'=>$isisppb->sppb_isi_id]);
                        foreach($request->uraian_sppb[$isi] as $urai =>$value2){
                            $a= $value2['jumlah'];
                            $b= str_replace(".","",$a);
                            $isiuraiansppb = new IsiUraiansppb;
                            $isiuraiansppb ->sppb_isi_id = $request->sppb_isi_id;
                            $isiuraiansppb ->sppb_uraian_uraian  = $value2['ket'];
                            $isiuraiansppb ->sppb_uraian_nominal = $b;
                            $isiuraiansppb ->save();
                            $sum1 += $b;
                        } 
                    }
                    else{
                        $isisppb = new isisppb;
                        $isisppb->sppb_id=$request->sppb_id;
                        $isisppb->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppb->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppb->master_customer_id = $value['customer'];
                        }else{
                            $isisppb->master_gl_id = $value['gl'];
                        } 
                                                $isisppb->master_profit_center_id = $value['profit_center'];
                        $isisppb->master_cash_flow_id = $value['cash_flow'];
                        $isisppb->save();
                        $request->request->add(['sppb_isi_id'=>$isisppb->sppb_isi_id]);
                        foreach($request->uraian_sppb[$isi] as $urai =>$value2){
                            $a=$value2['jumlah'];
                            $b=str_replace(".","",$a);
                            $isiuraiansppb = new IsiUraiansppb;
                            $isiuraiansppb ->sppb_isi_id = $request->sppb_isi_id;
                            $isiuraiansppb ->sppb_uraian_uraian  = $value2['ket'];
                            $isiuraiansppb ->sppb_uraian_nominal = $b;
                            $isiuraiansppb ->save();
                            $sum2 += $b;
                            } 
                        }
                        
                    } 

            $sum=$sum1+$sum2;
            
            $isisum= Sppb::find($request->sppb_id);
            $isisum->sppb_total=$sum;
            $isisum->save();
            
                $spp = Spp::create([
                    'sppb_id' =>  $request->sppb_id,
                    'spp_tanggal' => $tanggals,
                    'master_bagian_id' => $request->bagian_sppb,
                    'spp_status_ob' => 0,
                    'spp_jenis_sumber_dana'=> $sumberdana,
                    'spp_status_proses' => 0,
                    'spp_status_bayar' => 0,
                    'spp_status_posisi' => 1,
                    'spp_jalur_pajak' => $request->jalur_pajak,
                ]);
            
            $faktur_pajak = $request->faktur_pajak_sppb;
            foreach($faktur_pajak as $key => $value){
                $fp = new FakturPajak;
                $fp -> sppb_id = $request->sppb_id;
                $fp -> sppn_id = null;
                $fp -> faktur_pajak_nomor = $value['fp'];
                $fp -> save();
            }
            if($request->metode_pembayaran_sppb == "karyawan"){
                if($request->pilih_data_sppb == 'input_data'){
                    $karyawan = $request->karyawan_sppb_input;
                    foreach($karyawan as $key => $value){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $value['nama'];
                        $krywn -> karyawan_nama_bank = $value['bank'];
                        $krywn -> karyawan_no_rek = $value['no_rek'];
                        $krywn -> karyawan_alamat = $value['alamat'];
                        $krywn -> save();
                    }
                }
                else if($request->pilih_data_sppb == 'master_data'){
                    $karyawan = $request->karyawan_sppb;
                    foreach($karyawan as $key => $value){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $value['nama'];
                        $krywn -> karyawan_nama_bank = $value['bank'];
                        $krywn -> karyawan_no_rek = $value['no_rek'];
                        $krywn -> karyawan_alamat = $value['alamat'];
                        $krywn -> save();
                    }
                }else{
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = "TERLAMPIR";
                        $krywn -> karyawan_nama_bank = "TERLAMPIR";
                        $krywn -> karyawan_alamat = "TERLAMPIR";
                        $krywn -> karyawan_no_rek = "TERLAMPIR";
                        $krywn -> save();
                }
            }else if($request->metode_pembayaran_sppb == "bank" || $request->metode_pembayaran_sppb == "skbdn"){
                if($request->pilih_data_sppb == 'input_data'){
                    $krywn = new NamaKaryawanModel;
                    $krywn -> sppb_id = $request->sppb_id;
                    $krywn -> sppn_id = null;
                    $krywn -> karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                    $krywn -> karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                    $krywn -> karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                    $krywn -> karyawan_alamat = $request->alamat_bank_sppb_vendor;
                    $krywn -> save();
                }elseif($request->pilih_data_sppb == 'master_data'){
                    $krywn = new NamaKaryawanModel;
                    $krywn -> sppb_id = $request->sppb_id;
                    $krywn -> sppn_id = null;
                    $krywn -> karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                    $krywn -> karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                    $krywn -> karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                    $krywn -> karyawan_alamat = $request->alamat_bank_sppb_vendor;
                    $krywn -> save();
                }else{
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = "TERLAMPIR";
                        $krywn -> karyawan_nama_bank = "TERLAMPIR";
                        $krywn -> karyawan_no_rek = "TERLAMPIR";
                        $krywn -> karyawan_alamat = "TERLAMPIR";
                        $krywn -> save();
                }
            }else if($request->metode_pembayaran_sppb == "kas" || $request->metode_pembayaran_sppb == "kas_negara"){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $request->kas_nama_sppb;
                        $krywn -> karyawan_nama_bank = "-";
                        $krywn -> karyawan_no_rek = "-";
                        $krywn -> karyawan_alamat = $request->kas_alamat_sppb;
                        $krywn -> save();
            }
            
            
            $request->request->add(['spp_id'=>$spp->spp_id]);

            $rekam_jejak = RekamJejak::create([
                'spp_id' => $request->spp_id,
                'master_user_id' => $level,
                'master_user_id_asal' => Session::get('id'),
                'rekam_jejak_status' =>0,
                'rekam_jejak_revisi' => null
            ]);
            $spp_proses = SppProses::create([
                'spp_id' => $request->spp_id,
                'spp_proses_operator_bagian' =>1,
                'spp_proses_kepala_bagian' => 1
            ]);

            $action = $request->status_btn;
            if($action == "0"){
                $index_cetak = 0;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak);
            }
            else{
                $index_cetak = 1;
                $id_cetak = $request->spp_id;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak)->with('id_cetak',$id_cetak);
            }
        }
        else if($request -> jenis_form == 'sppn'){
            $request -> validate([
                'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
            ]);

            $kodebagiansppn = Bagian::where('master_bagian_id',$request->bagian_sppn)->select('master_bagian_kode')->first();
            $kodebagian = $kodebagiansppn->master_bagian_kode;
            $bulanromawi=array('','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
            $tanggal = $request->tanggal_sppn;
            $tanggals = date('Y-m-d',strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;

            $bulan = $bulanromawi[$month];
            $nomor_surat_sppn = DB::table('sppn')
                    ->select('sppn_urutan','sppn_tahun', DB::raw('MAX(sppn_urutan) as maxnosppn'))
                    ->where('sppn_tahun', $tahun)->first();
            if ($day == 3 && $month == 1 && $tahun != $nomor_surat_sppn->sppn_tahun ) {
                $urutansppn = 1;
            }else{
                $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
            }
            $nomor=$kodebagian."/SPPn/".$urutansppn."/".$bulan."/".$tahun;
            
                $sppn = Sppn::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppn,
                    'master_bank_id' => $request->id_bank_sppn,
                    'sppn_jenis'=>'Keuangan',
                    'sppn_no'=> $nomor,
                    'sppn_urutan' => $urutansppn,
                    'sppn_bulan' => $bulan,
                    'sppn_tahun' => $tahun,
                    'sppn_kwitansi' => $request -> kwitansi_sppn,
                    'sppn_referensi' => $request -> referensi_sppn,
                    'sppn_ba_au_53' => $request -> baau58_sppn,
                    'sppn_faktur_pajak' => 0,
                    'sppn_tanggal' => $tanggals,
                    
                    'sppn_no_rek' => 0,
                    'sppn_atas_nama' => 0,
                    'sppn_nama_bank' => 0,
                    'sppn_sp_opl' => $request -> sp_opl_sppn,
                    'sppn_catatan' => $request -> catatan_sppn,
                    'sppn_status' => 0,
                    'sppn_jumlah' =>0,

            ]);
            
            
            $request->request->add(['sppn_id'=>$sppn->sppn_id]);
            $dokpensppn = $request->file('dokumen_pendukung_sppn');
            if($dokpensppn != null)
                {
                    foreach ($dokpensppn as $file) {
                        $dokpensppns = $file->getClientOriginalName();
                        $file->move('dokumen',$dokpensppns);
                        $dokumenpendukungsppn = new \App\DokumenPendukungSppn;
                        $dokumenpendukungsppn -> sppn_id = $request->sppn_id;
                        $dokumenpendukungsppn -> dokumen_pendukung_sppn_nama = $dokpensppns;
                        $dokumenpendukungsppn->save();
                    }
                }
                $isisppn=$request->isi_sppn;
                $sum1=0;
                $sum2=0;
                foreach( $isisppn as $isi =>$value){
                    if($value['jenis_center']=='cost_center'){
                        $isisppn = new isisppn;
                        $isisppn->sppn_id=$request->sppn_id;
                        $isisppn->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppn->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppn->master_customer_id = $value['customer'];
                        }else{
                            $isisppn->master_gl_id = $value['vendor'];
                        }                          
                        $isisppn->master_cost_center_id = $value['cost_center'];
                        $isisppn->master_cash_flow_id = $value['cash_flow'];
                        $isisppn->save();
                        $request->request->add(['sppn_isi_id'=>$isisppn->sppn_isi_id]);
                        foreach($request->uraian_sppn[$isi] as $urai =>$value2){
                            $a= $value2['jumlah'];
                            $b= str_replace(".","",$a);
                            $isiuraiansppn = new IsiUraiansppn;
                            $isiuraiansppn ->sppn_isi_id = $request->sppn_isi_id;
                            $isiuraiansppn ->sppn_uraian_uraian  = $value2['ket'];
                            $isiuraiansppn ->sppn_uraian_nominal = $b;
                            $isiuraiansppn ->save();
                            $sum1 += $b;
                        } 
                    }
                    else{
                        $isisppn = new isisppn;
                        $isisppn->sppn_id=$request->sppn_id;
                        $isisppn->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppn->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppn->master_customer_id = $value['customer'];
                        }else{
                            $isisppn->master_gl_id = $value['gl'];
                        }                          
                        $isisppn->master_profit_center_id = $value['profit_center'];
                        $isisppn->master_cash_flow_id = $value['cash_flow'];
                        $isisppn->save();
                        $request->request->add(['sppn_isi_id'=>$isisppn->sppn_isi_id]);
                        foreach($request->uraian_sppn[$isi] as $urai =>$value2){
                            $a=$value2['jumlah'];
                            $b=str_replace(".","",$a);
                            $isiuraiansppn = new IsiUraiansppn;
                            $isiuraiansppn ->sppn_isi_id = $request->sppn_isi_id;
                            $isiuraiansppn ->sppn_uraian_uraian  = $value2['ket'];
                            $isiuraiansppn ->sppn_uraian_nominal = $b;
                            $isiuraiansppn ->save();
                            $sum2 += $b;
                            } 
                        }
                        
                    } 
                    $sum=$sum1+$sum2;
                    // dd($sum);
                    $isisum= Sppn::find($request->sppn_id);
                    $isisum->sppn_jumlah=$sum;
                    $isisum->save();

            $faktur_pajak = $request->faktur_pajak_sppn;
            foreach($faktur_pajak as $key => $value){
                $fp = new FakturPajak;
                $fp -> sppb_id = null;
                $fp -> sppn_id = $request->sppn_id;
                $fp -> faktur_pajak_nomor = $value['fp'];
                $fp -> save();
            }

                    $krywn = new NamaKaryawanModel;
                    $krywn -> sppb_id = null;
                    $krywn -> sppn_id = $request->sppn_id;
                    $krywn -> karyawan_nama = $request->diterima_dari;
                    $krywn -> karyawan_nama_bank = "-";
                    $krywn -> karyawan_no_rek = "-";
                    $krywn -> karyawan_alamat = $request->alamat_sppn;
                    $krywn -> save();
            // if($request->metode_pembayaran_sppn == "karyawan"){
            //     $karyawan = $request->karyawan_sppn;
            //     if($request->pilih_data_sppn == 'input_data'){
            //         foreach($karyawan as $key => $value){
            //             $krywn = new NamaKaryawanModel;
            //             $krywn -> sppb_id = null;
            //             $krywn -> sppn_id = $request->sppn_id;
            //             $krywn -> karyawan_nama = $value['nama'];
            //             $krywn -> karyawan_nama_bank = $value['bank'];
            //             $krywn -> karyawan_no_rek = $value['no_rek'];
            //             $krywn -> save();
            //         }
            //     }else{
            //         $krywn = new NamaKaryawanModel;
            //             $krywn -> sppb_id = null;
            //             $krywn -> sppn_id = $request->sppn_id;
            //             $krywn -> karyawan_nama = "TERLAMPIR";
            //             $krywn -> karyawan_nama_bank = "TERLAMPIR";
            //             $krywn -> karyawan_no_rek = "TERLAMPIR";
            //             $krywn -> save();
            //     }
            // }
            $spp = Spp::create([
                'sppn_id' =>  $request->sppn_id,
                'spp_tanggal' => $tanggals,
                'master_bagian_id' => $request->bagian_sppn,
                'spp_status_ob' => 0,
                'spp_jenis_sumber_dana'=> $sumberdana,
                'spp_status_proses' => 0,
                'spp_status_posisi' => 1,
                'spp_status_terima' => 0,
                'spp_jalur_pajak' => $request->jalur_pajak,
            ]);
            $request->request->add(['spp_id'=>$spp->spp_id]);
            
            $rekam_jejak = RekamJejak::create([
                'spp_id' => $request->spp_id,
                'master_user_id' => $level,
                'master_user_id_asal' => Session::get('id'),
                'rekam_jejak_status' =>0,
                'rekam_jejak_revisi' => null
            ]);
            $spp_proses = SppProses::create([
                'spp_id' => $request->spp_id,
                'spp_proses_operator_bagian' =>1,
                'spp_proses_kepala_bagian' => 1
            ]);

            $action = $request->status_btn;
            if($action == "0"){
                $index_cetak = 0;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak);
            }
            else{
                $index_cetak = 1;
                $id_cetak = $request->spp_id;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak)->with('id_cetak',$id_cetak);
            }
        }
        else {
            $request -> validate([
                'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
            ]);

            $kodebagianb = Bagian::where('master_bagian_id',$request->bagian_sppb)->select('master_bagian_kode')->first();
            $kodebagiann = Bagian::where('master_bagian_id',$request->bagian_sppn)->select('master_bagian_kode')->first();
            $kodebagiansppb = $kodebagianb->master_bagian_kode;
            $kodebagiansppn = $kodebagiann->master_bagian_kode;
            
            $bulanromawi=array('','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
            $tanggal = $request->tanggal_sppb;
            $tanggals = date('Y-m-d',strtotime($tanggal));
            $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
            $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
            $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;

            $bulan = $bulanromawi[$month];

            $nomor_surat_sppb = DB::table('sppb')
                   ->select('sppb_urutan','sppb_tahun', DB::raw('MAX(sppb_urutan) as maxno'))
                   ->where('sppb_tahun', $tahun)->first();
            $nomor_surat_sppn = DB::table('sppn')
                   ->select('sppn_urutan','sppn_tahun', DB::raw('MAX(sppn_urutan) as maxnosppn'))
                   ->where('sppn_tahun', $tahun)->first();
            if($day == 3 && $month == 1 && $tahun != $nomor_surat_sppb->sppb_tahun ){
                $urutansppb = 1;
                if($day == 3 && $month == 1 && $tahun != $nomor_surat_sppn->sppn_tahun ){
                    $urutansppn = 1;
                }else {
                    $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
                }
                // dd($urutansppn,$urutansppb);
            }else{
                $urutansppb = $nomor_surat_sppb->maxno + 1;
                $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
            }
             
            
            $nomorsppb=$kodebagiansppb."/SPPb/".$urutansppb."/".$bulan."/".$tahun;
            $nomorsppn=$kodebagiansppn."/SPPn/".$urutansppn."/".$bulan."/".$tahun;
            $data_metpen = $request->pilih_data_sppb;     
            
                $sppb = Sppb::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppb,
                    'master_bank_id' => $request->id_bank_sppb,
                    'sppb_no'=> $nomorsppb,
                    'sppb_urutan' => $urutansppb,
                    'sppb_bulan' => $bulan,
                    'sppb_tahun' => $tahun,
                    'sppb_jenis' => 'Keuangan',
                    'sppb_kwitansi' => $request -> kwitansi_sppb,
                    'sppb_referensi' => $request -> referensi_sppb,
                    'sppb_au_53' => $request -> au53_sppb,
                    'sppb_berita_acara' => $request -> berita_acara_sppb,
                    'sppb_faktur_pajak' =>0,
                    'sppb_sp_opl' => $request -> sp_opl_sppb,
                    'sppb_tanggal' => $tanggals,
                    'sppb_metode_pembayaran' => $request -> metode_pembayaran_sppb,
                    'sppb_no_rek' => 0,
                    'sppb_atas_nama' => 0,
                    'sppb_nama_bank' => 0,
                    'sppb_catatan' => $request -> catatan_sppb,
                    'sppb_status' =>0,
                    'sppb_total' =>0,
                    'sppb_data_metpen' => $data_metpen,

                ]);
            
            
            $request->request->add(['sppb_id'=>$sppb->sppb_id]);
            $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
           
            if($dokumenpendukung != null)
                {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukungs = $file->getClientOriginalName();
                        $file->move('dokumen',$dokumenpendukungs);
                        $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                        $dokumenpendukungsppb -> sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb -> dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }
           
            $isisppb=$request->isi_sppb;
            $sum1=0;
            $sum2=0;
            foreach( $isisppb as $isi =>$value){
                if($value['jenis_center']=='cost_center'){
                    $isisppb = new isisppb;
                    $isisppb->sppb_id=$request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if($value['jenis_sap'] == 'vendor'){
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    }elseif($value['jenis_sap'] == 'customer'){
                        $isisppb->master_customer_id = $value['customer'];
                    }else{
                        $isisppb->master_gl_id = $value['gl'];
                    }                    
                    $isisppb->master_cost_center_id = $value['cost_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id'=>$isisppb->sppb_isi_id]);
                    foreach($request->uraian_sppb[$isi] as $urai =>$value2){
                        $a= $value2['jumlah'];
                        $b= str_replace(".","",$a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb ->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb ->sppb_uraian_uraian  = $value2['ket'];
                        $isiuraiansppb ->sppb_uraian_nominal = $b;
                        $isiuraiansppb ->save();
                        $sum1 += $b;
                    } 
                }
                else{
                    $isisppb = new isisppb;
                    $isisppb->sppb_id=$request->sppb_id;
                    $isisppb->master_kode_kbb = $value['kode_kbb'];

                    if($value['jenis_sap'] == 'vendor'){
                        $isisppb->master_kode_vendor_id = $value['vendor'];
                    }elseif($value['jenis_sap'] == 'customer'){
                        $isisppb->master_customer_id = $value['customer'];
                    }else{
                        $isisppb->master_gl_id = $value['gl'];
                    }                    $isisppb->master_profit_center_id = $value['profit_center'];
                    $isisppb->master_cash_flow_id = $value['cash_flow'];
                    $isisppb->save();
                    $request->request->add(['sppb_isi_id'=>$isisppb->sppb_isi_id]);
                    foreach($request->uraian_sppb[$isi] as $urai =>$value2){
                        $a=$value2['jumlah'];
                        $b=str_replace(".","",$a);
                        $isiuraiansppb = new IsiUraiansppb;
                        $isiuraiansppb ->sppb_isi_id = $request->sppb_isi_id;
                        $isiuraiansppb ->sppb_uraian_uraian  = $value2['ket'];
                        $isiuraiansppb ->sppb_uraian_nominal = $b;
                        $isiuraiansppb ->save();
                        $sum2 += $b;
                        } 
                    }
                    
                } 
                $sumsppb=$sum1+$sum2;
                $isisum= Sppb::find($request->sppb_id);
                $isisum->sppb_total=$sumsppb;
                $isisum->save();
                
            $request -> validate([
                'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
            ]);
            
                $sppn = Sppn::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppn,
                    'master_bank_id' => $request->id_bank_sppn,
                    'sppn_no'=> $nomorsppn,
                    'sppn_urutan' => $urutansppn,
                    'sppn_bulan' => $bulan,
                    'sppn_tahun' => $tahun,
                    'sppn_jenis'=> 'Keuangan',
                    'sppn_kwitansi' => $request -> kwitansi_sppn,
                    'sppn_referensi' => $request -> referensi_sppn,
                    'sppn_ba_au_53' => $request -> baau58_sppn,
                    'sppn_faktur_pajak' =>0,
                    'sppn_tanggal' => $tanggals,
                    
                    'sppn_no_rek' => 0,
                    'sppn_atas_nama' => 0,
                    'sppn_nama_bank' => 0,
                    'sppn_sp_opl' => $request -> sp_opl_sppn,
                    'sppn_catatan' => $request -> catatan_sppn,
                    'sppn_status' => 0,
                    'sppn_jumlah' => 0,
            ]);
            
            
            $request->request->add(['sppn_id'=>$sppn->sppn_id]);
            $dokumenpendukungsppn = $request->file('dokumen_pendukung_sppn');
            if($dokumenpendukungsppn != null)
                {
                    foreach ($dokumenpendukungsppn as $file) {
                        $dokumenpendukungsppns = $file->getClientOriginalName();
                        $file->move('dokumen',$dokumenpendukungsppns);
                        $dokumenpendukungsppnsppn = new \App\DokumenPendukungSppn;
                        $dokumenpendukungsppnsppn -> sppn_id = $request->sppn_id;
                        $dokumenpendukungsppnsppn -> dokumen_pendukung_sppn_nama = $dokumenpendukungsppns;
                        $dokumenpendukungsppnsppn->save();
                    }
                }
                $isisppn=$request->isi_sppn;
                $total1=0;
                $total2=0; 
                foreach( $isisppn as $isi =>$value){
                    if($value['jenis_center']=='cost_center'){
                        $isisppn = new isisppn;
                        $isisppn->sppn_id=$request->sppn_id;
                        $isisppn->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppn->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppn->master_customer_id = $value['customer'];
                        }else{
                            $isisppn->master_gl_id = $value['gl'];
                        }                           $isisppn->master_cost_center_id = $value['cost_center'];
                        $isisppn->master_cash_flow_id = $value['cash_flow'];
                        $isisppn->save();
                        $request->request->add(['sppn_isi_id'=>$isisppn->sppn_isi_id]);
                        foreach($request->uraian_sppn[$isi] as $urai =>$value2){
                            $a=$value2['jumlah'];
                            $b=str_replace(".","",$a);
                            $isiuraiansppn = new IsiUraiansppn;
                            $isiuraiansppn ->sppn_isi_id = $request->sppn_isi_id;
                            $isiuraiansppn ->sppn_uraian_uraian  = $value2['ket'];
                            $isiuraiansppn ->sppn_uraian_nominal = $b;
                            $isiuraiansppn ->save();
                            $total1 += $b;
                        } 
                    }
                    else{
                        $isisppn = new isisppn;
                        $isisppn->sppn_id=$request->sppn_id;
                        $isisppn->master_kode_kbb = $value['kode_kbb'];

                        if($value['jenis_sap'] == 'vendor'){
                            $isisppn->master_kode_vendor_id = $value['vendor'];
                        }elseif($value['jenis_sap'] == 'customer'){
                            $isisppn->master_customer_id = $value['customer'];
                        }else{
                            $isisppn->master_gl_id = $value['gl'];
                        }                           $isisppn->master_profit_center_id = $value['profit_center'];
                        $isisppn->master_cash_flow_id = $value['cash_flow'];
                        $isisppn->save();
                        $request->request->add(['sppn_isi_id'=>$isisppn->sppn_isi_id]);
                        foreach($request->uraian_sppn[$isi] as $urai =>$value2){
                            $a=$value2['jumlah'];
                            $b= str_replace(".","",$a);
                            $isiuraiansppn = new IsiUraiansppn;
                            $isiuraiansppn ->sppn_isi_id = $request->sppn_isi_id;
                            $isiuraiansppn ->sppn_uraian_uraian  = $value2['ket'];
                            $isiuraiansppn ->sppn_uraian_nominal = $b;
                            $isiuraiansppn ->save();
                            $total2 += $b;
                        } 
                    }
                        
                } 
                $totals=$total1+$total2;
                $isisumsppn= Sppn::find($request->sppn_id);
                $isisumsppn->sppn_jumlah=$totals;
                $isisumsppn->save();

                $faktur_pajak = $request->faktur_pajak_spp;
                foreach($faktur_pajak as $key => $value){
                    $fp = new FakturPajak;
                    $fp -> sppb_id = $request->sppb_id;
                    $fp -> sppn_id = $request->sppn_id;
                    $fp -> faktur_pajak_nomor = $value['fp'];
                    $fp -> save();
                }
                    $krywn = new NamaKaryawanModel;
                    $krywn -> sppb_id = null;
                    $krywn -> sppn_id = $request->sppn_id;
                    $krywn -> karyawan_nama = $request->diterima_dari;
                    $krywn -> karyawan_nama_bank = "-";
                    $krywn -> karyawan_no_rek = "-";
                    $krywn -> karyawan_alamat = $request->alamat_sppn;
                    $krywn -> save();

                if($request->metode_pembayaran_sppb == "karyawan"){
                    if($request->pilih_data_sppb == 'input_data'){
                        $karyawan = $request->karyawan_sppb_input;
                        foreach($karyawan as $key => $value){
                            $krywn = new NamaKaryawanModel;
                            $krywn -> sppb_id =$request->sppb_id;
                            $krywn -> sppn_id = null;
                            $krywn -> karyawan_nama = $value['nama'];
                            $krywn -> karyawan_nama_bank = $value['bank'];
                            $krywn -> karyawan_no_rek = $value['no_rek'];
                            $krywn -> karyawan_alamat = $value['alamat'];
                            $krywn -> save();
                        }
                    }
                    else if($request->pilih_data_sppb == 'master_data'){
                        $karyawan = $request->karyawan_sppb;
                        foreach($karyawan as $key => $value){
                            $krywn = new NamaKaryawanModel;
                            $krywn -> sppb_id =$request->sppb_id;
                            $krywn -> sppn_id = null;
                            $krywn -> karyawan_nama = $value['nama'];
                            $krywn -> karyawan_nama_bank = $value['bank'];
                            $krywn -> karyawan_no_rek = $value['no_rek'];
                            $krywn -> karyawan_alamat = $value['alamat'];
                            $krywn -> save();
                        }
                    }else{
                            $krywn = new NamaKaryawanModel;
                            $krywn -> sppb_id =$request->sppb_id;
                            $krywn -> sppn_id = null;
                            $krywn -> karyawan_nama = "TERLAMPIR";
                            $krywn -> karyawan_nama_bank = "TERLAMPIR";
                            $krywn -> karyawan_no_rek = "TERLAMPIR";
                            $krywn -> karyawan_alamat = "TERLAMPIR";
                            $krywn -> save();
                    }
                }else if($request->metode_pembayaran_sppb == "bank" || $request->metode_pembayaran_sppb == "skbdn"){
                    if($request->pilih_data_sppb == 'input_data'){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id = $request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                        $krywn -> karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                        $krywn -> karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                        $krywn -> karyawan_alamat = $request->alamat_bank_sppb_vendor;
                        $krywn -> save();
                    }elseif($request->pilih_data_sppb == 'master_data'){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id = $request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $request->atas_nama_bank_sppb_vendor;
                        $krywn -> karyawan_nama_bank = $request->nama_bank_sppb_vendor;
                        $krywn -> karyawan_no_rek = $request->rekening_bank_sppb_vendor;
                        $krywn -> karyawan_alamat = $request->alamat_bank_sppb_vendor;
                        $krywn -> save();
                    }else{
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = "TERLAMPIR";
                        $krywn -> karyawan_nama_bank = "TERLAMPIR";
                        $krywn -> karyawan_no_rek = "TERLAMPIR";
                        $krywn -> karyawan_alamat = "TERLAMPIR";
                        $krywn -> save();
                    }
                }else if($request->metode_pembayaran_sppb == "kas" || $request->metode_pembayaran_sppb == "kas_negara"){
                        $krywn = new NamaKaryawanModel;
                        $krywn -> sppb_id =$request->sppb_id;
                        $krywn -> sppn_id = null;
                        $krywn -> karyawan_nama = $request->kas_nama_sppb;
                        $krywn -> karyawan_nama_bank = "-";
                        $krywn -> karyawan_no_rek = "-";
                        $krywn -> karyawan_alamat = $request->kas_alamat_sppb;
                        $krywn -> save();
                }

                // if($request->metode_pembayaran_sppn == "karyawan"){
                //     $karyawan = $request->karyawan_sppn;
                //     if($request->pilih_data_sppn == 'input_data'){
                //         foreach($karyawan as $key => $value){
                //             $krywn = new NamaKaryawanModel;
                //             $krywn -> sppb_id = null;
                //             $krywn -> sppn_id = $request->sppn_id;
                //             $krywn -> karyawan_nama = $value['nama'];
                //             $krywn -> karyawan_nama_bank = $value['bank'];
                //             $krywn -> karyawan_no_rek = $value['no_rek'];
                //             $krywn -> save();
                //         }
                //     }else{
                //         $krywn = new NamaKaryawanModel;
                //         $krywn -> sppb_id = null;
                //         $krywn -> sppn_id = $request->sppn_id;
                //         $krywn -> karyawan_nama = "TERLAMPIR";
                //         $krywn -> karyawan_nama_bank = "TERLAMPIR";
                //         $krywn -> karyawan_no_rek = "TERLAMPIR";
                //         $krywn -> save();
                //     }
                // }
            $spp = Spp::create([
                'sppb_id' =>  $request->sppb_id,
                'sppn_id' => $request->sppn_id,
                'spp_tanggal' => $tanggals,
                'master_bagian_id' => $request->bagian_sppb,
                'spp_status_ob' => 0,
                'spp_status_proses' => 0,
                'spp_status_posisi' => 1,
                'spp_status_bayar' => 0,
                'spp_status_terima' => 0,
                'spp_jenis_sumber_dana'=> $sumberdana,
                'spp_jalur_pajak' => $request->jalur_pajak,
            ]);
            $request->request->add(['spp_id'=>$spp->spp_id]);
            
            $rekam_jejak = RekamJejak::create([
                'spp_id' => $request->spp_id,
                'master_user_id' => $level,
                'master_user_id_asal' => Session::get('id'),
                'rekam_jejak_status' =>0,
                'rekam_jejak_revisi' => null
            ]);
            $spp_proses = SppProses::create([
                'spp_id' => $request->spp_id,
                'spp_proses_operator_bagian' =>1,
                'spp_proses_kepala_bagian' => 1
            ]);
            
            $action = $request->status_btn;
            if($action == "0"){
                $index_cetak = 0;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak);
            }
            else{
                $index_cetak = 1;
                $id_cetak = $request->spp_id;
                return redirect('spp_keuangan')->with('index_cetak',$index_cetak)->with('id_cetak',$id_cetak);
            }
        }
        

    }
}
