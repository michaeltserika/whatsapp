<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class WhatsAppController extends AbstractController
{
    private string $business_number;
    private string $access_token;
    private HttpClientInterface $client;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, HttpClientInterface $client)
    {
        $this->business_number = $_SERVER['WA_BUSINESS_NUMBER'] ?? $_ENV['WA_BUSINESS_NUMBER'];
        $this->access_token = $_SERVER['FB_PERMANENT_ACCESS_TOKEN'] ?? $_ENV['FB_PERMANENT_ACCESS_TOKEN'];
        $this->logger = $logger;

        $this->client = $client->withOptions([
            'base_uri' => 'https://graph.facebook.com/v20.0/' . $this->business_number,
            'headers' => [
                'Authorization' => "Bearer " . $this->access_token,
                'Content-Type' => 'application/json'
            ],
        ]);
    }

    #[Route("/whatsapp/webhook", methods: ['GET'], name: "app_whatsapp_send_webhook")]
    public function sendMessage(): Response
    {
        return new Response("Hello world");
    }

    #[Route("/whatsapp/webhook", methods: ['POST'], name: "app_whatsapp_receive_webhook")]
    public function receiveMessage(Request $request): Response
    {
        $reqBody = json_decode($request->getContent());

        if (
            isset($reqBody->object) &&
            $reqBody->object === 'whatsapp_business_account' &&
            isset($reqBody->entry[0]->changes[0]->value)
        ) {
            $messageData = $reqBody->entry[0]->changes[0]->value;
            if ($messageData->messages[0]->type === 'document') {
                $this->logger->info("Received document!");
                $mediaId = $messageData->messages[0]->document->id;
                $fileName = str_replace(' ', '_', $messageData->messages[0]->document->filename);

                $response = $this->client->request('GET', "/$mediaId", [
                    'headers' => [
                        'Authorization' => "Bearer " . $this->access_token
                    ]
                ]);

                // Enregistrement du fichier téléchargé (exemple)
                file_put_contents('/path/to/save/' . $fileName, $response->getContent());

                return new Response("Document reçu et sauvegardé.", 200);
            } else {
                $this->logger->error('Type de message inconnu. Rien à faire.');
            }
        } else {
            return new Response("Requête non reconnue.", 404);
        }

        return new Response("", 200);
    }

    #[Route("/whatsapp/verify", methods: ['GET', 'POST'], name: "verifyToken")]
    public function verifyToken(Request $request): Response
    {
        $TOKEN = $this->access_token;

        if ($request->isMethod('get')) {
            $hubMode = $request->get('hub_mode');
            $hubChallenge = $request->get('hub_challenge');
            $hubVerifyToken = $request->get('hub_verify_token');

            if ($hubMode === 'subscribe' && $hubChallenge && $hubVerifyToken === $TOKEN) {
                $this->logger->info('WEBHOOK_VERIFIED');
                return new Response($hubChallenge, 200);
            }

            return new Response("Token de vérification incorrect.", 403);
        }

        if ($request->isMethod('post')) {
            $signature = $request->headers->get('X-Hub-Signature-256');
            $payload = $request->getContent();
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $TOKEN);

            if ($signature !== $expectedSignature) {
                $this->logger->error('La signature hash ne correspond pas.');
                return new Response('SIGNATURE HASH INVALIDE', 403);
            }

            $body = json_decode($payload);

            if (isset($body->object) && $body->object === 'page') {
                foreach ($body->entry as $entry) {
                    $changeEvent = $entry->changes[0];
                    if ($changeEvent->field !== 'feed') {
                        continue;
                    }

                    $postId = $changeEvent->value->post_id;
                    // Logique pour gérer l'événement du post_id
                }

                return new Response('WEBHOOK EVENT HANDLED', 200);
            }

            return new Response('EVENEMENT WEBHOOK INVALIDE', 403);
        }

        return new Response("", 403);
    }

    #[Route("/config/webhook", methods: ['GET'], name: 'app_config_webhook_whatsapp')]
    public function verifyWebhook(Request $request): Response
    {
        $webhookVerifyToken = $_SERVER['WA_WEBHOOK_VERIFY_TOKEN'] ?? $_ENV['WA_WEBHOOK_VERIFY_TOKEN'];
        
        $mode = $request->get('hub_mode');
        $token = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode === "subscribe" && $token === $webhookVerifyToken) {
            $this->logger->info('Webhook verified successfully!');
            return new Response($challenge, 200);
        }
        
        return new Response("Vérification du webhook échouée.", 403);
    }

    #[Route("/config/webhook", methods: ['POST'], name: 'app_webhook_whatsapp')]
    public function configWebhook(Request $request): Response
    {
        $reqBody = json_decode($request->getContent());
        $this->logger->info("Incoming webhook message: " . json_encode($reqBody));

        if (
            is_object($reqBody) &&
            isset($reqBody->entry[0]->changes[0]->value)
        ) {
            $message = $reqBody->entry[0]->changes[0]->value->messages[0] ?? null;

            if ($message && $message->type === 'text') {
                $this->logger->info("Received text message: " . $message->text->body);

                // Répondre au message
                $this->client->request('POST', '/messages', [
                    'json' => [
                        'messaging_product' => "whatsapp",
                        'to' => $message->from,
                        'text' => [
                            'body' => "Echo: " . $message->text->body,
                        ],
                        'context' => [
                            'message_id' => $message->id,
                        ],
                    ],
                ]);

                return new Response('Message traité.', 200);
            }
        }

        return new Response('Requête non reconnue.', 404);
    }
}
