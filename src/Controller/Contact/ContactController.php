<?php

namespace App\Controller\Contact;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Contact;
use App\Form\ContactType;

#[IsGranted('ROLE_ADMIN')]
class ContactController extends AbstractController
{
    #[Route('/contacts', methods: ['GET'], name: 'app_contact_contact')]
    public function index(
        Request $request,
        ContactRepository $repos,

    ): Response
    {
        $contacts = $repos->findAll();
        
        // dd($contacts);

        return $this->render('contacts/list.html.twig', [
            "contacts" => $contacts,
        ]);
    }

    #[Route('/contacts/ajouter', name: 'app_contact_create')]
    public function add(
        Request $request,
        ContactRepository $repos,
        // EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();

        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $contact = $form->getData();

            $contact->setUserId($user->getId());

            $repos->save($contact);


            if ($success)
                $this->addFlash('notice', 'Contact a été bien enregistrer.');

            return $this->redirectRoute('app_', ['id' => $fzef]);
        }

        return $this->render('contacts/add.html.twig', [
            'form' => $form,
            // 'contact' => $contact
        ]);
    }



    #[Route('/contacts/unscribe', methods: ['GET'], name: 'app_contact_unscribe')]
    public function unscribe(): Response
    {
        
    }
}
