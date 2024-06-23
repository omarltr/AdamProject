<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Equipements;
use App\Form\EquipementsType;
use App\Repository\EquipementsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/equipements')]
class EquipementsController extends AbstractController
{
    #[Route('/', name: 'app_equipements_index', methods: ['GET'])]
    public function index(EquipementsRepository $equipementsRepository): Response
    {
        return $this->render('equipements/index.html.twig', [
            'equipements' => $equipementsRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_equipements_show', methods: ['GET'])]
    public function show(Equipements $equipement): Response
    {
        return $this->render('equipements/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }
 
    #[Route('/{id}/edit', name: 'app_equipements_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipements $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementsType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipements_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipements/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipements_delete', methods: ['POST'])]
    public function delete(Request $request, Equipements $equipement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $equipement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipements_index', [], Response::HTTP_SEE_OTHER);
    }
}
