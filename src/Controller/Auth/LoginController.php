<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/auth/login', name: 'app_login')]
    #[Route('/connexion', name: 'app_connexion')]
    public function index(
        Request $request,
        AuthenticationUtils $authenticationUtils,
        Security $security,
    ): Response
    {
        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // dd($lastUsername);

        // if ($security->isGranted('ROLE_ADMIN'))
        //     return $this->redirect('/campaign');

        return $this->render('auth/login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    
}
