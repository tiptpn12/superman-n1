<?php

namespace App\Http\Controllers;

use App\Bagian;
use App\Company;
use App\DetailLogin;
use App\HakAkses;
use App\HistoryLogin;
use App\Http\Requests\StoreChangePasswordRequest;
use App\Mail\ResetPasswordMail;
use App\Ui;
use App\User;
use GuzzleHttp\Client;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;


class UserController extends Controller
{
    // function __construct(Redirector $redirect){
    //     $this->middleware(function ($request,$next) {
    //         // fetch session and use it in entire class with constructor
    //         $this->user = session()->get('username');
    //         //dd($this->user);
    //         //return $next($request);
    //         if($this->user == null){

    //             $redirect->to('login')->send();

    //         }
    //         else{
    //             return $next($request);
    //         }
    //     });


    // }
    // public function generate_token(){
    //     $user = User::all();
    //     foreach($user as $item => $val){
    //         $User = User::find($val->master_user_id);
    //         $User->api_token = Str::random(88);
    //         $User->save();
    //     }
    // }
    public function change_password()
    {
        $id = Session::get('id');
        $user = DB::table('master_user')->where('master_user_id', $id)->select('master_user.*')->first();
        return view('page.user.change_password')->with('user', $user);
    }

    public function change_password_store(StoreChangePasswordRequest $request)
    {
        // Validasi otomatis terjadi di StoreChangePasswordRequest
        $validated = $request->validated();

        try {
            // Cek apakah password saat ini benar
            $user = User::find($request->id);
            if ($request->current_password == decrypt($user->master_user_password)) {
                // Jika benar, update password
                $user->master_user_password = encrypt($request->new_password);
                $user->api_token = Str::random(88); // Generate token baru
                $user->save();

                return redirect('change_password')->with('alert-success', 'Berhasil mengubah password!');
            } else {
                return redirect('change_password')->with('alert', 'Gagal mengubah password! Password Salah');
            }
        } catch (\Throwable $th) {
            return redirect('change_password')->with('alert', 'Gagal mengubah password! ' . $th->getMessage());
        }
    }


    public function reloadcaptcha()
    {
        return response()->json(['captcha' => captcha_img('math')]);
    }

    public function loginpost(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        // Cek apakah pengguna terkunci
        if (session()->has('login_attempts')) {
            $attempts = session('login_attempts', 0);
            $lastAttemptTime = session('last_attempt_time', 0);
            $lockoutTime = 30; // detik

            if ($attempts >= 5 && (time() - $lastAttemptTime) < $lockoutTime) {
                $remainingTime = $lockoutTime - (time() - $lastAttemptTime);
                return redirect('login')->with('alert', "Terlalu banyak percobaan yang gagal. Silakan coba lagi dalam {$remainingTime} detik.");
            }
        }

        $request->validate([
            'captcha' => 'required|captcha',
        ], ['captcha' => 'Captcha tidak valid', 'required' => 'Captcha harus diisi']);

        $data = User::whereRaw('master_user_name = ?', [$username])->first();

        if ($data) {
            $result = @file_get_contents("https://ipinfo.io/?token=6718d4ac50f02a");

            $ipaddress = $_SERVER['REMOTE_ADDR'];
            $agent = new Agent();
            $browser = $agent->browser();
            $os = $agent->platform();

            $level = HakAkses::where('master_hak_akses_id', $data->master_hak_akses_id)->first();

            try {
                $pw = decrypt($data->master_user_password);
            } catch (DecryptException $e) {
                return redirect('login')->with('alert', 'Login gagal! Kesalahan saat mendekripsi password.');
            }

            // Cek password
            if ($password == $pw) {
                // Login berhasil
                session()->regenerateToken();
                Session::forget('login_attempts'); // Reset jumlah percobaan
                Session::forget('last_attempt_time'); // Reset waktu percobaan terakhir

                // Simpan data sesi pengguna
                Session::put('username', $data->master_user_name);
                Session::put('petugas_pp', $data->user_petugas_pp);
                Session::put('hak_akses', $data->master_hak_akses_id);
                Session::put('cost_center', $data->master_cost_center_id);
                Session::put('company', $data->master_hak_akses_id == 1 ? 5 : $data->company_id);
                Session::put('bagian', $data->master_bagian_id);
                Session::put('id', $data->master_user_id);
                Session::put('level', $level->master_hak_akses_level);
                Session::put('grup_ui', $level->grup_ui_id);
                Session::put('email', $data->user_emails);

                if ($result) {
                    $ip = json_decode($result);
                    // Simpan detail login
                    $detail_login = new DetailLogin;
                    $detail_login->detail_login_ip = $ip->ip;
                    $detail_login->detail_login_hostname = isset($ip->hostname) ? $ip->hostname : '-';
                    $detail_login->detail_login_city = $ip->city;
                    $detail_login->detail_login_region = $ip->region;
                    $detail_login->detail_login_country_code = $ip->country;
                    $detail_login->detail_login_loc = $ip->loc;
                    $detail_login->detail_login_country = country_name($ip->country);
                    $detail_login->detail_login_browser = $browser;
                    $detail_login->detail_login_os = $os;
                    $detail_login->save();

                    $request->request->add(['detail_login_id' => $detail_login->detail_login_id]);

                    // Simpan riwayat login
                    $history = new HistoryLogin;
                    $history->master_user_id = $data->master_user_id;
                    $history->history_login_status = 1;
                    $history->detail_login_id = $request->detail_login_id;
                    $history->save();
                }

                // Redirect berdasarkan peran pengguna
                $bagian = Session::get('bagian');
                $level = Session::get('level');
                if ($bagian == 2 && $level == 88) {
                    return redirect('dashboard_kabag_kasi')->with('alert', 'Berhasil Login');
                } else if ($bagian == 10 && $level == 99) {
                    return redirect('dashboard')->with('alert', 'Berhasil Login');
                } else {
                    return redirect('dashboard')->with('alert', 'Berhasil Login');
                }
            } else {

                $attempts = session('login_attempts', 0) + 1;
                session(['login_attempts' => $attempts]);
                session(['last_attempt_time' => time()]); // Set waktu percobaan terakhir

                if ($attempts >= 5) {
                    $lockoutTime = 30; // dalam detik
                    $unlockTime = time() + $lockoutTime; // waktu saat pengguna bisa mencoba lagi
                    session(['unlock_time' => $unlockTime]); // simpan waktu unlock di session
                    return redirect('login')
                        ->with('unlock_time', $unlockTime);
                }

                return redirect('login')->with('alert', 'Login gagal! Username / Password Salah');
            }
        } else {

            $attempts = session('login_attempts', 0) + 1;
            session(['login_attempts' => $attempts]);
            session(['last_attempt_time' => time()]); // Set waktu percobaan terakhir

            if ($attempts >= 5) {
                $lockoutTime = 30; // dalam detik
                $unlockTime = time() + $lockoutTime; // waktu saat pengguna bisa mencoba lagi
                session(['unlock_time' => $unlockTime]); // simpan waktu unlock di session
                return redirect('login')
                    ->with('unlock_time', $unlockTime);
            }

            return redirect('login')->with('alert', 'Login gagal! Username / Password Salah');
        }
    }


