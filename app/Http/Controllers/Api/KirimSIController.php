<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class KirimSIController extends Controller
{
    /**
     * Bertindak sebagai proxy untuk mengambil data dari service Python.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchAll()
    {
        Log::info('Meneruskan permintaan ke service Python untuk mengambil data SPP AMCO.');

        $pythonFetcherUrl = config('api.python_fetcher_url');

        if (!$pythonFetcherUrl) {
            Log::error('URL service Python (PYTHON_FETCHER_URL) tidak dikonfigurasi.');
            return response()->json(['message' => 'Service pengambilan data tidak dikonfigurasi dengan benar.'], 503); // 503 Service Unavailable
        }

        try {
            // Membuat instance Guzzle Client
            $client = new \GuzzleHttp\Client([
                'timeout' => 120.0, // Timeout dalam detik
            ]);

            // Mengirim request GET ke server Python
            $response = $client->request('GET', $pythonFetcherUrl);

            // Guzzle akan melempar exception untuk status 4xx/5xx, jadi kita hanya perlu cek status 200
            if ($response->getStatusCode() === 200) {
                Log::info('Berhasil menerima data dari service Python.');
                // Mengembalikan respons dari Python langsung ke client
                return response($response->getBody()->getContents(), 200)->header('Content-Type', 'application/json');
            }

            // Fallback jika status bukan 200 (meskipun jarang terjadi tanpa exception)
            Log::warning('Service Python merespons dengan status non-200: ' . $response->getStatusCode());
            return response()->json(['message' => 'Service pengambilan data merespons dengan error.'], 502);
        } catch (\Exception $e) {
            Log::error('Gagal berkomunikasi dengan service Python: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat berkomunikasi dengan service pengambilan data.', 'error' => $e->getMessage()], 502); // 502 Bad Gateway
        }
    }
}
