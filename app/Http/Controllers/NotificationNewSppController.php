<?php

namespace App\Http\Controllers;

// use App\User;
Use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Notifications\NewSppNotification;
use Illuminate\Support\Facades\Notification;

class NotificationNewSppController extends Controller
{

    public function __construct()
    {
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

    public function index(){
        $listNotifikasi = User::find(Session::get('id'))->notifications;

        return view('page.home_notifikasi', compact('listNotifikasi'));
    }


    // ini hanya untuk tujuan tes
    // mengirim notifikasi ke user dengan hak akses 4 yaitu bagian penerima
    public function sendTesNotif(){
        $notificationData = [
                'spp_id' => 1,
                'username' => "barokah"
            ];

            $petugasPenerima = User::where("master_hak_akses_id", '=', 4)->get();

            Notification::sendNow($petugasPenerima, new NewSppNotification($notificationData));

            dd("Tes Selesai");
    }

    /**
     * Tandai notifikasi yang dipilih sebagai dibaca.
     * function ini akan update kolom read_at pada table notifications.
     * notifikasi yang sudah ditandai sebagai dibaca tidak akan ditampilkan lagi
     *
     * @param request request berupa id notifikasi {id}
     * @return response no content
     */
    public function markNotifAsRead(Request $request){
        User::find(Session::get('id'))->unreadNotifications->when($request->id, function($query) use ($request){
            return $query->where('id', $request->id);
        })->markAsRead();

        return response()->noContent();
    }

    /**
     * Tandai notifikasi semua notifikasi user sebagai dibaca.
     * notifikasi yang sudah ditandai sebagai dibaca tidak akan ditampilkan lagi
     *
     * @return response no content
     */
    public function markAllNotifAsRead(){
        User::find(Session::get('id'))->unreadNotifications->markAsRead();

        return response()->noContent();
    }

    public function deleteAllNotifications(){
        User::find(Session::get('id'))->notifications()->delete();

        return response()->noContent();
    }

    // get notifikasi yang belum dibaca oleh user yang login
    public function getUserNotification(){
        //dd(Session::get('id'));
        //dd(User::find(Session::get('id'))->notifications);
        return User::find(Session::get('id'))->unreadNotifications;
    }

    // public function getAllNotfi
}
