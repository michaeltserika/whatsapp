<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\WhatsappService;
use Symfony\Component\HttpFoundation\Request;

class SendMessageController extends AbstractController
{
    public function __construct(
        WhatsappService $whatsapp_service,
    )
    {}

    #[Route('/', methods: ['GET'], name: 'app_index')]
    public function frontoffice(Request $request): Response
    {
        return $this->redirect('/campaign');
    }

    #[Route('/send/message', methods: ['GET','POST'], name: 'app_send_message')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('get'))
            return $this->render('send_message/index.html.twig', [
                'controller_name' => 'SendMessageController',
            ]);
        
        if ($request->isMethod('post'))
        {
            // Instantiate the WhatsAppCloudApi super class.
            $whatsapp_cloud_api = $this->whasapp_service->getWhatsappCloudApi();

            $phone = $request->get('phone');
            $message = $request->get('message');

            $whatsapp_cloud_api->sendTextMessage($phone, $message);

            return $this->json([
                'hello' => "world !"
            ]);
        }
    }
}
