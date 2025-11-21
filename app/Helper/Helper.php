<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Helper
{
    public static function sendMessage($phone, $message)
    {
        try {
            // $response = Http::post(env('URL_GATEWAY') . 'send-message', [
            //     'api_key' => env('API_GATEWAY'),
            //     'sender' => env('SENDER_PHONE'),
            //     'number' => $phone,
            //     'message' => $message,
            // ]);
            $response = Http::withHeaders([
                'Authorization' => env('API_KEY')
            ])->post(env('URL_GATEWAY'), [
                'recipient_type' => 'individual',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);
            return $response;
        } catch (\Throwable $th) {
            return $th;
        }
        // return true;

    }

    public static function getGrade($score)
    {
        if (intval($score) < 50) {
            return 'E';
        } else if (intval($score) >= 50 && intval($score) <= 59) {
            return 'D';
        } else if (intval($score) >= 60 && intval($score) <= 69) {
            return 'C';
        } else if (intval($score) >= 70 && intval($score) <= 85) {
            return 'B';
        } else if (intval($score) >= 86 && intval($score) <= 100) {
            return 'A';
        }
    }

    // public static function sendBroadCast($phone, $message)
    // {
    //     try {
    //         $response = Http::withHeaders([
    //             'Authorization' => env('API_KEY_BROADCAST')
    //         ])->post(env('URL_GATEWAY_BROADCAST'), [
    //             'recipient_type' => 'individual',
    //             'to' => $phone,
    //             'type' => 'text',
    //             'text' => [
    //                 'body' => $message
    //             ]
    //         ]);
    //         Log::info($response);
    //         return $response;
    //     } catch (\Throwable $th) {
    //         Log::info($th);
    //         return $th;
    //     }
    // }

    public static function sendBroadCast($phone, $message): bool
    {
        try {
            // Mengambil API Key dan URL Gateway dari environment variables #v5
            $apiKey = env('API_KEY_BROADCAST');
            $urlGateway = env('URL_GATEWAY_BROADCAST');

            // $apiKey = env('API_KEY');
            // $urlGateway = env('URL_GATEWAY');

            // Log informasi sebelum mengirim request
            // Log::info('Mencoba mengirim broadcast.', [
            //     'phone' => $phone,
            //     'url' => $urlGateway,
            //     'api_key_status' => !empty($apiKey) ? 'Set' : 'Not Set', // Cek apakah API Key terisi
            // ]);

            // Melakukan request POST ke API gateway
            $response = Http::withHeaders([
                'Authorization' => $apiKey // Menggunakan API Key dari .env
            ])->post($urlGateway, [
                'recipient_type' => 'individual',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ]);

            // Log respons lengkap dari API gateway
            // Log::info('Respons API Broadcast untuk ' . $phone . ':', [
            //     'status' => $response->status(), // Kode status HTTP (e.g., 200, 400, 500)
            //     'body' => $response->body(),     // Body respons dari API
            //     'successful' => $response->successful(), // Apakah respons berstatus 2xx
            //     'ok' => $response->ok(),         // Apakah respons berstatus 200
            // ]);

            // Mengembalikan true jika respons HTTP adalah 2xx (berhasil), selain itu false
            return $response->successful();
        } catch (Throwable $th) {
            // Menangkap setiap pengecualian yang terjadi selama proses HTTP request
            // Log::error('Terjadi kesalahan saat mengirim broadcast untuk ' . $phone . ':', [
            //     'error_message' => $th->getMessage(),
            //     'file' => $th->getFile(),
            //     'line' => $th->getLine(),
            //     'trace' => $th->getTraceAsString(),
            // ]);

            // Mengembalikan false karena terjadi exception (kegagalan)
            return false;
        }
    }
}
