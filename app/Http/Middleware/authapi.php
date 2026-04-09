<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class authapi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = DB::table('master_user')->where('api_token','=',[$token, 1])->first(); 
        if ($user){
            $request->user = $user;
            return $next($request);
        }
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada token'
        ],403);
    }
}
