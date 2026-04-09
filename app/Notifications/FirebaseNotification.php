<?php

namespace App\Notifications;

class FirebaseNotification {

    /**
     * Send notifikasi ke satu device berdasarkan token
     * token dapat di get dari table user
     *
     */
    public static function sendTo($token, $notification, $data, $isBroadcast = false)
    {

        if ($isBroadcast){
            $data = [
                'registration_ids' => $token,
                'notification' => $notification,
                'data' => $data
            ];
        } else {
            $data = [
                'to' => $token,
                'notification' => $notification,
                'data' => $data
            ];
        }

        $dataString = json_encode($data);

        $headers = [
            'Authorization: key=' . config('services.firebase.key'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);
        // var_dump($response);
        // die();

        return json_decode($response);
    }

}