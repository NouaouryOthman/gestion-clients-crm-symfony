<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        return $this->render('client/index.html.twig', ['clients' => $clients]);
    }

    /**
     * @Route("/newClient", name="newClient")
     */
    public function createClient(Request $request, EntityManagerInterface $manager)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($client);
            $manager->flush();
            $this->addFlash(
                'success',
                "Le client <strong>{$client->getNom()} {$client->getPrenom()}</strong> a été bien enregistré!");
            return $this->redirectToRoute('index');
        }
        return $this->render('client/newClient.html.twig', [
            'form' => $form->createView()
        ]);
    }
}