<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index() {
        return $this->render('client/index.html.twig');
    }
    /**
     * @Route("/newClient", name="ticket_create")
     */
    public function createClient(Request $request,EntityManagerInterface $manager)
    {
        $client =new Client();
        $form=$this->createForm(TicketType::class,$client);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($client);
            $manager->flush();
            $this->addFlash(
                'success',
                "Le client <strong>{$client->getNom()} {$client->getPrenom()}</strong> a bien été enregistré!"
            );
        }
        return $this->render('ticket/newClient.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
