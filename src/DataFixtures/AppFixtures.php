<?php

namespace App\DataFixtures;

use App\Entity\Consultation;
use App\Entity\Medecin;
use App\Entity\Ordonnance;
use App\Entity\Patient;
use App\Entity\Prestation;
use App\Entity\RendezVous;
use App\Entity\ResponsablePrestation;
use App\Entity\Secretaire;
use App\Entity\Resultat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Créer des patients
        $patient1 = $this->createPatient($manager, 'Diallo', 'Fatou', 'patient1@example.com');
        $patient2 = $this->createPatient($manager, 'Sow', 'Mamadou', 'patient2@example.com');
        $patient3 = $this->createPatient($manager, 'Diop', 'Aissatou', 'patient3@example.com');
        
        // Créer des médecins
        $medecin1 = $this->createMedecin($manager, 'Ndiaye', 'Abdoulaye', 'medecin1@example.com', 'Généraliste');
        $medecin2 = $this->createMedecin($manager, 'Fall', 'Aminata', 'medecin2@example.com', 'Dentiste');
        $medecin3 = $this->createMedecin($manager, 'Mbaye', 'Moussa', 'medecin3@example.com', 'Ophtalmologie');
        
        // Créer des secrétaires
        $secretaire = $this->createSecretaire($manager, 'Sarr', 'Marie', 'secretaire@example.com', 'Accueil');
        
        // Créer des responsables de prestations
        $responsable = $this->createResponsable($manager, 'Gueye', 'Omar', 'responsable@example.com', 'Laboratoire');
        
        // Créer des rendez-vous
        $this->createRendezVous($manager, $patient1, $medecin1, 
            (new \DateTime())->modify('+2 days'), 
            (new \DateTime('09:30')), 
            'Fièvre et maux de tête', 
            'valide', true, false);
            
        $this->createRendezVous($manager, $patient1, $medecin2, 
            (new \DateTime())->modify('+5 days'), 
            (new \DateTime('14:00')), 
            'Rendez-vous pour extraction dentaire', 
            'valide', true, false);
            
        $this->createRendezVous($manager, $patient2, $medecin3, 
            (new \DateTime())->modify('+1 days'), 
            (new \DateTime('10:15')), 
            'Contrôle annuel de la vue', 
            'demande', true, false);
            
        $rdvPrestation = $this->createRendezVous($manager, $patient3, null, 
            (new \DateTime())->modify('+3 days'), 
            (new \DateTime('08:45')), 
            'Analyse de sang', 
            'valide', false, true);
            
        // Créer des consultations passées
        $rdvPasse = $this->createRendezVous($manager, $patient1, $medecin1, 
            (new \DateTime())->modify('-10 days'), 
            (new \DateTime('11:00')), 
            'Douleurs abdominales', 
            'valide', true, false);
            
        $consultation = $this->createConsultation($manager, $patient1, $medecin1, 
            (new \DateTime())->modify('-10 days'), 
            'terminee', 
            ['temperature' => '38.5', 'tension' => '12/8']);
            
        $consultation->setRendezVous($rdvPasse);
        $rdvPasse->setConsultation($consultation);
        
        // Créer une ordonnance
        $ordonnance = new Ordonnance();
        $ordonnance->setDate(new \DateTime());
        $ordonnance->setConsultation($consultation);
        $ordonnance->addMedicament('PARA001', 'Paracétamol 1000mg', '1 comprimé 3 fois par jour pendant 5 jours');
        $ordonnance->addMedicament('AMOX002', 'Amoxicilline 500mg', '1 gélule matin et soir pendant 7 jours');
        
        $consultation->setOrdonnance($ordonnance);
        
        $manager->persist($ordonnance);
        
        // Créer une prestation passée (Radiographie terminée)
        $rdvPrestationPasse = $this->createRendezVous($manager, $patient2, null, 
            (new \DateTime())->modify('-15 days'), 
            (new \DateTime('09:00')), 
            'Radiographie thoracique', 
            'valide', false, true);
            
        $prestation = $this->createPrestation($manager, $patient2, $responsable, 
            (new \DateTime())->modify('-15 days'), 
            'Radiographie', 
            'terminee', 
            'Radiographie normale, pas d\'anomalie détectée.');
            
        $prestation->setRendezVous($rdvPrestationPasse);
        $rdvPrestationPasse->setPrestation($prestation);
        
        // Prestation en cours (Analyse sanguine)
        $rdvPrestationEncours = $this->createRendezVous($manager, $patient1, null, 
            (new \DateTime())->modify('-2 days'), 
            (new \DateTime('10:30')), 
            'Analyse sanguine complète', 
            'valide', false, true);
            
        $prestationEncours = $this->createPrestation($manager, $patient1, $responsable, 
            (new \DateTime())->modify('-2 days'), 
            'Analyse', 
            'en cours', 
            '');
            
        $prestationEncours->setRendezVous($rdvPrestationEncours);
        $rdvPrestationEncours->setPrestation($prestationEncours);
        
        // Prestation programmée (IRM cérébrale)
        $rdvPrestationProgrammee = $this->createRendezVous($manager, $patient3, null, 
            (new \DateTime())->modify('+7 days'), 
            (new \DateTime('11:15')), 
            'IRM cérébrale', 
            'valide', false, true);
            
        $prestationProgrammee = $this->createPrestation($manager, $patient3, $responsable, 
            (new \DateTime())->modify('+7 days'), 
            'IRM', 
            'programmé', 
            '');
            
        $prestationProgrammee->setRendezVous($rdvPrestationProgrammee);
        $rdvPrestationProgrammee->setPrestation($prestationProgrammee);
        $prestationProgrammee->setLaboratoire('Centre d\'imagerie médicale');
        $prestationProgrammee->setDescription('IRM cérébrale sans injection de produit de contraste');
        $prestationProgrammee->setInstructions('Ne pas porter de bijoux ou d\'objets métalliques. Signaler toute prothèse métallique, pacemaker ou claustrophobie.');
        $prestationProgrammee->setPrix(55000);
        
        // Prestation annulée (Scanner thoracique)
        $rdvPrestationAnnulee = $this->createRendezVous($manager, $patient2, null, 
            (new \DateTime())->modify('-5 days'), 
            (new \DateTime('14:00')), 
            'Scanner thoracique', 
            'annule', false, true);
            
        $prestationAnnulee = $this->createPrestation($manager, $patient2, $responsable, 
            (new \DateTime())->modify('-5 days'), 
            'Scanner', 
            'annulé', 
            '');
            
        $prestationAnnulee->setRendezVous($rdvPrestationAnnulee);
        $rdvPrestationAnnulee->setPrestation($prestationAnnulee);
        $prestationAnnulee->setLaboratoire('Centre d\'imagerie médicale');
        $prestationAnnulee->setDescription('Scanner thoracique avec injection de produit de contraste');
        
        $manager->flush();
    }
    
    private function createPatient(ObjectManager $manager, string $nom, string $prenom, string $email): Patient
    {
        $patient = new Patient();
        $patient->setNom($nom);
        $patient->setPrenom($prenom);
        $patient->setEmail($email);
        $patient->setPassword($this->passwordHasher->hashPassword($patient, 'password'));
        $patient->setCode('PAT' . uniqid());
        $patient->setAntecedentsMedicaux(['Allergie aux arachides', 'Hypertension']);
        
        $manager->persist($patient);
        
        return $patient;
    }
    
    private function createMedecin(ObjectManager $manager, string $nom, string $prenom, string $email, string $specialite): Medecin
    {
        $medecin = new Medecin();
        $medecin->setNom($nom);
        $medecin->setPrenom($prenom);
        $medecin->setEmail($email);
        $medecin->setPassword($this->passwordHasher->hashPassword($medecin, 'password'));
        $medecin->setSpecialite($specialite);
        
        $manager->persist($medecin);
        
        return $medecin;
    }
    
    private function createSecretaire(ObjectManager $manager, string $nom, string $prenom, string $email, string $service): Secretaire
    {
        $secretaire = new Secretaire();
        $secretaire->setNom($nom);
        $secretaire->setPrenom($prenom);
        $secretaire->setEmail($email);
        $secretaire->setPassword($this->passwordHasher->hashPassword($secretaire, 'password'));
        $secretaire->setService($service);
        
        $manager->persist($secretaire);
        
        return $secretaire;
    }
    
    private function createResponsable(ObjectManager $manager, string $nom, string $prenom, string $email, string $service): ResponsablePrestation
    {
        $responsable = new ResponsablePrestation();
        $responsable->setNom($nom);
        $responsable->setPrenom($prenom);
        $responsable->setEmail($email);
        $responsable->setPassword($this->passwordHasher->hashPassword($responsable, 'password'));
        $responsable->setService($service);
        
        $manager->persist($responsable);
        
        return $responsable;
    }
    
    private function createRendezVous(
        ObjectManager $manager, 
        Patient $patient, 
        ?Medecin $medecin, 
        \DateTime $date, 
        \DateTime $heure, 
        string $motif, 
        string $statut,
        bool $isConsultation,
        bool $isPrestation
    ): RendezVous {
        $rendezVous = new RendezVous();
        $rendezVous->setPatient($patient);
        $rendezVous->setMedecin($medecin);
        $rendezVous->setDate($date);
        $rendezVous->setHeure($heure);
        $rendezVous->setMotif($motif);
        $rendezVous->setStatut($statut);
        $rendezVous->setIsConsultation($isConsultation);
        $rendezVous->setIsPrestation($isPrestation);
        
        if ($isConsultation) {
            $rendezVous->setType('consultation');
        } elseif ($isPrestation) {
            $rendezVous->setType('prestation');
        }
        
        $manager->persist($rendezVous);
        
        return $rendezVous;
    }
    
    private function createConsultation(
        ObjectManager $manager, 
        Patient $patient, 
        Medecin $medecin, 
        \DateTime $date, 
        string $statut,
        array $constantes
    ): Consultation {
        $consultation = new Consultation();
        $consultation->setPatient($patient);
        $consultation->setMedecin($medecin);
        $consultation->setDate($date);
        $consultation->setStatut($statut);
        $consultation->setConstantes($constantes);
        
        $manager->persist($consultation);
        
        return $consultation;
    }
    
    private function createPrestation(
        ObjectManager $manager, 
        Patient $patient, 
        ResponsablePrestation $responsable, 
        \DateTime $date, 
        string $type,
        string $statut,
        string $resultats
    ): Prestation {
        $prestation = new Prestation();
        $prestation->setPatient($patient);
        $prestation->setResponsable($responsable);
        $prestation->setDate($date);
        $prestation->setType($type);
        $prestation->setStatut($statut);
        $prestation->setResultats($resultats);
        
        // Ajout de nouveaux champs
        $prestation->setPrix(mt_rand(5000, 25000));
        $prestation->setService('Service d\'imagerie médicale');
        
        if ($type === 'Radiographie') {
            $prestation->setLaboratoire('Laboratoire d\'imagerie');
            $prestation->setTechnicien('Dr. Seck Ibrahim');
            $prestation->setDescription('Radiographie thoracique standard de face et de profil.');
            $prestation->setInstructions('Se présenter à jeun, sans bijoux ni objets métalliques.');
        } else if ($type === 'Analyse') {
            $prestation->setLaboratoire('Laboratoire d\'analyses');
            $prestation->setTechnicien('Dr. Diop Astou');
            $prestation->setDescription('Analyse sanguine complète incluant NFS, glycémie, bilan lipidique et rénal.');
            $prestation->setInstructions('Se présenter à jeun depuis 12 heures. Boire de l\'eau est autorisé.');
        }
        
        $manager->persist($prestation);
        
        // Créer un résultat si la prestation est terminée
        if ($statut === 'terminee') {
            $this->createResultat($manager, $prestation);
        }
        
        return $prestation;
    }
    
    private function createResultat(ObjectManager $manager, Prestation $prestation): Resultat
    {
        $resultat = new Resultat();
        $resultat->setPrestation($prestation);
        $resultat->setDateResultat((clone $prestation->getDate())->modify('+2 days'));
        
        if ($prestation->getType() === 'Radiographie') {
            $resultat->setContenu('Radiographie thoracique : absence d\'image en foyer, absence d\'épanchement pleural. Cœur de taille normale. Conclusion : radiographie normale.');
            $resultat->setCommentaire('Aucune anomalie détectée. Contrôle annuel recommandé.');
        } else if ($prestation->getType() === 'Analyse') {
            $resultat->setContenu("Hémoglobine: 14.2 g/dL (normal)\nGlycémie: 0.95 g/L (normal)\nCholestérol total: 1.8 g/L (normal)\nCréatinine: 8 mg/L (normal)");
            $resultat->setCommentaire('Tous les paramètres sont dans les normes. Bilan satisfaisant.');
        }
        
        $prestation->setResultat($resultat);
        $manager->persist($resultat);
        
        return $resultat;
    }
} 