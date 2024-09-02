<?php

namespace App\Controller\Message;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class TemplateController extends AbstractController
{
    #[Route('/templates', name: 'app_message_template')]
    public function index(): Response
    {
        return $this->render('message/template.html.twig', [
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Message/TemplateController.php',
        ]);
    }
}
