<?php

namespace App\Http\Controllers;

use App\Bagian;
use App\Budget;
use App\CashFlow;
use App\CostCenter;
use App\Customer;
use App\DokumenPendukungSpbb;
use App\DokumenPendukungSppn;
use App\FakturPajak;
use App\Flow;
use App\GL;
use App\IsiSppb;
use App\IsiSppn;
use App\IsiUraianSppb;
use App\IsiUraianSppn;
use App\MasterKaryawan;
use App\NamaKaryawanModel;
use App\Notifications\NewSppNotification;
use App\ProfitCenter;
use App\RekamJejak;
use App\Rekening;
use App\Spp;
use App\Sppb;
use App\Sppn;
use App\SppProses;
use App\SumberDana;
use App\User;
use App\Vendor;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Redirect;


class SppbController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            $this->user = session()->get('username');
            $this->company = session()->get('company');
            // $this->id = session()->get('id');
            // dd($this->id);
            //return $next($request);
            if ($this->user == null) {

                return redirect('login');
            } else {
                return $next($request);
            }
        });
    }
    public function realisasi(Request $request)
    {
        $gl_id = $request->gl_id;
        $bagian = Session::get('bagian');
        $cacheKey = 'realisasi_data_' . $gl_id . '_' . $bagian;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $thn = date('Y');

        $realisasi = DB::table('sppb_isi')
            ->where('sppb.master_bagian_id', '=', $bagian)
            ->where('master_gl_id', '=', $gl_id)
            ->where('spp.sppd_status', '!=', 3)
            ->where('sppb_tahun', '=', $thn)
            ->leftJoin('sppb', 'sppb_isi.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('spp', 'sppb.sppb_id', '=', 'spp.sppb_id')
            ->sum('sppb_total');
        $onproses = DB::table('sppb_isi')
            ->where('sppb.master_bagian_id', '=', $bagian)
            ->where('master_gl_id', '=', $gl_id)
            ->where('spp.sppd_status', '=', 3)
            ->where('sppb_tahun', '=', $thn)
            ->leftJoin('sppb', 'sppb_isi.sppb_id', '=', 'sppb.sppb_id')
            ->leftJoin('spp', 'sppb.sppb_id', '=', 'spp.sppb_id')
            ->sum('sppb_total');
        $realisasisppn = DB::table('sppn_isi')
            ->where('sppn.master_bagian_id', '=', $bagian)
            ->where('master_gl_id', '=', $gl_id)
            ->where('spp.sppd_status', '!=', 3)
            ->where('sppn_tahun', '=', $thn)
            ->leftJoin('sppn', 'sppn_isi.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('spp', 'sppn.sppn_id', '=', 'spp.sppn_id')
            ->sum('sppn_jumlah');
        $onprosessppn = DB::table('sppn_isi')
            ->where('sppn.master_bagian_id', '=', $bagian)
            ->where('master_gl_id', '=', $gl_id)
            ->where('spp.sppd_status', '=', 3)
            ->where('spp_status_ob', '!=', 4)
            ->where('sppn_tahun', '=', $thn)
            ->leftJoin('sppn', 'sppn_isi.sppn_id', '=', 'sppn.sppn_id')
            ->leftJoin('spp', 'sppn.sppn_id', '=', 'spp.sppn_id')
            ->sum('sppn_jumlah');

        Cache::put($cacheKey, [
            'realisasi' => $realisasi,
            'onproses' => $onproses,
            'realisasisppn' => $realisasisppn,
            'onprosessppn' => $onprosessppn,
        ], 60);

        return response()->json(array(
            'realisasi' => $realisasi,
            'onproses' => $onproses,
            'realisasisppn' => $realisasisppn,
            'onprosessppn' => $onprosessppn,
        ), 200);
    }
    public function master_rek()
    {
        $rekening = DB::table('master_rekening')->where('company_id', $this->company)->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
            ->where('master_rekening_kode_sap', '<>', '')
            ->where('master_rekening_kode_sap', '<>', 0)->select('master_rekening.*')->get();

        $datatable = datatables()->of($rekening)->addIndexColumn()->toJson();
        return $datatable;
    }

    public function master_gl()
    {

        $gl = DB::table('master_gl')->where('company_id', $this->company)->select('master_gl.*')->get();
        $datatable = datatables()->of($gl)->addIndexColumn()->toJson();
        return $datatable;
    }
    public function master_rek_tambah(Request $request)
    {
        $perPage = 100;
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $search = $request->get('search', '');

        if ($search) {
            // Kondisi pencarian
            $rekening = DB::table('master_rekening')
                ->whereNotNull('master_rekening_kode_sap')
                ->where('master_rekening_kode_sap', '<>', '')
                ->where('master_rekening_kode_sap', '<>', 0)
                ->where(function ($query) use ($search) {
                    $query->where('master_rekening_kode_sap', 'like', '%' . $search . '%')
                        ->orWhere('master_rekening_keterangan', 'like', '%' . $search . '%');
                })
                ->offset($offset)
                ->limit($perPage)
                ->get();

            return response()->json([
                'rekening' => $rekening,
                'status' => 'success',
                'message' => 'Search results retrieved successfully.'
            ], 200);
        }

        // Cache hanya untuk non-pencarian
        $rekening = Cache::remember('master_rekening_' . $page . '_' . $perPage . '_' . $offset, 60 * 2, function () use ($perPage, $offset) {
            return DB::table('master_rekening')
                ->whereNotNull('master_rekening_kode_sap')
                ->where('master_rekening_kode_sap', '<>', '')
                ->where('master_rekening_kode_sap', '<>', 0)
                ->groupBy('master_rekening_kode_sap')
                ->offset($offset)
                ->limit($perPage)
                ->get();
        });

        return response()->json([
            'rekening' => $rekening,
            'status' => 'success',
            'message' => 'Data retrieved successfully.'
        ], 200);
    }

    public function get_cost_center(Request $request)
    {
        $company = Session::get('company');

        if (count($_GET) > 1) {
            $costcenter = CostCenter::where(function ($query) {
                return $query->where('master_cost_center_kode', 'like', '%' . $_GET['q'] . '%')
                    ->orWhere('master_cost_center_keterangan', 'like', '%' . $_GET['q'] . '%');
            })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $costcenter = CostCenter::paginate(100, ['*'], 'page', $_GET['page']);
        }

        return response()->json([
            'total_data' => $costcenter->total(),
            "incomplete_results" => $costcenter->hasMorePages(),
            'result' => $costcenter->items(),
        ]);
    }

    public function master_rek_tambah_pagination()
    {
        $rekening = [];
        $company = Session::get('company');

        if (count($_GET) > 1) {
            $rekening = DB::table('master_rekening')
                ->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
                // ->where('company_id', $company)
                ->where('master_rekening_kode_sap', '<>', '')
                ->where('master_rekening_kode_sap', '<>', 0)
                ->where(function ($query) {
                    $query->where('master_rekening_kode_sap', 'LIKE', '%' . $_GET['q'] . '%')
                        ->orWhere('master_rekening_keterangan', 'LIKE', '%' . $_GET['q'] . '%');
                })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $rekening = DB::table('master_rekening')
                ->groupBy('master_rekening_kode_sap')->whereNotNull('master_rekening_kode_sap')
                // ->where('company_id', $company)
                ->where('master_rekening_kode_sap', '<>', '')
                ->where('master_rekening_kode_sap', '<>', 0)
                ->paginate(100, ['*'], 'page', $_GET['page']);
        }
        return response()->json([
            'total_data' => $rekening->total(),
            "incomplete_results" => $rekening->hasMorePages(),
            'result' => $rekening->items(),
        ]);
    }

    public function master_gl_pagination()
    {
        $gl = [];
        $bagian = Session::get('bagian');

        if (count($_GET) > 1) {
            $gl = DB::table('master_budget')->where('master_budget.bagian_id', '=', $bagian)
                ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
                ->where(function ($query) {
                    $query->where('master_gl_kode', 'LIKE', '%' . $_GET['q'] . '%')
                        ->orWhere('master_gl_keterangan', 'LIKE', '%' . $_GET['q'] . '%');
                })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $gl = DB::table('master_budget')->where('master_budget.bagian_id', '=', $bagian)
                ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
                ->paginate(100, ['*'], 'page', $_GET['page']);
        }
        return response()->json([
            'total_data' => $gl->total(),
            "incomplete_results" => $gl->hasMorePages(),
            'result' => $gl->items(),
        ]);
    }

    public function get_budget_by_gl_code($id)
    {
        try {
            $bagian = SESSION::get('bagian');

            $budget = DB::table('master_budget')
                ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
                ->where('master_budget.bagian_id', '=', $bagian)
                ->where('master_gl.master_gl_id', '=', $id)
                ->first();

            return response()->json([
                'jumlah_budget' => $budget->jumlah_budget,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ], 500);
        }
    }

    public function master_cashflow_pagination()
    {
        if (count($_GET) > 1) {
            $cashflow = CashFlow::where(function ($query) {
                return $query->where('master_cash_flow_key', 'LIKE', '%' . $_GET['q'] . '%')
                    ->orWhere('master_cash_flow_keterangan', 'LIKE', '%' . $_GET['q'] . '%');
            })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $cashflow = CashFlow::paginate(100, ['*'], 'page', $_GET['page']);
        }

        return response()->json([
            'total_data' => $cashflow->total(),
            "incomplete_results" => $cashflow->hasMorePages(),
            'result' => $cashflow->items(),
        ]);
    }

    public function master_cost_profit()
    {
        $company = Session::get('company');

        if (count($_GET) > 1) {
            $profitcenter = ProfitCenter::where(function ($query) {
                return $query->where('master_profit_center_kode', 'like', '%' . $_GET['q'] . '%')
                    ->orWhere('master_profit_unit', 'like', '%' . $_GET['q'] . '%');
            })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $profitcenter = ProfitCenter::paginate(100, ['*'], 'page', $_GET['page']);
        }

        return response()->json([
            'total_data' => $profitcenter->total(),
            "incomplete_results" => $profitcenter->hasMorePages(),
            'result' => $profitcenter->items(),
        ]);
    }

    public function master_customer()
    {
        if (count($_GET) > 1) {
            $customers = DB::table('master_customer')
                ->where(function ($query) {
                    return $query->where('master_customer_kode_sap', 'like', '%' . $_GET['q'] . '%')
                        ->orWhere('master_customer_nama', 'like', '%' . $_GET['q'] . '%');
                })
                ->paginate(100, ['*'], 'page', $_GET['page']);
        } else {
            $customers = DB::table('master_customer')->paginate(100, ['*'], 'page', $_GET['page']);
        }

        return response()->json([
            'total_data' => $customers->total(),
            "incomplete_results" => $customers->hasMorePages(),
            'result' => $customers->items(),
        ]);
    }

    public function index(Request $request)
    {
        $level = Session::get('level');
        $company = Session::get('company');
        $error_code = Session::get('error_code');
        // $rekening = [];
        // $rekening = Rekening::where('company_id',$this->company )->get();
        // $rekening = DB::table('master_rekening')->where('company_id', $company)
        //     ->get();
        // dd($rekening);


        // $costcenter = CostCenter::where('company_id', $company)->get(); // ajax
        // $profitcenter = ProfitCenter::where('company_id', $company)->get(); // ajax
        $cashflow = CashFlow::All(); // ajax
        $flow = DB::table('master_flow')
            ->where('master_company_detail.company_id', $company)
            ->leftJoin('master_company_detail', 'master_flow.flow_id', '=', 'master_company_detail.flow_id')
            ->where('flow_status', '=', 1)
            ->get(); // biarkan

        $sumberDana = SumberDana::All(); // biarkan
        $bagian = Session::get('bagian');
        $bagian_id = Bagian::where('master_bagian_id', $bagian)->first(); // biarkan
        $bagianall = Bagian::where('master_bagian_id', '!=', 10)->get(); // biarkan

        $thn = date('Y');

        // $gl = DB::table('master_budget')->where('master_budget.bagian_id', '=', $bagian)
        //     ->where('budget_tahun', '=', $thn)
        //     ->leftJoin('master_bagian', 'master_budget.bagian_id', '=', 'master_bagian.master_bagian_id')
        //     ->leftJoin('master_gl', 'master_budget.gl_id', '=', 'master_gl.master_gl_id')
        //     ->get();
        //dd($gl);
        // dd($bagian);
        // if (in_array($bagian, [124, 126, 127])) {
        //     $customer = DB::table('master_customer')
        //         ->where('master_customer.company_id', '=', $company)
        //         // ->select('master_customer_id', 'master_customer_kode_sap', 'master_customer_nama')
        //         ->get();
        // } else {
        //     $customer = collect(); // Mengembalikan koleksi kosong jika bagian_id tidak sesuai
        // }
        $customer = DB::table('master_customer')->get();

        // dd($customer->first());  // Debugging untuk memeriksa hasil


        $client = new Client();
        // //$url = "https://ino.ptpn12.com/api/get_karyawan/4a685f78e08fb8037fb34905d8440be9225dcdeae25873ae0ae145d6ebd3ab3f7a80fcefb84cb2e460b2724182c2eb730b75570897d9893f48d6117582a17823T3kiHux2Py8pTxJ5fmiotFETRjSfjDFM";
        // $url = "";
        // $response = $client->request('GET', $url, [
        //     'verify' => false,
        // ]);
        $karyawan_all = null;

        $bagian_karyawan = DB::table('master_bagian')->where('master_bagian.master_bagian_id', '=', $bagian)
            ->select('master_bagian.*')->first();

        // $ino_bagian_id = $bagian_karyawan->ino_bagian_id;
        // // if ($bagian_karyawan->pemisah_keb_bag == 1) {
        // //     $profit_center_id = ProfitCenter::where('master_profit_center_id', $costcenter)->get();
        // //     $cost_center_id = CostCenter::where('company_id', $costcenter)->get();
        // // } elseif ($bagian_karyawan->pemisah_keb_bag == 2) {
        // //     $profit_center_id = ProfitCenter::where('master_profit_center_id', $costcenter)->get();
        // //     $cost_center_id = ProfitCenter::where('master_profit_center_id', $costcenter)->get();
        // // } else {
        // //     $cost_center_id = CostCenter::where('master_cost_center_id', $costcenter)->get();
        // //     $profit_center_id = ProfitCenter::where('master_profit_center_id', $costcenter)->get();
        // // }
        $karyawan_bagian = null;
        if ($level == 99) {
            $karyawan_bagian = Arr::where($karyawan_all, function ($value, $key) {
                return $value->bagian_id != 7;
            });
        }

        if ($bagian == 61) {
            $master_karyawan = DB::table('master_karyawan')
                ->where('company_id', '=', $company)->get();
        } else {
            $master_karyawan = DB::table('master_karyawan')
                ->where('master_bagian_id', '=', $bagian)
                ->where('company_id', '=', $company)->get();
        }
        // $status_flow = DB::table('master_flow')->where('flow_status', '=', '1')->get();
        $data = array(
            // 'rekening' => $rekening,
            // 'costcenter' => $costcenter,
            // 'profitcenter' => $profitcenter,
            'cashflow' => $cashflow,
            'bagian' => $bagian_id,
            'bagianall' => $bagianall,
            'sumberdana' => $sumberDana,
            // 'cost_center_id' => $cost_center_id,
            // 'profit_center_id' => $profit_center_id,
            // 'flow_status' => $status_flow,
            'karyawan' => $karyawan_bagian,
            // 'bagian_karyawan' => $bagian_karyawan,
            'master_karyawan' => $master_karyawan,
            'flow' => $flow,
            // 'gl' => $gl,
            'customer' => $customer,
        );
        //dd($profitcenter);
        // dd($data);
        // dd($customer->first());
        return view('page.spp.spp_tambah', $data)
            ->with('error_code', $error_code);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // dd($request->penerima_kas_sppb_karyawan,$request->sppb_id);
        //dd($request->pilih_data_sppb_vendor,$request->atas_nama_bank_sppb_vendor);
        // dd($request->atas_nama_bank_sppb_kas,$request->alamat_bank_sppb_kas);
        $perusahaan = $this->company;
        // dd($perusahaan);
        $flow = $request->flow_id;
        $master_flow = Flow::where('master_flow.flow_id', $flow)->leftJoin('master_flow_detail', 'master_flow_detail.flow_id', '=', 'master_flow.flow_id')->select('master_flow_detail.*')->get();
        $flow_posisi_buat = DB::table('master_flow_detail')->where('master_flow_detail.flow_id', '=', $flow)->first();
        // dd(json_decode($master_flow[0]->flow_detail_urutan));
        // dd($master_flow[0]->flow_detail_urutan);
        // dd($request->isi_sppn);
        $bagian = Session::get('bagian');
        $user = Session::get('id');
        $level = Session::get('hak_akses');
        $sumberdana = $request->sumber_dana;
        $current = date('His-dmY');


        // $notificationData = [
        //     'spp_id' => $request->spp_id,
        //     'username' => session()->get('username'),
        //     'message' => "Pembuatan SPP Baru"
        // ];

        // $userNotifable = User::find(62);

        // Notification::send($userNotifable, new NewSppNotification($notificationData));

        //LOGIKA FORM SPPB
        if ($request->jenis_form == 'sppb') {
            DB::beginTransaction();
            try {
                $tidak = $request->tidak_transfer_cat;
                // dd($user);
                $request->validate([
                    'kontrak_perjanjian_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'invoice_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'efaktur_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'berita_acara_file_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);
                $kontrak_perjanjian = $request->file('kontrak_perjanjian_sppb');
                if ($kontrak_perjanjian != null) {
                    $kontrak_perjanjian_file_name = str_replace("'", '', $kontrak_perjanjian->getClientOriginalName());
                    $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian_file_name;
                    $kontrak_perjanjian->move('dokumen/kontrakperjanjian', $kontrak_perjanjians);
                } else {
                    $kontrak_perjanjians = null;
                }

                $invoice = $request->file('invoice_sppb');
                if ($invoice != null) {
                    $invoice_file_name = str_replace("'", '', $invoice->getClientOriginalName());
                    $invoices = $current . '-' . $invoice_file_name;
                    $invoice->move('dokumen/invoice', $invoices);
                } else {
                    $invoices = null;
                }

                $efaktur = $request->file('efaktur_sppb');
                if ($efaktur != null) {
                    $efaktur_file_name = str_replace("'", '', $efaktur->getClientOriginalName());
                    $efakturs = $current . '-' . $efaktur_file_name;
                    $efaktur->move('dokumen/efaktur', $efakturs);
                } else {
                    $efakturs = null;
                }

                $berita_acara_file = $request->file('berita_acara_file_sppb');
                if ($berita_acara_file != null) {
                    $berita_acara_file_name = str_replace("'", '', $berita_acara_file->getClientOriginalName());
                    $berita_acara_files = $current . '-' . $berita_acara_file_name;
                    $berita_acara_file->move('dokumen/beritaacara', $berita_acara_files);
                } else {
                    $berita_acara_files = null;
                }
                $kodebagiansppb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
                $kodebagian = $kodebagiansppb->master_bagian_kode;
                $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $tanggal = $request->tanggal_sppb;
                $tanggals = date('Y-m-d', strtotime($tanggal));
                $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
                $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
                $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;
                $bulan = $bulanromawi[$month];

                $nomor_surat = DB::table('sppb')
                    ->select('sppb_urutan', 'sppb_tahun', DB::raw('MAX(sppb_urutan) as maxno'))
                    // ->where('sppb_tahun', $tahun)
                    ->where('master_bagian_id', $request->bagian_sppb)
                    ->first();

                if ($day == 3 && $month == 1 && $tahun != $nomor_surat->sppb_tahun) {
                    $urutansppb = 1;
                } else {
                    $urutansppb = $nomor_surat->maxno + 1;
                }
                // dd($kodebagian);


                $nomor = $kodebagian . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;

                // $kodesppb =DB::table('sppb')->where('master_bagian_id',$request->bagian_sppb)->select('sppb.*')->latest()->first();
                // $filter = (int) filter_var($nomor, FILTER_SANITIZE_NUMBER_INT);
                // $trim  = substr($filter, 0,-4);
                // dd($trim + 1);
                // dd($nomor);

                if ($request->jenis == 'vendor') {
                    $data_metpen = $request->pilih_data_sppb_vendor;
                } else {
                    $data_metpen = $request->pilih_data_sppb;
                }
                $sppb = Sppb::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppb,
                    'master_bank_id' => $request->id_bank_sppb,
                    'sppb_jenis' => $request->jenis,
                    'sppb_sumber_dana' => $request->sumber_dana,
                    'sppb_no' => $nomor,
                    'sppb_urutan' => $urutansppb,
                    'sppb_bulan' => $bulan,
                    'sppb_tahun' => $tahun,
                    'sppb_kwitansi' => $request->kwitansi_sppb,
                    'sppb_referensi' => $request->referensi_sppb,
                    'sppb_au_53' => $request->au53_sppb,
                    'sppb_berita_acara' => $request->berita_acara_sppb,
                    'sppb_faktur_pajak' => 0,
                    'sppb_sp_opl' => $request->sp_opl_sppb,
                    'sppb_tanggal' => $tanggals,
                    'sppb_metode_pembayaran' => $request->metode_pembayaran_sppb,
                    'sppb_no_rek' => 0,
                    'sppb_atas_nama' => 0,
                    'sppb_nama_bank' => 0,
                    'sppb_catatan' => $request->catatan_sppb,
                    'sppb_kontrak_perjanjian' => $kontrak_perjanjians,
                    'sppb_invoice' => $invoices,
                    'sppb_efaktur' => $efakturs,
                    'sppb_berita_acara_file' => $berita_acara_files,
                    'sppb_status' => 0,
                    'sppb_total' => 0,
                    'sppb_data_metpen' => $data_metpen,
                    'sppb_tidak_transfer' => $tidak,
                    'alasan_tidak_tf' => $request->karyawan_tidak_transfer
                ]);

                $request->request->add(['sppb_id' => $sppb->sppb_id]);
                // ddd($sppb);

                $isisppb = $request->isi_sppb;
                //dd($isisppb);

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
                        // dd($value2);
                        $a = str_replace('.', '', $value2['jumlah']);
                        // dd($a);
                        $angka_pajak = str_replace('.', '', $value2['pajak']);
                        $angka_potongan = str_replace('.', '', $value2['potongan']);
                        $angka_dpp_ppn = str_replace('.', '', $value2['dpp_ppn']);
                        $angka_akhir = str_replace('.', '', $value2['total_pajak']);
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
                        $isiuraiansppb->sppb_uraian_pph = str_replace('.', '', $value2['pph']);
                        $isiuraiansppb->sppb_pajak_manual = $value2['manual'];
                        $isiuraiansppb->sppb_uraian_nominal = $a;
                        $isiuraiansppb->sppb_nominal_pajak = $angka_pajak;
                        $isiuraiansppb->sppb_nominal_akhir = $b;
                        $isiuraiansppb->sppb_dpp_ppn = $angka_dpp_ppn;
                        $isiuraiansppb->sppb_potongan = $angka_potongan;

                        if (isset($value2['type_pajak_sppb'])) {
                            $jenispajak = substr($value2['type_pajak_sppb'], 0, 9);  //substring untuk mengambil type pajak
                            // dd($jenispajak);
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
                                    $isiuraiansppb->sppb_pajak_wapu = "manual";
                                } else {
                                    // Kondisi wapu diluar kombinasi pph
                                }
                            } else if ($jenispajak == 'waba_sppb') {
                                $jeniswaba = substr($value2['pilih_waba_sppb'], 0, 12); // substring untuk mengambil pilih waba
                                // dd($jeniswaba);
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
                                } else {
                                    // Kondisi waba diluar kombinasi pph
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
                                } else {
                                    // Kondisi pph diluar daftar
                                }
                            } else if ($jenispajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_tanpa_pajak = "Ya";
                            } else {
                                // kondisi pilihan pajak tanpa kombinasi
                            }
                        } else {
                            // Khusus nominal tanpa pilihan pajak
                        }
                        //dd($isiuraiansppb);
                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                }
                // dd($isisppb);
                $sum = $sum1 + $sum2;



                $isisum = Sppb::find($request->sppb_id);
                $isisum->sppb_total = $sum;
                $isisum->save();


                $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
                if ($dokumenpendukung != null) {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukung_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungs = $current . '-' . $dokumenpendukung_file_name;
                        $file->move('dokumen', $dokumenpendukungs);

                        $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                        $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }


                $spp = Spp::create([
                    'sppb_id' => $request->sppb_id,
                    'sppd_status' => 0,
                    'master_bagian_id' => $request->bagian_sppb,
                    'spp_status_ob' => 0,
                    'sppd_proses' => 0,
                    'sppd_posisi' => $master_flow[0]->flow_detail_urutan,
                    'flow_id' => $flow,
                    'spp_jenis_sumber_dana' => $sumberdana,
                    'spp_status_proses' => 0,
                    'spp_status_bayar' => 0,
                    'spp_status_posisi' => 1,
                    'spp_buat' => $level,
                    'spp_tanggal' => $tanggals,
                    'company_id' => $perusahaan,
                ]);
                $faktur_pajak = $request->faktur_pajak_sppb;
                foreach ($faktur_pajak as $key => $value) {
                    $fp = new FakturPajak;
                    $fp->sppb_id = $request->sppb_id;
                    $fp->sppn_id = null;
                    $fp->faktur_pajak_nomor = $value['fp'];
                    $fp->save();
                }
                if ($request->jenis == "karyawan") {
                    if ($request->metode_pembayaran_sppb == "kas") {

                        if ($request->pilih_data_sppb == 'input_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->penerima_kas_sppb_karyawan;
                            $krywn->karyawan_alamat = $request->alamat_kas_sppb_karyawan;
                            $krywn->save();
                        } else if ($request->pilih_data_sppb == 'master_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = $request->penerima_kas_sppb_karyawan_master;
                            $krywn->karyawan_nama_bank = "-";
                            $krywn->karyawan_no_rek = "-";
                            $krywn->karyawan_alamat = $request->alamat_kas_sppb_karyawan_master;
                            $krywn->save();
                        } else {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->karyawan_nama_bank = "-";
                            $krywn->karyawan_no_rek = "-";
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
                        if ($request->pilih_data_sppb == 'lampirkan_data') {
                            $krywn = new NamaKaryawanModel;
                            $krywn->sppb_id = $request->sppb_id;
                            $krywn->sppn_id = null;
                            $krywn->karyawan_nama = "TERLAMPIR";
                            $krywn->karyawan_nama_bank = "TERLAMPIR";
                            $krywn->karyawan_no_rek = "TERLAMPIR";
                            $krywn->karyawan_alamat = "TERLAMPIR";
                            $krywn->save();
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


                $request->request->add(['spp_id' => $spp->spp_id]);

                $rekam_jejak = RekamJejak::create([
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
                DB::commit();
                $action = $request->status_btn;
                if ($action == 0) {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $request->spp_id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
                // dd($spp);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
        //LOGIKA FORM SPPN
        else if ($request->jenis_form == 'sppn') {
            //dd(request()->all());
            DB::beginTransaction();
            try {
                $request->validate([
                    'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);

                $kodebagiansppn = Bagian::where('master_bagian_id', $request->bagian_sppn)->select('master_bagian_kode')->first();
                $kodebagian = $kodebagiansppn->master_bagian_kode;

                $bulanromawi = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
                $tanggal = $request->tanggal_sppn;
                $tanggals = date('Y-m-d', strtotime($tanggal));
                $tahun = Carbon::createFromFormat('d-m-Y', $tanggal)->year;
                $month = Carbon::createFromFormat('d-m-Y', $tanggal)->month;
                $day = Carbon::createFromFormat('d-m-Y', $tanggal)->day;

                $bulan = $bulanromawi[$month];

                $nomor_surat_sppn = DB::table('sppn')
                    ->select('sppn_urutan', 'sppn_tahun', DB::raw('MAX(sppn_urutan) as maxnosppn'))
                    // ->where('sppn_tahun', $tahun)
                    ->where('master_bagian_id', $request->bagian_sppn)
                    ->first();
                if ($day == 3 && $month == 1 && $tahun != $nomor_surat_sppn->sppn_tahun) {
                    $urutansppn = 1;
                } else {
                    $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
                }
                $nomor = $kodebagian . "/SPPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;
                // $nomor=$kodebagian."/PP/".$urutansppn."/".$tahun;

                $sppn = Sppn::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppn,
                    'master_bank_id' => $request->id_bank_sppn,
                    'sppn_jenis' => $request->jenis,
                    'sppn_no' => $nomor,
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
                    'sppn_jumlah' => 0,
                    'alasan_tidak_tf' => $request->karyawan_tidak_transfer_sppn
                ]);
                // ddd($sppn);


                $request->request->add(['sppn_id' => $sppn->sppn_id]);
                $dokpensppn = $request->file('dokumen_pendukung_sppn');
                if ($dokpensppn != null) {
                    foreach ($dokpensppn as $file) {
                        $dokpensppn_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokpensppns = $current . '-' . $dokpensppn_file_name;
                        $file->move('dokumen', $dokpensppns);

                        $dokumenpendukungsppn = new DokumenPendukungSppn;
                        $dokumenpendukungsppn->sppn_id = $request->sppn_id;
                        $dokumenpendukungsppn->dokumen_pendukung_sppn_nama = $dokpensppns;
                        $dokumenpendukungsppn->save();
                    }
                }
                $isisppn = $request->isi_sppn;
                $sum1 = 0;
                $sum2 = 0;
                foreach ($isisppn as $isi => $value) {

                    $isisppn = new isisppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    // $isisppn->master_kode_kbb = $value['kode_kbb'];

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
                        // dd($value2);
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

                        $isiuraiansppn = new IsiUraiansppn;
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
                            } else {
                                // Kondisi wapu diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'waba_sppn') {
                            $jeniswaba = substr($value2['pilih_waba_sppn'], 0, 12); // substring untuk mengambil pilih waba
                            // dd($jeniswaba);
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
                            } else {
                                // Kondisi waba diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'pph_sppn_') {
                            $jenispph = substr($value2['pilih_pph_sppn'], 0, 12); // substring untuk mengambil pilih pph
                            // dd($jenispph);

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
                            } else {
                                // Kondisi pph diluar daftar
                            }
                        } else if ($jenispajak == 'tanpa_paj') {
                            $isiuraiansppn->sppn_tanpa_pajak = "Ya";
                        } else {
                            // kondisi pilihan pajak tanpa kombinasi
                        }

                        // dd($isiuraiansppn);
                        $isiuraiansppn->save();
                        $sum1 += $b;
                    }
                }
                $sum = $sum1 + $sum2;
                $isisum = Sppn::find($request->sppn_id);
                $isisum->sppn_jumlah = $sum;
                $isisum->save();

                $faktur_pajak = $request->faktur_pajak_sppn;
                foreach ($faktur_pajak as $key => $value) {
                    $fp = new FakturPajak;
                    $fp->sppb_id = null;
                    $fp->sppn_id = $request->sppn_id;
                    $fp->faktur_pajak_nomor = $value['fp'];
                    $fp->save();
                }

                $krywn = new NamaKaryawanModel;
                $krywn->sppb_id = null;
                $krywn->sppn_id = $request->sppn_id;
                $krywn->karyawan_nama = $request->diterima_dari;
                $krywn->karyawan_nama_bank = '-';
                $krywn->karyawan_no_rek = '-';
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
                        if ($request->pilih_data_sppn == 'input_data' || $request->pilih_data_sppn == 'master_data') {
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
                }
                $spp = Spp::create([
                    'sppn_id' => $request->sppn_id,
                    'master_bagian_id' => $request->bagian_sppn,
                    'spp_status_ob' => 0,
                    'sppd_proses' => 0,
                    'sppd_status' => 0,
                    'flow_id' => $flow,
                    'sppd_posisi' => $master_flow[0]->flow_detail_urutan,
                    'spp_status_proses' => 0,
                    'spp_status_posisi' => 1,
                    'spp_status_terima' => 0,
                    'spp_buat' => $level,
                    'spp_jenis_sumber_dana' => $sumberdana,
                    'spp_tanggal' => $tanggals,
                    'company_id' => $perusahaan,
                ]);


                $request->request->add(['spp_id' => $spp->spp_id]);

                $rekam_jejak = RekamJejak::create([
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
                DB::commit();
                $action = $request->status_btn;
                if ($action == 0) {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $request->spp_id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
            } catch (\Exception $e) {
                DB::rollback();
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
        //LOGIKA FORM SPPB DAN SPPN
        else {
            DB::beginTransaction();
            try {
                $request->validate([
                    'kontrak_perjanjian_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'invoice_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'efaktur_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'dokumen_pendukung_sppb[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                    'berita_acara_file_sppb' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);
                $kontrak_perjanjian = $request->file('kontrak_perjanjian_sppb');
                if ($kontrak_perjanjian != null) {
                    $kontrak_perjanjian_file_name = str_replace("'", '', $kontrak_perjanjian->getClientOriginalName());
                    $kontrak_perjanjians = $current . '-' . $kontrak_perjanjian_file_name;
                    $kontrak_perjanjian->move('dokumen/kontrakperjanjian', $kontrak_perjanjians);
                } else {
                    $kontrak_perjanjians = null;
                }

                $invoice = $request->file('invoice_sppb');
                if ($invoice != null) {
                    $invoice_file_name = str_replace("'", '', $invoice->getClientOriginalName());
                    $invoices = $current . '-' . $invoice_file_name;
                    $invoice->move('dokumen/invoice', $invoices);
                } else {
                    $invoices = null;
                }
                $efaktur = $request->file('efaktur_sppb');
                if ($efaktur != null) {
                    $efaktur_file_name = str_replace("'", '', $efaktur->getClientOriginalName());
                    $efakturs = $current . '-' . $efaktur_file_name;
                    $efaktur->move('dokumen/efaktur', $efakturs);
                } else {
                    $efakturs = null;
                }
                $berita_acara_file = $request->file('berita_acara_file_sppb');
                if ($berita_acara_file != null) {
                    $berita_acara_file_name = str_replace("'", '', $berita_acara_file->getClientOriginalName());
                    $berita_acara_files = $current . '-' . $berita_acara_file_name;
                    $berita_acara_file->move('dokumen/beritaacara', $berita_acara_files);
                } else {
                    $berita_acara_files = null;
                }

                // $kodebagiansppb= $request->bagian_sppb;
                $kodebagianb = Bagian::where('master_bagian_id', $request->bagian_sppb)->select('master_bagian_kode')->first();
                $kodebagiann = Bagian::where('master_bagian_id', $request->bagian_sppn)->select('master_bagian_kode')->first();
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
                    // dd($urutansppn,$urutansppb);
                } else {
                    $urutansppb = $nomor_surat_sppb->maxno + 1;
                    $urutansppn = $nomor_surat_sppn->maxnosppn + 1;
                }

                $nomorsppb = $kodebagiansppb . "/SPPb/" . $urutansppb . "/" . $bulan . "/" . $tahun;
                $nomorsppn = $kodebagiansppn . "/SPPn/" . $urutansppn . "/" . $bulan . "/" . $tahun;
                if ($request->jenis == 'vendor') {
                    $data_metpen = $request->pilih_data_sppb_vendor;
                } else {
                    $data_metpen = $request->pilih_data_sppb;
                }
                $sppb = Sppb::create([
                    'master_user_id' => $user,
                    'master_bagian_id' => $request->bagian_sppb,
                    'master_bank_id' => $request->id_bank_sppb,
                    'sppb_jenis' => $request->jenis,
                    'sppb_sumber_dana' => $request->sumber_dana,
                    'sppb_no' => $nomorsppb,
                    'sppb_urutan' => $urutansppb,
                    'sppb_bulan' => $bulan,
                    'sppb_tahun' => $tahun,
                    'sppb_kwitansi' => $request->kwitansi_sppb,
                    'sppb_referensi' => $request->referensi_sppb,
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
                    'sppb_kontrak_perjanjian' => $kontrak_perjanjians,
                    'sppb_invoice' => $invoices,
                    'sppb_efaktur' => $efakturs,
                    'sppb_berita_acara_file' => $berita_acara_files,
                    'sppb_status' => 0,
                    'sppb_total' => 0,
                    'sppb_data_metpen' => $data_metpen,
                    'sppb_tidak_transfer' => $request->tidak_transfer_cat,
                    'alasan_tidak_tf' => $request->karyawan_tidak_transfer
                ]);
                // dd($sppb);
                $request->request->add(['sppb_id' => $sppb->sppb_id]);
                $dokumenpendukung = $request->file('dokumen_pendukung_sppb');
                if ($dokumenpendukung != null) {
                    foreach ($dokumenpendukung as $file) {
                        $dokumenpendukung_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungs = $current . '-' . $dokumenpendukung_file_name;
                        $file->move('dokumen', $dokumenpendukungs);
                        // DokumenPendukungSppb::create([
                        //     'sppb_id' => $request->sppb_id,
                        //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                        // ]);
                        $dokumenpendukungsppb = new \App\DokumenPendukungSppb;
                        $dokumenpendukungsppb->sppb_id = $request->sppb_id;
                        $dokumenpendukungsppb->dokumen_pendukung_sppb_nama = $dokumenpendukungs;
                        $dokumenpendukungsppb->save();
                    }
                }
                $isisppb = $request->isi_sppb;
                //dd($isisppb);
                // $countisi=count($isisppb);
                //dd($isisppb);
                // for($i=0;$i<=$countisi;$i++){
                $sum1 = 0;
                $sum2 = 0;
                foreach ($isisppb as $isi => $value) {

                    $isisppb = new isisppb;
                    $isisppb->sppb_id = $request->sppb_id;
                    // $isisppb->master_kode_kbb = $value['kode_kbb'];

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
                                } else {
                                    // Kondisi waba sppb diluar kombinasi pph
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
                                } else {
                                    // Kondisi pph diluar daftar
                                }
                            } else if ($jenispajak == 'tanpa_paj') {
                                $isiuraiansppb->sppb_tanpa_pajak = "Ya";
                            } else {
                                // kondisi pilihan pajak tanpa kombinasi
                            }
                        } else {
                            // Khusus nominal tanpa pilihan pajak
                        }

                        $isiuraiansppb->save();
                        $sum1 += $b;
                    }
                }
                $sum = $sum1 + $sum2;
                $isisum = Sppb::find($request->sppb_id);
                $isisum->sppb_total = $sum;
                $isisum->save();

                $request->validate([
                    'dokumen_pendukung_sppn[]' => 'mimes:pdf,jpg,jpeg,png|max:55000',
                ]);

                $sppn = Sppn::create([
                    'master_user_id' => $user,
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
                    'sppn_jumlah' => 0,
                    'sppn_metode_pembayaran' => $request->metode_pembayaran_sppn,
                    'alasan_tidak_tf' => $request->karyawan_tidak_transfer_sppn
                ]);
                    //dd($request->all());
                $request->request->add(['sppn_id' => $sppn->sppn_id]);
                $dokumenpendukungsppn = $request->file('dokumen_pendukung_sppn');
                if ($dokumenpendukungsppn != null) {
                    foreach ($dokumenpendukungsppn as $file) {
                        $dokumenpendukungsppn_file_name = str_replace("'", '', $file->getClientOriginalName());
                        $dokumenpendukungsppns = $current . '-' . $dokumenpendukungsppn_file_name;
                        $file->move('dokumen', $dokumenpendukungsppns);
                        // DokumenPendukungSppb::create([
                        //     'sppb_id' => $request->sppb_id,
                        //     'dokumen_pendukung_sppb_nama' => $dokumenpendukungs
                        // ]);
                        $dokumenpendukungsppnsppn = new DokumenPendukungSppn;
                        $dokumenpendukungsppnsppn->sppn_id = $request->sppn_id;
                        $dokumenpendukungsppnsppn->dokumen_pendukung_sppn_nama = $dokumenpendukungsppns;
                        $dokumenpendukungsppnsppn->save();
                    }
                }
                $isisppn = $request->isi_sppn;
                // $countisi=count($isisppn);
                // dd($countisi);
                // for($i=0;$i<=$countisi;$i++){
                $total1 = 0;
                $total2 = 0;
                foreach ($isisppn as $isi => $value) {

                    $isisppn = new isisppn;
                    $isisppn->sppn_id = $request->sppn_id;
                    // $isisppn->master_kode_kbb = $value['kode_kbb'];

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
                        // dd($value2);
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
                        $isiuraiansppn = new IsiUraiansppn;
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
                            } else {
                                // Kondisi wapu diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'waba_sppn') {
                            $jeniswaba = substr($value2['pilih_waba_sppn'], 0, 12); // substring untuk mengambil pilih waba
                            // dd($jeniswaba);
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
                            } else {
                                // Kondisi waba diluar kombinasi pph
                            }
                        } else if ($jenispajak == 'pph_sppn_') {
                            $jenispph = substr($value2['pilih_pph_sppn'], 0, 12); // substring untuk mengambil pilih pph
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
                            } else {
                                // Kondisi pph diluar daftar
                            }
                        } else if ($jenispajak == 'tanpa_paj') {
                            $isiuraiansppn->sppn_tanpa_pajak = "Ya";
                        } else {
                            // kondisi pilihan pajak tanpa kombinasi
                        }
                        $isiuraiansppn->save();
                        $total1 += $b;
                    }
                }
                $totals = $total1 + $total2;
                $isisumsppn = Sppn::find($request->sppn_id);
                $isisumsppn->sppn_jumlah = $totals;
                $isisumsppn->save();
                $spp = Spp::create([
                    'sppb_id' => $request->sppb_id,
                    'sppn_id' => $request->sppn_id,
                    'master_bagian_id' => $request->bagian_sppb,
                    'spp_status_ob' => 0,
                    'sppd_proses' => 0,
                    'sppd_status' => 0,
                    'flow_id' => $flow,
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
                        if ($request->pilih_data_sppn == 'input_data' || $request->pilih_data_sppn == 'master_data') {
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
                $rekam_jejak = RekamJejak::create([
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
                if ($action == 0) {
                    $index_cetak = 0;
                    return redirect('sppd')->with('index_cetak', $index_cetak);
                } else {
                    $index_cetak = 1;
                    $id_cetak = $request->spp_id;
                    return redirect('sppd')->with('index_cetak', $index_cetak)->with('id_cetak', $id_cetak);
                }
                //dd($spp);
            } catch (\Exception $e) {
                DB::rollback();
                return redirect::back()
                    ->with('error_code', 5);
            }
        }
    }
}
