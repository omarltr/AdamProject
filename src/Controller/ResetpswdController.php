<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;


use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;


///
use App\Form\ForgotPasswordType;    
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Swift_Mailer;
use Swift_Message;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetpswdController extends AbstractController
{
    #[Route('/resetpswd', name: 'app_resetpswd')]
    public function index(): Response
    {
        return $this->render('resetpswd/index.html.twig', [
            'controller_name' => 'ResetpswdController',
        ]);
    }



    #[Route('/forgot', name: 'forgot')]
    public function forgot(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        // Create the form
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the submitted email
            $email = $form->get('email')->getData();
            echo "<script>console.log(" . json_encode($email) . ");</script>";

            // Call the function to test and notify invalid email
            $this->testAndNotifyInvalidEmail($email, $entityManager, $mailer);

            // Redirect to a page indicating that the password reset email has been sent
        }

        // Render the template with the form
        return $this->render('resetpswd/Forgotpassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        private function testAndNotifyInvalidEmail(string $email, EntityManagerInterface $entityManager, MailerInterface $mailer): void
        {
            // Check if the email exists in the database
            $userRepository = $entityManager->getRepository(User::class);
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // If the email does not exist, send an email notification
                $transport = Transport::fromDsn('smtp://ademkhdher@gmail.com:rijx%20emmd%20hfbq%20kehx@smtp.gmail.com:587');
                $mailer = new \Symfony\Component\Mailer\Mailer($transport);

                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setTokenExpiration(new \DateTime('+1 hour'));
                $entityManager->persist($user);
                $entityManager->flush();
                $resetUrl = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                echo "<script>console.log(" . json_encode($resetUrl) . ");</script>";

                $emailMessage = (new Email())
                    ->from('ademkhdher@gmail.com')
                    ->to($email)
                    ->subject('on va envouye le lient pour reset password ')
                    ->html("<p>Click the following link to reset your password: <a href='{$resetUrl}'>Reset Password</a></p>");

                $mailer->send($emailMessage);
                $this->addFlash('message', 'Votre compte a été récupéré avec succès. Veuillez vérifier votre e-mail pour continuer.');

            
            }else{
                $this->addFlash('danger', 'cette adresse n\'existe pas');
            }
        }

        
        #[Route('/reset-password/{token}', name: 'reset_password')]
        public function resetPassword(Request $request, string $token, UserPasswordEncoderInterface $passwordEncoder): Response
        {
            // Fetch the user by token
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['resetToken' => $token]);
        
            if (!$user) {
                // Handle the case where the user is not found (e.g., display an error message or redirect)
                // For example:
                throw $this->createNotFoundException('User not found for token: ' . $token);
            }
        
            // Create the reset password form
            $form = $this->createForm(ResetPasswordType::class);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                // Update the user's password
                $encodedPassword = $passwordEncoder->encodePassword($user, $form->get('password')->getData());
                $user->setPassword($encodedPassword);
                $user->setResetToken(null);
                $user->setTokenExpiration(null);
        
                // Persist the changes to the database
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
        
        // Redirect to a success page or login page
        return $this->redirectToRoute('app_login');
    }

    // Render the reset password form
    return $this->render('resetpswd/reset_password.html.twig', [
        'form' => $form->createView(),
        'token' => $token,
    ]);
        
        
}
    }
        
        

