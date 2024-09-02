<?php
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', methods: ['GET', 'POST'], name: 'app_register')]
    public function index(
        Request $request,
        UserRepository $userRepos,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($request->isMethod('post'))
        {
            // dd($request->get('_email'));
            $email = $request->get('_email');
            $password = $request->get('_password');

            $user = new User();
            $user->setEmail($email);
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $password
            );
            $user->setPassword($hashedPassword);

            // tell Doctrine you want to (eventually) save the Product (no queries yet)
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            return $this->redirect('/register');
        }
        
        return $this->render('auth/register/index.html.twig', [
            'error' => new \stdClass,
            // 'path' => 'src/Controller/Contact/ContactController.php',
        ]);
    }
}