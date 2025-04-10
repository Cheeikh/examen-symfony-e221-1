<?php

namespace App\Repository;

use App\Entity\Prestation;
use App\Entity\Patient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestation>
 *
 * @method Prestation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prestation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prestation[]    findAll()
 * @method Prestation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestation::class);
    }

    /**
     * @return Prestation[] Returns an array of Prestation objects
     */
    public function findByPatient(Patient $patient): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :val')
            ->setParameter('val', $patient)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Prestation[] Returns an array of Prestation objects
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.statut = :val')
            ->setParameter('val', $status)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find prestations by status for a patient
     *
     * @param Patient $patient
     * @param string $status
     * @return Prestation[]
     */
    public function findByPatientAndStatus(Patient $patient, string $status): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.statut = :status')
            ->setParameter('patient', $patient)
            ->setParameter('status', $status)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find prestations scheduled for today
     *
     * @return Prestation[]
     */
    public function findTodayPrestations(): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);
        $tomorrow = clone $today;
        $tomorrow->modify('+1 day');

        return $this->createQueryBuilder('p')
            ->andWhere('p.date >= :today')
            ->andWhere('p.date < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow)
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find upcoming prestations for a patient
     *
     * @param Patient $patient
     * @return Prestation[]
     */
    public function findUpcomingForPatient(Patient $patient): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.date >= :today')
            ->andWhere('p.statut != :canceled')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->setParameter('canceled', 'annulé')
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find past prestations for a patient
     *
     * @param Patient $patient
     * @return Prestation[]
     */
    public function findPastForPatient(Patient $patient): array
    {
        $today = new \DateTime();
        $today->setTime(0, 0, 0);

        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.date < :today OR p.statut = :completed')
            ->setParameter('patient', $patient)
            ->setParameter('today', $today)
            ->setParameter('completed', 'terminee')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    /**
     * Find prestations that can be canceled (at least 48h before)
     */
    public function findCancelableByPatient(Patient $patient): array
    {
        $now = new \DateTime();
        $limit = (clone $now)->modify('+48 hours');
        
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->andWhere('p.date > :limit')
            ->andWhere('p.statut NOT IN (:statuses)')
            ->setParameter('patient', $patient)
            ->setParameter('limit', $limit)
            ->setParameter('statuses', ['annulé', 'terminee'])
            ->orderBy('p.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find recent prestations for a patient
     *
     * @param Patient $patient
     * @param int $limit
     * @return Prestation[]
     */
    public function findRecentPrestations(Patient $patient, int $limit = 5): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.patient = :patient')
            ->setParameter('patient', $patient)
            ->orderBy('p.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find prestations by type
     *
     * @param string $type
     * @return Prestation[]
     */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
} 