    public function logout()
    {
        session()->flush();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('login')->with('alert', 'Anda berhasil logout');
    }

    public function index()
    {
        $this->user = session()->get('username');
        // dd($this->user);
        // return $next($request);
        if ($this->user == null) {

            return redirect('login');
        } else {
            $user = User::leftJoin('master_bagian', 'master_bagian.master_bagian_id', '=', 'master_user.master_bagian_id')
                ->leftJoin('master_hak_akses', 'master_hak_akses.master_hak_akses_id', '=', 'master_user.master_hak_akses_id')
                ->leftJoin('master_company', 'master_company.company_id', '=', 'master_user.company_id')
                ->get();
            $email = DB::table('master_user')->select('user_emails')->get();
            $company = Company::where('company_status', 1)->get();
            $bagian = Bagian::where('master_bagian_status', 1)->get();
            $hak_akses = HakAkses::where('master_hak_akses_status', 1)->get();

            foreach ($user as $item) {
                try {
                    $pw = decrypt($item->master_user_password);
                    $item['master_user_password_decrypt'] = $pw;
                } catch (DecryptException $e) {
                    $item['master_user_password_decrypt'] = '';
                }
            }
            //dd($user);

            $data = array(
                'user' => $user,
                'company' => $company,
                'bagian' => $bagian,
                'hak_akses' => $hak_akses,
                'emails' => $email
            );
            // dd($email);
            return view('page.user.user', $data);
        }
    }

    public function store(Request $request)
    {
        User::create([
            'master_bagian_id' => $request->bagian,
            'master_hak_akses_id' => $request->hak_akses,
            'company_id' => $request->company,
            'master_user_name' => $request->username,
            'master_user_password' => encrypt($request->password),
            'user_emails' => $request->email,
            'nomor_handphone' => $request->nomor_hp,
            'api_token' => Str::random(88),
        ]);

        return redirect('/user');
    }

    public function getBagianByCompany($id)
    {
        $bagian = Bagian::where('master_bagian_status', 1)->where('company_id', $id)->get();
        return response()->json($bagian);
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);
        $user->master_bagian_id = $request->bagian;
        $user->master_hak_akses_id = $request->hak_akses;
        $user->company_id = $request->company;
        $user->master_user_name = $request->username;
        $user->master_user_password = encrypt($request->password);
        $user->user_emails = $request->email;
        $user->nomor_handphone = $request->nomor_hp;
        $user->save();

        return redirect('/user');
    }

    public function destroy($id, $status)
    {
        $user = User::find($id);
        $user->master_user_status = $status == 1 ? 0 : 1;
        $user->save();
        return redirect('/user');
    }

    public function forgot_password()
    {
        return view('auth.forgot-password');
    }
    public function forgot_password_store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:master_user,user_emails',
            'captcha' => 'required|captcha'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
            'captcha.required' => 'Captcha harus diisi',
            'captcha.captcha' => 'Captcha salah',
        ]);

        $user = User::where('user_emails', $request->email)->first();

        $token = Str::random(60);
        Session::put('reset_token', $token);
        Session::put('reset_token_expiration', now()->addMinutes(15));

        Mail::to($user->user_emails)->send(new ResetPasswordMail($token, $user->user_emails));

        return back()->with('alert-success', 'We have emailed your password reset link!');
    }
    public function reset_password(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');

        if (Session::get('reset_token') !== $token || Session::get('reset_token_expiration') < now()) {
            return redirect()->route('forgot.password')->withErrors(['token' => 'This reset link is invalid or expired.']);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }
    public function reset_password_post(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6',
        ], [
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Password tidak cocok',
            'password.min' => 'Password minimal 6 karakter',
        ]);

        $user = User::where('user_emails', $request->email)->first();

        // Mengupdate password user
        $user->master_user_password = encrypt($request->password);
        $user->save();

        Session::forget('reset_token');
        Session::forget('reset_token_expiration');

        return redirect()->route('login')->with('alert-success', 'Your password has been reset successfully.');
    }
}
