<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            try {
                // Création de l'email
                $email = (new Email())
                    ->from('noreply@centre-medical.com')
                    ->to('contact@centre-medical.com')
                    ->subject('Nouveau message de contact: ' . $data->getSujet())
                    ->html(
                        $this->renderView('emails/contact.html.twig', [
                            'nom' => $data->getNom(),
                            'prenom' => $data->getPrenom(),
                            'email' => $data->getEmail(),
                            'sujet' => $data->getSujet(),
                            'message' => $data->getMessage(),
                        ])
                    );
                
                // Envoi de l'email
                $mailer->send($email);
                
                // Message flash de succès
                $this->addFlash('success', 'Votre message a bien été envoyé !');
                
                // Redirection pour éviter la soumission multiple du formulaire
                return $this->redirectToRoute('app_contact');
                
            } catch (\Exception $e) {
                // En cas d'erreur, ajout d'un message flash d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du message.');
            }
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 