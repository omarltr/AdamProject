<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/dashboard')]
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
    
    #[Route('/reclamation', name: 'app_reclamation_index', methods: ['GET'])]
    public function reclamation(ReclamationRepository $reclamationRepository): Response
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
}
