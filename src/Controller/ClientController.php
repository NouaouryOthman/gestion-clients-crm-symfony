<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Note;
use App\Form\ClientType;
use App\Form\NoteType;
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

    /**
     * @Route("/afficherClient/{id}", name="client")
    */
    public function afficherClient(Client $client) {
        return $this->render('client/afficherClient.html.twig',['client'=>$client]);
    }

    /**
     * @Route("/modifierClient/{id}", name="modifierClient")
     */
    public function modifierClient(Client $client, Request $request) {
        $form=$this->createForm(ClientType::class,$client);
        $form->handleRequest($request);
        $manager=$this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($client);
            $manager->flush();
            return $this->redirectToRoute("client",['id'=>$client->getId()]);
        }
        return $this->render("client/modifierClient.html.twig",[
            'form'=>$form->createView(),
            'client'=>$client
        ]);
    }

    /**
     * @Route("/ajouterNoteClient/{id}", name="ajouterNoteClient")
     */
    public function ajouterNoteClient(Client $client, Request $request) {
        $note = new Note();
        $note->setClient($client);
        $form=$this->createForm(NoteType::class,$note);
        $form->handleRequest($request);
        $manager=$this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($note);
            $manager->flush();
            return $this->redirectToRoute("client",['id'=>$note->getClient()->getId()]);
        }
        return $this->render("client/ajouterNoteClient.html.twig",[
            'form'=>$form->createView(),
            'client'=>$client]);
    }
}