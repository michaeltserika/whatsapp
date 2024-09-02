<?php 

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WhatsappService
{
    private HttpClientInterface $client;
    private string $base_url;
    private array $payload;

    public function __construct(HttpClientInterface $client)
    {
        $businnes_number = $_SERVER['WA_PHONE_ID'] ?? $_ENV['WA_PHONE_ID'];
        $access_token = $_SERVER['FB_PERMANENT_ACCESS_TOKEN'] ?? $_ENV['FB_PERMANENT_ACCESS_TOKEN'];
        $this->base_url = ($_SERVER['FB_GRAPH_BASE'] ?? 'https://graph.facebook.com/v20.0') . '/' . $businnes_number;

        $this->payload = ['messaging_product' => "whatsapp"];

        $this->client = $client->withOptions([
            'headers' => [
                'Authorization' => "Bearer " . $access_token,
                "Accept" => "application/json",
                'Content-Type' => "application/json"
            ],
        ]);
    }

    /**
     * Validation d'un numéro de téléphone au format international (+XXX...).
     */
    private function validatePhoneNumber(string $phone): bool
    {
        if (preg_match('/^\+\d{10,15}$/', $phone)) {
            return true;
        }
        throw new \InvalidArgumentException("Numéro de téléphone invalide.");
    }

    /**
     * Envoi d'un message texte via WhatsApp.
     */
    public function sendTextMessage(string $phone, string $text, ?string $message_id = null): string
    {
        $this->validatePhoneNumber($phone);

        $this->payload['to'] = $phone;
        $this->payload['text'] = ['body' => $text];

        if ($message_id) {
            $this->payload['context'] = ['message_id' => $message_id];
        }

        return $this->sendRequest('/messages');
    }

    /**
     * Envoi d'un message avec un média (image, vidéo, etc.) via WhatsApp.
     */
    public function sendMediaMessage(string $phone, string $mediaUrl, string $mediaType = 'image', string $caption = ''): string
    {
        $this->validatePhoneNumber($phone);

        $this->payload['to'] = $phone;
        $this->payload['type'] = $mediaType;
        $this->payload[$mediaType] = [
            'link' => $mediaUrl,
            'caption' => $caption
        ];

        return $this->sendRequest('/messages');
    }

    /**
     * Récupère les messages entrants de WhatsApp.
     */
    public function receiveMessages(): array
    {
        return $this->sendRequest('/messages', 'GET');
    }

    /**
     * Méthode privée pour gérer les requêtes HTTP vers l'API WhatsApp.
     */
    private function sendRequest(string $endpoint, string $method = 'POST'): mixed
    {
        try {
            $response = $this->client->request($method, $this->base_url . $endpoint, [
                'json' => $this->payload
            ]);

            if ($response->getStatusCode() !== 200) {
                throw new HttpException($response->getStatusCode(), "Erreur lors de la communication avec l'API WhatsApp");
            }

            return $response->getContent();
        } catch (\Exception $e) {
            // Gestion des exceptions pour renvoyer un message d'erreur clair
            throw new \RuntimeException("Échec de la requête vers l'API WhatsApp : " . $e->getMessage());
        }
    }
}
