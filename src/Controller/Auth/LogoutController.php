<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

#[IsGranted('ROLE_ADMIN')]
class LogoutController extends AbstractController
{
    #[Route('/auth/logout', name: 'app_auth_logout')]
    public function index(Security $security): Response
    {
        // logout the user in on the current firewall
        // $response = $security->logout();

        // you can also disable the csrf logout
        $response = $security->logout(false);

        return $this->redirect('/auth/login');
    }
}
