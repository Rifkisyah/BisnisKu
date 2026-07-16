<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FonnteService
{
    protected $token;
    protected $baseUrl = 'https://api.fonnte.com';

    public function __construct()
    {
        $this->token = env('FONNTE_TOKEN');
    }

    public function sendMessage($target, $message, $url = null)
    {
        if (!$this->token) {
            return false;
        }

        $data = [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62', // Default Indonesia
        ];

        if ($url) {
            $data['url'] = $url;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token
            ])->post($this->baseUrl . '/send', $data);

            return $response->json();
        } catch (\Exception $e) {
            return false;
        }
    }
}
