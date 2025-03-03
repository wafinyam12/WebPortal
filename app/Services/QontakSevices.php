<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QontakSevices
{
    /*
    * Properties 
    */
    protected $baseUrl;

    protected $token;

    /*
    * Constructor Qontak API
    */
    public function __construct()
    {
        $this->baseUrl = env('QONTAK_WHATSAPP_URL');
        $this->token = env('QONTAK_WHATSAPP_TOKEN');
    }

    /*
    * Send Message to Qontak API
    */
    public function sendMessage($phone, $name, $templateId, $body)
    {
        // Base Url Send Message
        $endpoint = $this->baseUrl . 'broadcasts/whatsapp/direct';

        // Payload
        $payload = [
            'to_number' => $phone,
            'to_name' => $name,
            'message_template_id' => $templateId,
            'channel_integration_id' => '0a62d1f1-bfc6-4d82-8197-9dbfda6ba41a',
            'language' => [
                'code' => 'id',
            ],
            'parameters' => [
                'body' => $body
            ],
        ];

        // Send Message
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Content-Type' => 'application/json',
        ])->post($endpoint, $payload);

        // Check if the request was successful
        if ($response->successful()) {
            return $response;
        } else {
            $errorMessage = $response->body();
            throw new \Exception("Request to Qontak API failed: " . $errorMessage);
        }
    }
}
