<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Equipements;
use App\Entity\Reclamation;
use App\Form\CategorieType;
use App\Form\EquipementsType;
use App\Repository\AnnonceRepository;
use App\Repository\EquipementsRepository;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }


    // partie reclamation
    
    #[Route('/reclamation', name: 'app_reclamation', methods: ['GET'])]
    public function listReclamation(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('dashboard/reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }
    #[Route('/reclamation/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('dashboard/reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/reclamation/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }

    //partie annonces

    #[Route('/annonce', name: 'app_annonce', methods: ['GET'])]
    public function listAnnonce(AnnonceRepository $annonceRepository): Response
    {
        return $this->render('dashboard/annonce/index.html.twig', [
            'annonces' => $annonceRepository->findAll(),
        ]);
    }
     
    //partie equipements

     #[Route('/equipements', name: 'app_equipements_new', methods: ['GET', 'POST'])]
     public function newEquipement(Request $request, EntityManagerInterface $entityManager): Response
     {
         $equipement = new Equipements();
         $form = $this->createForm(EquipementsType::class, $equipement);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager->persist($equipement);
             $entityManager->flush();
 
             return $this->redirectToRoute('app_equipements_index', [], Response::HTTP_SEE_OTHER);
         }
 
         return $this->renderForm('dashboard/equipements/new.html.twig', [
             'equipement' => $equipement,
             'form' => $form,
         ]);
     }
 
     // partie Categories
     #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
     public function newCategorie(Request $request, EntityManagerInterface $entityManager): Response
     {
         $categorie = new Categorie();
         $form = $this->createForm(CategorieType::class, $categorie);
         $form->handleRequest($request);
 
         if ($form->isSubmitted() && $form->isValid()) {
             $entityManager->persist($categorie);
             $entityManager->flush();
 
             return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
         }
 
         return $this->renderForm('dashboard/categorie/new.html.twig', [
             'categorie' => $categorie,
             'form' => $form,
         ]);
     }
}
