<?php

namespace App\Controller;

use App\Entity\Patient;
use App\Entity\RendezVous;
use App\Entity\Consultation;
use App\Entity\Prestation;
use App\Entity\Resultat;
use App\Form\RendezVousType;
use App\Form\ConsultationType;
use App\Form\ProfileType;
use App\Form\MedicalHistoryType;
use App\Form\PasswordChangeType;
use App\Form\PreferencesType;
use App\Form\PrivacySettingsType;
use App\Repository\ConsultationRepository;
use App\Repository\PatientRepository;
use App\Repository\PrestationRepository;
use App\Repository\RendezVousRepository;
use App\Repository\ResultatRepository;
use App\Repository\ServiceRepository;
use App\Repository\MedecinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/patient')]
#[IsGranted('ROLE_PATIENT')]
class PatientController extends AbstractController
{
    #[Route('/', name: 'app_patient_dashboard')]
    public function index(
        ConsultationRepository $consultationRepository, 
        PrestationRepository $prestationRepository,
        RendezVousRepository $rendezVousRepository
    ): Response {
        $patient = $this->getUser();
        
        // Récupérer les consultations à venir (planifiées)
        $upcomingConsultations = $rendezVousRepository->findUpcomingConsultations($patient);
        
        // Récupérer les consultations récentes (terminées)
        $recentConsultations = $consultationRepository->findRecentConsultations($patient, 5);
        
        // Récupérer les prestations récentes
        $recentPrestations = $prestationRepository->findRecentPrestations($patient, 5);
        
        return $this->render('patient/dashboard.html.twig', [
            'upcoming_consultations' => $upcomingConsultations,
            'recent_consultations' => $recentConsultations,
            'recent_prestations' => $recentPrestations,
        ]);
    }
    
    #[Route('/profil', name: 'app_patient_profile')]
    public function profile(
        Request $request, 
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $patient = $this->getUser();
        
        // Formulaire de profil
        $profileForm = $this->createForm(ProfileType::class, $patient);
        $profileForm->handleRequest($request);
        
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->persist($patient);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_patient_profile');
        }
        
        // Formulaire d'antécédents médicaux
        $medicalHistoryForm = $this->createForm(MedicalHistoryType::class, $patient);
        $medicalHistoryForm->handleRequest($request);
        
        if ($medicalHistoryForm->isSubmitted() && $medicalHistoryForm->isValid()) {
            $entityManager->persist($patient);
            $entityManager->flush();
            
            $this->addFlash('success', 'Vos antécédents médicaux ont été mis à jour avec succès.');
            return $this->redirectToRoute('app_patient_profile');
        }
        
        // Formulaire de changement de mot de passe
        $passwordForm = $this->createForm(PasswordChangeType::class);
        $passwordForm->handleRequest($request);
        
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $passwordForm->get('currentPassword')->getData();
            
