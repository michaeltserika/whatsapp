<?php

namespace App\Controller\Compain;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Campaign;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CampaignRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class CompainController extends AbstractController
{
    #[Route('/campaign', methods: ['GET'], name: 'app_compain_compain')]
    public function index(
        Request $request,
        CampaignRepository $repos
    ): Response
    {
        $user = $this->getUser();

        $listCampaign = $repos->findBy([
            'user_id' => $user->getId(),
        ]);

        return $this->render('compain/index.html.twig', [
            'campaigns' => $listCampaign,
        ]);
    }

    #[Route('/campaign/{id}/edit', methods: ['GET', 'POST'], name: 'app_compain_edit')]
    public function editRender(
        Request $request,
        CampaignRepository $repos,
        $id
    ): Response
    {
        $user = $this->getUser();

        $campaign = $repos->find($id);

        if (is_null($campaign))
            throw $this->createNotFoundException(
                'No campaign found for id "'.$id.'"'
            );
        

        return $this->render('compain/add.html.twig', [
            'title' => 'Modifier un campagne',
            'action' => '/campagn/'. $id .'/edit' ,
            'campaign' => $campaign,
        ]);
    }

    #[Route('/campaign/{id}/delete', methods: ['POST'], name: 'app_compain_delete')]
    public function deleteAction(
        Request $request,
        CampaignRepository $repos,
        $id
    ) {
        $user = $this->getUser();

        $campaign = $repos->find($id);

        if (is_null($campaign))
            throw $this->createNotFoundException(
                'No campaign found for id '.$id
            );
            // return $this->throwNotFoundException();

        $repos->remove($campaign);

        return $this->json([
            'success' => true,
        ]);
    }

    #[Route('/campaign', methods: ['POST'], name: 'app_compain_store')]
    public function store(EntityManagerInterface $entityManager, Request $request): Response
    {
        $title = $request->get('title');
        $email = $request->get('Email');
        $smtp = $request->get('detailsmtp');
        $emailList = $request->get('emailist');
        $templates = $request->get('templates');
        $status = $request->get('status');
        
        $compaign = new Campaign;

        $compaign->setTitle($title);

        $compaign->setEmail($email);

        $compaign->setTemplate($templates);
        $compaign->setEmailList($emailList);

        $compaign->setSmtpDetails($smtp);

        $compaign->setStatus($status);

        $compaign->setCreateOn(new \DateTime());

        $compaign->setUserId($this->getUser()->getId());

        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($compaign);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return $this->redirect('/campaign');
    }

    #[Route('/compain/add', methods: ['GET'], name: 'app_compaign_add')]
    public function add(): Response
    {
        return $this->render('compain/add.html.twig', [
            'controller_name' => 'CompainController',
        ]);
    }
}
