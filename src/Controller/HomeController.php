<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categorie;
use App\Entity\Annonce;
use App\Entity\Adresse;
use App\Entity\Images;
use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AnnonceRepository;
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AnnonceRepository $etat , EntityManagerInterface $em ): Response
    {
       
        $repository = $em->getRepository(Categorie::class);
        $Categories = $repository->findAll();
        $Annonces = $em->getRepository(Annonce::class)->findAll();
        $user = $this->getUser();
        $Avis = $em->getRepository(Avis::class)->findAll();

    
        $Adresses = [];
        foreach ($Annonces as $annonce) {
            $adresse = $em->getRepository(Adresse::class)->findOneBy(['annonce' => $annonce->getId()]);
            if ($adresse) {
                $Adresses[$annonce->getId()] = $adresse;
            }
        }

        // Example: Fetch principal Image for each Annonce
        $Images = [];
        foreach ($Annonces as $annonce) {
            $image = $em->getRepository(Images::class)->findOneBy(['annonce' => $annonce->getId(), 'is_principal' => true]);
            if ($image) {
                $Images[$annonce->getId()] = $image;
            }
        }

        // Render the template with fetched data
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'Categories' => $Categories,
            'Annonces' => $Annonces,
            'Adresses' => $Adresses,
            'Images' => $Images,
            'Avis' => $Avis,
            'user' => $user
        ]);
    }

}
