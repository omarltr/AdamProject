<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }


        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findByUser($this->getUser()),
        ]);
    }

    #[Route('/{id}/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Annonce $annonce): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        $reservation = $reservation->setAnnonce($annonce);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/accepter', name: 'app_reservation_accepter', methods: ['GET', 'POST'])]
    public function accepter(int $id, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        // Vérifie si l'annonce existe
        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée');
        }

        // Changer l'état de l'annonce à "Réservé" (état 3)
        $annonce->setEtat(3);
        $entityManager->flush();

        // Redirection vers la page de détails de l'annonce
        return $this->redirectToRoute('app_annonce_show', ['id' => $annonce->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/refuser', name: 'app_reservation_refuser', methods: ['GET', 'POST'])]
    public function refuser(int $id, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($id);

        // Vérifie si l'annonce existe
        if (!$annonce) {
            throw $this->createNotFoundException('Annonce non trouvée');
        }

        // Changer l'état de l'annonce à "Annulé" (état 4)
        $annonce->setEtat(4);
        $entityManager->flush();


        return $this->redirectToRoute('app_annonce_show', ['id' => $annonce->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
