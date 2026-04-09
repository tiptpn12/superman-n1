<?php

namespace App\Helpers;

class API{
    protected static $response = [
        'code' => null,
        'message' => null,
        'data' => null,
    ];

    public static function createApi($data = null)
    {
        $response = [
            'data' => $data,
        ];
        return response()->json($response);
    }
}