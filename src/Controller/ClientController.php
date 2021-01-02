<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Note;
use App\Entity\Tache;
use App\Form\ClientType;
use App\Form\NoteType;
use App\Form\TacheType;
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
        $compteur = $this->getDoctrine()->getRepository(Note::class)->compteurNotes($client->getId());
        $note = $this->getDoctrine()->getRepository(Note::class)->derniereNote($client->getId());
        return $this->render('client/afficherClient.html.twig',['client'=>$client,'note'=>$note, 'compteur'=>$compteur]);
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

    /**
     * @Route("/notesClient/{id}", name="notesClient")
     */
    public function notesClient(Client $client) {
        return $this->render('client/notesClient.html.twig',['client'=>$client]);
    }

    /**
     * @Route("/ajouterTacheClient/{id}", name="ajouterTacheClient")
     */
    public function ajouterNote(Client $client, Request $request) {
        $tache = new Tache();
        $tache->setClient($client);
        $tache->setStatut("A faire");
        $form=$this->createForm(TacheType::class,$tache);
        $form->handleRequest($request);
        $manager=$this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($tache);
            $manager->flush();
            return $this->redirectToRoute("client",['id'=>$tache->getClient()->getId()]);
        }
        return $this->render("client/ajouterTacheClient.html.twig",[
            'form'=>$form->createView(),
            'client'=>$client]);
    }

    /**
     * @Route("/supprimerTache/{id}", name="supprimerTache")
     */
    public function supprimerTacheClient(Tache $tache) {
        $manager=$this->getDoctrine()->getManager();
        $manager->remove($tache);
        $manager->flush();
        return $this->redirectToRoute("client",['id'=>$tache->getClient()->getId()]);
    }
}