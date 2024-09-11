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
class ContactController  extends AbstractController
{
    #[Route('/contacts', name: 'contact_list', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        return $this->render('contacts/list.html.twig', ['contacts' => $contacts]);
    }

    #[Route('/contacts/add', name: 'contact_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($contact);
            $em->flush();
            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contacts/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/contacts/{id}/edit', name: 'contact_edit', methods: ['GET', 'POST'])]
    public function edit(Contact $contact, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('contact_list');
        }

        return $this->render('contacts/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/contacts/{id}', name: 'contact_delete', methods: ['GET'])]
    public function delete(Contact $contact, EntityManagerInterface $em): Response
    {
        $em->remove($contact);
        $em->flush();
        return $this->redirectToRoute('contact_list');
    }
}
