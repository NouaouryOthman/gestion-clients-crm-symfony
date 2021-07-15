<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Societe;
use App\Form\SocieteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SocieteController extends AbstractController
{
    /**
     * @Route("/ajouterSociete", name="societe")
     */
    public function index(EntityManagerInterface $manager, Request $request)
    {
        $societe = new Societe();
        $form=$this->createForm(SocieteType::class,$societe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($societe);
            $manager->flush();
            return $this->redirectToRoute('listeClients');
        }
        return $this->render('societe/ajouterSociete.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
