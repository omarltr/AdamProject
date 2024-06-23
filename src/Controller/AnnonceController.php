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
use App\Entity\Equipements;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\EquipementsRepository;
use App\Repository\ReservationRepository;

#[Route('/annonce')]
class AnnonceController extends AbstractController
{

    #[Route('/', name: 'app_annonce_index', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, EntityManagerInterface $em): Response
    {

        $search = $request->query->get('search', '');
        $Annonces = $annonceRepository->findBySearchTerm($search);
        $repository = $em->getRepository(Categorie::class);
        $Categories = $repository->findAll();

        $Images = [];
        foreach ($Annonces as $annonce) {
            $image = $em->getRepository(Images::class)->findOneBy(['annonce' => $annonce->getId(), 'is_principal' => true]);
            if ($image) {
                $Images[$annonce->getId()] = $image;
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('annonce/_annonce_list.html.twig', [
                'Annonces' => $Annonces,
                'Images' => $Images,
                'Categories' => $Categories
            ]);
        }
        return $this->render('annonce/index.html.twig', [
            'Annonces' => $Annonces,
            'Images' => $Images,
            'Categories' => $Categories

        ]);
    }
    #[Route('/categorie/{id}', name: 'app_annonce_categorie', methods: ['GET'])]
    public function categorie(AnnonceRepository $annonceRepository, EntityManagerInterface $em, Categorie $categorie): Response
    {
        $Annonces = $annonceRepository->findBy(['categorie' => $categorie->getId()]);
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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);
        $annonce->setUser($this->getUser());

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($annonce);
            $entityManager->flush();
            $annonceId = $annonce->getId();
            return $this->redirectToRoute('app_adresse_new', ['id' => $annonceId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_show', methods: ['GET', 'POST'])]
    public function show(ReservationRepository $reservationRepository, EquipementsRepository $equipementsRepository, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipements = $equipementsRepository->findAll();
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        // Vérifie si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // Création d'une nouvelle réservation liée à l'annonce
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setAnnonce($annonce);

        // Création du formulaire de réservation
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        // Traitement du formulaire soumis
        if ($form->isSubmitted() && $form->isValid()) {
            // Modifier l'état de l'annonce
            $annonce->setEtat(2);

            $entityManager->persist($reservation);
            $entityManager->flush();

            // Redirection vers la même page après la réservation
            return $this->redirectToRoute('app_annonce_show', ['id' => $annonce->getId()]);
        }

        // Récupération des réservations de l'annonce
        $reservations = $reservationRepository->findByAnnonce($annonce);

        // Affichage de la page avec le formulaire de réservation
        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
            'equipements' => $equipements,
            'reservations' => $reservations
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_show', ['id' => $annonce->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $annonce->getId(), $request->request->get('_token'))) {

            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/annonce/{annonceId}/desaffecter/{id}', name: 'app_desaffecter_equipement', methods: ['GET'])]
    public function desaffecterEquipement(int $annonceId, int $id, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'équipement et l'annonce concernés
        $equipement = $entityManager->getRepository(Equipements::class)->find($id);
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);

        // Vérifier si l'équipement et l'annonce existent
        if (!$equipement || !$annonce) {
            throw $this->createNotFoundException('L\'équipement ou l\'annonce n\'existe pas.');
        }

        // Désaffecter l'équipement de l'annonce
        $annonce->removeEquipement($equipement);
        $entityManager->flush();

        // Redirection vers la même page d'annonce après la désaffectation
        return $this->redirectToRoute('app_annonce_show', ['id' => $annonceId]);
    }

    #[Route('/annonce/{annonceId}/affecter', name: 'app_affecter_equipement', methods: ['POST'])]
    public function affecterEquipement(int $annonceId, Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipementId = $request->request->get('equipement');

        // Récupérer l'équipement et l'annonce concernés
        $equipement = $entityManager->getRepository(Equipements::class)->find($equipementId);
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);

        // Vérifier si l'équipement et l'annonce existent
        if (!$equipement || !$annonce) {
            throw $this->createNotFoundException('L\'équipement ou l\'annonce n\'existe pas.');
        }

        // Affecter l'équipement à l'annonce
        $annonce->addEquipement($equipement);
        $entityManager->flush();

        // Redirection vers la même page d'annonce après l'affectation
        return $this->redirectToRoute('app_annonce_show', ['id' => $annonceId]);
    }
}
