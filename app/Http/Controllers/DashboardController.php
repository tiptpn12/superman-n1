<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            // fetch session and use it in entire class with constructor
            // dd(Session::all());
            $this->user = session()->get('username');
            $this->hakakses = session()->get('hak_akses');
            $this->bagian = session()->get('bagian');
            //return $next($request);
            if ($this->user == null && !$request->query('token')) {
                // dd(true);
                return redirect('login');
            } else {
                if ($request->query('token')) {
                    if ($request->query('token') != 'dc6f15e9aed0d633') {
                        return redirect(route('login'));
                    } else {
                        Session::put('company', 5);
                        Session::put('hak_akses', 46);
                    }
                }
                return $next($request);
            }
        });
    }
    public function index(Request $request)
    {
        if ($request->query('token')) {
            return view('dashboard_a');
        }
        // dd(Session::all());
        $grup_ui = Session::get('grup_ui');
        $id = Session::get('id');
        $level = Session::get('level');
        $bagian = Session::get('bagian');
        $petugas_pp = Session::get('petugas_pp');
        $company = Session::get('company');
        // dd($bagian);
        $hakakses = Session::get('hak_akses');
        $flow = DB::table('master_flow_detail')
            ->where('company_id', $company)
            ->where('flow_detail_urutan', $hakakses)
            ->leftjoin('master_company_detail', 'master_flow_detail.flow_id', '=', 'master_company_detail.flow_id')
            ->select('master_flow_detail.flow_id')
            ->pluck('master_flow_detail.flow_id');
        //dump($flow,$grup_id,$level,$hakakses,$petuga

        $datas = 0;
        $data_bagian = 0;
        $data_operator = 0;
        // dd($hakakses);
        $user = DB::table('master_user')->where('master_user.master_user_id', $id)->select('master_user.*')->first();

        $users = DB::table('master_flow_detail')
            ->where('flow_detail_urutan', '=', $hakakses)
            ->get();
        // dump('grup ui', $grup_ui, 'id', $id, 'level', $level, 'bagian', $bagian,'user', $user, 'hakakses', $hakakses);
        //  COUNT LAMA
        // if( $grup_ui == 1 ){

        //     //count
        //     $data_operator = DB::table('spp')->where('sppd_posisi',$hakakses)->where('sppd_status','!=',4)->where('master_bagian_id',$bagian)->count();
        // // dd($data_operator);
        // }else if($user->master_bagian_id == 111){
        //     $datas = DB::table('spp')->where('sppd_posisi',$hakakses)->count();
        // // dd($datas);
        // }else{
        //     $data_bagian=DB::table('spp')->where('master_bagian_id','=',$bagian)->where('sppd_posisi',$hakakses)->where('spp.sppd_proses','!=',NULL)->count();
        // //  dd($data_bagian);
        // }

        if ($grup_ui == 1 || $hakakses == 18) {
            $total_proses = DB::table('spp')
                ->where('master_bagian_id', '=', $bagian)
                ->where('sppd_posisi', $hakakses)
                ->whereBetween('sppd_status', [0, 3])

                ->get();

            $total_proses = $total_proses->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            });

            $total_proses = $total_proses->count();
        } else if ($grup_ui == 2) {
            if ($petugas_pp == 1) {
                $to_do = DB::table('spp')
                    ->where('sppd_posisi', $hakakses)
                    ->where('company_id', $company)
                    ->whereBetween('sppd_status', [1, 2])

                    ->get();

                $to_do = $to_do->filter(function ($value, $key) use ($flow) {
                    return in_array($value->flow_id, $flow->toArray());
                })->count();


                $revisi = DB::table('spp')
                    ->where('spp.company_id', $company)
                    ->where('spp.sppd_revisi', '=', $hakakses)
                    ->where('spp.sppd_proses', '<', $users[0]->flow_akses)

                    ->orWhere('spp.company_id', $company)
                    ->where('spp.sppd_status', '=', 3)
                    ->where('spp.sppd_posisi', '=', $hakakses)

                    ->get();

                $revisi = $revisi->filter(function ($value, $key) use ($flow) {
                    return in_array($value->flow_id, $flow->toArray());
                })->count();

                $total_proses = $to_do + $revisi;
            } else {
                $to_do = DB::table('spp')
                    ->where('master_bagian_id', '=', $bagian)
                    ->where('sppd_posisi', $hakakses)
                    ->whereBetween('sppd_status', [1, 2])
                    ->get();

                $to_do = $to_do->filter(function ($value, $key) use ($flow) {
                    return in_array($value->flow_id, $flow->toArray());
                })->count();

                $revisi = DB::table('spp')
                    ->where('spp.company_id', $company)
                    ->where('spp.sppd_revisi', '=', $hakakses)
                    ->where('spp.sppd_proses', '<', $users[0]->flow_akses)

                    ->orWhere('spp.company_id', $company)
                    ->where('spp.sppd_status', '=', 3)
                    ->where('spp.sppd_posisi', '=', $hakakses)

                    ->get();

                $revisi = $revisi->filter(function ($value, $key) use ($flow) {
                    return in_array($value->flow_id, $flow->toArray());
                })->count();

                $total_proses = $to_do + $revisi;
            }
        } else if ($grup_ui == 3) {
            $to_do = DB::table('spp')
                ->where('sppd_posisi', $hakakses)
                ->where('company_id', $company)
                ->whereBetween('sppd_status', [1, 2])
                ->get();

            $to_do = $to_do->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();


            $revisi = DB::table('spp')
                ->where('spp.company_id', $company)
                ->where('spp.sppd_revisi', '=', $hakakses)
                ->where('spp.sppd_proses', '<', $users[0]->flow_akses)

                ->orWhere('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->where('spp.sppd_posisi', '=', $hakakses)

                ->get();

            $revisi = $revisi->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();

            $total_proses = $to_do + $revisi;
        } else if ($grup_ui == 4) {
            $to_do = DB::table('spp')
                ->where('sppd_posisi', $hakakses)
                ->where('company_id', $company)
                ->whereBetween('sppd_status', [1, 2])

                ->get();

            $to_do = $to_do->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();

            $revisi = DB::table('spp')
                ->where('spp.company_id', $company)
                ->where('spp.sppd_revisi', '=', $hakakses)
                ->where('spp.sppd_proses', '<', $users[0]->flow_akses)

                ->orWhere('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->where('spp.sppd_posisi', '=', $hakakses)

                ->get();
            $revisi = $revisi->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();

            $total_proses = $to_do + $revisi;
        } else if ($grup_ui == 7) {
            $to_do = DB::table('spp')
                ->where('sppd_posisi', $hakakses)
                ->where('company_id', $company)
                ->whereBetween('sppd_status', [1, 2])

                ->get();
            $to_do = $to_do->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();
            $revisi = DB::table('spp')
                ->where('spp.company_id', $company)
                ->where('spp.sppd_revisi', '=', $hakakses)
                ->where('spp.sppd_proses', '<', $users[0]->flow_akses)

                ->orWhere('spp.company_id', $company)
                ->where('spp.sppd_status', '=', 3)
                ->where('spp.sppd_posisi', '=', $hakakses)

                ->get();
            $revisi = $revisi->filter(function ($value, $key) use ($flow) {
                return in_array($value->flow_id, $flow->toArray());
            })->count();

            $total_proses = $to_do + $revisi;
        } else {
            $total_proses = 0;
        }


        // dd($data_bagian);
        return view('dashboard_a', compact('data_operator', 'datas', 'user', 'data_bagian', 'total_proses'));
    }
}
