<?php

use Illuminate\Support\Facades\Http;

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

    public static function sendBroadCast($phone, $message)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => env('API_KEY_BROADCAST')
            ])->post(env('URL_GATEWAY_BROADCAST'), [
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
    }
}
