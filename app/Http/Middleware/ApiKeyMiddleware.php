<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil API key dari header request
        $apiKey = $request->header('X-API-Key');

        // Ambil API key yang valid dari file config/api.php
        $validApiKey = config('api.si_key');

        // Cek apakah key ada dan valid
        if (!$apiKey || $apiKey !== $validApiKey) {
            // Jika tidak valid, kirim respons error
            return response()->json(['message' => 'Unauthorized. Invalid or missing API Key.'], 401);
        }

        // Jika valid, lanjutkan request ke controller
        return $next($request);
    }
}
