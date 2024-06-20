<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Images;
use App\Entity\Categorie;


#[Route('/annonce')]
class AnnonceController extends AbstractController
{
    #[Route('/', name: 'app_annonce_index', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository , EntityManagerInterface $em): Response
    {
        $Annonces= $annonceRepository->findAll();
        $repository = $em->getRepository(Categorie::class);
        $Categories = $repository->findAll();

        $Images = [];
        foreach ($Annonces as $annonce) {
            $image = $em->getRepository(Images::class)->findOneBy(['annonce' => $annonce->getId(), 'is_principal' => true]);
            if ($image) {
                $Images[$annonce->getId()] = $image;
            }
        }
        
        return $this->render('annonce/index.html.twig', [
            'Annonces' => $Annonces,
            'Images' => $Images,
            'Categories' => $Categories

        ]);
    }
    #[Route('/categorie/{id}', name: 'app_annonce_categorie', methods: ['GET'])]
    public function categorie(AnnonceRepository $annonceRepository , EntityManagerInterface $em, Categorie $categorie): Response
    {
        $Annonces= $annonceRepository->findBy(['categorie' => $categorie->getId()]);
        $repository = $em->getRepository(Categorie::class);
        $Categories = $repository->findAll();

        $Images = [];
        foreach ($Annonces as $annonce) {
            $image = $em->getRepository(Images::class)->findOneBy(['annonce' => $annonce->getId(), 'is_principal' => true]);
            if ($image) {
                $Images[$annonce->getId()] = $image;
            }
        }
        
        return $this->render('annonce/index.html.twig', [
            'Annonces' => $Annonces,
            'Images' => $Images,
            'Categories' => $Categories

        ]);
    }

    #[Route('/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        $annonce->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {
           
            $entityManager->persist($annonce);
            $entityManager->flush();
            $annonceId = $annonce->getId();
            return $this->redirectToRoute('app_adresse_new', [ 'id' => $annonceId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_show', methods: ['GET'])]
    public function show(Annonce $annonce): Response
    {
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }
}
