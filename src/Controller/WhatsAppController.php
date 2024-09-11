<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contact;
use App\Entity\Message;
use App\Repository\ContactRepository;
use App\Repository\MessageRepository;

class WhatsAppController extends AbstractController
{
    private string $businessNumber;
    private string $accessToken;
    private HttpClientInterface $client;
    private LoggerInterface $logger;
    private EntityManagerInterface $entityManager;
    private ContactRepository $contactRepository;
    private MessageRepository $messageRepository;

    public function __construct(
        LoggerInterface $logger, 
        HttpClientInterface $client, 
        EntityManagerInterface $entityManager, 
        ContactRepository $contactRepository, 
        MessageRepository $messageRepository
    ) {
        // Chargement des variables d'environnement
        $this->businessNumber = $_ENV['WA_BUSINESS_NUMBER'] ?? 'DEFAULT_BUSINESS_NUMBER';
        $this->accessToken = $_ENV['FB_PERMANENT_ACCESS_TOKEN'] ?? 'DEFAULT_ACCESS_TOKEN';

        // Configuration du client HTTP
        $this->client = $client->withOptions([
            'base_uri' => 'https://graph.facebook.com/v20.0/' . $this->businessNumber,
            'headers' => [
                'Authorization' => "Bearer " . $this->accessToken,
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->contactRepository = $contactRepository;
        $this->messageRepository = $messageRepository;
    }

    #[Route("/whatsapp/conversations", name: "app_conversation")]
    public function showConversations(Request $request): Response
    {
        $contactId = $request->query->get('contact_id');
        $contacts = $this->contactRepository->findAll();

        $selectedContact = null;
        $messages = [];

        if ($contactId) {
            $selectedContact = $this->contactRepository->find($contactId);
            if ($selectedContact) {
                $messages = $this->messageRepository->findByContact($selectedContact);
            }
        }

        return $this->render('whatsapp/conversations.html.twig', [
            'contacts' => $contacts,
            'selected_contact' => $selectedContact,
            'messages' => $messages,
        ]);
    }

    #[Route("/whatsapp/send", name: "send_message", methods: ['POST'])]
    public function sendMessage(Request $request): Response
    {
        $phone = $request->request->get('phone');
        $messageContent = $request->request->get('message');
        
        if (empty($phone) || empty($messageContent)) {
            $this->addFlash('error', 'Le numéro de téléphone et le message sont obligatoires.');
            return $this->redirectToRoute('app_conversation');
        }

        try {
            // Envoi du message via l'API WhatsApp
            $response = $this->client->request('POST', 'messages', [
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'body' => $messageContent,
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseContent = $response->getContent(false);

            if ($statusCode === 200) {
                $this->logger->info('Message envoyé avec succès à ' . $phone);

                // Sauvegarde dans la base de données
                $contact = $this->contactRepository->findOneBy(['phoneNumber' => $phone]);
                if ($contact) {
                    $messageEntity = new Message();
                    $messageEntity->setContact($contact);
                    $messageEntity->setContent($messageContent);
                    $messageEntity->setSentAt(new \DateTime());

                    $this->entityManager->persist($messageEntity);
                    $this->entityManager->flush();

                    $this->logger->info('Message enregistré dans la base de données.');
                } else {
                    $this->logger->warning('Aucun contact trouvé pour le numéro : ' . $phone);
                }

                $this->addFlash('success', 'Message envoyé et enregistré avec succès.');
            } else {
                $this->logger->error('Erreur lors de l\'envoi : ' . $statusCode . ', Détails: ' . $responseContent);
                $this->addFlash('error', 'Erreur lors de l\'envoi du message.');
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du message : ' . $e->getMessage());
            $this->addFlash('error', 'Une erreur s\'est produite lors de l\'envoi du message.');
        }

        return $this->redirectToRoute('app_conversation');
    }
}
