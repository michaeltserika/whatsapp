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
    private string $phoneId;

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
        $this->phoneId = $_ENV['WA_PHONE_ID'] ?? 'DEFAULT_PHONE_ID';

        // Configuration du client HTTP
        $this->client = $client;

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
            // Envoi du message via WhatsApp
            $response = $this->client->request('POST', 'https://graph.facebook.com/v20.0/' . $this->phoneId . '/messages', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'text',
                    'text' => [
                        'body' => "$messageContent",
                    ],
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseContent = $response->getContent(false);

            if ($statusCode === 200) {
                $this->logger->info('Message envoyé et enregistré pour le numéro ' . $phone);
                $this->saveMessage($phone, $messageContent);
                $this->addFlash('success', 'Message envoyé avec succès.');
            } else {
                $this->logger->error('Erreur lors de l\'envoi du message via WhatsApp. Code statusCode != 200: ' . $statusCode . ', Réponse: ' . $responseContent);
                
                // Vérifier l'erreur spécifique et essayer d'envoyer un SMS
                if ($responseContent && strpos($responseContent, 'Recipient phone number not in allowed list') !== false) {
                    $this->logger->info('Envoi d\'un SMS en raison d\'un problème avec WhatsApp.');
                    $this->sendSms($phone, $messageContent);
                }
                
                $this->addFlash('error', 'Erreur lors de l\'envoi du message statusCode: '. $statusCode . ', Reponse : ' . $responseContent);
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du message try catch: ' . $e->getMessage());
            $this->addFlash('error', 'Une erreur s\'est produite.');
        }

        return $this->redirectToRoute('app_conversation');
    }

    private function saveMessage(string $phone, string $messageContent): void
    {
        $contact = $this->contactRepository->findOneBy(['phone_number' => $phone]);
        
        if ($contact) {
            $this->logger->info('Contact trouvé', [
                'contact_id' => $contact->getId(),
                'phone' => $contact->getPhoneNumber(),
            ]);
    
            $messageEntity = new Message();
            $messageEntity->setContact($contact);
            $messageEntity->setContent($messageContent);
            $messageEntity->setSentAt(new \DateTime());
            $messageEntity->setIsSent(true); 
    
            $this->entityManager->persist($messageEntity);
            $this->entityManager->flush();
    
            $this->logger->info('Message enregistré dans la base de données', [
                'message_content' => $messageContent,
                'contact_id' => $contact->getId(),
            ]);
        } else {
            $this->logger->warning('Aucun contact trouvé pour le numéro ' . $phone);
        }
    }
    

    private function sendSms(string $phone, string $messageContent): void
    {
        // Configuration du client SMS ici
        $smsClient = new SmsClient(); // Remplacez SmsClient par votre client SMS
        try {
            $smsResponse = $smsClient->sendSms($phone, $messageContent);
            if ($smsResponse->isSuccessful()) {
                $this->logger->info('Message SMS envoyé avec succès à ' . $phone);
            } else {
                $this->logger->error('Erreur lors de l\'envoi du SMS. Détails: ' . $smsResponse->getErrorMessage());
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
        }
    }

}