            if ($passwordHasher->isPasswordValid($patient, $currentPassword)) {
                $newPassword = $passwordForm->get('newPassword')->getData();
                $patient->setPassword($passwordHasher->hashPassword($patient, $newPassword));
                
                $entityManager->persist($patient);
                $entityManager->flush();
                
                $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
                return $this->redirectToRoute('app_patient_profile');
            } else {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
            }
        }
        
        // Formulaire de préférences
        $preferencesForm = $this->createForm(PreferencesType::class);
        
        // Formulaire de confidentialité
        $privacyForm = $this->createForm(PrivacySettingsType::class);
        
        // Historique de connexion (simulé)
        $loginHistory = [
            [
                'date' => new \DateTime('now'),
                'ip' => '192.168.1.1',
                'device' => 'Chrome sur Windows',
                'status' => 'success'
            ],
            [
                'date' => new \DateTime('-1 day'),
                'ip' => '192.168.1.1',
                'device' => 'Safari sur iPhone',
                'status' => 'success'
            ]
        ];
        
        // Documents partagés (simulés)
        $sharedDocuments = [
            [
                'name' => 'Résultat analyse sanguine',
                'sharedWith' => 'Dr. Ndiaye Abdoulaye',
                'date' => new \DateTime('-5 days')
            ]
        ];
        
        return $this->render('patient/profile.html.twig', [
            'profile_form' => $profileForm->createView(),
            'medical_history_form' => $medicalHistoryForm->createView(),
            'password_form' => $passwordForm->createView(),
            'preferences_form' => $preferencesForm->createView(),
            'privacy_form' => $privacyForm->createView(),
            'login_history' => $loginHistory,
            'shared_documents' => $sharedDocuments
        ]);
    }

    #[Route('/consultations', name: 'app_patient_consultation_index')]
    public function listConsultations(
        Request $request,
        RendezVousRepository $rendezVousRepository,
        ConsultationRepository $consultationRepository
    ): Response {
        $patient = $this->getUser();
        $filter = $request->query->get('filter', 'all');
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 9;
        
        // Récupérer les consultations selon le filtre
        switch ($filter) {
            case 'upcoming':
                $consultations = $rendezVousRepository->findUpcomingConsultations($patient, ($page - 1) * $limit, $limit);
                $count = $rendezVousRepository->countUpcomingConsultations($patient);
                break;
            case 'past':
                $consultations = $consultationRepository->findPastConsultations($patient, ($page - 1) * $limit, $limit);
                $count = $consultationRepository->countPastConsultations($patient);
                break;
            case 'cancelled':
                $consultations = $rendezVousRepository->findCancelledConsultations($patient, ($page - 1) * $limit, $limit);
                $count = $rendezVousRepository->countCancelledConsultations($patient);
                break;
            default:
                $consultations = $rendezVousRepository->findAllConsultations($patient, ($page - 1) * $limit, $limit);
                $count = $rendezVousRepository->countAllConsultations($patient);
                break;
        }
        
        // Compter les consultations pour chaque catégorie
        $upcomingCount = $rendezVousRepository->countUpcomingConsultations($patient);
        $pastCount = $consultationRepository->countPastConsultations($patient);
        $cancelledCount = $rendezVousRepository->countCancelledConsultations($patient);
        
        return $this->render('patient/consultation_list.html.twig', [
            'consultations' => $consultations,
            'upcoming_count' => $upcomingCount,
            'past_count' => $pastCount,
            'cancelled_count' => $cancelledCount,
            'page' => $page,
            'pages_total' => ceil($count / $limit),
        ]);
    }

    #[Route('/consultation/nouvelle', name: 'app_patient_consultation_new')]
    public function newConsultation(
        Request $request, 
        EntityManagerInterface $entityManager,
        MedecinRepository $medecinRepository
    ): Response {
        $form = $this->createForm(ConsultationType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $rendezVous = new RendezVous();
            $rendezVous->setPatient($this->getUser());
            $rendezVous->setStatut('demande');
            $rendezVous->setDate($form->get('date')->getData());
            $rendezVous->setHeure($form->get('heure')->getData());
            $rendezVous->setMotif($form->get('motif')->getData());
            $rendezVous->setMedecin($form->get('medecin')->getData());
            $rendezVous->setIsConsultation(true);
            $rendezVous->setIsPrestation(false);
            $rendezVous->setType('consultation');
            
            $entityManager->persist($rendezVous);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre demande de rendez-vous a été enregistrée et sera traitée par notre équipe.');
            return $this->redirectToRoute('app_patient_consultation_index');
        }
        
        return $this->render('patient/consultation_new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/consultation/{id}', name: 'app_patient_consultation_show')]
    public function showConsultation(RendezVous $rendezVous): Response
    {
        // Vérifier si le RDV appartient au patient connecté
        if ($rendezVous->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas voir ce rendez-vous.');
        }
        
        // Si le rendez-vous a une consultation associée, on l'utilise
        // Sinon, on passe le rendezVous (qui possède une propriété heure)
        $consultationRdv = $rendezVous->getConsultation() ?: $rendezVous;
        
        return $this->render('patient/consultation_show.html.twig', [
            'consultation' => $consultationRdv,
        ]);
    }
    
    #[Route('/consultation/{id}/cancel', name: 'app_patient_consultation_cancel')]
    public function cancelConsultation(
        Request $request,
        RendezVous $rendezVous, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si le RDV appartient au patient connecté
        if ($rendezVous->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler ce rendez-vous.');
        }
        
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('cancel-consultation', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        
        // Vérifier si le RDV peut être annulé (48h avant)
        $dateRdv = clone $rendezVous->getDate();
        $heureRdv = $rendezVous->getHeure();
        $dateRdv->setTime(
            (int) $heureRdv->format('H'),
            (int) $heureRdv->format('i')
        );
        
        $now = new \DateTime();
        $interval = $now->diff($dateRdv);
        $heuresRestantes = $interval->days * 24 + $interval->h;
        
        if ($heuresRestantes < 48 && !$interval->invert) {
            $this->addFlash('error', 'Vous ne pouvez pas annuler un rendez-vous moins de 48h avant la date prévue.');
            return $this->redirectToRoute('app_patient_consultation_index');
        }
        
        // Enregistrer le motif d'annulation et le commentaire
        $motifAnnulation = $request->request->get('motif_annulation');
        $commentaire = $request->request->get('commentaire');
        
        $rendezVous->setStatut('annule');
        // Si vous avez des champs pour stocker le motif d'annulation et le commentaire, les ajouter ici
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre rendez-vous a été annulé avec succès.');
        return $this->redirectToRoute('app_patient_consultation_index');
    }
    
    #[Route('/consultation/{id}/reschedule', name: 'app_patient_consultation_reschedule')]
    public function rescheduleConsultation(
        Request $request,
        RendezVous $rendezVous, 
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si le RDV appartient au patient connecté
        if ($rendezVous->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas reprogrammer ce rendez-vous.');
        }
        
        // Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('reschedule-consultation', $request->request->get('token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }
        
        // Récupérer la nouvelle date et heure
        $newDate = \DateTime::createFromFormat('Y-m-d', $request->request->get('new_date'));
        $newTime = $request->request->get('new_time');
        
        if (!$newDate || !$newTime) {
            $this->addFlash('error', 'Veuillez fournir une date et une heure valides.');
            return $this->redirectToRoute('app_patient_consultation_show', ['id' => $rendezVous->getId()]);
        }
        
        // Convertir l'heure en objet DateTime
        $timeParts = explode(':', $newTime);
        $newTimeObj = new \DateTime();
        $newTimeObj->setTime((int) $timeParts[0], (int) $timeParts[1]);
        
        // Mettre à jour le rendez-vous
        $rendezVous->setDate($newDate);
        $rendezVous->setHeure($newTimeObj);
        $rendezVous->setStatut('demande'); // Retour à l'état de demande pour validation
        
        $entityManager->flush();
        
        $this->addFlash('success', 'Votre demande de reprogrammation a été enregistrée et sera traitée par notre équipe.');
        return $this->redirectToRoute('app_patient_consultation_show', ['id' => $rendezVous->getId()]);
    }

    #[Route('/prestations', name: 'app_patient_prestation_list')]
    public function listPrestations(PrestationRepository $prestationRepository): Response
    {
        $patient = $this->getUser();
        $prestations = $prestationRepository->findByPatient($patient);

        return $this->render('patient/prestation_list.html.twig', [
            'prestations' => $prestations,
        ]);
    }

    #[Route('/prestation/{id}', name: 'app_patient_prestation_show')]
    public function showPrestation(Prestation $prestation): Response
    {
        // Vérifier si la prestation appartient au patient connecté
        if ($prestation->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas voir cette prestation.');
        }
        
        return $this->render('patient/prestation_show.html.twig', [
            'prestation' => $prestation,
        ]);
    }
    
    #[Route('/resultat/{id}', name: 'app_patient_resultat_show')]
    public function showResultat(Resultat $resultat): Response
    {
        // Vérifier si le résultat appartient au patient connecté via la prestation
        if ($resultat->getPrestation()->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas voir ce résultat.');
        }
        
        return $this->render('patient/resultat_show.html.twig', [
            'resultat' => $resultat,
        ]);
    }
    
    #[Route('/prestation/{id}/cancel', name: 'app_patient_prestation_cancel')]
    public function cancelPrestation(
        Prestation $prestation,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si la prestation appartient au patient connecté
        if ($prestation->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas annuler cette prestation.');
        }
        
        // Vérifier si la prestation peut être annulée (uniquement si statut "en attente" ou "programmé")
        if ($prestation->getStatut() !== 'en attente' && $prestation->getStatut() !== 'programmé') {
            $this->addFlash('error', 'Cette prestation ne peut plus être annulée car son statut est "' . $prestation->getStatut() . '".');
            return $this->redirectToRoute('app_patient_prestation_show', ['id' => $prestation->getId()]);
        }
        
        // Mettre à jour le statut de la prestation
        $prestation->setStatut('annulé');
        $entityManager->flush();
        
        $this->addFlash('success', 'La prestation a été annulée avec succès.');
        return $this->redirectToRoute('app_patient_prestation_list');
    }
    
    #[Route('/prestation/{id}/contact', name: 'app_patient_prestation_contact')]
    public function contactService(
        Request $request,
        Prestation $prestation,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérifier si la prestation appartient au patient connecté
        if ($prestation->getPatient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas contacter le service pour cette prestation.');
        }
        
        // Traiter le formulaire de contact
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
        
        if (!$subject || !$message) {
            $this->addFlash('error', 'Veuillez remplir tous les champs du formulaire.');
            return $this->redirectToRoute('app_patient_prestation_show', ['id' => $prestation->getId()]);
        }
        
        // Ici, vous pourriez envoyer un email ou enregistrer le message dans la base de données
        // Pour l'instant, on se contente d'afficher un message de succès
        
        $this->addFlash('success', 'Votre message a été envoyé avec succès au service concerné.');
        return $this->redirectToRoute('app_patient_prestation_show', ['id' => $prestation->getId()]);
    }
    
    #[Route('/dossier-medical', name: 'app_patient_medical_record')]
    public function medicalRecord(
        ConsultationRepository $consultationRepository,
        PrestationRepository $prestationRepository
    ): Response {
        $patient = $this->getUser();
        $consultations = $consultationRepository->findByPatient($patient);
        $prestations = $prestationRepository->findByPatient($patient);
        
        return $this->render('patient/medical_record.html.twig', [
            'consultations' => $consultations,
            'prestations' => $prestations,
        ]);
    }
    
    #[Route('/messages', name: 'app_patient_messages')]
    public function messages(): Response
    {
        $patient = $this->getUser();
        
        // Pour l'instant, nous utilisons des données factices
        // Dans une implémentation réelle, vous récupéreriez les messages depuis une repository
        
        return $this->render('patient/messages.html.twig');
    }
    
    // Routes pour les fichiers PDF (à implémenter plus tard)
    #[Route('/consultation/{id}/pdf', name: 'app_patient_consultation_pdf')]
    public function downloadConsultationPdf(Consultation $consultation): Response
    {
        // TODO: Implémenter la génération du PDF
        $this->addFlash('info', 'Cette fonctionnalité sera disponible prochainement.');
        return $this->redirectToRoute('app_patient_consultation_show', ['id' => $consultation->getId()]);
    }
    
    #[Route('/resultat/{id}/download', name: 'app_patient_resultat_download')]
    public function downloadResultatPdf(Resultat $resultat): Response
    {
        // TODO: Implémenter la génération du PDF
        $this->addFlash('info', 'Cette fonctionnalité sera disponible prochainement.');
        return $this->redirectToRoute('app_patient_resultat_show', ['id' => $resultat->getId()]);
    }
    
    #[Route('/resultat/{id}/email', name: 'app_patient_resultat_email')]
    public function emailResultat(Resultat $resultat): Response
    {
        // TODO: Implémenter l'envoi de l'email
        $this->addFlash('info', 'Un email contenant vos résultats vous a été envoyé à l\'adresse ' . $this->getUser()->getEmail());
        return $this->redirectToRoute('app_patient_resultat_show', ['id' => $resultat->getId()]);
    }

    #[Route('/rendez-vous', name: 'app_patient_rdv_list')]
    public function listRendezVous(RendezVousRepository $rendezVousRepository): Response
    {
        $patient = $this->getUser();
        $rendezVous = $rendezVousRepository->findByPatient($patient);
        
        return $this->render('patient/rendez_vous_list.html.twig', [
            'rendez_vous' => $rendezVous,
        ]);
    }

    #[Route('/rendez-vous/nouveau', name: 'app_patient_rdv_new')]
    public function newRendezVous(
        Request $request, 
        EntityManagerInterface $entityManager,
        MedecinRepository $medecinRepository
    ): Response {
        return $this->redirectToRoute('app_patient_consultation_new');
    }
}
