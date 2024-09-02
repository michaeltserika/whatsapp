<?php

namespace App\Controller\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, Request};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\WhatsappService;

#[IsGranted('ROLE_ADMIN')]
class MessageController extends AbstractController
{

    public function __construct(
        protected WhatsappService $whatsapp_services
    )
    {}

    #[Route('/message/send/manually', methods: ['GET'], name: 'app_message_guide')]
    public function index(): Response
    {
        return $this->render('message/manually.html.twig', [
            'controller_name' => 'CompainController',
        ]);
    }

    #[Route('/message/send/manually', methods: ['POST'], name: 'app_manually_send')]
    public function sendAction(
        Request $request
    ): Response
    {
        $phone_number = $request->get('phone_manual');
        $phone_list = $request->get('contacts');
        $message = $request->get('messages');

        $currentUser = $this->getUser();

        foreach($phone_list as $phone)
        {
            $cloud_api = $this->whatsapp_services->sendTextMessage($phone, $message);

            // $cloud_api->sendTextMessage($phone, $message);
        }

        return new Response('Message envoyer evec success');
    }
}
