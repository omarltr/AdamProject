<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RegistrationFormType;
use App\Repository\ReservationRepository;
use Symfony\Component\HttpFoundation\Request;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil', methods: ['GET', 'POST'])]
    public function index(AnnonceRepository $annonceRepository, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $form = $this->createForm(RegistrationFormType::class, $user, [
            'include_password' => false,  'include_terms' => false, // Exclude password field for profile editing
        ]);
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();
                return $this->redirectToRoute('app_profil');
            }
        }
        $annonces = $annonceRepository->findByUser($this->getUser());

        return $this->render('profil/index.html.twig', [
            'annonces' => $annonces,
            'form' => $form->createView()
        ]);
    }
    #[Route('/reservation', name: 'app_reservation_user')]
    public function reservation(AnnonceRepository $annonceRepository, Request $request, ReservationRepository $reservationRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'include_password' => false,  'include_terms' => false, // Exclude password field for profile editing
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/reservation.html.twig', [

            'reservations' => $reservationRepository->findByUser($user),
            'form' => $form->createView()
        ]);
    }
}
