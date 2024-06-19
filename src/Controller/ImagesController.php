<?php

namespace App\Controller;

use App\Entity\Images;
use App\Entity\Annonce;
use App\Form\ImagesType;
use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/images')]
class ImagesController extends AbstractController
{
    #[Route('/', name: 'app_images_index', methods: ['GET'])]
    public function index(ImagesRepository $imagesRepository): Response
    {
        return $this->render('images/index.html.twig', [
            'images' => $imagesRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_images_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Annonce $annonce): Response
    {
        $image = new Images();
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('image')->getData();
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoDirectory = 'uploads/images';
    
                // Move the file to the directory where profile photos are stored
                try {
                    $photoFile->move(
                        $photoDirectory,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                    $this->addFlash('danger', 'Failed to upload photo. Please try again.');
                    return $this->render('images/new.html.twig', [
                        'form' => $form->createView(),
                    ]);
                }
    
                // Update the 'photo' property to store the file name
                $image->setImage($newFilename);
                $image->setIsPrincipal(true);
                $image->setAlt($originalFilename);
            }
    
            $image->setAnnonce($annonce);
            $entityManager->persist($image);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('images/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }
    
    


    #[Route('/{id}', name: 'app_images_show', methods: ['GET'])]
    public function show(Images $image): Response
    {
        return $this->render('images/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_images_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_images_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('images/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_images_delete', methods: ['POST'])]
    public function delete(Request $request, Images $image, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_images_index', [], Response::HTTP_SEE_OTHER);
    }
}