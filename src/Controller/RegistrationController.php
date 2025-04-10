<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        // Rediriger si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $patient = new Patient();
        $form = $this->createForm(RegistrationFormType::class, $patient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un code pour le patient
            $patient->setCode('PAT' . uniqid());
            
            // Encoder le mot de passe
            $patient->setPassword(
                $userPasswordHasher->hashPassword(
                    $patient,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($patient);
            $entityManager->flush();

            // Authentifier l'utilisateur après l'inscription
            return $userAuthenticator->authenticateUser(
                $patient,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
} 