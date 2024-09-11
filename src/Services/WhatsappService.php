<?php 

namespace App\Services;

use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WhatsappService
{
    protected $whatsapp_cloud_api;

    protected $base_url;

    protected $payload;

    public function __construct(
        private HttpClientInterface $client
    )
    {

        $businnes_number = $_SERVER['WA_PHONE_ID'] ?? 'thephone_number';
        $access_token = $_SERVER['FB_PERMANENT_ACCESS_TOKEN'] ?? 'permanent_tije';

        $this->base_url = $_SERVER['FB_GRAPH_BASE'] ?? 'https://graph.facebook.com/v20.0';
        $this->base_url .= '/'. $businnes_number;
        
        $this->payload = [
            'messaging_product' => "whatsapp"
        ];

        $this->client = $this->client->withOptions([
            'headers' => [
                'Authorization' => "Bearer ".$access_token,
                "Accept" => "application/json",
                'Content-Type' => "application/json"
            ],
        ]);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getBaseUrl()
    {
        return $this->base_url;
    }

    public function sendTextMessage($phone, $text, $message_id = null)
    {
        $this->payload['to'] = $phone;
        $this->payload['text'] = [ 'body' => $text ];
        

        if ( ! is_null($message_id) )
            $this->payload['context'] = ['message_id' => $message_id ];

        $response = $this->client->request('POST', $this->base_url.'/messages', [
            'json' => $this->payload
        ]);

        return $response->getContent();
    }
    
}