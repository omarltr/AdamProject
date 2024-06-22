<?php
namespace App\Controller;

    use App\Entity\User;
    use App\Form\RegistrationFormType;
    use App\Security\AppCustomAuthAuthenticator;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
    use Symfony\Contracts\Translation\TranslatorInterface;
    use Symfony\Component\HttpFoundation\File\Exception\FileException;

    class RegistrationController extends AbstractController
    {
        #[Route('/register', name: 'app_register')]
        public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppCustomAuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
        {
            $user = new User();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $currentDate = new \DateTime();
                 $currentDate->format('Y-m-d'); 
                $user->setDateConnexion($currentDate);
                $user->setDateInscription($currentDate);


                /////profilephot 

                $photoDirectory = 'uploads/Profilephoto';

                $photoFile = $form->get('photo')->getData();
                if ($photoFile) {
                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = uniqid().'.'.$photoFile->guessExtension();
    
                    // Move the file to the directory where profile photos are stored
                    try {
                        $photoFile->move(
                            $photoDirectory,
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Handle exception if something happens during file upload
                        $this->addFlash('danger', 'Failed to upload photo. Please try again.');
                        return $this->render('registration/register.html.twig', [
                            'registrationForm' => $form->createView(),
                        ]);
                    }
    
                    // Update the 'photo' property to store the file name
                    $user->setPhoto($newFilename);
                }else {
                    // If no photo uploaded, set a default image filename
                    $user->setPhoto('default_profile_photo.jpg'); // Change 'default_profile_photo.jpg' to your default image filename
                }
                ////////////////

                $user->setRoles(['ROLE_USER']);

                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }
    }
