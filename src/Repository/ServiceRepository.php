<?php

namespace App\Repository;

use App\Entity\Prestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les services (sans entité dédiée)
 * Cette classe permet d'accéder aux différents services (spécialités) disponibles
 * dans la clinique, en se basant sur les prestations et médecins existants.
 */
class ServiceRepository
{
    private $prestationRepository;
    private $medecinRepository;

    public function __construct(
        PrestationRepository $prestationRepository,
        MedecinRepository $medecinRepository
    ) {
        $this->prestationRepository = $prestationRepository;
        $this->medecinRepository = $medecinRepository;
    }

    /**
     * Trouve tous les services disponibles basés sur les spécialités des médecins
     * et les types de prestations
     *
     * @return array Liste des services disponibles
     */
    public function findAll(): array
    {
        $services = [];
        
        // Ajouter les spécialités des médecins
        $medecins = $this->medecinRepository->findAll();
        foreach ($medecins as $medecin) {
            $specialite = $medecin->getSpecialite();
            if ($specialite && !in_array($specialite, $services)) {
                $services[] = $specialite;
            }
        }
        
        // Ajouter les types de prestations
        $prestations = $this->prestationRepository->findAll();
        foreach ($prestations as $prestation) {
            $type = $prestation->getType();
            if ($type && !in_array($type, $services)) {
                $services[] = $type;
            }
        }
        
        // Ajouter quelques services courants si la liste est vide
        if (empty($services)) {
            $services = [
                'Médecine générale',
                'Cardiologie',
                'Pédiatrie',
                'Gynécologie',
                'Ophtalmologie',
                'Dermatologie',
                'Radiologie',
                'Laboratoire',
                'Chirurgie'
            ];
        }
        
        sort($services);
        return $services;
    }
    
    /**
     * Trouve les médecins associés à un service spécifique
     *
     * @param string $service Nom du service
     * @return array Liste des médecins du service
     */
    public function findMedecinsByService(string $service): array
    {
        return $this->medecinRepository->findBySpecialite($service);
    }
    
    /**
     * Trouve les prestations associées à un service spécifique
     *
     * @param string $service Nom du service
     * @return array Liste des prestations du service
     */
    public function findPrestationsByService(string $service): array
    {
        return $this->prestationRepository->findByType($service);
    }
} 